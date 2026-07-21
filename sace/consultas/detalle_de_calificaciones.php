<?
session_start();
###
###		Este script desplega el Detalle de las Notas de algun Estudiante, mostrando
###		por Periodos cada una de las Notas, con mayor detalle, a su vez, permite
###		ir a traves de un Link a ver el Detalle de un Acta de Estudios.
###


###
### Los Clasicos Includes
###
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/creditos.php");

include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_fecha.php");

//include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_datos_profesor.php");
require_once dirname(__FILE__) . '/../includes/conexion.php';
require_once dirname(__FILE__) . '/../includes/funcion_fecha.php';

$cedula = $_GET['cedula'];
$codcohorte = $_GET['codcohorte'];
###
### Busco los Datos Basicos Personales en la Base de Datos
###
/*$sqlcmd = "SELECT apellidos, nombres, fecha_nacimiento, lugar_nacimiento, nacionalidad, sexo, "
		. "(YEAR(CURRENT_DATE)-YEAR(fecha_nacimiento)) - (RIGHT(CURRENT_DATE,5)<RIGHT(fecha_nacimiento,5)) AS edad, "
		. "telefono_habitacion, telefono_trabajo, telefono_celular "
		. "FROM datos_personales "
		. "WHERE cedula='$cedula' ";

$query = mysql_db_query($conexion,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$apellidos = strtolower($registro->apellidos);
	$nombres = strtolower($registro->nombres);
	$fecha_nacimiento = $registro->fecha_nacimiento;
	$lugar_nacimiento = strtolower($registro->lugar_nacimiento);
	$nacionalidad = $registro->nacionalidad;
	$sexo = $registro->sexo;
	$edad = $registro->edad;
	$telefono_habitacion = $registro->telefono_habitacion;
	$telefono_trabajo = $registro->telefono_trabajo;
	$telefono_celular = $registro->telefono_celular;
}*/

$sqlcmd = "SELECT apellidos, nombres, fecha_nacimiento, lugar_nacimiento, nacionalidad, sexo, "
    . "(YEAR(CURDATE())-YEAR(fecha_nacimiento)) - (RIGHT(CURDATE(),5)<RIGHT(fecha_nacimiento,5)) AS edad, "
    . "telefono_habitacion, telefono_trabajo, telefono_celular "
    . "FROM datos_personales WHERE cedula='$cedula' ";

$resultado = mysqli_query($conexion, $sqlcmd);

if ($registro = mysqli_fetch_object($resultado)) {
    $apellidos = strtolower($registro->apellidos);
    $nombres = strtolower($registro->nombres);
    $fecha_nacimiento = $registro->fecha_nacimiento;
    $lugar_nacimiento = strtolower($registro->lugar_nacimiento);
    $nacionalidad = $registro->nacionalidad;
    $sexo = $registro->sexo;
    $edad = $registro->edad;
    $telefono_habitacion = $registro->telefono_habitacion;
    $telefono_trabajo = $registro->telefono_trabajo;
    $telefono_celular = $registro->telefono_celular;
}

// Formateo de valores
if ($apellidos && $nombres) {
    $apellidos_nombres = ucwords($apellidos) . ', ' . ucwords($nombres);
} elseif ($apellidos) {
    $apellidos_nombres = ucwords($apellidos);
} elseif ($nombres) {
    $apellidos_nombres = ucwords($nombres);
} else {
    $apellidos_nombres = 'No Existe Registro';
}

if ($fecha_nacimiento == '0000-00-00' || $fecha_nacimiento == '') {
    $fecha_nacimiento = 'No Existe Registro';
} else {
    $fecha_nacimiento = fecha($fecha_nacimiento);
}

if (!$lugar_nacimiento) {
    $lugar_nacimiento = 'No Existe Registro';
}
if (!$nacionalidad) {
    $nacionalidad = 'No Existe Registro';
}
if ($edad > 1 && $edad < 152) {
    $edad = $edad . ' a&ntilde;os';
} else {
    $edad = '';
}
if (!$telefono_habitacion) {
    $telefono_habitacion = 'No Existe Registro';
}
if (!$telefono_trabajo) {
    $telefono_trabajo = 'No Existe Registro';
}
if (!$telefono_celular) {
    $telefono_celular = 'No Existe Registro';
}
?>
<HTML>

