import {
  Controller,
  Get,
  Post,
  Put,
  Delete,
  Body,
  Param,
  Query,
  UseGuards,
  Res,
} from '@nestjs/common';
import * as express from 'express';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { Role } from '../auth/enums/role.enum';
import { AcademicoService } from './academico.service';
import { CreateOportunidadDto } from './dto/create-oportunidad.dto';
import { UpdateOportunidadDto } from './dto/update-oportunidad.dto';
import { CreatePensumDto } from './dto/create-pensum.dto';
import { UpdatePensumDto } from './dto/update-pensum.dto';

@Controller('academico')
@UseGuards(JwtAuthGuard)
export class AcademicoController {
  constructor(private readonly service: AcademicoService) {}

  // ==========================================
  // OPORTUNIDADES DE ESTUDIO
  // ==========================================

  @Get('oportunidades')
  async findAllOportunidades(
    @Query('skip') skip?: number,
    @Query('take') take?: number,
    @Query('search') search?: string,
  ) {
    return this.service.findAllOportunidades({ skip, take, search });
  }

  @Get('oportunidades/:codsede/:codopest')
  async findOneOportunidad(
    @Param('codsede') codsede: string,
    @Param('codopest') codopest: string,
  ) {
    return this.service.findOneOportunidad(codsede, codopest);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Post('oportunidades')
  async createOportunidad(@Body() dto: CreateOportunidadDto) {
    return this.service.createOportunidad(dto);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Put('oportunidades/:codsede/:codopest')
  async updateOportunidad(
    @Param('codsede') codsede: string,
    @Param('codopest') codopest: string,
    @Body() dto: UpdateOportunidadDto,
  ) {
    return this.service.updateOportunidad(codsede, codopest, dto);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Delete('oportunidades/:codsede/:codopest')
  async deleteOportunidad(
    @Param('codsede') codsede: string,
    @Param('codopest') codopest: string,
  ) {
    return this.service.deleteOportunidad(codsede, codopest);
  }

  // ==========================================
  // PENSUM DE ESTUDIOS
  // ==========================================

  @Get('pensum')
  async findAllPensum(
    @Query('skip') skip?: number,
    @Query('take') take?: number,
    @Query('codsede') codsede?: string,
    @Query('codopest') codopest?: string,
    @Query('search') search?: string,
  ) {
    return this.service.findAllPensum({ skip, take, codsede, codopest, search });
  }

  @Get('pensum/pdf')
  async downloadPensumPdf(
    @Query('codsede') codsede: string,
    @Query('codopest') codopest: string,
    @Res() res: express.Response,
  ) {
    const buffer = await this.service.generatePensumPdf(codsede, codopest);
    res.set({
      'Content-Type': 'application/pdf',
      'Content-Disposition': `attachment; filename=pensum_${codopest}.pdf`,
      'Content-Length': buffer.length,
    });
    res.end(buffer);
  }

  @Get('pensum/:codsede/:codopest/:codasig')
  async findOnePensum(
    @Param('codsede') codsede: string,
    @Param('codopest') codopest: string,
    @Param('codasig') codasig: string,
  ) {
    return this.service.findOnePensum(codsede, codopest, codasig);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Post('pensum')
  async createPensum(@Body() dto: CreatePensumDto) {
    return this.service.createPensum(dto);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Put('pensum/:codsede/:codopest/:codasig')
  async updatePensum(
    @Param('codsede') codsede: string,
    @Param('codopest') codopest: string,
    @Param('codasig') codasig: string,
    @Body() dto: UpdatePensumDto,
  ) {
    return this.service.updatePensum(codsede, codopest, codasig, dto);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Delete('pensum/:codsede/:codopest/:codasig')
  async deletePensum(
    @Param('codsede') codsede: string,
    @Param('codopest') codopest: string,
    @Param('codasig') codasig: string,
  ) {
    return this.service.deletePensum(codsede, codopest, codasig);
  }
}
