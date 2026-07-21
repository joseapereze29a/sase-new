<?php
session_start();
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/creditos.php");
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_fecha.php");
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/conexion.php"); // debe definir $conexion
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_datos_profesor.php");

$_codcohorte = $_GET['_codcohorte'];

$_codcohorte_esc = mysqli_real_escape_string($conexion, $_codcohorte);

// Consulta principal para info del postgrado
$sqlcmd = "SELECT directorio_cippsv.modalidad, directorio_cippsv.ciudad, directorio_cippsv.edo_prov, oportunidades_estudio.tipo, 
            oportunidades_estudio.mencion_especialidad, cohortes.fecha_inicio, cohortes.codsede, cohortes.codopest 
            FROM directorio_cippsv, oportunidades_estudio, cohortes 
            WHERE cohortes.codcohorte='$codcohorte_esc' 
              AND cohortes.codsede=oportunidades_estudio.codsede 
              AND cohortes.codopest=oportunidades_estudio.codopest 
              AND oportunidades_estudio.codsede=directorio_cippsv.codsede";

$query = mysqli_query($conexion, $sqlcmd);
if (!$query) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

while ($registro = mysqli_fetch_object($query)) {
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>CIPPSV Web Site | Sistema de Control de Estudios</title>
    <script>
    function popup(windowname, url, w, h) {
        popupwin = window.open("", windowname, "toolbar=no,location=no,directories=no,status=no,menubar=no,width=" + w + ",height=" + h + ",resizable=1,scrollbars=1");
        popupwin.location = url;
    }
    </script>
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#0000FF" alink="#0000FF" vlink="#0000FF">
<center>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/encabezado.php"); ?>

<!-- Navegación -->
<table border="0" width="100%" cellspacing="1" cellpadding="1">
<tr>
    <td width="100%" align="left" valign="top">
        <a href="../"><font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Home</b></font></a>
        <font size="-2" face="Verdana,Arial,Geneva" color="#000000">:</font>
        <a href="seleccion_de_sede.php"><font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Selección de Sede</b></font></a>
        <font size="-2" face="Verdana,Arial,Geneva" color="#000000">:</font>
        <a href="seleccion_postgrado.php?_codsede=<?php echo htmlspecialchars($_codsede_menu); ?>"><font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Selección del Postgrado</b></font></a>
        <font size="-2" face="Verdana,Arial,Geneva" color="#000000">:</font>
        <a href="seleccion_cohorte.php?_codsede=<?php echo htmlspecialchars($_codsede_menu); ?>&_codopest=<?php echo htmlspecialchars($_codopest_menu); ?>"><font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Cohortes Existentes</b></font></a>
        <font size="-2" face="Verdana,Arial,Geneva" color="#000000">:</font>
        <font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Actas Existentes</b></font>
    </td>
</tr>
</table>

<br>

<img src="/sace/imagenes/titulos_de_home/titulo_ingreso.jpg" alt="" width="380" height="20" border="0">

<br><br><br>

<!-- Información del Postgrado -->
<table border="0" width="710" cellspacing="2" cellpadding="2">
<tr>
    <td width="710" align="left" valign="top" bgcolor="#000099">
        <font face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Información sobre el Postgrado</b></font>
    </td>
</tr>
</table>

<table border="0" width="710" cellspacing="2" cellpadding="2">
<tr>
    <td width="260" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><b>Ciudad</b></font></td>
    <td width="250" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><b>Estado o Provincia</b></font></td>
    <td width="200" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><b>Modalidad</b></font></td>
</tr>
<tr>
    <td width="260" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($ciudad); ?></font></td>
    <td width="250" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($edo_prov); ?></font></td>
    <td width="200" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($modalidad); ?></font></td>
</tr>
</table>

<br>

<table border="0" width="710" cellspacing="2" cellpadding="2">
<tr>
    <td width="410" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><b>Mención o Especialidad</b></font></td>
    <td width="300" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><b>Tipo</b></font></td>
</tr>
<tr>
    <td width="410" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($mencion_especialidad); ?></font></td>
    <td width="300" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($tipo); ?></font></td>
</tr>
</table>

<br><br>

<table border="0" width="710" cellspacing="2" cellpadding="2">
<tr>
    <td width="410" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><b>Fecha de Inicio</b></font></td>
    <td width="300" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><b>Cohorte</b></font></td>
</tr>
<tr>
    <td width="410" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><?php echo fecha($fecha_inicio, 'corto'); ?></font></td>
    <td width="300" align="left" valign="top"><font size="-1" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($_codcohorte); ?></font></td>
</tr>
</table>

<br><br>

<font size="-1" face="Verdana,Arial,Geneva">
    Seleccione un Acta existente para visualizarla o ingrese un 
    <a href="ingreso_acta.php?_codcohorte=<?php echo htmlspecialchars($_codcohorte); ?>"><b>Acta Nueva</b></a> (<b>Nuevas Notas</b>)
</font>

<br><br>

<table border="0" width="771" cellspacing="1" cellpadding="2" bgcolor="#000099">
<tr>
    <td width="50" align="center" valign="top" bgcolor="#000099"><font size="-2" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Acta</b></font></td>
    <td width="230" align="left" valign="top" bgcolor="#000099"><font size="-2" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Profesor</b></font></td>
    <td width="311" align="left" valign="top" bgcolor="#000099"><font size="-2" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Asignatura</b></font></td>
    <td width="50" align="center" valign="top" bgcolor="#000099"><font size="-2" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Perio.</b></font></td>
    <td width="130" align="right" valign="top" bgcolor="#000099"><font size="-2" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Fecha Aprobaci&oacute;n</b></font></td>
</tr>
<?php

$sqlcmd = "SELECT registro_actas.codacta, registro_actas.cedula_profesor, pensum_estudios.asignatura, pensum_estudios.periodos, 
           registro_actas.fecha_aprobacion 
           FROM registro_actas, pensum_estudios, cohortes 
           WHERE registro_actas.codcohorte='$codcohorte_esc' 
             AND registro_actas.codasig=pensum_estudios.codasig 
             AND registro_actas.codcohorte=cohortes.codcohorte 
             AND cohortes.codsede=pensum_estudios.codsede 
             AND cohortes.codopest=pensum_estudios.codopest 
           ORDER BY pensum_estudios.periodos, registro_actas.codasig";

$query = mysqli_query($conexion, $sqlcmd);
if (!$query) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

$pase_por_aqui = 0;
$bg_celda = '#FFFFFF';

while ($registro = mysqli_fetch_object($query)) {
    $codacta = $registro->codacta;
    $cedula_profesor = $registro->cedula_profesor;
    $asignatura = $registro->asignatura;
    $periodos = $registro->periodos;
    $fecha_aprobacion = $registro->fecha_aprobacion;

    $pase_por_aqui = 1;

    $curso_d = substr($codacta, -3, 2);
    $curso_d = strtolower($curso_d);

    if ($curso_d == "cd") {
        $asignatura .= ' <b>(CD)</b>';
    }

    $apellidos_nombres = datos_profesor($cedula_profesor);

    if ($fecha_aprobacion == '0000-00-00' || $fecha_aprobacion == "") {
        $fecha_aprobacion = "";
    } else {
        $fecha_aprobacion = fecha($fecha_aprobacion, 'corto');
    }

    // Alternar color fila
    $bg_celda = ($bg_celda == '#CCCCCC') ? '#FFFFFF' : '#CCCCCC';

    ?>
    <tr>
        <td width="50" align="center" valign="top" bgcolor="<?php echo $bg_celda; ?>">
            <font size="-2" face="Verdana,Arial,Geneva">
                <a href="javascript:popup('_blank', 'detalle_acta.php?codacta=<?php echo urlencode($codacta); ?>',640,510)">
                    <font size="-2" face="Verdana,Arial,Geneva" color="#3300FF"><b>Ver</b></font>
                </a>
            </font>
        </td>
        <td width="230" align="left" valign="top" bgcolor="<?php echo $bg_celda; ?>">
            <font size="-2" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($apellidos_nombres); ?></font>
        </td>
        <td width="311" align="left" valign="top" bgcolor="<?php echo $bg_celda; ?>">
            <font size="-2" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($asignatura); ?></font>
        </td>
        <td width="50" align="center" valign="top" bgcolor="<?php echo $bg_celda; ?>">
            <font size="-2" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($periodos); ?></font>
        </td>
        <td width="130" align="right" valign="top" bgcolor="<?php echo $bg_celda; ?>">
            <font size="-2" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($fecha_aprobacion); ?></font>
        </td>
    </tr>
    <?php
    $cedula_profesor = '';
    $apellidos_nombres = '';
}

if (!$pase_por_aqui) {
    ?>
    <font size="-1" face="Verdana,Arial,Geneva"><br><br><b>No se encontraron Actas existentes para la Cohorte seleccionada.</b></font>
    <?php
}

// Busco si hay Multiactas
$sqlcmd = "SELECT pensum_estudios.asignatura, pensum_estudios.codasig
           FROM pensum_estudios, multiactas
           WHERE pensum_estudios.codsede='" . mysqli_real_escape_string($conexion, $_codsede_menu) . "'
             AND pensum_estudios.codopest='" . mysqli_real_escape_string($conexion, $_codopest_menu) . "'
             AND multiactas.codcohorte='$codcohorte_esc'
             AND pensum_estudios.codasig=multiactas.codasig
           GROUP BY pensum_estudios.codasig";

$query = mysqli_query($conexion, $sqlcmd);
if (!$query) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

$encabezado_multiacta = 0;
$contador = 0;
$bg_celda = '#FFFFFF';

while ($registro = mysqli_fetch_object($query)) {
    $asignatura = $registro->asignatura;
    $codasig = $registro->codasig;

    echo "<br>";

    if ($asignatura && $codasig) {
        if (!$encabezado_multiacta) {
            echo '<font size="-1" face="Verdana,Arial,Geneva">';
            echo htmlspecialchars($asignatura) . '<br>';
            echo '</font>';
            $encabezado_multiacta = 1;
        }
        ?>
        <table border="0" width="771" cellspacing="1" cellpadding="2" bgcolor="#000099">
            <tr>
                <td width="50" align="left" valign="top"><p> </p></td>
                <td width="591" align="center" valign="top" colspan="3">
                    <font size="-2" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>P &nbsp;  &nbsp; R &nbsp;  &nbsp; O &nbsp;  &nbsp; F &nbsp;  &nbsp; E &nbsp;  &nbsp; S &nbsp;  &nbsp; O &nbsp;  &nbsp; R &nbsp;  &nbsp; E &nbsp;  &nbsp; S</b></font>
                </td>
                <td width="130" align="center" valign="top">
                    <font size="-2" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Fecha Aprobaci&oacute;n</b></font>
                </td>
            </tr>
            <?php
            $sqlcmd2 = "SELECT mid, codacta, cedula_profesor1, cedula_profesor2, cedula_profesor3, fecha_aprobacion
                        FROM multiactas
                        WHERE codcohorte='$codcohorte_esc' AND codasig='" . mysqli_real_escape_string($conexion, $codasig) . "'";
            $query2 = mysqli_query($conexion, $sqlcmd2);
            if (!$query2) {
                die("Error en la consulta: " . mysqli_error($conexion));
            }
            while ($registro2 = mysqli_fetch_object($query2)) {
                $mid = $registro2->mid;
                $codacta = $registro2->codacta;
                $cedula_profesor1 = datos_profesor($registro2->cedula_profesor1);
                $cedula_profesor2 = datos_profesor($registro2->cedula_profesor2);
                $cedula_profesor3 = datos_profesor($registro2->cedula_profesor3);
                $fecha_aprobacion = $registro2->fecha_aprobacion;

                if ($fecha_aprobacion == '0000-00-00' || $fecha_aprobacion == "") {
                    $fecha_aprobacion = "";
                } else {
                    $fecha_aprobacion = fecha($fecha_aprobacion, 'corto');
                }

                $contador++;
                $bg_celda = ($bg_celda == '#CCCCCC') ? '#FFFFFF' : '#CCCCCC';

                ?>
                <tr>
                    <td width="50" align="center" valign="top" bgcolor="<?php echo $bg_celda; ?>">
                        <a href="javascript:popup('_blank', 'detalle_multiacta.php?codacta=<?php echo urlencode($codacta); ?>&mid=<?php echo urlencode($mid); ?>',650,350)">
                            <font size="-2" face="Verdana,Arial,Geneva" color="#3300FF"><b>Ver</b></font>
                        </a>
                    </td>
                    <td width="197" align="left" valign="top" bgcolor="<?php echo $bg_celda; ?>"><font size="-2" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($cedula_profesor1); ?></font></td>
                    <td width="197" align="left" valign="top" bgcolor="<?php echo $bg_celda; ?>"><font size="-2" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($cedula_profesor2); ?></font></td>
                    <td width="197" align="left" valign="top" bgcolor="<?php echo $bg_celda; ?>"><font size="-2" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($cedula_profesor3); ?></font></td>
                    <td width="130" align="right" valign="top" bgcolor="<?php echo $bg_celda; ?>"><font size="-2" face="Verdana,Arial,Geneva"><?php echo htmlspecialchars($fecha_aprobacion); ?></font></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    }
}
?>

<br>
<br>

</center>
</body>
</html>