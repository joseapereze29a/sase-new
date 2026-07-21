<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/arreglo_meses_dias.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_datos_profesor.php");

if ( ($continuar) OR ($cancelar) )
{

	if ($cancelar)
	{
		$url = "seleccion_acta.php?_codcohorte=" . $_codcohorte;

		header("Location: $url");
		exit;
	}


	if (	($_ci_profesor1) AND (! ereg ("^[0-9]+$", $_ci_profesor1) )	)
	{
		$error_ci_profesor = 1;
	}


	if (	($_ci_profesor2) AND (! ereg ("^[0-9]+$", $_ci_profesor2) )	)
	{
		$error_ci_profesor = 1;
	}


	if (	($_ci_profesor3) AND (! ereg ("^[0-9]+$", $_ci_profesor3) )	)
	{
		$error_ci_profesor = 1;
	}



	if ( ($_dia < 0) OR ($_dia > 31) OR ($_mes < 0 ) OR ($_mes > 12) OR ($_ano > (date(Y)+1) ) )
	{
		$error_fecha_aprobacion = 1;
	}


	if (	( ($_dia == 0) OR ($_mes == 0) OR ($_ano == 0) )		AND 	( ($_dia != 0) OR ($_mes != 0) OR ($_ano != 0) )	)
	{
		$error_fecha_aprobacion = 1;
	}



	if ( (! $error_ci_profesor) AND (! $error_fecha_aprobacion) )
	{

		if ( ($_dia > 0 ) AND ($_mes > 0) AND ($_ano > 0 ) )
		{
			$fecha_db = $_ano . '-' . $_mes . '-' . $_dia;
		
		} else {
		
			$fecha_db = '0';
		}

		if (! $_ci_profesor1) $_ci_profesor1 = 0;
		if (! $_ci_profesor2) $_ci_profesor2 = 0;
		if (! $_ci_profesor3) $_ci_profesor3 = 0;


		$sqlcmd = "UPDATE multiactas SET "
				. "cedula_profesor1='$_ci_profesor1', cedula_profesor2='$_ci_profesor2', cedula_profesor3='$_ci_profesor3', "
				. "fecha_aprobacion='$fecha_db', fecha_modificacion=NOW(), operador_modificacion='$PHP_AUTH_USER', "
				. "host_modificacion='$REMOTE_ADDR' "
				. "WHERE codacta='$_codacta' AND mid='$mid' ";

		$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");

		if ($fecha_db == '0')
		{
			$sqlcmd = "UPDATE multiactas SET fecha_aprobacion=NULL WHERE codacta='$_codacta' AND mid='$mid' ";
	
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
		}


		if ($_ci_profesor1 == '0')
		{
			$sqlcmd = "UPDATE multiactas SET cedula_profesor1=NULL WHERE codacta='$_codacta' AND mid='$mid' ";
	
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
		}

		if ($_ci_profesor2 == '0')
		{
			$sqlcmd = "UPDATE multiactas SET cedula_profesor2=NULL WHERE codacta='$_codacta' AND mid='$mid' ";
	
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
		}

		if ($_ci_profesor3 == '0')
		{
			$sqlcmd = "UPDATE multiactas SET cedula_profesor3=NULL WHERE codacta='$_codacta' AND mid='$mid' ";
	
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
		}


		if ($_ci_profesor1 == '0')	$_ci_profesor1 = 'NULL';
		if ($_ci_profesor2 == '0')	$_ci_profesor2 = 'NULL';
		if ($_ci_profesor3 == '0')	$_ci_profesor3 = 'NULL';

		if ($fecha_db == '0')
		{
			$fecha_db = 'NULL';

		} else {
		
			$fecha_db = $_ano . '-' . ABS($_mes) . '-' . ABS($_dia);
		}



		if ( ($fecha_aprobacion_anterior != $fecha_db) AND (! ( ($fecha_db == 'NULL') AND ($fecha_aprobacion_anterior == '0-0-0') ) ) )
		{

			if ($fecha_aprobacion_anterior == '0-0-0')	$fecha_aprobacion_anterior = 'NULL';

			$sqlcmd = "INSERT INTO ediciones_multiactas (mid, codcohorte, codacta, fecha_modificacion, operador_modificacion, host_modificacion, operacion) VALUES ("
					. "'$mid', '$_codcohorte', '$_codacta', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR', "
					. "'Se modifico la fecha del acta: $_codacta. La anterior era $fecha_aprobacion_anterior, la nueva es $fecha_db.') "; 
	
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
			$emid = mysql_insert_id();
	
	
			$sqlcmd = "UPDATE ediciones_multiactas SET transaccion_id='$emid' WHERE emid='$emid' ";
			
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
			
			$agregue_fecha = 1;
		
		}


		if ($cedula_profesor_anterior1 == '')	$cedula_profesor_anterior1 = 'NULL';
		if ($cedula_profesor_anterior2 == '')	$cedula_profesor_anterior2 = 'NULL';
		if ($cedula_profesor_anterior3 == '')	$cedula_profesor_anterior3 = 'NULL';


		if ( ($_ci_profesor1 != $cedula_profesor_anterior1) AND ($agregue_fecha) AND (! ( ($_ci_profesor1 == 'NULL') AND ($cedula_profesor_anterior1 == 'NULL') ) ) )
		{
			$sqlcmd = "INSERT INTO ediciones_multiactas (mid, transaccion_id, codcohorte, codacta, fecha_modificacion, operador_modificacion, host_modificacion, operacion) VALUES ("
					. "'$mid', '$emid', '$_codcohorte', '$_codacta', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR', "
					. "'Se modifico la cedula del profesor del acta: $_codacta. La anterior era: $cedula_profesor_anterior1, la nueva es: $_ci_profesor1.') ";

			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
		}

		if ( ($_ci_profesor2 != $cedula_profesor_anterior2) AND ($agregue_fecha) AND (! ( ($_ci_profesor2 == 'NULL') AND ($cedula_profesor_anterior2 == 'NULL') ) ) )
		{
			$sqlcmd = "INSERT INTO ediciones_multiactas (mid, transaccion_id, codcohorte, codacta, fecha_modificacion, operador_modificacion, host_modificacion, operacion) VALUES ("
					. "'$mid', '$emid', '$_codcohorte', '$_codacta', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR', "
					. "'Se modifico la cedula del profesor del acta: $_codacta. La anterior era: $cedula_profesor_anterior2, la nueva es: $_ci_profesor2.') ";

			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
		}

		if ( ($_ci_profesor3 != $cedula_profesor_anterior3) AND ($agregue_fecha) AND (! ( ($_ci_profesor3 == 'NULL') AND ($cedula_profesor_anterior3 == 'NULL') ) ) )
		{
			$sqlcmd = "INSERT INTO ediciones_multiactas (mid, transaccion_id, codcohorte, codacta, fecha_modificacion, operador_modificacion, host_modificacion, operacion) VALUES ("
					. "'$mid', '$emid', '$_codcohorte', '$_codacta', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR', "
					. "'Se modifico la cedula del profesor del acta: $_codacta. La anterior era: $cedula_profesor_anterior3, la nueva es: $_ci_profesor3.') ";

			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
		}




		if ( ($_ci_profesor1 != $cedula_profesor_anterior1) AND (! $agregue_fecha) AND (! ( ($_ci_profesor1 == 'NULL') AND ($cedula_profesor_anterior1 == 'NULL') ) ) )
		{
			$sqlcmd = "INSERT INTO ediciones_multiactas (mid, transaccion_id, codcohorte, codacta, fecha_modificacion, operador_modificacion, host_modificacion, operacion) VALUES ("
					. "'$mid', '$emid', '$_codcohorte', '$_codacta', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR', "
					. "'Se modifico la cedula del profesor del acta: $_codacta. La anterior era: $cedula_profesor_anterior1, la nueva es: $_ci_profesor1.') ";

			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");


			if (! $emid)	$emid = mysql_insert_id();
	
	
			$sqlcmd = "UPDATE ediciones_multiactas SET transaccion_id='$emid' WHERE emid='$emid' ";
			
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");

		}

		if ( ($_ci_profesor2 != $cedula_profesor_anterior2) AND (! $agregue_fecha) AND (! ( ($_ci_profesor2 == 'NULL') AND ($cedula_profesor_anterior2 == 'NULL') ) ) )
		{
			$sqlcmd = "INSERT INTO ediciones_multiactas (mid, transaccion_id, codcohorte, codacta, fecha_modificacion, operador_modificacion, host_modificacion, operacion) VALUES ("
					. "'$mid', '$emid', '$_codcohorte', '$_codacta', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR', "
					. "'Se modifico la cedula del profesor del acta: $_codacta. La anterior era: $cedula_profesor_anterior2, la nueva es: $_ci_profesor2.') ";

			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");


			if (! $emid)	$emid = mysql_insert_id();
	
	
			$sqlcmd = "UPDATE ediciones_multiactas SET transaccion_id='$emid' WHERE emid='$emid' ";
			
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
		}

		if ( ($_ci_profesor3 != $cedula_profesor_anterior3) AND (! $agregue_fecha) AND (! ( ($_ci_profesor3 == 'NULL') AND ($cedula_profesor_anterior3 == 'NULL') ) ) )
		{
			$sqlcmd = "INSERT INTO ediciones_multiactas (mid, transaccion_id, codcohorte, codacta, fecha_modificacion, operador_modificacion, host_modificacion, operacion) VALUES ("
					. "'$mid', '$emid', '$_codcohorte', '$_codacta', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR', "
					. "'Se modifico la cedula del profesor del acta: $_codacta. La anterior era: $cedula_profesor_anterior3, la nueva es: $_ci_profesor3.') ";

			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");


			if (! $emid)	$emid = mysql_insert_id();
	
	
			$sqlcmd = "UPDATE ediciones_multiactas SET transaccion_id='$emid' WHERE emid='$emid' ";
			
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
		}



		$url = "edicion_datos_acta_finalizado.php?_codacta=" . $_codacta . "&_codcohorte=" . $_codcohorte;

		header("Location: $url");
		exit;
	}


}
		


