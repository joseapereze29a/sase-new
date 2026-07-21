<?
###
###		Este script desplega el Listado de las Cohortes existentes para que 
###		el Usuario seleccione con cual va a Trabajar. Existe tambien la 
###		opcion de Crear una Nueva Cohorte.
###


###
### Los Clasicos Includes
###
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");


###
###	Busco en la Base de Datos, los campos necesarios para poder Desplegar la Informacion apropiada
###
$sqlcmd = "SELECT directorio_cippsv.modalidad, directorio_cippsv.ciudad, directorio_cippsv.edo_prov, "
		. "oportunidades_estudio.tipo, oportunidades_estudio.mencion_especialidad "
		. "FROM directorio_cippsv, oportunidades_estudio "
		. "WHERE oportunidades_estudio.codsede='$_codsede' AND oportunidades_estudio.codopest='$_codopest' AND "
		. "directorio_cippsv.codsede=oportunidades_estudio.codsede ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$modalidad = $registro->modalidad;
	$ciudad = $registro->ciudad;
	$edo_prov = $registro->edo_prov;
	$tipo = $registro->tipo;
	$mencion_especialidad = $registro->mencion_especialidad;
}
?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#0000FF" VLINK="#0000FF">

<CENTER>

<?
	include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/encabezado.php");
?>


<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
	<TD WIDTH="100%" ALIGN="left" VALIGN="top">
	
		<A HREF="../"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_de_sede.php"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n de Sede</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_postgrado.php?_codsede=<? echo $_codsede ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Postgrado</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Cohortes Existentes</B></FONT>
	</TD>
</TR>
</TABLE>


<BR>

<FONT FACE="Verdana,Arial,Geneva">
	Eliminar una Cohorte (y todo su Contenido)
</FONT>

<BR><BR><BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Informaci&oacute;n sobre el Postgrado</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Ciudad</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Estado o Provincia</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Modalidad</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $ciudad ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $edo_prov ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $modalidad ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Menci&oacute;n o Especialidad</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Tipo</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $mencion_especialidad ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $tipo ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR><BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
	<B>Las Notas y Actas que conformen la Cohorte,<BR>
	como la Cohorte misma; ser&aacute;n Eliminadas.</B>
</FONT>

<BR><BR>


<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
<TR>
	<TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>N&uacute;m.</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Cohorte</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Periodo Lectivo</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="right" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Fecha de Inicio</B>
		</FONT>
	</TD>
</TR>
<?

###
###	Realizo el Query en la Base de Datos para ver cuales son las Cohortes que se deben mostrar
###

	$sqlcmd = "SELECT codcohorte, fecha_inicio, periodo_lectivo "
			. "FROM cohortes "
			. "WHERE codsede='$_codsede' AND codopest='$_codopest' "
			. "ORDER BY fecha_inicio ";
	
	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
	while ($registro = mysql_fetch_object($query))
	{
		$codcohorte = $registro->codcohorte;
		$fecha_inicio = $registro->fecha_inicio;
		$periodo_lectivo = $registro->periodo_lectivo;
		
		$pase_por_aqui = 1;
	
		$contador++;
	
		if ( ($fecha_inicio == '0000-00-00') OR ($fecha_inicio == "") )
		{
			$fecha_inicio = "";
		
		} else {
		
			$fecha_inicio = fecha($fecha_inicio);
		}
	
	
		if ($bg_celda == '#CCCCCC')
		{
			$bg_celda = '#FFFFFF';
		} else {
			$bg_celda = '#CCCCCC';
		}
?>
		<TR>
			<TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $contador ?>
				</FONT>
			</TD>
			<TD WIDTH="150" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
				<A HREF="eliminar_cohorte.php?codcohorte=<? echo $codcohorte ?>">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FF0000"><B>ELIMINAR</B></FONT></A>
			</TD>
			<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $periodo_lectivo ?>
				</FONT>
			</TD>
			<TD WIDTH="200" ALIGN="right" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $fecha_inicio ?>
				</FONT>
			</TD>
		</TR>
<?
	}
?>
</TABLE>


<?
	if (! $pase_por_aqui):
?>

		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<BR><BR>
			<B>No se encontraron Cohortes existentes para el Postgrado seleccionado.</B>
		</FONT>

<?
	endif;
?>
		

<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
