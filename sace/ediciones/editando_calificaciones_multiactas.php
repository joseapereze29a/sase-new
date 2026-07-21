<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_datos_profesor.php");


if ( ($continuar) OR ($cancelar) )
{

	if ($cancelar)
	{
		$url = "editar_calificaciones_multiactas.php?codacta=" . $codacta . "&mid=" . $mid;

		header("Location: $url");
		exit;
	}


	if (! ereg ("^[0-9]+$", $_cedula) )
	{
		$error_cedula = 1;
	}



	if ( ($_calificacion < 1) OR ($_calificacion > 404) )
	{
		$error_calificacion = 1;
	}



	if ( (! $error_cedula) AND (! $error_calificacion) )
	{

		if ( ($_cedula == $_cedula_old) AND ($_calificacion == $_calificacion_old) )
		{
			$url = "editar_calificaciones_multiactas.php?codacta=" . $codacta . "&mid=" . $mid;
	
			header("Location: $url");
			exit;
		}


/*
CREATE TABLE ediciones_notas (
  enid int(7) unsigned NOT NULL auto_increment,
  transaccion_id int(7) unsigned,
  codcohorte varchar(15) NOT NULL default '',
  codacta varchar(20) NOT NULL default '',
  mid int(6) unsigned default NULL,
  fecha_modificacion datetime default NULL,
  operador_modificacion varchar(20) default NULL,
  host_modificacion varchar(20) default NULL,
  operacion varchar(255) default NULL,
  PRIMARY KEY  (enid)
) TYPE=MyISAM;
*/


		if ($_cedula == $_cedula_old)
		{

			$sqlcmd = "UPDATE record_notas SET "
					. "calificacion='$_calificacion', fecha_modificacion=NOW(), operador_modificacion='$PHP_AUTH_USER', "
					. "host_modificacion='$REMOTE_ADDR' "
					. "WHERE codacta='$codacta' AND cedula='$_cedula' AND mid='$mid' ";


			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");



			$sqlcmd = "INSERT INTO ediciones_notas (codcohorte, codacta, mid, fecha_modificacion, operador_modificacion, host_modificacion, operacion) VALUES ("
					. "'$_codcohorte', '$codacta', '$mid', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR', "
					. "'Se modifico la calificacion del alumno cuya cedula es: $_cedula. La anterior nota era: $_calificacion_old, la nueva es: $_calificacion.') ";

			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");


			$enid = mysql_insert_id();
	
	
			$sqlcmd = "UPDATE ediciones_notas SET transaccion_id='$enid' WHERE enid='$enid' ";
			
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");



			$url = "editando_calificaciones_finalizado.php?codacta=" . $codacta . "&mid=" . $mid;
			
			header("Location: $url");
			exit;

		} else {

			$sqlcmd = "SELECT count(*) as encontrado "
					. "FROM record_notas "
					. "WHERE codacta='$codacta' AND cedula='$_cedula' ";
			
			$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
			
			while ($registro = mysql_fetch_object($query))
			{
				$encontrado = $registro->encontrado;
			}


			if ($encontrado > 0)
			{
				$error_ya_existe_ci = 1;
				
			} else {
			
				$sqlcmd = "UPDATE record_notas SET "
						. "calificacion='$_calificacion', cedula='$_cedula', fecha_modificacion=NOW(), operador_modificacion='$PHP_AUTH_USER', "
						. "host_modificacion='$REMOTE_ADDR' "
						. "WHERE codacta='$codacta' AND cedula='$_cedula_old' and mid='$mid' ";

				$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");

				$sqlcmd = "INSERT INTO ediciones_notas (codcohorte, codacta, mid, fecha_modificacion, operador_modificacion, host_modificacion, operacion) VALUES ("
						. "'$_codcohorte', '$codacta', '$mid', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR', "
						. "'Se modifico la cedula del alumno. La anterior era: $_cedula_old. La nueva es: $_cedula.') ";
	
				$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");


				$enid = mysql_insert_id();
	
	
				$sqlcmd = "UPDATE ediciones_notas SET transaccion_id='$enid' WHERE enid='$enid' ";
				
				if ($_calificacion_old != $_calificacion)
				{
				
					$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
	
					$sqlcmd = "INSERT INTO ediciones_notas (transaccion_id, codcohorte, codacta, mid, fecha_modificacion, operador_modificacion, host_modificacion, operacion) VALUES ("
							. "'$enid', '$_codcohorte', '$codacta', '$mid', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR', "
							. "'Ademas, se modifico la calificacion del alumno cuya cedula era/es: $_cedula_old / $_cedula. La anterior nota era: $_calificacion_old, la nueva es: $_calificacion.') ";
	
					$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");

				}

				$url = "editando_calificaciones_finalizado.php?codacta=" . $codacta . "&mid=" . $mid;
				
				header("Location: $url");
				exit;
			}
			
			



#				Se modifico la fecha del acta: acta. La anterior era: fecha_anterior, la nueva es: $fecha_new.
#				Se modifico la cedula del acta: acta. La anterior era: cedula_anterior, la nueva es: $cedula_new.

#Se modifico la calificacion del alumno cuya ci es: 12421100. La anterior nota es: 100, la nueva es: 404.



		}

	}


}


