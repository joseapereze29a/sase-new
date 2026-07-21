<?php
session_start();

###
### Validación de la cédula recibida por GET o POST
###
$cedula = isset($_SESSION['cedula']) ? $_SESSION['cedula'] : '';

if (!preg_match("/^[0-9]+$/", $cedula)) {
	header("Location: ingreso_de_cedula.php");
	exit;
}

###
### Includes clásicos
###
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/creditos.php");
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_fecha.php");
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/conexion.php");

###
### Verifico si existe datos personales o notas para esa cédula
###
$cantidad = 0;
$cantidad_notas = 0;

// Consulta para contar datos personales
$sql = "SELECT COUNT(*) AS cantidad FROM datos_personales WHERE cedula=?";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "s", $cedula);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $cantidad);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Consulta para contar notas (agrupado por cohorte)
$sql = "SELECT COUNT(*) as cantidad_notas 
        FROM record_notas rn
        INNER JOIN registro_actas ra ON rn.codacta = ra.codacta
        WHERE rn.cedula=?
        GROUP BY ra.codcohorte";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "s", $cedula);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
	mysqli_stmt_bind_result($stmt, $cantidad_notas_tmp);
	while (mysqli_stmt_fetch($stmt)) {
		$cantidad_notas = $cantidad_notas_tmp; // toma el último valor, basta para saber que hay notas
	}
}
mysqli_stmt_close($stmt);

// Si no hay datos personales pero sí notas, asumimos que existe registro
if ($cantidad < 1 && $cantidad_notas > 0) {
	$cantidad = 1;
}

