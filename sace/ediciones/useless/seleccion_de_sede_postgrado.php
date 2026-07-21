<?
### Este script esta FUERA de uso !!!

if ( ( ($continuar) OR ($continuar_x) ) AND ($_codsede) AND ($_codopest) )
{
	$url= 'seleccion_cohorte.php?_codsede=' . $_codsede . '&_codopest=' .  $_codopest;
	header ("Location: $url");
	exit;
}

			
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");
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
		<IMG SRC="/sace/imagenes/titulo_sistema_de_control_de_estudios.gif" ALT="" WIDTH="380" HEIGHT="22"><BR><BR>

		<TABLE BORDER="0" WIDTH="400" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<TD WIDTH="400" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<A HREF="../">Home</A>: Selecci&oacute;n de Sede
				</FONT>	
			</TD>
		</TR>
		</TABLE>

	</TD>
</TR>
</TABLE>

<?
	#include ("$DOCUMENT_ROOT/includes/encabezado.php");
?>

<BR>

<FONT FACE="Verdana,Arial,Geneva" COLOR="#0000FF">
<B>Edici&oacute;n de Actas</B>
</FONT>

<BR>

<FORM ACTION="seleccion_de_sede.php" METHOD="POST">

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
	<B>Seleccione la Sede a la cual Pertecene el Acta a Editar</B>
</FONT>

	<?
		if ( ( ($continuar) OR ($continuar_x) ) AND (! $_codsede) ) :
	?>
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FF0000">
			<BR><B>Usted debe seleccionar una Sede !</B>
		</FONT>
	<?
		endif;
	?>

<BR><BR>

<TABLE BORDER="0" WIDTH="500" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#FF0000">
<TR>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Ciudad</B>
		</FONT>
	</TD>
	<TD WIDTH="170" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Estado o Provincia</B>
		</FONT>
	</TD>
	<TD WIDTH="130" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Modalidad</B>
		</FONT>
	</TD>
	<TD WIDTH="50" ALIGN="left" VALIGN="top">
		<P> </P>
	</TD>
</TR>
<?
	$sqlcmd = "SELECT codsede, modalidad, ciudad, edo_prov "
			. "FROM directorio_cippsv "
			. "ORDER BY ciudad ";
	
	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

	while ($registro = mysql_fetch_object($query))
	{
		$codsede = $registro->codsede;
		$modalidad = $registro->modalidad;
		$ciudad = $registro->ciudad;
		$edo_prov = $registro->edo_prov;
?>
		<TR>
			<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $ciudad ?>
				</FONT>
			</TD>
			<TD WIDTH="170" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $edo_prov ?>
				</FONT>
			</TD>
			<TD WIDTH="130" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $modalidad ?>
				</FONT>
			</TD>
			<TD WIDTH="50" ALIGN="center" VALIGN="top" BGCOLOR="#FFFFFF">
				<?
					if ($codsede == $_codsede)
					{
						echo '<INPUT TYPE="radio" NAME="_codsede" VALUE="' . $codsede . '" CHECKED>';
					} else {
						echo '<INPUT TYPE="radio" NAME="_codsede" VALUE="' . $codsede . '">';
					}
				?>
			</TD>
		</TR>
<?
	}
?>
</TABLE>


<BR><BR><BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
	<B>Seleccione el PostGrado a el cual Pertecene el Acta a Editar</B>
</FONT>

	<?
		if ( ( ($continuar) OR ($continuar_x) ) AND (! $_codopest) ) :
	?>
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FF0000">
			<BR><B>Usted debe seleccionar un Postgrado !</B>
		</FONT>
	<?
		endif;
	?>

<BR><BR>

<TABLE BORDER="0" WIDTH="500" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#FF0000">
<TR>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Menci&oacute;n o Especialidad</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Tipo</B>
		</FONT>
	</TD>
	<TD WIDTH="50" ALIGN="left" VALIGN="top">
		<P> </P>
	</TD>
</TR>
<?
	$sqlcmd = "SELECT codopest, tipo, mencion_especialidad "
			. "FROM oportunidades_estudio "
			. "GROUP BY codopest "
			. "ORDER BY mencion_especialidad ";

	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

	while ($registro = mysql_fetch_object($query))
	{
		$codopest = $registro->codopest;
		$tipo = $registro->tipo;
		$mencion_especialidad = $registro->mencion_especialidad;
?>
		<TR>
			<TD WIDTH="300" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $mencion_especialidad ?>
				</FONT>
			</TD>
			<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $tipo ?>
				</FONT>
			</TD>
			<TD WIDTH="50" ALIGN="center" VALIGN="top" BGCOLOR="#FFFFFF">
				<?
					if ($codopest == $_codopest)
					{
						echo '<INPUT TYPE="radio" NAME="_codopest" VALUE="' . $codopest . '" CHECKED>';
					} else {
						echo '<INPUT TYPE="radio" NAME="_codopest" VALUE="' . $codopest . '">';
					}
				?>
			</TD>
		</TR>
<?
	}
?>
</TABLE>

<BR>

<TABLE WIDTH="500" BORDER="0" CELLSPACING="0" CELLPADDING="0">
<TR>
	<TD ALIGN="right" VALIGN="top">
		<INPUT TYPE="submit" NAME="continuar" VALUE="Continuar">
		<INPUT TYPE="hidden" NAME="continuar_x" VALUE="Continuar">
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
