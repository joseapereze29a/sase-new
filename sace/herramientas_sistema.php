<?
session_start();
if ($_SESSION['account_rol'] !== '1') {
    exit("Acceso denegado: no tienes permisos suficientes.");
}

	include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");
?>
<HTML>
<HEAD>
	<TITLE>Sistema Automatizado de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#0000FF" VLINK="#0000FF">

<CENTER>

<?
	include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/encabezado.php");
?>

<BR><BR>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1">
<TR>
	<TD ALIGN="left" VALIGN="top">
		<FONT FACE="Verdana,Arial,Geneva">
			<B>&bull; <A HREF="reportes/seleccion_de_sede.php">Reportes Estad&iacute;sticos</A><BR><BR><BR>
		
			&bull; <A HREF="profesores/">Manejo de Profesores</A><BR><BR><BR>

			&bull; <A HREF="profesores/">Manejo de Estudiantes</A><BR><BR><BR>
			
			<CENTER>
				<HR SIZE="1" WIDTH="200">
			</CENTER>
			
			<BR>

			&bull; <A HREF="eliminar/">Eliminar Cohortes, Actas, y/o Notas</A><BR><BR><BR>

			&bull; <A HREF="ingresos/oportunidad_de_estudio/seleccion_de_sede.php">Administrar Oportunidad de Estudio</A><BR><BR><BR>

			&bull; <A HREF="eliminar/">Administrar Pensun de Estudio</A><BR><BR><BR>
			
			&bull; <A HREF="accesos/">Privilegios de Acceso</A></B><BR><BR><BR>
		</FONT>
	</TD>
</TR>
</TABLE>


<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
Ir al <A HREF="/sace/">Home</A>
</FONT>


<BR>


<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
