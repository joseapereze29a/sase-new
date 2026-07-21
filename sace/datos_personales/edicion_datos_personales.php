<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/arreglo_meses_dias.php");


if (! ereg ("^[0-9]+$", $cedula) )
{
	header ("Location: ingreso_de_cedula.php");
	exit;
}

if ($cancelar)
{
	header ("Location: ingreso_de_cedula.php");
	exit;
}



if ( ($cedula) AND ($actualizar) )
{


	if (! $nombres) $error_str .= '&bull; El estudiante debe contener un Nombre v&aacute;lido, favor revisar.<BR>';

	if (! $apellidos) $error_str .= '&bull; El estudiante debe contener un Apellido v&aacute;lido, favor revisar.<BR>';


	if ( ($dia_fecha) OR ($mes_fecha) OR ($ano_fecha) )
	{
		if ($dia_fecha)
		{
			if ( ($dia_fecha < 1) OR ($dia_fecha > 31) ) $error_dia_fecha = 1;
		}

		if ($mes_fecha)
		{
			if ( ($mes_fecha < 1) OR ($mes_fecha > 12) ) $error_mes_fecha = 1;
		}

		if ($ano_fecha)
		{
			if ( ($ano_fecha < 1900) OR ($ano_fecha > 2050) ) $error_ano_fecha = 1;
		}

	}

	if ( ($dia_fecha) AND ( (! $mes_fecha) OR (! $ano_fecha) ) ) $error_dia_fecha = 1;
	if ( ($mes_fecha) AND ( (! $dia_fecha) OR (! $ano_fecha) ) ) $error_mes_fecha = 1;
	if ( ($ano_fecha) AND ( (! $dia_fecha) OR (! $mes_fecha) ) ) $error_ano_fecha = 1;

	if ( ($error_dia_fecha) OR ($error_mes_fecha) OR ($error_ano_fecha) ) 
	{
		$error_str .= '&bull; El estudiante debe tener una Fecha de Nacimiento v&aacute;lida, favor revisar.<BR>';
	}


	if ($email)
	{
		if ( (! ereg ("^[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+)*@[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+)*(\.[a-zA-Z]+)$", $email) ) )
		{
			$error_str .= '&bull; La direcci&oacute;n de correo electr&oacute;nico (E-Mail) no es v&aacute;lida, favor revisar.<BR>';
		}
	}


	if ($cid_conyuge)
	{
		if (! ereg ("^[0-9]+$", $cid_conyuge) )
		{
			$error_str .= '&bull; El N&uacute;mero de C&eacute;dula de Identidad del Conyuge no es v&aacute;lido, favor revisar.<BR>';
		}
	}


	if ( ($dia_conyuge) OR ($mes_conyuge) OR ($ano_conyuge) )
	{
		if ($dia_conyuge)
		{
			if ( ($dia_conyuge < 1) OR ($dia_conyuge > 31) ) $error_dia_conyuge = 1;
		}

		if ($mes_conyuge)
		{
			if ( ($mes_conyuge < 1) OR ($mes_conyuge > 12) ) $error_mes_conyuge = 1;
		}

		if ($ano_conyuge)
		{
			if ( ($ano_conyuge < 1900) OR ($ano_conyuge > 2050) ) $error_ano_conyuge = 1;
		}

	}

	if ( ($dia_conyuge) AND ( (! $mes_conyuge) OR (! $ano_conyuge) ) ) $error_dia_conyuge = 1;
	if ( ($mes_conyuge) AND ( (! $dia_conyuge) OR (! $ano_conyuge) ) ) $error_mes_conyuge = 1;
	if ( ($ano_conyuge) AND ( (! $dia_conyuge) OR (! $mes_conyuge) ) ) $error_ano_conyuge = 1;

	if ( ($error_dia_conyuge) OR ($error_mes_conyuge) OR ($error_ano_conyuge) ) 
	{
		$error_str .= '&bull; El Conyuge debe tener una Fecha de Nacimiento v&aacute;lida, favor revisar.<BR>';
	}


	if ($ano)
	{
		if ( ($ano < 1900) OR ($ano > 2050) ) $error_str .= '&bull; El A&ntilde;o del Vehiculo Automotor es inv&aacute;lido, favor revisar.<BR>';
	}




	if (! $error_str)
	{

			if ( ($dia_fecha) AND ($mes_fecha) )
			{
				$fecha_nacimiento = $ano_fecha . '-' . $mes_fecha . '-' . $dia_fecha;
	
			} else {
	
				$fecha_nacimiento = "0000-00-00";
			}
	
	
			if ( ($dia_conyuge) AND ($mes_conyuge) )
			{
				$fecha_nacimiento_conyuge = $ano_conyuge . '-' . $mes_conyuge . '-' . $dia_conyuge;
	
			} else {
	
				$fecha_nacimiento_conyuge = "0000-00-00";
			}

##	update datos_personales set telefono_trabajo='(0212) 552-8922 ext. 233', email='jbianco@yisu.net' where cedula=12421100;


$sqlcmd = "UPDATE datos_personales SET apellidos='$apellidos', nombres='$nombres', fecha_nacimiento='$fecha_nacimiento', "
		. "lugar_nacimiento='$lugar_nacimiento', nacionalidad='$nacionalidad', sexo='$sexo', direccion='$direccion', "
		. "telefono_habitacion='$telefono_habitacion', fax='$fax', email='$email', profesion_oficio='$profesion_oficio', "
		. "institucion='$institucion', empleado_en='$empleado_en', cargo_desempena='$cargo_desempena', "
		. "direccion_telefono='$direccion_telefono', sueldo_salario='$sueldo_salario', estado_civil='$estado_civil', "
		. "cid_conyuge='$cid_conyuge', apellidos_nombres_conyuge='$apellidos_nombres_conyuge', "
		. "nacionalidad_conyuge='$nacionalidad_conyuge', grado_instruccion='$grado_instruccion', "
		. "profesion_ocupacion='$profesion_ocupacion', nro_grupo_familiar='$nro_grupo_familiar', "
		. "ingreso_familiar='$ingreso_familiar', tipo_vivienda='$tipo_vivienda', vehiculo='$vehiculo', "
		. "marca_vehiculo='$marca_vehiculo', ano='$ano', licencia_nro='$licencia_nro', telefono_trabajo='$telefono_trabajo', "
		. "fecha_nacimiento_conyuge='$fecha_nacimiento_conyuge', telefono_celular='$telefono_celular', "
		. "modelo_vehiculo='$modelo_vehiculo', original='si', fecha_modificacion=NOW(), "
		. "operador_modificacion='$PHP_AUTH_USER', host_modificacion='$REMOTE_ADDR' "
		. "WHERE cedula='$cedula' ";
	
#echo "$sqlcmd <BR><BR>";

		$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");


		if (! $nro_grupo_familiar) 
		{
			$sqlcmd_grupo_familiar = "UPDATE datos_personales SET nro_grupo_familiar=NULL where cedula='$cedula' ";
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd_grupo_familiar");
		}

		if (! $ano)
		{
			$sqlcmd_ano = "UPDATE datos_personales SET ano=NULL where cedula='$cedula' ";
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd_ano");
		}



		header ("Location: edicion_datos_personales_finalizado.php");
		exit;



	}


}


















