<?
session_start();
//echo $_SESSION['account_rol'];
// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: index.php');
    exit;
}
	//include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");
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
<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
	<TD WIDTH="100%" ALIGN="left" VALIGN="top">
	
		<A HREF=""><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>

	</TD>
</TR>
</TABLE>
<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="0" CELLPADDING="0">
<TR>
	<TD WIDTH="600" ALIGN="center" VALIGN="top">
		<IMG SRC="/sace/imagenes/titulo_seleccione_una_op.jpg" ALT="" WIDTH="600" HEIGHT="35" BORDER="0">
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="10">
<TR>
	<TD WIDTH="100" ALIGN="center" VALIGN="top">
		<A HREF="consultas/ingreso_de_cedula.php"><IMG SRC="imagenes/lupa.jpg" ALT="" WIDTH="65" HEIGHT="63" BORDER="0"></A>
	</TD>
	<TD WIDTH="500" ALIGN="left" VALIGN="middle">
		<A HREF="consultas/ingreso_de_cedula.php"><IMG SRC="imagenes/titulos_de_home/tiutlo_consulta_ci.jpg" ALT="" WIDTH="451" HEIGHT="20" BORDER="0"></A>
	</TD>
</TR>
<TR>
	<TD WIDTH="100" ALIGN="center" VALIGN="top">
		<A HREF="datos_personales/ingreso_de_cedula.php"><IMG SRC="imagenes/usuario.jpg" ALT="" WIDTH="65" HEIGHT="63" BORDER="0"></A>
	</TD>
	<TD WIDTH="500" ALIGN="left" VALIGN="middle">
		<A HREF="datos_personales/ingreso_de_cedula.php"><IMG SRC="imagenes/titulos_de_home/titulo_datos_personales.jpg" ALT="" WIDTH="384" HEIGHT="19" BORDER="0"></A>
	</TD>
</TR>
<TR>
	<TD WIDTH="100" ALIGN="center" VALIGN="top">
		<A HREF="ingresos/seleccion_de_sede.php"><IMG SRC="imagenes/caja_y_flecha.jpg" ALT="" WIDTH="65" HEIGHT="64" BORDER="0"></A>
	</TD>
	<TD WIDTH="500" ALIGN="left" VALIGN="middle">
		<A HREF="ingresos/seleccion_de_sede.php"><IMG SRC="imagenes/titulos_de_home/titulo_ingreso.jpg" ALT="" WIDTH="380" HEIGHT="20" BORDER="0"></A>
	</TD>
</TR>
<TR>
	<TD WIDTH="100" ALIGN="center" VALIGN="top">
		<A HREF="ediciones/seleccion_de_sede.php"><IMG SRC="imagenes/archivero.jpg" ALT="" WIDTH="65" HEIGHT="63" BORDER="0"></A>
	</TD>
	<TD WIDTH="500" ALIGN="left" VALIGN="middle">
		<A HREF="ediciones/seleccion_de_sede.php"><IMG SRC="imagenes/titulos_de_home/titulo_editar.jpg" ALT="" WIDTH="363" HEIGHT="21" BORDER="0"></A>
	</TD>
</TR>
<TR>
	<TD WIDTH="100" ALIGN="center" VALIGN="top">
		<A HREF="herramientas_sistema.php"><IMG SRC="imagenes/herramientas.jpg" ALT="" WIDTH="65" HEIGHT="64" BORDER="0"></A>
	</TD>
	<TD WIDTH="500" ALIGN="left" VALIGN="middle">
		<A HREF="herramientas_sistema.php"><IMG SRC="imagenes/titulos_de_home/titulo_herramientas.jpg" ALT="" WIDTH="451" HEIGHT="24" BORDER="0"></A>
	</TD>
</TR>
</TABLE>


<BR>


<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
