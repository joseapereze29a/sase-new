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
  ForbiddenException,
  ParseIntPipe,
  Res,
} from '@nestjs/common';
import * as express from 'express';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { Role } from '../auth/enums/role.enum';
import { DatosPersonalesService } from './datos-personales.service';
import { CreateDatosPersonalesDto } from './dto/create-datos-personales.dto';
import { UpdateDatosPersonalesDto } from './dto/update-datos-personales.dto';

@Controller('datos-personales')
@UseGuards(JwtAuthGuard)
export class DatosPersonalesController {
  constructor(private readonly service: DatosPersonalesService) {}

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR, Role.COORDINADOR, Role.PROFESOR)
  @Get()
  async findAll(
    @Query('skip') skip?: number,
    @Query('take') take?: number,
    @Query('search') search?: string,
    @Request() req?: any,
  ) {
    return this.service.findAll({ skip, take, search }, req?.user);
  }

  @Get(':cedula')
  async findOne(@Param('cedula', ParseIntPipe) cedula: number, @Request() req: any) {
    const { user } = req;
    const isOwner = user.username === String(cedula);
    const hasPrivileges = [Role.SUPER_USUARIO, Role.ADMINISTRADOR, Role.COORDINADOR].includes(user.role);

    let hasAccess = isOwner || hasPrivileges;
    if (!hasAccess && user.role === Role.PROFESOR) {
      hasAccess = await this.service.isStudentInTeacherPrograms(cedula, Number(user.username));
    }

    if (!hasAccess) {
      throw new ForbiddenException('No tienes permiso para consultar este expediente.');
    }

    return this.service.findOne(cedula);
  }

  @Get(':cedula/record-notas/pdf')
  async generateRecordPdf(
    @Param('cedula', ParseIntPipe) cedula: number,
    @Query('codcohorte') codcohorte: string,
    @Request() req: any,
    @Res() res: express.Response,
  ) {
    const { user } = req;
    const isOwner = user.username === String(cedula);
    const hasPrivileges = [Role.SUPER_USUARIO, Role.ADMINISTRADOR, Role.COORDINADOR].includes(user.role);

    let hasAccess = isOwner || hasPrivileges;
    if (!hasAccess && user.role === Role.PROFESOR) {
      hasAccess = await this.service.isStudentInTeacherPrograms(cedula, Number(user.username));
    }

    if (!hasAccess) {
      throw new ForbiddenException('No tienes permiso para consultar este expediente.');
    }

    const buffer = await this.service.generateRecordNotasPdf(cedula, codcohorte);
    
    res.set({
      'Content-Type': 'application/pdf',
      'Content-Disposition': `attachment; filename=record_${cedula}_${codcohorte}.pdf`,
      'Content-Length': buffer.length,
    });
    
    res.end(buffer);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Post()
  async create(@Body() dto: CreateDatosPersonalesDto) {
    return this.service.create(dto);
  }

  @Put(':cedula')
  async update(
    @Param('cedula', ParseIntPipe) cedula: number,
    @Body() dto: UpdateDatosPersonalesDto,
    @Request() req: any,
  ) {
    const { user } = req;
    const isOwner = user.username === String(cedula);
    const hasPrivileges = [Role.SUPER_USUARIO, Role.ADMINISTRADOR, Role.COORDINADOR].includes(user.role);

    if (!isOwner && !hasPrivileges) {
      throw new ForbiddenException('No tienes permiso para actualizar este expediente.');
    }

    return this.service.update(cedula, dto);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Delete(':cedula')
  async delete(@Param('cedula', ParseIntPipe) cedula: number) {
    return this.service.delete(cedula);
  }
}