<HEAD>
    <TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
</HEAD>

<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#00CC00" VLINK="#CC0000">
    <CENTER>
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/encabezado.php"); ?>
        <TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
            <TR>
                <TD ALIGN="left">
                    <A HREF="../">
                        <FONT SIZE="-2" FACE="Verdana">Home</FONT>
                    </A> :
                    <A HREF="ingreso_de_cedula.php">
                        <FONT SIZE="-2" FACE="Verdana">Consultar Notas</FONT>
                    </A> :
                    <A HREF="consulta_por_cedula.php?cedula=<?php echo $cedula ?>">
                        <FONT SIZE="-2" FACE="Verdana">Resultado</FONT>
                    </A> :
                    <FONT SIZE="-2" FACE="Verdana"><B>Detalle de las Notas</B></FONT>
                </TD>
            </TR>
        </TABLE>
        <BR>
        <IMG SRC="/sace/imagenes/menu_consultar_notas.jpg" WIDTH="234" HEIGHT="18">
        <BR><BR>
        <FONT SIZE="-1" FACE="Verdana">C&eacute;dula Consultada: <B><?php echo number_format($cedula, 0, ',', '.'); ?></B></FONT>
        <BR><BR>

        <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
            <TR>
                <TD BGCOLOR="#000099">
                    <FONT FACE="Verdana" COLOR="#FFFFFF"><B>Datos Personales</B></FONT>
                </TD>
            </TR>
        </TABLE>

        <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
            <TR>
                <TD WIDTH="250">
                    <FONT SIZE="-1" FACE="Verdana"><B>Apellidos, Nombres</B></FONT>
                </TD>
                <TD WIDTH="250">
                    <FONT SIZE="-1" FACE="Verdana"><B>Fecha de Nacimiento</B></FONT>
                </TD>
                <TD WIDTH="200">
                    <FONT SIZE="-1" FACE="Verdana"><B>Edad</B></FONT>
                </TD>
            </TR>
            <TR>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $apellidos_nombres ?></FONT>
                </TD>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $fecha_nacimiento ?></FONT>
                </TD>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $edad ?></FONT>
                </TD>
            </TR>
        </TABLE>

        <BR>

        <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
            <TR>
                <TD WIDTH="250">
                    <FONT SIZE="-1" FACE="Verdana"><B>Lugar de Nacimiento</B></FONT>
                </TD>
                <TD WIDTH="250">
                    <FONT SIZE="-1" FACE="Verdana"><B>Nacionalidad</B></FONT>
                </TD>
                <TD WIDTH="200">
                    <FONT SIZE="-1" FACE="Verdana"><B>Sexo</B></FONT>
                </TD>
            </TR>
            <TR>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo ucwords($lugar_nacimiento) ?></FONT>
                </TD>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo ucwords($nacionalidad) ?></FONT>
                </TD>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $sexo ?></FONT>
                </TD>
            </TR>
        </TABLE>

        <BR>

        <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
            <TR>
                <TD WIDTH="250">
                    <FONT SIZE="-1" FACE="Verdana"><B>Tel&eacute;fono Celular</B></FONT>
                </TD>
                <TD WIDTH="250">
                    <FONT SIZE="-1" FACE="Verdana"><B>Tel&eacute;fono Trabajo</B></FONT>
                </TD>
                <TD WIDTH="200">
                    <FONT SIZE="-1" FACE="Verdana"><B>Tel&eacute;fono Habitaci&oacute;n</B></FONT>
                </TD>
            </TR>
            <TR>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $telefono_celular ?></FONT>
                </TD>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $telefono_trabajo ?></FONT>
                </TD>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $telefono_habitacion ?></FONT>
                </TD>
            </TR>
        </TABLE>
        <HR SIZE="1" WIDTH="640">
        <?
        ###
        ### Busco en la Base de Datos, los Datos de la Cohorte del Postgrado que el Estudiante ha cursado o cursa
        ###

        $sqlcmd = "SELECT 
  dc.ciudad,
  oe.tipo,
  oe.mencion_especialidad,
  oe.codopest,
  oe.codsede,
  oe.actividad_especial_final,
  oe.periodos,
  oe.creditos,
  co.fecha_inicio,
  co.periodo_lectivo
FROM directorio_cippsv AS dc
JOIN oportunidades_estudio AS oe
  ON dc.codsede = oe.codsede
