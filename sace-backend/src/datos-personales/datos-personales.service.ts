import { Injectable, NotFoundException, ConflictException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CreateDatosPersonalesDto } from './dto/create-datos-personales.dto';
import { UpdateDatosPersonalesDto } from './dto/update-datos-personales.dto';
import PDFDocument from 'pdfkit';
import { Role } from '../auth/enums/role.enum';

@Injectable()
export class DatosPersonalesService {
  constructor(private readonly prisma: PrismaService) {}

  async findAll(params: { skip?: number; take?: number; search?: string }, user?: any) {
    const { skip, take, search } = params;

    const where: any = {};
    if (search) {
      const parsedSearch = parseInt(search, 10);
      if (!isNaN(parsedSearch)) {
        where.cedula = parsedSearch;
      } else {
        where.OR = [
          { nombres: { contains: search } },
          { apellidos: { contains: search } },
        ];
      }
    }

    // Filtrar si el rol es PROFESOR
    if (user && user.role === Role.PROFESOR) {
      // 1. Encontrar actas asignadas a este profesor
      const teacherActas = await this.prisma.registroActas.findMany({
        where: { cedula_profesor: Number(user.username) },
        select: { codcohorte: true },
      });
      const teacherCohortes = [...new Set(teacherActas.map((a) => a.codcohorte))];

      // 2. Encontrar programas de estudio de esas cohortes
      const cohortesList = await this.prisma.cohortes.findMany({
        where: { codcohorte: { in: teacherCohortes } },
        select: { codopest: true },
      });
      const teacherPrograms = [...new Set(cohortesList.map((c) => c.codopest))];

      // 3. Encontrar todas las cohortes vinculadas a esos programas de estudio
      const allCohortesOfPrograms = await this.prisma.cohortes.findMany({
        where: { codopest: { in: teacherPrograms } },
        select: { codcohorte: true },
      });
      const allowedCohortes = allCohortesOfPrograms.map((c) => c.codcohorte);

      // 4. Encontrar las cédulas de estudiantes registrados en esas cohortes
      const statusRecords = await this.prisma.status.findMany({
        where: { codcohorte: { in: allowedCohortes } },
        select: { cedula: true },
      });

      // También chequear recordNotas por si hay histórico heredado de esas cohortes
      const legacyActas = await this.prisma.registroActas.findMany({
        where: { codcohorte: { in: allowedCohortes } },
        select: { codacta: true },
      });
      const legacyActaCodes = legacyActas.map((a) => a.codacta);
      const legacyNotas = await this.prisma.recordNotas.findMany({
        where: { codacta: { in: legacyActaCodes } },
        select: { cedula: true },
      });

      const allAllowedCedulas = [...new Set([
        ...statusRecords.map((s) => s.cedula),
        ...legacyNotas.map((n) => n.cedula),
      ].filter(Boolean))];

      if (allAllowedCedulas.length === 0) {
        return { items: [], total: 0 };
      }

      // Si ya hay un filtro por cédula ingresado por buscador:
      if (where.cedula) {
        if (!allAllowedCedulas.includes(where.cedula)) {
          return { items: [], total: 0 };
        }
      } else {
        where.cedula = { in: allAllowedCedulas };
      }
    }

    const [items, total] = await Promise.all([
      this.prisma.datosPersonales.findMany({
        where,
        skip: skip ? Number(skip) : undefined,
        take: take ? Number(take) : undefined,
        orderBy: { cedula: 'asc' },
      }),
      this.prisma.datosPersonales.count({ where }),
    ]);

    return { items, total };
  }

