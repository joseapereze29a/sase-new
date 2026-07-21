<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_datos_profesor.php");

$sqlcmd = "SELECT directorio_cippsv.modalidad, directorio_cippsv.ciudad, directorio_cippsv.edo_prov, "
		. "oportunidades_estudio.tipo, oportunidades_estudio.mencion_especialidad, cohortes.codcohorte, "
		. "cohortes.fecha_inicio "
		. "FROM directorio_cippsv, oportunidades_estudio, cohortes, registro_actas "
		. "WHERE registro_actas.codacta='$codacta' AND registro_actas.codcohorte=cohortes.codcohorte AND "
		. "cohortes.codsede=oportunidades_estudio.codsede AND cohortes.codopest=oportunidades_estudio.codopest AND "
		. "oportunidades_estudio.codsede=directorio_cippsv.codsede ";

/*
+-----------+---------+------------------+-----------+----------------------------+-------------+--------------+
| modalidad | ciudad  | edo_prov         | tipo      | mencion_especialidad       | codcohorte  | fecha_inicio |
+-----------+---------+------------------+-----------+----------------------------+-------------+--------------+
| Sede      | Caracas | Distrito Federal | Postgrado | Orientaci—n de la Conducta | PPALOC97-II | 1997-09-05   |
+-----------+---------+------------------+-----------+----------------------------+-------------+--------------+
*/

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$modalidad = $registro->modalidad;
	$ciudad = $registro->ciudad;
	$edo_prov = $registro->edo_prov;
	$tipo = $registro->tipo;
	$mencion_especialidad = $registro->mencion_especialidad;
	$codcohorte = $registro->codcohorte;
	$fecha_inicio = $registro->fecha_inicio;
}


if ( ($fecha_inicio == '0000-00-00') OR ($fecha_inicio == "") )
{
	$fecha_inicio = "";

} else {

	$fecha_inicio = fecha($fecha_inicio);
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
	include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/encabezado.php");
?>


<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
	<TD WIDTH="100%" ALIGN="left" VALIGN="top">
	
		<A HREF="../"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_de_sede.php"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n de Sede</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_postgrado.php?_codsede=<? echo $_codsede_menu ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Postgrado</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_cohorte.php?_codsede=<? echo $_codsede_menu ?>&_codopest=<? echo $_codopest_menu ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Cohortes Existentes</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_acta.php?_codcohorte=<? echo $codcohorte ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Actas Existentes</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Detalle de Acta</B></FONT>

	</TD>
</TR>
</TABLE>

<BR>

<FONT FACE="Verdana,Arial,Geneva">
	Eliminar una Nota
</FONT>

<BR><BR><BR>

<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="710" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Informaci&oacute;n sobre el Acta</B>
		</FONT>
	</TD>
</TR>
</TABLE>


<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="260" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Ciudad</B>
		</FONT>
	</TD>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
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
	<TD WIDTH="260" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $ciudad ?>
		</FONT>
	</TD>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
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

<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="410" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Menci&oacute;n o Especialidad</B>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Tipo</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="410" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $mencion_especialidad ?>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $tipo ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="410" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Inicio</B>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Cohorte</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="410" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $fecha_inicio ?>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $codcohorte ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>


<?
$sqlcmd = "select registro_actas.codasig, registro_actas.cedula_profesor, registro_actas.fecha_aprobacion, "
		. "pensum_estudios.asignatura, pensum_estudios.creditos, pensum_estudios.periodos "
		. "FROM registro_actas, pensum_estudios, cohortes, oportunidades_estudio "
		. "WHERE registro_actas.codacta='$codacta' AND registro_actas.codcohorte=cohortes.codcohorte AND "
		. "cohortes.codsede=oportunidades_estudio.codsede AND cohortes.codopest=oportunidades_estudio.codopest AND "
		. "oportunidades_estudio.codsede=pensum_estudios.codsede and oportunidades_estudio.codopest=pensum_estudios.codopest AND "
		. "registro_actas.codasig=pensum_estudios.codasig ";


$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$codasig = $registro->codasig;
	$cedula_profesor = $registro->cedula_profesor;
	$fecha_aprobacion = $registro->fecha_aprobacion;
	$asignatura = $registro->asignatura;
	$creditos = $registro->creditos;
	$periodos = $registro->periodos;
}


$curso_d = substr($codacta, -3, 2);
$curso_d = strtolower($curso_d);
			
if ($curso_d == "cd")
{
	$curso_d_num = substr($codacta, -1);
	$asignatura = $asignatura . ' <B>(CD' . $curso_d_num . ')</B>';
	$curso_d_num = '';
}


	

$curso_d == '';


$apellidos_nombres = datos_profesor($cedula_profesor);


if ( ($fecha_aprobacion == '0000-00-00') OR ($fecha_aprobacion == "") )
{
	$fecha_aprobacion = "";

} else {

	$fecha_aprobacion = fecha($fecha_aprobacion);
}


