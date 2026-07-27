import {
  Injectable,
  NotFoundException,
  ConflictException,
  BadRequestException,
  ForbiddenException,
} from '@nestjs/common';
import PDFDocument from 'pdfkit';
import { PrismaService } from '../prisma/prisma.service';
import { CreateActaDto } from './dto/create-acta.dto';
import { UpdateActaDto } from './dto/update-acta.dto';
import { CreateNotaDto } from './dto/create-nota.dto';
import { UpdateNotaDto } from './dto/update-nota.dto';
import { Role } from '../auth/enums/role.enum';

@Injectable()
export class EvaluacionesService {
  constructor(private readonly prisma: PrismaService) {}

  // ==========================================
  // GESTIÓN DE ACTAS (RegistroActas)
  // ==========================================

  async findAllActas(
    params: { skip?: number; take?: number; search?: string; codcohorte?: string },
    user: any,
  ) {
    const { skip, take, search, codcohorte } = params;
    const where: any = {};

    if (user.role === Role.PROFESOR) {
      where.cedula_profesor = Number(user.username);
    }

    if (codcohorte) {
      where.codcohorte = codcohorte;
    }

    if (search) {
      where.OR = [
        { codacta: { contains: search } },
        { codasig: { contains: search } },
        { codcohorte: { contains: search } },
      ];
    }

    const [items, total] = await Promise.all([
      this.prisma.registroActas.findMany({
        where,
        skip: skip ? Number(skip) : undefined,
        take: take ? Number(take) : undefined,
        orderBy: [{ codacta: 'asc' }],
      }),
      this.prisma.registroActas.count({ where }),
    ]);

    const profCedulas = [...new Set(items.map((i) => i.cedula_profesor).filter((c): c is number => typeof c === 'number'))];
    const asigCodes = [...new Set(items.map((i) => i.codasig).filter((c): c is string => typeof c === 'string'))];
    const cohCodes = [...new Set(items.map((i) => i.codcohorte).filter((c): c is string => typeof c === 'string'))];

    const [profesores, subjects, cohortesList] = await Promise.all([
      this.prisma.profesores_cippsv.findMany({
        where: { cedula_profesor: { in: profCedulas } },
      }),
      this.prisma.pensumEstudios.findMany({
        where: { codasig: { in: asigCodes } },
      }),
      this.prisma.cohortes.findMany({
        where: { codcohorte: { in: cohCodes } },
      }),
    ]);

    const uniquePrograms = [...new Set(cohortesList.map((c) => `${c.codsede}_${c.codopest}`))];
    let programasList: any[] = [];
    if (uniquePrograms.length > 0) {
      programasList = await this.prisma.oportunidadesEstudio.findMany({
        where: {
          OR: uniquePrograms.map(p => {
            const [codsede, codopest] = p.split('_');
            return { codsede, codopest };
          })
        }
      });
    }

    const profMap = new Map(profesores.map((p) => [p.cedula_profesor, p]));
    const subjectMap = new Map(subjects.map((s) => [s.codasig, s]));
    const cohorteMap = new Map(cohortesList.map((c) => [c.codcohorte, c]));
    const programaMap = new Map(programasList.map((p) => [`${p.codsede}_${p.codopest}`, p]));

    const enrichedItems = items.map((item) => {
      const prof = item.cedula_profesor ? profMap.get(item.cedula_profesor) : null;
      const sub = item.codasig ? subjectMap.get(item.codasig) : null;
      const coh = item.codcohorte ? cohorteMap.get(item.codcohorte) : null;
      const progKey = coh ? `${coh.codsede}_${coh.codopest}` : '';
      const prog = progKey ? programaMap.get(progKey) : null;

      return {
        ...item,
        profesor: prof ? `${prof.apellidos_nombres}`.trim() : `C.I. ${item.cedula_profesor}`,
        asignatura_nombre: sub ? sub.asignatura : 'Desconocida',
        periodo: sub ? sub.periodos : null,
        creditos: sub ? sub.creditos : null,
        programa_nombre: prog ? (prog.mencion_especialidad || prog.titulo_a_otorgar) : 'Desconocido',
        cohorte_fecha_inicio: coh ? coh.fecha_inicio : null,
      };
    });

    return { items: enrichedItems, total };
  }

