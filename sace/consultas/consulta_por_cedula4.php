<?php

session_start();
###
###		Este script desplega los Datos Personales y el/los diferentes Postgrado(s)
###		realizados por el Estudiante si fuera el caso. Este script da la opcion de
###		ver con mas Detalle tanto los Datos Personales como las Calificaciones, asi
###		como preparar las Notas certificadas para mandarlas a Imprimir.
###


###
### Si no pasa una CI voy a la pagina anterior
###
echo 'la cedula es:' . $_SESSION['cedula'];
if ($_SESSION['cedula']) {
  if (!preg_match("/^[0-9]+$/", $_SESSION['cedula'])) {
    $_SESSION['error'] = 'La cédula debe tener entre 7 y 8 dígitos numéricos.';

    header("Location: ingreso_de_cedula.php");
    exit;
  }
  $cedula = $_SESSION['cedula'];
}


###
### Los Clasicos Includes
###
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/creditos.php");

include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_fecha.php");

//include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");
require_once dirname(__FILE__) . '/../includes/conexion.php';


if (!$conexion) {
  exit('Error de conexión: ' . mysqli_connect_error());
}

###
### Chequeo si existen Datos para la CI que he recibido del script anterior
###

// Función para ejecutar consultas con un solo parámetro y devolver un arreglo con resultados
function ejecutarConsultaSimple($conexion, $sql, $param)
{
  $resultados = array();

  if ($stmt = mysqli_prepare($conexion, $sql)) {
    mysqli_stmt_bind_param($stmt, 's', $param);
    if (mysqli_stmt_execute($stmt)) {
      mysqli_stmt_bind_result($stmt, $valor);
      // Recopilamos todos los resultados (en caso de múltiples filas)
      while (mysqli_stmt_fetch($stmt)) {
        $resultados[] = $valor;
      }
    } else {
      error_log('Error en ejecución: ' . mysqli_stmt_error($stmt));
      exit('Error en la ejecución de la consulta.');
    }
    mysqli_stmt_close($stmt);
  } else {
    error_log('Error en prepare: ' . mysqli_error($conexion));
    exit('Error en preparación de la consulta.');
  }
  return $resultados;
}

// 1) Consulta cantidad de datos personales
$sql1 = 'SELECT COUNT(*) FROM datos_personales WHERE cedula = ?';
$cantidadArray = ejecutarConsultaSimple($conexion, $sql1, $cedula);
$cantidad = isset($cantidadArray[0]) ? $cantidadArray[0] : 0;

// 2) Consulta cantidad de notas por cohorte
$sql2 = 'SELECT COUNT(*) FROM record_notas rn JOIN registro_actas ra ON rn.codacta = ra.codacta WHERE rn.cedula = ? GROUP BY ra.codcohorte';
$cantidadNotasArray = ejecutarConsultaSimple($conexion, $sql2, $cedula);

$cantidad_notas_total = 0;
foreach ($cantidadNotasArray as $cantidadNotas) {
  $cantidad_notas_total += $cantidadNotas;
}

// Mostrar resultados
//echo "Total de registros personales: $cantidad\n";
//echo "Total de notas registradas: $cantidad_notas_total\n";

###
### Reviso si Existen Datos Personales para esa CI, o si existen por lo menos Notas Asociadas al Estudiante
###

if ($cantidad < 1) {
  if ($cantidad_notas > 0)  $cantidad = 1;
}


###
### Si la Cedula de Identidad existe en la Base de Datos, Busco los Datos Personales
###