/*
+---------+-----------------+------------------+-------------+----------+----------+
| codasig | cedula_profesor | fecha_aprobacion | asignatura  | creditos | periodos |
+---------+-----------------+------------------+-------------+----------+----------+
| OC-012  |         4825080 | 1999-05-12       | Conducta IV |        1 |        4 |
+---------+-----------------+------------------+-------------+----------+----------+
*/
/*
$sqlcmd2 = "SELECT apellidos_nombres FROM profesores_cippsv WHERE cedula_profesor='$cedula_profesor' ";

$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

while ($registro2 = mysql_fetch_object($query2))
{
	$apellidos_nombres = $registro2->apellidos_nombres;
}
*/

if ( ($cedula_profesor == '') OR ($cedula_profesor == 0) )
{
	$cedula_profesor = '';

} else {

	$cedula_profesor = strtr (number_format($cedula_profesor), ",", ".");
}


if ($apellidos_nombres == "")
{
	$apellidos_nombres == "";

} else {

	#$apellidos_nombres = strtolower($apellidos_nombres);
	#$apellidos_nombres = ucwords($apellidos_nombres);
}


?>
<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="360" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Asignatura</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Codigo</B>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Periodo</B>
		</FONT>
	</TD>

	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Creditos</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="360" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $asignatura ?>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $codasig ?>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $periodos ?>
		</FONT>
	</TD>

	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $creditos ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="235" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha Aprobaci&oacute;n</B>
		</FONT>
	</TD>
	<TD WIDTH="235" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>C&eacute;dula del Profesor</B>
		</FONT>
	</TD>
	<TD WIDTH="240" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Profesor</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="235" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $fecha_aprobacion ?>
		</FONT>
	</TD>
	<TD WIDTH="235" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $cedula_profesor ?>
		</FONT>
	</TD>
	<TD WIDTH="240" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $apellidos_nombres ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="710" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
<TR>
	<TD WIDTH="70" ALIGN="left" VALIGN="top">
		<P> </P>
	</TD>
	<TD WIDTH="80" ALIGN="center" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>C&eacute;dula</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Apellidos</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Nombres</B>
		</FONT>
	</TD>
	<TD WIDTH="70" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Nota</B>
		</FONT>
	</TD>
	<TD WIDTH="90" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Equivalencia</B>
		</FONT>
	</TD>
</TR>
<?
$sqlcmd = "SELECT record_notas.cedula, record_notas.calificacion, record_notas.codeq "
		. "FROM record_notas "
		. "WHERE codacta='$codacta' "
		. "ORDER BY record_notas.cedula ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{

	$cantidad++;

	$calificacion = "";
	$cedula = "";
	$apellidos = "";
	$nombres = "";
	
	$cedula = $registro->cedula;
	$calificacion = $registro->calificacion;
	$codeq = $registro->codeq;



	$sqlcmd2 = "SELECT datos_personales.apellidos, datos_personales.nombres "
			 . "FROM datos_personales "
			 . "WHERE datos_personales.cedula='$cedula' ";

	$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

	while ($registro2 = mysql_fetch_object($query2))
	{
		$apellidos = strtolower($registro2->apellidos);
		$nombres = strtolower($registro2->nombres);
	}


	if ($bg_celda == '#CCCCCC')
	{
		$bg_celda = '#FFFFFF';
	} else {
		$bg_celda = '#CCCCCC';
	}
?>
<TR>
	<TD WIDTH="70" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<A HREF="eliminar_nota.php?codacta=<? echo $codacta ?>&cedula=<? echo $cedula ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FF0000"><B>ELIMINAR</B></FONT></A></TD>
	<TD WIDTH="80" ALIGN="right" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			if ( ($calificacion >= 1) AND ($calificacion <= 14) )
			{
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>' . strtr (number_format($cedula), ",", ".") . '<B> &nbsp; </FONT></TD>';
			} else {
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">' . strtr (number_format($cedula), ",", ".") . ' &nbsp; </FONT></TD>';
			}
		?>
	<TD WIDTH="200" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			if ( ($calificacion >= 1) AND ($calificacion <= 14) )
			{
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>' . ucwords($apellidos) . '</B></FONT></TD>';
			} else {
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">' . ucwords($apellidos) . '</FONT></TD>';
			}
		?>
	<TD WIDTH="200" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			if ( ($calificacion >= 1) AND ($calificacion <= 14) )
			{
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>' . ucwords($nombres) . '</B></FONT></TD>';
			} else {
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">' . ucwords($nombres) . '</FONT></TD>';
			}
		?>
	<TD WIDTH="70" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			if ( ($calificacion >= 1) AND ($calificacion <= 14) )
			{
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>' . $calificacion . '</B></FONT></TD>';
			} else {
				if ($calificacion == 404) $calificacion = 'No Curs&oacute;';
				if ($calificacion == 99) $calificacion = 'Reprobado';
				if ($calificacion == 100) $calificacion = 'Aprobado';
				if ($calificacion == 110) $calificacion = 'Meritorio';
				if ($calificacion == 120) $calificacion = 'Excelencia';
				if ($calificacion == 212) $calificacion = 'Equivalencia';
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">' . $calificacion . '</FONT></TD>';
			}
		?>
	<TD WIDTH="90" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $codeq ?>
		</FONT>
	</TD>
</TR>
<?
}
?>
</TABLE>


</CENTER>

</BODY>
</HTML>
