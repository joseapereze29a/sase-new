<?php
session_start();
###
###		Este script desplega los Datos Personales y el/los diferentes Postgrado(s)
###		realizados por el Estudiante si fuera el caso. Este script da la opcion de
###		ver con mas Detalle tanto los Datos Personales como las Calificaciones, asi
###		como preparar las Notas certificadas para mandarlas a Imprimir.
###


###
### Si no pasa una CI voy a la pagina anterior
###

/*if (! ereg ("^[0-9]+$", $cedula) )
{
	header ("Location: ingreso_de_cedula.php");
	exit;
}*/
$cedula=$_POST['cedula'];
if (!preg_match("/^[0-9]+$/", $cedula)) {
    header("Location: ingreso_de_cedula.php");
    exit;
}


###
### Los Clasicos Includes
###
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");


###
### Chequeo si existen Datos para la CI que he recibido del script anterior
###

/*$sqlcmd = "SELECT count(*) as cantidad "
		. "FROM datos_personales "
		. "WHERE cedula='$cedula' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$cantidad = $registro->cantidad;
}


$sqlcmd = "SELECT count(*) as cantidad_notas "
		. "FROM record_notas, registro_actas "
		. "WHERE record_notas.codacta=registro_actas.codacta AND record_notas.cedula='$cedula' "
		. "GROUP BY codcohorte ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$cantidad_notas = $registro->cantidad_notas;
}*/
$sqlcmd = "SELECT count(*) as cantidad FROM datos_personales WHERE cedula=?";
$stmt = mysqli_prepare($conexion, $sqlcmd);
mysqli_stmt_bind_param($stmt, "s", $cedula);
mysqli_stmt_execute($stmt);
$result = $result = array();
mysqli_stmt_store_result($stmt);
$meta = mysqli_stmt_result_metadata($stmt);
$fields = $meta->fetch_fields();
$data = array();
$bindArgs = array();
foreach ($fields as $field) {
    $data[$field->name] = null;
    $bindArgs[] = &$data[$field->name];
}
call_user_func_array(array($stmt, 'bind_result'), $bindArgs);
if (mysqli_stmt_fetch($stmt)) {
    foreach ($data as $key => $val) {
        $result[$key] = $val;
    }
}

$registro = mysqli_fetch_assoc($result);
$cantidad = $registro['cantidad'];
mysqli_stmt_close($stmt);

$sqlcmd = "SELECT count(*) as cantidad_notas "
        . "FROM record_notas rn JOIN registro_actas ra ON rn.codacta = ra.codacta "
        . "WHERE rn.cedula=? "
        . "GROUP BY ra.codcohorte";
$stmt = mysqli_prepare($conexion, $sqlcmd);
mysqli_stmt_bind_param($stmt, "s", $cedula);
mysqli_stmt_execute($stmt);
$result = $result = array();
mysqli_stmt_store_result($stmt);
$meta = mysqli_stmt_result_metadata($stmt);
$fields = $meta->fetch_fields();
$data = array();
$bindArgs = array();
foreach ($fields as $field) {
    $data[$field->name] = null;
    $bindArgs[] = &$data[$field->name];
}
call_user_func_array(array($stmt, 'bind_result'), $bindArgs);
if (mysqli_stmt_fetch($stmt)) {
    foreach ($data as $key => $val) {
        $result[$key] = $val;
    }
}

while ($registro = mysqli_fetch_assoc($result)) {
    $cantidad_notas = $registro['cantidad_notas'];
}
mysqli_stmt_close($stmt);

###
### Reviso si Existen Datos Personales para esa CI, o si existen por lo menos Notas Asociadas al Estudiante
###

if ($cantidad < 1)									
{													
	if ($cantidad_notas > 0)	$cantidad = 1;		
}													


###
### Si la Cedula de Identidad existe en la Base de Datos, Busco los Datos Personales
###

