<?
###
###		Este script Elimina una Cohorte seleccionada, las Actas y 
###		Multiactas en ella, y todas las notas que contengan.
###

###
### Debe haber un (codcohorte) para saber que voy a Eliminar, sino voy a la pagina principal de Eliminar.
###

if (! $codcohorte)
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
### Si me estan pasando el (codcohorte) es por que debe de existir, verifico en la DB 
### que exista como tal, sino voy a la pagina principal de Eliminar.
###

$sqlcmd = "SELECT count(*) as cantidad_cohorte_encontrada "
		. "FROM cohortes "
		. "WHERE codcohorte='$codcohorte' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$cantidad_cohorte_encontrada = $registro->cantidad_cohorte_encontrada;
}


if ($cantidad_cohorte_encontrada <= 0)
{
	$url= '/sace/eliminar/';
	header ("Location: $url");
	exit;
}


###
### La Cohorte existe como tal, asi que antes de Eliminarla, copio sus Datos a la Tabla "eliminaciones_cohortes"
###

$sqlcmd = "SELECT codsede, codopest, codcohorte, fecha_inicio, periodo_lectivo, fecha_creacion, fecha_modificacion, "
		. "operador_creacion, operador_modificacion, host_creacion, host_modificacion "
		. "FROM cohortes "
		. "WHERE codcohorte='$codcohorte' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$codsede = $registro->codsede;
	$codopest = $registro->codopest;
	$codcohorte = $registro->codcohorte;
	$fecha_inicio = $registro->fecha_inicio;
	$periodo_lectivo = $registro->periodo_lectivo;
	$fecha_creacion = $registro->fecha_creacion;
	$fecha_modificacion = $registro->fecha_modificacion;
	$operador_creacion = $registro->operador_creacion;
	$operador_modificacion = $registro->operador_modificacion;
	$host_creacion = $registro->host_creacion;
	$host_modificacion = $registro->host_modificacion;
}

$sqlcmd = "INSERT INTO eliminaciones_cohortes (codsede, codopest, codcohorte, fecha_inicio, periodo_lectivo, fecha_creacion, "
		. "fecha_modificacion, operador_creacion, operador_modificacion, host_creacion, host_modificacion, fecha_eliminacion, "
		. "operador_eliminacion, host_eliminacion) VALUES ("
		. "'$codsede', '$codopest', '$codcohorte', '$fecha_inicio', '$periodo_lectivo', '$fecha_creacion', "
		. "'$fecha_modificacion', '$operador_creacion', '$operador_modificacion', '$host_creacion', '$host_modificacion', NOW(), "
		. "'$PHP_AUTH_USER', '$REMOTE_ADDR') ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");



###
### La Cohorte pudiera contener Actas y/o Multiactas, asi que verifico si existen, para luego Copiarlas y despues Eliminarlas.
###


### Verifico si existen Actas para dicha Cohorte

$sqlcmd = "SELECT count(*) as cantidad_actas_encontrada "
		. "FROM registro_actas "
		. "WHERE codcohorte='$codcohorte' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$cantidad_actas_encontrada = $registro->cantidad_actas_encontrada;
}

if ($cantidad_actas_encontrada > 0)		$existen_actas = 1;



### Verifico si existen Multiactas para dicha Cohorte

$sqlcmd = "SELECT count(*) as cantidad_multiactas_encontrada "
		. "FROM multiactas "
		. "WHERE codcohorte='$codcohorte' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$cantidad_multiactas_encontrada = $registro->cantidad_multiactas_encontrada;
}

if ($cantidad_multiactas_encontrada > 0)		$existen_multiactas = 1;



###
### Si Existen Actas, antes de Eliminarlas, copio sus Datos a la Tabla "eliminaciones_actas"
### Si de esas Actas existen Notas, copio las Notas a la Tabla "eliminaciones_notas" antes de Borrarlas
###

if ($existen_actas)
{

		### Busco las Actas que Existan para la Cohorte determinada
		
		$sqlcmd = "SELECT codcohorte, codasig, codacta, cedula_profesor, fecha_aprobacion, fecha_creacion, fecha_modificacion, "
				. "operador_creacion, operador_modificacion, host_creacion, host_modificacion "
				. "FROM registro_actas "
				. "WHERE codcohorte='$codcohorte' ";

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

}



###
### Si Existen Multiactas, antes de Eliminarlas, copio sus Datos a la Tabla "eliminaciones_multiactas"
### Si de esas Multiactas existen Notas, copio las Notas a la Tabla "eliminaciones_notas" antes de Borrarlas
###

