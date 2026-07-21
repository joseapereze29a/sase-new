<?php
session_start();

include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/creditos.php");
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_fecha.php");
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/conexion.php");

// Verifica si se ha recibido el código de sede
if (empty($_SESSION['_codsede'])) {
    header("Location: seleccion_de_sede.php");
    exit;
}

$_codsede = $_SESSION['_codsede'];

// Prevenir SQL Injection con escape
$_codsede_esc = mysqli_real_escape_string($conexion, $_codsede);

// Buscar datos de la sede
$sql_sede = "SELECT ciudad, edo_prov FROM directorio_cippsv WHERE codsede='$_codsede_esc'";
$res_sede = mysqli_query($conexion, $sql_sede);

$ciudad = $edo_prov = '';
if ($res_sede && mysqli_num_rows($res_sede) > 0) {
    $row = mysqli_fetch_assoc($res_sede);
    $ciudad = $row['ciudad'];
    $edo_prov = $row['edo_prov'];
}
?>
<HTML>
<HEAD>
    <TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
    <META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#00CC00" VLINK="#CC0000">

<CENTER>

<?php include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/encabezado.php"); ?>

<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
    <TD WIDTH="100%" ALIGN="left" VALIGN="top">
        <A HREF="../"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>
        <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 
        <A HREF="seleccion_de_sede.php"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selección de Sede</B></FONT></A>
        <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 
        <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selección del Postgrado</B></FONT>
    </TD>
</TR>
</TABLE>

<BR>
<IMG SRC="/sace/imagenes/titulos_de_home/titulo_ingreso.jpg" ALT="" WIDTH="380" HEIGHT="20" BORDER="0">
<BR><BR><BR>

<FONT FACE="Verdana,Arial,Geneva" COLOR="#000099">
    <B><?= "$ciudad ($edo_prov)" ?></B> &nbsp;
</FONT>

<BR><BR><BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
    <B>Seleccione el Postgrado con el cual desea Trabajar</B>
</FONT>

<BR><BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
<TR>
    <TD WIDTH="330" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF"><B>Postgrado</B></FONT>
    </TD>
    <TD WIDTH="150" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF"><B>Tipo</B></FONT>
    </TD>
    <TD WIDTH="120" ALIGN="left" VALIGN="top"><P> </P></TD>
</TR>

<?php
$sql_postgrados = "SELECT codopest, tipo, mencion_especialidad 
                   FROM oportunidades_estudio 
                   WHERE codsede='$_codsede_esc' 
                   ORDER BY mencion_especialidad, tipo";

$res_postgrados = mysqli_query($conexion, $sql_postgrados);

if ($res_postgrados && mysqli_num_rows($res_postgrados) > 0) {
    while ($registro = mysqli_fetch_assoc($res_postgrados)) {
        $codopest = $registro['codopest'];
        $tipo = $registro['tipo'];
        $mencion = $registro['mencion_especialidad'];
        ?>
        <TR>
            <TD WIDTH="330" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
                <FONT SIZE="-1" FACE="Verdana,Arial,Geneva"><?= $mencion ?></FONT>
            </TD>
            <TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
                <FONT SIZE="-1" FACE="Verdana,Arial,Geneva"><?= $tipo ?></FONT>
            </TD>
            <FORM ACTION="seleccion_cohorte.php" METHOD="POST">
                <TD WIDTH="120" ALIGN="center" VALIGN="top" BGCOLOR="#FFFFFF">
                    <INPUT TYPE="submit" NAME="seleccionar" VALUE="Seleccionar">
                    <INPUT TYPE="hidden" NAME="_codsede" VALUE="<?= $_codsede ?>">
                    <INPUT TYPE="hidden" NAME="_codopest" VALUE="<?= $codopest ?>">
                </TD>
            </FORM>
        </TR>
        <?php
    }
}
?>
</TABLE>

<BR><BR>

<?php
// include($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>
</BODY>
</HTML>