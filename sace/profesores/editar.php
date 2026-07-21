<?php
session_start();
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/conexion.php");

$error_ci = false;
$error_num_ci = false;
$si_actualizo = false;

if (isset($_POST['_cancelar'])) {
    header("Location: index.php");
    exit;
}

$cedula = '';
if (isset($_POST['cedula'])) {
    $cedula = trim($_POST['cedula']);
} elseif (isset($_GET['cedula'])) {
    $cedula = trim($_GET['cedula']);
}
$apellidos = isset($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
$nombres = isset($_POST['nombres']) ? trim($_POST['nombres']) : '';
$cid = isset($_POST['cid']) ? $_POST['cid'] : '';
echo $_editando = isset($_POST['_editando']);

if ($cedula && !preg_match('/^[0-9]+$/', $cedula)) {
    $error_num_ci = true;
}

if ($_editando && !$error_num_ci) {
    if ($apellidos || $nombres) {
        $sql = "SELECT cid FROM profesores_cippsv WHERE cedula_profesor='$cedula'";
        $result = mysqli_query($conexion, $sql);
        $new_cid = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $new_cid = $row['cid'];
        }

        if ($new_cid == $cid) {
            $si_actualizo = true;
        } else {
            $sql = "SELECT count(cedula_profesor) AS cantidad FROM profesores_cippsv WHERE cedula_profesor='$cedula'";
            $result = mysqli_query($conexion, $sql);
            $cantidad = 0;
            if ($row = mysqli_fetch_assoc($result)) {
                $cantidad = $row['cantidad'];
            }
            if ($cantidad > 0) {
                $error_ci = true;
            } else {
                $si_actualizo = true;
            }
        }

        if ($si_actualizo) {
            $sql = "UPDATE profesores_cippsv SET cedula_profesor='$cedula', apellidos_nombres='$apellidos', nombres='$nombres' WHERE cid='$cid'";
            mysqli_query($conexion, $sql);
            header("Location: index.php");
            exit;
        }
    }
}

// Recuperar datos para mostrar
$sql = "SELECT cid, apellidos_nombres, nombres FROM profesores_cippsv WHERE cedula_profesor='$cedula'";
$result = mysqli_query($conexion, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $cid = $row['cid'];
    $apellidos = $row['apellidos_nombres'];
    $nombres = $row['nombres'];
}
?>
<html>
<head>
    <title>Phones: Editando</title>
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#0000FF" alink="#009900" vlink="#CC0000">
<center>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/encabezado.php"); ?>
<br>

<form action="editar.php" method="post">
<table border="0" width="700" cellspacing="0">
<tr><td align="center"><font size="-1" face="Verdana,Arial,Geneva"><b>Editando los Datos de un Profesor</b></font></td></tr>
</table>
<br>

<?php if ($error_ci): ?>
<br>
<table border="0" width="700" cellspacing="0" cellpadding="5">
<tr><td align="center"><font size="-1" face="Verdana,Arial,Geneva" color="#FF0000"><b>El N&uacute;mero de C&eacute;dula de Identidad ya Existe en la BD, favor revisar.</b></font></td></tr>
</table>
<br>
<?php endif; ?>

<?php if ($error_num_ci): ?>
<br>
<table border="0" width="700" cellspacing="0" cellpadding="5">
<tr><td align="center"><font size="-1" face="Verdana,Arial,Geneva" color="#FF0000"><b>El N&uacute;mero de C&eacute;dula de Identidad no es V&aacute;lido, favor revisar.</b></font></td></tr>
</table>
<br>
<?php endif; ?>

<table border="0" width="700" cellspacing="0" cellpadding="5">
<tr>
    <td width="250" align="right"><font size="-2" face="Verdana,Arial,Geneva"><b>C&eacute;dula de Ident.</b></font></td>
    <td width="450">
        <input type="text" name="cedula" value="<?php echo htmlspecialchars($cedula); ?>" size="14" maxlength="12">
        <font size="-2" face="Verdana,Arial,Geneva">&nbsp; Ej: <b>12421101</b> (Sin puntos o Comas)</font>
    </td>
</tr>
<tr>
    <td align="right"><font size="-2" face="Verdana,Arial,Geneva"><b>Apellidos</b></font></td>
    <td>
        <input type="text" name="apellidos" value="<?php echo htmlspecialchars($apellidos); ?>" size="42" maxlength="40">
        <font size="-2" face="Verdana,Arial,Geneva">&nbsp; Ej: <b>Croes B.</b></font>
    </td>
</tr>
<tr>
    <td align="right"><font size="-2" face="Verdana,Arial,Geneva"><b>Nombres</b></font></td>
    <td>
        <input type="text" name="nombres" value="<?php echo htmlspecialchars($nombres); ?>" size="42" maxlength="40">
        <font size="-2" face="Verdana,Arial,Geneva">&nbsp; Ej: <b>Valentina M.</b></font>
    </td>
</tr>
</table>

<br>
<input type="hidden" name="cid" value="<?php echo $cid; ?>">
<input type="submit" name="_editando" value="Editar">
<input type="submit" name="_cancelar" value="Cancelar">
</center>
</form>
</body>
</html>