$sqlcmd = "SELECT directorio_cippsv.modalidad, directorio_cippsv.ciudad, directorio_cippsv.edo_prov, oportunidades_estudio.tipo, "
		. "oportunidades_estudio.mencion_especialidad, cohortes.fecha_inicio, cohortes.codsede, cohortes.codopest "
		. "FROM directorio_cippsv, oportunidades_estudio, cohortes "
		. "WHERE cohortes.codcohorte='$_codcohorte' AND cohortes.codsede=oportunidades_estudio.codsede AND "
		. "cohortes.codopest=oportunidades_estudio.codopest AND oportunidades_estudio.codsede=directorio_cippsv.codsede ";

#echo "$sqlcmd<BR>";

/*
+-----------+---------+------------------+----------+------------------------+--------------+---------+----------+
| modalidad | ciudad  | edo_prov         | tipo     | mencion_especialidad   | fecha_inicio | codsede | codopest |
+-----------+---------+------------------+----------+------------------------+--------------+---------+----------+
| Sede      | Caracas | Distrito Federal | Maestria | Terapia de la Conducta | 2003-09-05   | PPAL    | MC-TC    |
+-----------+---------+------------------+----------+------------------------+--------------+---------+----------+
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

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");
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

		<A HREF="seleccion_postgrado.php?_codsede=<? echo $codsede ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Postgrado</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_cohorte.php?_codsede=<? echo $codsede ?>&_codopest=<? echo $codopest ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Cohortes Existentes</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_acta.php?_codcohorte=<? echo $_codcohorte ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Actas Existentes</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Edici&oacute;n de Acta</B></FONT>
	</TD>
</TR>
</TABLE>

<BR>

<IMG SRC="/sace/imagenes/titulos_de_home/titulo_editar.jpg" ALT="" WIDTH="363" HEIGHT="21" BORDER="0">

<BR><BR><BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Informaci&oacute;n sobre el Postgrado o Especializaci&oacute;n</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Ciudad</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
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
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $ciudad ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
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

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Menci&oacute;n o Especialidad</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Tipo</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $mencion_especialidad ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $tipo ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Inicio de la Cohorte</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Cohorte</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo fecha($fecha_inicio) ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $_codcohorte ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<?
	$sqlcmd = "SELECT multiactas.codasig, pensum_estudios.asignatura "
			. "FROM multiactas, pensum_estudios "
			. "WHERE multiactas.codasig=pensum_estudios.codasig AND multiactas.codacta='$_codacta'";
	
	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
	while ($registro = mysql_fetch_object($query))
	{
		$codasig = $registro->codasig;
		$asignatura = $registro->asignatura;
	}

	$curso_d = substr($_codacta, -3, 2);
	$curso_d = strtolower($curso_d);
		
	if ($curso_d == "cd")
	{
		$asignatura = $asignatura . ' <B>(CD)</B>';
	}
	
	$curso_d == '';

?>
<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Asignatura</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>C&oacute;digo Asignatura</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $asignatura ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $codasig ?>
		</FONT>
	</TD>
</TR>
</TABLE>


<FORM ACTION="editando_datos_multiactas.php" METHOD="post">

<?

	$sqlcmd = "SELECT cedula_profesor1, cedula_profesor2, cedula_profesor3, fecha_aprobacion "
			. "FROM multiactas "
			. "WHERE codacta='$_codacta' AND mid='$mid' ";
	
	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
	while ($registro = mysql_fetch_object($query))
	{
		$cedula_profesor1 = $registro->cedula_profesor1;
		$cedula_profesor2 = $registro->cedula_profesor2;
		$cedula_profesor3 = $registro->cedula_profesor3;
		$fecha_aprobacion = $registro->fecha_aprobacion;
	}

	if (! $cedula_profesor_anterior1)	$cedula_profesor_anterior1 = $cedula_profesor1;
	if (! $cedula_profesor_anterior2)	$cedula_profesor_anterior2 = $cedula_profesor2;
	if (! $cedula_profesor_anterior3)	$cedula_profesor_anterior3 = $cedula_profesor3;

	$fecha_aprobacion_anterior = $fecha_aprobacion;


	list($_ano_anterior,$_mes_anterior,$_dia_anterior) = split ("-", $fecha_aprobacion_anterior);
	$fecha_aprobacion_anterior = ABS($_ano_anterior) . '-' . ABS($_mes_anterior) . '-' . ABS($_dia_anterior);



	if ( (! $_ano) AND (! $_mes) AND (! $_dia) )
	{
		list($_ano,$_mes,$_dia) = split ("-", $fecha_aprobacion);
	}


	$apellidos_nombres1 = datos_profesor($cedula_profesor1);
	$apellidos_nombres2 = datos_profesor($cedula_profesor2);
	$apellidos_nombres3 = datos_profesor($cedula_profesor3);


/*
		$sqlcmd = "SELECT apellidos_nombres "
				. "FROM profesores_cippsv "
				. "WHERE cedula_profesor='$cedula_profesor' ";
		
		$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
		
		while ($registro = mysql_fetch_object($query))
		{
			$apellidos_nombres = strtolower($registro->apellidos_nombres);
		}
*/

