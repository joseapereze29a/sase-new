import { IsString, MinLength, MaxLength, IsNumber, IsOptional } from 'class-validator';

export class RegisterDto {
  @IsString()
  @MinLength(3)
  @MaxLength(30)
  username: string;

  @IsString()
  @MinLength(6)
  password: string;

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