/*
if ( ($ingresar) OR ($ingresar_x) )
{

	if (! $nombres) $error_str .= '&bull; El estudiante debe contener un Nombre v&aacute;lido, favor revisar.<BR>';

	if (! $apellidos) $error_str .= '&bull; El estudiante debe contener un Apellido v&aacute;lido, favor revisar.<BR>';


	if ( ($dia_fecha) OR ($mes_fecha) OR ($ano_fecha) )
	{
		if ($dia_fecha)
		{
			if ( ($dia_fecha < 1) OR ($dia_fecha > 31) ) $error_dia_fecha = 1;
		}

		if ($mes_fecha)
		{
			if ( ($mes_fecha < 1) OR ($mes_fecha > 12) ) $error_mes_fecha = 1;
		}

		if ($ano_fecha)
		{
			if ( ($ano_fecha < 1900) OR ($ano_fecha > 2050) ) $error_ano_fecha = 1;
		}

	}

	if ( ($dia_fecha) AND ( (! $mes_fecha) OR (! $ano_fecha) ) ) $error_dia_fecha = 1;
	if ( ($mes_fecha) AND ( (! $dia_fecha) OR (! $ano_fecha) ) ) $error_mes_fecha = 1;
	if ( ($ano_fecha) AND ( (! $dia_fecha) OR (! $mes_fecha) ) ) $error_ano_fecha = 1;

	if ( ($error_dia_fecha) OR ($error_mes_fecha) OR ($error_ano_fecha) ) 
	{
		$error_str .= '&bull; El estudiante debe tener una Fecha de Nacimiento v&aacute;lida, favor revisar.<BR>';
	}


	if ($email)
	{
		if ( (! ereg ("^[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+)*@[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+)*(\.[a-zA-Z]+)$", $email) ) )
		{
			$error_str .= '&bull; La direcci&oacute;n de correo electr&oacute;nico (E-Mail) no es v&aacute;lida, favor revisar.<BR>';
		}
	}


	if ($cid_conyuge)
	{
		if (! ereg ("^[0-9]+$", $cid_conyuge) )
		{
			$error_str .= '&bull; El N&uacute;mero de C&eacute;dula de Identidad del Conyuge no es v&aacute;lido, favor revisar.<BR>';
		}
	}


	if ( ($dia_conyuge) OR ($mes_conyuge) OR ($ano_conyuge) )
	{
		if ($dia_conyuge)
		{
			if ( ($dia_conyuge < 1) OR ($dia_conyuge > 31) ) $error_dia_conyuge = 1;
		}

		if ($mes_conyuge)
		{
			if ( ($mes_conyuge < 1) OR ($mes_conyuge > 12) ) $error_mes_conyuge = 1;
		}

		if ($ano_conyuge)
		{
			if ( ($ano_conyuge < 1900) OR ($ano_conyuge > 2050) ) $error_ano_conyuge = 1;
		}

	}

	if ( ($dia_conyuge) AND ( (! $mes_conyuge) OR (! $ano_conyuge) ) ) $error_dia_conyuge = 1;
	if ( ($mes_conyuge) AND ( (! $dia_conyuge) OR (! $ano_conyuge) ) ) $error_mes_conyuge = 1;
	if ( ($ano_conyuge) AND ( (! $dia_conyuge) OR (! $mes_conyuge) ) ) $error_ano_conyuge = 1;

	if ( ($error_dia_conyuge) OR ($error_mes_conyuge) OR ($error_ano_conyuge) ) 
	{
		$error_str .= '&bull; El Conyuge debe tener una Fecha de Nacimiento v&aacute;lida, favor revisar.<BR>';
	}


	if ($ano)
	{
		if ( ($ano < 1900) OR ($ano > 2050) ) $error_str .= '&bull; El A&ntilde;o del Vehiculo Automotor es inv&aacute;lido, favor revisar.<BR>';
	}



	if (! $error_str)
	{

			if ( ($dia_fecha) AND ($mes_fecha) )
			{
				$fecha_nacimiento = $ano_fecha . '-' . $mes_fecha . '-' . $dia_fecha;
	
			} else {
	
				$fecha_nacimiento = "0000-00-00";
			}
	
	
			if ( ($dia_conyuge) AND ($mes_conyuge) )
			{
				$fecha_nacimiento_conyuge = $ano_conyuge . '-' . $mes_conyuge . '-' . $dia_conyuge;
	
			} else {
	
				$fecha_nacimiento_conyuge = "0000-00-00";
			}

			


		$sqlcmd = "INSERT INTO datos_personales (cedula, apellidos, nombres, fecha_nacimiento, lugar_nacimiento, nacionalidad, "
				. "sexo, direccion, telefono_habitacion, fax, email, profesion_oficio, institucion, empleado_en, cargo_desempena, "
				. "direccion_telefono, sueldo_salario, estado_civil, cid_conyuge, apellidos_nombres_conyuge, nacionalidad_conyuge, "
				. "grado_instruccion, profesion_ocupacion, nro_grupo_familiar, ingreso_familiar, tipo_vivienda, vehiculo, "
				. "marca_vehiculo, ano, licencia_nro, telefono_trabajo, fecha_nacimiento_conyuge, telefono_celular, "
				. "modelo_vehiculo, original, fecha_creacion, operador_creacion, host_creacion) VALUES ("
				. "'$cedula', '$apellidos', '$nombres', '$fecha_nacimiento', '$lugar_nacimiento', '$nacionalidad', "
				. "'$sexo', '$direccion', '$telefono_habitacion', '$fax', '$email', '$profesion_oficio', '$institucion', '$empleado_en', '$cargo_desempena', "
				. "'$direccion_telefono', '$sueldo_salario', '$estado_civil', '$cid_conyuge', '$apellidos_nombres_conyuge', '$nacionalidad_conyuge', "
				. "'$grado_instruccion', '$profesion_ocupacion', '$nro_grupo_familiar', '$ingreso_familiar', '$tipo_vivienda', '$vehiculo', "
				. "'$marca_vehiculo', '$ano', '$licencia_nro', '$telefono_trabajo', '$fecha_nacimiento_conyuge', '$telefono_celular', "
				. "'$modelo_vehiculo', 'si', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR') ";

		$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");


		if (! $nro_grupo_familiar) 
		{
			$sqlcmd_grupo_familiar = "UPDATE datos_personales SET nro_grupo_familiar=NULL where cedula='$cedula' ";
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd_grupo_familiar");
		}

		if (! $ano)
		{
			$sqlcmd_ano = "UPDATE datos_personales SET ano=NULL where cedula='$cedula' ";
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd_ano");
		}



		header ("Location: ingreso_datos_personales_finalizado.php");
		exit;
			


	}


}
*/

