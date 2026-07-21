import { IsString, IsNotEmpty, IsInt, IsOptional, Min, Max } from 'class-validator';

export class CreateNotaDto {
  @IsString()
  @IsNotEmpty()
  codacta: string;

  @IsInt()
  @IsNotEmpty()
  cedula: number;

  @IsInt()
  @Min(0)
  @Max(20)
  @IsOptional()
  calificacion?: number;

  @IsString()
  @IsOptional()
  codeq?: string;
}
