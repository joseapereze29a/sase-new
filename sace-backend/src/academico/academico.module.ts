import { Module } from '@nestjs/common';
import { AcademicoService } from './academico.service';
import { AcademicoController } from './academico.controller';
import { PrismaService } from '../prisma/prisma.service';

@Module({
  controllers: [AcademicoController],
  providers: [AcademicoService, PrismaService],
  exports: [AcademicoService],
})
export class AcademicoModule {}
