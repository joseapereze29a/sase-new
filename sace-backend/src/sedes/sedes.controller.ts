import { Controller, Get, Post, Put, Delete, Body, Param, Query, UseGuards } from '@nestjs/common';
import { SedesService } from './sedes.service';
import { CreateSedeDto } from './dto/create-sede.dto';
import { UpdateSedeDto } from './dto/update-sede.dto';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { Role } from '../auth/enums/role.enum';

@Controller('sedes')
export class SedesController {
  constructor(private readonly sedesService: SedesService) {}

  @UseGuards(JwtAuthGuard)
  @Get()
  async findAll(@Query('search') search?: string) {
    return this.sedesService.findAll(search);
  }

  @UseGuards(JwtAuthGuard)
  @Get(':codsede')
  async findOne(@Param('codsede') codsede: string) {
    return this.sedesService.findOne(codsede);
  }

  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Post()
  async create(@Body() dto: CreateSedeDto) {
    return this.sedesService.create(dto);
  }

  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Put(':codsede')
  async update(@Param('codsede') codsede: string, @Body() dto: UpdateSedeDto) {
    return this.sedesService.update(codsede, dto);
  }

  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Delete(':codsede')
  async delete(@Param('codsede') codsede: string) {
    return this.sedesService.delete(codsede);
  }
}