$sqlcmd = "SELECT directorio_cippsv.modalidad, directorio_cippsv.ciudad, directorio_cippsv.edo_prov, "
		. "oportunidades_estudio.tipo, oportunidades_estudio.mencion_especialidad, cohortes.codcohorte, "
		. "cohortes.fecha_inicio "
		. "FROM directorio_cippsv, oportunidades_estudio, cohortes, multiactas "
		. "WHERE multiactas.codacta='$codacta' AND multiactas.codcohorte=cohortes.codcohorte AND "
		. "cohortes.codsede=oportunidades_estudio.codsede AND cohortes.codopest=oportunidades_estudio.codopest AND "
		. "oportunidades_estudio.codsede=directorio_cippsv.codsede AND mid='$mid' ";

/*
+-----------+---------+------------------+-----------------+---------------------------------+-------------+--------------+
| modalidad | ciudad  | edo_prov         | tipo            | mencion_especialidad            | codcohorte  | fecha_inicio |
+-----------+---------+------------------+-----------------+---------------------------------+-------------+--------------+
| Sede      | Caracas | Distrito Federal | Especializacion | Terapia de la Conducta Infantil | PPALTCI03-V | 2003-09-05   |
+-----------+---------+------------------+-----------------+---------------------------------+-------------+--------------+
*/

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$modalidad = $registro->modalidad;
	$ciudad = $registro->ciudad;
	$edo_prov = $registro->edo_prov;
	$tipo = $registro->tipo;
	$mencion_especialidad = $registro->mencion_especialidad;
	$codcohorte = $registro->codcohorte;
	$fecha_inicio = $registro->fecha_inicio;
}


if ( ($fecha_inicio == '0000-00-00') OR ($fecha_inicio == "") )
{
	$fecha_inicio = "";

} else {

	$fecha_inicio = fecha($fecha_inicio);
}
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

<BR>

<IMG SRC="/sace/imagenes/titulos_de_home/titulo_editar.jpg" ALT="" WIDTH="363" HEIGHT="21" BORDER="0">

<BR><BR><BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Informaci&oacute;n sobre el Acta</B>
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
			<? echo $fecha_inicio ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $codcohorte ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>


<?
$sqlcmd = "select multiactas.codasig, multiactas.cedula_profesor1, multiactas.cedula_profesor2, multiactas.cedula_profesor3, "
		. "multiactas.fecha_aprobacion, pensum_estudios.asignatura, pensum_estudios.creditos, pensum_estudios.periodos "
		. "FROM multiactas, pensum_estudios, cohortes, oportunidades_estudio "
		. "WHERE multiactas.codacta='$codacta' AND multiactas.codcohorte=cohortes.codcohorte AND "
		. "cohortes.codsede=oportunidades_estudio.codsede AND cohortes.codopest=oportunidades_estudio.codopest AND "
		. "oportunidades_estudio.codsede=pensum_estudios.codsede and oportunidades_estudio.codopest=pensum_estudios.codopest AND "
		. "multiactas.codasig=pensum_estudios.codasig AND multiactas.mid='$mid' ";


