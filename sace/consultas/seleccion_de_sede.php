<?
###
###		Este script desplega el Listado de las Sedes o Nucleos existentes para que 
###		el Usuario seleccione con cual va a Trabajar.
###


###
### Dependiendo de la Opcion seleccionada, paso el Codigo de la Sede (codsede)
###

if ( ($seleccionar) AND ($_codsede) )
{
	$url= 'generar_archivo_por_sede.php?_codsede=' . $_codsede;
	header ("Location: $url");
	exit;
}


###
### Los Clasicos Includes
###
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");
?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#00CC00" VLINK="#CC0000">

<CENTER>

<?
	include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/encabezado.php");
?>


<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
	<TD WIDTH="100%" ALIGN="left" VALIGN="top">
	
		<A HREF="../"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="ingreso_de_cedula.php"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Consultar Notas de un Estudiante</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n de Sede</B></FONT>

	</TD>
</TR>
</TABLE>


<BR><BR>


<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
	<B>Seleccione la Sede o el Nucleo para generar Reporte de los Alumnos</B><BR>
	Este reporte ser&aacute; por Sede o Nucle, y tendr&aacute; El nombre y apellido del Alumno,<BR>
	C&eacute;dula de Identidad, Indice Acad&eacute;mico, Ciudad, Cohorte, Periodo, etc.</B> 
</FONT>

<BR><BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
	<B>Este proceso tarda varios minutos, Usted debe esperar hasta que el<BR>
	Navegador (Browser) indique que hacer con el archivo llamado "alumnos.csv"</B> 
</FONT>


<BR><BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#CC0000">
	<B>Presione el Boton de Generar una Sola Vez, no haga double click !</B> 
</FONT>


<BR><BR>

<TABLE BORDER="0" WIDTH="570" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
<TR>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Ciudad</B>
		</FONT>
	</TD>
	<TD WIDTH="170" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Estado o Provincia</B>
		</FONT>
	</TD>
	<TD WIDTH="130" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Modalidad</B>
		</FONT>
	</TD>
	<TD WIDTH="120" ALIGN="left" VALIGN="top">
		<P> </P>
	</TD>
</TR>
<?

###
###	Realizo el Query en la Base de Datos para ver cuales son las Sedes o Nucleos que se deben mostrar
###

	$sqlcmd = "SELECT codsede, modalidad, ciudad, edo_prov "
			. "FROM directorio_cippsv "
			. "ORDER BY ciudad ";
	
	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

	while ($registro = mysql_fetch_object($query))
	{
		$codsede = $registro->codsede;
		$modalidad = $registro->modalidad;
		$ciudad = $registro->ciudad;
		$edo_prov = $registro->edo_prov;
?>
		<TR>
			<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $ciudad ?>
				</FONT>
			</TD>
			<TD WIDTH="170" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $edo_prov ?>
				</FONT>
			</TD>
			<TD WIDTH="130" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $modalidad ?>
				</FONT>
			</TD>
			<TD WIDTH="120" ALIGN="center" VALIGN="top" BGCOLOR="#FFFFFF">
				<A HREF="generar_archivo_por_sede.php?_codsede=<? echo $codsede ?>">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#CC0000"><B>Gererar</B></FONT></A>
			</TD>
		</TR>
<?
	}
?>
</TABLE>

<BR><BR>


<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
