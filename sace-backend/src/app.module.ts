import { Module } from '@nestjs/common';
import { AppController } from './app.controller';
import { AppService } from './app.service';
import { AuthModule } from './auth/auth.module';
import { PrismaService } from './prisma/prisma.service';
import { DatosPersonalesModule } from './datos-personales/datos-personales.module';
import { AcademicoModule } from './academico/academico.module';
import { CohortesModule } from './cohortes/cohortes.module';
import { ProfesoresModule } from './profesores/profesores.module';
import { EvaluacionesModule } from './evaluaciones/evaluaciones.module';
import { SedesModule } from './sedes/sedes.module';

@Module({
  imports: [
    AuthModule,
    DatosPersonalesModule,
    AcademicoModule,
    CohortesModule,
    ProfesoresModule,
    EvaluacionesModule,
    SedesModule,
  ],
  controllers: [AppController],
  providers: [AppService, PrismaService],
})
export class AppModule {}