if ($cantidad > 0) {
    $sqlcmd = "SELECT apellidos, nombres, fecha_nacimiento, lugar_nacimiento, nacionalidad, sexo, "
            . "(YEAR(CURDATE())-YEAR(fecha_nacimiento)) - (RIGHT(CURDATE(),5)<RIGHT(fecha_nacimiento,5)) AS edad, "
            . "telefono_habitacion, telefono_trabajo, telefono_celular "
            . "FROM datos_personales "
            . "WHERE cedula=?";
    $stmt = mysqli_prepare($conexion, $sqlcmd);
    mysqli_stmt_bind_param($stmt, "s", $cedula);
    mysqli_stmt_execute($stmt);
    $result = $result = array();
mysqli_stmt_store_result($stmt);
$meta = mysqli_stmt_result_metadata($stmt);
$fields = $meta->fetch_fields();
$data = array();
$bindArgs = array();
foreach ($fields as $field) {
    $data[$field->name] = null;
    $bindArgs[] = &$data[$field->name];
}
call_user_func_array(array($stmt, 'bind_result'), $bindArgs);
if (mysqli_stmt_fetch($stmt)) {
    foreach ($data as $key => $val) {
        $result[$key] = $val;
    }
}

    while ($registro = mysqli_fetch_assoc($result)) {
        $apellidos = strtolower($registro['apellidos']);
        $nombres = strtolower($registro['nombres']);
        $fecha_nacimiento = $registro['fecha_nacimiento'];
        $lugar_nacimiento = strtolower($registro['lugar_nacimiento']);
        $nacionalidad = $registro['nacionalidad'];
        $sexo = $registro['sexo'];
        $edad = $registro['edad'];
        $telefono_habitacion = $registro['telefono_habitacion'];
        $telefono_trabajo = $registro['telefono_trabajo'];
        $telefono_celular = $registro['telefono_celular'];
    }
    mysqli_stmt_close($stmt);
		
### Formateo los Datos obtenidos, para mostarlos correctamente
		
		if ( ($apellidos) AND ($nombres) ) $apellidos_nombres = ucwords($apellidos) . ', ' . ucwords($nombres);
		if ( ($apellidos) AND (! $nombres) ) $apellidos_nombres = ucwords($apellidos);
		if ( (! $apellidos) AND ($nombres) ) $apellidos_nombres = ucwords($nombres);
		if ( (! $apellidos) AND (! $nombres) ) $apellidos_nombres = 'No Existe Registro';
		
		
		if ( ($fecha_nacimiento == '0000-00-00') OR ($fecha_nacimiento == "") )
		{
			$fecha_nacimiento = 'No Existe Registro';
		
		} else {
		
			$fecha_nacimiento = fecha($fecha_nacimiento);
		}
		
		
		if ($lugar_nacimiento == "") $lugar_nacimiento = 'No Existe Registro';
		
		
		if ($nacionalidad == "") $nacionalidad = 'No Existe Registro';
		
		
		if ( ($edad > 1) AND ($edad<152) )
		{
			$edad = $edad . ' a&ntilde;os';
		
		} else {
		
			$edad = '';
		}
		
		if ($telefono_habitacion == "") $telefono_habitacion = "No Existe Registro";
		
		if ($telefono_trabajo == "") $telefono_trabajo = "No Existe Registro";
		
		if ($telefono_celular == "") $telefono_celular = "No Existe Registro";

}
?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#0000FF" VLINK="#0000FF">

<CENTER>

<?php
	include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/encabezado.php");
?>


<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
	<TD WIDTH="100%" ALIGN="left" VALIGN="top">
	
		<A HREF="../"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="ingreso_de_cedula.php"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Consultar Notas de un Estudiante</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Resultado</B></FONT> 
	</TD>
</TR>
</TABLE>

<BR>

<IMG SRC="/sace/imagenes/menu_consultar_notas.jpg" ALT="" WIDTH="234" HEIGHT="18" BORDER="0">

<BR><BR>


<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000000">
C&eacute;dula de Identidad Consultada: <B><?php echo strtr (number_format($cedula), ",", ".") ?></B>
</FONT>

<BR><BR>

<?php

###
### Si NO existe la CI en la Base de Datos, muestro un mensaje, de lo contrario Muestro los Datos Obtenidos
###

