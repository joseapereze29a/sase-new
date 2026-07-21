<?
###
###		Este script Elimina una Nota de un Estudiante seleccionado.
###

###
### Debe haber un (codacta) y una (cedula) para saber que voy a Eliminar, sino voy a la pagina principal de Eliminar.
###

if ( (! $codacta) OR (! $cedula) )
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
### Si me estan pasando el (codacta) y la (cedula) es por que debe de existir, verifico en la DB 
### que exista como tal, sino voy a la pagina principal de Eliminar.
###


### Debo revisar, si esta por una Acta o por una Multiacta

if (! $mid)
{

	$sqlcmd = "SELECT count(*) as cantidad_notas_encontrada_acta "
			. "FROM record_notas "
			. "WHERE codacta='$codacta' AND cedula='$cedula' ";
	
	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
	while ($registro = mysql_fetch_object($query))
	{
		$cantidad_notas_encontrada_acta = $registro->cantidad_notas_encontrada_acta;
	}

}



if ($mid)
{

	$sqlcmd = "SELECT count(*) as cantidad_notas_encontrada_multiacta "
			. "FROM record_notas "
			. "WHERE codacta='$codacta' AND cedula='$cedula' AND mid='$mid' ";
	
	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
	while ($registro = mysql_fetch_object($query))
	{
		$cantidad_notas_encontrada_multiacta = $registro->cantidad_notas_encontrada_multiacta;
	}

}



if ( ($cantidad_notas_encontrada_acta <= 0) AND ($cantidad_notas_encontrada_multiacta <= 0) )
{
	$url= '/sace/eliminar/';
	header ("Location: $url");
	exit;
}



### Busco las Notas del Acta que voy a Eliminar, si es por Acta y NO por Multiacta

if ( ($cantidad_notas_encontrada_acta > 0) AND (! $mid) )
{

		$sqlcmd = "SELECT cedula, calificacion, codeq, fecha_creacion, fecha_modificacion, operador_creacion, "
				. "operador_modificacion, host_creacion, host_modificacion "
				. "FROM record_notas "
				. "WHERE codacta='$codacta' AND cedula='$cedula' ";
		
		$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
		
		while ($registro = mysql_fetch_object($query))
		{
			$cedula_rn = $registro->cedula;
			$calificacion_rn = $registro->calificacion;
			$codeq_rn = $registro->codeq;
			$fecha_creacion_rn = $registro->fecha_creacion;
			$fecha_modificacion_rn = $registro->fecha_modificacion;
			$operador_creacion_rn = $registro->operador_creacion;
			$operador_modificacion_rn = $registro->operador_modificacion;
			$host_creacion_rn = $registro->host_creacion;
			$host_modificacion_rn = $registro->host_modificacion;
		
		
			### Inserto las Notas en la Tabla, para tener un Respaldo de la Informacion que se va a Eliminar
		
			$sqlcmd2 = "INSERT INTO eliminaciones_notas (codacta, cedula, calificacion, codeq, fecha_creacion, fecha_modificacion, "
					 . "operador_creacion, operador_modificacion, host_creacion, host_modificacion, "
					 . "fecha_eliminacion, operador_eliminacion, host_eliminacion) VALUES ("
		
					 . "'$codacta', '$cedula_rn', '$calificacion_rn', '$codeq_rn', '$fecha_creacion_rn', '$fecha_modificacion_rn', "
					 . "'$operador_creacion_rn', '$operador_modificacion_rn', '$host_creacion_rn', '$host_modificacion_rn', "
					 . "NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR') ";
		
			$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");
		
		}

}


### Busco las Notas del Acta que voy a Eliminar, si es por Multiacta y NO por Acta

if ( ($cantidad_notas_encontrada_multiacta > 0) AND ($mid) )
{

		$sqlcmd = "SELECT cedula, calificacion, codeq, fecha_creacion, fecha_modificacion, operador_creacion, "
				. "operador_modificacion, host_creacion, host_modificacion, mid "
				. "FROM record_notas "
				. "WHERE codacta='$codacta' AND cedula='$cedula' AND mid='$mid' ";

		$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
		
		while ($registro = mysql_fetch_object($query))
		{
			$cedula_rn = $registro->cedula;
			$calificacion_rn = $registro->calificacion;
			$codeq_rn = $registro->codeq;
			$fecha_creacion_rn = $registro->fecha_creacion;
			$fecha_modificacion_rn = $registro->fecha_modificacion;
			$operador_creacion_rn = $registro->operador_creacion;
			$operador_modificacion_rn = $registro->operador_modificacion;
			$host_creacion_rn = $registro->host_creacion;
			$host_modificacion_rn = $registro->host_modificacion;
			$mid_rn = $registro->mid;


			### Inserto las Notas en la Tabla, para tener un Respaldo de la Informacion que se va a Eliminar

			$sqlcmd2 = "INSERT INTO eliminaciones_notas (codacta, cedula, calificacion, codeq, fecha_creacion, fecha_modificacion, "
					 . "operador_creacion, operador_modificacion, host_creacion, host_modificacion, mid, "
					 . "fecha_eliminacion, operador_eliminacion, host_eliminacion) VALUES ("

					 . "'$codacta', '$cedula_rn', '$calificacion_rn', '$codeq_rn', '$fecha_creacion_rn', '$fecha_modificacion_rn', "
					 . "'$operador_creacion_rn', '$operador_modificacion_rn', '$host_creacion_rn', '$host_modificacion_rn', '$mid_rn', "
					 . "NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR') ";

			$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

		}
		
}


###
### Si existe la Nota, la Elimino
###

if ( ($cantidad_notas_encontrada_acta > 0) AND (! $mid) )
{

	### Elimino la Nota del Acta como tal
	
	$sqlcmd = "DELETE FROM record_notas "
			. "WHERE codacta='$codacta' AND cedula='$cedula' ";

	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");	

}



if ( ($cantidad_notas_encontrada_multiacta > 0) AND ($mid) )
{

	### Elimino la Nota del Multiacta como tal
	
	$sqlcmd = "DELETE FROM record_notas "
			. "WHERE codacta='$codacta' AND cedula='$cedula' AND mid='$mid' ";

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
	Eliminar una Nota
</FONT>

<BR><BR><BR>


<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="center" VALIGN="top">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#000099">
			<B>La Nota ha sido Eliminada Satisfactoriamente.</B>
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
