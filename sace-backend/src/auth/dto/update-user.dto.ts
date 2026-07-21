import { IsString, MinLength, MaxLength, IsNumber, IsOptional } from 'class-validator';

export class UpdateUserDto {
  @IsString()
  @MinLength(3)
  @MaxLength(30)
  @IsOptional()
  username?: string;

  @IsString()
  @MinLength(6)
  @IsOptional()
  password?: string;

  @IsNumber()
  @IsOptional()
  role?: number;

  @IsNumber()
  @IsOptional()
  cedula?: number;

  @IsString()
  @IsOptional()
  usuario?: string;
}