if ( ($cedula) AND (! $actualizar) )
{
	

$sqlcmd = "SELECT apellidos, nombres, fecha_nacimiento, lugar_nacimiento, nacionalidad, sexo, direccion, "
		. "telefono_habitacion, fax, email, profesion_oficio, institucion, empleado_en, cargo_desempena, "
		. "direccion_telefono, sueldo_salario, estado_civil, cid_conyuge, apellidos_nombres_conyuge, "
		. "nacionalidad_conyuge, grado_instruccion, profesion_ocupacion, nro_grupo_familiar, ingreso_familiar, "
		. "tipo_vivienda, vehiculo, marca_vehiculo, ano, licencia_nro, telefono_trabajo, fecha_nacimiento_conyuge, "
		. "telefono_celular, modelo_vehiculo "
		. "FROM datos_personales "
		. "WHERE cedula='$cedula' ";


$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$apellidos = $registro->apellidos;
	$nombres = $registro->nombres;
	$fecha_nacimiento = $registro->fecha_nacimiento;
	$lugar_nacimiento = $registro->lugar_nacimiento;
	$nacionalidad = $registro->nacionalidad;
	$sexo = $registro->sexo;
	$direccion = $registro->direccion;
	$telefono_habitacion = $registro->telefono_habitacion;
	$fax = $registro->fax;
	$email = $registro->email;
	$profesion_oficio = $registro->profesion_oficio;
	$institucion = $registro->institucion;
	$empleado_en = $registro->empleado_en;
	$cargo_desempena = $registro->cargo_desempena;
	$direccion_telefono = $registro->direccion_telefono;
	$sueldo_salario = $registro->sueldo_salario;
	$estado_civil = $registro->estado_civil;
	$cid_conyuge = $registro->cid_conyuge;
	$apellidos_nombres_conyuge = $registro->apellidos_nombres_conyuge;
	$nacionalidad_conyuge = $registro->nacionalidad_conyuge;
	$grado_instruccion = $registro->grado_instruccion;
	$profesion_ocupacion = $registro->profesion_ocupacion;
	$nro_grupo_familiar = $registro->nro_grupo_familiar;
	$ingreso_familiar = $registro->ingreso_familiar;
	$tipo_vivienda = $registro->tipo_vivienda;
	$vehiculo = $registro->vehiculo;
	$marca_vehiculo = $registro->marca_vehiculo;
	$ano = $registro->ano;
	$licencia_nro = $registro->licencia_nro;
	$telefono_trabajo = $registro->telefono_trabajo;
	$fecha_nacimiento_conyuge = $registro->fecha_nacimiento_conyuge;
	$telefono_celular = $registro->telefono_celular;
	$modelo_vehiculo = $registro->modelo_vehiculo;
}


