import {
  Injectable,
  NotFoundException,
  ConflictException,
  BadRequestException,
  ForbiddenException,
} from '@nestjs/common';
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

    // Si es Profesor, filtrar solo las asignadas a él
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

    // Obtener detalles de profesores y asignaturas en lote para enriquecer las actas
    const profCedulas = [...new Set(items.map((i) => i.cedula_profesor).filter((c): c is number => typeof c === 'number'))];
    const asigCodes = [...new Set(items.map((i) => i.codasig).filter((c): c is string => typeof c === 'string'))];

    const [profesores, subjects] = await Promise.all([
      this.prisma.profesores_cippsv.findMany({
        where: { cedula_profesor: { in: profCedulas } },
      }),
      this.prisma.pensumEstudios.findMany({
        where: { codasig: { in: asigCodes } },
      }),
    ]);

    const profMap = new Map(profesores.map((p) => [p.cedula_profesor, p]));
    const subjectMap = new Map(subjects.map((s) => [s.codasig, s]));

    const enrichedItems = items.map((item) => {
      const prof = item.cedula_profesor ? profMap.get(item.cedula_profesor) : null;
      const sub = item.codasig ? subjectMap.get(item.codasig) : null;
      return {
        ...item,
        profesor: prof ? `${prof.apellidos_nombres}`.trim() : `C.I. ${item.cedula_profesor}`,
        asignatura_nombre: sub ? sub.asignatura : 'Desconocida',
        periodo: sub ? sub.periodos : null,
        creditos: sub ? sub.creditos : null,
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
        const actasCodes = [...ordActas.map((a) => a.codacta), ...multiActas.map((ma) => ma.codacta)];

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
}
