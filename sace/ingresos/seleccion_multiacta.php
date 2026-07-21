<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_construyo_codacta.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_datos_profesor.php");


if ($cancelar)
{
	$url = "seleccion_acta.php?_codcohorte=" . $_codcohorte;

	header("Location: $url");
	exit;
}


if ($continuar)
{
		$url = "ingresando_multiacta.php?_codcohorte=" . $_codcohorte . '&_codasig=' . $_codasig;

		if ($_cd) $url = $url . '&_cd=' . $_cd;
	
		header("Location: $url");
		exit;
}

$sqlcmd = "SELECT directorio_cippsv.modalidad, directorio_cippsv.ciudad, directorio_cippsv.edo_prov, oportunidades_estudio.tipo, "
		. "oportunidades_estudio.mencion_especialidad, cohortes.fecha_inicio, cohortes.codsede, cohortes.codopest "
		. "FROM directorio_cippsv, oportunidades_estudio, cohortes "
		. "WHERE cohortes.codcohorte='$_codcohorte' AND cohortes.codsede=oportunidades_estudio.codsede AND "
		. "cohortes.codopest=oportunidades_estudio.codopest AND oportunidades_estudio.codsede=directorio_cippsv.codsede ";

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



### Busco en la DB como se llama la Asignatura

$sqlcmd = "SELECT pensum_estudios.asignatura "
		. "FROM pensum_estudios, cohortes "
		. "WHERE cohortes.codsede=pensum_estudios.codsede AND cohortes.codopest=pensum_estudios.codopest AND "
		. "cohortes.codcohorte='$_codcohorte' AND cohortes.codsede='$codsede' AND cohortes.codopest='$codopest' AND "
		. "pensum_estudios.codasig='$_codasig'";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$asignatura = $registro->asignatura;
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

		<A HREF="ingreso_acta.php?_codcohorte=<? echo $_codcohorte ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Acta</B></FONT></A>
		
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

<FORM ACTION="seleccion_multiacta.php" METHOD="POST">

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
<B>
<?
	if ($_cd)	echo $asignatura . " (CD $_cd)";
	if (! $_cd)	echo $asignatura;
?>
</B>
</FONT>

<BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
Seleccione un Acta ya existente o un Cree una Nueva.<BR><BR>
</FONT>


<?
	### Construyo el Acta (codacta)

	$codacta_final = construyo_codacta($_codcohorte,$_codasig,$_cd);


##	list($cohorte_parte_1, $cohorte_parte_2) = split ('-', $_codcohorte);

##	list($asig_parte_1, $asig_parte_2) = split ('-', $_codasig);

##	$asig_parte_2 = substr("$asig_parte_2", -2);

##	$codacta_final = $cohorte_parte_1 . $cohorte_parte_2 . '-' . $asig_parte_2;

