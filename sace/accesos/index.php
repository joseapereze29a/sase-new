<?

### Este Script lista todos los Usuarios que tiene acceso al Sistema a traves de REALMs
### Para poder, crear nuevos Usuarios y darle sus Respectivos Privilegios o para
### Editar a los Usuarios existentes y/o cambiar sus Claves o Privilegios


### Los Clasicos Includes

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

### Hago el Query en la BD y Seleciono todos los usuarios del Sistema

$sqlcmd = "SELECT user, pass, usuario "
		. "FROM usuarios_sace "
		. "ORDER BY user ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
?>
<HTML>
<HEAD>
	<TITLE>Sistema Automatizado de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.3 - MacOS X">
</HEAD>
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
				<IMG SRC="/sace/imagenes/titulo_sace.jpg" ALT="" WIDTH="400" HEIGHT="35">
			</TD>
		</TR>
		</TABLE>

	</TD>
</TR>
</TABLE>


<?
	#include ("$DOCUMENT_ROOT/includes/encabezado.php");
?>

<BR><BR>

<TABLE BORDER="0" WIDTH="700" CELLSPACING="1" CELLPADDING="2">
<TR>
	<TD WIDTH="700" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<A HREF="agregar.php"><B>Agregar Usuario</B></A>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="700" CELLSPACING="1" CELLPADDING="3" BGCOLOR="#000000">
<TR>
	<TD ALIGN="left" WIDTH="250" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Usuario</B>
		</FONT>
	</TD>
	<TD ALIGN="left" WIDTH="150" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Clave</B>
		</FONT>
	</TD>
	<TD ALIGN="center" WIDTH="300" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Cambiar Clave o Accesos</B>
		</FONT>
	</TD>
</TR>
<?
	while ($registro = mysql_fetch_object($query))
	{
		$user = $registro->user;
		$pass = $registro->pass;
		$usuario = $registro->usuario;
		
		$cantidad_digitos = strlen($pass);
		
		$string_clave = "";
		$asteriscos = str_pad($string_clave, $cantidad_digitos, "*", STR_PAD_LEFT);

?>
<TR BGCOLOR="#FFFFFF">
	<TD ALIGN="left" WIDTH="250" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B><? echo $user ?></B> <? if ($usuario) echo '('. $usuario . ')' ?>
		</FONT>
	</TD>
	<TD ALIGN="left" WIDTH="150" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B><? echo $asteriscos ?></B>
		</FONT>
	</TD>
	<TD ALIGN="center" WIDTH="300" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B><A HREF="editar.php?user=<? echo $user ?>">Cambiar la Clave y/o Acceso(s)</A></B>
		</FONT>
	</TD>
</TR>
<?
	}
?>
</TABLE>

<BR><BR><BR><BR><BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
Ir al <A HREF="/sace/">Home</A>
</FONT>


<BR>


<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
