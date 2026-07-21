import { IsString, IsOptional, IsDateString } from 'class-validator';

export class UpdateCohorteDto {
  @IsDateString()
  @IsOptional()
  fecha_inicio?: string;

  @IsString()
  @IsOptional()
  periodo_lectivo?: string;
}