# $apellidos = ucwords($apellidos);
# $nombres = ucwords($nombres);


if ( ($fecha_nacimiento == '0000-00-00') OR ($fecha_nacimiento == "") )
{
	$ano_fecha = '';
	$mes_fecha = '';
	$dia_fecha = '';

} else {

	list($ano_fecha, $mes_fecha, $dia_fecha) = split ('-', $fecha_nacimiento);
	
	$dia_fecha = ABS($dia_fecha);
	$mes_fecha = ABS($mes_fecha);
}


#if ($lugar_nacimiento == "") $lugar_nacimiento = '';
#if ($nacionalidad == "") $nacionalidad = '';
#if ($sexo == "") $sexo = '';


#if ($telefono_celular == "") $telefono_celular = '';
#if ($telefono_trabajo == "") $telefono_trabajo = '';
#if ($telefono_habitacion == "") $telefono_habitacion = '';
#if ($fax == "") $fax = '';
#if ($direccion == "") $direccion = '';
#if ($email == "") $email = '';


#if ($profesion_oficio == "") $profesion_oficio = '';
#if ($institucion == "") $institucion = '';
#if ($empleado_en == "") $empleado_en = '';
#if ($cargo_desempena == "") $cargo_desempena = '';
#if ($direccion_telefono == "") $direccion_telefono = '';
#if ($sueldo_salario == "") $sueldo_salario = '';


#$apellidos_nombres_conyuge = ucwords($apellidos_nombres_conyuge);

#if ($estado_civil == "") $estado_civil = '';
#if ($cid_conyuge == "") $cid_conyuge = '';



if ( ($fecha_nacimiento_conyuge == '0000-00-00') OR ($fecha_nacimiento_conyuge == "") )
{
	$ano_conyuge = '';
	$mes_conyuge = '';
	$dia_conyuge = '';

} else {

	list($ano_conyuge, $mes_conyuge, $dia_conyuge) = split ('-', $fecha_nacimiento_conyuge);
	
	$dia_conyuge = ABS($dia_conyuge);
	$mes_conyuge = ABS($mes_conyuge);
}


