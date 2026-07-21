import { Injectable, NotFoundException, ConflictException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CreateProfesorDto } from './dto/create-profesor.dto';
import { UpdateProfesorDto } from './dto/update-profesor.dto';

@Injectable()
export class ProfesoresService {
  constructor(private readonly prisma: PrismaService) {}

  async findAll(params: { skip?: number; take?: number; search?: string }) {
    const { skip, take, search } = params;
    const where: any = {};

    if (search) {
      const parsedSearch = parseInt(search, 10);
      if (!isNaN(parsedSearch)) {
        where.cedula_profesor = parsedSearch;
      } else {
        where.OR = [
          { apellidos_nombres: { contains: search } },
          { nombres: { contains: search } },
        ];
      }
    }

    const [items, total] = await Promise.all([
      this.prisma.profesores_cippsv.findMany({
        where,
        skip: skip ? Number(skip) : undefined,
        take: take ? Number(take) : undefined,
        orderBy: { cedula_profesor: 'asc' },
      }),
      this.prisma.profesores_cippsv.count({ where }),
    ]);

    return { items, total };
  }

  async findOne(cedula_profesor: number) {
    const record = await this.prisma.profesores_cippsv.findUnique({
      where: { cedula_profesor },
    });
    if (!record) {
      throw new NotFoundException(
        `No se encontró profesor con cédula ${cedula_profesor}`,
      );
    }
    return record;
  }

  async create(dto: CreateProfesorDto) {
    const exists = await this.prisma.profesores_cippsv.findUnique({
      where: { cedula_profesor: dto.cedula_profesor },
    });
    if (exists) {
      throw new ConflictException(
        `El profesor con cédula ${dto.cedula_profesor} ya existe en el directorio.`,
      );
    }

    return this.prisma.profesores_cippsv.create({
      data: dto,
    });
  }

  async update(cedula_profesor: number, dto: UpdateProfesorDto) {
    // Verificar existencia
    await this.findOne(cedula_profesor);

    return this.prisma.profesores_cippsv.update({
      where: { cedula_profesor },
      data: dto,
    });
  }

  async delete(cedula_profesor: number) {
    // Verificar existencia
    await this.findOne(cedula_profesor);

    return this.prisma.profesores_cippsv.delete({
      where: { cedula_profesor },
    });
  }
}