###
### Si hay datos personales, los obtengo
###
if ($cantidad > 0) {
	$sql = "SELECT apellidos, nombres, fecha_nacimiento, lugar_nacimiento, nacionalidad, sexo, 
            (YEAR(CURDATE()) - YEAR(fecha_nacimiento)) - (RIGHT(CURDATE(),5) < RIGHT(fecha_nacimiento,5)) AS edad,
            telefono_habitacion, telefono_trabajo, telefono_celular
            FROM datos_personales WHERE cedula=?";
	$stmt = mysqli_prepare($conexion, $sql);
	mysqli_stmt_bind_param($stmt, "s", $cedula);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result(
		$stmt,
		$apellidos,
		$nombres,
		$fecha_nacimiento,
		$lugar_nacimiento,
		$nacionalidad,
		$sexo,
		$edad,
		$telefono_habitacion,
		$telefono_trabajo,
		$telefono_celular
	);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);

	// Formateo los datos para mostrar
	$apellidos = strtolower($apellidos);
	$nombres = strtolower($nombres);
	$apellidos_nombres = trim(ucwords($apellidos) . ', ' . ucwords($nombres));
	if (!$apellidos && $nombres) $apellidos_nombres = ucwords($nombres);
	if ($apellidos && !$nombres) $apellidos_nombres = ucwords($apellidos);
	if (!$apellidos && !$nombres) $apellidos_nombres = 'No Existe Registro';

	if ($fecha_nacimiento == '0000-00-00' || $fecha_nacimiento == '') {
		$fecha_nacimiento_fmt = 'No Existe Registro';
	} else {
		$fecha_nacimiento_fmt = fecha($fecha_nacimiento);
	}

	$lugar_nacimiento = $lugar_nacimiento ? ucwords(strtolower($lugar_nacimiento)) : 'No Existe Registro';
	$nacionalidad = $nacionalidad ? ucwords(strtolower($nacionalidad)) : 'No Existe Registro';

	$edad = ($edad > 1 && $edad < 152) ? $edad . ' años' : '';

	$telefono_habitacion = $telefono_habitacion ? $telefono_habitacion : 'No Existe Registro';
	$telefono_trabajo = $telefono_trabajo ? $telefono_trabajo : 'No Existe Registro';
	$telefono_celular = $telefono_celular ? $telefono_celular : 'No Existe Registro';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8" />
	<title>CIPPSV Web Site | Sistema de Control de Estudios</title>
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#0000FF" alink="#0000FF" vlink="#0000FF">
	<center>
		<?php include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/encabezado.php"); ?>

		<table border="0" width="100%" cellspacing="1" cellpadding="1">
			<tr>
				<td width="100%" align="left" valign="top">
					<a href="../">
						<font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Home</b></font>
					</a>
					<font size="-2" face="Verdana,Arial,Geneva" color="#000000">:</font>
					<a href="ingreso_de_cedula.php">
						<font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Consultar Notas de un Estudiante</b></font>
					</a>
					<font size="-2" face="Verdana,Arial,Geneva" color="#000000">:</font>
					<font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Resultado</b></font>
				</td>
			</tr>
		</table>
		<br>
		<img src="/sace/imagenes/menu_consultar_notas.jpg" alt="" width="234" height="18" border="0" />
		<br><br>
		<font size="-1" face="Verdana,Arial,Geneva" color="#000000">
			C&eacute;dula de Identidad Consultada: <b><?php echo number_format($cedula, 0, ',', '.'); ?></b>
		</font>
		<br><br>

		<?php if ($cantidad < 1): ?>
			<br><br><br>
			<table border="0" width="700" cellspacing="2" cellpadding="2">
				<tr>
					<td width="700" align="center" valign="top">
						<font face="Verdana,Arial,Geneva"><b>No se Encontr&oacute; ningun Registro que concuerde<br><br>con el N&uacute;mero de C&eacute;dula de Identidad suministrado.</b></font>
					</td>
				</tr>
			</table>
		<?php else: ?>

			<table border="0" width="700" cellspacing="2" cellpadding="2">
				<tr bgcolor="#000099">
					<td colspan="3" align="left" valign="top">
						<font face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Datos Personales</b></font>
					</td>
				</tr>
				<tr>
					<td width="250" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><b>Apellidos, Nombres</b></font>
					</td>
					<td width="250" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><b>Fecha de Nacimiento</b></font>
					</td>
					<td width="200" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><b>Edad</b></font>
					</td>
				</tr>
				<tr>
					<td width="250" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><?php echo $apellidos_nombres; ?></font>
					</td>
					<td width="250" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><?php echo $fecha_nacimiento_fmt; ?></font>
					</td>
					<td width="200" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><?php echo $edad; ?></font>
					</td>
				</tr>
			</table>

			<br>

			<table border="0" width="700" cellspacing="2" cellpadding="2">
				<tr>
					<td width="250" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><b>Lugar de Nacimiento</b></font>
					</td>
					<td width="250" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><b>Nacionalidad</b></font>
					</td>
					<td width="200" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><b>Sexo</b></font>
					</td>
				</tr>
				<tr>
					<td width="250" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><?php echo $lugar_nacimiento; ?></font>
					</td>
					<td width="250" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><?php echo $nacionalidad; ?></font>
					</td>
					<td width="200" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><?php echo $sexo; ?></font>
					</td>
				</tr>
			</table>

			<br>

			<table border="0" width="700" cellspacing="2" cellpadding="2">
				<tr>
					<td width="250" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><b>Tel&eacute;fono Celular</b></font>
					</td>
					<td width="250" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><b>Tel&eacute;fono Trabajo</b></font>
					</td>
					<td width="200" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><b>Tel&eacute;fono Habitaci&oacute;n</b></font>
					</td>
				</tr>
				<tr>
					<td width="250" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><?php echo $telefono_celular; ?></font>
					</td>
					<td width="250" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><?php echo $telefono_trabajo; ?></font>
					</td>
					<td width="200" align="left" valign="top">
						<font size="-1" face="Verdana,Arial,Geneva"><?php echo $telefono_habitacion; ?></font>
					</td>
				</tr>
			</table>

			<table border="0" width="700" cellspacing="10" cellpadding="2">
				<tr>
					<td width="600" align="left" valign="top">&nbsp;</td>
					<td width="100" align="center" valign="top" bgcolor="#0066FF">
						<a href="detalle_datos_personales.php?cedula=<?php echo urlencode($cedula); ?>">
							<font size="-2" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Ver más detalle</b></font>
						</a>
					</td>
				</tr>
			</table>

			<hr size="1" width="640">

			<?php
			// Obtengo las cohortes en las que tiene notas
			$sql = "SELECT DISTINCT ra.codcohorte
            FROM record_notas rn
            INNER JOIN registro_actas ra ON rn.codacta = ra.codacta
            WHERE rn.cedula=?";
			$stmt = mysqli_prepare($conexion, $sql);
			mysqli_stmt_bind_param($stmt, "s", $cedula);
			mysqli_stmt_execute($stmt);

			// Ahora hay que bindear la variable para obtener el resultado
			mysqli_stmt_bind_result($stmt, $codcohorte);

			$cohortes = array();

			while (mysqli_stmt_fetch($stmt)) {
				$cohortes[] = $codcohorte;
			}

			mysqli_stmt_close($stmt);

			foreach ($cohortes as $codcohorte) {
				// --- OBTENER DATOS DEL POSTGRADO PARA LA COHORTE ---
				$sql = "SELECT dc.ciudad, oe.tipo, oe.mencion_especialidad, oe.codopest, oe.codsede, oe.periodos, oe.creditos, co.fecha_inicio, co.periodo_lectivo
			FROM cohortes co
			INNER JOIN oportunidades_estudio oe ON co.codopest = oe.codopest AND co.codsede = oe.codsede
			INNER JOIN directorio_cippsv dc ON oe.codsede = dc.codsede
			WHERE co.codcohorte=?";
				$stmt0 = mysqli_prepare($conexion, $sql);
				if (!$stmt0) die("Error en prepare: " . mysqli_error($conexion));
				mysqli_stmt_bind_param($stmt0, "s", $codcohorte);
				mysqli_stmt_execute($stmt0);
				mysqli_stmt_bind_result($stmt0, $ciudad, $tipo, $mencion_especialidad, $codopest, $codsede, $periodos, $creditos, $fecha_inicio, $periodo_lectivo);
				mysqli_stmt_store_result($stmt0);

				while (mysqli_stmt_fetch($stmt0)) {
					// --- BLOQUE DE INFORMACIÓN DEL POSTGRADO ---
					echo "<table border='0' cellpadding='2' cellspacing='1' width='600'>";
					echo "<tr><td colspan='2' bgcolor='#003399'><font face='Verdana' size='2' color='#FFFFFF'><b>POSTGRADO CURSADO</b></font></td></tr>";

					echo "<tr>";
					echo "<td width='200' bgcolor='#CCCCCC'><font face='Verdana' size='-1'><b>Tipo de Postgrado</b></font></td>";
					echo "<td bgcolor='#FFFFFF'><font face='Verdana' size='-1'>{$tipo}</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td bgcolor='#CCCCCC'><font face='Verdana' size='-1'><b>Especialidad</b></font></td>";
					echo "<td bgcolor='#FFFFFF'><font face='Verdana' size='-1'>{$mencion_especialidad}</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td bgcolor='#CCCCCC'><font face='Verdana' size='-1'><b>Ciudad</b></font></td>";
					echo "<td bgcolor='#FFFFFF'><font face='Verdana' size='-1'>{$ciudad}</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td bgcolor='#CCCCCC'><font face='Verdana' size='-1'><b>Créditos Totales</b></font></td>";
					echo "<td bgcolor='#FFFFFF'><font face='Verdana' size='-1'>{$creditos}</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td bgcolor='#CCCCCC'><font face='Verdana' size='-1'><b>Períodos Académicos</b></font></td>";
					echo "<td bgcolor='#FFFFFF'><font face='Verdana' size='-1'>{$periodos}</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td bgcolor='#CCCCCC'><font face='Verdana' size='-1'><b>Fecha de Inicio</b></font></td>";
					echo "<td bgcolor='#FFFFFF'><font face='Verdana' size='-1'>" . fecha($fecha_inicio, 'corto') . "</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td bgcolor='#CCCCCC'><font face='Verdana' size='-1'><b>Periodo Lectivo</b></font></td>";
					echo "<td bgcolor='#FFFFFF'><font face='Verdana' size='-1'>{$periodo_lectivo}</font></td>";
					echo "</tr>";

					echo "</table><br>";

					// --- CREAR Y LLENAR LA TABLA TEMPORAL ---
					mysqli_query($conexion, "DROP TEMPORARY TABLE IF EXISTS actas_consulta_temp");
					mysqli_query($conexion, "CREATE TEMPORARY TABLE actas_consulta_temp (
			codasig VARCHAR(255),
			asignatura VARCHAR(255),
			creditos INT,
			periodos INT,
			calificacion VARCHAR(255),
			fecha_aprobacion DATE,
			codasig_imp VARCHAR(255),
			codacta VARCHAR(255)
		)");

					// INSERT 1: REGISTRO_ACTAS
					$sql1 = "INSERT INTO actas_consulta_temp (codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta)
				SELECT ra.codasig, pe.asignatura, pe.creditos, pe.periodos, rn.calificacion, ra.fecha_aprobacion, pe.codasig_imp, rn.codacta
				FROM registro_actas ra
				JOIN pensum_estudios pe ON ra.codasig = pe.codasig AND pe.codsede=? AND pe.codopest=?
				JOIN record_notas rn ON ra.codacta = rn.codacta
				WHERE ra.codcohorte=? AND rn.cedula=?";
					$stmt1 = mysqli_prepare($conexion, $sql1);
					mysqli_stmt_bind_param($stmt1, "sssd", $codsede, $codopest, $codcohorte, $cedula);
					mysqli_stmt_execute($stmt1);
					mysqli_stmt_close($stmt1);

					// INSERT 2: MULTIACTAS
					$sql2 = "INSERT INTO actas_consulta_temp (codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta)
				SELECT ma.codasig, pe.asignatura, pe.creditos, pe.periodos, rn.calificacion, ma.fecha_aprobacion, pe.codasig_imp, ma.codacta
				FROM pensum_estudios pe
				JOIN multiactas ma ON pe.codasig = ma.codasig AND pe.codsede=? AND pe.codopest=?
				JOIN record_notas rn ON ma.mid = rn.mid
				WHERE ma.codcohorte=? AND rn.cedula=?";
					$stmt2 = mysqli_prepare($conexion, $sql2);
					mysqli_stmt_bind_param($stmt2, "sssd", $codsede, $codopest, $codcohorte, $cedula);
					mysqli_stmt_execute($stmt2);
					mysqli_stmt_close($stmt2);

					// --- CONSULTAR NOTAS DESDE TEMPORAL ---
					if (!$_orden) $_orden = 'periodos, codasig, codacta';
					$sql_select = "SELECT codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta
					   FROM actas_consulta_temp ORDER BY " . mysqli_real_escape_string($conexion, $_orden);
					$result = mysqli_query($conexion, $sql_select);

					$notas = 0;
					$total_creditos = 0;
					$bg_celda = '#CCCCCC';

					echo "<table border='0' width='600' cellspacing='1' cellpadding='2'>";
					echo "<tr><td><b>Código</b></td><td><b>Asignatura</b></td><td><b>Nota</b></td><td><b>Fecha Aprobación</b></td></tr>";

					if ($result && mysqli_num_rows($result) > 0) {
						while ($r = mysqli_fetch_assoc($result)) {
							$cal = $r['calificacion'];
							if (($cal >= 1) && ($cal <= 20) && ($r['creditos'] > 0)) {
								$notas += ($cal * $r['creditos']);
								$total_creditos += $r['creditos'];
							}
							switch ($cal) {
								case 404:
									$cal = "No Cursó";
									break;
								case 99:
									$cal = "Reprobado";
									break;
								case 100:
									$cal = "Aprobado";
									break;
								case 110:
									$cal = "Meritorio";
									break;
								case 120:
									$cal = "Excelencia";
									break;
								case 212:
									$cal = "Equivalencia";
									break;
							}
							$f_aprob = ($r['fecha_aprobacion'] == '0000-00-00' || !$r['fecha_aprobacion']) ? '' : fecha($r['fecha_aprobacion'], 'corto');
							$bg_celda = ($bg_celda == '#CCCCCC') ? '#FFFFFF' : '#CCCCCC';

							echo "<tr>
						<td width='100' align='left' bgcolor='$bg_celda'><font size='-1' face='Verdana'>{$r['codasig_imp']}</font></td>
						<td width='350' align='left' bgcolor='$bg_celda'><font size='-1' face='Verdana'>{$r['asignatura']}</font></td>
						<td width='100' align='center' bgcolor='$bg_celda'><font size='-1' face='Verdana'>{$cal}</font></td>
						<td width='150' align='right' bgcolor='$bg_celda'><font size='-1' face='Verdana'>{$f_aprob}</font></td>
					  </tr>";
						}
						mysqli_free_result($result);
					}
					echo "</table>";

					// Mostrar índice académico
					echo "<table border='0' width='600' cellspacing='1' cellpadding='2'><tr><td align='center'>";
					echo "<font size='-1' face='Verdana'><b>Índice Académico: ";
					echo ($total_creditos > 0) ? number_format($notas / $total_creditos, 2, ',', '') : "0,00";
					echo "</b></font></td></tr></table><br><hr width='640'>";
				}
				mysqli_stmt_close($stmt0);
			}

			?>
		<?php endif; ?>

	</center>
</body>

</html>