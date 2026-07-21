import { Injectable, NotFoundException, ConflictException, BadRequestException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CreateCohorteDto } from './dto/create-cohorte.dto';
import { UpdateCohorteDto } from './dto/update-cohorte.dto';
import { Role } from '../auth/enums/role.enum';

@Injectable()
export class CohortesService {
  constructor(private readonly prisma: PrismaService) {}

  async findAll(
    params: {
      skip?: number;
      take?: number;
      search?: string;
      codsede?: string;
      codopest?: string;
    },
    user?: any,
  ) {
    const { skip, take, search, codsede, codopest } = params;
    const where: any = {};

    if (codsede) where.codsede = codsede;
    if (codopest) where.codopest = codopest;

    if (user && user.role === Role.PROFESOR) {
      // Profesores solo ven cohortes en las que tengan actas asignadas
      const teacherActas = await this.prisma.registroActas.findMany({
        where: { cedula_profesor: Number(user.username) },
        select: { codcohorte: true },
      });
      const teacherCohortes = [...new Set(teacherActas.map((a) => a.codcohorte))];
      
      // Si el profesor no tiene ninguna cohorte asignada, retornar vacío
      if (teacherCohortes.length === 0) {
        return { items: [], total: 0 };
      }

      if (where.codcohorte) {
        if (Array.isArray(where.codcohorte.in)) {
          where.codcohorte.in = where.codcohorte.in.filter((c: string) => teacherCohortes.includes(c));
        } else if (typeof where.codcohorte === 'string') {
          if (!teacherCohortes.includes(where.codcohorte)) {
            return { items: [], total: 0 };
          }
        }
      } else {
        where.codcohorte = { in: teacherCohortes };
      }
    }

    if (search) {
      where.OR = [
        { codcohorte: { contains: search } },
        { periodo_lectivo: { contains: search } },
        { codsede: { contains: search } },
        { codopest: { contains: search } },
      ];
    }

    const [items, total] = await Promise.all([
      this.prisma.cohortes.findMany({
        where,
        skip: skip ? Number(skip) : undefined,
        take: take ? Number(take) : undefined,
        orderBy: [{ codcohorte: 'asc' }],
      }),
      this.prisma.cohortes.count({ where }),
    ]);

    return { items, total };
  }

  async findOne(codsede: string, codopest: string, codcohorte: string) {
    const record = await this.prisma.cohortes.findUnique({
      where: {
        codsede_codopest_codcohorte: { codsede, codopest, codcohorte },
      },
    });
    if (!record) {
      throw new NotFoundException(
        `No se encontró cohorte con sede ${codsede}, programa ${codopest} y código ${codcohorte}`,
      );
    }
    return record;
  }

  async create(dto: CreateCohorteDto) {
    // 1. Validar que exista la Oportunidad de Estudio
    const opExists = await this.prisma.oportunidadesEstudio.findUnique({
      where: {
        codsede_codopest: { codsede: dto.codsede, codopest: dto.codopest },
      },
    });
    if (!opExists) {
      throw new BadRequestException(
        `No existe la oportunidad de estudio con sede ${dto.codsede} y programa ${dto.codopest}`,
      );
    }

    // 2. Validar que la cohorte no exista previamente
    const cohExists = await this.prisma.cohortes.findUnique({
      where: {
        codsede_codopest_codcohorte: {
          codsede: dto.codsede,
          codopest: dto.codopest,
          codcohorte: dto.codcohorte,
        },
      },
    });
    if (cohExists) {
      throw new ConflictException(
        `La cohorte ${dto.codcohorte} para sede ${dto.codsede} y programa ${dto.codopest} ya existe.`,
      );
    }

    const data: any = {
      codsede: dto.codsede,
      codopest: dto.codopest,
      codcohorte: dto.codcohorte,
      periodo_lectivo: dto.periodo_lectivo,
      fecha_creacion: new Date(),
    };
    if (dto.fecha_inicio) {
      data.fecha_inicio = new Date(dto.fecha_inicio);
    }

    return this.prisma.cohortes.create({
      data,
    });
  }

  async update(codsede: string, codopest: string, codcohorte: string, dto: UpdateCohorteDto) {
    // Verificar existencia
    await this.findOne(codsede, codopest, codcohorte);

    const data: any = {
      fecha_modificacion: new Date(),
    };
    if (dto.periodo_lectivo !== undefined) {
      data.periodo_lectivo = dto.periodo_lectivo;
    }
    if (dto.fecha_inicio !== undefined) {
      data.fecha_inicio = dto.fecha_inicio ? new Date(dto.fecha_inicio) : null;
    }

    return this.prisma.cohortes.update({
      where: {
        codsede_codopest_codcohorte: { codsede, codopest, codcohorte },
      },
      data,
    });
  }

  async delete(codsede: string, codopest: string, codcohorte: string) {
    // Verificar existencia
    await this.findOne(codsede, codopest, codcohorte);

    return this.prisma.cohortes.delete({
      where: {
        codsede_codopest_codcohorte: { codsede, codopest, codcohorte },
      },
    });
  }

  async findCiudades(user?: any) {
    if (user && user.role === Role.PROFESOR) {
      const teacherActas = await this.prisma.registroActas.findMany({
        where: { cedula_profesor: Number(user.username) },
        select: { codcohorte: true },
      });
      const teacherCohortes = [...new Set(teacherActas.map((a) => a.codcohorte))];
      const cohortesList = await this.prisma.cohortes.findMany({
        where: { codcohorte: { in: teacherCohortes } },
        select: { codsede: true },
      });
      const teacherSedes = [...new Set(cohortesList.map((c) => c.codsede))];
      const records = await this.prisma.directorio_cippsv.findMany({
        where: { codsede: { in: teacherSedes } },
        select: { ciudad: true },
        distinct: ['ciudad'],
        orderBy: { ciudad: 'asc' },
      });
      return records.map((r) => r.ciudad).filter(Boolean);
    }

    const records = await this.prisma.directorio_cippsv.findMany({
      select: { ciudad: true },
      distinct: ['ciudad'],
      orderBy: { ciudad: 'asc' },
    });
    return records.map(r => r.ciudad).filter(Boolean);
  }

  async findProgramasPorCiudad(ciudad: string, user?: any) {
    const sedes = await this.prisma.directorio_cippsv.findMany({
      where: { ciudad },
      select: { codsede: true },
    });
    const codsedes = sedes.map(s => s.codsede);

    const where: any = { codsede: { in: codsedes } };

    if (user && user.role === Role.PROFESOR) {
      const teacherActas = await this.prisma.registroActas.findMany({
        where: { cedula_profesor: Number(user.username) },
        select: { codcohorte: true },
      });
      const teacherCohortes = [...new Set(teacherActas.map((a) => a.codcohorte))];
      const cohortesList = await this.prisma.cohortes.findMany({
        where: { codcohorte: { in: teacherCohortes } },
        select: { codopest: true },
      });
      const teacherPrograms = [...new Set(cohortesList.map((c) => c.codopest))];
      where.codopest = { in: teacherPrograms };
    }

    return this.prisma.oportunidadesEstudio.findMany({
      where,
      orderBy: { titulo_a_otorgar: 'asc' },
    });
  }
}
