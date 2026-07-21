<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");


if ( ($continuar) OR ($continuar_x) )
{

	if (! ereg ("^[0-9]+$", $cedula) )
	{
		header ("Location: ingreso_de_cedula.php");
		exit;
	
	} else {
	
		header ("Location: revision_de_cedula.php?cedula=$cedula");
		exit;
	}

}
?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>

<script language="JavaScript">
<!--
function popup( windowname, url, w, h )
{
	popupwin = window.open( "", windowname, "toolbar=no,location=no,directories=no,status=no,menubar=no,width="+ w +",height=" + h + ",resizable=1,scrollbars=1" );
	popupwin.location = url;
}
//-->
</script>

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

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Datos Personales de un Estudiante</B></FONT>

	</TD>
</TR>
</TABLE>

<?
	#include ("$DOCUMENT_ROOT/includes/encabezado.php");
?>

<BR>

<IMG SRC="/sace/imagenes/titulos_de_home/titulo_datos_personales.jpg" ALT="" WIDTH="384" HEIGHT="19" BORDER="0">

<BR><BR>

<FORM ACTION="ingreso_de_cedula.php" METHOD="POST">

<TABLE BORDER="0" CELLSPACING="5" CELLPADDING="7">
<TR>
	<TD ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			Ingrese la C&eacute;dula de Identidad del Estudiante
		</FONT>
	</TD>
	<TD ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="cedula" VALUE="" SIZE="12" MAXLENGTH="8">
	</TD>
	<TD ALIGN="left" VALIGN="top">
		<INPUT TYPE="submit" NAME="continuar" VALUE="Continuar">
		<INPUT TYPE="hidden" NAME="continuar_x" VALUE="Continuar">
	</TD>
</TR>
</TABLE>

<BR><BR><BR><BR>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="600" BGCOLOR="#000099">
<TR>
	<TD ALIGN="left" VALIGN="top" WIDTH="600" BGCOLOR="#FFFFFF">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			El N&uacute;mero de C&eacute;dula de Identidad, No debe tener car&aacute;cteres especiales como: 
			Puntos, Comas o Letras. <B>Solo debe contener n&uacute;meros.</B>
		</FONT>
	</TD>
</TR>
</TABLE>
		
</FORM>

<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
