<?php
session_start();
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/conexion.php");


if (!$conexion) die('Error al conectar con la base de datos');

if (isset($_POST['Buscar']) || isset($_POST['Buscar_x'])) {
	$_patron_nombre = mysqli_real_escape_string($conexion, $_POST['_patron_nombre']);
	$_patron_apellido = mysqli_real_escape_string($conexion, $_POST['_patron_apellido']);
	$_patron_ci = mysqli_real_escape_string($conexion, $_POST['_patron_ci']);

	$sqlcmd = "SELECT cedula_profesor, apellidos_nombres, nombres
               FROM profesores_cippsv
               WHERE nombres LIKE '%$_patron_nombre%'
               AND apellidos_nombres LIKE '%$_patron_apellido%'
               AND cedula_profesor LIKE '%$_patron_ci%'
               ORDER BY apellidos_nombres
               LIMIT 30";

	$query = mysqli_query($conexion, $sqlcmd);
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>CIPPSV | Buscar Profesores</title>
	<meta charset="UTF-8">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#0000FF" alink="#0000FF" vlink="#0000FF">
	<center>

		<form action="buscar_profesor.php" method="POST">
			<table border="0" width="600" cellspacing="2" cellpadding="2">
				<tr bgcolor="#000099">
					<td colspan="4">
						<font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Buscar Profesores</b></font>
					</td>
				</tr>
			</table>

			<table border="0" width="600" cellspacing="2" cellpadding="2">
				<tr>
					<td width="200">
						<font size="-2" face="Verdana,Arial,Geneva"><b>Nombre</b></font>
					</td>
					<td width="200">
						<font size="-2" face="Verdana,Arial,Geneva"><b>Apellido</b></font>
					</td>
					<td width="100">
						<font size="-2" face="Verdana,Arial,Geneva"><b>Cedula</b></font>
					</td>
					<td width="100"></td>
				</tr>
				<tr>
					<td><input type="text" name="_patron_nombre" value="<?php echo $_POST['_patron_nombre']; ?>" size="17" maxlength="15"></td>
					<td><input type="text" name="_patron_apellido" value="<?php echo $_POST['_patron_apellido']; ?>" size="17" maxlength="15"></td>
					<td><input type="text" name="_patron_ci" value="<?php echo $_POST['_patron_ci']; ?>" size="12" maxlength="10"></td>
					<td align="right">
						<input type="submit" name="Buscar" value="Buscar">
						<input type="hidden" name="Buscar_x" value="Buscar">
					</td>
				</tr>
			</table>
		</form>

		<br>

		<?php if (isset($query) && mysqli_num_rows($query) > 0): ?>
			<table border="0" width="600" cellspacing="0" cellpadding="2">
				<tr style="background-color:#000099; color:#FFFFFF;">
					<td width="400"><b>Nombre(s) y Apellido(s)</b></td>
					<td width="200"><b>C&eacute;dula de Identidad</b></td>
				</tr>
				<?php
				$bg_celda = '#FFFFFF';
				while ($registro = mysqli_fetch_assoc($query)) {
					$cedula_profesor = $registro['cedula_profesor'];
					$apellidos_nombres = ucwords(strtolower(trim($registro['nombres'] . ' ' . $registro['apellidos_nombres'])));
					$bg_celda = ($bg_celda == '#CCCCCC') ? '#FFFFFF' : '#CCCCCC';
				?>
					<tr style="background-color:<?php echo $bg_celda ?>; cursor:pointer;"
						onclick="window.opener.document.ingresando_acta._ci_profesor.value='<?php echo $cedula_profesor ?>'; window.close();"
						onmouseover="this.style.backgroundColor='#FFFF99';"
						onmouseout="this.style.backgroundColor='<?php echo $bg_celda ?>';">
						<td><?php echo $apellidos_nombres; ?></td>
						<td><?php echo $cedula_profesor; ?></td>
					</tr>
				<?php } ?>
			</table>
		<?php elseif (isset($query)): ?>
			<p>No se encontraron resultados.</p>
		<?php endif; ?>

	</center>
</body>

</html>
<?php mysqli_close($conexion); ?>