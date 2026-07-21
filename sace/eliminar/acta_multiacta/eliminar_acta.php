<?
###
###		Este script Elimina una Acta seleccionada y todas las notas que contenga esa Acta.
###

###
### Debe haber un (codacta) para saber que voy a Eliminar, sino voy a la pagina principal de Eliminar.
###

if (! $codacta)
{
	$url= '/sace/eliminar/';
	header ("Location: $url");
	exit;
}

###
### Los Clasicos Includes
###
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");


###
### Si me estan pasando el (codacta) es por que debe de existir, verifico en la DB 
### que exista como tal, sino voy a la pagina principal de Eliminar.
###

$sqlcmd = "SELECT count(*) as cantidad_actas_encontrada "
		. "FROM registro_actas "
		. "WHERE codacta='$codacta' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$cantidad_actas_encontrada = $registro->cantidad_actas_encontrada;
}


if ($cantidad_actas_encontrada <= 0)
{
	$url= '/sace/eliminar/';
	header ("Location: $url");
	exit;
}


###
### El Acta existe como tal, asi que antes de Eliminarla, copio sus Datos a la Tabla "eliminaciones_actas"
### Si del Acta existen Notas, copio las Notas a la Tabla "eliminaciones_notas" antes de Borrarlas
###


### Busco los Datos que tenga el Acta

$sqlcmd = "SELECT codcohorte, codasig, codacta, cedula_profesor, fecha_aprobacion, fecha_creacion, fecha_modificacion, "
		. "operador_creacion, operador_modificacion, host_creacion, host_modificacion "
		. "FROM registro_actas "
		. "WHERE codacta='$codacta' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{

		$codcohorte = $registro->codcohorte;
		$codasig = $registro->codasig;
		$codacta = $registro->codacta;
		$cedula_profesor = $registro->cedula_profesor;
		$fecha_aprobacion = $registro->fecha_aprobacion;
		$fecha_creacion  = $registro->fecha_creacion;
		$fecha_modificacion = $registro->fecha_modificacion;
		$operador_creacion = $registro->operador_creacion;
		$operador_modificacion = $registro->operador_modificacion;
		$host_creacion= $registro->host_creacion;
		$host_modificacion = $registro->host_modificacion;



		### Inserto los Datos del Acta en la Tabla, para tener un Respaldo de la Informacion que se va a Eliminar
		
		$sqlcmd2 = "INSERT INTO eliminaciones_actas (codcohorte, codasig, codacta, cedula_profesor, fecha_aprobacion, fecha_creacion, "
				 . "fecha_modificacion, operador_creacion, operador_modificacion, host_creacion, host_modificacion, fecha_eliminacion, "
				 . "operador_eliminacion, host_eliminacion) VALUES ("
				 . "'$codcohorte', '$codasig', '$codacta', '$cedula_profesor', '$fecha_aprobacion', '$fecha_creacion', "
				 . "'$fecha_modificacion', '$operador_creacion', '$operador_modificacion', '$host_creacion', '$host_modificacion', NOW(), "
				 . "'$PHP_AUTH_USER', '$REMOTE_ADDR') ";
	
		$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");



		### Busco las Notas del Acta que voy a Eliminar

		$sqlcmd3 = "SELECT cedula, calificacion, codeq, fecha_creacion, fecha_modificacion, operador_creacion, "
				 . "operador_modificacion, host_creacion, host_modificacion "
				 . "FROM record_notas "
				 . "WHERE codacta='$codacta' ";
		
		$query3 = mysql_db_query(DB_DATABASE,"$sqlcmd3");
		
		while ($registro3 = mysql_fetch_object($query3))
		{
			$cedula_rn = $registro3->cedula;
			$calificacion_rn = $registro3->calificacion;
			$codeq_rn = $registro3->codeq;
			$fecha_creacion_rn = $registro3->fecha_creacion;
			$fecha_modificacion_rn = $registro3->fecha_modificacion;
			$operador_creacion_rn = $registro3->operador_creacion;
			$operador_modificacion_rn = $registro3->operador_modificacion;
			$host_creacion_rn = $registro3->host_creacion;
			$host_modificacion_rn = $registro3->host_modificacion;


			### Inserto las Notas en la Tabla, para tener un Respaldo de la Informacion que se va a Eliminar

			$sqlcmd4 = "INSERT INTO eliminaciones_notas (codacta, cedula, calificacion, codeq, fecha_creacion, fecha_modificacion, "
					 . "operador_creacion, operador_modificacion, host_creacion, host_modificacion, "
					 . "fecha_eliminacion, operador_eliminacion, host_eliminacion) VALUES ("

					 . "'$codacta', '$cedula_rn', '$calificacion_rn', '$codeq_rn', '$fecha_creacion_rn', '$fecha_modificacion_rn', "
					 . "'$operador_creacion_rn', '$operador_modificacion_rn', '$host_creacion_rn', '$host_modificacion_rn', "
					 . "NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR') ";

			$query4 = mysql_db_query(DB_DATABASE,"$sqlcmd4");

		}

}




###
### Si existe el Acta la Elimino, y Todas sus Notas
###

if ($cantidad_actas_encontrada > 0)
{

	### Elimino el Acta como tal
	
	$sqlcmd = "DELETE FROM registro_actas "
			. "WHERE codacta='$codacta' ";

	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");	



	### Elimino las Notas que Contenga el Acta

	$sqlcmd = "DELETE FROM record_notas "
			. "WHERE codacta='$codacta' ";

	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

}
?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.3 - MacOS X">
</HEAD>

<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#0000FF" VLINK="#0000FF">

<CENTER>

<?
	include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/encabezado.php");
?>

<BR><BR>

<FONT FACE="Verdana,Arial,Geneva">
	Eliminar una Acta (y todo su Contenido)
</FONT>

<BR><BR><BR>


<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="center" VALIGN="top">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#000099">
			<B>El Acta y sus Notas han sido<BR>
			Eliminadas Satisfactoriamente.</B>
		</FONT>
		
		<BR><BR><BR><BR>
		
		<FONT FACE="Verdana,Arial,Geneva">
			Presione <A HREF="/sace/eliminar/">Aqu&iacute;</A> para Continuar.
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
