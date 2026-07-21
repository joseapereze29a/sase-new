import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CreateOportunidadDto } from './dto/create-oportunidad.dto';
import { UpdateOportunidadDto } from './dto/update-oportunidad.dto';
import { CreatePensumDto } from './dto/create-pensum.dto';
import { UpdatePensumDto } from './dto/update-pensum.dto';
import PDFDocument from 'pdfkit';

@Injectable()
export class AcademicoService {
  constructor(private readonly prisma: PrismaService) {}

  // ==========================================
  // OPORTUNIDADES DE ESTUDIO
  // ==========================================

  async findAllOportunidades(params: { skip?: number; take?: number; search?: string }) {
    const { skip, take, search } = params;
    const where: any = {};

    if (search) {
      where.OR = [
        { codopest: { contains: search } },
        { mencion_especialidad: { contains: search } },
        { titulo_a_otorgar: { contains: search } },
      ];
    }

    const allItems = await this.prisma.oportunidadesEstudio.findMany({
      where,
      orderBy: { codopest: 'asc' },
    });

    // Deduplicar en memoria por codopest
    const uniqueMap = new Map<string, any>();
    allItems.forEach((item) => {
      if (!uniqueMap.has(item.codopest)) {
        uniqueMap.set(item.codopest, item);
      }
    });

    const uniqueItems = Array.from(uniqueMap.values());
    const total = uniqueItems.length;

    // Aplicar paginación (skip y take) manual
    const skipNum = skip ? Number(skip) : 0;
    const takeNum = take ? Number(take) : undefined;
    const items = takeNum !== undefined 
      ? uniqueItems.slice(skipNum, skipNum + takeNum) 
      : uniqueItems.slice(skipNum);

    return { items, total };
  }

  async findOneOportunidad(codsede: string, codopest: string) {
    const record = await this.prisma.oportunidadesEstudio.findUnique({
      where: {
        codsede_codopest: { codsede, codopest },
      },
    });
    if (!record) {
      throw new NotFoundException(`No se encontró programa con sede ${codsede} y código ${codopest}`);
    }
    return record;
  }

  async createOportunidad(dto: CreateOportunidadDto) {
    // Verificar si ya existe
    const exists = await this.prisma.oportunidadesEstudio.findUnique({
      where: {
        codsede_codopest: { codsede: dto.codsede, codopest: dto.codopest },
      },
    });
    if (exists) {
      throw new BadRequestException(
        `El programa con sede ${dto.codsede} y código ${dto.codopest} ya existe.`,
      );
    }

    return this.prisma.oportunidadesEstudio.create({
      data: dto as any,
    });
  }

  async updateOportunidad(codsede: string, codopest: string, dto: UpdateOportunidadDto) {
    await this.findOneOportunidad(codsede, codopest);

    return this.prisma.oportunidadesEstudio.update({
      where: {
        codsede_codopest: { codsede, codopest },
      },
      data: dto as any,
    });
  }

  async deleteOportunidad(codsede: string, codopest: string) {
    await this.findOneOportunidad(codsede, codopest);

    return this.prisma.oportunidadesEstudio.delete({
      where: {
        codsede_codopest: { codsede, codopest },
      },
    });
  }

  // ==========================================
  // PENSUM DE ESTUDIOS
  // ==========================================