  async isStudentInTeacherPrograms(studentCedula: number, teacherCedula: number): Promise<boolean> {
    // 1. Encontrar actas asignadas a este profesor
    const teacherActas = await this.prisma.registroActas.findMany({
      where: { cedula_profesor: teacherCedula },
      select: { codcohorte: true },
    });
    const teacherCohortes = [...new Set(teacherActas.map((a) => a.codcohorte))];

    // 2. Encontrar programas de estudio de esas cohortes
    const cohortesList = await this.prisma.cohortes.findMany({
      where: { codcohorte: { in: teacherCohortes } },
      select: { codopest: true },
    });
    const teacherPrograms = [...new Set(cohortesList.map((c) => c.codopest))];

    // 3. Encontrar todas las cohortes vinculadas a esos programas de estudio
    const allCohortesOfPrograms = await this.prisma.cohortes.findMany({
      where: { codopest: { in: teacherPrograms } },
      select: { codcohorte: true },
    });
    const allowedCohortes = allCohortesOfPrograms.map((c) => c.codcohorte);

    // 4. Verificar si el estudiante está registrado en alguna de esas cohortes (status)
    const statusCount = await this.prisma.status.count({
      where: {
        cedula: studentCedula,
        codcohorte: { in: allowedCohortes }
      }
    });
    if (statusCount > 0) return true;

    // Verificar histórico de notas en esas cohortes
    const legacyActas = await this.prisma.registroActas.findMany({
      where: { codcohorte: { in: allowedCohortes } },
      select: { codacta: true }
    });
    const legacyActaCodes = legacyActas.map(a => a.codacta);
    const legacyCount = await this.prisma.recordNotas.count({
      where: {
        cedula: studentCedula,
        codacta: { in: legacyActaCodes }
      }
    });
    return legacyCount > 0;
  }

  async findOne(cedula: number) {
    const record = await this.prisma.datosPersonales.findUnique({
      where: { cedula },
    });
    if (!record) {
      throw new NotFoundException(`No se encontró expediente con cédula ${cedula}`);
    }

    // 1. Obtener record de notas
    const recordNotas = await this.prisma.recordNotas.findMany({
      where: { cedula },
      orderBy: { codacta: 'asc' },
    });

    const cohorteCodes = new Set<string>();

    // A. Buscar a través de las notas asociadas (Historial legacy)
    if (recordNotas.length > 0) {
      const actas = await this.prisma.registroActas.findMany({
        where: {
          codacta: { in: recordNotas.map((n) => n.codacta) },
        },
      });
      actas.forEach((a) => cohorteCodes.add(a.codcohorte));

      const mActas = await this.prisma.multiactas.findMany({
        where: {
          codacta: { in: recordNotas.map((n) => n.codacta) },
        },
      });
      mActas.forEach((ma) => cohorteCodes.add(ma.codcohorte));
    }

    // B. Buscar a través de la tabla 'status' (Nuevos registros)
    const statusRecords = await this.prisma.status.findMany({
      where: { cedula },
    });
    statusRecords.forEach((s) => s.codcohorte && cohorteCodes.add(s.codcohorte));

    // C. Cargar especializaciones correspondientes a las cohortes
    const especializaciones = [];
    for (const codcohorte of cohorteCodes) {
      const cohorte = await this.prisma.cohortes.findFirst({
        where: { codcohorte },
      });
      if (cohorte) {
        const prog = await this.prisma.oportunidadesEstudio.findUnique({
          where: {
            codsede_codopest: { codsede: cohorte.codsede, codopest: cohorte.codopest },
          },
        });
        const statRec = statusRecords.find((s) => s.codcohorte === codcohorte);
        especializaciones.push({
          codcohorte,
          codsede: cohorte.codsede,
          codopest: cohorte.codopest,
          programa: prog?.titulo_a_otorgar || 'No registrado',
          mencion: prog?.mencion_especialidad || 'No registrada',
          tipo: prog?.tipo || 'No registrado',
          status: statRec?.status || 'Activo',
          fecha_inicio: cohorte.fecha_inicio,
        });
      }
    }

    // 2. Construir detalle de notas
    const notasDetalle = [];
    for (const nota of recordNotas) {
      const acta = await this.prisma.registroActas.findFirst({
        where: { codacta: nota.codacta },
      });
      let asignaturaNombre = 'Asignatura no encontrada';
      let periodos = null;
      let creditos = null;
      let fechaAprobacion = null;
      let codasig = 'No registrado';
      let codcohorte = 'No registrado';

      if (acta) {
        codasig = acta.codasig || 'No registrado';
        codcohorte = acta.codcohorte || 'No registrado';
        fechaAprobacion = acta.fecha_aprobacion || null;
        const asig = await this.prisma.pensumEstudios.findFirst({
          where: { codasig: acta.codasig },
        });
        if (asig) {
          asignaturaNombre = asig.asignatura || 'Sin nombre';
          periodos = asig.periodos;
          creditos = asig.creditos;
        }
      } else {
        // Fallback to multiactas
        const multiacta = await this.prisma.multiactas.findFirst({
          where: { codacta: nota.codacta },
        });
        if (multiacta) {
          codasig = multiacta.codasig || 'No registrado';
          codcohorte = multiacta.codcohorte || 'No registrado';
          fechaAprobacion = multiacta.fecha_aprobacion || null;
          const asig = await this.prisma.pensumEstudios.findFirst({
            where: { codasig: multiacta.codasig },
          });
          if (asig) {
            asignaturaNombre = asig.asignatura || 'Sin nombre';
            periodos = asig.periodos;
            creditos = asig.creditos;
          }
        }
      }

      notasDetalle.push({
        codacta: nota.codacta,
        codcohorte,
        codasig,
        asignatura: asignaturaNombre,
        calificacion: nota.calificacion,
        codeq: nota.codeq,
        periodo: periodos,
        creditos,
        fecha_aprobacion: fechaAprobacion,
      });
    }

    return {
      ...record,
      especializaciones,
      notas: notasDetalle,
    };
  }

