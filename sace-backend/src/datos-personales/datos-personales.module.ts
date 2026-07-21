import { Module } from '@nestjs/common';
import { DatosPersonalesService } from './datos-personales.service';
import { DatosPersonalesController } from './datos-personales.controller';
import { PrismaService } from '../prisma/prisma.service';

@Module({
  controllers: [DatosPersonalesController],
  providers: [DatosPersonalesService, PrismaService],
  exports: [DatosPersonalesService],
})
export class DatosPersonalesModule {}
