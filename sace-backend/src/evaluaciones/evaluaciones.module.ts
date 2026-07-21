import { Module } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { EvaluacionesController } from './evaluaciones.controller';
import { EvaluacionesService } from './evaluaciones.service';

@Module({
  controllers: [EvaluacionesController],
  providers: [EvaluacionesService, PrismaService],
  exports: [EvaluacionesService],
})
export class EvaluacionesModule {}
