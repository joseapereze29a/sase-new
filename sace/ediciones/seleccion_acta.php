<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_datos_profesor.php");


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
	$modalidad = $registro->modalidad;
	$ciudad = $registro->ciudad;
	$edo_prov = $registro->edo_prov;
	$tipo = $registro->tipo;
	$mencion_especialidad = $registro->mencion_especialidad;
	$fecha_inicio = $registro->fecha_inicio;

	$_codsede_menu = $registro->codsede;
	$_codopest_menu = $registro->codopest;
}
?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>

<script language="JavaScript">
<!--
function popup( windowname, url, w, h )
{
	popupwin = window.open( "", windowname, "toolbar=no,location=no,directories=no,status=no,menubar=no,width="+ w +",height=" + h + ",resizable=1,scrollbars=1" );
	popupwin.location = url;
}
//-->
</script>

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

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Actas Existentes</B></FONT>
	</TD>
</TR>
</TABLE>

<BR>

<IMG SRC="/sace/imagenes/titulos_de_home/titulo_editar.jpg" ALT="" WIDTH="363" HEIGHT="21" BORDER="0">

<BR><BR><BR>


<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="710" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Informaci&oacute;n sobre el Postgrado</B>
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

<BR><BR>

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
			<? echo fecha($fecha_inicio) ?>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $_codcohorte ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR><BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
	Seleccione un Acta existente para <B>Visualizarla</B>,<BR>
	o seleccione Editar los <B>Datos del Acta</B> (Notas)
</FONT>

<BR><BR>

<TABLE BORDER="0" WIDTH="770" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
<TR>
	<TD WIDTH="120" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Acta</B>
		</FONT>
	</TD>
	<TD WIDTH="210" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Profesor</B>
		</FONT>
	</TD>
	<TD WIDTH="270" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Asignatura</B>
		</FONT>
	</TD>
	<TD WIDTH="40" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Perio.</B>
		</FONT>
	</TD>
	<TD WIDTH="130" ALIGN="right" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Fecha Aprobaci&oacute;n</B>
		</FONT>
	</TD>
</TR>
<?
$sqlcmd = "SELECT registro_actas.codacta, registro_actas.cedula_profesor, pensum_estudios.asignatura, pensum_estudios.periodos, "
		. "registro_actas.fecha_aprobacion "
		. "FROM registro_actas, pensum_estudios, cohortes "
		. "WHERE registro_actas.codcohorte='$_codcohorte' AND registro_actas.codasig=pensum_estudios.codasig AND "
		. "registro_actas.codcohorte=cohortes.codcohorte AND cohortes.codsede=pensum_estudios.codsede AND "
		. "cohortes.codopest=pensum_estudios.codopest ORDER BY pensum_estudios.periodos, registro_actas.codasig";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$codacta = $registro->codacta;
	$cedula_profesor = $registro->cedula_profesor;
	$asignatura = $registro->asignatura;
	$periodos = $registro->periodos;
	$fecha_aprobacion = $registro->fecha_aprobacion;

	$pase_por_aqui = 1;

	$curso_d = substr($codacta, -3, 2);
	$curso_d = strtolower($curso_d);
		
	if ($curso_d == "cd")
	{
		$asignatura = $asignatura . ' <B>(CD)</B>';
	}
	
	$curso_d == '';

	$apellidos_nombres = datos_profesor($cedula_profesor);

/*
	$sqlcmd2 = "SELECT apellidos_nombres FROM profesores_cippsv WHERE cedula_profesor='$cedula_profesor' ";
	
	$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

	while ($registro2 = mysql_fetch_object($query2))
	{
		$apellidos_nombres = strtolower($registro2->apellidos_nombres);	
	}
*/

	if ( ($fecha_aprobacion == '0000-00-00') OR ($fecha_aprobacion == "") )
	{
		$fecha_aprobacion = "";
	} else {
		$fecha_aprobacion = fecha ($fecha_aprobacion, corto);
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
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">

			<A HREF="javascript:popup('_blank', '../ingresos/detalle_acta.php?codacta=<? echo $codacta ?>',640,510)"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>Ver</B></FONT></A> &nbsp; &nbsp; 

			<A HREF="editando_datos_acta.php?_codacta=<? echo $codacta ?>&_codcohorte=<? echo $_codcohorte ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>Editar Acta</B></FONT></A>
		
		</FONT>
	</TD>
	<TD WIDTH="210" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $apellidos_nombres ?>
		</FONT>
	</TD>
	<TD WIDTH="270" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $asignatura ?>
		</FONT>
	</TD>
	<TD WIDTH="40" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $periodos ?>
		</FONT>
	</TD>
	<TD WIDTH="130" ALIGN="right" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $fecha_aprobacion ?>
		</FONT>
	</TD>
</TR>
<?

	$cedula_profesor = '';
	$apellidos_nombres = '';
}
?>
</TABLE>