  async findOneActa(codcohorte: string, codasig: string, codacta: string, user: any) {
    const record = await this.prisma.registroActas.findUnique({
      where: {
        codcohorte_codasig_codacta: { codcohorte, codasig, codacta },
      },
    });

    if (!record) {
      throw new NotFoundException(
        `No se encontró el acta ${codacta} para la asignatura ${codasig} y cohorte ${codcohorte}`,
      );
    }

    // Validar asignación si es Profesor
    if (user.role === Role.PROFESOR && record.cedula_profesor !== Number(user.username)) {
      throw new ForbiddenException('No tienes permiso para ver los detalles de esta acta.');
    }

    return record;
  }

  async createActa(dto: CreateActaDto) {
    // 1. Validar que la asignatura (PensumEstudios) exista
    const asignaturas = await this.prisma.pensumEstudios.findMany({
      where: { codasig: dto.codasig },
    });
    if (asignaturas.length === 0) {
      throw new BadRequestException(`No existe la asignatura con código ${dto.codasig} en el pensum.`);
    }

    // 2. Validar que la cohorte exista y pertenezca a la misma sede y carrera que alguna de las asignaturas
    let cohorteValida = false;
    for (const asig of asignaturas) {
      const cohorte = await this.prisma.cohortes.findUnique({
        where: {
          codsede_codopest_codcohorte: {
            codsede: asig.codsede,
            codopest: asig.codopest,
            codcohorte: dto.codcohorte,
          },
        },
      });
      if (cohorte) {
        cohorteValida = true;
        break;
      }
    }

    if (!cohorteValida) {
      throw new BadRequestException(
        `La cohorte ${dto.codcohorte} no está disponible o no coincide con la sede y especialidad de la asignatura ${dto.codasig}.`,
      );
    }

    // 3. Validar profesor si es provisto
    if (dto.cedula_profesor) {
      const prof = await this.prisma.profesores_cippsv.findUnique({
        where: { cedula_profesor: dto.cedula_profesor },
      });
      if (!prof) {
        throw new BadRequestException(`El profesor con cédula ${dto.cedula_profesor} no existe.`);
      }
    }

    // 4. Validar existencia del acta duplicada
    const exists = await this.prisma.registroActas.findUnique({
      where: {
        codcohorte_codasig_codacta: {
          codcohorte: dto.codcohorte,
          codasig: dto.codasig,
          codacta: dto.codacta,
        },
      },
    });
    if (exists) {
      throw new ConflictException(
        `El acta con código ${dto.codacta} ya existe para esta materia y cohorte.`,
      );
    }

    const data: any = {
      codcohorte: dto.codcohorte,
      codasig: dto.codasig,
      codacta: dto.codacta,
      cedula_profesor: dto.cedula_profesor,
      fecha_creacion: new Date(),
    };
    if (dto.fecha_aprobacion) {
      data.fecha_aprobacion = new Date(dto.fecha_aprobacion);
    }

    return this.prisma.registroActas.create({
      data,
    });
  }

  async updateActa(
    codcohorte: string,
    codasig: string,
    codacta: string,
    dto: UpdateActaDto,
  ) {
    const record = await this.prisma.registroActas.findUnique({
      where: {
        codcohorte_codasig_codacta: { codcohorte, codasig, codacta },
      },
    });
    if (!record) {
      throw new NotFoundException(`El acta con código ${codacta} no existe.`);
    }

    if (dto.cedula_profesor) {
      const prof = await this.prisma.profesores_cippsv.findUnique({
        where: { cedula_profesor: dto.cedula_profesor },
      });
      if (!prof) {
        throw new BadRequestException(`El profesor con cédula ${dto.cedula_profesor} no existe.`);
      }
    }

    const data: any = {
      fecha_modificacion: new Date(),
    };
    if (dto.cedula_profesor !== undefined) {
      data.cedula_profesor = dto.cedula_profesor;
    }
    if (dto.fecha_aprobacion !== undefined) {
      data.fecha_aprobacion = dto.fecha_aprobacion ? new Date(dto.fecha_aprobacion) : null;
    }

    return this.prisma.registroActas.update({
      where: {
        codcohorte_codasig_codacta: { codcohorte, codasig, codacta },
      },
      data,
    });
  }