if ($cantidad < 1)
{
?>

		<BR><BR><BR>

		<TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
		<TR>
			<TD WIDTH="700" ALIGN="center" VALIGN="top">
				<FONT FACE="Verdana,Arial,Geneva">
					<B>No se Encontr&oacute; ningun Registro que concuerde<BR><BR>
					con el N&uacute;mero de C&eacute;dula de Identidad suministrado.</B>
				</FONT>
			</TD>
		</TR>
		</TABLE>

<?php
} else {
?>

		<TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
		<TR>
			<TD WIDTH="700" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
				<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
					<B>Datos Personales</B>
				</FONT>
			</TD>
		</TR>
		</TABLE>
		
		<TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
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
			<TD WIDTH="200" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<B>Edad</B>
				</FONT>
			</TD>
		</TR>
		<TR>
			<TD WIDTH="250" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<?php echo $apellidos_nombres ?>
				</FONT>
			</TD>
			<TD WIDTH="250" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<?php echo $fecha_nacimiento ?>
				</FONT>
			</TD>
			<TD WIDTH="200" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<?php echo $edad ?>
				</FONT>
			</TD>
		</TR>
		</TABLE>
		
		<BR>
		
		<TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
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
			<TD WIDTH="200" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<B>Sexo</B>
				</FONT>
			</TD>
		</TR>
		<TR>
			<TD WIDTH="250" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<?php echo ucwords($lugar_nacimiento) ?>
				</FONT>
			</TD>
			<TD WIDTH="250" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<?php echo ucwords($nacionalidad) ?>
				</FONT>
			</TD>
			<TD WIDTH="200" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<?php echo $sexo ?>
				</FONT>
			</TD>
		</TR>
		</TABLE>
		
		<BR>
		
		<TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
		<TR>
			<TD WIDTH="250" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<B>Tel&eacute;fono Celular</B>
				</FONT>
			</TD>
			<TD WIDTH="250" ALIGN="left" VALIGN="top">
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
			<TD WIDTH="250" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<?php echo $telefono_celular ?>
				</FONT>
			</TD>
			<TD WIDTH="250" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<?php echo $telefono_trabajo ?>
				</FONT>
			</TD>
			<TD WIDTH="200" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<?php echo $telefono_habitacion ?>
				</FONT>
			</TD>
		</TR>
		</TABLE>
		
		<TABLE BORDER="0" WIDTH="700" CELLSPACING="10" CELLPADDING="2">
		<TR>
			<TD WIDTH="600" ALIGN="left" VALIGN="top">
				<P> </P>
			</TD>
			<TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="#0066FF">
				<A HREF="detalle_datos_personales.php?cedula=<?php echo $cedula ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
						<B>Ver mas detalle</B>
					</FONT>
				</A>
			</TD>
		</TR>
		</TABLE>
		
		<HR SIZE="1" WIDTH="640">

<?php
###
### Busco su Record de Notas, primero busco en cuantas Cohortes aparece este Estudiante
###

$sqlcmd = "SELECT ra.codcohorte "
. "FROM record_notas rn JOIN registro_actas ra ON rn.codacta = ra.codacta "
. "WHERE rn.cedula=? "
. "GROUP BY ra.codcohorte";
$stmt = mysqli_prepare($conexion, $sqlcmd);
mysqli_stmt_bind_param($stmt, "s", $cedula);
mysqli_stmt_execute($stmt);
$result = $result = array();
mysqli_stmt_store_result($stmt);
$meta = mysqli_stmt_result_metadata($stmt);
$fields = $meta->fetch_fields();
$data = array();
$bindArgs = array();
foreach ($fields as $field) {
    $data[$field->name] = null;
    $bindArgs[] = &$data[$field->name];
}
call_user_func_array(array($stmt, 'bind_result'), $bindArgs);
if (mysqli_stmt_fetch($stmt)) {
    foreach ($data as $key => $val) {
        $result[$key] = $val;
    }
}

$codcohorte = array();
while ($registro = mysqli_fetch_assoc($result)) {
$codcohorte[] = $registro['codcohorte'];
}
mysqli_stmt_close($stmt);

$cuantas_cohortes = count($codcohorte);

for ($i = 0; $i < $cuantas_cohortes; $i++) {
$sqlcmd = "SELECT dc.ciudad, oe.tipo, oe.mencion_especialidad, oe.codopest, oe.codsede, "
	. "oe.periodos, oe.creditos, co.fecha_inicio, co.periodo_lectivo "
	. "FROM directorio_cippsv dc JOIN oportunidades_estudio oe ON dc.codsede = oe.codsede "
	. "JOIN cohortes co ON oe.codopest = co.codopest AND oe.codsede = co.codsede "
	. "WHERE co.codcohorte=?";
$stmt = mysqli_prepare($conexion, $sqlcmd);
mysqli_stmt_bind_param($stmt, "s", $codcohorte[$i]);
mysqli_stmt_execute($stmt);
$result = $result = array();
mysqli_stmt_store_result($stmt);
$meta = mysqli_stmt_result_metadata($stmt);
$fields = $meta->fetch_fields();
$data = array();
$bindArgs = array();
foreach ($fields as $field) {
    $data[$field->name] = null;
    $bindArgs[] = &$data[$field->name];
}
call_user_func_array(array($stmt, 'bind_result'), $bindArgs);
if (mysqli_stmt_fetch($stmt)) {
    foreach ($data as $key => $val) {
        $result[$key] = $val;
    }
}

while ($registro = mysqli_fetch_assoc($result)) {
$ciudad = $registro['ciudad'];
$tipo = $registro['tipo'];
$mencion_especialidad = $registro['mencion_especialidad'];
$codopest = $registro['codopest'];
$codsede = $registro['codsede'];
$fecha_inicio = $registro['fecha_inicio'];
$periodo_lectivo = $registro['periodo_lectivo'];
}
mysqli_stmt_close($stmt);
?>

			<BR>
			
			<TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
			<TR>
				<TD WIDTH="700" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
					<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
						<B>Informaci&oacute;n del Postgrado</B>
					</FONT>
				</TD>
			</TR>
			</TABLE>
			
			<TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
			<TR>
				<TD WIDTH="175" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<B>Ciudad</B>
					</FONT>
				</TD>
				<TD WIDTH="175" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<B>Tipo</B>
					</FONT>
				</TD>
				<TD WIDTH="350" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<B>Mencion o Especialidad</B>
					</FONT>
				</TD>
			</TR>
			<TR>
				<TD WIDTH="175" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<?php echo $ciudad ?>
					</FONT>
				</TD>
				<TD WIDTH="175" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<?php echo $tipo ?>
					</FONT>
				</TD>
				<TD WIDTH="350" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<?php echo $mencion_especialidad ?>
					</FONT>
				</TD>
			</TR>
			</TABLE>
			
			<BR>
			
			
			<TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
			<TR>
				<TD WIDTH="175" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<B>Cohorte</B>
					</FONT>
				</TD>
				<TD WIDTH="175" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<B>Periodo Lectivo</B>
					</FONT>
				</TD>
				<TD WIDTH="350" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<B>Fecha de Inicio</B>
					</FONT>
				</TD>
			</TR>
			<TR>
				<TD WIDTH="175" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<?php echo $codcohorte[$i] ?>
					</FONT>
				</TD>
				<TD WIDTH="175" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<?php echo $periodo_lectivo ?>
					</FONT>
				</TD>
				<TD WIDTH="350" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<?php echo fecha($fecha_inicio) ?>
					</FONT>
				</TD>
			</TR>
			</TABLE>
			
			<BR><BR>
			
			<TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
			<TR>
				<TD WIDTH="280" ALIGN="left" VALIGN="top">
					<FONT FACE="Verdana,Arial,Geneva" COLOR="#000099">
						<B>Record de Notas</B>
					</FONT>
				</TD>
				<TD WIDTH="120" ALIGN="left" VALIGN="top">
			
						<TABLE BORDER="0" WIDTH="120" CELLSPACING="2" CELLPADDING="2">
						<TR>
							<TD WIDTH="120" ALIGN="center" VALIGN="top" BGCOLOR="#0066FF">
								<A HREF="notas_certificadas.php?cedula=<?php echo $cedula ?>&codcohorte=<?php echo $codcohorte[$i] ?>">
									<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
										<B>Notas Simples</B>
									</FONT>
								</A>
							</TD>
						</TR>
						</TABLE>

				</TD>
				<TD WIDTH="200" ALIGN="left" VALIGN="top">
			
						<TABLE BORDER="0" WIDTH="200" CELLSPACING="2" CELLPADDING="2">
						<TR>
							<TD WIDTH="200" ALIGN="center" VALIGN="top" BGCOLOR="#0066FF">
								<A HREF="notas_certificadas_egresados.php?cedula=<?php echo $cedula ?>&codcohorte=<?php echo $codcohorte[$i] ?>">
									<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
										<B>Notas Certificadas</B>
									</FONT>
								</A>
							</TD>
						</TR>
						</TABLE>

				</TD>
				<TD WIDTH="100" ALIGN="left" VALIGN="top">
			
						<TABLE BORDER="0" WIDTH="100" CELLSPACING="2" CELLPADDING="2">
						<TR>
							<TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="#0066FF">
								<A HREF="detalle_de_calificaciones.php?cedula=<?php echo $cedula ?>&codcohorte=<?php echo $codcohorte[$i] ?>">
									<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
										<B>Ver mas detalle</B>
									</FONT>
								</A>
							</TD>
						</TR>
						</TABLE>
			
				</TD>
			</TR>
			</TABLE>
			
			<TABLE BORDER="0" WIDTH="700" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
			<TR>
				<TD WIDTH="100" ALIGN="left" VALIGN="top">
					<A HREF="consulta_por_cedula.php?cedula=<?php echo $cedula ?>&_orden=codasig">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF"><B>Codigo</B></FONT></A>
				</TD>
				<TD WIDTH="350" ALIGN="left" VALIGN="top">
					<A HREF="consulta_por_cedula.php?cedula=<?php echo $cedula ?>&_orden=asignatura">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF"><B>Asignatura</B></FONT></A>
				</TD>
				<TD WIDTH="100" ALIGN="center" VALIGN="top">
					<A HREF="consulta_por_cedula.php?cedula=<?php echo $cedula ?>&_orden=calificacion">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF"><B>Nota</B></FONT></A>
				</TD>
				<TD WIDTH="150" ALIGN="right" VALIGN="top">
					<A HREF="consulta_por_cedula.php?cedula=<?php echo $cedula ?>&_orden=fecha_aprobacion">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF"><B>Fecha Aprobacion</B></FONT></A>
				</TD>
			</TR>
<?php
###
### Muestro las Notas Obtenidas por el Query 
### (Junto las Actas de la Tabla Registo_Actas y las Multiactas) en una Tabla temporal y listo de ese Resultado
###

if (!$_orden) $_orden = 'periodos, codasig, codacta';

$sqlcmd_temp = "CREATE TEMPORARY TABLE actas_consulta_temp (
	codasig VARCHAR(255),
	asignatura VARCHAR(255),
	creditos INT,
	periodos INT,
	calificacion VARCHAR(255),
	fecha_aprobacion DATE,
	codasig_imp VARCHAR(255),
	codacta VARCHAR(255)
)";
mysqli_query($conexion, $sqlcmd_temp);

$sqlcmd_insert1 = "INSERT INTO actas_consulta_temp (codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta) "
		. "SELECT ra.codasig, pe.asignatura, pe.creditos, pe.periodos, rn.calificacion, ra.fecha_aprobacion, pe.codasig_imp, rn.codacta "
		. "FROM registro_actas ra JOIN pensum_estudios pe ON ra.codasig = pe.codasig AND pe.codsede=? AND pe.codopest=? "
		. "JOIN record_notas rn ON ra.codacta = rn.codacta "
		. "JOIN cohortes co ON ra.codcohorte = co.codcohorte "
		. "WHERE co.codcohorte=? AND rn.cedula=?";
$stmt_insert1 = mysqli_prepare($conexion, $sqlcmd_insert1);
mysqli_stmt_bind_param($stmt_insert1, "sssd", $codsede, $codopest, $codcohorte[$i], $cedula);
mysqli_stmt_execute($stmt_insert1);
mysqli_stmt_close($stmt_insert1);

$sqlcmd_insert2 = "INSERT INTO actas_consulta_temp (codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta) "
		. "SELECT ma.codasig, pe.asignatura, pe.creditos, pe.periodos, rn.calificacion, ma.fecha_aprobacion, pe.codasig_imp, ma.codacta "
		. "FROM pensum_estudios pe JOIN multiactas ma ON pe.codasig = ma.codasig AND pe.codsede=? AND pe.codopest=? "
		. "JOIN record_notas rn ON ma.mid = rn.mid "
		. "WHERE ma.codcohorte=? AND rn.cedula=?";
$stmt_insert2 = mysqli_prepare($conexion, $sqlcmd_insert2);
mysqli_stmt_bind_param($stmt_insert2, "sssd", $codsede, $codopest, $codcohorte[$i], $cedula);
mysqli_stmt_execute($stmt_insert2);
mysqli_stmt_close($stmt_insert2);

$sqlcmd_select_notas = "SELECT codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta "
		. "FROM actas_consulta_temp "
		. "ORDER BY " . $_orden;
$result_notas = mysqli_query($conexion, $sqlcmd_select_notas);

$notas = 0;
$total_creditos = 0;

while ($registro_notas = mysqli_fetch_assoc($result_notas)) {
	$codasig = $registro_notas['codasig'];
	$asignatura = $registro_notas['asignatura'];
	$creditos = $registro_notas['creditos'];
	$periodos = $registro_notas['periodos'];
	$calificacion = $registro_notas['calificacion'];
	$fecha_aprobacion = $registro_notas['fecha_aprobacion'];
	$codasig_imp = $registro_notas['codasig_imp'];
	$codacta = $registro_notas['codacta'];

	if (($fecha_aprobacion == '0000-00-00') || ($fecha_aprobacion == "")) {
		$fecha_aprobacion = "";
	} else {
		$fecha_aprobacion = fecha($fecha_aprobacion, 'corto');
	}

	if (($calificacion >= 1) && ($calificacion <= 20) && ($creditos > 0)) {
		$notas += ($calificacion * $creditos);
		$total_creditos += $creditos;
	}

	if ($calificacion == 404) $calificacion = 'No Cursó';
	if ($calificacion == 99) $calificacion = 'Reprobado';
	if ($calificacion == 100) $calificacion = 'Aprobado';
	if ($calificacion == 110) $calificacion = 'Meritorio';
	if ($calificacion == 120) $calificacion = 'Excelencia';
	if ($calificacion == 212) $calificacion = 'Equivalencia';

	$bg_celda = ($bg_celda == '#CCCCCC') ? '#FFFFFF' : '#CCCCCC';
	?>
	<TR>
		<TD WIDTH="100" ALIGN="left" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" <?php if (($calificacion >= 1) && ($calificacion <= 14)) echo 'COLOR="#3300FF"'; ?>>
				<?php echo $codasig_imp ?>
			</FONT>
		</TD>
		<TD WIDTH="350" ALIGN="left" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" <?php if (($calificacion >= 1) && ($calificacion <= 14)) echo 'COLOR="#3300FF"'; ?>>
				<?php
				$curso_d = strtolower(substr($codacta, -3, 2));
				if ($curso_d == "cd") {
					echo $asignatura . ' <B>(CD)</B>';
				} else {
					echo $asignatura;
				}
				$asignatura = '';
				?>
			</FONT>
		</TD>
		<TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" <?php if (($calificacion >= 1) && ($calificacion <= 14)) echo 'COLOR="#3300FF"><B>'; ?>><?php echo $calificacion ?></FONT>
		</TD>
		<TD WIDTH="150" ALIGN="right" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
				<?php echo $fecha_aprobacion ?>
			</FONT>
		</TD>
	</TR>
	<?php
	$creditos = "";
	$calificacion = "";
}
mysqli_free_result($result_notas);
?>
</TABLE>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2">
	<TR>
		<TD WIDTH="600" ALIGN="center" VALIGN="top">
			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
				<B>Índice Académico: <?php echo number_format(($notas / $total_creditos), 2, ',', '') ?></B>
			</FONT>
		</TD>
	</TR>
</TABLE>

<BR>
<HR SIZE="1" WIDTH="640">
<?php
mysqli_query($conexion, "DROP TEMPORARY TABLE IF EXISTS actas_consulta_temp");
}
}

// Cierra la conexión al final del script
mysqli_close($conexion);
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