?>
<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Aprobaci&oacute;n del Acta</B>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>C.I. del Profesor</B>
		</FONT>
		 &nbsp; 
		<A HREF="javascript:popup('_blank', '../ingresos/buscar_profesor.php',650,400)">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#3300FF">Buscar Profesor</FONT></A>
	</TD>
</TR>
<TR>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">

			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
				<SELECT NAME="_dia">
					<?
						if ( ($_dia < 1) OR ($_dia > 31) )
						{
							echo '<OPTION VALUE="0" SELECTED>D&iacute;a';
						} else {
							echo '<OPTION VALUE="0">D&iacute;a';
						}
					
			
						for($i=1; $i<32; $i++)
						{
							if ($_dia == $i)
							{
								echo '<OPTION VALUE="' . $i . '" SELECTED>' . $i . "\n";
							} else {
								echo '<OPTION VALUE="' . $i . '">' . $i . "\n";
							}
						}
					?>
				</SELECT>
				<SELECT NAME="_mes">
					<?
						if ( ($_mes < 1) OR ($_mes > 12) )
						{
							echo '<OPTION VALUE="0" SELECTED>Mes';
						} else {
							echo '<OPTION VALUE="0">Mes';
						}

			
						for($i=1; $i<13; $i++)
						{
							if ($_mes == $i)
							{
								echo '<OPTION VALUE="' . $i . '" SELECTED>' . $meses[$i] . "\n";
							} else {
								echo '<OPTION VALUE="' . $i . '">' . $meses[$i] . "\n";
							}
						}
					?>
				</SELECT>
				<SELECT NAME="_ano">
					<?
						if ( ($_ano < 1975) OR ($_ano > (date(Y)+1) ) )
						{
							echo '<OPTION VALUE="0" SELECTED>A&ntilde;o';
						} else {
							echo '<OPTION VALUE="0">A&ntilde;o';
						}
						
						for($i=1975; $i<=date(Y); $i++)
						{
							if ($_ano == $i)
							{
								echo '<OPTION VALUE="' . ($i) . '" SELECTED>' . $i . "\n";
							} else {
								echo '<OPTION VALUE="' . ($i) . '">' . $i . "\n";
							}
						}
					?>
				</SELECT>
			</FONT>

	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<?
			if ($_ci_profesor1 == 0 ) $_ci_profesor1 = '';
			if ($_ci_profesor2 == 0 ) $_ci_profesor2 = '';
			if ($_ci_profesor3 == 0 ) $_ci_profesor3 = '';

			if ($cedula_profesor1 == 0) $cedula_profesor1 = '';
			if ($cedula_profesor2 == 0) $cedula_profesor2 = '';
			if ($cedula_profesor3 == 0) $cedula_profesor3 = '';

			if (! $_ci_profesor1)
			{
				echo '<INPUT TYPE="text" NAME="_ci_profesor1" VALUE="' . $cedula_profesor1 . '" SIZE="10" MAXLENGTH="9"> &nbsp;';

			} else {

				echo '<INPUT TYPE="text" NAME="_ci_profesor1" VALUE="' . $_ci_profesor1 . '" SIZE="10" MAXLENGTH="9"> &nbsp;';
			}