##	if ($_cd) $codacta_final = $codacta_final . 'CD' . $_cd;



	### Reviso si ya existe alguna acta

	$sqlcmd = "SELECT count(*) AS cantidad "
			. "FROM multiactas "
			. "WHERE codcohorte='$_codcohorte' AND codasig='$_codasig' AND codacta='$codacta_final' ";


	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
	while ($registro = mysql_fetch_object($query))
	{
		$cantidad = $registro->cantidad;
	}


	### Si no existe una Acta, muestro mensaje, sino Muestro Actas Existentes
	if ($cantidad < 1)
	{
		echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
		echo '<B>No Existen Actas para la asignatura Asociada.</B>';
		echo '</FONT><BR>';
	
	} else {
?>
		<TABLE BORDER="0" WIDTH="765" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
		<TR>
			<TD WIDTH="30" ALIGN="left" VALIGN="top">
				<P> </P>
			</TD>
			<TD WIDTH="30" ALIGN="left" VALIGN="top">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
					<B>N&uacute;m.</B>
				</FONT>
			</TD>
			<TD WIDTH="525" ALIGN="center" VALIGN="top" COLSPAN="3">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
					<B>P &nbsp;  &nbsp; R &nbsp;  &nbsp; O &nbsp;  &nbsp; F &nbsp;  &nbsp; E &nbsp;  &nbsp; S &nbsp;  &nbsp; O &nbsp;  &nbsp; R &nbsp;  &nbsp; E &nbsp;  &nbsp; S</B>
				</FONT>
			</TD>
			<TD WIDTH="130" ALIGN="center" VALIGN="top">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
					<B>Fecha</B>
				</FONT>
			</TD>
			<TD WIDTH="50" ALIGN="left" VALIGN="top">
				<P> </P>
			</TD>
		</TR>
<?

		### Busco en la DB las Actas Existentes
		$sqlcmd = "SELECT mid, cedula_profesor1, cedula_profesor2, cedula_profesor3, fecha_aprobacion "
				. "FROM multiactas "
				. "WHERE codcohorte='$_codcohorte' AND codasig='$_codasig' AND codacta='$codacta_final' "
				. "ORDER BY fecha_aprobacion ";
	

		$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
		
		while ($registro = mysql_fetch_object($query))
		{
			$mid = $registro->mid;
			$cedula_profesor1 = $registro->cedula_profesor1;
			$cedula_profesor2 = $registro->cedula_profesor2;
			$cedula_profesor3 = $registro->cedula_profesor3;
			$fecha_aprobacion = $registro->fecha_aprobacion;
			$contador++;

		if ( ($fecha_aprobacion == '0000-00-00') OR ($fecha_aprobacion == "") )
		{
			$fecha_aprobacion = 'No Existe Registro';
		
		} else {
		
			$fecha_aprobacion = fecha($fecha_aprobacion, 'corto');
		}
		
		$cedula_profesor1 = datos_profesor($cedula_profesor1);
		$cedula_profesor2 = datos_profesor($cedula_profesor2);
		$cedula_profesor3 = datos_profesor($cedula_profesor3);
/*
		$sqlcmd2 = "SELECT apellidos_nombres FROM profesores_cippsv WHERE cedula_profesor='$cedula_profesor1' ";

		$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");
		
		while ($registro2 = mysql_fetch_object($query2))
		{
			$apellidos_nombres_prof1 = $registro2->apellidos_nombres;
		}

		$sqlcmd2 = "SELECT apellidos_nombres FROM profesores_cippsv WHERE cedula_profesor='$cedula_profesor2' ";

		$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");
		
		while ($registro2 = mysql_fetch_object($query2))
		{
			$apellidos_nombres_prof2 = $registro2->apellidos_nombres;
		}

		$sqlcmd2 = "SELECT apellidos_nombres FROM profesores_cippsv WHERE cedula_profesor='$cedula_profesor3' ";

		$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");
		
		while ($registro2 = mysql_fetch_object($query2))
		{
			$apellidos_nombres_prof3 = $registro2->apellidos_nombres;
		}
*/


		if ($bg_celda == '#CCCCCC')
		{
			$bg_celda = '#FFFFFF';
		} else {
			$bg_celda = '#CCCCCC';
		}

?>
			<TR>
				<TD WIDTH="30" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<A HREF="javascript:popup('_blank', 'detalle_multiacta.php?codacta=<? echo $codacta_final ?>&mid=<? echo $mid ?>',650,600)">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF">Ver</FONT></A>
				</TD>
				<TD WIDTH="30" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo $contador ?>
					</FONT>
				</TD>
				<TD WIDTH="175" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo $cedula_profesor1 ?>
					</FONT>
				</TD>
				<TD WIDTH="175" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo $cedula_profesor2 ?>
					</FONT>
				</TD>
				<TD WIDTH="175" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo $cedula_profesor3 ?>
					</FONT>
				</TD>
				<TD WIDTH="130" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo $fecha_aprobacion ?>
					</FONT>
				</TD>
				<TD WIDTH="50" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<?
							$url = '';
						
							$url = "ingresando_multiacta.php?_codcohorte=" . $_codcohorte . '&_codasig=' . $_codasig . '&_codacta=' . $codacta_final . '&mid=' . $mid;
							
							if ($_cd) $url = $url . '&_cd=' . $_cd;
						?>
						<A HREF="<? echo $url ?>">Agregar</A>
					</FONT>
				</TD>
			</TR>
<?

			#$cedula_profesor1 = '';
			#$cedula_profesor2 = '';
			#$cedula_profesor3 = '';
			
			#$apellidos_nombres_prof1 = '';
			#$apellidos_nombres_prof2 = '';
			#$apellidos_nombres_prof3 = '';
			
		}

		echo '</TABLE>';
	}
?>


<BR>

<BR><BR>

<INPUT TYPE="hidden" NAME="_codcohorte" VALUE="<? echo $_codcohorte ?>">
<INPUT TYPE="hidden" NAME="_codasig" VALUE="<? echo $_codasig ?>">
<INPUT TYPE="hidden" NAME="_codacta" VALUE="<? echo $codacta_final ?>">
<INPUT TYPE="hidden" NAME="_cd" VALUE="<? echo $_cd ?>">

<INPUT TYPE="submit" NAME="continuar" VALUE="Crear Nueva Acta">
<INPUT TYPE="submit" NAME="cancelar" VALUE="Cancelar">

<BR><BR>

</FORM>

<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
