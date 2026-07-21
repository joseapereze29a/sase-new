<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");


if (! ereg ("^[0-9]+$", $cedula) )
{
	header ("Location: ingreso_de_cedula.php");
	exit;
}


$sqlcmd = "SELECT count(cedula) AS cantidad "
		. "FROM datos_personales "
		. "WHERE cedula='$cedula' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$cantidad = $registro->cantidad;
}


if ($cantidad > 0)
{
	header ("Location: edicion_datos_personales.php?cedula=$cedula");
	exit;

} else {

	header ("Location: ingreso_datos_personales.php?cedula=$cedula");
	exit;
}
?>