#if ($nacionalidad_conyuge == "") $nacionalidad_conyuge = '';
#if ($tipo_vivienda == "") $tipo_vivienda = '';
#if ($ingreso_familiar == "") $ingreso_familiar = '';
#if ($nro_grupo_familiar == "") $nro_grupo_familiar = '';
#if ($grado_instruccion == "") $grado_instruccion = '';
#if ($profesion_ocupacion == "") $profesion_ocupacion = '';

#if ($vehiculo == "") $vehiculo = '';
#if ($licencia_nro == "") $licencia_nro = '';
#if ($marca_vehiculo == "") $marca_vehiculo = '';
#if ($modelo_vehiculo == "") $modelo_vehiculo = '';
#if ($ano == "") $ano = '';

}
?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#00CC00" VLINK="#CC0000">

<CENTER>

<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
<TR>
	<TD WIDTH="100%" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
	
		<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
		<TR>
			<TD WIDTH="130" ALIGN="center" VALIGN="middle">
				<A HREF="/sace/"><IMG SRC="/sace/imagenes/logo3.jpg" ALT="" WIDTH="111" HEIGHT="110" BORDER="0"></A>
			</TD><TD WIDTH="470" ALIGN="center" VALIGN="middle" BGCOLOR="#000099">
				<IMG SRC="/sace/imagenes/titulo_sace.jpg" ALT="" WIDTH="400" HEIGHT="35"><BR><BR>
			</TD>
		</TR>
		</TABLE>

	</TD>
</TR>
</TABLE>


<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
	<TD WIDTH="100%" ALIGN="left" VALIGN="top">
	
		<A HREF="../"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="ingreso_de_cedula.php"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Datos Personales de un Estudiante</B></FONT></A>
		
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT>
		
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Edici&oacute;n de Datos Personales</B></FONT>

	</TD>
</TR>
</TABLE>

<?
	#include ("$DOCUMENT_ROOT/includes/encabezado.php");
?>

<BR>

<IMG SRC="/sace/imagenes/menu_editar_datos_personales_estudiante.jpg" ALT="" WIDTH="309" HEIGHT="17" BORDER="0">

<BR><BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000000">
N&uacute;mero de C&eacute;dula de Identidad: <B><? echo strtr (number_format($cedula), ",", ".") ?></B>
</FONT>

<BR>

<FORM ACTION="edicion_datos_personales.php" METHOD="POST">

<?

if ($error_str)
{
	echo '<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">';
	echo '<TR><TD WIDTH="600" ALIGN="left" VALIGN="top">';
	
	echo '<FONT FACE="Verdana,Arial,Geneva" COLOR="#FF0000">';
	echo '<B>Se ha encontrado algun Error al tratar de procesar la Informaci&oacute;n</B>';
	echo '</FONT><BR><BR>';
}

if ($error_str)
{
	echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
	echo $error_str;
	echo '</FONT><BR>';
	echo '</TD></TR></TABLE>';
}

