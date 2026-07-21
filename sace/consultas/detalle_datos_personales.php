<?
session_start();
###
###		Este script muestra el detalle de los Datos Personales, mostrando todos los
###		campos de Dicha Tabla en la Base de Datos, y en algunos casos dandole Formato.
###


###
### Los Clasicos Includes
###
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

//include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

require_once dirname(__FILE__) . '/../includes/conexion.php';
###
### Busco los Datos Personales en la Base de Datos segun la Cedula de Identidad suministrada
###
$cedula=$_GET['cedula'];
$sqlcmd = "SELECT apellidos, nombres, fecha_nacimiento, lugar_nacimiento, nacionalidad, sexo, "
		. "(YEAR(CURRENT_DATE)-YEAR(fecha_nacimiento)) - (RIGHT(CURRENT_DATE,5)<RIGHT(fecha_nacimiento,5)) AS edad, "
		. "telefono_habitacion, telefono_trabajo, telefono_celular, direccion, fax, email, profesion_oficio, institucion, "
		. "empleado_en, cargo_desempena, direccion_telefono, sueldo_salario, estado_civil, cid_conyuge, "
		. "apellidos_nombres_conyuge, nacionalidad_conyuge, grado_instruccion, profesion_ocupacion, nro_grupo_familiar, "
		. "ingreso_familiar, tipo_vivienda, vehiculo, marca_vehiculo, ano, licencia_nro, fecha_nacimiento_conyuge, "
		. "modelo_vehiculo, "
		. "(YEAR(CURRENT_DATE)-YEAR(fecha_nacimiento_conyuge)) - (RIGHT(CURRENT_DATE,5)<RIGHT(fecha_nacimiento_conyuge,5)) AS edad_conyuge "
		. "FROM datos_personales "
		. "WHERE cedula='$cedula' ";


$sqlcmd = "SELECT * FROM datos_personales WHERE cedula='$cedula'";
$query = mysqli_query($conexion, $sqlcmd);

if ($registro = mysqli_fetch_object($query)) {
    $apellidos              = strtolower($registro->apellidos);
    $nombres                = strtolower($registro->nombres);
    $fecha_nacimiento       = $registro->fecha_nacimiento;
    $lugar_nacimiento       = strtolower($registro->lugar_nacimiento);
    $nacionalidad           = $registro->nacionalidad;
    $sexo                   = $registro->sexo;
    $edad                   = $registro->edad;
    $telefono_habitacion    = $registro->telefono_habitacion;
    $telefono_trabajo       = $registro->telefono_trabajo;
    $telefono_celular       = $registro->telefono_celular;
    $direccion              = $registro->direccion;
    $fax                    = $registro->fax;
    $email                  = $registro->email;
    $profesion_oficio       = $registro->profesion_oficio;
    $institucion            = $registro->institucion;
    $empleado_en            = $registro->empleado_en;
    $cargo_desempena        = $registro->cargo_desempena;
    $direccion_telefono     = $registro->direccion_telefono;
    $sueldo_salario         = $registro->sueldo_salario;
    $estado_civil           = $registro->estado_civil;
    $cid_conyuge            = $registro->cid_conyuge;
    $apellidos_nombres_conyuge = strtolower($registro->apellidos_nombres_conyuge);
    $nacionalidad_conyuge   = $registro->nacionalidad_conyuge;
    $grado_instruccion      = $registro->grado_instruccion;
    $profesion_ocupacion    = $registro->profesion_ocupacion;
    $nro_grupo_familiar     = $registro->nro_grupo_familiar;
    $ingreso_familiar       = $registro->ingreso_familiar;
    $tipo_vivienda          = $registro->tipo_vivienda;
    $vehiculo               = $registro->vehiculo;
    $marca_vehiculo         = $registro->marca_vehiculo;
    $ano                    = $registro->ano;
    $licencia_nro           = $registro->licencia_nro;
    $fecha_nacimiento_conyuge = $registro->fecha_nacimiento_conyuge;
    $modelo_vehiculo        = $registro->modelo_vehiculo;
    $edad_conyuge           = $registro->edad_conyuge;
} else {
    // Si no se encuentra el registro
    $apellidos = $nombres = $fecha_nacimiento = $lugar_nacimiento = $nacionalidad = $sexo = $edad = '';
    $telefono_habitacion = $telefono_trabajo = $telefono_celular = $direccion = $fax = '';
    $email = $profesion_oficio = $institucion = $empleado_en = $cargo_desempena = '';
    $direccion_telefono = $sueldo_salario = $estado_civil = $cid_conyuge = $apellidos_nombres_conyuge = '';
    $nacionalidad_conyuge = $grado_instruccion = $profesion_ocupacion = $nro_grupo_familiar = '';
    $ingreso_familiar = $tipo_vivienda = $vehiculo = $marca_vehiculo = $ano = $licencia_nro = '';
    $fecha_nacimiento_conyuge = $modelo_vehiculo = $edad_conyuge = '';
}
mysqli_free_result($query);

// Formateo
$apellidos_nombres = 'No Existe Registro';
if ($apellidos && $nombres)      $apellidos_nombres = ucwords($apellidos) . ', ' . ucwords($nombres);
elseif ($apellidos)              $apellidos_nombres = ucwords($apellidos);
elseif ($nombres)                $apellidos_nombres = ucwords($nombres);

