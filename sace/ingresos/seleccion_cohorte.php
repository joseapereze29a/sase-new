<?php
session_start();

//include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/creditos.php");
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_fecha.php");
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/conexion.php");

// Tomar variables y validar
$_codopest = isset($_POST['_codopest']) ? $_POST['_codopest'] : '';
$_codsede = isset($_SESSION['_codsede']) ? $_SESSION['_codsede'] : '';

if (empty($_codopest) || empty($_codsede)) {
    // Redirigir si faltan datos
    header("Location: seleccion_postgrado.php?_codsede=" . urlencode($_codsede));
    exit;
}

// Escapar variables para seguridad
$_codopest_esc = mysqli_real_escape_string($conexion, $_codopest);
$_codsede_esc = mysqli_real_escape_string($conexion, $_codsede);

// Consulta para obtener info del postgrado
$sqlcmd = "SELECT dc.modalidad, dc.ciudad, dc.edo_prov, oe.tipo, oe.mencion_especialidad
           FROM directorio_cippsv dc
           INNER JOIN oportunidades_estudio oe ON dc.codsede = oe.codsede
           WHERE oe.codsede = '$_codsede_esc'
             AND oe.codopest = '$_codopest_esc'";

$query = mysqli_query($conexion, $sqlcmd);

$modalidad = $ciudad = $edo_prov = $tipo = $mencion_especialidad = '';

