<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");


if ( ($continuar) OR ($continuar_x) )
{

	if (! ereg ("^[0-9]+$", $cedula) )
	{
		header ("Location: ingreso_de_cedula.php");
		exit;
	
	} else {
	
		header ("Location: eliminar_notas_estudiante.php?cedula=$cedula");
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


<?
	include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/encabezado.php");
?>

<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
	<TD WIDTH="100%" ALIGN="left" VALIGN="top">
	
		<A HREF="../"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Eliminar Notas de un Estudiante</B></FONT>

	</TD>
</TR>
</TABLE>

<BR>

<FONT FACE="Verdana,Arial,Geneva">
	Eliminar TODAS las Notas de un Estudiante
</FONT>

<BR><BR><BR>

<?
		if ($error == 1)
		{
?>
			<FONT SIZE="-1" COLOR="#FF0000" FACE="Verdana,Arial,Geneva">
				<B>Para la C&eacute;dula de Identidad Ingresada,<BR>
				No se encontraron Notas asociadas.<BR><BR>
				Favor revisar la C&eacute;dula de Identidad suministrada.</B><BR><BR>
			</FONT>

<?
		}
?>


<FORM ACTION="ingreso_de_cedula.php" METHOD="POST">

<TABLE BORDER="0" CELLSPACING="5" CELLPADDING="7">
<TR>
	<TD ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			Ingrese la C&eacute;dula de Identidad del Estudiante
		</FONT>
	</TD>
	<TD ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="cedula" VALUE="<? echo $cedula ?>" SIZE="12" MAXLENGTH="8">
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
