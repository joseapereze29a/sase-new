<?
session_start();
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");
$cedula=$_POST['cedula'];
$nombres=$_POST['nombres'];
$apellidos=$_POST['apellidos'];
$_agregando=$_POST['_agregando'];


if ($_cancelar)
{
	header ("Location: index.php");
	exit;
}

if ($cedula)
{
	if (!preg_match("/^[0-9]+$/", $cedula)) {
	$_SESSION['error'] = 'La cédula debe tener entre 7 y 8 dígitos numéricos.';
		$error_num_ci = 1;
	}
}

if ( ($_agregando) AND (! $error_num_ci) )
{

	$sql = "SELECT COUNT(cedula_profesor) AS cantidad FROM profesores_cippsv WHERE cedula_profesor='$cedula'";
    $res = mysqli_query($conexion, $sql);

    $error_ci_ya_existe = false;

    if ($res) {
        $row = mysqli_fetch_assoc($res);
        if ($row && $row['cantidad'] > 0) {
            $error_ci_ya_existe = true;
        }
    }

    // Si no existe, insertar
    if (!$error_ci_ya_existe) {
        $sql = "INSERT INTO profesores_cippsv (cedula_profesor, apellidos_nombres, nombres) 
                VALUES ('$cedula', '$apellidos', '$nombres')";
        if (mysqli_query($conexion, $sql)) {
            header("Location: index.php");
            exit;
        } else {
            echo "Error al insertar: " . mysqli_error($conexion);
        }
    } else {
        echo "Error: Ya existe un profesor con esa cédula.";
    }
	
}
?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.3 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#0000FF" VLINK="#0000FF">

<CENTER>

<?
	include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/encabezado.php");
?>

<FORM ACTION="agregar.php" METHOD="post">

<TABLE BORDER="0" WIDTH="700" CELLSPACING="0">
<TR ALIGN="left" VALIGN="top">
<TD WIDTH="700" ALIGN="center" VALIGN="top">
	<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
	<B>Ingresando los Datos de un Profesor</B>
	</FONT>
</TD></TR>
</TABLE>

<BR>


<?
if ($error_ci_ya_existe)
{
?>
	<BR>
	
	<TABLE BORDER="0" WIDTH="700" CELLSPACING="0" CELLPADDING="5">
	<TR ALIGN="left" VALIGN="top">
		<TD WIDTH="700" ALIGN="center" VALIGN="top">
			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FF0000">
				<B>El N&uacute;mero de C&eacute;dula de Identidad ya Existe en la BD, favor revisar.</B>
			</FONT>
		</TD>
	</TR>
	</TABLE>

	<BR>
<?
}


if ($error_num_ci)
{
?>
	<BR>
	
	<TABLE BORDER="0" WIDTH="700" CELLSPACING="0" CELLPADDING="5">
	<TR ALIGN="left" VALIGN="top">
		<TD WIDTH="700" ALIGN="center" VALIGN="top">
			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FF0000">
				<B>El N&uacute;mero de C&eacute;dula de Identidad no es V&aacute;lido, favor revisar.</B>
			</FONT>
		</TD>
	</TR>
	</TABLE>

	<BR>
<?
}
?>

<TABLE BORDER="0" WIDTH="700" CELLSPACING="0" CELLPADDING="5">
<TR ALIGN="left" VALIGN="top">
<TD WIDTH="250" ALIGN="right" VALIGN="top">
	<FONT SIZE="-2" FACE="Verdana,Arial,Geneva"><B>C&eacute;dula de Ident.</B></FONT>
</TD><TD WIDTH="450" ALIGN="left" VALIGN="top">
	<INPUT TYPE="text" NAME="cedula" VALUE="<? echo $cedula ?>" SIZE="14" MAXLENGTH="12">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			&nbsp; Ej: <B>12421101 &nbsp; (Sin puntos o Comas)</B>
		</FONT>
</TD></TR>
<TR ALIGN="left" VALIGN="top">
<TD WIDTH="250" ALIGN="right" VALIGN="top">
	<FONT SIZE="-2" FACE="Verdana,Arial,Geneva"><B>Apellidos</B></FONT>
</TD><TD WIDTH="450" ALIGN="left" VALIGN="top">
	<INPUT TYPE="text" NAME="apellidos" VALUE="<? echo $apellidos ?>" SIZE="42" MAXLENGTH="40">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			&nbsp; Ej: <B>Croes B.</B>
		</FONT>
</TD></TR>
<TR ALIGN="left" VALIGN="top">
<TD WIDTH="250" ALIGN="right" VALIGN="top">
	<FONT SIZE="-2" FACE="Verdana,Arial,Geneva"><B>Nombres</B></FONT>
</TD><TD WIDTH="450" ALIGN="left" VALIGN="top">
	<INPUT TYPE="text" NAME="nombres" VALUE="<? echo $nombres ?>" SIZE="42" MAXLENGTH="40">	
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			&nbsp; Ej: <B>Valentina M.</B>
		</FONT>
</TD></TR>
</TABLE>

<BR>
<INPUT TYPE="submit" NAME="_agregando" VALUE="Agregar"> 
<INPUT TYPE="submit" NAME="_cancelar" VALUE="Cancelar">
</CENTER>

</FORM>

</BODY>
</HTML>