  async findAllPensum(params: {
    skip?: number;
    take?: number;
    codsede?: string;
    codopest?: string;
    search?: string;
  }) {
    const { skip, take, codsede, codopest, search } = params;
    const where: any = {};

    if (codsede) where.codsede = codsede;
    if (codopest) where.codopest = codopest;

    if (search) {
      where.OR = [
        { codasig: { contains: search } },
        { asignatura: { contains: search } },
      ];
    }

    const [items, total] = await Promise.all([
      this.prisma.pensumEstudios.findMany({
        where,
        skip: skip ? Number(skip) : undefined,
        take: take ? Number(take) : undefined,
        orderBy: [{ periodos: 'asc' }, { codasig: 'asc' }],
      }),
      this.prisma.pensumEstudios.count({ where }),
    ]);

    // Consultar prelaciones en lote (batch query) para mayor rendimiento
    const codsSede = [...new Set(items.map((i) => i.codsede))];
    const codsOpest = [...new Set(items.map((i) => i.codopest))];
    const codsAsig = items.map((i) => i.codasig);

    const prelaciones = await this.prisma.pensum_prelaciones.findMany({
      where: {
        codsede: { in: codsSede },
        codopest: { in: codsOpest },
        codasig: { in: codsAsig },
      },
    });

    const enrichedItems = items.map((item) => {
      const itemPrels = prelaciones
        .filter((p) => p.codsede === item.codsede && p.codopest === item.codopest && p.codasig === item.codasig)
        .map((p) => p.prelacion);
      return {
        ...item,
        prelaciones: itemPrels,
      };
    });

    return { items: enrichedItems, total };
  }

  async findOnePensum(codsede: string, codopest: string, codasig: string) {
    const record = await this.prisma.pensumEstudios.findUnique({
      where: {
        codsede_codopest_codasig: { codsede, codopest, codasig },
      },
    });
    if (!record) {
      throw new NotFoundException(
        `No se encontró la asignatura ${codasig} en el pensum de sede ${codsede} y programa ${codopest}`,
      );
    }
    return record;
  }

  async createPensum(dto: CreatePensumDto) {
    // Verificar si existe la oportunidad de estudio primero
    await this.findOneOportunidad(dto.codsede, dto.codopest);

    // Verificar si ya existe la asignatura en el pensum
    const exists = await this.prisma.pensumEstudios.findUnique({
      where: {
        codsede_codopest_codasig: {
          codsede: dto.codsede,
          codopest: dto.codopest,
          codasig: dto.codasig,
        },
      },
    });
    if (exists) {
      throw new BadRequestException(
        `La asignatura con código ${dto.codasig} ya existe en el pensum del programa.`,
      );
    }

    const { prelaciones, ...pensumData } = dto;

    const created = await this.prisma.pensumEstudios.create({
      data: pensumData as any,
    });

    if (prelaciones && Array.isArray(prelaciones)) {
      for (const pre of prelaciones) {
        if (pre && pre.trim()) {
          await this.prisma.pensum_prelaciones.create({
            data: {
              codsede: dto.codsede,
              codopest: dto.codopest,
              codasig: dto.codasig,
              prelacion: pre.trim(),
            },
          });
        }
      }
    }

    return created;
  }

  async updatePensum(codsede: string, codopest: string, codasig: string, dto: UpdatePensumDto) {
    await this.findOnePensum(codsede, codopest, codasig);

    const { prelaciones, ...pensumData } = dto;

    const updated = await this.prisma.pensumEstudios.update({
      where: {
        codsede_codopest_codasig: { codsede, codopest, codasig },
      },
      data: pensumData as any,
    });

    if (prelaciones && Array.isArray(prelaciones)) {
      // Borrar prelaciones viejas
      await this.prisma.pensum_prelaciones.deleteMany({
        where: { codsede, codopest, codasig },
      });

      // Crear prelaciones nuevas
      for (const pre of prelaciones) {
        if (pre && pre.trim()) {
          await this.prisma.pensum_prelaciones.create({
            data: {
              codsede,
              codopest,
              codasig,
              prelacion: pre.trim(),
            },
          });
        }
      }
    }

    return updated;
  }

  async deletePensum(codsede: string, codopest: string, codasig: string) {
    await this.findOnePensum(codsede, codopest, codasig);

    // Eliminar prelaciones asociadas
    await this.prisma.pensum_prelaciones.deleteMany({
      where: { codsede, codopest, codasig },
    });

    return this.prisma.pensumEstudios.delete({
      where: {
        codsede_codopest_codasig: { codsede, codopest, codasig },
      },
    });
  }

