import { Module } from '@nestjs/common';
import { SedesService } from './sedes.service';
import { SedesController } from './sedes.controller';
import { PrismaService } from '../prisma/prisma.service';

@Module({
  providers: [SedesService, PrismaService],
  controllers: [SedesController],
  exports: [SedesService],
})
export class SedesModule {}