JOIN cohortes AS co
  ON  oe.codopest = co.codopest
  AND oe.codsede  = co.codsede
WHERE co.codcohorte = '$codcohorte'
LIMIT 0,30;
";

        $query = mysqli_query($conexion, $sqlcmd);

        if ($registro = mysqli_fetch_object($query)) {
            $ciudad = $registro->ciudad;
            $tipo = $registro->tipo;
            $mencion_especialidad = $registro->mencion_especialidad;
            $codopest = $registro->codopest;
            $codsede = $registro->codsede;
            $actividad_especial_final = $registro->actividad_especial_final;
            $periodos = $registro->periodos;
            $creditos = $registro->creditos;
            $fecha_inicio = $registro->fecha_inicio;
            $periodo_lectivo = $registro->periodo_lectivo;
        }
        ?>

        <BR>

        <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
            <TR>
                <TD WIDTH="700" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
                    <FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
                        <B>Informaci&oacute;n de la Especializaci&oacute;n</B>
                    </FONT>
                </TD>
            </TR>
        </TABLE>

        <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
            <TR>
                <TD WIDTH="175">
                    <FONT SIZE="-1" FACE="Verdana"><B>Ciudad</B></FONT>
                </TD>
                <TD WIDTH="175">
                    <FONT SIZE="-1" FACE="Verdana"><B>Tipo</B></FONT>
                </TD>
                <TD WIDTH="350">
                    <FONT SIZE="-1" FACE="Verdana"><B>Menci&oacute;n o Especialidad</B></FONT>
                </TD>
            </TR>
            <TR>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $ciudad ?></FONT>
                </TD>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $tipo ?></FONT>
                </TD>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $mencion_especialidad ?></FONT>
                </TD>
            </TR>
        </TABLE>

        <BR>

        <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
            <TR>
                <TD WIDTH="175">
                    <FONT SIZE="-1" FACE="Verdana"><B>Periodos</B></FONT>
                </TD>
                <TD WIDTH="175">
                    <FONT SIZE="-1" FACE="Verdana"><B>Cr&eacute;ditos</B></FONT>
                </TD>
                <TD WIDTH="350">
                    <FONT SIZE="-1" FACE="Verdana"><B>Actividad Especial Final</B></FONT>
                </TD>
            </TR>
            <TR>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $periodos ?></FONT>
                </TD>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $creditos ?></FONT>
                </TD>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $actividad_especial_final ?></FONT>
                </TD>
            </TR>
        </TABLE>

        <BR>

        <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
            <TR>
                <TD WIDTH="175">
                    <FONT SIZE="-1" FACE="Verdana"><B>Cohorte</B></FONT>
                </TD>
                <TD WIDTH="175">
                    <FONT SIZE="-1" FACE="Verdana"><B>Periodo Lectivo</B></FONT>
                </TD>
                <TD WIDTH="350">
                    <FONT SIZE="-1" FACE="Verdana"><B>Fecha de Inicio</B></FONT>
                </TD>
            </TR>
            <TR>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $codcohorte ?></FONT>
                </TD>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo $periodo_lectivo ?></FONT>
                </TD>
                <TD>
                    <FONT SIZE="-1" FACE="Verdana"><?php echo fecha($fecha_inicio) ?></FONT>
                </TD>
            </TR>
        </TABLE>

        <BR><BR>

        <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
            <TR>
                <TD WIDTH="700">
                    <FONT FACE="Verdana" COLOR="#000099"><B>Record de Notas</B></FONT>
                </TD>
                <TD WIDTH="100">
                    <P> </P>
                </TD>
            </TR>
        </TABLE>

        <TABLE BORDER="0" WIDTH="700" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
            <TR>
                <TD WIDTH="60">
                    <FONT SIZE="-2" FACE="Verdana" COLOR="#FFFFFF"><B>C&oacute;digo</B></FONT>
                </TD>
                <TD WIDTH="20">
                    <FONT SIZE="-2" FACE="Verdana" COLOR="#FFFFFF"><B>Cred.</B></FONT>
                </TD>
                <TD WIDTH="270">
                    <FONT SIZE="-2" FACE="Verdana" COLOR="#FFFFFF"><B>Asignatura</B></FONT>
                </TD>
                <TD WIDTH="70" ALIGN="center">
                    <FONT SIZE="-2" FACE="Verdana" COLOR="#FFFFFF"><B>Nota</B></FONT>
                </TD>
                <TD WIDTH="120">
                    <FONT SIZE="-2" FACE="Verdana" COLOR="#FFFFFF"><B>Fecha Aprobaci&oacute;n</B></FONT>
                </TD>
                <TD WIDTH="160">
                    <FONT SIZE="-2" FACE="Verdana" COLOR="#FFFFFF"><B>Profesor</B></FONT>
                </TD>
            </TR>
        </TABLE>

        <?
        /*
	$sqlcmd = "SELECT registro_actas.codasig, pensum_estudios.asignatura, record_notas.calificacion, "
			. "registro_actas.fecha_aprobacion "
			. "FROM registro_actas, pensum_estudios, record_notas "
			. "WHERE registro_actas.codasig=pensum_estudios.codasig AND pensum_estudios.codsede='$codsede' AND "
			. "pensum_estudios.codopest='$codopest' AND registro_actas.codacta=record_notas.codacta AND "
			. "record_notas.cedula='$cedula' "
			. "ORDER BY pensum_estudios.periodos, pensum_estudios.codasig";
*/


        ###
        ### Busco en la Base de Datos, las Calificaciones del Alumno segun la Cohorte suministrada
        ###
        /*
$sqlcmd = "SELECT registro_actas.codacta, registro_actas.codasig, pensum_estudios.asignatura, pensum_estudios.creditos, pensum_estudios.periodos, "
		. "record_notas.calificacion, registro_actas.fecha_aprobacion, registro_actas.cedula_profesor "
		. "FROM registro_actas, pensum_estudios, record_notas, cohortes "
		. "WHERE registro_actas.codasig=pensum_estudios.codasig AND pensum_estudios.codsede='$codsede' AND pensum_estudios.codopest='$codopest' "
		. "AND registro_actas.codacta=record_notas.codacta AND "
		. "cohortes.codcohorte=registro_actas.codcohorte AND cohortes.codcohorte='$codcohorte' AND "
		. "record_notas.cedula='$cedula' "
		. "ORDER BY pensum_estudios.periodos, pensum_estudios.codasig ";
*/


        $sqlcmd = "CREATE TEMPORARY TABLE actas_consulta_detalle_temp "
            . "SELECT registro_actas.codasig, pensum_estudios.asignatura, pensum_estudios.creditos, pensum_estudios.periodos, "
            . "record_notas.calificacion, registro_actas.fecha_aprobacion, pensum_estudios.codasig_imp, record_notas.codacta, "
            . "registro_actas.cedula_profesor, record_notas.mid "
            . "FROM registro_actas, pensum_estudios, record_notas, cohortes "
            . "WHERE registro_actas.codasig=pensum_estudios.codasig AND pensum_estudios.codsede='$codsede' AND "
            . "pensum_estudios.codopest='$codopest' AND registro_actas.codacta=record_notas.codacta AND "
            . "cohortes.codcohorte=registro_actas.codcohorte AND cohortes.codcohorte='$codcohorte' AND record_notas.cedula='$cedula'";

        $query = mysqli_query($conexion, $sqlcmd);
        if (!$query) {
            die("Error al crear tabla temporal: " . mysqli_error($conexion));
        }

        // Insertar datos en la tabla temporal
        $sqlcmd = "INSERT INTO actas_consulta_detalle_temp (codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta, cedula_profesor, mid) "
            . "SELECT multiactas.codasig, pensum_estudios.asignatura, pensum_estudios.creditos, pensum_estudios.periodos, "
            . "record_notas.calificacion, multiactas.fecha_aprobacion, pensum_estudios.codasig_imp, multiactas.codacta, "
            . "multiactas.cedula_profesor1 AS cedula_profesor, record_notas.mid "
            . "FROM pensum_estudios, multiactas, record_notas "
            . "WHERE pensum_estudios.codsede='$codsede' AND pensum_estudios.codopest='$codopest' AND "
            . "pensum_estudios.codasig=multiactas.codasig AND record_notas.mid=multiactas.mid AND "
            . "multiactas.codcohorte='$codcohorte' AND record_notas.cedula='$cedula'";

        $query = mysqli_query($conexion, $sqlcmd);
        if (!$query) {
            die("Error al insertar en tabla temporal: " . mysqli_error($conexion));
        }

        // Seleccionar datos de la tabla temporal
        $sqlcmd = "SELECT codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta, cedula_profesor, mid "
            . "FROM actas_consulta_detalle_temp "
            . "ORDER BY periodos, codasig, codacta";

        $query = mysqli_query($conexion, $sqlcmd);
        if (!$query) {
            die("Error al consultar tabla temporal: " . mysqli_error($conexion));
        }

        // Inicialización de variables para acumulados
        $notas = 0;
        $total_creditos = 0;
        $notas_periodo = 0;
        $total_creditos_periodo = 0;
        $periodos_actual = null;
        $bg_celda = '#FFFFFF';

        echo '<TABLE BORDER="0" WIDTH="700" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">' . "\n";

        while ($registro = mysqli_fetch_object($query)) {
            $creditos = $registro->creditos;
            $calificacion = $registro->calificacion;
            $mid = $registro->mid;
            $codasig = $registro->codasig;
            $asignatura = $registro->asignatura;
            $periodos = $registro->periodos;
            $fecha_aprobacion = $registro->fecha_aprobacion;
            $codasig_imp = $registro->codasig_imp;
            $codacta = $registro->codacta;
            $cedula_profesor = $registro->cedula_profesor;
            $total_creditos_periodo = $total_creditos_periodo + $creditos;
            if ($periodos_actual !== null && $periodos != $periodos_actual) {
                echo '</TABLE>' . "\n\n";

                if ($notas >= 1 && $total_creditos >= 1) {
        ?>
                    <TABLE BORDER="0" WIDTH="700" CELLSPACING="1" CELLPADDING="2">
                        <TR>
                            <TD WIDTH="350" ALIGN="left" VALIGN="top">
                                <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
                                    Indice Acad&eacute;mico en este Periodo: <B><?php echo number_format(($notas_periodo / $total_creditos_periodo), 2, ',', '') ?></B>
                                </FONT>
                            </TD>
                            <TD WIDTH="350" ALIGN="right" VALIGN="top">
                                <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
                                    Indice Acad&eacute;mico Acumulado: <B><?php echo number_format(($notas / $total_creditos), 2, ',', '') ?></B>
                                </FONT>
                            </TD>
                        </TR>
                    </TABLE>
            <?php
                    $notas_periodo = 0;
                    $total_creditos_periodo = 0;
                }

                echo '<BR>';
                echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva"><B>Periodo: ' . $periodos . '</B></FONT>' . "\n\n";
                $periodos_actual = $periodos;

                echo '<TABLE BORDER="0" WIDTH="700" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">' . "\n";
            } else {
                if ($periodos != $periodos_actual) {
                    echo '<BR>';
                    echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva"><B>Periodo: ' . $periodos . '</B></FONT>' . "\n\n";
                    $periodos_actual = $periodos;

                    echo '<TABLE BORDER="0" WIDTH="700" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">' . "\n";
                }
            }

            if ($calificacion >= 1 && $calificacion <= 20) {
                $notas += ($calificacion * $creditos);
                $total_creditos += $creditos;
                $notas_periodo += ($calificacion * $creditos);
                $total_creditos_periodo += $creditos;
            }

            if ($fecha_aprobacion == '0000-00-00' || $fecha_aprobacion == "") {
                $fecha_aprobacion = "";
            } else {
                //$fecha_aprobacion = fecha($fecha_aprobacion, corto);
            }

            // Alternar color de celda
            $bg_celda = ($bg_celda == '#CCCCCC') ? '#FFFFFF' : '#CCCCCC';

            $apellidos_nombres = datos_profesor($cedula_profesor, $conexion);
            ?>

            <TR>
                <TD WIDTH="60" ALIGN="left" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
                    <?php
                    if ($calificacion >= 1 && $calificacion <= 14) {
                        if (empty($mid)) {
                    ?>
                            <a href="javascript:popup('_blank','../ingresos/detalle_acta.php?codacta=<?php echo urlencode($codacta); ?>',640,510)">
                                <font size="-2" face="Verdana,Arial,Geneva" color="#3300FF"><b><?php echo htmlspecialchars($codasig_imp); ?></b></font>
                            </a>
                        <?php
                        } else {
                        ?>
                            <A HREF="javascript:popup('_blank', '../ingresos/detalle_multiacta.php?codacta=<?php echo $codacta ?>&mid=<?php echo $mid ?>',650,350)">
                                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B><?php echo $codasig_imp ?></B></FONT>
                            </A>
                        <?php
                        }
                    } else {
                        if (empty($mid)) {
                        ?>
                            <A HREF="javascript:popup('_blank', '../ingresos/detalle_acta.php?codacta=<?php echo $codacta ?>',640,510)">
                                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B><?php echo $codasig_imp ?></B></FONT>
                            </A>
                        <?php
                        } else {
                        ?>
                            <A HREF="javascript:popup('_blank', '../ingresos/detalle_multiacta.php?codacta=<?php echo $codacta ?>&mid=<?php echo $mid ?>',650,350)">
                                <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B><?php echo $codasig_imp ?></B></FONT>
                            </A>
                    <?php
                        }
                    }
                    ?>
                </TD>
                <TD WIDTH="20" ALIGN="center" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
                    <FONT SIZE="-2" FACE="Verdana,Arial,Geneva"><?php echo $creditos ?></FONT>
                </TD>
                <TD WIDTH="270" ALIGN="left" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
                    <?php
                    $curso_d = strtolower(substr($codacta, -3, 2));
                    if ($curso_d == "cd") {
                        $asignatura .= ' <B>(CD)</B>';
                    }
                    if ($calificacion >= 1 && $calificacion <= 14) {
                        echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>' . $asignatura . '</B></FONT>';
                    } else {
                        echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">' . $asignatura . '</FONT>';
                    }
                    ?>
                </TD>
                <TD WIDTH="70" ALIGN="center" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
                    <?php
                    switch ($calificacion) {
                        case 404:
                            $calificacion = 'No Curs&oacute;';
                            break;
                        case 99:
                            $calificacion = 'Reprobado';
                            break;
                        case 100:
                            $calificacion = 'Aprobado';
                            break;
                        case 110:
                            $calificacion = 'Meritorio';
                            break;
                        case 120:
                            $calificacion = 'Excelencia';
                            break;
                        case 212:
                            $calificacion = 'Equivalencia';
                            break;
                    }
                    if ($calificacion >= 1 && $calificacion <= 14) {
                        echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>' . $calificacion . '</B></FONT>';
                    } else {
                        echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">' . $calificacion . '</FONT>';
                    }
                    ?>
                </TD>
                <TD WIDTH="120" ALIGN="right" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
                    <FONT SIZE="-2" FACE="Verdana,Arial,Geneva"><?php echo $fecha_aprobacion ?></FONT>
                </TD>
                <TD WIDTH="160" ALIGN="left" VALIGN="top" BGCOLOR="<?php echo $bg_celda ?>">
                    <FONT SIZE="-2" FACE="Verdana,Arial,Geneva">&nbsp;<?php echo $apellidos_nombres ?></FONT>
                </TD>
            </TR>
        <?php
            $cedula_profesor = "";
            $apellidos_nombres = "";
        }
        echo '</TABLE>';

        if ($notas >= 1 && $total_creditos >= 1) {
        ?>
            <TABLE BORDER="0" WIDTH="700" CELLSPACING="1" CELLPADDING="2">
                <TR>
                    <TD WIDTH="350" ALIGN="left" VALIGN="top">
                        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
                            Indice Acad&eacute;mico en este Periodo: <B><?php echo number_format(($notas_periodo / $total_creditos_periodo), 2, ',', '') ?></B>
                        </FONT>
                    </TD>
                    <TD WIDTH="350" ALIGN="right" VALIGN="top">
                        <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
                            <B>Indice Acad&eacute;mico Acumulado: <?php echo number_format(($notas / $total_creditos), 2, ',', '') ?></B>
                        </FONT>
                    </TD>
                </TR>
            </TABLE>
        <?php
        }

        // Borrar tabla temporal
        $sqlcmd = "DROP TABLE actas_consulta_detalle_temp";
        $query = mysqli_query($conexion, $sqlcmd);
        if (!$query) {
            die("Error al eliminar tabla temporal: " . mysqli_error($conexion));
        }

        ?>

        <BR>
        <HR SIZE="1" WIDTH="640">
        <?

        #include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");

        ?>

    </CENTER>

</BODY>

</HTML>