?>
			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
				<? echo ucwords($apellidos_nombres1) . "<BR>" ?>
			</FONT>
<?
			if (! $_ci_profesor2)
			{
				echo '<INPUT TYPE="text" NAME="_ci_profesor2" VALUE="' . $cedula_profesor2 . '" SIZE="10" MAXLENGTH="9"> &nbsp;';

			} else {

				echo '<INPUT TYPE="text" NAME="_ci_profesor2" VALUE="' . $_ci_profesor2 . '" SIZE="10" MAXLENGTH="9"> &nbsp;';
			}
?>
			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
				<? echo ucwords($apellidos_nombres2) . "<BR>" ?>
			</FONT>
<?
			if (! $_ci_profesor3)
			{
				echo '<INPUT TYPE="text" NAME="_ci_profesor3" VALUE="' . $cedula_profesor3 . '" SIZE="10" MAXLENGTH="9"> &nbsp;';

			} else {

				echo '<INPUT TYPE="text" NAME="_ci_profesor3" VALUE="' . $_ci_profesor3 . '" SIZE="10" MAXLENGTH="9"> &nbsp;';
			}
		?>

		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo ucwords($apellidos_nombres3) . "<BR>" ?>
		</FONT>

	</TD>
</TR>
</TABLE>

<BR>

