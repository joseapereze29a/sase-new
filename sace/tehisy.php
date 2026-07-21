<?

###
### Los Clasicos Includes
###
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

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


<BR><BR>

<FONT FACE="Verdana,Arial,Geneva">
	Datos Personales Creados
</FONT>
		
<TABLE BORDER="0" WIDTH="400" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#CC0000">
<TR>
	<TD WIDTH="200" ALIGN="center" VALIGN="top" BGCOLOR="#FFFFFF">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Cantidad</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="center" VALIGN="top" BGCOLOR="#FFFFFF">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Creacion</B>
		</FONT>
	</TD>
</TR>
<?


$sqlcmd = "select count(*) as cantidad, DATE_FORMAT(fecha_creacion, '%Y-%m-%d') as fc from datos_personales where operador_creacion='tehisy' group by fc order by fc ";


$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$cantidad = $registro->cantidad;
	$fc = $registro->fc;
?>
<TR>
	<TD WIDTH="200" ALIGN="right" VALIGN="top" BGCOLOR="#FFFFFF">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $cantidad ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="right" VALIGN="top" BGCOLOR="#FFFFFF">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo fecha($fc) ?>
		</FONT>
	</TD>
</TR><?
}
?>
</TABLE>

<BR><BR>

<FONT FACE="Verdana,Arial,Geneva">
	Datos Personales Modificados
</FONT>
		
<TABLE BORDER="0" WIDTH="400" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#CC0000">
<TR>
	<TD WIDTH="200" ALIGN="center" VALIGN="top" BGCOLOR="#FFFFFF">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Cantidad</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="center" VALIGN="top" BGCOLOR="#FFFFFF">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Modificaci&oacute;n</B>
		</FONT>
	</TD>
</TR>
<?


$sqlcmd = "select count(*) as cantidad, DATE_FORMAT(fecha_modificacion, '%Y-%m-%d') as fm from datos_personales where operador_modificacion='tehisy' group by fm order by fm ";


$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$cantidad = $registro->cantidad;
	$fm = $registro->fm;
?>
<TR>
	<TD WIDTH="200" ALIGN="right" VALIGN="top" BGCOLOR="#FFFFFF">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $cantidad ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="right" VALIGN="top" BGCOLOR="#FFFFFF">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo fecha($fm) ?>
		</FONT>
	</TD>
</TR><?
}
?>
</TABLE>

</CENTER>

</BODY>
</HTML>
