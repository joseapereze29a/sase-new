<?php
session_start();
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/creditos.php");

include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_fecha.php");

//include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");
require_once dirname(__FILE__) . '/../includes/conexion.php';

// Obtener cédula desde GET o POST
if ($_SESSION['cedula']) {
    if (!preg_match("/^[0-9]+$/", $_SESSION['cedula'])) {
        $_SESSION['error'] = 'La cédula debe tener entre 7 y 8 dígitos numéricos.';

        header("Location: ingreso_de_cedula.php");
        exit;
    }
    $cedula = $_SESSION['cedula'];
}

$_orden = isset($_GET['_orden']) ? $_GET['_orden'] : '';

// Verifica que la cédula sea válida
if (empty($cedula)) {
    echo "Debe indicar la cédula del estudiante.";
    exit;
}

// ==========================================
// CONSULTA DATOS PERSONALES
// ==========================================
$sql1 = "SELECT cedula, apellidos, nombres, sexo, fecha_nacimiento, lugar_nacimiento 
         FROM datos_personales 
         WHERE cedula = ?";

$stmt_info = mysqli_prepare($conexion, $sql1);
mysqli_stmt_bind_param($stmt_info, 's', $cedula);
mysqli_stmt_execute($stmt_info);
mysqli_stmt_bind_result($stmt_info, $cedula_db, $apellidos, $nombres, $sexo, $fecha_nacimiento, $lugar_nacimiento);

if (mysqli_stmt_fetch($stmt_info)) {
?>
    <table border="0" width="700" cellspacing="2" cellpadding="2">
        <tr>
            <td width="700" align="left" valign="top" bgcolor="#000099">
                <font face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Datos Personales</b></font>
            </td>
        </tr>
    </table>

    <table border="0" width="700" cellspacing="2" cellpadding="2">
        <tr>
            <td width="175">
                <font size="-1" face="Verdana,Arial,Geneva"><b>Cédula</b></font>
            </td>
            <td width="175">
                <font size="-1" face="Verdana,Arial,Geneva"><b>Apellidos</b></font>
            </td>
            <td width="175">
                <font size="-1" face="Verdana,Arial,Geneva"><b>Nombres</b></font>
            </td>
            <td width="175">
                <font size="-1" face="Verdana,Arial,Geneva"><b>Sexo</b></font>
            </td>
        </tr>
        <tr>
            <td>
                <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($cedula_db) ?></font>
            </td>
            <td>
                <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($apellidos) ?></font>
            </td>
            <td>
                <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($nombres) ?></font>
            </td>
            <td>
                <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($sexo) ?></font>
            </td>
        </tr>
    </table>

    <br>
    <table border="0" width="700" cellspacing="2" cellpadding="2">
        <tr>
            <td width="350">
                <font size="-1" face="Verdana,Arial,Geneva"><b>Fecha de Nacimiento</b></font>
            </td>
            <td width="350">
                <font size="-1" face="Verdana,Arial,Geneva"><b>Lugar de Nacimiento</b></font>
            </td>
        </tr>
        <tr>
            <td>
                <font size="-1" face="Verdana,Arial,Geneva"><?= fecha($fecha_nacimiento) ?></font>
            </td>
            <td>
                <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($lugar_nacimiento) ?></font>
            </td>
        </tr>
    </table>
    <?php
}
mysqli_stmt_free_result($stmt_info);
mysqli_stmt_close($stmt_info);

// ==========================================
// CONSULTA POSTGRADOS Y COHORTES
// ==========================================
$sql2 = "SELECT dc.ciudad, oe.tipo, oe.mencion_especialidad, oe.codopest, oe.codsede,
                oe.periodos, oe.creditos, co.fecha_inicio, co.periodo_lectivo
         FROM cohortes co
         JOIN oportunidades_estudio oe ON co.codopest = oe.codopest
         JOIN directorio_cippsv dc ON oe.codsede = dc.codsede
         WHERE co.codcohorte = ?";