?>
<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Datos Personales</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Nombres</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="nombres" VALUE="<? echo $nombres ?>" SIZE="32" MAXLENGTH="30">
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Apellidos</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="apellidos" VALUE="<? echo $apellidos ?>" SIZE="32" MAXLENGTH="30">
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Nacimiento</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">


			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
				<SELECT NAME="dia_fecha">
					<OPTION VALUE="" SELECTED>D&iacute;a
					<?
						#if (! $_dia) $_dia = date(j); 
			
						for($i=1; $i<32; $i++)
						{
							if ($dia_fecha == $i)
							{
								echo '<OPTION VALUE="' . $i . '" SELECTED>' . $i . "\n";
							} else {
								echo '<OPTION VALUE="' . $i . '">' . $i . "\n";
							}
						}
					?>
				</SELECT>
				<SELECT NAME="mes_fecha">
					<OPTION VALUE="" SELECTED>Mes
					<?
						#if (! $_mes) $_mes = date(n);
			
						for($i=1; $i<13; $i++)
						{
							if ($mes_fecha == $i)
							{
								echo '<OPTION VALUE="' . $i . '" SELECTED>' . $meses[$i] . "\n";
							} else {
								echo '<OPTION VALUE="' . $i . '">' . $meses[$i] . "\n";
							}
						}
					?>
				</SELECT>


				<INPUT TYPE="text" NAME="ano_fecha" VALUE="<? echo $ano_fecha ?>" SIZE="6" MAXLENGTH="4">

				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					&nbsp; Ejemplo de A&ntilde;o: <B>1973</B>
				</FONT>

	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Lugar de Nacimiento</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="lugar_nacimiento" VALUE="<? echo $lugar_nacimiento ?>" SIZE="45" MAXLENGTH="50">
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Nacionalidad</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<SELECT NAME="nacionalidad">
			<?

				if (! $nacionalidad)
				{
					echo '<OPTION VALUE="" SELECTED>' . ' - - ' . "\n";
					echo '<OPTION VALUE="Venezolana">' . 'Venezolano(a)' . "\n";
					echo '<OPTION VALUE="Extranjera">' . 'Extranjero(a)' . "\n";
				}


				if ($nacionalidad == 'Venezolana')
				{
					echo '<OPTION VALUE="Venezolana" SELECTED>' . 'Venezolano(a)' . "\n";
					echo '<OPTION VALUE="Extranjera">' . 'Extranjero(a)' . "\n";
				}


				if ($nacionalidad == 'Extranjera')
				{
					echo '<OPTION VALUE="Venezolana">' . 'Venezolano(a)' . "\n";
					echo '<OPTION VALUE="Extranjera" SELECTED>' . 'Extranjero(a)' . "\n";
				}

			?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Sexo</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<SELECT NAME="sexo">
			<?

				if (! $sexo)
				{
					echo '<OPTION VALUE="" SELECTED>' . ' - - ' . "\n";
					echo '<OPTION VALUE="Femenino">' . 'Femenino' . "\n";
					echo '<OPTION VALUE="Masculino">' . 'Masculino' . "\n";
				}


				if ($sexo == 'Femenino')
				{
					echo '<OPTION VALUE="Femenino" SELECTED>' . 'Femenino' . "\n";
					echo '<OPTION VALUE="Masculino">' . 'Masculino' . "\n";
				}


				if ($sexo == 'Masculino')
				{
					echo '<OPTION VALUE="Femenino">' . 'Femenino' . "\n";
					echo '<OPTION VALUE="Masculino" SELECTED>' . 'Masculino' . "\n";
				}

			?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Tel&eacute;fono Celular</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="telefono_celular" VALUE="<? echo $telefono_celular ?>" SIZE="32" MAXLENGTH="30">

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			&nbsp; Ej: <B>(0414) 304-1234</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Tel&eacute;fono Trabajo</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="telefono_trabajo" VALUE="<? echo $telefono_trabajo ?>" SIZE="32" MAXLENGTH="30">

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			&nbsp; Ej: <B>(0212) 552-1234 &nbsp; ext. 212</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Tel&eacute;fono Habitaci&oacute;n</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="telefono_habitacion" VALUE="<? echo $telefono_habitacion ?>" SIZE="32" MAXLENGTH="30">

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			&nbsp; Ej: <B>(0212) 552-1234</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fax</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="fax" VALUE="<? echo $fax ?>" SIZE="32" MAXLENGTH="30">

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			&nbsp; Ej: <B>(0212) 552-1234</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Direcci&oacute;n</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<TEXTAREA NAME="direccion" ROWS="4" COLS="50"><? echo $direccion ?></TEXTAREA>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>E-Mail</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="email" VALUE="<? echo $email ?>" SIZE="40" MAXLENGTH="100">

		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			&nbsp; Ej: <B>info@cantv.net</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<HR SIZE="1" WIDTH="640">

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Datos Profesionales</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Profesi&oacute;n u Oficio</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<TEXTAREA NAME="profesion_oficio" ROWS="2" COLS="50"><? echo $profesion_oficio ?></TEXTAREA>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Egresado de la Instituci&oacute;n</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<TEXTAREA NAME="institucion" ROWS="2" COLS="50"><? echo $institucion ?></TEXTAREA>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Empleado en</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<TEXTAREA NAME="empleado_en" ROWS="2" COLS="50"><? echo $empleado_en ?></TEXTAREA>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Direcci&oacute;n Trabajo</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<TEXTAREA NAME="direccion_telefono" ROWS="4" COLS="50"><? echo $direccion_telefono ?></TEXTAREA>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Cargo que desempe&ntilde;a</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<TEXTAREA NAME="cargo_desempena" ROWS="4" COLS="50"><? echo $cargo_desempena ?></TEXTAREA>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Sueldo o Salario</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="sueldo_salario" VALUE="<? echo $sueldo_salario ?>" SIZE="35" MAXLENGTH="50">

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			&nbsp; Ej: <B>1.600.000,oo</B>
		</FONT>
	</TD>
</TR>
</TABLE>


<BR>