$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$codasig = $registro->codasig;
	$cedula_profesor1 = $registro->cedula_profesor1;
	$cedula_profesor2 = $registro->cedula_profesor2;
	$cedula_profesor3 = $registro->cedula_profesor3;
	$fecha_aprobacion = $registro->fecha_aprobacion;
	$asignatura = $registro->asignatura;
	$creditos = $registro->creditos;
	$periodos = $registro->periodos;
}


if ( ($fecha_aprobacion == '0000-00-00') OR ($fecha_aprobacion == "") )
{
	$fecha_aprobacion = "";

} else {

	$fecha_aprobacion = fecha($fecha_aprobacion);
}


$apellidos_nombres1 = datos_profesor($cedula_profesor1);
$apellidos_nombres2 = datos_profesor($cedula_profesor2);
$apellidos_nombres3 = datos_profesor($cedula_profesor3);


$curso_d = substr($codacta, -3, 2);
$curso_d = strtolower($curso_d);
	
if ($curso_d == "cd")
{
	$asignatura = $asignatura . ' <B>(CD)</B>';
}

$curso_d == '';


/*
+---------+-----------------+------------------+-------------+----------+----------+
| codasig | cedula_profesor | fecha_aprobacion | asignatura  | creditos | periodos |
+---------+-----------------+------------------+-------------+----------+----------+
| OC-012  |         4825080 | 1999-05-12       | Conducta IV |        1 |        4 |
+---------+-----------------+------------------+-------------+----------+----------+


$sqlcmd2 = "SELECT apellidos_nombres FROM profesores_cippsv WHERE cedula_profesor='$cedula_profesor' ";

$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

while ($registro2 = mysql_fetch_object($query2))
{
	$apellidos_nombres  = strtolower($registro2->apellidos_nombres);
}
*/



if (! $_cedula_old)		$_cedula_old = $_cedula;

if ($_calificacion)
{
	if (! $_calificacion_old)	$_calificacion_old = $_calificacion;

} else {

	$sqlcmd = "SELECT calificacion "
			. "FROM record_notas "
			. "WHERE codacta='$codacta' AND cedula='$_cedula' AND mid='$mid' ";
	
	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
	while ($registro = mysql_fetch_object($query))
	{
	
		$_calificacion = $registro->calificacion;
		
		if (! $_calificacion_old)	$_calificacion_old = $_calificacion;
	}
}


$sqlcmd2 = "SELECT datos_personales.apellidos, datos_personales.nombres "
		 . "FROM datos_personales "
		 . "WHERE datos_personales.cedula='$_cedula' ";

$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

while ($registro2 = mysql_fetch_object($query2))
{
	$apellidos = $registro2->apellidos;
	$nombres = $registro2->nombres;
}

?>
<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2">
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

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2">
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Aprobaci&oacute;n del Acta</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Profesor</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $fecha_aprobacion ?>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<?
				echo $apellidos_nombres1 . "<BR>";
				echo $apellidos_nombres2 . "<BR>";
				echo $apellidos_nombres3;
			?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<FORM ACTION="editando_calificaciones_multiactas.php" METHOD="post">