  async create(dto: CreateDatosPersonalesDto) {
    const exists = await this.prisma.datosPersonales.findUnique({
      where: { cedula: dto.cedula },
    });
    if (exists) {
      throw new ConflictException(`El expediente con cédula ${dto.cedula} ya existe.`);
    }

    const data: any = { ...dto };
    if (dto.fecha_nacimiento) {
      data.fecha_nacimiento = new Date(dto.fecha_nacimiento);
    }
    if (dto.fecha_nacimiento_conyuge) {
      data.fecha_nacimiento_conyuge = new Date(dto.fecha_nacimiento_conyuge);
    }

    return this.prisma.datosPersonales.create({
      data,
    });
  }

  async update(cedula: number, dto: UpdateDatosPersonalesDto) {
    // Verificar que existe
    await this.findOne(cedula);

    const data: any = { ...dto };
    if (dto.fecha_nacimiento) {
      data.fecha_nacimiento = new Date(dto.fecha_nacimiento);
    }
    if (dto.fecha_nacimiento_conyuge) {
      data.fecha_nacimiento_conyuge = new Date(dto.fecha_nacimiento_conyuge);
    }

    return this.prisma.datosPersonales.update({
      where: { cedula },
      data,
    });
  }

  async delete(cedula: number) {
    // Verificar que existe
    await this.findOne(cedula);

    return this.prisma.datosPersonales.delete({
      where: { cedula },
    });
  }