  async deleteActa(codcohorte: string, codasig: string, codacta: string) {
    const record = await this.prisma.registroActas.findUnique({
      where: {
        codcohorte_codasig_codacta: { codcohorte, codasig, codacta },
      },
    });
    if (!record) {
      throw new NotFoundException(`El acta con código ${codacta} no existe.`);
    }

    return this.prisma.registroActas.delete({
      where: {
        codcohorte_codasig_codacta: { codcohorte, codasig, codacta },
      },
    });
  }

  // ==========================================
  // GESTIÓN DE CALIFICACIONES (RecordNotas)
  // ==========================================

  async findAllNotas(
    params: { skip?: number; take?: number; search?: string; codacta?: string },
    user: any,
  ) {
    const { skip, take, search, codacta } = params;
    const where: any = {};

    // Si es Estudiante, filtrar solo su cédula
    if (user.role === Role.ESTUDIANTE) {
      where.cedula = Number(user.username);
    } else if (user.role === Role.PROFESOR) {
      // Profesores solo ven notas de actas asignadas a ellos
      const actas = await this.prisma.registroActas.findMany({
        where: { cedula_profesor: Number(user.username) },
        select: { codacta: true },
      });
      const codactas = actas.map((a) => a.codacta);
      where.codacta = { in: codactas };
    }

    if (codacta) {
      where.codacta = codacta;
    }

    if (search) {
      const parsedSearch = parseInt(search, 10);
      if (!isNaN(parsedSearch)) {
        if (user.role === Role.ESTUDIANTE && parsedSearch !== Number(user.username)) {
          where.cedula = Number(user.username);
        } else {
          where.cedula = parsedSearch;
        }
      } else {
        where.codacta = { contains: search };
      }
    }

    const [items, total] = await Promise.all([
      this.prisma.recordNotas.findMany({
        where,
        skip: skip ? Number(skip) : undefined,
        take: take ? Number(take) : undefined,
        orderBy: [{ cedula: 'asc' }],
      }),
      this.prisma.recordNotas.count({ where }),
    ]);

    // Enriquecer notas con nombres y apellidos de estudiantes
    const studentCedulas = [...new Set(items.map((i) => i.cedula))];
    const students = await this.prisma.datosPersonales.findMany({
      where: { cedula: { in: studentCedulas } },
      select: { cedula: true, nombres: true, apellidos: true },
    });

    const studentMap = new Map(students.map((s) => [s.cedula, s]));

    const enrichedItems = items.map((item) => {
      const st = studentMap.get(item.cedula);
      return {
        ...item,
        nombres: st ? st.nombres : 'Desconocido',
        apellidos: st ? st.apellidos : 'Desconocido',
      };
    });

    return { items: enrichedItems, total };
  }

  async findOneNota(codacta: string, cedula: number, user: any) {
    // Validar accesos antes de buscar en la BD si es un Estudiante
    if (user.role === Role.ESTUDIANTE && cedula !== Number(user.username)) {
      throw new ForbiddenException('No tienes permiso para ver esta calificación.');
    }

    const record = await this.prisma.recordNotas.findUnique({
      where: {
        codacta_cedula: { codacta, cedula },
      },
    });

    if (!record) {
      throw new NotFoundException(
        `No se encontró calificación para el estudiante ${cedula} en el acta ${codacta}`,
      );
    }

    if (user.role === Role.PROFESOR) {
      // Verificar si el acta pertenece al profesor
      const acta = await this.prisma.registroActas.findFirst({
        where: { codacta, cedula_profesor: Number(user.username) },
      });
      if (!acta) {
        throw new ForbiddenException('No tienes permiso para ver esta calificación.');
      }
    }

    return record;
  }