<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="2">
<TR>
	<TD WIDTH="175" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Apellidos</B>
		</FONT>
	</TD>
	<TD WIDTH="175" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Nombres</B>
		</FONT>
	</TD>
	<TD WIDTH="120" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>C&eacute;dula</B>
		</FONT>
	</TD>
	<TD WIDTH="130" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Calificaci&oacute;n</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="175" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva"><? echo $apellidos ?></FONT>
	</TD>
	<TD WIDTH="175" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva"><? echo $nombres ?></FONT>
	</TD>
	<TD WIDTH="120" ALIGN="left" VALIGN="top">
		<INPUT TYPE="text" NAME="_cedula" VALUE="<? echo $_cedula ?>" SIZE="11" MAXLENGTH="9">
	</TD>
	<TD WIDTH="130" ALIGN="left" VALIGN="top">
		<SELECT NAME="_calificacion">
			<?
					for ($i=1; $i<21; $i++)
					{
						if ($i == $_calificacion)
						{
							echo '<OPTION VALUE="' . $i . '" SELECTED>' . $i;
						
						} else {
						
							echo '<OPTION VALUE="' . $i . '">' . $i;
						}
					}
		

					if (404 == $_calificacion)
					{
						echo '<OPTION VALUE="' . 404 . '" SELECTED>No Curs&oacute;';
					
					} else {
					
						echo '<OPTION VALUE="' . 404 . '">No Curs&oacute;';
					}


					#if (321 == $_calificacion)
					#{
					#	echo '<OPTION VALUE="' . 321 . '" SELECTED>Retir&oacute;';
					
					#} else {
					
					#	echo '<OPTION VALUE="' . 321 . '">Retir&oacute;';
					#}


					if ('99' == $_calificacion)
					{
						echo '<OPTION VALUE="' . 99 . '" SELECTED>Reprobado' . "\n";
					} else {
						echo '<OPTION VALUE="' . 99 . '">Reprobado' . "\n";
					}
	
	
					if ('100' == $_calificacion)
					{
						echo '<OPTION VALUE="' . 100 . '" SELECTED>Aprobado' . "\n";
					} else {
						echo '<OPTION VALUE="' . 100 . '">Aprobado' . "\n";
					}
	
	
					if ('110' == $_calificacion)
					{
						echo '<OPTION VALUE="' . 110 . '" SELECTED>Meritorio' . "\n";
					} else {
						echo '<OPTION VALUE="' . 110 . '">Meritorio' . "\n";
					}
	
					if ('120' == $_calificacion)
					{
						echo '<OPTION VALUE="' . 120 . '" SELECTED>Excelencia' . "\n";
					} else {
						echo '<OPTION VALUE="' . 120 . '">Excelencia' . "\n";
					}

					if ('212' == $_calificacion)
					{
						echo '<OPTION VALUE="' . 212 . '" SELECTED>Equivalencia' . "\n";
					} else {
						echo '<OPTION VALUE="' . 212 . '">Equivalencia' . "\n";
					}

			?>
		</SELECT>
	</TD>
</TR>
</TABLE>

<BR>

<?
	if ( ($error_cedula) OR ($error_calificacion) OR ($error_ya_existe_ci) )
	{
		echo '<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">';
		echo '<TR><TD WIDTH="600" ALIGN="left" VALIGN="top">';
		
		echo '<FONT FACE="Verdana,Arial,Geneva" COLOR="#FF0000">';
		echo '<B>Se ha encontrado algun Error al tratar de procesar el Acta</B>';
		echo '</FONT><BR><BR>';
	}
	
	
	if ($error_cedula)
	{
		echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
		echo '&bull; Debe selecionar una C&eacute;dula de Identidad que sea v&aacute;lida, favor revisar.';
		echo '</FONT><BR>';
	}
	
	if ($error_calificacion)
	{
		echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
		echo '&bull; Debe selecionar una Calificaci&oacute;n v&aacute;lida, favor revisar.';
		echo '</FONT><BR>';
	}

	if ($error_ya_existe_ci)
	{
		echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
		echo '&bull; Debe selecionar una C&eacute;dula de Identidad Distinta, esa ya existe en la Base de Datos.';
		echo '</FONT><BR>';
	}
	
	if ( ($error_cedula) OR ($error_calificacion) OR ($error_ya_existe_ci) )
	{
		echo '</TD></TR></TABLE>';
	}
?>


<BR>

<INPUT TYPE="hidden" NAME="codacta" VALUE="<? echo $codacta ?>">
<INPUT TYPE="hidden" NAME="_codcohorte" VALUE="<? echo $codcohorte ?>">


<INPUT TYPE="hidden" NAME="_cedula_old" VALUE="<? echo $_cedula_old ?>">

<INPUT TYPE="hidden" NAME="_calificacion_old" VALUE="<? echo $_calificacion_old ?>">

<INPUT TYPE="hidden" NAME="mid" VALUE="<? echo $mid ?>">


<INPUT TYPE="submit" NAME="continuar" VALUE="Continuar">
<INPUT TYPE="submit" NAME="cancelar" VALUE="Cancelar">

</FORM>

</CENTER>

</BODY>
</HTML>