if ($existen_multiactas)
{

		### Busco las Multiactas que Existan para la Cohorte determinada

		$sqlcmd = "SELECT mid, codcohorte, codasig, codacta, cedula_profesor1, cedula_profesor2, cedula_profesor3, cedula_profesor4, cedula_profesor5, "
				. "fecha_aprobacion, fecha_creacion, fecha_modificacion, operador_creacion, operador_modificacion, host_creacion, host_modificacion "
				. "FROM multiactas "
				. "WHERE codcohorte='$codcohorte' ";

		$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
		
		while ($registro = mysql_fetch_object($query))
		{
				$mid = $registro->mid;
				$codcohorte = $registro->codcohorte;
				$codasig = $registro->codasig;
				$codacta = $registro->codacta;
				
				$cedula_profesor1 = $registro->cedula_profesor1;
				$cedula_profesor2 = $registro->cedula_profesor2;
				$cedula_profesor3 = $registro->cedula_profesor3;
				$cedula_profesor4 = $registro->cedula_profesor4;
				$cedula_profesor5 = $registro->cedula_profesor5;
				
				$fecha_aprobacion = $registro->fecha_aprobacion;
				$fecha_creacion  = $registro->fecha_creacion;
				$fecha_modificacion = $registro->fecha_modificacion;
				$operador_creacion = $registro->operador_creacion;
				$operador_modificacion = $registro->operador_modificacion;
				$host_creacion= $registro->host_creacion;
				$host_modificacion = $registro->host_modificacion;



				### Inserto los Datos de la Multiacta en la Tabla, para tener un Respaldo de la Informacion que se va a Eliminar

				$sqlcmd2 = "INSERT INTO eliminaciones_multiactas (mid, codcohorte, codasig, codacta, cedula_profesor1, cedula_profesor2, cedula_profesor3, "
						 . "cedula_profesor4, cedula_profesor5, fecha_aprobacion, fecha_creacion, fecha_modificacion, operador_creacion, "
						 . "operador_modificacion, host_creacion, host_modificacion, fecha_eliminacion, operador_eliminacion, host_eliminacion) VALUES ("
						 . "'$mid', '$codcohorte', '$codasig', '$codacta', '$cedula_profesor1', '$cedula_profesor2', '$cedula_profesor3', "
						 . "'$cedula_profesor4', '$cedula_profesor5', '$fecha_aprobacion', '$fecha_creacion', '$fecha_modificacion', '$operador_creacion', "
						 . "'$operador_modificacion', '$host_creacion', '$host_modificacion', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR') ";
			
				$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");



				### Busco las Notas de la Multiacta que voy a Eliminar
	
				$sqlcmd3 = "SELECT cedula, calificacion, codeq, fecha_creacion, fecha_modificacion, operador_creacion, "
						 . "operador_modificacion, host_creacion, host_modificacion, mid "
						 . "FROM record_notas "
						 . "WHERE codacta='$codacta' AND mid='$mid' ";

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
					$mid_rn = $registro3->mid;


					### Inserto las Notas en la Tabla, para tener un Respaldo de la Informacion que se va a Eliminar
	
					$sqlcmd4 = "INSERT INTO eliminaciones_notas (codacta, cedula, calificacion, codeq, fecha_creacion, fecha_modificacion, "
							 . "operador_creacion, operador_modificacion, host_creacion, host_modificacion, mid, "
							 . "fecha_eliminacion, operador_eliminacion, host_eliminacion) VALUES ("

							 . "'$codacta', '$cedula_rn', '$calificacion_rn', '$codeq_rn', '$fecha_creacion_rn', '$fecha_modificacion_rn', "
							 . "'$operador_creacion_rn', '$operador_modificacion_rn', '$host_creacion_rn', '$host_modificacion_rn', '$mid_rn', "
							 . "NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR') ";

					$query4 = mysql_db_query(DB_DATABASE,"$sqlcmd4");

				}

		}

}



###
### Elimino la Cohorte, sus Actas, Multiactas y Todas sus Notas
###

$sqlcmd = "DELETE FROM cohortes "
		. "WHERE codcohorte='$codcohorte' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");



###
### Si existen Actas que pertenecen a la Cohorte, las Elimino y Todas sus Notas
###

if ($existen_actas)
{
		### Busco todas las Actas que voy a Borrar
	
		$sqlcmd = "SELECT codacta "
				. "FROM registro_actas "
				. "WHERE codcohorte='$codcohorte' ";
		
		$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
		
		while ($registro = mysql_fetch_object($query))
		{
				$codacta = $registro->codacta;


				### Voy Eliminando una a una, las Notas que pertenezcan al Acta

				$sqlcmd2 = "DELETE FROM record_notas "
						 . "WHERE codacta='$codacta' ";
		
				$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");
	
		}
		
		
		### Elimino las Actas de la Cohorte
		
		$sqlcmd = "DELETE FROM registro_actas "
				. "WHERE codcohorte='$codcohorte' ";

		$query = mysql_db_query(DB_DATABASE,"$sqlcmd");	

}



###
### Si existen Multiactas que pertenecen a la Cohorte, las Elimino y Todas sus Notas
###

if ($existen_multiactas)
{
		### Busco todas las Multiactas que voy a Borrar
	
		$sqlcmd = "SELECT mid, codacta "
				. "FROM multiactas "
				. "WHERE codcohorte='$codcohorte' ";
		
		$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
		
		while ($registro = mysql_fetch_object($query))
		{
				$mid = $registro->mid;
				$codacta = $registro->codacta;


				### Voy Eliminando una a una, las Notas que pertenezcan al Acta

				$sqlcmd2 = "DELETE FROM record_notas "
						 . "WHERE codacta='$codacta' AND mid='$mid' ";
		
				$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");
	
		}
		
		
		### Elimino las Multiactas de la Cohorte
		
		$sqlcmd = "DELETE FROM multiactas "
				. "WHERE codcohorte='$codcohorte' ";

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
	Eliminar una Cohorte (y todo su Contenido)
</FONT>

<BR><BR><BR>


<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="center" VALIGN="top">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#000099">
			<B>La Cohorte, sus Actas y sus Notas han sido<BR>
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
