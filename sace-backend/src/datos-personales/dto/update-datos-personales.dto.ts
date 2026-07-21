import { IsString, IsOptional, IsEmail, IsEnum, IsDateString, IsNumber } from 'class-validator';
import { Nacionalidad, Sexo, EstadoCivil, SiNo } from './create-datos-personales.dto';

export class UpdateDatosPersonalesDto {
  @IsString()
  @IsOptional()
  apellidos?: string;

  @IsString()
  @IsOptional()
  nombres?: string;

  @IsDateString()
  @IsOptional()
  fecha_nacimiento?: string;

  @IsString()
  @IsOptional()
  lugar_nacimiento?: string;

  @IsEnum(Nacionalidad)
  @IsOptional()
  nacionalidad?: Nacionalidad;

  @IsEnum(Sexo)
  @IsOptional()
  sexo?: Sexo;

  @IsString()
  @IsOptional()
  direccion?: string;

  @IsString()
  @IsOptional()
  telefono_habitacion?: string;

  @IsString()
  @IsOptional()
  fax?: string;

  @IsEmail()
  @IsOptional()
  email?: string;

  @IsString()
  @IsOptional()
  profesion_oficio?: string;

  @IsString()
  @IsOptional()
  institucion?: string;

  @IsString()
  @IsOptional()
  empleado_en?: string;

  @IsString()
  @IsOptional()
  cargo_desempena?: string;

  @IsString()
  @IsOptional()
  direccion_telefono?: string;

  @IsString()
  @IsOptional()
  sueldo_salario?: string;

  @IsEnum(EstadoCivil)
  @IsOptional()
  estado_civil?: EstadoCivil;

  @IsString()
  @IsOptional()
  cid_conyuge?: string;

  @IsString()
  @IsOptional()
  apellidos_nombres_conyuge?: string;

  @IsEnum(Nacionalidad)
  @IsOptional()
  nacionalidad_conyuge?: Nacionalidad;

  @IsString()
  @IsOptional()
  grado_instruccion?: string;

  @IsString()
  @IsOptional()
  profesion_ocupacion?: string;

  @IsNumber()
  @IsOptional()
  nro_grupo_familiar?: number;

  @IsString()
  @IsOptional()
  ingreso_familiar?: string;

  @IsString()
  @IsOptional()
  tipo_vivienda?: string;

  @IsEnum(SiNo)
  @IsOptional()
  vehiculo?: SiNo;

  @IsString()
  @IsOptional()
  marca_vehiculo?: string;

  @IsNumber()
  @IsOptional()
  ano?: number;

  @IsString()
  @IsOptional()
  licencia_nro?: string;

  @IsString()
  @IsOptional()
  telefono_trabajo?: string;

  @IsDateString()
  @IsOptional()
  fecha_nacimiento_conyuge?: string;

  @IsString()
  @IsOptional()
  telefono_celular?: string;

  @IsString()
  @IsOptional()
  modelo_vehiculo?: string;

  @IsEnum(SiNo)
  @IsOptional()
  original?: SiNo;
}
