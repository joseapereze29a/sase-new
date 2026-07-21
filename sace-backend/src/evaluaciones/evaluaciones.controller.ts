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
  Request,
  ParseIntPipe,
} from '@nestjs/common';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { Role } from '../auth/enums/role.enum';
import { EvaluacionesService } from './evaluaciones.service';
import { CreateActaDto } from './dto/create-acta.dto';
import { UpdateActaDto } from './dto/update-acta.dto';
import { CreateNotaDto } from './dto/create-nota.dto';
import { UpdateNotaDto } from './dto/update-nota.dto';

@Controller('evaluaciones')
@UseGuards(JwtAuthGuard)
export class EvaluacionesController {
  constructor(private readonly service: EvaluacionesService) {}

  // ==========================================
  // ENDPOINTS PARA ACTAS DE EVALUACIÓN
  // ==========================================

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR, Role.COORDINADOR, Role.PROFESOR)
  @Get('actas')
  async findAllActas(
    @Query('skip') skip?: number,
    @Query('take') take?: number,
    @Query('search') search?: string,
    @Query('codcohorte') codcohorte?: string,
    @Request() req?: any,
  ) {
    return this.service.findAllActas({ skip, take, search, codcohorte }, req.user);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR, Role.COORDINADOR)
  @Get('sugerir-profesor')
  async findLastTeacherForSubject(@Query('codasig') codasig: string) {
    return this.service.findLastTeacherForSubject(codasig);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR, Role.COORDINADOR, Role.PROFESOR)
  @Get('actas/:codcohorte/:codasig/:codacta')
  async findOneActa(
    @Param('codcohorte') codcohorte: string,
    @Param('codasig') codasig: string,
    @Param('codacta') codacta: string,
    @Request() req?: any,
  ) {
    return this.service.findOneActa(codcohorte, codasig, codacta, req.user);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Post('actas')
  async createActa(@Body() dto: CreateActaDto) {
    return this.service.createActa(dto);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Put('actas/:codcohorte/:codasig/:codacta')
  async updateActa(
    @Param('codcohorte') codcohorte: string,
    @Param('codasig') codasig: string,
    @Param('codacta') codacta: string,
    @Body() dto: UpdateActaDto,
  ) {
    return this.service.updateActa(codcohorte, codasig, codacta, dto);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Delete('actas/:codcohorte/:codasig/:codacta')
  async deleteActa(
    @Param('codcohorte') codcohorte: string,
    @Param('codasig') codasig: string,
    @Param('codacta') codacta: string,
  ) {
    return this.service.deleteActa(codcohorte, codasig, codacta);
  }

  // ==========================================
  // ENDPOINTS PARA CALIFICACIONES
  // ==========================================

  @Get('notas')
  async findAllNotas(
    @Query('skip') skip?: number,
    @Query('take') take?: number,
    @Query('search') search?: string,
    @Query('codacta') codacta?: string,
    @Request() req?: any,
  ) {
    return this.service.findAllNotas({ skip, take, search, codacta }, req.user);
  }

  @Get('notas/:codacta/:cedula')
  async findOneNota(
    @Param('codacta') codacta: string,
    @Param('cedula', ParseIntPipe) cedula: number,
    @Request() req?: any,
  ) {
    return this.service.findOneNota(codacta, cedula, req.user);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR, Role.PROFESOR)
  @Post('notas')
  async createNota(@Body() dto: CreateNotaDto, @Request() req?: any) {
    return this.service.createNota(dto, req.user);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR, Role.PROFESOR)
  @Put('notas/:codacta/:cedula')
  async updateNota(
    @Param('codacta') codacta: string,
    @Param('cedula', ParseIntPipe) cedula: number,
    @Body() dto: UpdateNotaDto,
    @Request() req?: any,
  ) {
    return this.service.updateNota(codacta, cedula, dto, req.user);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Delete('notas/:codacta/:cedula')
  async deleteNota(
    @Param('codacta') codacta: string,
    @Param('cedula', ParseIntPipe) cedula: number,
  ) {
    return this.service.deleteNota(codacta, cedula);
  }
}