  async createNota(dto: CreateNotaDto, user: any) {
    // 1. Si es Profesor, validar asignación
    if (user.role === Role.PROFESOR) {
      const acta = await this.prisma.registroActas.findFirst({
        where: { codacta: dto.codacta, cedula_profesor: Number(user.username) },
      });
      if (!acta) {
        throw new ForbiddenException('No tienes permiso para registrar notas en este acta.');
      }
    }

    // 2. Validar que el estudiante exista en DatosPersonales
    const student = await this.prisma.datosPersonales.findUnique({
      where: { cedula: dto.cedula },
    });
    if (!student) {
      throw new BadRequestException(`El estudiante con cédula ${dto.cedula} no existe.`);
    }

    // 3. Validar que el acta exista en RegistroActas o Multiactas
    let acta: any = await this.prisma.registroActas.findFirst({
      where: { codacta: dto.codacta },
    });
    let isMultiacta = false;
    if (!acta) {
      const multiacta = await this.prisma.multiactas.findFirst({
        where: { codacta: dto.codacta },
      });
      if (!multiacta) {
        throw new BadRequestException(`El acta con código ${dto.codacta} no existe.`);
      }
      acta = multiacta as any;
      isMultiacta = true;
    }

    // 4. Validar prelaciones (requisitos académicos previos)
    const cohorte = await this.prisma.cohortes.findFirst({
      where: { codcohorte: acta.codcohorte },
    });
    if (cohorte) {
      const requirements = await this.prisma.pensum_prelaciones.findMany({
        where: {
          codsede: cohorte.codsede,
          codopest: cohorte.codopest,
          codasig: acta.codasig,
        },
      });

      for (const req of requirements) {
        // Buscar actas ordinarias para la materia prelante
        const ordActas = await this.prisma.registroActas.findMany({
          where: { codasig: req.prelacion },
          select: { codacta: true },
        });
        // Buscar multiactas para la materia prelante
        const multiActas = await this.prisma.multiactas.findMany({
          where: { codasig: req.prelacion },
          select: { codacta: true },
        });
        const actasCodes = [...ordActas.map((a: any) => a.codacta), ...multiActas.map((ma: any) => ma.codacta)];

        // Buscar en record_notas si hay nota aprobatoria en alguna de esas actas
        const approvedNote = await this.prisma.recordNotas.findFirst({
          where: {
            cedula: dto.cedula,
            codacta: { in: actasCodes },
            OR: [
              { calificacion: { gte: 15 } },
              { calificacion: { in: [100, 110, 120, 212] } }, // Equivalencias y TG
            ],
          },
        });

        if (!approvedNote) {
          const materiaPrelante = await this.prisma.pensumEstudios.findFirst({
            where: { codasig: req.prelacion },
            select: { asignatura: true },
          });
          const nombreMateria = materiaPrelante ? materiaPrelante.asignatura : req.prelacion;
          throw new BadRequestException(
            `No se puede registrar la calificación: el estudiante no ha aprobado la prelación obligatoria "${nombreMateria}" (${req.prelacion}).`,
          );
        }
      }
    }

    // 5. Validar duplicado
    const exists = await this.prisma.recordNotas.findUnique({
      where: {
        codacta_cedula: { codacta: dto.codacta, cedula: dto.cedula },
      },
    });
    if (exists) {
      throw new ConflictException(
        `Ya existe una calificación registrada para el estudiante ${dto.cedula} en el acta ${dto.codacta}.`,
      );
    }

    return this.prisma.recordNotas.create({
      data: {
        codacta: dto.codacta,
        cedula: dto.cedula,
        calificacion: dto.calificacion ?? null,
        codeq: dto.codeq ?? null,
        fecha_creacion: new Date(),
      },
    });
  }

