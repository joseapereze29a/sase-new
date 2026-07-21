import { Module } from '@nestjs/common';
import { CohortesController } from './cohortes.controller';
import { CohortesService } from './cohortes.service';
import { PrismaService } from '../prisma/prisma.service';

@Module({
  controllers: [CohortesController],
  providers: [CohortesService, PrismaService],
  exports: [CohortesService],
})
export class CohortesModule {}
