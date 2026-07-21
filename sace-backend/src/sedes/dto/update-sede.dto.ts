import { IsString, IsOptional, IsEnum } from 'class-validator';
import { directorio_cippsv_modalidad } from '@prisma/client';

export class UpdateSedeDto {
  @IsEnum(directorio_cippsv_modalidad)
  @IsOptional()
  modalidad?: directorio_cippsv_modalidad;

  @IsString()
  @IsOptional()
  director_coordinador?: string;

  @IsString()
  @IsOptional()
  direccion?: string;

  @IsString()
  @IsOptional()
  ciudad?: string;

  @IsString()
  @IsOptional()
  edo_prov?: string;

  @IsString()
  @IsOptional()
  fax?: string;

  @IsString()
  @IsOptional()
  email?: string;
}
