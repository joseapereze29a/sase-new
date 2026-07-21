import { IsString, IsOptional, IsEnum, IsNumber } from 'class-validator';

export enum Status {
  Activa = 'Activa',
  Inactiva = 'Inactiva',
}

export enum SiNo {
  si = 'si',
  no = 'no',
}

export class CreatePensumDto {
  @IsString()
  codsede: string;

  @IsString()
  codopest: string;

  @IsString()
  codasig: string;

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
  codasig_imp: string;

  @IsEnum(SiNo)
  @IsOptional()
  multiacta?: SiNo;

  @IsOptional()
  prelaciones?: string[];
}
