<?php
session_start();
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/conexion.php");

// Valores predeterminados
$cantidad_por_pagina = 15;
$_orden = isset($_POST['_orden']) ? $_POST['_orden'] : (isset($_GET['_orden']) ? $_GET['_orden'] : 'apellidos_nombres');
$_desde = isset($_GET['_desde']) ? intval($_GET['_desde']) : 0;
$_patron = isset($_POST['_patron']) ? trim($_POST['_patron']) : '';

$where = "";
if (!empty($_patron)) {
    $valores = explode(' ', $_patron);
    $filtros = array();
    foreach ($valores as $valor) {
        $valor = $conexion->real_escape_string($valor);
        $filtros[] = "(apellidos_nombres LIKE '%$valor%' OR nombres LIKE '%$valor%' OR cedula_profesor LIKE '%$valor%')";
    }
    $where = "WHERE " . implode(" OR ", $filtros);
}

// Query principal
$sqlcmd = "SELECT cid, cedula_profesor, apellidos_nombres, nombres 
           FROM profesores_cippsv 
           $where 
           ORDER BY $_orden 
           LIMIT $_desde, $cantidad_por_pagina";

$query = mysqli_query($conexion, $sqlcmd);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sistema Automatizado de Control de Estudios</title>
    <meta charset="UTF-8">
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#0000FF" alink="#0000FF" vlink="#0000FF">
<center>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/encabezado.php"); ?>
<br>
<font size="-1" face="Verdana,Arial,Geneva"><b>Listado de Profesores del C.I.P.P.S.V.</b></font>

<form action="index.php" method="post">
    <table border="0" width="700" cellspacing="1" cellpadding="2">
        <tr>
            <td width="450" align="left">
                <font size="-1" face="Verdana,Arial,Geneva">
                    &nbsp;[ <a href="agregar.php"><b>Agregar Profesor</b></a> ]
                </font>
            </td>
            <td width="75" align="right"><font size="-1" face="Verdana,Arial,Geneva">Búsqueda</font></td>
            <td width="100" align="center"><input type="text" name="_patron" size="8" maxlength="15"></td>
            <td width="75" align="left"><input type="submit" name="_buscar" value="Buscar"></td>
        </tr>
    </table>

    <table border="0" width="700" cellspacing="1" cellpadding="2" bgcolor="#000099">
        <tr>
            <td width="200" align="center" bgcolor="#000099">
                <a href="index.php?_orden=cedula_profesor"><font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Cédula</b></font></a>
            </td>
            <td width="300" align="left" bgcolor="#000099">
                <a href="index.php?_orden=apellidos_nombres"><font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Apellidos</b></font></a>
            </td>
            <td width="200" align="left" bgcolor="#000099">
                <a href="index.php?_orden=nombres"><font size="-1" face="Verdana,Arial,Geneva" color="#FFFFFF"><b>Nombres</b></font></a>
            </td>
        </tr>
<?php
$color_celda = "#FFFFFF";
while ($registro = mysqli_fetch_object($query)) {
    $color_celda = ($color_celda == "#CCCCCC") ? "#FFFFFF" : "#CCCCCC";
    ?>
    <tr valign="top">
        <td align="center" bgcolor="<?php echo $color_celda; ?>">
            <font size="-1" face="Verdana,Arial,Geneva">
                <a href="editar.php?cedula=<?php echo $registro->cedula_profesor; ?>">
                    <?php echo number_format($registro->cedula_profesor, 0, '', '.'); ?>
                </a>
            </font>
        </td>
        <td bgcolor="<?php echo $color_celda; ?>"><font size="-1" face="Verdana,Arial,Geneva"><?php echo $registro->apellidos_nombres; ?></font></td>
        <td bgcolor="<?php echo $color_celda; ?>"><font size="-1" face="Verdana,Arial,Geneva"><?php echo $registro->nombres; ?></font></td>
    </tr>
<?php
}
?>
    </table>
    <br><br>
<?php
$sql_total = "SELECT COUNT(cid) AS contados FROM profesores_cippsv";
$total_result = mysqli_query($conexion, $sql_total);
$total = mysqli_fetch_object($total_result)->contados;
$mostrar_fin = min($_desde + $cantidad_por_pagina, $total);
?>
<?php if (empty($_patron)): ?>
<table border="0" width="700" cellspacing="0" cellpadding="2">
    <tr>
        <td width="150" align="left" bgcolor="#CCCCCC">
            <font size="-1" face="Verdana,Arial,Geneva">
                <?php
                if ($_desde > 0) {
                    $nuevo_desde = $_desde - $cantidad_por_pagina;
                    echo "&lt;- <a href=\"index.php?_desde=$nuevo_desde&_orden=$_orden\">Anteriores</a>";
                } else {
                    echo "&lt;- Anteriores";
                }
                ?>
            </font>
        </td>
        <td width="400" align="center" bgcolor="#CCCCCC">
            <font size="-1" face="Verdana,Arial,Geneva">
                <b>Mostrando <?php echo ($_desde + 1) . " a $mostrar_fin de $total Profesores"; ?></b>
            </font>
        </td>
        <td width="150" align="right" bgcolor="#CCCCCC">
            <font size="-1" face="Verdana,Arial,Geneva">
                <?php
                if ($mostrar_fin < $total) {
                    echo "<a href=\"index.php?_desde=$mostrar_fin&_orden=$_orden\">Siguientes</a> -&gt;";
                } else {
                    echo "Siguientes -&gt;";
                }
                ?>
            </font>
        </td>
    </tr>
</table>
<?php endif; ?>
</center>
</form>
</body>
</html>