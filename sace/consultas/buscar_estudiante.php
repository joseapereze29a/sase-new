<?
###
###		Este script desplega una Forma con los campos de CI, Nombre(s) y/o Apellido(s)
###		para que el Operador pueda buscar a algun alumno por dichos campos. El script
###		despues mostrara una lista limitada a un maximo de 20 estudiantes, para que el
###		Operador seleccione al alumno el cual; se le desea consultar sus notas.
###


include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

if (	( ($Buscar) OR ($Buscar_x) ) AND ( ($_patron_nombre) OR ($_patron_apellido) OR ($_patron_ci) )		)
{

$sqlcmd = 'SELECT cedula, apellidos, nombres '
		. 'FROM datos_personales '
		. 'WHERE nombres like "%' . $_patron_nombre . '%" AND apellidos like "%' . $_patron_apellido . '%" AND '
		. 'cedula like "%' . $_patron_ci . '%" '
		. 'ORDER BY apellidos, nombres '
		. 'LIMIT 20 ';

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

}
?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
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

		<A HREF="ingreso_de_cedula.php"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Consultar Notas de un Estudiante</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Buscar Estudiante</B></FONT> 
	</TD>
</TR>
</TABLE>

<BR>

<IMG SRC="/sace/imagenes/menu_consultar_notas.jpg" ALT="" WIDTH="234" HEIGHT="18" BORDER="0">

<BR><BR>

<FORM ACTION="buscar_estudiante.php" METHOD="POST">

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Buscar Estudiantes</B>
		</FONT>
	</TD>
</TR>
</TABLE>


<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2">
<TR>
	<TD WIDTH="175" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Cedula</B>
		</FONT>
	</TD>
	<TD WIDTH="175" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Nombre</B>
		</FONT>
	</TD>
	<TD WIDTH="175" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Apellido</B>
		</FONT>
	</TD>
	<TD WIDTH="75" ALIGN="left" VALIGN="top">
		<P> </P>
	</TD>
</TR>
<TR>
	<TD WIDTH="175" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="_patron_ci" VALUE="<? echo $_patron_ci ?>" SIZE="12" MAXLENGTH="10">
	</TD>
	<TD WIDTH="175" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="_patron_nombre" VALUE="<? echo $_patron_nombre ?>" SIZE="17" MAXLENGTH="15">
	</TD>
	<TD WIDTH="175" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="_patron_apellido" VALUE="<? echo $_patron_apellido ?>" SIZE="17" MAXLENGTH="15">
	</TD>
	<TD WIDTH="75" ALIGN="right" VALIGN="top">
		<INPUT TYPE="submit" NAME="Buscar" VALUE="Buscar">
		<INPUT TYPE="hidden" NAME="Buscar_x" VALUE="Buscar">
	</TD>
</TR>
</TABLE>

</FORM>


<?
if (	( ($Buscar) OR ($Buscar_x) ) AND ( ($_patron_nombre) OR ($_patron_apellido) OR ($_patron_ci) )		) :
?>
		<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
		<TR>
			<TD WIDTH="30" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<P> </P>
			</TD>
			<TD WIDTH="170" ALIGN="center" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<B>C&eacute;dula de Identidad</B>
				</FONT>
			</TD>
			<TD WIDTH="200" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<B>Nombre(s)</B>
				</FONT>
			</TD>
			<TD WIDTH="200" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<B>Apellido(s)</B>
				</FONT>
			</TD>

		</TR>
<?
		while ($registro = mysql_fetch_object($query))
		{
			$cedula = $registro->cedula;
			$apellidos = strtolower($registro->apellidos);
			$nombres = strtolower($registro->nombres);

			if ($apellidos) $apellidos = ucwords($apellidos);
			if ($nombres) $nombres = ucwords($nombres);

			$contador++;

			if ($bg_celda == '#CCCCCC')
			{
				$bg_celda = '#FFFFFF';
			} else {
				$bg_celda = '#CCCCCC';
			}
			
			$cantidad_registros++;

?>
			<TR>
				<TD WIDTH="30" ALIGN="right" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<? echo $contador ?>
					</FONT>
				</TD>
				<TD WIDTH="170" ALIGN="right" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
				
					<A HREF="consulta_por_cedula.php?cedula=<? echo $cedula ?>">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#0000FF"><? echo strtr (number_format($cedula), ",", ".") ?></FONT></A>
					 &nbsp; &nbsp; &nbsp; &nbsp; 
							 
				</TD>
				<TD WIDTH="200" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<? echo $nombres ?>
					</FONT>
				</TD>
				<TD WIDTH="200" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<? echo $apellidos ?>
					</FONT>
				</TD>
			</TR>
<?
		}
?>
		</TABLE>

<?
	if ($cantidad_registros > 0)
	{


			if ($cantidad_registros >= 20)
			{
?>
				<BR>
				
				<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
				<TR>
					<TD WIDTH="600" ALIGN="center" VALIGN="top">
						<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
							<B>Recuerde que la B&uacute;squeda se limita a los primeros 20 Registros Encontrados.</B>
						</FONT>
					</TD>
				</TR>
				</TABLE>
	
<?
			} else {
?>

				<BR>
				
				<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
				<TR>
					<TD WIDTH="600" ALIGN="center" VALIGN="top">
						<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
							<B>Se encontraron <? echo $contador ?> Registros.</B>
						</FONT>
					</TD>
				</TR>
				</TABLE>
	
<?
			}


	} else {
?>

			<BR><BR><BR>
			
			<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
			<TR>
				<TD WIDTH="600" ALIGN="center" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<B>No se Encontr&oacute; ningun Registro que concuerde con la B&uacute;queda.</B>
					</FONT>
				</TD>
			</TR>
			</TABLE>

<?
	}


endif;
?>

</CENTER>

</BODY>
</HTML>
