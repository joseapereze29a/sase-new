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
} from '@nestjs/common';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { Role } from '../auth/enums/role.enum';
import { CohortesService } from './cohortes.service';
import { CreateCohorteDto } from './dto/create-cohorte.dto';
import { UpdateCohorteDto } from './dto/update-cohorte.dto';

@Controller('cohortes')
@UseGuards(JwtAuthGuard)
export class CohortesController {
  constructor(private readonly service: CohortesService) {}

  @Get()
  async findAll(
    @Query('skip') skip?: number,
    @Query('take') take?: number,
    @Query('search') search?: string,
    @Query('codsede') codsede?: string,
    @Query('codopest') codopest?: string,
    @Request() req?: any,
  ) {
    return this.service.findAll({ skip, take, search, codsede, codopest }, req?.user);
  }

  @Get('ciudades')
  async getCiudades(@Request() req?: any) {
    return this.service.findCiudades(req?.user);
  }

  @Get('programas-por-ciudad/:ciudad')
  async getProgramasPorCiudad(@Param('ciudad') ciudad: string, @Request() req?: any) {
    return this.service.findProgramasPorCiudad(ciudad, req?.user);
  }

  @Get(':codsede/:codopest/:codcohorte')
  async findOne(
    @Param('codsede') codsede: string,
    @Param('codopest') codopest: string,
    @Param('codcohorte') codcohorte: string,
  ) {
    return this.service.findOne(codsede, codopest, codcohorte);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Post()
  async create(@Body() dto: CreateCohorteDto) {
    return this.service.create(dto);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Put(':codsede/:codopest/:codcohorte')
  async update(
    @Param('codsede') codsede: string,
    @Param('codopest') codopest: string,
    @Param('codcohorte') codcohorte: string,
    @Body() dto: UpdateCohorteDto,
  ) {
    return this.service.update(codsede, codopest, codcohorte, dto);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Delete(':codsede/:codopest/:codcohorte')
  async delete(
    @Param('codsede') codsede: string,
    @Param('codopest') codopest: string,
    @Param('codcohorte') codcohorte: string,
  ) {
    return this.service.delete(codsede, codopest, codcohorte);
  }
}
