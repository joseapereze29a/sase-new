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

<TABLE BORDER="0" WIDTH="100%" CELLSPACING="0" CELLPADDING="0">
<TR>
	<TD WIDTH="100%" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
	
		<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
		<TR>
			<TD WIDTH="130" ALIGN="center" VALIGN="middle">
				<A HREF="/sace/"><IMG SRC="/sace/imagenes/logo3.jpg" ALT="" WIDTH="111" HEIGHT="110" BORDER="0"></A>
			</TD><TD WIDTH="470" ALIGN="center" VALIGN="middle" BGCOLOR="#000099">
				<IMG SRC="/sace/imagenes/titulo_sace.jpg" ALT="" WIDTH="400" HEIGHT="35"><BR><BR>
			</TD>
		</TR>
		</TABLE>

	</TD>
</TR>
</TABLE>


<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
	<TD WIDTH="100%" ALIGN="left" VALIGN="top">
	
		<A HREF="../"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_de_sede.php"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n de Sede</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_postgrado.php?_codsede=<? echo $codsede ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Postgrado</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_cohorte.php?_codsede=<? echo $codsede ?>&_codopest=<? echo $codopest ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Cohortes Existentes</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_acta.php?_codcohorte=<? echo $_codcohorte ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Actas Existentes</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Acta</B></FONT> 
	</TD>
</TR>
</TABLE>

<?
	#include ("$DOCUMENT_ROOT/includes/encabezado.php");
?>

<BR>

<IMG SRC="/sace/imagenes/titulos_de_home/titulo_ingreso.jpg" ALT="" WIDTH="380" HEIGHT="20" BORDER="0">

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

<BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
<B>Pensum de Estudios</B>
</FONT>


<BR><BR>
	
<TABLE BORDER="0" WIDTH="710" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
<TR>
	<TD WIDTH="90" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Acta</B>
		</FONT>
	</TD>
	<TD WIDTH="80" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Codigo</B>
		</FONT>
	</TD>
	<TD WIDTH="280" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Asignatura</B>
		</FONT>
	</TD>
	<TD WIDTH="40" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Perio.</B>
		</FONT>
	</TD>
	<TD WIDTH="220" ALIGN="center" VALIGN="top" BGCOLOR="#000099" COLSPAN="2">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Seleccione el tipo de Ingreso</B>
		</FONT>
	</TD>
</TR>
<?

$sqlcmd = "CREATE TEMPORARY TABLE pensum_temp "
		. "SELECT codasig, asignatura, periodos, codasig_imp "
		. "FROM pensum_estudios "
		. "WHERE codsede='$codsede' AND codopest='$codopest' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

#echo "$sqlcmd<BR><BR>";

$sqlcmd = "CREATE TEMPORARY TABLE actas_temp "
		. "SELECT codasig, codacta "
		. "FROM registro_actas "
		. "WHERE codcohorte='$_codcohorte' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
#echo "$sqlcmd<BR><BR>";


$sqlcmd = "SELECT pensum_temp.codasig_imp, pensum_temp.codasig, pensum_temp.asignatura, pensum_temp.periodos, actas_temp.codacta "
		. "FROM pensum_temp LEFT JOIN actas_temp ON pensum_temp.codasig=actas_temp.codasig "
		. "ORDER BY pensum_temp.periodos, pensum_temp.codasig ";
#echo "$sqlcmd<BR><BR>";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$codasig_imp = $registro->codasig_imp;
	$codasig = $registro->codasig;
	$asignatura = $registro->asignatura;
	$periodos = $registro->periodos;
	$codacta = $registro->codacta;

	$pase_por_aqui = 1;


	$curso_d = substr($codacta, -2);
	$curso_d = strtolower($curso_d);
				
	if ($curso_d == "cd")
	{
		$asignatura = $asignatura . ' <B>(CD)</B>';
	}
	
	$curso_d == '';


	if ($bg_celda == '#CCCCCC')
	{
		$bg_celda = '#FFFFFF';
	} else {
		$bg_celda = '#CCCCCC';
	}

?>
<TR>
	<TD WIDTH="90" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			if ($codacta == NULL)
			{
		?>
				<P> </P>
		<?
			} else {
		?>
				<A HREF="javascript:popup('_blank', 'detalle_acta.php?codacta=<? echo $codacta ?>',650,600)">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF">
						<B>Ver el Acta</B>
					</FONT>
				</A> 
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo #&nbsp; $codacta ?>
					</FONT>
		<?
			}
		?>
	</TD>
	<TD WIDTH="80" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $codasig_imp ?>
		</FONT>
	</TD>
	<TD WIDTH="280" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $asignatura ?>
		</FONT>
	</TD>
	<TD WIDTH="40" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $periodos ?>
		</FONT>
	</TD>
	<TD WIDTH="110" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			$curso_d = substr($codacta, -2);
			$curso_d = strtolower($curso_d);
			
			if ($curso_d == 'cd')
			{
				$codacta_new = substr($codacta, 0, -2);

			} else {

				$codacta_new = $codacta;
			}
			
			$curso_d = '';
		?>
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<A HREF="ingresando_acta.php?_codcohorte=<? echo $_codcohorte ?>&_codasig=<? echo $codasig ?>&_codacta=<? echo $codacta_new ?>"><B>Lineal</B></A>
		</FONT>
	</TD>
	<TD WIDTH="110" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			$curso_d = substr($codacta, -2);
			$curso_d = strtolower($curso_d);

			if ($curso_d != 'cd')
			{
				if ($codacta != '') $codacta_new = $codacta . 'CD';
			} else {

				$codacta_new = $codacta;
			}
						
			$curso_d = '';
		?>
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<A HREF="ingresando_acta.php?_codcohorte=<? echo $_codcohorte ?>&_codasig=<? echo $codasig ?>&_codacta=<? echo $codacta_new ?>&_cd=1"><B>Curso D.</B></A>
		</FONT>
	</TD>
</TR>
<?
}

	$sqlcmd = "DROP TABLE pensum_temp, actas_temp ";
	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
?>
</TABLE>

<?
	if (! $pase_por_aqui):
?>

		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<BR><BR>
			<B>No se encontraron registros para dicha Cohorte.</B>
		</FONT>

<?
	endif;
?>

<BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>


<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