// Función para obtener una fila asociativa desde una consulta con un parámetro
function obtenerFilaUnica($conexion, $sql, $param)
{
  $fila = array();

  if ($stmt = mysqli_prepare($conexion, $sql)) {
    mysqli_stmt_bind_param($stmt, 's', $param);
    if (mysqli_stmt_execute($stmt)) {
      // Obtener metadatos de las columnas
      $meta = mysqli_stmt_result_metadata($stmt);
      if (!$meta) {
        error_log('Error obteniendo metadatos: ' . mysqli_stmt_error($stmt));
        exit('Error interno al procesar resultados.');
      }

      $fields = array();
      $row = array();

      // Crear referencias para el bind
      while ($field = mysqli_fetch_field($meta)) {
        $fields[] = &$row[$field->name];
      }

      // Bind dinámico
      call_user_func_array(array($stmt, 'bind_result'), $fields);

      if (mysqli_stmt_fetch($stmt)) {
        foreach ($row as $key => $val) {
          $fila[$key] = $val;
        }
      }

      mysqli_free_result($meta);
    } else {
      error_log('Error en ejecución: ' . mysqli_stmt_error($stmt));
      exit('Error al ejecutar la consulta.');
    }

    mysqli_stmt_close($stmt);
  } else {
    error_log('Error en prepare: ' . mysqli_error($conexion));
    exit('Error al preparar la consulta.');
  }

  return $fila;
}

// ---------------------------------------
// USO
// ---------------------------------------
if ($cantidad > 0) {
  $sql = '
      SELECT
        apellidos,
        nombres,
        fecha_nacimiento,
        lugar_nacimiento,
        nacionalidad,
        sexo,
        (YEAR(CURDATE()) - YEAR(fecha_nacimiento))
          - (RIGHT(CURDATE(),5) < RIGHT(fecha_nacimiento,5)) AS edad,
        telefono_habitacion,
        telefono_trabajo,
        telefono_celular
      FROM datos_personales
      WHERE cedula = ?
    ';

  $datos = obtenerFilaUnica($conexion, $sql, $cedula);

  if (empty($datos)) {
    exit('No existe un registro con esa cédula');
  }

  // Procesar campos específicos
  $apellidos        = $datos['apellidos'];
  $nombres          = $datos['nombres'];
  $lugar_nacimiento = $datos['lugar_nacimiento'];
  $fecha_nacimiento = $datos['fecha_nacimiento'];

  // Los demás campos quedan disponibles:
  // $datos['fecha_nacimiento'], $datos['nacionalidad'], $datos['sexo'],
  // $datos['edad'], $datos['telefono_habitacion'], etc.

  if (($apellidos) and ($nombres)) $apellidos_nombres = ucwords($apellidos) . ', ' . ucwords($nombres);
  if (($apellidos) and (! $nombres)) $apellidos_nombres = ucwords($apellidos);
  if ((! $apellidos) and ($nombres)) $apellidos_nombres = ucwords($nombres);
  if ((! $apellidos) and (! $nombres)) $apellidos_nombres = 'No Existe Registro';


  if (($fecha_nacimiento == '0000-00-00') or ($fecha_nacimiento == "")) {
    $fecha_nacimiento = 'No Existe Registro';
  } else {

    $fecha_nacimiento = fecha($fecha_nacimiento);
  }


  if ($lugar_nacimiento == "") $lugar_nacimiento = 'No Existe Registro';


  if ($nacionalidad == "") $nacionalidad = 'No Existe Registro';


  if (($edad > 1) and ($edad < 152)) {
    $edad = $edad . ' a&ntilde;os';
  } else {

    $edad = '';
  }

  if ($telefono_habitacion == "") $telefono_habitacion = "No Existe Registro";

  if ($telefono_trabajo == "") $telefono_trabajo = "No Existe Registro";

  if ($telefono_celular == "") $telefono_celular = "No Existe Registro";
}

?>
<HTML>

<HEAD>
  <TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
  <META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>

