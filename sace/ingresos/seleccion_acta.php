<?
session_start();
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_datos_profesor.php");

$_codcohorte=$_GET['_codcohorte'];

$sqlcmd = "
    SELECT 
        d.modalidad, 
        d.ciudad, 
        d.edo_prov, 
        o.tipo, 
        o.mencion_especialidad, 
        c.fecha_inicio, 
        c.codsede, 
        c.codopest
    FROM cohortes c
    INNER JOIN oportunidades_estudio o ON c.codsede = o.codsede AND c.codopest = o.codopest
    INNER JOIN directorio_cippsv d ON o.codsede = d.codsede
    WHERE c.codcohorte = '$_codcohorte'
";

// Ejecutar la consulta
$query = mysqli_query($conexion, $sqlcmd);
if (!$query) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

// Extraer los datos
if ($registro = mysqli_fetch_object($query)) {
    $modalidad = $registro->modalidad;
    $ciudad = $registro->ciudad;
    $edo_prov = $registro->edo_prov;
    $tipo = $registro->tipo;
    $mencion_especialidad = $registro->mencion_especialidad;
    $fecha_inicio = $registro->fecha_inicio;

    $_codsede_menu = $registro->codsede;
    $_codopest_menu = $registro->codopest;
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

		<A HREF="seleccion_postgrado.php?_codsede=<? echo $_codsede_menu ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Postgrado</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_cohorte.php?_codsede=<? echo $_codsede_menu ?>&_codopest=<? echo $_codopest_menu ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Cohortes Existentes</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Actas Existentes</B></FONT>
	</TD>
</TR>
</TABLE>

<BR>

<IMG SRC="/sace/imagenes/titulos_de_home/titulo_ingreso.jpg" ALT="" WIDTH="380" HEIGHT="20" BORDER="0">

<BR><BR><BR>


<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="710" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Informaci&oacute;n sobre el Postgrado</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="260" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Ciudad</B>
		</FONT>
	</TD>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
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
	<TD WIDTH="260" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $ciudad ?>
		</FONT>
	</TD>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
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

<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="410" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Menci&oacute;n o Especialidad</B>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Tipo</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="410" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $mencion_especialidad ?>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $tipo ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR><BR>

<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="410" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Inicio</B>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Cohorte</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="410" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo fecha($fecha_inicio) ?>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $_codcohorte ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR><BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
	Seleccione un Acta existente para visualizarla o ingrese un 
	<A HREF="ingreso_acta.php?_codcohorte=<? echo $_codcohorte ?>"><B>Acta Nueva</B></A> (<B>Nuevas Notas</B>)
</FONT>

<BR><BR>

<TABLE BORDER="0" WIDTH="771" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
<TR>
	<TD WIDTH="50" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Acta</B>
		</FONT>
	</TD>
	<TD WIDTH="230" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Profesor</B>
		</FONT>
	</TD>
	<TD WIDTH="311" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Asignatura</B>
		</FONT>
	</TD>
	<TD WIDTH="50" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Perio.</B>
		</FONT>
	</TD>
	<TD WIDTH="130" ALIGN="right" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Fecha Aprobaci&oacute;n</B>
		</FONT>
	</TD>
</TR>
<?php
$sqlcmd = "
SELECT 
    registro_actas.codacta, 
    registro_actas.cedula_profesor, 
    pensum_estudios.asignatura, 
    pensum_estudios.periodos, 
    registro_actas.fecha_aprobacion
FROM registro_actas
INNER JOIN pensum_estudios ON registro_actas.codasig = pensum_estudios.codasig
INNER JOIN cohortes ON registro_actas.codcohorte = cohortes.codcohorte
    AND cohortes.codsede = pensum_estudios.codsede
    AND cohortes.codopest = pensum_estudios.codopest
WHERE registro_actas.codcohorte = '$_codcohorte'
ORDER BY pensum_estudios.periodos, registro_actas.codasig
";

$resultado = mysqli_query($conexion, $sqlcmd);
if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

while ($registro = mysqli_fetch_object($resultado)) {
    $codacta = $registro->codacta;
    $cedula_profesor = $registro->cedula_profesor;
    $asignatura = $registro->asignatura;
    $periodos = $registro->periodos;
    $fecha_aprobacion = $registro->fecha_aprobacion;

    $curso_d = strtolower(substr($codacta, -3, 2));
    if ($curso_d == "cd") {
        $asignatura .= ' <B>(CD)</B>';
    }

    $apellidos_nombres = datos_profesor($cedula_profesor,$conexion);

    if ($fecha_aprobacion == '0000-00-00' || $fecha_aprobacion == "") {
        $fecha_aprobacion = "";
    } else {
        $fecha_aprobacion = fecha($fecha_aprobacion, corto);
    }

    $bg_celda = ($bg_celda == '#CCCCCC') ? '#FFFFFF' : '#CCCCCC';
?>
<TR>
    <TD WIDTH="50" ALIGN="center" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
        <FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
            <A HREF="javascript:popup('_blank', 'detalle_acta.php?codacta=<?php echo $codacta ?>',640,510)">
                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>Ver</B></FONT>
            </A>
        </FONT>
    </TD>
    <TD WIDTH="230" ALIGN="left" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
        <FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
            <?php echo $apellidos_nombres ?>
        </FONT>
    </TD>
    <TD WIDTH="311" ALIGN="left" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
        <FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
            <?php echo $asignatura ?>
        </FONT>
    </TD>
    <TD WIDTH="50" ALIGN="center" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
        <FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
            <?php echo $periodos ?>
        </FONT>
    </TD>
    <TD WIDTH="130" ALIGN="right" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
        <FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
            <?php echo $fecha_aprobacion ?>
        </FONT>
    </TD>
</TR>
<?php
    $cedula_profesor = '';
    $apellidos_nombres = '';
}
?>
</TABLE>

<?
		if (! $pase_por_aqui)
		{
?>

			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
				<BR><BR>
				<B>No se encontraron Actas existentes para la Cohorte seleccionada.</B>
			</FONT>
<?
		}


### Busco si hay Multiactas

$sqlcmd = "
SELECT DISTINCT 
    pe.asignatura, 
    pe.codasig 
FROM pensum_estudios pe
INNER JOIN multiactas m ON pe.codasig = m.codasig
WHERE 
    pe.codsede = '$_codsede_menu' AND 
    pe.codopest = '$_codopest_menu' AND 
    m.codcohorte = '$_codcohorte'
ORDER BY pe.asignatura
";

$resultado = mysqli_query($conexion, $sqlcmd);
if (!$resultado) {
    die("Error en consulta: " . mysqli_error($conexion));
}

while ($registro = mysqli_fetch_object($resultado)) {
    $asignatura = $registro->asignatura;
    $codasig = $registro->codasig;

    if ($asignatura && $codasig) {
        echo "<br><font size='-1' face='Verdana,Arial,Geneva'><b>$asignatura</b></font><br>";

        echo '<table border="0" width="771" cellspacing="1" cellpadding="2" bgcolor="#000099">';
        echo '<tr>
                <td width="50"></td>
                <td width="591" colspan="3" align="center" valign="top">
                    <font size="-2" face="Verdana,Arial,Geneva" color="#FFFFFF">
                        <b>P&nbsp;&nbsp;R&nbsp;&nbsp;O&nbsp;&nbsp;F&nbsp;&nbsp;E&nbsp;&nbsp;S&nbsp;&nbsp;O&nbsp;&nbsp;R&nbsp;&nbsp;E&nbsp;&nbsp;S</b>
                    </font>
                </td>
                <td width="130" align="center" valign="top">
                    <font size="-2" face="Verdana,Arial,Geneva" color="#FFFFFF">
                        <b>Fecha Aprobación</b>
                    </font>
                </td>
              </tr>';

        $sqlcmd2 = "
        SELECT 
            mid, codacta, 
            cedula_profesor1, 
            cedula_profesor2, 
            cedula_profesor3, 
            fecha_aprobacion
        FROM multiactas
        WHERE codcohorte = '$_codcohorte' AND codasig = '$codasig'
        ";

        $resultado2 = mysqli_query($conexion, $sqlcmd2);
        if (!$resultado2) {
            die("Error en consulta 2: " . mysqli_error($conexion));
        }

        while ($reg = mysqli_fetch_object($resultado2)) {
            $mid = $reg->mid;
            $codacta = $reg->codacta;

           $profesores = array(
    datos_profesor($reg->cedula_profesor1,$conexion),
    datos_profesor($reg->cedula_profesor2,$conexion),
    datos_profesor($reg->cedula_profesor3,$conexion)
);

            $fecha_aprobacion = (
                $reg->fecha_aprobacion == '0000-00-00' || 
                $reg->fecha_aprobacion == ''
            ) ? '' : fecha($reg->fecha_aprobacion);

            $bg_celda = ($bg_celda == '#CCCCCC') ? '#FFFFFF' : '#CCCCCC';
            ?>
            <tr>
                <td width="50" align="center" valign="top" bgcolor="<?= $bg_celda ?>">
                    <a href="javascript:popup('_blank', 'detalle_multiacta.php?codacta=<?= $codacta ?>&mid=<?= $mid ?>',650,350)">
                        <font size="-2" face="Verdana,Arial,Geneva" color="#3300FF"><b>Ver</b></font>
                    </a>
                </td>
                <?php foreach ($profesores as $nombre): ?>
                <td width="197" align="left" valign="top" bgcolor="<?= $bg_celda ?>">
                    <font size="-2" face="Verdana,Arial,Geneva"><?= $nombre ?></font>
                </td>
                <?php endforeach; ?>
                <td width="130" align="right" valign="top" bgcolor="<?= $bg_celda ?>">
                    <font size="-2" face="Verdana,Arial,Geneva"><?= $fecha_aprobacion ?></font>
                </td>
            </tr>
            <?php
        }
        echo '</table>';

        // Reiniciar valores para siguiente asignatura
        $bg_celda = '#FFFFFF';
    }
}
?>


<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
