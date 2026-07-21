import { IsString, IsOptional } from 'class-validator';

export class UpdateProfesorDto {
  @IsString()
  @IsOptional()
  apellidos_nombres?: string;

  @IsString()
  @IsOptional()
  nombres?: string;
}
