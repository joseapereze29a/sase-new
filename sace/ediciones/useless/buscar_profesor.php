<?
#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

if ( ($Buscar) OR ($Buscar_x) )
{

$sqlcmd = 'SELECT cedula_profesor, apellidos_nombres '
		. 'FROM profesores_cippsv '
		. 'WHERE apellidos_nombres like "%' . $_patron_nombre . '%" AND apellidos_nombres like "%' . $_patron_apellido . '%" AND '
		. 'cedula_profesor like "%' . $_patron_ci . '%" '
		. 'ORDER BY apellidos_nombres '
		. 'LIMIT 30 ';

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

}
?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#00CC00" VLINK="#CC0000">

<CENTER>

<FORM ACTION="buscar_profesor.php" METHOD="POST">

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#FF0000">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Buscar Profesores</B>
		</FONT>
	</TD>
</TR>
</TABLE>


<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Nombre</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Apellido</B>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Cedula</B>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<P> </P>
	</TD>
</TR>
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="_patron_nombre" VALUE="<? echo $_patron_nombre ?>" SIZE="17" MAXLENGTH="15">
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="_patron_apellido" VALUE="<? echo $_patron_apellido ?>" SIZE="17" MAXLENGTH="15">
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="_patron_ci" VALUE="<? echo $_patron_ci ?>" SIZE="12" MAXLENGTH="10">
	</TD>
	<TD WIDTH="100" ALIGN="right" VALIGN="top">
		<INPUT TYPE="submit" NAME="Buscar" VALUE="Buscar">
		<INPUT TYPE="hidden" NAME="Buscar_x" VALUE="Buscar">
	</TD>
</TR>
</TABLE>

</FORM>

<BR>

<?
if ( ($Buscar) OR ($Buscar_x) ) :
?>
		<TABLE BORDER="0" WIDTH="600" CELLSPACING="0" CELLPADDING="2">
		<TR>
			<TD WIDTH="400" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<B>Apellido(s), Nombre(s)</B>
				</FONT>
			</TD>
			<TD WIDTH="200" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<B>C&eacute;dula de Identidad</B>
				</FONT>
			</TD>
		</TR>
<?
		while ($registro = mysql_fetch_object($query))
		{
			$cedula_profesor = $registro->cedula_profesor;
			$apellidos_nombres = $registro->apellidos_nombres;

			if ($bg_celda == '#CCCCCC')
			{
				$bg_celda = '#FFFFFF';
			} else {
				$bg_celda = '#CCCCCC';
			}

?>
			<TR>
				<TD WIDTH="400" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<? echo $apellidos_nombres ?>
					</FONT>
				</TD>
				<TD WIDTH="200" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva"><? echo $cedula_profesor ?></FONT></TD>
			</TR>
<?
		}
?>
		</TABLE>

<?
endif;
?>

</CENTER>

</BODY>
</HTML>
