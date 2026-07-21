<?php
session_start();

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_alumnos_por_cohorte.php");

// Conexión mysqli - Asumiendo que $conexion viene de conexion.php
// Si no, crea la conexión aquí:
// $conexion = mysqli_connect('localhost', 'usuario', 'clave', DB_DATABASE);
$codsede=$_GET['codsede'];
// Obtenemos ciudad y estado/provincia
$sqlcmd = "SELECT ciudad, edo_prov FROM directorio_cippsv WHERE codsede='$codsede'";
$query = mysqli_query($conexion, $sqlcmd);
$ciudad = "";
$edo_prov = "";
if ($registro = mysqli_fetch_object($query)) {
    $ciudad = $registro->ciudad;
    $edo_prov = $registro->edo_prov;
}

// Obtenemos fecha de la primera cohorte
$sqlcmd = "SELECT fecha_inicio FROM cohortes WHERE codsede='$codsede' ORDER BY fecha_inicio LIMIT 1";
$query = mysqli_query($conexion, $sqlcmd);
$primera_cohorte = "";
if ($registro = mysqli_fetch_object($query)) {
    $primera_cohorte = $registro->fecha_inicio;
}

// Contamos la cantidad de cohortes
$sqlcmd = "SELECT count(*) AS cantidad_cohortes FROM cohortes WHERE codsede='$codsede'";
$query = mysqli_query($conexion, $sqlcmd);
$cantidad_cohortes = 0;
if ($registro = mysqli_fetch_object($query)) {
    $cantidad_cohortes = $registro->cantidad_cohortes;
}

?>
<HTML>
<HEAD>
    <TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
    <META NAME="generator" CONTENT="BBEdit 6.5.3 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#0000FF" VLINK="#0000FF">

<CENTER>

<?php
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/encabezado.php");
?>

<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
    <TD WIDTH="100%" ALIGN="left" VALIGN="top">

        <A HREF="../"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>

        <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT>

        <A HREF="seleccion_de_sede.php"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n de Sede</B></FONT></A>

    </TD>
</TR>
</TABLE>

<BR><BR>

<FONT FACE="Verdana,Arial,Geneva" COLOR="#000099">
<B>Seleccione la Cohorte a la cual desea ver los Reportes</B>
</FONT>

<BR><BR><BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
    <TD WIDTH="200" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
            <B>Ciudad</B>
        </FONT>
    </TD>
    <TD WIDTH="200" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
            <B>Estado o Provincia</B>
        </FONT>
    </TD>
    <TD WIDTH="200" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
            <B>Primera Cohorte</B>
        </FONT>
    </TD>
</TR>
<TR>
    <TD WIDTH="200" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
            <?php echo $ciudad; ?>
        </FONT>
    </TD>
    <TD WIDTH="200" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
            <?php echo $edo_prov; ?>
        </FONT>
    </TD>
    <TD WIDTH="200" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
            <?php echo fecha($primera_cohorte); ?>
        </FONT>
    </TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
    <TD WIDTH="600" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
            <B>Total Cohortes:</B> &nbsp;  <?php echo $cantidad_cohortes; ?>
        </FONT>
    </TD>
</TR>
</TABLE>

<BR><BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
    Viendo Reporte Por A&ntilde;o | 
    <A HREF="seleccion_de_cohorte2.php?codsede=<?php echo $codsede; ?>">Viendo Reporte por Especialidad o Postgrado</A>
</FONT>

<BR><BR>

<?php
$sqlcmd = "SELECT DATE_FORMAT(fecha_inicio, '%Y') AS ano_cohortes
        FROM cohortes
        WHERE codsede='$codsede'
        GROUP BY ano_cohortes
        ORDER BY fecha_inicio";
$query = mysqli_query($conexion, $sqlcmd);

$cantidad_actas_total_final = 0;
$cantidad_notas_acumuladas_total_final = 0;
$alumnos_por_cohorte_var_total_final = 0;

$ano_cohortes_anterior = null;
$cantidad_por_ano_anterior = null;

