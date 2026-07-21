<?
	include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");
?>
<HTML>
<HEAD>
	<TITLE>Sistema Automatizado de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.3 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#0000FF" VLINK="#0000FF">

<CENTER>

<?
	include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/encabezado.php");
?>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="0" CELLPADDING="0">
<TR>
	<TD WIDTH="600" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Las Cohortes, Actas y/o Notas que han sido Eliminadas,<BR>
			No podran recuperarse,<BR>
			Tendran que ser Ingresadas nuevamente.</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR><BR>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1">
<TR>
	<TD ALIGN="left" VALIGN="top">
		<FONT FACE="Verdana,Arial,Geneva">
			<B>&bull; <A HREF="acta_multiacta/seleccion_de_sede.php">Eliminar una Acta</A> (y todo su Contenido)<BR><BR><BR>
			
			
			&bull; <A HREF="notas/seleccion_de_sede.php">Eliminar una Nota</A><BR><BR><BR>
			
			
			&bull; <A HREF="estudiante/ingreso_de_cedula.php">Eliminar TODAS las Notas de un Estudiante</A></B><BR><BR><BR><BR>
		</FONT>

		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>&bull; <A HREF="cohorte/seleccion_de_sede.php">Eliminar una Cohorte</A> (y todo su Contenido)</B><BR><BR><BR>
		</FONT>	
	</TD>
</TR>
</TABLE>

<BR><BR><BR>

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

