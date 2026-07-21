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


<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
	<TD WIDTH="100%" ALIGN="left" VALIGN="top">
	
		<A HREF="../"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_de_sede.php"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n de Sede</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_postgrado.php?_codsede=<? echo $_codsede ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Postgrado</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_cohorte.php?_codsede=<? echo $_codsede ?>&_codopest=<? echo $_codopest ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Cohortes Existentes</B></FONT></A>
		
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Edici&oacute;n de Cohorte</B></FONT>
	</TD>
</TR>
</TABLE>


<BR>

<IMG SRC="/sace/imagenes/titulos_de_home/titulo_editar.jpg" ALT="" WIDTH="363" HEIGHT="21" BORDER="0">

<BR><BR><BR><BR><BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="center" VALIGN="top">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#000099">
			<B>Los Datos de la Cohorte han sido<BR>Editados Satisfactoriamente.</B>
		</FONT>
		
		<BR><BR><BR><BR>
		
		<FONT FACE="Verdana,Arial,Geneva">
			Presione <A HREF="seleccion_cohorte.php?_codsede=<? echo $_codsede ?>&_codopest=<? echo $_codopest ?>">Aqu&iacute;</A> para Continuar.
		</FONT>
	</TD>
</TR>
</TABLE>


</CENTER>

</BODY>
</HTML>
