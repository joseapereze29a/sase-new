import { IsString, IsNotEmpty, IsOptional, IsInt, IsDateString, IsArray } from 'class-validator';

export class CreateActaDto {
  @IsString()
  @IsNotEmpty()
  codcohorte: string;

  @IsString()
  @IsNotEmpty()
  codasig: string;

  @IsString()
  @IsNotEmpty()
  codacta: string;

  @IsInt()
  @IsOptional()
  cedula_profesor?: number;

  @IsArray()
  @IsInt({ each: true })
  @IsOptional()
  cedulas_profesores?: number[];

  @IsDateString()
  @IsOptional()
  fecha_aprobacion?: string;
}
