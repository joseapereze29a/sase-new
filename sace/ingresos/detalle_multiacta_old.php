<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_datos_profesor.php");

$cantidad_por_pagina = "10";	### Muestro de Tantos en Tantos Alumnos por Listado

if (! $_desde) $_desde = '0';	### Variable que utiliza el Paginator

$cantidad = $_desde;

/*
$sqlcmd = "SELECT directorio_cippsv.modalidad, directorio_cippsv.ciudad, directorio_cippsv.edo_prov, "
		. "oportunidades_estudio.tipo, oportunidades_estudio.mencion_especialidad, cohortes.codcohorte, "
		. "cohortes.fecha_inicio "
		. "FROM directorio_cippsv, oportunidades_estudio, cohortes, registro_actas "
		. "WHERE registro_actas.codacta='$codacta' AND registro_actas.codcohorte=cohortes.codcohorte AND "
		. "cohortes.codsede=oportunidades_estudio.codsede AND cohortes.codopest=oportunidades_estudio.codopest AND "
		. "oportunidades_estudio.codsede=directorio_cippsv.codsede ";


# +-----------+---------+------------------+-----------+----------------------------+-------------+--------------+
# | modalidad | ciudad  | edo_prov         | tipo      | mencion_especialidad       | codcohorte  | fecha_inicio |
# +-----------+---------+------------------+-----------+----------------------------+-------------+--------------+
# | Sede      | Caracas | Distrito Federal | Postgrado | Orientaci—n de la Conducta | PPALOC97-II | 1997-09-05   |
# +-----------+---------+------------------+-----------+----------------------------+-------------+--------------+


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
*/

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
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Informaci&oacute;n sobre el Acta</B>
		</FONT>
	</TD>
</TR>
</TABLE>


<?
$sqlcmd = "select multiactas.codasig, multiactas.cedula_profesor1, multiactas.cedula_profesor2, multiactas.cedula_profesor3, "
		. "multiactas.fecha_aprobacion, "
		. "pensum_estudios.asignatura, pensum_estudios.creditos, pensum_estudios.periodos "
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



$curso_d = substr($codacta, -3, 2);
$curso_d = strtolower($curso_d);
			
if ($curso_d == "cd")
{
	$curso_d_num = substr($codacta, -1);
	$asignatura = $asignatura . ' <B>(CD' . $curso_d_num . ')</B>';
	$curso_d_num = '';
}

$curso_d = '';


$apellidos_nombres1 = datos_profesor($cedula_profesor1);
$apellidos_nombres2 = datos_profesor($cedula_profesor2);
$apellidos_nombres3 = datos_profesor($cedula_profesor3);

#if ($cedula_profesor1 == 0) $cedula_profesor1 = '';
#if ($cedula_profesor2 == 0) $cedula_profesor2 = '';
#if ($cedula_profesor3 == 0) $cedula_profesor3 = '';

if ( ($cedula_profesor1 == '') OR ($cedula_profesor1 == 0) )
{
	$cedula_profesor1 = '';

} else {

	$cedula_profesor1 = strtr (number_format($cedula_profesor1), ",", ".");
}


if ( ($cedula_profesor2 == '') OR ($cedula_profesor2 == 0) )
{
	$cedula_profesor2 = '';

} else {

	$cedula_profesor2 = strtr (number_format($cedula_profesor2), ",", ".");
}


if ( ($cedula_profesor3 == '') OR ($cedula_profesor3 == 0) )
{
	$cedula_profesor3 = '';

} else {

	$cedula_profesor3 = strtr (number_format($cedula_profesor3), ",", ".");
}



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
$sqlcmd2 = "SELECT apellidos_nombres, nombres FROM profesores_cippsv WHERE cedula_profesor='$cedula_profesor1' ";

$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

while ($registro2 = mysql_fetch_object($query2))
{
	$apellidos_nombres1 = $registro2->apellidos_nombres;
	$nombres1 = $registro2->nombres;
}


if ( ($cedula_profesor1 == '') OR ($cedula_profesor1 == 0) )
{
	$cedula_profesor1 = '';

} else {

	$cedula_profesor1 = strtr (number_format($cedula_profesor1), ",", ".");
}


if ($apellidos_nombres1 == "")
{
	$apellidos_nombres1 == "";

} else {

	$apellidos_nombres1 = ucwords(strtolower($apellidos_nombres1)) . ' ' . ucwords(strtolower($nombres1));
}



$sqlcmd2 = "SELECT apellidos_nombres, nombres FROM profesores_cippsv WHERE cedula_profesor='$cedula_profesor2' ";

$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

while ($registro2 = mysql_fetch_object($query2))
{
	$apellidos_nombres2 = $registro2->apellidos_nombres;
	$nombres2 = $registro2->nombres;
}


if ( ($cedula_profesor2 == '') OR ($cedula_profesor2 == 0) )
{
	$cedula_profesor2 = '';

} else {

	$cedula_profesor2 = strtr (number_format($cedula_profesor2), ",", ".");
}


if ($apellidos_nombres2 == "")
{
	$apellidos_nombres2 == "";

} else {

	$apellidos_nombres2 = ucwords(strtolower($apellidos_nombres2)) . ' ' . ucwords(strtolower($nombres2));
}



$sqlcmd2 = "SELECT apellidos_nombres, nombres FROM profesores_cippsv WHERE cedula_profesor='$cedula_profesor3' ";

$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

while ($registro2 = mysql_fetch_object($query2))
{
	$apellidos_nombres3 = $registro2->apellidos_nombres;
	$nombres3 = $registro2->nombres;
}