<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#0000FF" VLINK="#0000FF">

  <CENTER>

    <?
    include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/encabezado.php");
    ?>


    <TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
      <TR>
        <TD WIDTH="100%" ALIGN="left" VALIGN="top">

          <A HREF="../">
            <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT>
          </A>

          <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT>

          <A HREF="ingreso_de_cedula.php">
            <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Consultar Notas de un Estudiante</B></FONT>
          </A>

          <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT>

          <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Resultado</B></FONT>
        </TD>
      </TR>
    </TABLE>

    <BR>

    <IMG SRC="/sace/imagenes/menu_consultar_notas.jpg" ALT="" WIDTH="234" HEIGHT="18" BORDER="0">

    <BR><BR>


    <FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000000">
      C&eacute;dula de Identidad Consultada: <B><? echo strtr(number_format($cedula), ",", ".") ?></B>
    </FONT>

    <BR><BR>

    <?
    ###
    ### Si NO existe la CI en la Base de Datos, muestro un mensaje, de lo contrario Muestro los Datos Obtenidos
    ###

    if ($cantidad < 1) {
    ?>

      <BR><BR><BR>

      <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
        <TR>
          <TD WIDTH="700" ALIGN="center" VALIGN="top">
            <FONT FACE="Verdana,Arial,Geneva">
              <B>No se Encontr&oacute; ningun Registro que concuerde<BR><BR>
                con el N&uacute;mero de C&eacute;dula de Identidad suministrado.</B>
            </FONT>
          </TD>
        </TR>
      </TABLE>

    <?
    } else {
    ?>
      <table width="700" cellspacing="2" cellpadding="2">
        <tr>
          <td bgcolor="#000099" align="left" valign="top">
            <font face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Datos Personales</b></font>
          </td>
        </tr>
      </table>

      <table width="700" cellspacing="2" cellpadding="2">
        <tr>
          <td width="250"><b>Apellidos, Nombres</b></td>
          <td width="250"><b>Fecha de Nacimiento</b></td>
          <td width="200"><b>Edad</b></td>
        </tr>
        <tr>
          <td><?= $apellidos_nombres ?></td>
          <td><?= $fecha_nacimiento ?></td>
          <td><?= $edad ?></td>
        </tr>
      </table>

      <br>

      <table width="700" cellspacing="2" cellpadding="2">
        <tr>
          <td width="250"><b>Lugar de Nacimiento</b></td>
          <td width="250"><b>Nacionalidad</b></td>
          <td width="200"><b>Sexo</b></td>
        </tr>
        <tr>
          <td><?= ucwords($lugar_nacimiento) ?></td>
          <td><?= ucwords($nacionalidad) ?></td>
          <td><?= $sexo ?></td>
        </tr>
      </table>

      <br>

      <table width="700" cellspacing="2" cellpadding="2">
        <tr>
          <td width="250"><b>Teléfono Celular</b></td>
          <td width="250"><b>Teléfono Trabajo</b></td>
          <td width="200"><b>Teléfono Habitación</b></td>
        </tr>
        <tr>
          <td><?= $telefono_celular ?></td>
          <td><?= $telefono_trabajo ?></td>
          <td><?= $telefono_habitacion ?></td>
        </tr>
      </table>

      <table width="700" cellspacing="10" cellpadding="2">
        <tr>
          <td width="600"></td>
          <td width="100" align="center" bgcolor="#0066FF">
            <a href="detalle_datos_personales.php?cedula=<?= $cedula ?>">
              <font size="-2" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Ver más detalle</b></font>
            </a>
          </td>
        </tr>
      </table>

      <hr size="1" width="640">
    <?php
    }
    ###
    ### Busco su Record de Notas, primero busco en cuantas Cohortes aparece este Estudiante
    ###
    // 2) Obtén todos los codcohorte
    function render_postgrado($codcohorte, $detalles, $cedula)
    {
    ?>
      <BR>
      <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
        <TR>
          <TD WIDTH="700" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
            <FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF"><B>Información del Postgrado</B></FONT>
          </TD>
        </TR>
      </TABLE>

      <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
        <TR>
          <TD WIDTH="175"><B>Ciudad</B></TD>
          <TD WIDTH="175"><B>Tipo</B></TD>
          <TD WIDTH="350"><B>Mención o Especialidad</B></TD>
        </TR>
        <TR>
          <TD><?= htmlspecialchars($detalles['ciudad']) ?></TD>
          <TD><?= htmlspecialchars($detalles['tipo']) ?></TD>
          <TD><?= htmlspecialchars($detalles['mencion']) ?></TD>
        </TR>
      </TABLE>

      <BR>

      <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
        <TR>
          <TD WIDTH="175"><B>Cohorte</B></TD>
          <TD WIDTH="175"><B>Periodo Lectivo</B></TD>
          <TD WIDTH="350"><B>Fecha de Inicio</B></TD>
        </TR>
        <TR>
          <TD><?= htmlspecialchars($codcohorte) ?></TD>
          <TD><?= htmlspecialchars($detalles['periodo_lectivo']) ?></TD>
          <TD><?= fecha($detalles['fecha_inicio']) ?></TD>
        </TR>
      </TABLE>

      <BR>

      <!-- Enlaces a notas -->
      <TABLE BORDER="0" WIDTH="700" CELLSPACING="2" CELLPADDING="2">
        <TR>
          <TD WIDTH="280" ALIGN="left" VALIGN="top">
            <FONT FACE="Verdana,Arial,Geneva" COLOR="#000099">
              <B>Record de Notas</B>
            </FONT>
          </TD>
          <TD WIDTH="120" ALIGN="left" VALIGN="top">

            <TABLE BORDER="0" WIDTH="120" CELLSPACING="2" CELLPADDING="2">
              <TR>
                <TD WIDTH="120" ALIGN="center" VALIGN="top" BGCOLOR="#0066FF">
                  <A HREF="notas_certificadas.php?cedula=<? echo $cedula ?>&codcohorte=<? echo $codcohorte ?>">
                    <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
                      <B>Notas Simples</B>
                    </FONT>
                  </A>
                </TD>
              </TR>
            </TABLE>

          </TD>
          <TD WIDTH="200" ALIGN="left" VALIGN="top">

            <TABLE BORDER="0" WIDTH="200" CELLSPACING="2" CELLPADDING="2">
              <TR>
                <TD WIDTH="200" ALIGN="center" VALIGN="top" BGCOLOR="#0066FF">
                  <A HREF="notas_certificadas_egresados.php?cedula=<? echo $cedula ?>&codcohorte=<? echo $codcohorte ?>">
                    <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
                      <B>Notas Certificadas</B>
                    </FONT>
                  </A>
                </TD>
              </TR>
            </TABLE>

          </TD>
          <TD WIDTH="100" ALIGN="left" VALIGN="top">

            <TABLE BORDER="0" WIDTH="100" CELLSPACING="2" CELLPADDING="2">
              <TR>
                <TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="#0066FF">
                  <A HREF="detalle_de_calificaciones.php?cedula=<? echo $cedula ?>&codcohorte=<? echo $codcohorte ?>">
                    <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
                      <B>Ver mas detalle</B>
                    </FONT>
                  </A>
                </TD>
              </TR>
            </TABLE>

          </TD>
        </TR>
      </TABLE>
      <BR>
    <?php
    }

    // --- Función para renderizar la tabla de notas ---
    function render_notas($conexion, $codcohorte, $detalles, $cedula)
    {
      mysqli_query($conexion, "DROP TEMPORARY TABLE IF EXISTS actas_consulta_temp");
      mysqli_query($conexion, "
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
    ");

      // Insertar actas normales
      $sql_insert1 = "
        INSERT INTO actas_consulta_temp (codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta)
        SELECT ra.codasig, pe.asignatura, pe.creditos, pe.periodos, rn.calificacion, ra.fecha_aprobacion, pe.codasig_imp, rn.codacta
        FROM registro_actas ra
        JOIN pensum_estudios pe ON ra.codasig = pe.codasig AND pe.codsede = ? AND pe.codopest = ?
        JOIN record_notas rn ON ra.codacta = rn.codacta
        WHERE ra.codcohorte = ? AND rn.cedula = ?";
      $stmt = mysqli_prepare($conexion, $sql_insert1);
      mysqli_stmt_bind_param($stmt, 'sssd', $detalles['codsede'], $detalles['codopest'], $codcohorte, $cedula);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);

      // Insertar multiactas
      $sql_insert2 = "
        INSERT INTO actas_consulta_temp (codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta)
        SELECT ma.codasig, pe.asignatura, pe.creditos, pe.periodos, rn.calificacion, ma.fecha_aprobacion, pe.codasig_imp, ma.codacta
        FROM multiactas ma
        JOIN pensum_estudios pe ON ma.codasig = pe.codasig AND pe.codsede = ? AND pe.codopest = ?
        JOIN record_notas rn ON ma.mid = rn.mid
        WHERE ma.codcohorte = ? AND rn.cedula = ?";
      $stmt = mysqli_prepare($conexion, $sql_insert2);
      mysqli_stmt_bind_param($stmt, 'sssd', $detalles['codsede'], $detalles['codopest'], $codcohorte, $cedula);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);

      // Cabecera de la tabla de notas
    ?>
      <TABLE BORDER="0" WIDTH="700" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
        <tr>
          <td width="100" align="left" valign="top">
            <a href="consulta_por_cedula4.php?cedula=<?= urlencode($cedula) ?>&_orden=codasig">
              <font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Código</b></font>
            </a>
          </td>
          <td width="350" align="left" valign="top">
            <a href="consulta_por_cedula4.php?cedula=<?= urlencode($cedula) ?>&_orden=asignatura">
              <font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Asignatura</b></font>
            </a>
          </td>
          <td width="100" align="center" valign="top">
            <a href="consulta_por_cedula4.php?cedula=<?= urlencode($cedula) ?>&_orden=calificacion">
              <font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Nota</b></font>
            </a>
          </td>
          <td width="150" align="right" valign="top">
            <a href="consulta_por_cedula4.php?cedula=<?= urlencode($cedula) ?>&_orden=fecha_aprobacion">
              <font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Fecha Aprobación</b></font>
            </a>
          </td>
        </tr>
      </TABLE>
    <?php

      // Mostrar notas
      $_orden = $_GET['_orden'];
      //$_orden = 'periodos, codasig, codacta';
      $result = mysqli_query($conexion, "SELECT * FROM actas_consulta_temp ORDER BY $_orden");
      $bg_celda = '#CCCCCC';
      $notas = 0;
      $total_creditos = 0;

      echo '<TABLE BORDER="0" WIDTH="700" CELLSPACING="1" CELLPADDING="2">';
      while ($row = mysqli_fetch_assoc($result)) {
        $fecha = ($row['fecha_aprobacion'] == '0000-00-00' || $row['fecha_aprobacion'] == '') ? '' : fecha($row['fecha_aprobacion'], 'corto');

        if (is_numeric($row['calificacion']) && $row['calificacion'] >= 1 && $row['calificacion'] <= 20 && $row['creditos'] > 0) {
          $notas += $row['calificacion'] * $row['creditos'];
          $total_creditos += $row['creditos'];
        }

        $mapa_calificaciones = array(404 => 'No Cursó', 99 => 'Reprobado', 100 => 'Aprobado', 110 => 'Meritorio', 120 => 'Excelencia', 212 => 'Equivalencia');
        $calificacion_display = isset($mapa_calificaciones[$row['calificacion']]) ? $mapa_calificaciones[$row['calificacion']] : $row['calificacion'];
        $bg_celda = ($bg_celda == '#CCCCCC') ? '#FFFFFF' : '#CCCCCC';

        $color_nota = '';
        if ((is_numeric($row['calificacion']) && $row['calificacion'] <= 14) || $row['calificacion'] == 99) {
          $color_nota = 'COLOR="#3300FF"'; // azul
        }

        echo '<TR>';
        echo '<TD WIDTH="100" ALIGN="left" VALIGN="top" BGCOLOR="' . $bg_celda . '"><FONT SIZE="-1" FACE="Verdana,Arial,Geneva" ' . $color_nota . '>' . $row['codasig_imp'] . '</FONT></TD>';
        echo '<TD WIDTH="350" ALIGN="left" VALIGN="top" BGCOLOR="' . $bg_celda . '"><FONT SIZE="-1" FACE="Verdana,Arial,Geneva" ' . $color_nota . '>' . $row['asignatura'] . '</FONT></TD>';
        echo '<TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="' . $bg_celda . '"><FONT SIZE="-1" FACE="Verdana,Arial,Geneva" ' . $color_nota . '>' . $calificacion_display . '</FONT></TD>';
        echo '<TD WIDTH="150" ALIGN="right" VALIGN="top" BGCOLOR="' . $bg_celda . '"><FONT SIZE="-1" FACE="Verdana,Arial,Geneva" ' . $color_nota . '>' . $fecha . '</FONT></TD>';
        echo '</TR>';
      }
      echo '</TABLE>';
      mysqli_free_result($result);

      $indice = ($total_creditos > 0) ? number_format($notas / $total_creditos, 2, ',', '') : '0.00';
      echo '<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2"><TR><TD WIDTH="600" ALIGN="center"><B>Índice Académico: ' . $indice . '</B></TD></TR></TABLE><BR><HR SIZE="1" WIDTH="640">';
      mysqli_query($conexion, "DROP TEMPORARY TABLE IF EXISTS actas_consulta_temp");
    }

    // --- Obtener cohortes ---
    $sql1 = "SELECT ra.codcohorte FROM record_notas rn JOIN registro_actas ra ON rn.codacta = ra.codacta WHERE rn.cedula=? GROUP BY ra.codcohorte";
    $codcohortes = array();
    $stmt = mysqli_prepare($conexion, $sql1);
    mysqli_stmt_bind_param($stmt, 's', $cedula);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $codcohorte);
    while (mysqli_stmt_fetch($stmt)) $codcohortes[] = $codcohorte;
    mysqli_stmt_close($stmt);

    // --- Procesar cada cohorte ---
    $sql2 = "SELECT dc.ciudad, oe.tipo, oe.mencion_especialidad, oe.codopest, oe.codsede, oe.periodos, oe.creditos, co.fecha_inicio, co.periodo_lectivo
         FROM directorio_cippsv dc
         JOIN oportunidades_estudio oe ON dc.codsede = oe.codsede
         JOIN cohortes co ON oe.codopest = co.codopest AND oe.codsede = co.codsede
         WHERE co.codcohorte = ?";
    foreach ($codcohortes as $codcohorte) {
      $stmt2 = mysqli_prepare($conexion, $sql2);
      mysqli_stmt_bind_param($stmt2, 's', $codcohorte);
      mysqli_stmt_execute($stmt2);
      mysqli_stmt_bind_result($stmt2, $ciudad, $tipo, $mencion, $codopest, $codsede, $periodos, $creditos, $fecha_inicio, $periodo_lectivo);
      $detalles = array();
      while (mysqli_stmt_fetch($stmt2)) {
        $detalles = array(
          'ciudad' => $ciudad,
          'tipo' => $tipo,
          'mencion' => $mencion,
          'codopest' => $codopest,
          'codsede' => $codsede,
          'periodos' => $periodos,
          'creditos' => $creditos,
          'fecha_inicio' => $fecha_inicio,
          'periodo_lectivo' => $periodo_lectivo
        );
      }
      mysqli_stmt_close($stmt2);

      render_postgrado($codcohorte, $detalles, $cedula);
      render_notas($conexion, $codcohorte, $detalles, $cedula);
    }
    ?>