<?
		if (! $pase_por_aqui)
		{
?>

			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
				<BR><BR>
				<B>No se encontraron Actas existentes para la Cohorte seleccionada.</B>
			</FONT>
<?
		}


### Busco si hay Multiactas

$sqlcmd = "SELECT pensum_estudios.asignatura, pensum_estudios.codasig "
		. "FROM pensum_estudios, multiactas "
		. "WHERE pensum_estudios.codsede='$_codsede_menu' AND pensum_estudios.codopest='$_codopest_menu' AND "
		. "multiactas.codcohorte='$_codcohorte' AND pensum_estudios.codasig=multiactas.codasig "
		. "GROUP BY pensum_estudios.codasig ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$asignatura = $registro->asignatura;
	$codasig = $registro->codasig;

	echo "<BR>";


	if ( ($asignatura) AND ($codasig) )
	{

		if (! $encabezado_multiacta)
		{
			echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
			echo $asignatura . '<BR>';
			echo '</FONT>';
				
			$encabezado_multiacta = 1;
		}
?>
		<TABLE BORDER="0" WIDTH="770" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
		<TR>
			<TD WIDTH="110" ALIGN="left" VALIGN="top">
				<P> </P>
			</TD>
			<TD WIDTH="540" ALIGN="center" VALIGN="top" COLSPAN="3">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
					<B>P &nbsp;  &nbsp; R &nbsp;  &nbsp; O &nbsp;  &nbsp; F &nbsp;  &nbsp; E &nbsp;  &nbsp; S &nbsp;  &nbsp; O &nbsp;  &nbsp; R &nbsp;  &nbsp; E &nbsp;  &nbsp; S</B>
				</FONT>
			</TD>
			<TD WIDTH="120" ALIGN="center" VALIGN="top">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
					<B>Fecha Aprobaci&oacute;n</B>
				</FONT>
			</TD>
		</TR>
<?
		$sqlcmd2 = "SELECT mid, codacta, cedula_profesor1, cedula_profesor2, cedula_profesor3, fecha_aprobacion "
				 . "FROM multiactas "
				 . "WHERE codcohorte='$_codcohorte' AND codasig='$codasig' ";


		$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");
		
		while ($registro2 = mysql_fetch_object($query2))
		{
			$mid = $registro2->mid;
			$codacta = $registro2->codacta;
			$cedula_profesor1 = $registro2->cedula_profesor1;
			$cedula_profesor2 = $registro2->cedula_profesor2;
			$cedula_profesor3 = $registro2->cedula_profesor3;
			$fecha_aprobacion = $registro2->fecha_aprobacion;


			$cedula_profesor1 = datos_profesor($cedula_profesor1);
			$cedula_profesor2 = datos_profesor($cedula_profesor2);
			$cedula_profesor3 = datos_profesor($cedula_profesor3);


			if ( ($fecha_aprobacion == '0000-00-00') OR ($fecha_aprobacion == "") )
			{
				$fecha_aprobacion = "";
			} else {
				$fecha_aprobacion = fecha ($fecha_aprobacion, corto);
			}

			$contador++;

			if ($bg_celda == '#CCCCCC')
			{
				$bg_celda = '#FFFFFF';
			} else {
				$bg_celda = '#CCCCCC';
			}

?>
			<TR>
				<TD WIDTH="110" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<A HREF="javascript:popup('_blank', '../ingresos/detalle_multiacta.php?codacta=<? echo $codacta ?>&mid=<? echo $mid ?>',650,350)">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>Ver</B></FONT></A><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"> &nbsp; </FONT>
					
					<A HREF="editando_datos_multiactas.php?_codacta=<? echo $codacta ?>&_codcohorte=<? echo $_codcohorte ?>&mid=<? echo $mid ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>Editar Acta</B></FONT></A>
				</TD>
				<TD WIDTH="180" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo $cedula_profesor1 ?>
					</FONT></TD>
				<TD WIDTH="180" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo $cedula_profesor2 ?>
					</FONT></TD>
				<TD WIDTH="180" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo $cedula_profesor3 ?>
					</FONT></TD>
				<TD WIDTH="120" ALIGN="right" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo $fecha_aprobacion ?>
					</FONT></TD>
			</TR>
<?
			}

			echo '</TABLE>';
		
			$encabezado_multiacta = '';
			$contador = '';
			$bg_celda = '#FFFFFF';

		}


}
?>


<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