$fecha_nacimiento = ($fecha_nacimiento == '0000-00-00' || $fecha_nacimiento == '') 
    ? 'No Existe Registro' 
    : fecha($fecha_nacimiento);

if ($lugar_nacimiento == '') $lugar_nacimiento = 'No Existe Registro';
if ($nacionalidad == '') $nacionalidad = 'No Existe Registro';
if ($telefono_habitacion == '') $telefono_habitacion = 'No Existe Registro';
if ($telefono_trabajo == '') $telefono_trabajo = 'No Existe Registro';
if ($telefono_celular == '') $telefono_celular = 'No Existe Registro';
if ($direccion == '') $direccion = 'No Existe Registro';
if ($fax == '') $fax = 'No Existe Registro';

if ($email == '') {
    $email = 'No Existe Registro';
} else {
    $email = '<a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a>';
}

if ($profesion_oficio == '') $profesion_oficio = 'No Existe Registro';
if ($institucion == '') $institucion = 'No Existe Registro';
if ($empleado_en == '') $empleado_en = 'No Existe Registro';
if ($cargo_desempena == '') $cargo_desempena = 'No Existe Registro';
if ($direccion_telefono == '') $direccion_telefono = 'No Existe Registro';
if ($sueldo_salario == '') $sueldo_salario = 'No Existe Registro';
if ($estado_civil == '') $estado_civil = 'No Existe Registro';
if ($cid_conyuge == '') $cid_conyuge = 'No Existe Registro';

if ($apellidos_nombres_conyuge == '') {
    $apellidos_nombres_conyuge = 'No Existe Registro';
} else {
    $apellidos_nombres_conyuge = ucwords($apellidos_nombres_conyuge);
}

if ($fecha_nacimiento_conyuge == '0000-00-00' || $fecha_nacimiento_conyuge == '') {
    $fecha_nacimiento_conyuge = 'No Existe Registro';
} else {
    $fecha_nacimiento_conyuge = fecha($fecha_nacimiento_conyuge);
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

		<A HREF="ingreso_de_cedula.php"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Consultar Notas de un Estudiante</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="consulta_por_cedula.php?cedula=<? echo $cedula ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Resultado</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Detalle Ficha</B></FONT> 
	</TD>
</TR>
</TABLE>

<BR>

<IMG SRC="/sace/imagenes/menu_consultar_notas.jpg" ALT="" WIDTH="234" HEIGHT="18" BORDER="0">

<BR><BR>

<?
	#include ("$DOCUMENT_ROOT/includes/encabezado.php");
?>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000000">
C&eacute;dula de Identidad Consultada: <B><? echo strtr (number_format($cedula), ",", ".") ?></B>
</FONT>

<BR><BR>


<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Datos Personales (Detalle)</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Apellidos, Nombres</B>
		</FONT>
	</TD>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Nacimiento</B>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Edad</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $apellidos_nombres ?>
		</FONT>
	</TD>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $fecha_nacimiento ?>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $edad ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Lugar de Nacimiento</B>
		</FONT>
	</TD>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Nacionalidad</B>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Sexo</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo ucwords($lugar_nacimiento) ?>
		</FONT>
	</TD>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo ucwords($nacionalidad) ?>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $sexo ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Tel&eacute;fono Celular</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Tel&eacute;fono Trabajo</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Tel&eacute;fono Habitaci&oacute;n</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $telefono_celular ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $telefono_trabajo ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $telefono_habitacion ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Direcci&oacute;n</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $direccion ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="450" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>E-Mail</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fax</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="450" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $email ?>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $fax ?>
		</FONT>
	</TD>
</TR>
</TABLE>

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
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Profesi&oacute;n u Oficio</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $profesion_oficio ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Egresado de la Instituci&oacute;n</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $institucion ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Empleado en</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $empleado_en ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Direcci&oacute;n Trabajo</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $direccion_telefono ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Cargo que desempe&ntilde;a</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $cargo_desempena ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Sueldo o Salario</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $sueldo_salario ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<HR SIZE="1" WIDTH="640">

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Datos de la Pareja</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Apellidos, Nombres</B>
		</FONT>
	</TD>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Nacimiento</B>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Edad</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $apellidos_nombres_conyuge ?>
		</FONT>
	</TD>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $fecha_nacimiento_conyuge ?>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $edad_conyuge ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Estado Civil</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>C&eacute;dula de Identidad</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Nacionalidad</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $estado_civil ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $cid_conyuge ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $nacionalidad_conyuge ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Tipo de Vivienda</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Ingreso Familiar</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Grupo Familiar</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $tipo_vivienda ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $ingreso_familiar ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $nro_grupo_familiar ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Grado de Instrucci&oacute;n</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $grado_instruccion ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Profes&oacute;n u Ocupaci&oacute;n</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $profesion_ocupacion ?>
		</FONT>
	</TD>
</TR>
</TABLE>


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
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Licencia de Conducir Num.</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $vehiculo ?>
		</FONT>
	</TD>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $licencia_nro ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Marca</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Modelo</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>A&ntilde;o</B>
		</FONT>
	</TD>
</TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $marca_vehiculo ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $modelo_vehiculo ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $ano ?>
		</FONT>
	</TD>
</TR>
</TABLE>


<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
