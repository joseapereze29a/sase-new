<?php
session_start();
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");
?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.3 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#0000FF" VLINK="#0000FF">

<CENTER>

<?php include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/encabezado.php"); ?>

<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
	<TD WIDTH="100%" ALIGN="left" VALIGN="top">
		<A HREF="../"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n de Sede</B></FONT>
	</TD>
</TR>
</TABLE>

<BR><BR>

<FONT FACE="Verdana,Arial,Geneva" COLOR="#000099">
<B>Seleccione la Sede</B>
</FONT>

<BR><BR><BR>
<TABLE>
<TR>
	<TD ALIGN="center" VALIGN="top" COLSPAN="2">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
        <A HREF="ingresar_oportunidad_estudio.php?codsede=<?php echo 'todas'; ?>"><?php echo 'SELECCIONAR TODAS'; ?></A>
        </FONT>
	</TD>
</TR>
</TABLE>    

<TABLE BORDER="0" WIDTH="400" CELLSPACING="1" CELLPADDING="3" BGCOLOR="#000099">
<TR>
	<TD WIDTH="80" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF"><B>N&uacute;m.</B></FONT>
	</TD>
	<TD WIDTH="170" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF"><B>Ciudad</B></FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF"><B>Estado o Provincia</B></FONT>
	</TD>
</TR>
<?php

$sqlcmd = "SELECT codsede, modalidad, ciudad, edo_prov FROM directorio_cippsv ORDER BY ciudad";
$query = mysqli_query($conexion, $sqlcmd);

$contador = 0;
if ($query) {
	while ($registro = mysqli_fetch_object($query)) {
		$contador++;
		$codsede = $registro->codsede;
		$ciudad = $registro->ciudad;
		$edo_prov = $registro->edo_prov;
?>
		<TR>
			<TD WIDTH="80" ALIGN="center" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva"><?php echo $contador; ?></FONT>
			</TD>
			<TD WIDTH="170" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<A HREF="ingresar_oportunidad_estudio.php?codsede=<?php echo $codsede; ?>"><?php echo $ciudad; ?></A>
				</FONT>
			</TD>
			<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva"><?php echo $edo_prov; ?></FONT>
			</TD>
		</TR>
<?php
	}
} else {
	echo "<tr><td colspan='3' bgcolor='#FFFFFF' align='center'><font color='red'>Error al consultar las sedes.</font></td></tr>";
}
?>
</TABLE>

<BR><BR><BR>

<?php #include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php"); ?>

</CENTER>

</BODY>
</HTML>