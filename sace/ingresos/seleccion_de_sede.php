<?php
session_start();

// Redirige si se recibe _codsede por POST
if (!empty($_POST['_codsede'])) {
	$_SESSION['_codsede'] = $_POST['_codsede'];
	header("Location: seleccion_postgrado.php?_codsede=" . urlencode($_POST['_codsede']));
	exit;
}

// Includes necesarios
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/creditos.php");
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_fecha.php");
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/conexion.php"); // Debe crear $mysqli

?>
<!DOCTYPE html>
<html>
<head>
	<title>CIPPSV Web Site | Sistema de Control de Estudios</title>
	<meta charset="UTF-8">
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#0000FF" alink="#00CC00" vlink="#CC0000">
<center>

<?php include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/encabezado.php"); ?>

<table border="0" width="100%" cellspacing="1" cellpadding="1">
<tr>
	<td align="left" valign="top">
		<a href="../"><font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Home</b></font></a>
		<font size="-2" face="Verdana,Arial,Geneva" color="#000000">:</font> 
		<font size="-2" face="Verdana,Arial,Geneva" color="#000000"><b>Selección de Sede</b></font>
	</td>
</tr>
</table>

<br>
<img src="/sace/imagenes/titulos_de_home/titulo_ingreso.jpg" alt="" width="380" height="20" border="0">
<br><br><br>

<font size="-1" face="Verdana,Arial,Geneva"><b>Seleccione la Sede o el Núcleo con el cual desea Trabajar</b></font>
<br><br>

<table border="0" width="570" cellspacing="1" cellpadding="2" bgcolor="#000099">
<tr>
	<td width="150" align="left" valign="top">
		<font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Ciudad</b></font>
	</td>
	<td width="170" align="left" valign="top">
		<font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Estado o Provincia</b></font>
	</td>
	<td width="130" align="left" valign="top">
		<font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Modalidad</b></font>
	</td>
	<td width="120" align="left" valign="top">&nbsp;</td>
</tr>

<?php
$sqlcmd = "SELECT codsede, modalidad, ciudad, edo_prov FROM directorio_cippsv ORDER BY ciudad";
$result = mysqli_query($conexion, $sqlcmd);

if ($result) {
	while ($registro = mysqli_fetch_object($result)) {
?>
	<form action="seleccion_de_sede.php" method="POST">
	<tr>
		<td bgcolor="#FFFFFF"><font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($registro->ciudad) ?></font></td>
		<td bgcolor="#FFFFFF"><font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($registro->edo_prov) ?></font></td>
		<td bgcolor="#FFFFFF"><font size="-1" face="Verdana,Arial,Geneva"><?= htmlspecialchars($registro->modalidad) ?></font></td>
		<td bgcolor="#FFFFFF" align="center">
			<input type="submit" name="seleccionar" value="Seleccionar">
			<input type="hidden" name="_codsede" value="<?= htmlspecialchars($registro->codsede) ?>">
		</td>
	</tr>
	</form>
<?php
	}
} else {
	echo '<tr><td colspan="4" bgcolor="#FFFFFF"><font face="Verdana" size="-1">Error al consultar las sedes.</font></td></tr>';
}
?>
</table>

<br><br>

<?php
// include ($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/pie_de_pagina.php");
?>

</center>
</body>
</html>