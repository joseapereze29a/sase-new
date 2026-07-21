import { IsInt, IsOptional, IsString, Min, Max } from 'class-validator';

export class UpdateNotaDto {
  @IsInt()
  @Min(0)
  @Max(20)
  @IsOptional()
  calificacion?: number;

  @IsString()
  @IsOptional()
  codeq?: string;
}
