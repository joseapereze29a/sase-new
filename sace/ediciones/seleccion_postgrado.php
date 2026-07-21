<?
###
###		Este script desplega el Listado de los Postgrados existentes para que 
###		el Usuario seleccione con cual va a Trabajar.
###


###
### Si no recibo el Codigo de la Sede (codsede), voy a al Script Anterior
###

if (! $_codsede)
{
	$url= 'seleccion_de_sede.php';
	header ("Location: $url");
	exit;
}


###
### Los Clasicos Includes
###			
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");


###
###	Busco en la Base de Datos, los campos necesarios para poder Desplegar la Informacion apropiada
###
$sqlcmd = "SELECT ciudad, edo_prov "
		. "FROM directorio_cippsv "
		. "WHERE codsede='$_codsede' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$ciudad = $registro->ciudad;
	$edo_prov = $registro->edo_prov;
}
?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#00CC00" VLINK="#CC0000">

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

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Postgrado</B></FONT>
	</TD>
</TR>
</TABLE>



<BR>

<IMG SRC="/sace/imagenes/titulos_de_home/titulo_editar.jpg" ALT="" WIDTH="363" HEIGHT="21" BORDER="0">

<BR><BR><BR>


<FONT FACE="Verdana,Arial,Geneva" COLOR="#000099">
	<B><? echo "$ciudad ($edo_prov)" ?></B> &nbsp;
</FONT>

		
<BR><BR><BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
	<B>Seleccione el Postgrado con el cual desea Trabajar</B>
</FONT>

<BR><BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
<TR>
	<TD WIDTH="330" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Postgrado</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Tipo</B>
		</FONT>
	</TD>
	<TD WIDTH="120" ALIGN="left" VALIGN="top">
		<P> </P>
	</TD>
</TR>
<?

###
###	Realizo el Query en la Base de Datos para ver cuales son Postgrados que se deben mostrar
###

	$sqlcmd = "SELECT codopest, tipo, mencion_especialidad "
			. "FROM oportunidades_estudio "
			. "WHERE codsede='$_codsede' "
			. "ORDER BY mencion_especialidad, tipo ";

	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

	while ($registro = mysql_fetch_object($query))
	{
		$codopest = $registro->codopest;
		$tipo = $registro->tipo;
		$mencion_especialidad = $registro->mencion_especialidad;
?>
		<TR>
			<TD WIDTH="330" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $mencion_especialidad ?>
				</FONT>
			</TD>
			<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $tipo ?>
				</FONT>
			</TD><FORM ACTION="seleccion_cohorte.php" METHOD="POST">
			<TD WIDTH="120" ALIGN="center" VALIGN="top" BGCOLOR="#FFFFFF">
				<INPUT TYPE="submit" NAME="seleccionar" VALUE="Seleccionar">
				<INPUT TYPE="hidden" NAME="_codsede" VALUE="<? echo $_codsede ?>">
				<INPUT TYPE="hidden" NAME="_codopest" VALUE="<? echo $codopest ?>">
			</TD></FORM>
		</TR>
<?
	}
?>
</TABLE>

<BR><BR>


<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
