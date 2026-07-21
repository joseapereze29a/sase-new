import { IsString, IsNotEmpty, IsInt } from 'class-validator';

export class CreateProfesorDto {
  @IsInt()
  @IsNotEmpty()
  cedula_profesor: number;

  @IsString()
  @IsNotEmpty()
  apellidos_nombres: string;

  @IsString()
  @IsNotEmpty()
  nombres: string;
}
