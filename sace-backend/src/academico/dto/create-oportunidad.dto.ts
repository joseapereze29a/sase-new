import { IsString, IsOptional, IsEnum, IsNumber } from 'class-validator';

export enum OportunidadTipo {
  Maestria = 'Maestria',
  Especializacion = 'Especializacion',
  Doctorado = 'Doctorado',
  Diplomado = 'Diplomado',
}

export enum ActividadEspecial {
  Trabajo_de_Grado = 'Trabajo_de_Grado',
  Trabajo_de_Investigacion = 'Trabajo_de_Investigacion',
}

export class CreateOportunidadDto {
  @IsString()
  codsede: string;

  @IsString()
  codopest: string;

  @IsEnum(OportunidadTipo)
  @IsOptional()
  tipo?: OportunidadTipo;

  @IsString()
  mencion_especialidad: string;

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
  titulo_a_otorgar: string;
}