<HR SIZE="1" WIDTH="640">

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Datos del Conyuge</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Estado Civil</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<SELECT NAME="estado_civil">
			<?
				if (! $estado_civil)
				{
					echo '<OPTION VALUE="" SELECTED>' . ' - - ' . "\n";
					echo '<OPTION VALUE="Soltero(a)">' . 'Soltero(a)' . "\n";
					echo '<OPTION VALUE="Casado(a)">' . 'Casado(a)' . "\n";
					echo '<OPTION VALUE="Divorciado(a)">' . 'Divorciado(a)' . "\n";
					echo '<OPTION VALUE="Viudo(a)">' . 'Viudo(a)' . "\n";
				}


				if ($estado_civil == 'Soltero(a)')
				{
					echo '<OPTION VALUE="Soltero(a)" SELECTED>' . 'Soltero(a)' . "\n";
					echo '<OPTION VALUE="Casado(a)">' . 'Casado(a)' . "\n";
					echo '<OPTION VALUE="Divorciado(a)">' . 'Divorciado(a)' . "\n";
					echo '<OPTION VALUE="Viudo(a)">' . 'Viudo(a)' . "\n";
				}


				if ($estado_civil == 'Casado(a)')
				{
					echo '<OPTION VALUE="Soltero(a)">' . 'Soltero(a)' . "\n";
					echo '<OPTION VALUE="Casado(a)" SELECTED>' . 'Casado(a)' . "\n";
					echo '<OPTION VALUE="Divorciado(a)">' . 'Divorciado(a)' . "\n";
					echo '<OPTION VALUE="Viudo(a)">' . 'Viudo(a)' . "\n";
				}


				if ($estado_civil == 'Divorciado(a)')
				{
					echo '<OPTION VALUE="Soltero(a)">' . 'Soltero(a)' . "\n";
					echo '<OPTION VALUE="Casado(a)">' . 'Casado(a)' . "\n";
					echo '<OPTION VALUE="Divorciado(a)" SELECTED>' . 'Divorciado(a)' . "\n";
					echo '<OPTION VALUE="Viudo(a)">' . 'Viudo(a)' . "\n";
				}


				if ($estado_civil == 'Viudo(a)')
				{
					echo '<OPTION VALUE="Soltero(a)">' . 'Soltero(a)' . "\n";
					echo '<OPTION VALUE="Casado(a)">' . 'Casado(a)' . "\n";
					echo '<OPTION VALUE="Divorciado(a)">' . 'Divorciado(a)' . "\n";
					echo '<OPTION VALUE="Viudo(a)" SELECTED>' . 'Viudo(a)' . "\n";
				}
			?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>C&eacute;dula de Identidad</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="cid_conyuge" VALUE="<? echo $cid_conyuge ?>" SIZE="12" MAXLENGTH="8">

		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			&nbsp; Ej: <B>12421101 &nbsp; (Sin puntos o Comas)</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Apellidos, Nombres</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="apellidos_nombres_conyuge" VALUE="<? echo $apellidos_nombres_conyuge ?>" SIZE="45" MAXLENGTH="40">
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Nacimiento</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">


			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
				<SELECT NAME="dia_conyuge">
					<OPTION VALUE="" SELECTED>D&iacute;a
					<?
						#if (! $_dia) $_dia = date(j); 
			
						for($i=1; $i<32; $i++)
						{
							if ($dia_conyuge == $i)
							{
								echo '<OPTION VALUE="' . $i . '" SELECTED>' . $i . "\n";
							} else {
								echo '<OPTION VALUE="' . $i . '">' . $i . "\n";
							}
						}
					?>
				</SELECT>
				<SELECT NAME="mes_conyuge">
					<OPTION VALUE="" SELECTED>Mes
					<?
						#if (! $_mes) $_mes = date(n);
			
						for($i=1; $i<13; $i++)
						{
							if ($mes_conyuge == $i)
							{
								echo '<OPTION VALUE="' . $i . '" SELECTED>' . $meses[$i] . "\n";
							} else {
								echo '<OPTION VALUE="' . $i . '">' . $meses[$i] . "\n";
							}
						}
					?>
				</SELECT>


				<INPUT TYPE="text" NAME="ano_conyuge" VALUE="<? echo $ano_conyuge ?>" SIZE="6" MAXLENGTH="4">

				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					&nbsp; Ejemplo de A&ntilde;o: <B>1973</B>
				</FONT>

	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Nacionalidad</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<SELECT NAME="nacionalidad_conyuge">
			<?

				if (! $nacionalidad_conyuge)
				{
					echo '<OPTION VALUE="" SELECTED>' . ' - - ' . "\n";
					echo '<OPTION VALUE="Venezolana">' . 'Venezolana' . "\n";
					echo '<OPTION VALUE="Extranjera">' . 'Extranjera' . "\n";
				}


				if ($nacionalidad_conyuge == 'Venezolana')
				{
					echo '<OPTION VALUE="Venezolana" SELECTED>' . 'Venezolana' . "\n";
					echo '<OPTION VALUE="Extranjera">' . 'Extranjera' . "\n";
				}


				if ($nacionalidad_conyuge == 'Extranjera')
				{
					echo '<OPTION VALUE="Venezolana">' . 'Venezolana' . "\n";
					echo '<OPTION VALUE="Extranjera" SELECTED>' . 'Extranjera' . "\n";
				}

			?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Tipo de Vivienda</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<SELECT NAME="tipo_vivienda">
			<?
				if (! $tipo_vivienda)
				{
					echo '<OPTION VALUE="" SELECTED>' . ' - - ' . "\n";
					echo '<OPTION VALUE="Apartamento">' . 'Apartamento' . "\n";
					echo '<OPTION VALUE="Casa">' . 'Casa' . "\n";
				}


				if ($tipo_vivienda == 'Apartamento')
				{
					echo '<OPTION VALUE="Apartamento" SELECTED>' . 'Apartamento' . "\n";
					echo '<OPTION VALUE="Casa">' . 'Casa' . "\n";
				}


				if ($tipo_vivienda == 'Casa')
				{
					echo '<OPTION VALUE="Apartamento">' . 'Apartamento' . "\n";
					echo '<OPTION VALUE="Casa" SELECTED>' . 'Casa' . "\n";
				}
			?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Ingreso Familiar</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="ingreso_familiar" VALUE="<? echo $ingreso_familiar ?>" SIZE="35" MAXLENGTH="50">

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			&nbsp; Ej: <B>1.600.000,oo</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Grupo Familiar</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<SELECT NAME="nro_grupo_familiar">
			<?
				if ($nro_grupo_familiar == "")
				{
					echo '<OPTION VALUE="" SELECTED>' . ' - - ' . "\n";
				} else {
					echo '<OPTION VALUE="">' . ' - - ' . "\n";
				}
				
				for ($i=1; $i<10; $i++)
				{
						if ($nro_grupo_familiar == $i)
						{
							echo '<OPTION VALUE="' . $i . '" SELECTED>' . $i . "\n";
						} else {
							echo '<OPTION VALUE="' . $i . '">' . $i . "\n";
						}
				}
			?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Grado de Instrucci&oacute;n</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<TEXTAREA NAME="grado_instruccion" ROWS="2" COLS="50"><? echo $grado_instruccion ?></TEXTAREA>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Profes&oacute;n u Ocupaci&oacute;n</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<TEXTAREA NAME="profesion_ocupacion" ROWS="2" COLS="50"><? echo $profesion_ocupacion ?></TEXTAREA>
	</TD>