while ($registro = mysqli_fetch_object($query)) {
    $ano_cohortes = $registro->ano_cohortes;

    // Cantidad de cohortes en este año
    $sqlcmd2 = "SELECT count(*) AS cantidad_por_ano
                FROM cohortes
                WHERE codsede='$codsede' AND DATE_FORMAT(fecha_inicio, '%Y')='$ano_cohortes'";

    $query2 = mysqli_query($conexion, $sqlcmd2);
    $cantidad_por_ano = 0;
    if ($registro2 = mysqli_fetch_object($query2)) {
        $cantidad_por_ano = $registro2->cantidad_por_ano;
    }

    // Datos de cohortes y especialidades en ese año
    $sqlcmd3 = "SELECT cohortes.codcohorte, cohortes.fecha_inicio, oportunidades_estudio.mencion_especialidad, oportunidades_estudio.tipo
                FROM cohortes, oportunidades_estudio
                WHERE cohortes.codopest = oportunidades_estudio.codopest
                  AND oportunidades_estudio.codsede = '$codsede'
                  AND cohortes.codsede = '$codsede'
                  AND DATE_FORMAT(fecha_inicio, '%Y') = '$ano_cohortes'
                ORDER BY cohortes.fecha_inicio";

    $query3 = mysqli_query($conexion, $sqlcmd3);

    $contador = 0;
    $cantidad_actas_total = 0;
    $cantidad_notas_acumuladas_total = 0;
    $alumnos_por_cohorte_var_total = 0;
    $imprimo_ano_cohortes = 0;
    $imprimo_cantidad_por_ano = 0;
    $bg_celda = '#FFFFFF';

    if ($ano_cohortes != $ano_cohortes_anterior) {
        $imprimo_ano_cohortes = 1;
        $ano_cohortes_anterior = $ano_cohortes;
        $cantidad_por_ano_anterior = 0;
    }

    if ($cantidad_por_ano != $cantidad_por_ano_anterior) {
        $imprimo_cantidad_por_ano = 1;
        $cantidad_por_ano_anterior = $cantidad_por_ano;
    }

    if ($imprimo_ano_cohortes) {
        ?>
        <BR>

        <TABLE BORDER="0" WIDTH="750" CELLSPACING="1" CELLPADDING="2">
        <TR>
            <TD WIDTH="250" ALIGN="left" VALIGN="top">
                <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
                    <B>A&ntilde;o:</B> <?php echo $ano_cohortes; ?>
                </FONT>
            </TD>
            <TD WIDTH="500" ALIGN="left" VALIGN="top">
                <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
                    <B>Total Cohortes en este A&ntilde;o:</B> <?php echo $cantidad_por_ano; ?>
                </FONT>
            </TD>
        </TR>
        </TABLE>

        <TABLE BORDER="0" WIDTH="750" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
        <TR>
            <TD WIDTH="50" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
                    <B>N&uacute;m.</B>
                </FONT>
            </TD>
            <TD WIDTH="380" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
                    <B>Especialidad o Postgrado</B>
                </FONT>
            </TD>
            <TD WIDTH="130" ALIGN="right" VALIGN="top" BGCOLOR="#000099">
                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
                    <B>Fecha de Inicio</B>
                </FONT>
            </TD>
            <TD WIDTH="55" ALIGN="right" VALIGN="top" BGCOLOR="#000099">
                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
                    <B>Actas</B>
                </FONT>
            </TD>
            <TD WIDTH="65" ALIGN="right" VALIGN="top" BGCOLOR="#000099">
                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
                    <B>Notas</B>
                </FONT>
            </TD>
            <TD WIDTH="70" ALIGN="right" VALIGN="top" BGCOLOR="#000099">
                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
                    <B>Alumnos</B>
                </FONT>
            </TD>
        </TR>
    <?php
    }

    while ($registro3 = mysqli_fetch_object($query3)) {
        $contador++;

        $codcohorte = $registro3->codcohorte;
        $fecha_inicio = $registro3->fecha_inicio;
        $mencion_especialidad = $registro3->mencion_especialidad;
        $tipo = $registro3->tipo;

        // Cantidad actas registro_actas
        $sqlcmd4 = "SELECT count(*) as cantidad_actas FROM registro_actas WHERE codcohorte='$codcohorte'";
        $query4 = mysqli_query($conexion, $sqlcmd4);
        $cantidad_actas = 0;
        if ($registro4 = mysqli_fetch_object($query4)) {
            $cantidad_actas = $registro4->cantidad_actas;
        }

        // Cantidad multiactas
        $sqlcmd4 = "SELECT count(*) as cantidad_multiactas FROM multiactas WHERE codcohorte='$codcohorte'";
        $query4 = mysqli_query($conexion, $sqlcmd4);
        $cantidad_multiactas = 0;
        if ($registro4 = mysqli_fetch_object($query4)) {
            $cantidad_multiactas = $registro4->cantidad_multiactas;
        }

        $cantidad_actas_total += $cantidad_actas + $cantidad_multiactas;

        // Cantidad notas en registro_actas
        $sqlcmd5 = "SELECT count(*) as cantidad_notas
                    FROM registro_actas, record_notas
                    WHERE registro_actas.codcohorte='$codcohorte' AND registro_actas.codacta=record_notas.codacta
                    GROUP BY registro_actas.codacta";
        $query5 = mysqli_query($conexion, $sqlcmd5);

        $cantidad_notas_acumuladas = 0;
        while ($registro5 = mysqli_fetch_object($query5)) {
            $cantidad_notas_acumuladas += $registro5->cantidad_notas;
        }

        // Cantidad notas en multiactas
        $sqlcmd5 = "SELECT count(*) as cantidad_notas
                    FROM multiactas, record_notas
                    WHERE multiactas.codcohorte='$codcohorte' AND multiactas.codacta=record_notas.codacta
                      AND multiactas.mid=record_notas.mid
                    GROUP BY multiactas.codacta";
        $query5 = mysqli_query($conexion, $sqlcmd5);

        while ($registro5 = mysqli_fetch_object($query5)) {
            $cantidad_notas_acumuladas += $registro5->cantidad_notas;
        }

        $cantidad_notas_acumuladas_total += $cantidad_notas_acumuladas;

        // Alumnos por cohorte (usa la función externa)
        $alumnos_por_cohorte_var = alumnos_por_cohorte($codcohorte);
        $alumnos_por_cohorte_var_total += $alumnos_por_cohorte_var;

        // Alternar color de celda
        $bg_celda = ($bg_celda == '#CCCCCC') ? '#FFFFFF' : '#CCCCCC';

        ?>
        <TR>
            <TD WIDTH="50" ALIGN="center" VALIGN="top" BGCOLOR="<?php echo $bg_celda; ?>">
                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
                    <?php echo $contador; ?>
                </FONT>
            </TD>
            <TD WIDTH="380" ALIGN="left" VALIGN="top" BGCOLOR="<?php echo $bg_celda; ?>">
                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
                    <?php echo $tipo . ' ' . $mencion_especialidad; ?>
                </FONT>
            </TD>
            <TD WIDTH="130" ALIGN="right" VALIGN="top" BGCOLOR="<?php echo $bg_celda; ?>">
                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
                    <?php echo fecha($fecha_inicio, 'corto'); ?>
                </FONT>
            </TD>
            <TD WIDTH="55" ALIGN="right" VALIGN="top" BGCOLOR="<?php echo $bg_celda; ?>">
                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
                    <?php echo $cantidad_actas + $cantidad_multiactas; ?>
                </FONT>
            </TD>
            <TD WIDTH="65" ALIGN="right" VALIGN="top" BGCOLOR="<?php echo $bg_celda; ?>">
                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
                    <?php echo $cantidad_notas_acumuladas; ?>
                </FONT>
            </TD>
            <TD WIDTH="70" ALIGN="right" VALIGN="top" BGCOLOR="<?php echo $bg_celda; ?>">
                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
                    <?php echo $alumnos_por_cohorte_var; ?>
                </FONT>
            </TD>
        </TR>
        <?php
    } // fin while $registro3

    ?>
    </TABLE>

    <TABLE BORDER="0" WIDTH="750" CELLSPACING="1" CELLPADDING="2">
    <TR>
        <TD WIDTH="560" ALIGN="right" VALIGN="top">
            <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000099">
                <B>TOTAL</B> &nbsp;
            </FONT>
        </TD>
        <TD WIDTH="55" ALIGN="right" VALIGN="top">
            <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000099">
                <B><?php echo $cantidad_actas_total; ?></B>
            </FONT>
        </TD>
        <TD WIDTH="65" ALIGN="right" VALIGN="top">
            <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000099">
                <B><?php echo $cantidad_notas_acumuladas_total; ?></B>
            </FONT>
        </TD>
        <TD WIDTH="70" ALIGN="right" VALIGN="top">
            <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000099">
                <B><?php echo $alumnos_por_cohorte_var_total; ?></B>
            </FONT>
        </TD>
    </TR>
    </TABLE>

    <?php
    // Acumular totales finales
    $cantidad_actas_total_final += $cantidad_actas_total;
    $cantidad_notas_acumuladas_total_final += $cantidad_notas_acumuladas_total;
    $alumnos_por_cohorte_var_total_final += $alumnos_por_cohorte_var_total;

} // fin while $registro

?>

<BR>

<TABLE BORDER="0" WIDTH="350" CELLSPACING="1" CELLPADDING="2">
<TR>
    <TD WIDTH="250" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
            <B>TOTAL ACTAS</B>
        </FONT>
    </TD>
    <TD WIDTH="100" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
            <B><?php echo $cantidad_actas_total_final; ?></B>
        </FONT>
    </TD>
</TR>
<TR>
    <TD WIDTH="250" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
            <B>TOTAL NOTAS</B>
        </FONT>
    </TD>
    <TD WIDTH="100" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
            <B><?php echo $cantidad_notas_acumuladas_total_final; ?></B>
        </FONT>
    </TD>
</TR>
<TR>
    <TD WIDTH="250" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
            <B>TOTAL ESTUDIANTES</B>
        </FONT>
    </TD>
    <TD WIDTH="100" ALIGN="left" VALIGN="top">
        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
            <B><?php echo $alumnos_por_cohorte_var_total_final; ?></B>
        </FONT>
    </TD>
</TR>
</TABLE>

<?php
// include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