  async generateRecordNotasPdf(cedula: number, codcohorte: string): Promise<Buffer> {
    const student = await this.findOne(cedula);
    
    // Buscar la especialización (programa)
    const normalize = (code: string) => (code || '').replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
    const esp = student.especializaciones.find(e => normalize(e.codcohorte) === normalize(codcohorte));
    if (!esp) {
      throw new NotFoundException(`El estudiante no tiene registrado el programa con cohorte ${codcohorte}`);
    }

    // Filtrar notas de este programa
    const espNotes = student.notas.filter(n => normalize(n.codcohorte) === normalize(codcohorte));

    // Obtener la ciudad de la sede
    const dir = await this.prisma.directorio_cippsv.findFirst({
      where: { codsede: esp.codsede }
    });
    const ciudad = dir?.ciudad || 'No especificada';

    // Calcular estadísticas
    let totalPonderado = 0;
    let totalCreditos = 0;
    let approvedCreditos = 0;
    
    const notesWithCredits = [];

    for (const note of espNotes) {
      const asig = await this.prisma.pensumEstudios.findFirst({
        where: { codasig: note.codasig, codsede: esp.codsede }
      });
      const credits = asig?.creditos || 0;
      const score = note.calificacion;
      
      if (score !== null && score >= 1 && score <= 20 && credits > 0) {
        totalPonderado += score * credits;
        totalCreditos += credits;
      }
      
      const isApproved = (score !== null && (score >= 15 || [100, 110, 120, 212].includes(score)));
      if (isApproved) {
        approvedCreditos += credits;
      }

      notesWithCredits.push({
        ...note,
        creditos: credits,
        codasig_imp: asig?.codasig_imp || note.codasig
      });
    }

    const promedio = totalCreditos > 0 ? (totalPonderado / totalCreditos).toFixed(2).replace('.', ',') : '0,00';

    // Agrupar por período
    const periodsMap: { [key: string]: any[] } = {};
    notesWithCredits.forEach((n) => {
      const pKey = n.periodo !== null ? `Período ${n.periodo}` : 'Sin Período Definido';
      if (!periodsMap[pKey]) periodsMap[pKey] = [];
      periodsMap[pKey].push(n);
    });

    const sortedPeriods = Object.keys(periodsMap).sort((a, b) => {
      if (a.includes('Definido')) return 1;
      if (b.includes('Definido')) return -1;
      const numA = parseInt(a.replace(/[^0-9]/g, ''), 10);
      const numB = parseInt(b.replace(/[^0-9]/g, ''), 10);
      return numA - numB;
    });

    // Helper functions for names
    const numberToSpanishWords = (n: number): string => {
      const words = [
        '', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE', 'DIEZ',
        'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO',
        'DIECINUEVE', 'VEINTE'
      ];
      return words[n] || String(n);
    };

    const formatNota = (nota: number | null): string => {
      if (nota === null) return 'PENDIENTE';
      if (nota === 404) return 'SIN NOTA';
      if (nota === 99) return 'REPROBADO';
      if (nota === 100) return 'APROBADA';
      if (nota === 110) return 'MERITORIO';
      if (nota === 120) return 'EXCELENCIA';
      if (nota === 212) return 'EQUIVALENCIA';
      if (nota >= 1 && nota <= 20) {
        return `${numberToSpanishWords(nota)} (${nota})`;
      }
      return String(nota);
    };

    return new Promise((resolve, reject) => {
      const doc = new PDFDocument({ margin: 30, size: 'A4' });
      const buffers: Buffer[] = [];

      doc.on('data', (chunk) => buffers.push(chunk));
      doc.on('end', () => resolve(Buffer.concat(buffers)));
      doc.on('error', (err) => reject(err));

      // --- RENDERIZADO DEL PDF ---
      // Logo
      doc.image('logo.png', 50, 30, { width: 50 });

      // Encabezado
      doc.font('Helvetica-Bold').fillColor('#000000').fontSize(9.5);
      doc.text('Centro de Investigaciones Psiquiátricas, Psicológicas y', 110, 32, { align: 'center', width: 370 });
      doc.text('Sexológicas de Venezuela', 110, 43, { align: 'center', width: 370 });
      doc.font('Helvetica').fontSize(8.5);
      doc.text('Coordinación Académica', 110, 54, { align: 'center', width: 370 });
      doc.text('Oficina de Control de Estudios', 110, 65, { align: 'center', width: 370 });

      const yHeaderEnd = 78;

      // Nombre del programa
      doc.font('Helvetica-Bold').fontSize(10.5).fillColor('#000000');
      doc.text(`${esp.tipo} en Ciencias Mención ${esp.mencion}`, 50, yHeaderEnd, { align: 'center' });

      // Datos personales (2 columnas para ahorrar espacio)
      const yMeta = yHeaderEnd + 16;
      doc.font('Helvetica-Bold').fontSize(8.5);
      doc.text('Nombres y Apellidos: ', 50, yMeta, { lineBreak: false } as any).font('Helvetica').text(`${student.nombres} ${student.apellidos}`);
      
      const nacLetter = student.nacionalidad === 'Venezolana' ? 'V' : 'E';
      const formattedCedula = student.cedula.toLocaleString('es-VE').replace(/\./g, '.');
      doc.font('Helvetica-Bold').text('Cédula: ', 50, yMeta + 11, { lineBreak: false } as any).font('Helvetica').text(`${nacLetter} - ${formattedCedula}`);
      
      doc.font('Helvetica-Bold').text('Sede: ', 350, yMeta, { lineBreak: false } as any).font('Helvetica').text(ciudad);
      
      const cohorteYear = esp.codcohorte.match(/\d{4}/)?.[0] || '2022';
      doc.font('Helvetica-Bold').text('Año de Ingreso: ', 350, yMeta + 11, { lineBreak: false } as any).font('Helvetica').text(cohorteYear);

      // Disclaimer
      const yDisclaimer = yMeta + 27;
      doc.font('Helvetica-Bold').fontSize(6).fillColor('#444444');
      doc.text('ESCALA DE CALIFICACIONES UNO A VEINTE (01-20) PUNTOS. NOTA MINIMA APROBATORIA QUINCE (15) PUNTOS.', 50, yDisclaimer);

      // Tabla de Calificaciones
      const startX = 50;
      let currentY = yDisclaimer + 12;

      // Dibujar cabecera de la tabla
      const rowHeight = 18;
      doc.fillColor('#002855').rect(startX, currentY, 500, rowHeight).fill();
      doc.font('Helvetica-Bold').fillColor('#ffffff').fontSize(8.5);
      doc.text('Código', startX + 10, currentY + 5);
      doc.text('Nombre de la Asignatura', startX + 80, currentY + 5);
      doc.text('Crédito', startX + 285, currentY + 5, { width: 45, align: 'center' });
      doc.text('Nota', startX + 335, currentY + 5, { width: 110, align: 'center' });
      doc.text('Periodo', startX + 450, currentY + 5, { width: 45, align: 'center' });
      
      currentY += rowHeight;

      // Dibujar filas agrupadas por período
      doc.fontSize(8).fillColor('#000000');

      sortedPeriods.forEach((period) => {
        const periodNotes = periodsMap[period];
        
        // Dibujar borde superior del bloque de período
        doc.strokeColor('#000000').lineWidth(0.5);
        doc.moveTo(startX, currentY).lineTo(startX + 500, currentY).stroke();
        
        periodNotes.forEach((note) => {
          // Dibujar fondo alterno o blanco
          doc.fillColor('#ffffff').rect(startX, currentY, 500, rowHeight).fill();

          // Dibujar textos
          doc.fillColor('#000000').font('Helvetica');
          doc.text(note.codasig_imp, startX + 10, currentY + 5);
          doc.text(note.asignatura, startX + 80, currentY + 5, { width: 200, height: 12, ellipsis: true });
          doc.text(String(note.creditos), startX + 285, currentY + 5, { width: 45, align: 'center' });
          doc.text(formatNota(note.calificacion), startX + 335, currentY + 5, { width: 110, align: 'center' });
          doc.text(note.periodo !== null ? String(note.periodo) : 'S/P', startX + 450, currentY + 5, { width: 45, align: 'center' });

          // Dibujar bordes de celda (borde inferior)
          doc.strokeColor('#000000').lineWidth(0.5);
          doc.moveTo(startX, currentY + rowHeight).lineTo(startX + 500, currentY + rowHeight).stroke();
          
          // Líneas verticales
          doc.moveTo(startX, currentY).lineTo(startX, currentY + rowHeight).stroke();
          doc.moveTo(startX + 75, currentY).lineTo(startX + 75, currentY + rowHeight).stroke();
          doc.moveTo(startX + 280, currentY).lineTo(startX + 280, currentY + rowHeight).stroke();
          doc.moveTo(startX + 330, currentY).lineTo(startX + 330, currentY + rowHeight).stroke();
          doc.moveTo(startX + 445, currentY).lineTo(startX + 445, currentY + rowHeight).stroke();
          doc.moveTo(startX + 500, currentY).lineTo(startX + 500, currentY + rowHeight).stroke();

          currentY += rowHeight;
        });

        // Dejar un espacio pequeño antes del siguiente período
        currentY += 4;
      });

      currentY = currentY - 4; // Descontar el último espacio sobrante
      doc.y = currentY;

      // Nota inferior
      currentY += 8;
      doc.font('Helvetica-Oblique').fontSize(7).fillColor('#333333');
      doc.text('NOTA: En caso de error u omisión, las actas son el único documento válido y definitivo para cualquier reclamo u observación.', 50, currentY, { width: 500 });
      
      currentY += 15;

      // Fecha y Promedio
      doc.font('Helvetica').fontSize(9).fillColor('#000000');
      
      const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
      const today = new Date();
      const dateString = `${ciudad}, ${today.getDate()} de ${meses[today.getMonth()]} del ${today.getFullYear()}`;
      
      doc.text(dateString, 50, currentY);
      doc.font('Helvetica-Bold').text(`Índice Académico: ${promedio}`, 350, currentY, { align: 'right', width: 200 });

      currentY += 35;

      // Firmas
      doc.strokeColor('#000000').lineWidth(0.5);
      doc.moveTo(50, currentY).lineTo(220, currentY).stroke();
      doc.moveTo(330, currentY).lineTo(500, currentY).stroke();

      doc.font('Helvetica').fontSize(8);
      doc.text('Lic. Mercedes Labrador', 50, currentY + 3, { align: 'center', width: 170 });
      doc.text('Jefe de Control de Estudios', 50, currentY + 12, { align: 'center', width: 170 });

      doc.text('Esp. Herman Y. Bandez S.', 330, currentY + 3, { align: 'center', width: 170 });
      doc.text('Secretario', 330, currentY + 12, { align: 'center', width: 170 });

      doc.end();
    });
  }
}
