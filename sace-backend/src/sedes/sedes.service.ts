import { Injectable, NotFoundException, ConflictException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CreateSedeDto } from './dto/create-sede.dto';
import { UpdateSedeDto } from './dto/update-sede.dto';

@Injectable()
export class SedesService {
  constructor(private readonly prisma: PrismaService) {}

  async findAll(search?: string) {
    const where: any = {};
    if (search) {
      where.OR = [
        { codsede: { contains: search } },
        { ciudad: { contains: search } },
        { edo_prov: { contains: search } },
        { director_coordinador: { contains: search } },
      ];
    }
    return this.prisma.directorio_cippsv.findMany({
      where,
      orderBy: { ciudad: 'asc' },
    });
  }

  async findOne(codsede: string) {
    const record = await this.prisma.directorio_cippsv.findUnique({
      where: { codsede },
    });
    if (!record) {
      throw new NotFoundException(`No se encontró la sede con código ${codsede}`);
    }
    return record;
  }

  async create(dto: CreateSedeDto) {
    // Verificar si ya existe
    const exists = await this.prisma.directorio_cippsv.findUnique({
      where: { codsede: dto.codsede },
    });
    if (exists) {
      throw new ConflictException(`La sede con código ${dto.codsede} ya existe.`);
    }

    return this.prisma.directorio_cippsv.create({
      data: dto,
    });
  }

  async update(codsede: string, dto: UpdateSedeDto) {
    await this.findOne(codsede);

    return this.prisma.directorio_cippsv.update({
      where: { codsede },
      data: dto,
    });
  }

  async delete(codsede: string) {
    await this.findOne(codsede);

    return this.prisma.directorio_cippsv.delete({
      where: { codsede },
    });
  }
}
