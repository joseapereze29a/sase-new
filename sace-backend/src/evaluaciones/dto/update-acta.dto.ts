import { IsInt, IsOptional, IsDateString } from 'class-validator';

export class UpdateActaDto {
  @IsInt()
  @IsOptional()
  cedula_profesor?: number;

  @IsDateString()
  @IsOptional()
  fecha_aprobacion?: string;
}