if ( ($cedula_profesor3 == '') OR ($cedula_profesor3 == 0) )
{
	$cedula_profesor3 = '';

} else {

	$cedula_profesor3 = strtr (number_format($cedula_profesor3), ",", ".");
}


if ($apellidos_nombres3 == "")
{
	$apellidos_nombres3 == "";

} else {

	$apellidos_nombres3 = ucwords(strtolower($apellidos_nombres3)) . ' ' . ucwords(strtolower($nombres3));
}
*/
?>
<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2">
<TR>
	<TD WIDTH="275" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Asignatura</B>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Codigo</B>
		</FONT>
	</TD>
	<TD WIDTH="75" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Periodo</B>
		</FONT>
	</TD>

	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Creditos</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="275" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $asignatura ?>
		</FONT>
	</TD>
	<TD WIDTH="75" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $codasig ?>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $periodos ?>
		</FONT>
	</TD>

	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $creditos ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2">
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Fecha Aprobaci&oacute;n</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>C&eacute;dula del Profesor</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Profesor</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $fecha_aprobacion ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $cedula_profesor1 ?><BR>
			<? echo $cedula_profesor2 ?><BR>
			<? echo $cedula_profesor3 ?><BR>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $apellidos_nombres1 ?><BR>
			<? echo $apellidos_nombres2 ?><BR>
			<? echo $apellidos_nombres3 ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
<TR>
	<TD WIDTH="30" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>N&uacute;m.</B>
		</FONT>
	</TD>
	<TD WIDTH="80" ALIGN="center" VALIGN="top">
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
	<TD WIDTH="60" ALIGN="left" VALIGN="top">
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
		. "ORDER BY record_notas.cedula "
		. "LIMIT $_desde, $cantidad_por_pagina ";

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

	$contador++;


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
	<TD WIDTH="30" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			&nbsp; <? echo $cantidad ?>
		</FONT></TD>
	<TD WIDTH="80" ALIGN="right" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			if ( ($calificacion >= 1) AND ($calificacion <= 14) )
			{
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>' . strtr (number_format($cedula), ",", ".") . '<B> &nbsp; </FONT></TD>';
			} else {
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">' . strtr (number_format($cedula), ",", ".") . ' &nbsp; </FONT></TD>';
			}
		?>
	<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			if ( ($calificacion >= 1) AND ($calificacion <= 14) )
			{
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>' . ucwords($apellidos) . '</B></FONT></TD>';
			} else {
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">' . ucwords($apellidos) . '</FONT></TD>';
			}
		?>
	<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			if ( ($calificacion >= 1) AND ($calificacion <= 14) )
			{
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>' . ucwords($nombres) . '</B></FONT></TD>';
			} else {
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">' . ucwords($nombres) . '</FONT></TD>';
			}
		?>
	<TD WIDTH="60" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
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
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $codeq ?>
		</FONT>
	</TD>
</TR>
<?
}
?>
</TABLE>


<?
	if ($contador > 0)
	{
	
		$sqlcmd = "SELECT (SUM(calificacion)/count(calificacion)) AS promedio "
				. "FROM record_notas "
				. "WHERE calificacion>0 AND calificacion<=20 AND codacta='$codacta' AND mid='$mid' ";
		
		$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
		
		while ($registro = mysql_fetch_object($query))
		{	
			$promedio = $registro->promedio;
		}

		if ($promedio > 0)
		{
?>
			<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2">
			<TR>
				<TD WIDTH="600" ALIGN="center" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
						<B>Promedio del Alumnado en esta Asignatura: <? echo number_format(($promedio), 2, ',', '')  ?></B>
					</FONT>
				</TD>
			</TR>
			</TABLE>
<?
		}

	}
?>


<?
###
### Paginator
###

	$sqlcmd = "SELECT count(codacta) AS contados "
			. "FROM record_notas "
			. "WHERE codacta='$codacta' AND mid='$mid' ";


	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

	while ($registro = mysql_fetch_object($query))
	{
		$contados = $registro->contados;
	}


	$mostrar_fin = $_desde + $cantidad_por_pagina;

	if ($mostrar_fin > $contados)
	{
		$mostrar_fin = $contados;
	}

if ($contados > $cantidad_por_pagina)
{
?>
	<BR>

	<TABLE BORDER="0" WIDTH="600" CELLSPACING="0" CELLPADDING="2">
	<TR><TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="#CCCCCC">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
		<?
			if ($_desde > 0)
			{
				$nuevo_desde = $_desde - $cantidad_por_pagina;
				echo '&lt;- <A HREF="detalle_multiacta.php?_desde=' . $nuevo_desde . '&codacta=' . $codacta . '&mid=' . $mid . '">Anteriores</A>';
			} else {
				echo '&lt;- Anteriores';
			}
		?>
		</FONT>
	</TD><TD WIDTH="300" ALIGN="center" VALIGN="top" BGCOLOR="#CCCCCC">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
		<?
			echo "<B>Mostrando " . ($_desde+1) . " a $mostrar_fin de $contados Estudiantes</B>";
		?>
		</FONT>
	</TD><TD WIDTH="150" ALIGN="right" VALIGN="top" BGCOLOR="#CCCCCC">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
		<?
			if ($mostrar_fin < $contados)
			{
				echo '<A HREF="detalle_multiacta.php?_desde=' . $mostrar_fin . '&codacta=' . $codacta . '&mid=' . $mid . '">Siguientes</A> -&gt;';
			} else {
				echo 'Siguientes -&gt;';
			}
		?>
		</FONT>
	</TD></TR>
	</TABLE>
<?
}
?>

</CENTER>

</BODY>
</HTML>