  async updateNota(codacta: string, cedula: number, dto: UpdateNotaDto, user: any) {
    // Verificar existencia
    const record = await this.findOneNota(codacta, cedula, user);

    // Si es Profesor, validar asignación del acta
    if (user.role === Role.PROFESOR) {
      const acta = await this.prisma.registroActas.findFirst({
        where: { codacta, cedula_profesor: Number(user.username) },
      });
      if (!acta) {
        throw new ForbiddenException('No tienes permiso para modificar notas en este acta.');
      }
    }

    const data: any = {
      fecha_modificacion: new Date(),
    };
    if (dto.calificacion !== undefined) {
      data.calificacion = dto.calificacion;
    }
    if (dto.codeq !== undefined) {
      data.codeq = dto.codeq;
    }

    return this.prisma.recordNotas.update({
      where: {
        codacta_cedula: { codacta, cedula },
      },
      data,
    });
  }

  async deleteNota(codacta: string, cedula: number) {
    // Verificar existencia (solo admin puede borrar)
    await this.prisma.recordNotas.findUnique({
      where: {
        codacta_cedula: { codacta, cedula },
      },
    });

    return this.prisma.recordNotas.delete({
      where: {
        codacta_cedula: { codacta, cedula },
      },
    });
  }

  async findLastTeacherForSubject(codasig: string) {
    const lastActa = await this.prisma.registroActas.findFirst({
      where: {
        codasig,
        cedula_profesor: { not: null },
      },
      orderBy: { fecha_creacion: 'desc' },
      select: { cedula_profesor: true },
    });

    if (lastActa && lastActa.cedula_profesor) {
      const prof = await this.prisma.profesores_cippsv.findUnique({
        where: { cedula_profesor: lastActa.cedula_profesor },
      });
      if (prof) {
        return {
          cedula_profesor: prof.cedula_profesor,
          apellidos_nombres: prof.apellidos_nombres,
        };
      }
    }

    const lastMultiacta = await this.prisma.multiactas.findFirst({
      where: {
        codasig,
        OR: [
          { cedula_profesor1: { not: null } },
          { cedula_profesor2: { not: null } },
          { cedula_profesor3: { not: null } },
          { cedula_profesor4: { not: null } },
          { cedula_profesor5: { not: null } },
        ],
      },
      orderBy: { fecha_creacion: 'desc' },
    });

    if (lastMultiacta) {
      const cedula = lastMultiacta.cedula_profesor1 || 
                     lastMultiacta.cedula_profesor2 || 
                     lastMultiacta.cedula_profesor3 || 
                     lastMultiacta.cedula_profesor4 || 
                     lastMultiacta.cedula_profesor5;
      if (cedula) {
        const prof = await this.prisma.profesores_cippsv.findUnique({
          where: { cedula_profesor: cedula },
        });
        if (prof) {
          return {
            cedula_profesor: prof.cedula_profesor,
            apellidos_nombres: prof.apellidos_nombres,
          };
        }
      }
    }

    return null;
  }

