import { IsOptional, IsEnum, IsNumber, IsString } from 'class-validator';
import { OportunidadTipo, ActividadEspecial } from './create-oportunidad.dto';

export class UpdateOportunidadDto {
  @IsEnum(OportunidadTipo)
  @IsOptional()
  tipo?: OportunidadTipo;

  @IsString()
  @IsOptional()
  mencion_especialidad?: string;

  @IsNumber()
  @IsOptional()
  periodos?: number;

  @IsEnum(ActividadEspecial)
  @IsOptional()
  actividad_especial_final?: ActividadEspecial;

  @IsNumber()
  @IsOptional()
  creditos?: number;

  @IsString()
  @IsOptional()
  titulo_a_otorgar?: string;
}
