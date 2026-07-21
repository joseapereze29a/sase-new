<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_datos_profesor.php");

$sqlcmd = "SELECT registro_actas.codasig, pensum_estudios.creditos, pensum_estudios.asignatura,  "
		. "registro_actas.fecha_aprobacion, registro_actas.cedula_profesor "
		. "FROM registro_actas, pensum_estudios, cohortes "
		. "WHERE registro_actas.codasig=pensum_estudios.codasig AND "
		. "registro_actas.codcohorte=cohortes.codcohorte AND cohortes.codsede=pensum_estudios.codsede AND "
		. "cohortes.codopest=pensum_estudios.codopest AND registro_actas.codacta='$codacta' ";


$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$codasig = $registro->codasig;
	$creditos = $registro->creditos;
	$asignatura = $registro->asignatura;
	$fecha_aprobacion = $registro->fecha_aprobacion;
	$cedula_profesor = $registro->cedula_profesor;
}

if ( ($fecha_aprobacion == '0000-00-00') OR ($fecha_aprobacion == "") )
{
	$fecha_aprobacion = "";

} else {

	$fecha_aprobacion = fecha($fecha_aprobacion);
}

$apellidos_nombres = datos_profesor($cedula_profesor);


/*
$sqlcmd2 = "SELECT apellidos_nombres FROM profesores_cippsv WHERE cedula_profesor='$cedula_profesor' ";

$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

while ($registro2 = mysql_fetch_object($query2))
{
	$apellidos_nombres  = strtolower($registro2->apellidos_nombres);
}
*/

/*
$sqlcmd = "SELECT record_notas.cedula, record_notas.calificacion, record_notas.codeq, "
		. "datos_personales.apellidos, datos_personales.nombres "
		. "FROM record_notas, datos_personales "
		. "WHERE record_notas.cedula=datos_personales.cedula AND codacta='$codacta' "
		. "ORDER BY datos_personales.apellidos, datos_personales.nombres ";
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
			<B>Informaci&oacute;n sobre la Asignatura</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Codigo</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Creditos</B>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Asignatura</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $codasig ?>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $creditos ?>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $asignatura ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Aprobaci&oacute;n</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>C&eacute;dula del Profesor</B>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<B>Profesor</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $fecha_aprobacion ?>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo strtr (number_format($cedula_profesor), ",", ".") ?>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
			<? echo $apellidos_nombres ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>


<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
<TR>
	<TD WIDTH="90" ALIGN="center" VALIGN="top">
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
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Equivalencia</B>
		</FONT>
	</TD>
</TR>
<?
$sqlcmd = "SELECT record_notas.cedula, record_notas.calificacion, record_notas.codeq "
		. "FROM record_notas "
		. "WHERE codacta='$codacta' "
		. "ORDER BY record_notas.cedula ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$calificacion = "";
	$cedula = "";
	$apellidos = "";
	$nombres = "";
	
	$cedula = $registro->cedula;
	$calificacion = $registro->calificacion;
	$codeq = $registro->codeq;


	$sqlcmd2 = "SELECT datos_personales.apellidos, datos_personales.nombres "
			 . "FROM datos_personales "
			 . "WHERE datos_personales.cedula='$cedula' ";

	$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

	while ($registro2 = mysql_fetch_object($query2))
	{
		$apellidos = strtolower($registro2->apellidos);
		$nombres = strtolower($registro2->nombres);
	}

	if ( ($calificacion >= 1) AND ($calificacion <= 20) )
	{
		$notas = $notas + $calificacion;
		
		$contador++;
	}

	if ($bg_celda == '#CCCCCC')
	{
		$bg_celda = '#FFFFFF';
	} else {
		$bg_celda = '#CCCCCC';
	}
?>
<TR>
	<TD WIDTH="90" ALIGN="right" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			if ( ($calificacion >= 1) AND ($calificacion <= 14) )
			{

				echo '<A HREF="consulta_por_cedula.php?cedula=' . $cedula . '" TARGET="_blank">';
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>' . strtr (number_format($cedula), ",", ".") . '</B></A> &nbsp; </FONT>';


			} else {
			
				echo '<A HREF="consulta_por_cedula.php?cedula=' . $cedula . '" TARGET="_blank">';
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>' . strtr (number_format($cedula), ",", ".") . '</B></A> &nbsp; </FONT>';

			}
		?>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			if ( ($calificacion >= 1) AND ($calificacion <= 14) )
			{
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>' . ucwords($apellidos) . '</B></FONT>';
				
			} else {
			
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">' . ucwords($apellidos) . '</FONT>';
			}
		?>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			if ( ($calificacion >= 1) AND ($calificacion <= 14) )
			{
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>' . ucwords($nombres) . '</B></FONT>';
				
			} else {
			
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">' . ucwords($nombres) . '</FONT>';
			}
		?>
	</TD>
	<TD WIDTH="60" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<?
			if ( ($calificacion >= 1) AND ($calificacion <= 14) )
			{
				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#3300FF"><B>' . $calificacion . '</B></FONT>';
				
			} else {

				#if ($calificacion == 321) $calificacion = 'Retir&oacute;';
				
				if ($calificacion == 404) $calificacion = 'No Curs&oacute;';

				if ($calificacion == 99) $calificacion = 'Reprobado';

				if ($calificacion == 100) $calificacion = 'Aprobado';
				
				if ($calificacion == 110) $calificacion = 'Meritorio';
				
				if ($calificacion == 120) $calificacion = 'Excelencia';
				
				if ($calificacion == 212) $calificacion = 'Equivalencia';


				echo '<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">' . $calificacion . '</FONT>';
			}
		?>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
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
	if ($contador >1):
?>
		<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2">
		<TR>
			<TD WIDTH="600" ALIGN="center" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
					<B>Promedio del Alumnado en esta Asignatura: <? echo number_format(($notas/$contador), 2, ',', '')  ?></B>
				</FONT>
			</TD>
		</TR>
		</TABLE>

<?
	endif;
?>


</CENTER>

</BODY>
</HTML>