  async generateActaPdf(codcohorte: string, codasig: string, codacta: string, user: any): Promise<Buffer> {
    const acta = await this.prisma.registroActas.findUnique({
      where: {
        codcohorte_codasig_codacta: { codcohorte, codasig, codacta },
      },
    });

    if (!acta) {
      throw new NotFoundException(`No se encontró el acta ${codacta}`);
    }

    const coh = await this.prisma.cohortes.findFirst({
      where: { codcohorte },
    });
    if (!coh) {
      throw new NotFoundException(`No se encontró la cohorte ${codcohorte}`);
    }

    const prog = await this.prisma.oportunidadesEstudio.findFirst({
      where: { codopest: coh.codopest, codsede: coh.codsede },
    });

    const dir = await this.prisma.directorio_cippsv.findFirst({
      where: { codsede: coh.codsede },
    });
    const ciudad = dir?.ciudad || 'Caracas';

    const sub = await this.prisma.pensumEstudios.findFirst({
      where: { codasig, codsede: coh.codsede },
    });
    const asignatura_nombre = sub ? sub.asignatura : 'Desconocida';
    const creditos = sub ? sub.creditos : 0;
    const periodo = sub ? sub.periodos : 1;

    let profesor_nombre = 'No asignado';
    if (acta.cedula_profesor) {
      const prof = await this.prisma.profesores_cippsv.findUnique({
        where: { cedula_profesor: acta.cedula_profesor },
      });
      if (prof) {
        profesor_nombre = prof.apellidos_nombres;
      }
    }

    const notasList = await this.prisma.recordNotas.findMany({
      where: { codacta },
      orderBy: { cedula: 'asc' },
    });

    const studentCedulas = notasList.map((n: any) => n.cedula);
    const students = await this.prisma.datosPersonales.findMany({
      where: { cedula: { in: studentCedulas } },
    });
    const studentMap = new Map(students.map((s: any) => [s.cedula, s]));

    const enrichedNotas = notasList.map((n: any) => {
      const s: any = studentMap.get(n.cedula);
      return {
        ...n,
        nombres_apellidos: s ? `${s.nombres} ${s.apellidos}`.trim() : `C.I. ${n.cedula}`,
      };
    });

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

      doc.image('logo.png', 50, 30, { width: 50 });

      doc.font('Helvetica-Bold').fillColor('#000000').fontSize(10);
      doc.text('Centro de Investigaciones Psiquiátricas, Psicológicas y', 110, 32, { align: 'center', width: 370 });
      doc.text('Sexológicas de Venezuela', 110, 43, { align: 'center', width: 370 });
      doc.font('Helvetica').fontSize(8.5);
      doc.text('Coordinación Académica', 110, 54, { align: 'center', width: 370 });
      doc.text('Oficina de Control de Estudios', 110, 65, { align: 'center', width: 370 });

      const yHeaderEnd = 78;

      doc.font('Helvetica-Bold').fontSize(12).fillColor('#002855');
      doc.text('ACTA DE EVALUACIÓN ESCOLAR', 50, yHeaderEnd, { align: 'center' });

      const yMeta = yHeaderEnd + 18;
      doc.font('Helvetica-Bold').fontSize(8.5).fillColor('#000000');
      doc.text('Programa: ', 50, yMeta, { lineBreak: false } as any)
         .font('Helvetica').text(prog ? `${prog.tipo} en Ciencias Mención ${prog.mencion_especialidad || prog.titulo_a_otorgar}` : 'Postgrado');

      doc.font('Helvetica-Bold').text('Asignatura: ', 50, yMeta + 11, { lineBreak: false } as any)
         .font('Helvetica').text(`${asignatura_nombre} (${codasig})`);

      doc.font('Helvetica-Bold').text('Profesor: ', 50, yMeta + 22, { lineBreak: false } as any)
         .font('Helvetica').text(profesor_nombre + (acta.cedula_profesor ? ` (C.I. ${acta.cedula_profesor})` : ''));

      doc.font('Helvetica-Bold').text('Código Acta: ', 350, yMeta, { lineBreak: false } as any)
         .font('Helvetica').text(codacta);

      doc.font('Helvetica-Bold').text('Cohorte: ', 350, yMeta + 11, { lineBreak: false } as any)
         .font('Helvetica').text(codcohorte);

      doc.font('Helvetica-Bold').text('Período / Créditos: ', 350, yMeta + 22, { lineBreak: false } as any)
         .font('Helvetica').text(`P-${periodo} / ${creditos} U.C.`);

      const startX = 50;
      let currentY = yMeta + 38;

      const rowHeight = 18;
      doc.fillColor('#002855').rect(startX, currentY, 500, rowHeight).fill();
      doc.font('Helvetica-Bold').fillColor('#ffffff').fontSize(8.5);
      doc.text('Nº', startX + 5, currentY + 5, { width: 25, align: 'center' });
      doc.text('Cédula', startX + 35, currentY + 5);
      doc.text('Apellidos y Nombres del Estudiante', startX + 110, currentY + 5);
      doc.text('Calificación Obtenida', startX + 340, currentY + 5, { width: 155, align: 'center' });

      currentY += rowHeight;
      doc.fontSize(8).fillColor('#000000');

      if (enrichedNotas.length === 0) {
        doc.fillColor('#ffffff').rect(startX, currentY, 500, rowHeight).fill();
        doc.fillColor('#000000').font('Helvetica');
        doc.text('No hay calificaciones cargadas en este acta.', startX + 110, currentY + 5);
        
        doc.strokeColor('#000000').lineWidth(0.5);
        doc.rect(startX, currentY, 500, rowHeight).stroke();
        currentY += rowHeight;
      } else {
        enrichedNotas.forEach((n: any, idx: number) => {
          doc.fillColor('#ffffff').rect(startX, currentY, 500, rowHeight).fill();

          doc.fillColor('#000000').font('Helvetica');
          doc.text(String(idx + 1), startX + 5, currentY + 5, { width: 25, align: 'center' });
          
          const s: any = studentMap.get(n.cedula);
          const nacLetter = s?.nacionalidad === 'Venezolana' ? 'V' : 'E';
          doc.text(`${nacLetter}-${n.cedula.toLocaleString('es-VE')}`, startX + 35, currentY + 5);
          doc.text(n.nombres_apellidos, startX + 110, currentY + 5, { width: 220, height: 12, ellipsis: true });
          doc.text(formatNota(n.calificacion), startX + 340, currentY + 5, { width: 155, align: 'center' });

          doc.strokeColor('#000000').lineWidth(0.5);
          doc.moveTo(startX, currentY + rowHeight).lineTo(startX + 500, currentY + rowHeight).stroke();
          
          doc.moveTo(startX, currentY).lineTo(startX, currentY + rowHeight).stroke();
          doc.moveTo(startX + 30, currentY).lineTo(startX + 30, currentY + rowHeight).stroke();
          doc.moveTo(startX + 105, currentY).lineTo(startX + 105, currentY + rowHeight).stroke();
          doc.moveTo(startX + 335, currentY).lineTo(startX + 335, currentY + rowHeight).stroke();
          doc.moveTo(startX + 500, currentY).lineTo(startX + 500, currentY + rowHeight).stroke();

          currentY += rowHeight;
        });
      }

      currentY += 15;
      
      const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
      const today = new Date();
      const dateString = `${ciudad}, ${today.getDate()} de ${meses[today.getMonth()]} del ${today.getFullYear()}`;
      doc.font('Helvetica').fontSize(9).text(dateString, 50, currentY);

      currentY += 45;

      doc.strokeColor('#000000').lineWidth(0.5);
      doc.moveTo(50, currentY).lineTo(180, currentY).stroke();
      doc.moveTo(215, currentY).lineTo(345, currentY).stroke();
      doc.moveTo(370, currentY).lineTo(500, currentY).stroke();

      doc.font('Helvetica').fontSize(7.5);
      doc.text('FIRMA DEL PROFESOR', 50, currentY + 3, { align: 'center', width: 130 });
      doc.text(`C.I. ${acta.cedula_profesor || ''}`, 50, currentY + 12, { align: 'center', width: 130 });

      doc.text('Lic. Mercedes Labrador', 215, currentY + 3, { align: 'center', width: 130 });
      doc.text('Jefe de Control de Estudios', 215, currentY + 12, { align: 'center', width: 130 });

      doc.text('Esp. Herman Y. Bandez S.', 370, currentY + 3, { align: 'center', width: 130 });
      doc.text('Secretario', 370, currentY + 12, { align: 'center', width: 130 });

      doc.end();
    });
  }
}