</TR>
</TABLE>

<BR>

<HR SIZE="1" WIDTH="640">

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Datos del Vehiculo Automotor</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Posee Vehiculo</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<SELECT NAME="vehiculo">
			<?

				if (! $vehiculo)
				{
					echo '<OPTION VALUE="" SELECTED>' . ' - - ' . "\n";
					echo '<OPTION VALUE="Si">' . 'Si' . "\n";
					echo '<OPTION VALUE="No">' . 'No' . "\n";
				}


				if ($vehiculo == 'Si')
				{
					echo '<OPTION VALUE="Si" SELECTED>' . 'Si' . "\n";
					echo '<OPTION VALUE="No">' . 'No' . "\n";
				}


				if ($vehiculo == 'No')
				{
					echo '<OPTION VALUE="Si">' . 'Si' . "\n";
					echo '<OPTION VALUE="No" SELECTED>' . 'No' . "\n";
				}

			?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Licencia de Conducir Num.</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="licencia_nro" VALUE="<? echo $licencia_nro ?>" SIZE="45" MAXLENGTH="50">
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Marca</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="marca_vehiculo" VALUE="<? echo $marca_vehiculo ?>" SIZE="45" MAXLENGTH="50">
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Modelo</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="modelo_vehiculo" VALUE="<? echo $modelo_vehiculo ?>" SIZE="45" MAXLENGTH="50">
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>A&ntilde;o</B>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="ano" VALUE="<? echo $ano ?>" SIZE="6" MAXLENGTH="4">
	</TD>
</TR>
</TABLE>

<BR>

	<INPUT TYPE="hidden" NAME="cedula" VALUE="<? echo $cedula ?>">

	<INPUT TYPE="submit" NAME="actualizar" VALUE="Actualizar"> &nbsp; &nbsp; 
	<INPUT TYPE="submit" NAME="cancelar" VALUE="Cancelar">	


<BR>


</FORM>

<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
