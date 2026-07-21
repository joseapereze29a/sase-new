<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_datos_profesor.php");

$sqlcmd = "SELECT directorio_cippsv.modalidad, directorio_cippsv.ciudad, directorio_cippsv.edo_prov, "
		. "oportunidades_estudio.tipo, oportunidades_estudio.mencion_especialidad, cohortes.codcohorte, "
		. "cohortes.fecha_inicio, cohortes.codsede, cohortes.codopest "
		. "FROM directorio_cippsv, oportunidades_estudio, cohortes, multiactas "
		. "WHERE multiactas.codacta='$codacta' AND multiactas.codcohorte=cohortes.codcohorte AND "
		. "cohortes.codsede=oportunidades_estudio.codsede AND cohortes.codopest=oportunidades_estudio.codopest AND "
		. "oportunidades_estudio.codsede=directorio_cippsv.codsede AND multiactas.mid='$mid' ";

/*
+-----------+---------+------------------+-----------------+---------------------------------+-------------+--------------+---------+----------+
| modalidad | ciudad  | edo_prov         | tipo            | mencion_especialidad            | codcohorte  | fecha_inicio | codsede | codopest |
+-----------+---------+------------------+-----------------+---------------------------------+-------------+--------------+---------+----------+
| Sede      | Caracas | Distrito Federal | Especializacion | Terapia de la Conducta Infantil | PPALTCI03-V | 2003-09-05   | PPAL    | ESP-TCI  |
+-----------+---------+------------------+-----------------+---------------------------------+-------------+--------------+---------+----------+
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

	$codsede_menu = $registro->codsede;
	$codopest_menu = $registro->codopest;
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

		<A HREF="seleccion_postgrado.php?_codsede=<? echo $codsede_menu ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Postgrado</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_cohorte.php?_codsede=<? echo $codsede_menu ?>&_codopest=<? echo $codopest_menu ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Cohortes Existentes</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_acta.php?_codcohorte=<? echo $codcohorte ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Actas Existentes</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="editando_datos_multiactas.php?_codacta=<? echo $codacta ?>&_codcohorte=<? echo $codcohorte ?>&mid=<? echo $mid ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Edici&oacute;n de Acta</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n Estudiante</B></FONT>
	</TD>
</TR>
</TABLE>

<BR>

<IMG SRC="/sace/imagenes/titulos_de_home/titulo_editar.jpg" ALT="" WIDTH="363" HEIGHT="21" BORDER="0">

<BR><BR><BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Informaci&oacute;n sobre el Acta</B>
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
			<B>Fecha de Inicio de la Cohorte</B>
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
			<? echo $fecha_inicio ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $codcohorte ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<?
$sqlcmd = "select multiactas.codasig, multiactas.cedula_profesor1, multiactas.cedula_profesor2, multiactas.cedula_profesor3, "
		. "multiactas.fecha_aprobacion, pensum_estudios.asignatura, pensum_estudios.creditos, pensum_estudios.periodos "
		. "FROM multiactas, pensum_estudios, cohortes, oportunidades_estudio "
		. "WHERE multiactas.codacta='$codacta' AND multiactas.codcohorte=cohortes.codcohorte AND "
		. "cohortes.codsede=oportunidades_estudio.codsede AND cohortes.codopest=oportunidades_estudio.codopest AND "
		. "oportunidades_estudio.codsede=pensum_estudios.codsede and oportunidades_estudio.codopest=pensum_estudios.codopest AND "
		. "multiactas.codasig=pensum_estudios.codasig AND multiactas.mid='$mid' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$codasig = $registro->codasig;
	$cedula_profesor1 = $registro->cedula_profesor1;
	$cedula_profesor2 = $registro->cedula_profesor2;
	$cedula_profesor3 = $registro->cedula_profesor3;
	$fecha_aprobacion = $registro->fecha_aprobacion;
	$asignatura = $registro->asignatura;
	$creditos = $registro->creditos;
	$periodos = $registro->periodos;
}


if ( ($fecha_aprobacion == '0000-00-00') OR ($fecha_aprobacion == "") )
{
	$fecha_aprobacion = "";

} else {

	$fecha_aprobacion = fecha($fecha_aprobacion);
}

$apellidos_nombres1 = datos_profesor($cedula_profesor1);
$apellidos_nombres2 = datos_profesor($cedula_profesor2);
$apellidos_nombres3 = datos_profesor($cedula_profesor3);

$curso_d = substr($codacta, -3, 2);
$curso_d = strtolower($curso_d);
	
if ($curso_d == "cd")
{
	$asignatura = $asignatura . ' <B>(CD)</B>';
}

$curso_d == '';


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
	$apellidos_nombres  = strtolower($registro2->apellidos_nombres);
}
*/

?>
<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2">
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Asignatura</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>C&oacute;digo Asignatura</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $asignatura ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $codasig ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2">
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Aprobaci&oacute;n del Acta</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Profesor(es)</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $fecha_aprobacion ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<?
				echo $apellidos_nombres1 . "<BR>";
				echo $apellidos_nombres2 . "<BR>";
				echo $apellidos_nombres3;
			?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
<TR>
	<TD WIDTH="120" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>C&eacute;dula</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Apellidos</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Nombres</B>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Nota</B>
		</FONT>
	</TD>
	<TD WIDTH="80" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Equivalencia</B>
		</FONT>
	</TD>
</TR>
<?
$sqlcmd = "SELECT record_notas.cedula, record_notas.calificacion, record_notas.codeq "
		. "FROM record_notas "
		. "WHERE codacta='$codacta' AND mid='$mid' "
		. "ORDER BY record_notas.cedula ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
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

	if ( ($calificacion >= 1) AND ($calificacion <= 20) )
	{
		$notas = $notas + $calificacion;
		
		$contador++;
	}

	if ($bg_celda == '#CCCCCC')
	{
		$bg_celda = '#FFFFFF';
	} else {
		$bg_celda = '#CCCCCC';
	}
?>
<TR>
	<TD WIDTH="120" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<A HREF="editando_calificaciones_multiactas.php?codacta=<? echo $codacta ?>&_cedula=<? echo $cedula ?>&mid=<? echo $mid ?>">
			<FONT SIZE="-2" FACE="Verdana,Arial,Geneva"><B>Editar</B></FONT></A><FONT SIZE="-2" FACE="Verdana,Arial,Geneva"> &nbsp; </FONT>
			<FONT SIZE="-2" FACE="Verdana,Arial,Geneva"><? echo strtr (number_format($cedula), ",", ".") ?></FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva"><? echo ucwords($apellidos) ?></FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva"><? echo ucwords($nombres) ?></FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			if ( ($calificacion >= 1) AND ($calificacion <= 14) )
			{
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">' . $calificacion . '</FONT>';
				
			} else {
			
				#if ($calificacion == 321) $calificacion = 'Retir&oacute;';
				
				if ($calificacion == 404) $calificacion = 'No Curs&oacute;';

				if ($calificacion == 99) $calificacion = 'Reprobado';

				if ($calificacion == 100) $calificacion = 'Aprobado';
				
				if ($calificacion == 110) $calificacion = 'Meritorio';
				
				if ($calificacion == 120) $calificacion = 'Excelencia';
				
				if ($calificacion == 212) $calificacion = 'Equivalencia';


				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">' . $calificacion . '</FONT>';
			}
		?>
	</TD>
	<TD WIDTH="80" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $codeq ?>
		</FONT>
	</TD>
</TR>
<?
}
?>
</TABLE>

<BR><BR>

</CENTER>

</BODY>
</HTML>
