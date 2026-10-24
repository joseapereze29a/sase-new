import { IsString, IsNotEmpty, IsInt, IsOptional, IsIn } from 'class-validator';

const VALID_CALIFICACIONES = [
  0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
  99, 100, 110, 120, 212, 404,
];

export class CreateNotaDto {
  @IsString()
  @IsNotEmpty()
  codacta: string;

  @IsInt()
  @IsNotEmpty()
  cedula: number;

  @IsInt()
  @IsIn(VALID_CALIFICACIONES, {
    message: 'La calificación debe estar entre 0 y 20 o ser un código especial válido (404, 99, 100, 110, 120, 212).',
  })
  @IsOptional()
  calificacion?: number;

  @IsString()
  @IsOptional()
  codeq?: string;
}
