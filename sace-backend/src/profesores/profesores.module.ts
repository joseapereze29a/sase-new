import { Module } from '@nestjs/common';
import { ProfesoresController } from './profesores.controller';
import { ProfesoresService } from './profesores.service';
import { PrismaService } from '../prisma/prisma.service';

@Module({
  controllers: [ProfesoresController],
  providers: [ProfesoresService, PrismaService],
  exports: [ProfesoresService],
})
export class ProfesoresModule {}
