<?php
session_start();

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_datos_profesor.php");

$cantidad_por_pagina = "10";	### Muestro de Tantos en Tantos Alumnos por Listado

if (! $_desde) $_desde = '0';	### Variable que utiliza el Paginator

$cantidad = $_desde;

// Obtener parámetros
$codacta = isset($_GET['codacta']) ? $_GET['codacta'] : '';
echo $mid = isset($_GET['mid']) ? $_GET['mid'] : '';
$_desde = isset($_GET['_desde']) ? (int)$_GET['_desde'] : 0;
$cantidad_por_pagina = 10;

// Simulación de variables precargadas (puedes cambiarlas si vienen de otro lado)
// $fecha_aprobacion = "10/07/2025";
// $cedula_profesor1 = "12345678";
// $cedula_profesor2 = "23456789";
// $cedula_profesor3 = "34567890";
// $apellidos_nombres1 = "Pérez, Juan";
// $apellidos_nombres2 = "Gómez, Ana";
// $apellidos_nombres3 = "Rojas, Luis";
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="ISO-8859-1">
	<title>Detalle Multiacta</title>
	<style>
		body { font-family: Verdana, Arial, Geneva; font-size: 11px; }
		table { border-collapse: collapse; width: 600px; }
		th, td { padding: 4px; }
		th { background-color: #000099; color: #FFFFFF; font-weight: bold; }
		.alt { background-color: #CCCCCC; }
		.normal { background-color: #FFFFFF; }
		.falla { color: #3300FF; font-weight: bold; }
	</style>
</head>
<body>
<center>

<!-- Tabla de Profesores -->
<table border="0" cellspacing="1" cellpadding="2">
	<tr>
		<td><b>Fecha Aprobación</b></td>
		<td><b>Cédula del Profesor</b></td>
		<td><b>Profesor</b></td>
	</tr>
	<tr>
		<td><?= $fecha_aprobacion ?></td>
		<td><?= $cedula_profesor1 ?><br><?= $cedula_profesor2 ?><br><?= $cedula_profesor3 ?></td>
		<td><?= $apellidos_nombres1 ?><br><?= $apellidos_nombres2 ?><br><?= $apellidos_nombres3 ?></td>
	</tr>
</table>
<br>

<!-- Tabla de Estudiantes -->
<table border="0" cellspacing="1" cellpadding="2">
	<tr>
		<th width="30">Núm.</th>
		<th width="80">Cédula</th>
		<th width="150">Apellidos</th>
		<th width="150">Nombres</th>
		<th width="60">Nota</th>
		<th width="80">Equivalencia</th>
	</tr>

<?php
$sql = "SELECT cedula, calificacion, codeq FROM record_notas
		WHERE codacta='$codacta' AND mid='$mid'
		ORDER BY cedula
		LIMIT $_desde, $cantidad_por_pagina";

$result = mysqli_query($conexion, $sql);
$cantidad = 0;
$bg = false;

while ($row = mysqli_fetch_assoc($result)) {
	$cantidad++;
	$cedula = $row['cedula'];
	$calificacion = $row['calificacion'];
	$codeq = $row['codeq'];

	// Consultar nombre del estudiante
	$sql2 = "SELECT apellidos, nombres FROM datos_personales WHERE cedula='$cedula' LIMIT 1";
	$res2 = mysqli_query($conexion, $sql2);
	$datos = mysqli_fetch_assoc($res2);

	$apellidos = ucwords(strtolower($datos['apellidos']));
	$nombres = ucwords(strtolower($datos['nombres']));

	// Alternar color
	$fila_clase = ($bg = !$bg) ? 'normal' : 'alt';

	// Nota en texto
	$falla = ($calificacion >= 1 && $calificacion <= 14);
	$nota_visible = $calificacion;

	switch ($calificacion) {
		case 404: $nota_visible = 'No Cursó'; break;
		case 99:  $nota_visible = 'Reprobado'; break;
		case 100: $nota_visible = 'Aprobado'; break;
		case 110: $nota_visible = 'Meritorio'; break;
		case 120: $nota_visible = 'Excelencia'; break;
		case 212: $nota_visible = 'Equivalencia'; break;
	}

	echo "<tr class=\"$fila_clase\">
		<td align='left'>&nbsp; $cantidad</td>
		<td align='right'>" . ($falla ? "<span class='falla'>" : "") . number_format($cedula) . ($falla ? "</span>" : "") . "</td>
		<td>" . ($falla ? "<span class='falla'>$apellidos</span>" : $apellidos) . "</td>
		<td>" . ($falla ? "<span class='falla'>$nombres</span>" : $nombres) . "</td>
		<td>" . ($falla ? "<span class='falla'>$nota_visible</span>" : $nota_visible) . "</td>
		<td>$codeq</td>
	</tr>";
}
?>
</table>

<?php
// Promedio del grupo
$sql = "SELECT AVG(calificacion) AS promedio FROM record_notas
		WHERE calificacion > 0 AND calificacion <= 20 AND codacta='$codacta' AND mid='$mid'";
$res = mysqli_query($conexion, $sql);
$row = mysqli_fetch_assoc($res);
$promedio = $row['promedio'];


if ($promedio > 0) {
	echo "<br><table><tr><td align='center'>
		<b style='color:#000099;'>Promedio del Alumnado en esta Asignatura: " . number_format($promedio, 2, ',', '') . "</b>
	</td></tr></table>";
}

// Paginación
$sql = "SELECT COUNT(*) AS total FROM record_notas WHERE codacta='$codacta' AND mid='$mid'";
$res = mysqli_query($conexion, $sql);
$row = mysqli_fetch_assoc($res);
$total = $row['total'];

$mostrar_fin = $_desde + $cantidad_por_pagina;
if ($mostrar_fin > $total) $mostrar_fin = $total;

if ($total > $cantidad_por_pagina) {
	echo "<br><table><tr>
		<td width='150' align='left' class='alt'>";
	if ($_desde > 0) {
		$nuevo_desde = $_desde - $cantidad_por_pagina;
		echo "&lt;- <a href=\"detalle_multiacta.php?_desde=$nuevo_desde&codacta=$codacta&mid=$mid\">Anteriores</a>";
	} else {
		echo "&lt;- Anteriores";
	}
	echo "</td><td width='300' align='center' class='alt'>
		<b>Mostrando " . ($_desde+1) . " a $mostrar_fin de $total Estudiantes</b>
		</td><td width='150' align='right' class='alt'>";
	if ($mostrar_fin < $total) {
		echo "<a href=\"detalle_multiacta.php?_desde=$mostrar_fin&codacta=$codacta&mid=$mid\">Siguientes</a> -&gt;";
	} else {
		echo "Siguientes -&gt;";
	}
	echo "</td></tr></table>";
}

mysqli_close($conexion);
?>

</center>
</body>
</html>