if ($stmt_cohorte = mysqli_prepare($conexion, $sql2)) {
    // Obtener todas las cohortes asociadas a la cédula
    $sql_cod = "SELECT codcohorte FROM inscripciones WHERE cedula = ?";
    $stmt_cod = mysqli_prepare($conexion, $sql_cod);
    mysqli_stmt_bind_param($stmt_cod, 's', $cedula);
    mysqli_stmt_execute($stmt_cod);
    mysqli_stmt_bind_result($stmt_cod, $codcohorte);
    $codcohortes = array();
    while (mysqli_stmt_fetch($stmt_cod)) {
        $codcohortes[] = $codcohorte;
    }
    mysqli_stmt_free_result($stmt_cod);
    mysqli_stmt_close($stmt_cod);

    foreach ($codcohortes as $cod) {
        mysqli_stmt_bind_param($stmt_cohorte, 's', $cod);
        mysqli_stmt_execute($stmt_cohorte);
        mysqli_stmt_bind_result(
            $stmt_cohorte,
            $ciudad,
            $tipo,
            $mencion_especialidad,
            $codopest,
            $codsede,
            $periodos,
            $creditos,
            $fecha_inicio,
            $periodo_lectivo
        );

        if (mysqli_stmt_fetch($stmt_cohorte)) {
    ?>
            <br>
            <table border="0" width="700" cellspacing="2" cellpadding="2">
                <tr>
                    <td width="700" bgcolor="#000099">
                        <font face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Información del Postgrado</b></font>
                    </td>
                </tr>
            </table>
            <table border="0" width="700" cellspacing="2" cellpadding="2">
                <tr>
                    <td width="175">
                        <font size="-1" face="Verdana,Arial,Geneva"><b>Ciudad</b></font>
                    </td>
                    <td width="175">
                        <font size="-1" face="Verdana,Arial,Geneva"><b>Tipo</b></font>
                    </td>
                    <td width="350">
                        <font size="-1" face="Verdana,Arial,Geneva"><b>Mención o Especialidad</b></font>
                    </td>
                </tr>
                <tr>
                    <td>
                        <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($ciudad) ?></font>
                    </td>
                    <td>
                        <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($tipo) ?></font>
                    </td>
                    <td>
                        <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($mencion_especialidad) ?></font>
                    </td>
                </tr>
            </table>
            <br>
            <table border="0" width="700" cellspacing="2" cellpadding="2">
                <tr>
                    <td width="175">
                        <font size="-1" face="Verdana,Arial,Geneva"><b>Cohorte</b></font>
                    </td>
                    <td width="175">
                        <font size="-1" face="Verdana,Arial,Geneva"><b>Periodo Lectivo</b></font>
                    </td>
                    <td width="350">
                        <font size="-1" face="Verdana,Arial,Geneva"><b>Fecha de Inicio</b></font>
                    </td>
                </tr>
                <tr>
                    <td>
                        <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($cod) ?></font>
                    </td>
                    <td>
                        <font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($periodo_lectivo) ?></font>
                    </td>
                    <td>
                        <font size="-1" face="Verdana,Arial,Geneva"><?= fecha($fecha_inicio) ?></font>
                    </td>
                </tr>
            </table>
            <br><br>
            <?php

            // ==========================
            // CREAR TABLA TEMPORAL
            mysqli_query($conexion, "DROP TEMPORARY TABLE IF EXISTS actas_consulta_temp");
            $sql_create_temp = "
              CREATE TEMPORARY TABLE actas_consulta_temp (
                codasig VARCHAR(255),
                asignatura VARCHAR(255),
                creditos INT,
                periodos INT,
                calificacion VARCHAR(255),
                fecha_aprobacion DATE,
                codasig_imp VARCHAR(255),
                codacta VARCHAR(255)
              ) ENGINE=MEMORY
            ";
            mysqli_query($conexion, $sql_create_temp);

            // INSERT 1
            $sql_insert1 = "
                            INSERT INTO actas_consulta_temp (codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta)
                            SELECT ra.codasig, pe.asignatura, pe.creditos, pe.periodos, rn.calificacion, ra.fecha_aprobacion, pe.codasig_imp, rn.codacta
                            FROM registro_actas ra
                            JOIN pensum_estudios pe ON ra.codasig = pe.codasig AND pe.codsede = ? AND pe.codopest = ?
                            JOIN record_notas rn ON ra.codacta = rn.codacta
                            JOIN cohortes co ON ra.codcohorte = co.codcohorte
                            WHERE co.codcohorte = ? AND rn.cedula = ?
                            ";

            $stmt_insert1 = mysqli_prepare($conexion, $sql_insert1);
            mysqli_stmt_bind_param($stmt_insert1, 'sssd', $codsede, $codopest, $cod, $cedula);
            mysqli_stmt_execute($stmt_insert1);
            mysqli_stmt_close($stmt_insert1);

            // INSERT 2
            $sql_insert2 = "
                            INSERT INTO actas_consulta_temp (codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta)
                            SELECT ma.codasig, pe.asignatura, pe.creditos, pe.periodos, rn.calificacion, ma.fecha_aprobacion, pe.codasig_imp, ma.codacta
                            FROM pensum_estudios pe
                            JOIN multiactas ma ON pe.codasig = ma.codasig AND pe.codsede = ? AND pe.codopest = ?
                            JOIN record_notas rn ON ma.mid = rn.mid
                            WHERE ma.codcohorte = ? AND rn.cedula = ?
                            ";

            $stmt_insert2 = mysqli_prepare($conexion, $sql_insert2);
            mysqli_stmt_bind_param($stmt_insert2, 'sssd', $codsede, $codopest, $cod, $cedula);
            mysqli_stmt_execute($stmt_insert2);
            mysqli_stmt_close($stmt_insert2);

            // SELECT de notas
            $ordenes_validos = array('codasig', 'asignatura', 'creditos', 'periodos', 'calificacion', 'fecha_aprobacion');
            if (!isset($_orden) || !in_array($_orden, $ordenes_validos)) {
                $_orden = 'periodos, codasig, codacta';
            }
            $sql_select = "SELECT codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta
                           FROM actas_consulta_temp ORDER BY " . $_orden;
            $result = mysqli_query($conexion, $sql_select);

            $notas = 0;
            $total_creditos = 0;
            $bg_celda = '#CCCCCC';

            echo '<TABLE BORDER="0" WIDTH="700" CELLSPACING="1" CELLPADDING="2">';
            while ($row = mysqli_fetch_assoc($result)) {
                // Formateo fecha
                $fecha = ($row['fecha_aprobacion'] == '0000-00-00' || $row['fecha_aprobacion'] == '') ? '' : fecha($row['fecha_aprobacion'], 'corto');

                // Calcula ponderado para promedio
                if (is_numeric($row['calificacion']) && $row['calificacion'] >= 1 && $row['calificacion'] <= 20 && $row['creditos'] > 0) {
                    $notas += $row['calificacion'] * $row['creditos'];
                    $total_creditos += $row['creditos'];
                }

                // Traduce códigos especiales
                $mapa_calificaciones = array(
                    404 => 'No Cursó',
                    99  => 'Reprobado',
                    100 => 'Aprobado',
                    110 => 'Meritorio',
                    120 => 'Excelencia',
                    212 => 'Equivalencia'
                );
                $calificacion_display = isset($mapa_calificaciones[$row['calificacion']]) ? $mapa_calificaciones[$row['calificacion']] : $row['calificacion'];

                // Alternar color fila
                $bg_celda = ($bg_celda == '#CCCCCC') ? '#FFFFFF' : '#CCCCCC';

                echo '<TR>';
                echo '<TD WIDTH="100" ALIGN="left" VALIGN="top" BGCOLOR="' . $bg_celda . '"><FONT SIZE="-1" FACE="Verdana,Arial,Geneva"';
                if (is_numeric($row['calificacion']) && $row['calificacion'] >= 1 && $row['calificacion'] <= 14) echo ' COLOR="#3300FF"';
                echo '>' . htmlspecialchars($row['codasig_imp']) . '</FONT></TD>';

                echo '<TD WIDTH="350" ALIGN="left" VALIGN="top" BGCOLOR="' . $bg_celda . '"><FONT SIZE="-1" FACE="Verdana,Arial,Geneva"';
                if (is_numeric($row['calificacion']) && $row['calificacion'] >= 1 && $row['calificacion'] <= 14) echo ' COLOR="#3300FF"';
                echo '>';
                $curso_d = strtolower(substr($row['codacta'], -3, 2));
                echo htmlspecialchars($row['asignatura']);
                if ($curso_d == 'cd') echo ' <b>(CD)</b>';
                echo '</FONT></TD>';

                echo '<TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="' . $bg_celda . '"><FONT SIZE="-1" FACE="Verdana,Arial,Geneva"';
                if (is_numeric($row['calificacion']) && $row['calificacion'] >= 1 && $row['calificacion'] <= 14) echo ' COLOR="#3300FF"><B>';
                echo htmlspecialchars($calificacion_display);
                if (is_numeric($row['calificacion']) && $row['calificacion'] >= 1 && $row['calificacion'] <= 14) echo '</B>';
                echo '</FONT></TD>';

                echo '<TD WIDTH="150" ALIGN="right" VALIGN="top" BGCOLOR="' . $bg_celda . '"><FONT SIZE="-1" FACE="Verdana,Arial,Geneva">' . htmlspecialchars($fecha) . '</FONT></TD>';
                echo '</TR>';
            }
            echo '</TABLE>';
            mysqli_free_result($result);


            // Mostrar índice académico
            $indice = ($total_creditos > 0) ? number_format($notas / $total_creditos, 2, ',', '') : '0.00';
            ?>
            <TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2">
                <TR>
                    <TD WIDTH="600" ALIGN="center" VALIGN="top">
                        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
                            <B>Índice Académico: <?= $indice ?></B>
                        </FONT>
                    </TD>
                </TR>
            </TABLE>

            <BR>
            <HR SIZE="1" WIDTH="640">

<?php


            mysqli_free_result($result);
            mysqli_query($conexion, "DROP TEMPORARY TABLE IF EXISTS actas_consulta_temp");
        }
    }
    mysqli_stmt_free_result($stmt_cohorte);
    mysqli_stmt_close($stmt_cohorte);
}
?>