if ($query && mysqli_num_rows($query) > 0) {
    $registro = mysqli_fetch_assoc($query);
    $modalidad = $registro['modalidad'];
    $ciudad = $registro['ciudad'];
    $edo_prov = $registro['edo_prov'];
    $tipo = $registro['tipo'];
    $mencion_especialidad = $registro['mencion_especialidad'];
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>CIPPSV Web Site | Sistema de Control de Estudios</title>
    <meta charset="UTF-8">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#0000FF" alink="#0000FF" vlink="#0000FF">

    <center>

        <?php include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/encabezado.php"); ?>

        <table border="0" width="100%" cellspacing="1" cellpadding="1">
            <tr>
                <td align="left" valign="top">
                    <a href="../">
                        <font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Home</b></font>
                    </a>
                    <font size="-2" face="Verdana,Arial,Geneva" color="#000000">:</font>
                    <a href="seleccion_de_sede.php">
                        <font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Selección de Sede</b></font>
                    </a>
                    <font size="-2" face="Verdana,Arial,Geneva" color="#000000">:</font>
                    <a href="seleccion_postgrado.php?_codsede=<?= urlencode($_codsede) ?>">
                        <font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Selección del Postgrado</b></font>
                    </a>
                    <font size="-2" face="Verdana,Arial,Geneva" color="#000000">:</font>
                    <font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Cohortes Existentes</b></font>
                </td>
            </tr>
        </table>

        <br>

        <img src="/sace/imagenes/titulos_de_home/titulo_ingreso.jpg" alt="" width="380" height="20" border="0">

        <br><br><br>

        <table border="0" width="600" cellspacing="2" cellpadding="2">
            <tr>
                <td width="600" align="left" valign="top" bgcolor="#000099">
                    <font face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Información sobre el Postgrado</b></font>
                </td>
            </tr>
        </table>

        <table border="0" width="600" cellspacing="2" cellpadding="2">
            <tr>
                <td width="200" align="left" valign="top">
                    <font size="-1" face="Verdana,Arial,Geneva"><b>Ciudad</b></font>
                </td>
                <td width="200" align="left" valign="top">
                    <font size="-1" face="Verdana,Arial,Geneva"><b>Estado o Provincia</b></font>
                </td>
                <td width="200" align="left" valign="top">
                    <font size="-1" face="Verdana,Arial,Geneva"><b>Modalidad</b></font>
                </td>
            </tr>
            <tr>
                <td width="200" align="left" valign="top">
                    <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($ciudad) ?></font>
                </td>
                <td width="200" align="left" valign="top">
                    <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($edo_prov) ?></font>
                </td>
                <td width="200" align="left" valign="top">
                    <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($modalidad) ?></font>
                </td>
            </tr>
        </table>

        <br>

        <table border="0" width="600" cellspacing="2" cellpadding="2">
            <tr>
                <td width="400" align="left" valign="top">
                    <font size="-1" face="Verdana,Arial,Geneva"><b>Mención o Especialidad</b></font>
                </td>
                <td width="200" align="left" valign="top">
                    <font size="-1" face="Verdana,Arial,Geneva"><b>Tipo</b></font>
                </td>
            </tr>
            <tr>
                <td width="400" align="left" valign="top">
                    <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($mencion_especialidad) ?></font>
                </td>
                <td width="200" align="left" valign="top">
                    <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($tipo) ?></font>
                </td>
            </tr>
        </table>

        <br><br>

        <font size="-1" face="Verdana,Arial,Geneva">
            Seleccione una Cohorte existente o ingrese una
            <a href="ingreso_cohorte.php?_codsede=<?= urlencode($_codsede) ?>&_codopest=<?= urlencode($_codopest) ?>">
                <font size="-1" face="Verdana,Arial,Geneva" color="#0000FF"><b>Cohorte Nueva</b></font>
            </a>
        </font>

        <br><br>

        <table border="0" width="600" cellspacing="1" cellpadding="2" bgcolor="#000099">
            <tr>
                <td width="100" align="center" valign="top" bgcolor="#000099">
                    <font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>N&uacute;m.</b></font>
                </td>
                <td width="150" align="center" valign="top" bgcolor="#000099">
                    <font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Cohorte</b></font>
                </td>
                <td width="150" align="center" valign="top" bgcolor="#000099">
                    <font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Periodo Lectivo</b></font>
                </td>
                <td width="200" align="right" valign="top" bgcolor="#000099">
                    <font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Fecha de Inicio</b></font>
                </td>
            </tr>
            <?php
            $sqlcmd = "SELECT codcohorte, fecha_inicio, periodo_lectivo
           FROM cohortes
           WHERE codsede = '$_codsede_esc'
             AND codopest = '$_codopest_esc'
           ORDER BY fecha_inicio";

            $query = mysqli_query($conexion, $sqlcmd);

            $contador = 0;
            $bg_celda = '#CCCCCC';
            $pase_por_aqui = 0;

            if ($query && mysqli_num_rows($query) > 0) {
                while ($registro = mysqli_fetch_assoc($query)) {
                    $contador++;
                    $codcohorte = $registro['codcohorte'];
                    $fecha_inicio = $registro['fecha_inicio'];
                    $periodo_lectivo = $registro['periodo_lectivo'];
                    $pase_por_aqui = 1;

                    if ($fecha_inicio == '0000-00-00' || $fecha_inicio == '') {
                        $fecha_inicio_fmt = '';
                    } else {
                        $fecha_inicio_fmt = fecha($fecha_inicio);
                    }

                    $bg_celda = ($bg_celda == '#CCCCCC') ? '#FFFFFF' : '#CCCCCC';
            ?>
                    <tr>
                        <td width="100" align="center" valign="top" bgcolor="<?= $bg_celda ?>">
                            <font size="-1" face="Verdana,Arial,Geneva"><?= $contador ?></font>
                        </td>
                        <td width="150" align="center" valign="top" bgcolor="<?= $bg_celda ?>">
                            <a href="seleccion_acta.php?_codcohorte=<?= urlencode($codcohorte) ?>">
                                <font size="-1" face="Verdana,Arial,Geneva" color="#0000FF"><b>Seleccionar</b></font>
                            </a>
                        </td>
                        <td width="150" align="left" valign="top" bgcolor="<?= $bg_celda ?>">
                            <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($periodo_lectivo) ?></font>
                        </td>
                        <td width="200" align="right" valign="top" bgcolor="<?= $bg_celda ?>">
                            <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($fecha_inicio_fmt) ?></font>
                        </td>
                    </tr>
                <?php
                }
            }

            if (!$pase_por_aqui):
                ?>
                <font size="-1" face="Verdana,Arial,Geneva">
                    <br><br>
                    <b>No se encontraron Cohortes existentes para el Postgrado seleccionado.</b>
                </font>
            <?php
            endif;
            ?>

            <?php
            // include($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
            ?>

    </center>

</body>

</html>