  async generatePensumPdf(codsede: string, codopest: string): Promise<Buffer> {
    // 1. Obtener la oportunidad de estudio (programa) ignorando la sede específica
    const programa = await this.prisma.oportunidadesEstudio.findFirst({
      where: { codopest },
    });
    if (!programa) {
      throw new NotFoundException(`No se encontró programa con código ${codopest}`);
    }

    // 2. Obtener todas las asignaturas asociadas y deduplicarlas por codasig
    const allAsig = await this.prisma.pensumEstudios.findMany({
      where: { codopest },
      orderBy: [{ periodos: 'asc' }, { codasig: 'asc' }],
    });

    const uniqueAsigMap = new Map<string, any>();
    allAsig.forEach((a) => {
      if (!uniqueAsigMap.has(a.codasig)) {
        uniqueAsigMap.set(a.codasig, a);
      }
    });
    const asignaturas = Array.from(uniqueAsigMap.values());

    const asigCodes = asignaturas.map((a) => a.codasig);
    const prelaRecords = await this.prisma.pensum_prelaciones.findMany({
      where: {
        codopest,
        codasig: { in: asigCodes },
      },
    });

    const enrichedAsignaturas = asignaturas.map((a) => {
      const aPrels = prelaRecords
        .filter((p) => p.codasig === a.codasig)
        .map((p) => p.prelacion);
      return {
        ...a,
        prelacionText: aPrels.length > 0 ? aPrels.join('/') : '-',
      };
    });

    // 3. Agrupar por período
    const periodsMap: { [key: string]: any[] } = {};
    enrichedAsignaturas.forEach((a) => {
      const pKey = a.periodos !== null ? `Período ${a.periodos}` : 'Sin Período Definido';
      if (!periodsMap[pKey]) periodsMap[pKey] = [];
      periodsMap[pKey].push(a);
    });

    const sortedPeriods = Object.keys(periodsMap).sort((a, b) => {
      if (a.includes('Definido')) return 1;
      if (b.includes('Definido')) return -1;
      const numA = parseInt(a.replace(/[^0-9]/g, ''), 10);
      const numB = parseInt(b.replace(/[^0-9]/g, ''), 10);
      return numA - numB;
    });

    return new Promise((resolve, reject) => {
      const doc = new PDFDocument({ margin: 50, size: 'A4' });
      const buffers: Buffer[] = [];

      doc.on('data', (chunk) => buffers.push(chunk));
      doc.on('end', () => resolve(Buffer.concat(buffers)));
      doc.on('error', (err) => reject(err));

      // --- DISEÑO DEL PDF ---
      // Logo
      doc.image('logo.png', 50, 42, { width: 60 });

      // Encabezado institucional
      doc.font('Helvetica-Bold').fillColor('#000000').fontSize(10);
      doc.text('Centro de Investigaciones Psiquiátricas, Psicológicas y', 120, 50, { align: 'center', width: 350 });
      doc.text('Sexológicas de Venezuela', 120, 62, { align: 'center', width: 350 });
      doc.font('Helvetica').fontSize(9);
      doc.text('Coordinación Académica', 120, 74, { align: 'center', width: 350 });
      doc.text('Oficina de Control de Estudios', 120, 86, { align: 'center', width: 350 });
      
      const yHeaderEnd = doc.y;

      // Título del Pensum
      doc.font('Helvetica-Bold').fillColor('#002855').fontSize(14).text('PENSUM DE ESTUDIOS', 50, yHeaderEnd + 15, { align: 'center' });
      doc.moveDown(1.5);

      // Detalles del programa
      const yMeta = doc.y;
      doc.font('Helvetica-Bold').fontSize(9.5).fillColor('#000000');
      doc.text('Programa: ', 50, yMeta, { lineBreak: false } as any).font('Helvetica').text(programa.titulo_a_otorgar || 'No especificado');
      doc.font('Helvetica-Bold').text('Mención/Especialidad: ', 50, yMeta + 12, { lineBreak: false } as any).font('Helvetica').text(programa.mencion_especialidad || 'No especificada');
      doc.font('Helvetica-Bold').text('Tipo: ', 50, yMeta + 24, { lineBreak: false } as any).font('Helvetica').text(`${programa.tipo || 'No especificado'} | Código: ${programa.codopest}`);
      doc.font('Helvetica-Bold').text('Créditos Requeridos: ', 50, yMeta + 36, { lineBreak: false } as any).font('Helvetica').text(String(programa.creditos || 'No especificado'));
      doc.moveDown(2);

      // Tabla de Asignaturas
      const startX = 50;
      let currentY = doc.y;

      // Dibujar cabecera
      doc.fillColor('#002855').rect(startX, currentY, 500, 18).fill();
      doc.font('Helvetica-Bold').fillColor('#ffffff').fontSize(8.5);
      doc.text('Código', startX + 10, currentY + 5);
      doc.text('Nombre de la Asignatura', startX + 80, currentY + 5);
      doc.text('Crédito', startX + 355, currentY + 5, { width: 60, align: 'center' });
      doc.text('Prelación', startX + 435, currentY + 5, { width: 55, align: 'center' });
      
      currentY += 18;

      // Dibujar filas por períodos
      doc.fontSize(8).fillColor('#000000');

      sortedPeriods.forEach((period) => {
        const periodNotes = periodsMap[period];

        // Dibujar borde superior del bloque de período
        doc.strokeColor('#000000').lineWidth(0.5);
        doc.moveTo(startX, currentY).lineTo(startX + 500, currentY).stroke();

        periodNotes.forEach((asig) => {
          // Verificar si cabe en la página
          if (currentY > 700) {
            doc.addPage();
            currentY = 50;
            // Redibujar borde superior del bloque en la nueva página
            doc.strokeColor('#000000').lineWidth(0.5);
            doc.moveTo(startX, currentY).lineTo(startX + 500, currentY).stroke();
          }

          const rowHeight = 18;

          // Dibujar fondo blanco
          doc.fillColor('#ffffff').rect(startX, currentY, 500, rowHeight).fill();

          // Dibujar textos
          doc.fillColor('#000000').font('Helvetica');
          doc.text(asig.codasig_imp || asig.codasig, startX + 10, currentY + 5);
          doc.text(asig.asignatura || '', startX + 80, currentY + 5, { width: 260, height: 12, ellipsis: true });
          doc.text(asig.creditos !== null ? String(asig.creditos) : '0', startX + 355, currentY + 5, { width: 60, align: 'center' });
          
          doc.fontSize(7);
          doc.text(asig.prelacionText || '-', startX + 432, currentY + 5.5, { width: 66, align: 'center' });
          doc.fontSize(8);

          // Dibujar bordes de celda (borde inferior)
          doc.strokeColor('#000000').lineWidth(0.5);
          doc.moveTo(startX, currentY + rowHeight).lineTo(startX + 500, currentY + rowHeight).stroke();
          
          // Líneas verticales
          doc.moveTo(startX, currentY).lineTo(startX, currentY + rowHeight).stroke();
          doc.moveTo(startX + 75, currentY).lineTo(startX + 75, currentY + rowHeight).stroke();
          doc.moveTo(startX + 350, currentY).lineTo(startX + 350, currentY + rowHeight).stroke();
          doc.moveTo(startX + 430, currentY).lineTo(startX + 430, currentY + rowHeight).stroke();
          doc.moveTo(startX + 500, currentY).lineTo(startX + 500, currentY + rowHeight).stroke();

          currentY += rowHeight;
        });

        // Dejar un espacio antes del siguiente período
        currentY += 10;
      });

      currentY = currentY - 10; // Descontar el último espacio sobrante
      doc.y = currentY;

      if (currentY > 700) {
        doc.addPage();
        currentY = 50;
      }

      // Dibujar pie de página
      doc.moveDown(2);
      doc.font('Helvetica-Oblique').fillColor('#444444').fontSize(7.5).text('Reporte de pensum de estudios oficial generado por SACE. Todos los derechos reservados CIPPSV.', { align: 'center' });

      // Finalizar documento
      doc.end();
    });
  }
}
