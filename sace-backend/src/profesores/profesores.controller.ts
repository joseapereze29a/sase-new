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
  ParseIntPipe,
  Request,
  ForbiddenException,
} from '@nestjs/common';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../auth/guards/roles.guard';
import { Roles } from '../auth/decorators/roles.decorator';
import { Role } from '../auth/enums/role.enum';
import { ProfesoresService } from './profesores.service';
import { CreateProfesorDto } from './dto/create-profesor.dto';
import { UpdateProfesorDto } from './dto/update-profesor.dto';

@Controller('profesores')
@UseGuards(JwtAuthGuard)
export class ProfesoresController {
  constructor(private readonly service: ProfesoresService) {}

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR, Role.COORDINADOR)
  @Get()
  async findAll(
    @Query('skip') skip?: number,
    @Query('take') take?: number,
    @Query('search') search?: string,
  ) {
    return this.service.findAll({ skip, take, search });
  }

  @Get(':cedula')
  async findOne(@Param('cedula', ParseIntPipe) cedula: number) {
    return this.service.findOne(cedula);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR)
  @Post()
  async create(@Body() dto: CreateProfesorDto) {
    return this.service.create(dto);
  }

  @UseGuards(RolesGuard)
  @Roles(Role.SUPER_USUARIO, Role.ADMINISTRADOR, Role.COORDINADOR, Role.PROFESOR)
  @Put(':cedula')
  async update(
    @Param('cedula', ParseIntPipe) cedula: number,
    @Body() dto: UpdateProfesorDto,
    @Request() req?: any,
  ) {
    if (req.user.role === Role.PROFESOR && Number(req.user.username) !== cedula) {
      throw new ForbiddenException('No tienes permisos para editar la ficha de otro profesor.');
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
