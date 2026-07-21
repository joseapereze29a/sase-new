import { IsString, IsNotEmpty, IsOptional, IsDateString } from 'class-validator';

export class CreateCohorteDto {
  @IsString()
  @IsNotEmpty()
  codsede: string;

  @IsString()
  @IsNotEmpty()
  codopest: string;

  @IsString()
  @IsNotEmpty()
  codcohorte: string;

  @IsDateString()
  @IsOptional()
  fecha_inicio?: string;

  @IsString()
  @IsNotEmpty()
  periodo_lectivo: string;
}