<?
	if ( ($error_fecha_aprobacion) OR ($error_ci_profesor) )
	{
		echo '<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">';
		echo '<TR><TD WIDTH="600" ALIGN="left" VALIGN="top">';
		
		echo '<FONT FACE="Verdana,Arial,Geneva" COLOR="#FF0000">';
		echo '<B>Se ha encontrado algun Error al tratar de procesar el Acta</B>';
		echo '</FONT><BR><BR>';
	}
	
	
	if ($error_fecha_aprobacion)
	{
		echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
		echo '&bull; Debe selecionar una Fecha de Aprobaci&oacute;n del Acta v&aacute;lida, favor revisar.';
		echo '</FONT><BR>';
	}
	
	if ($error_ci_profesor)
	{
		echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
		echo '&bull; Debe selecionar una C&eacute;dula de Identidad del Profesor v&aacute;lida, favor revisar.';
		echo '</FONT><BR>';
	}
	
	
	if ( ($error_fecha_aprobacion) OR ($error_ci_profesor) )
	{
		echo '</TD></TR></TABLE>';
	}
?>

<BR>


<INPUT TYPE="hidden" NAME="_codacta" VALUE="<? echo $_codacta ?>">
<INPUT TYPE="hidden" NAME="_codcohorte" VALUE="<? echo $_codcohorte ?>">

<INPUT TYPE="hidden" NAME="cedula_profesor_anterior1" VALUE="<? echo $cedula_profesor_anterior1 ?>">
<INPUT TYPE="hidden" NAME="cedula_profesor_anterior2" VALUE="<? echo $cedula_profesor_anterior2 ?>">
<INPUT TYPE="hidden" NAME="cedula_profesor_anterior3" VALUE="<? echo $cedula_profesor_anterior3 ?>">

<INPUT TYPE="hidden" NAME="fecha_aprobacion_anterior" VALUE="<? echo $fecha_aprobacion_anterior ?>">

<INPUT TYPE="hidden" NAME="mid" VALUE="<? echo $mid ?>">

<INPUT TYPE="submit" NAME="continuar" VALUE="Continuar">
<INPUT TYPE="submit" NAME="cancelar" VALUE="Cancelar">

</FORM>

<?
	if ( (! $error_fecha_aprobacion) AND (! $error_ci_profesor) ) :
?>
		<BR>
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>
			Presione
				<A HREF="editar_calificaciones_multiactas.php?codacta=<? echo $_codacta ?>&mid=<? echo $mid ?>"> 
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#3300FF">Aqu&iacute;</FONT></A> 
			para Editar las Calificaciones de alg&uacute;n Alumno.
			</B>
		<FONT>
				
		<BR><BR>
<?
	endif;
?>

<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
