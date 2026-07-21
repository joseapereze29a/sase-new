<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

$sqlcmd = "SELECT directorio_cippsv.modalidad, directorio_cippsv.ciudad, directorio_cippsv.edo_prov, oportunidades_estudio.tipo, "
		. "oportunidades_estudio.mencion_especialidad, cohortes.fecha_inicio, cohortes.codsede, cohortes.codopest "
		. "FROM directorio_cippsv, oportunidades_estudio, cohortes "
		. "WHERE cohortes.codcohorte='$_codcohorte' AND cohortes.codsede=oportunidades_estudio.codsede AND "
		. "cohortes.codopest=oportunidades_estudio.codopest AND oportunidades_estudio.codsede=directorio_cippsv.codsede ";

/*
+-----------+---------+----------+-----------+----------------------------+--------------+
| modalidad | ciudad  | edo_prov | tipo      | mencion_especialidad       | fecha_inicio |
+-----------+---------+----------+-----------+----------------------------+--------------+
| Nucleo    | Maracay | Aragua   | Postgrado | Orientaci—n de la Conducta | 1993-01-02   |
+-----------+---------+----------+-----------+----------------------------+--------------+
*/

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$codsede = $registro->codsede;
	$codopest = $registro->codopest;
	$modalidad = $registro->modalidad;
	$ciudad = $registro->ciudad;
	$edo_prov = $registro->edo_prov;
	$tipo = $registro->tipo;
	$mencion_especialidad = $registro->mencion_especialidad;
	$fecha_inicio = $registro->fecha_inicio;
}
?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>

<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#00CC00" VLINK="#CC0000">

<CENTER>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="130" ALIGN="center" VALIGN="top">
		<A HREF="/sace/"><IMG SRC="/sace/imagenes/logo.jpg" ALT="" WIDTH="119" HEIGHT="100" BORDER="0"></A>
	</TD><TD WIDTH="470" ALIGN="center" VALIGN="middle">
		<IMG SRC="/sace/imagenes/titulo_sistema_de_control_de_estudios.gif" ALT="" WIDTH="380" HEIGHT="22">
	</TD>
</TR>
</TABLE>

<?
	#include ("$DOCUMENT_ROOT/includes/encabezado.php");
?>

<BR>

<FONT FACE="Verdana,Arial,Geneva" COLOR="#0000FF">
<B>Ingreso de Actas</B>
</FONT>

<BR><BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#FF0000">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Informaci&oacute;n sobre el Postgrado o Especializaci&oacute;n</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Ciudad</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Estado o Provincia</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Modalidad</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $ciudad ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $edo_prov ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $modalidad ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Menci&oacute;n o Especialidad</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Tipo</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $mencion_especialidad ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $tipo ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Inicio</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Cohorte</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo fecha($fecha_inicio) ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $_codcohorte ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<?
$sqlcmd = "SELECT pensum_estudios.asignatura "
		. "FROM pensum_estudios, cohortes "
		. "WHERE cohortes.codcohorte='$_codcohorte' AND cohortes.codsede=pensum_estudios.codsede AND "
		. "cohortes.codopest=pensum_estudios.codopest AND pensum_estudios.codasig='$_codasig' ";
#echo "$sqlcmd<BR><BR>";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$asignatura = $registro->asignatura;
}
?>

<FONT FACE="Verdana,Arial,Geneva">
	<? echo $asignatura ?>
</FONT>

<BR>

<FORM ACTION="verificar_acta.php" METHOD="post">

<INPUT TYPE="hidden" NAME="_codcohorte" VALUE="<? echo $_codcohorte ?>">
<INPUT TYPE="hidden" NAME="_codasig" VALUE="<? echo $_codasig ?>">

<TABLE BORDER="0" WIDTH="400" CELLSPACING="2" CELLPADDING="2" BGCOLOR="#FF0000">
<TR>
	<TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="#FF0000">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>N&uacute;m.</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="#FF0000">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			&nbsp; &nbsp; <B>C&eacute;dula</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="center" VALIGN="top" BGCOLOR="#FF0000">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Calificaci&oacute;n</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<?
for ($i=1; $i<21; $i++)
{

	$cedula = 'cedula_' . $i;


	if (ereg ("^[0-9]+$", $$cedula))
	{
	 echo "Si va ";
	} else {
	 echo "Mama ";
	}
?>
<TABLE BORDER="0" WIDTH="400" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="100" ALIGN="right" VALIGN="top" BGCOLOR="#FF0000">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $i ?> &nbsp; &nbsp; &nbsp; 
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
	

		&nbsp; &nbsp; <INPUT TYPE="text" NAME="cedula_<? echo $i ?>" VALUE="<? echo $$cedula ?>" SIZE="11" MAXLENGTH="9">
	</TD>
	<TD WIDTH="150" ALIGN="center" VALIGN="top">
		<SELECT NAME="calificacion_<? echo $i ?>">
			<OPTION VALUE="seleccione"> - -
			<?
				$calificacion = 'calificacion_' . $i;
			
				for($j=1; $j<21; $j++)
				{
					if ($$calificacion == $j)
					{
						echo '<OPTION VALUE="' . $j . '" SELECTED>' . $j . "\n";
					} else {
						echo '<OPTION VALUE="' . $j . '">' . $j . "\n";
					}
				}



				if ($$calificacion == '404')
				{
					echo '<OPTION VALUE="' . 404 . '" SELECTED>No Curs&oacute;' . "\n";
				} else {
					echo '<OPTION VALUE="' . 404 . '">No Curs&oacute;' . "\n";
				}


				if ($$calificacion == '321')
				{
					echo '<OPTION VALUE="' . 321 . '" SELECTED>Retir&oacute;' . "\n";
				} else {
					echo '<OPTION VALUE="' . 321 . '">Retir&oacute;' . "\n";
				}


				if ($$calificacion == '212')
				{
					echo '<OPTION VALUE="' . 212 . '" SELECTED>Equivalencia' . "\n";
				} else {
					echo '<OPTION VALUE="' . 212 . '">Equivalencia' . "\n";
				}

			?>
		</SELECT>
	</TD>
</TR>
</TABLE>
<?
}
?>

<BR>

<TABLE BORDER="0" WIDTH="620" CELLSPACING="7" CELLPADDING="2">
<TR>
	<TD WIDTH="620" ALIGN="right" VALIGN="top">
		<FONT SIZE="-1" COLOR="#FF0000" FACE="Verdana,Arial,Geneva">
			Permite cerrar el Acta e ir a otra Secci&oacute;n -> 
		</FONT>
		<INPUT TYPE="submit" NAME="Continuar_f" VALUE="&nbsp; Continuar y Finalizar &nbsp;">
</TD></TR>
<TR><TD WIDTH="620" ALIGN="right" VALIGN="top">
		<FONT SIZE="-1" COLOR="#FF0000" FACE="Verdana,Arial,Geneva">
			Permite agregar mas Estudiantes a esta Acta -> 
		</FONT>
		<INPUT TYPE="submit" NAME="Continuar_a" VALUE="&nbsp; Continuar y Agregar Mas &nbsp;">
	</TD>
</TR>
</TABLE>

</FORM>

<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
