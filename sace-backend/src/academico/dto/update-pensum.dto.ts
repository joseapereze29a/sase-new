import { IsOptional, IsEnum, IsNumber, IsString } from 'class-validator';
import { Status, SiNo } from './create-pensum.dto';

export class UpdatePensumDto {
  @IsString()
  @IsOptional()
  asignatura?: string;

  @IsNumber()
  @IsOptional()
  creditos?: number;

  @IsNumber()
  @IsOptional()
  periodos?: number;

  @IsEnum(Status)
  @IsOptional()
  status?: Status;

  @IsString()
  @IsOptional()
  codasig_imp?: string;

  @IsEnum(SiNo)
  @IsOptional()
  multiacta?: SiNo;

  @IsOptional()
  prelaciones?: string[];
}
