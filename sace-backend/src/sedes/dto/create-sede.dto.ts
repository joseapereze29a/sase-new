import { IsString, IsNotEmpty, IsOptional, IsEnum } from 'class-validator';
import { directorio_cippsv_modalidad } from '@prisma/client';

export class CreateSedeDto {
  @IsString()
  @IsNotEmpty()
  codsede: string;

  @IsEnum(directorio_cippsv_modalidad)
  @IsOptional()
  modalidad?: directorio_cippsv_modalidad;

  @IsString()
  @IsNotEmpty()
  director_coordinador: string;

  @IsString()
  @IsNotEmpty()
  direccion: string;

  @IsString()
  @IsNotEmpty()
  ciudad: string;

  @IsString()
  @IsNotEmpty()
  edo_prov: string;

  @IsString()
  @IsOptional()
  fax?: string;

  @IsString()
  @IsOptional()
  email?: string;
}
