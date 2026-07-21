<?

if ( (! $cedula) OR (! $codcohorte) )
{
	header ("Location: ingreso_de_cedula.php");
	exit;
}


$ano = date ("Y");
$mes = date ("m");
$dia = date ("d");

$fecha_de_hoy = $ano . '-' . $mes . '-' . $dia;


include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");


### Busco los Datos Basicos Personales en la Base de Datos
$sqlcmd = "SELECT apellidos, nombres, fecha_nacimiento "
		. "FROM datos_personales "
		. "WHERE cedula='$cedula' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$apellidos = strtolower($registro->apellidos);
	$nombres = strtolower($registro->nombres);
	$fecha_nacimiento = $registro->fecha_nacimiento;
}


### Formateo los Datos obtenidos, para mostarlos correctamente

if ( ($apellidos) AND ($nombres) ) $apellidos_nombres = ucwords($nombres) . ' ' . ucwords($apellidos);
if ( ($apellidos) AND (! $nombres) ) $apellidos_nombres = ucwords($apellidos);
if ( (! $apellidos) AND ($nombres) ) $apellidos_nombres = ucwords($nombres);
if ( (! $apellidos) AND (! $nombres) ) $apellidos_nombres = 'No Existe Registro';


if ( ($fecha_nacimiento == '0000-00-00') OR ($fecha_nacimiento == "") )
{
	$fecha_nacimiento = 'No Existe Registro';

} else {

	$fecha_nacimiento = fecha($fecha_nacimiento);
}

###
###
###

$sqlcmd = "SELECT directorio_cippsv.ciudad, oportunidades_estudio.tipo, oportunidades_estudio.mencion_especialidad, "
		. "oportunidades_estudio.codopest, oportunidades_estudio.codsede, "
		. "oportunidades_estudio.periodos, oportunidades_estudio.creditos, DATE_FORMAT(cohortes.fecha_inicio, '%Y') as fecha_inicio "
		. "FROM directorio_cippsv, oportunidades_estudio, cohortes "
		. "WHERE cohortes.codsede=oportunidades_estudio.codsede AND cohortes.codopest=oportunidades_estudio.codopest AND "
		. "cohortes.codsede=directorio_cippsv.codsede AND cohortes.codcohorte='$codcohorte' ";


$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$ciudad = $registro->ciudad;
	$tipo = $registro->tipo;
	$mencion_especialidad = $registro->mencion_especialidad;
	$codopest = $registro->codopest;
	$codsede = $registro->codsede;
	#$periodos = $registro->periodos;
	#$creditos = $registro->creditos;
	$fecha_inicio = $registro->fecha_inicio;
	$periodo_lectivo = $registro->periodo_lectivo;
}


if ( ($fecha_inicio == '0000') OR ($fecha_inicio == "") )
{
	$fecha_inicio = 'No Existe Registro';

} else {

	$fecha_inicio = strtr (number_format($fecha_inicio), ",", ".");
}



if ($tipo == 'Especializacion') $tipo = "Programa de Especializaci&oacute;n en ";

if ($tipo == 'Maestria') $tipo = "Programa Maestria en ";

?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#00CC00" VLINK="#CC0000">

<CENTER>

<?
	#include ("$DOCUMENT_ROOT/includes/encabezado.php");
?>

<TABLE BORDER="0" WIDTH="680" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="120" ALIGN="center" VALIGN="middle" ROWSPAN="2">

		<A HREF="/sace/consultas/consulta_por_cedula.php?cedula=<? echo $cedula ?>"><IMG SRC="/sace/imagenes/logo_notas_certificadas.jpg" ALT="" WIDTH="100" HEIGHT="90" BORDER="0"></A>

	</TD><TD WIDTH="560" ALIGN="center" VALIGN="middle">
		<FONT SIZE="-1" FACE="Arial">
			<B>Centro de Investigaciones Psiqui&aacute;tricas,<BR>
			Psicol&oacute;gicas y Sexol&oacute;gicas de Venezuela<BR>
			Coordinaci&oacute;n Acad&eacute;mica<BR>
			Oficina de Control de Estudios
			</B>
		</FONT>
		
		<BR>
		
		<FONT SIZE="-1" FACE="Arial">
			<B><I>RECORD ACADEMICO</I></B><BR>
		</FONT>
		
	</TD>
</TR>
<TR>
	<TD WIDTH="560" ALIGN="center" VALIGN="top" COLSPAN="2">
		<FONT SIZE="-1" FACE="Arial">
			<B><? echo $tipo . ' ' . $mencion_especialidad ?></B>
		</FONT>
	</TD>
</TR>
<TABLE>

<TABLE BORDER="0" WIDTH="680" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="380" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial">
			<B>Nombre:</B> <? echo $apellidos_nombres ?>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial">
			<B>C.I. No.:</B> <? echo strtr (number_format($cedula), ",", ".") ?>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="380" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial">
			<B>A&ntilde;o de Ingreso:</B> <? echo $fecha_inicio ?>
		</FONT>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial">
			<B>Sede:</B> <? echo $ciudad ?>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="680" ALIGN="left" VALIGN="top" COLSPAN="2">
		<FONT SIZE="-3" FACE="Arial">
			ESCALA DE CALIFICACIONES UNO A VEINTE (01-20) PUNTOS. NOTA MINIMA APROBATORIA QUINCE (15) PUNTOS.
		</FONT>
	</TD>
</TR>
</TABLE>


<TABLE BORDER="0" WIDTH="680" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000000">
<TR>
	<TD WIDTH="70" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Codigo
		</FONT>
	</TD>
	<TD WIDTH="280" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Nombre de la Asignatura
		</FONT>
	</TD>
	<TD WIDTH="60" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Creditos
		</FONT>
	</TD>
	<TD WIDTH="70" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Nota
		</FONT>
	</TD>
	<TD WIDTH="60" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Periodo
		</FONT>
	</TD>
</TR>
<?
	$notas = "";
	$total_creditos = "";

/*
+---------+-------------------------------------------+----------+----------+--------------+------------------+-------------+--------------+
| codasig | asignatura                                | creditos | periodos | calificacion | fecha_aprobacion | codasig_imp | codacta      |
+---------+-------------------------------------------+----------+----------+--------------+------------------+-------------+--------------+
| EP-001  | Metodolog’a de la Investigaci—n I         |        1 |        1 |           19 | 1999-05-01       | EP-001      | OCC1EP98I-01 |
| EP-005  | Educaci—n para Padres  I                  |        1 |        1 |           20 | 1999-02-18       | EP-005      | OCC1EP98I-05 |
| EP-009  | Formaci—n de Actitudes del Orientador I   |        1 |        1 |           17 | 1999-01-19       | EP-009      | OCC1EP98I-09 |
| EP-013  | Recursos Audiovisuales                    |        1 |        1 |           20 | 1999-04-21       | EP-013      | OCC1EP98I-13 |
| EP-014  | Desarrollo Evolutivo                      |        1 |        1 |           20 | 1999-06-09       | EP-014      | OCC1EP98I-14 |
| EP-002  | Metodolog’a de la Investigaci—n II        |        1 |        2 |           17 | 1999-10-01       | EP-002      | OCC1EP98I-02 |
| EP-006  | Educaci—n para Padres II                  |        1 |        2 |           19 | 1999-10-01       | EP-006      | OCC1EP98I-06 |
| EP-010  | Formaci—n de Actitudes del Orientador II  |        2 |        2 |           19 | 1999-09-13       | EP-010      | OCC1EP98I-10 |
| EP-015  | Psiquiatr’a Infantil                      |        1 |        2 |           16 | 1999-10-22       | EP-015      | OCC1EP98I-15 |
| EP-003  | Metodolog’a de la Investigaci—n III       |        1 |        3 |           20 | 2000-04-01       | EP-003      | OCC1EP98I-03 |
| EP-007  | Educaci—n para Padres III                 |        1 |        3 |           20 | 2000-03-01       | EP-007      | OCC1EP98I-07 |
| EP-011  | Formaci—n de Actitudes del Orientador III |        1 |        3 |           20 | 2000-01-03       | EP-011      | OCC1EP98I-11 |
| EP-016  | Sexolog’a B‡sica                          |        1 |        3 |           19 | 2000-04-28       | EP-016      | OCC1EP98I-16 |
| EP-017  | Seminario I                               |        1 |        3 |           18 | 1999-12-08       | EP-017      | OCC1EP98I-17 |
| EP-004  | Metodolog’a de la Investigaci—n IV        |        1 |        4 |           17 | 2000-10-01       | EP-004      | OCC1EP98I-04 |
| EP-008  | Educaci—n para Padres IV                  |        1 |        4 |           20 | 2000-07-06       | EP-008      | OCC1EP98I-08 |
| EP-012  | Formaci—n de Actitudes del Orientador IV  |        1 |        4 |           19 | 2000-07-17       | EP-012      | OCC1EP98I-12 |
| EP-018  | Seminario II                              |        1 |        4 |           20 | 2000-09-21       | EP-018      | OCC1EP98I-18 |
| EP-019  | Presentacion de Casos                     |        1 |        4 |           18 | 2000-12-08       | EP-019      | OCC1EP98I-19 |
+---------+-------------------------------------------+----------+----------+--------------+------------------+-------------+--------------+

	$sqlcmd = "SELECT registro_actas.codasig, pensum_estudios.asignatura, pensum_estudios.creditos, pensum_estudios.periodos, "
			. "record_notas.calificacion, registro_actas.fecha_aprobacion, pensum_estudios.codasig_imp, record_notas.codacta "
			. "FROM registro_actas, pensum_estudios, record_notas, cohortes "
			. "WHERE registro_actas.codasig=pensum_estudios.codasig AND pensum_estudios.codsede='$codsede' AND "
			. "pensum_estudios.codopest='$codopest' AND registro_actas.codacta=record_notas.codacta AND "
			. "record_notas.cedula='$cedula' AND "
			. "cohortes.codcohorte=registro_actas.codcohorte AND cohortes.codcohorte='$codcohorte' "
			. "ORDER BY pensum_estudios.periodos, pensum_estudios.codasig ";
*/
	#echo "$sqlcmd<BR>";
	


	$sqlcmd = "CREATE TEMPORARY TABLE actas_notas_certif_egre_temp "
			. "SELECT registro_actas.codasig, pensum_estudios.asignatura, pensum_estudios.creditos, pensum_estudios.periodos, "
			. "record_notas.calificacion, registro_actas.fecha_aprobacion, pensum_estudios.codasig_imp, record_notas.codacta "
			. "FROM registro_actas, pensum_estudios, record_notas, cohortes "
			. "WHERE registro_actas.codasig=pensum_estudios.codasig AND pensum_estudios.codsede='$codsede' AND "
			. "pensum_estudios.codopest='$codopest' AND registro_actas.codacta=record_notas.codacta AND "
			. "cohortes.codcohorte=registro_actas.codcohorte AND cohortes.codcohorte='$codcohorte' AND record_notas.cedula='$cedula' ";

	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");



	$sqlcmd = "INSERT INTO actas_notas_certif_egre_temp (codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta) "
			. "SELECT multiactas.codasig, pensum_estudios.asignatura, pensum_estudios.creditos, pensum_estudios.periodos, "
			. "record_notas.calificacion, multiactas.fecha_aprobacion, pensum_estudios.codasig_imp, multiactas.codacta "
			. "FROM pensum_estudios, multiactas, record_notas "
			. "WHERE pensum_estudios.codsede='$codsede' AND pensum_estudios.codopest='$codopest' AND "
			. "pensum_estudios.codasig=multiactas.codasig AND record_notas.mid=multiactas.mid AND "
			. "multiactas.codcohorte='$codcohorte' AND record_notas.cedula='$cedula' ";

	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");



	$sqlcmd = "SELECT codasig, asignatura, creditos, periodos, calificacion, fecha_aprobacion, codasig_imp, codacta "
			. "FROM actas_notas_certif_egre_temp "
			. "ORDER BY periodos, codasig ";

	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

	while ($registro = mysql_fetch_object($query))
	{
		$codasig = $registro->codasig;
		$asignatura = $registro->asignatura;
		$creditos = $registro->creditos;
		$periodos = $registro->periodos;
		$calificacion = $registro->calificacion;
		$fecha_aprobacion = $registro->fecha_aprobacion;
		$codasig_imp = $registro->codasig_imp;
		$codacta = $registro->codacta;

		if (! $periodo_actual)  $periodo_actual = $periodos;
		
		if ( ($fecha_aprobacion == '0000-00-00') OR ($fecha_aprobacion == "") )
		{
			$fecha_aprobacion = "";
		} else {
			$fecha_aprobacion = fecha ($fecha_aprobacion, corto);
		}


		if ( ($calificacion >= 1) AND ($calificacion <= 20) )
		{
			$notas = $notas + ($calificacion * $creditos);
			
			$total_creditos = $total_creditos + $creditos;
		}



### Reviso si el Alumno no esta Reprobado, si No lo esta, sigo adelante !
### Si llega a estar Reprobado: veo si ES Curso Dirijido o si NO lo es !!


### Reviso si el Alumno esta Reprobado

		if ( ( ($calificacion > 0) AND ($calificacion <= 14) ) OR ($calificacion == 99) OR ($calificacion == 404) )
		{
			$alummo_reprobado = 1;

		} else {

			$imprimo_nota = 1;
		}


### Si el Alumno esta Reprobado, entonces Reviso Si esta Materia es Curso Dirigido

		if ($alummo_reprobado)
		{

			$codacta_cd = substr($codacta, -3, 2);

			if ("CD" == $codacta_cd) $soy_cd = 1;


			if ($soy_cd)
			{
				### Si fuese CD, voy a preguntar si SOY el Ultimo CD, si NO lo soy, sigo adelante
	
				$codacta_sin_cd = substr($codacta, 0, -3) . '%';
	
				$sqlcmd2 = "SELECT codacta AS codacta_last FROM actas_notas_certif_egre_temp "
						 . "WHERE codacta LIKE '$codacta_sin_cd' ORDER BY codacta DESC limit 1 ";
	
				$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");
		
				while ($registro2 = mysql_fetch_object($query2))
				{
					$codacta_last = $registro2->codacta_last;
				}
	
	
				if ($codacta == $codacta_last) $imprimo_nota = 1;
	
	
			} else {
	
				### Si no SOY CD, reviso si hay CD, si hay; NO imprimo.
				### Si no hay CD, entonces SI imprimo.
	
				$codacta_query3 = $codacta . 'CD%';
	
				$sqlcmd3 = "SELECT count(*) AS cantidad FROM actas_notas_certif_egre_temp WHERE codacta LIKE '$codacta_query3' ";

				$query3 = mysql_db_query(DB_DATABASE,"$sqlcmd3");
		
				while ($registro3 = mysql_fetch_object($query3))
				{
					$cantidad = $registro3->cantidad;
				}
	
	
				if ($cantidad == 0) $imprimo_nota = 1;
	
			}

		}


### Reviso si voy a Imprimir la Materia con la Nota o No

		if ($imprimo_nota)
		{
	
	
			if ($calificacion == 404) $calificacion = 'No Curs&oacute;';
	
			if ($calificacion ==  99) $calificacion = 'Reprobado';
	
			if ($calificacion == 100) $calificacion = 'Aprobado';
			
			if ($calificacion == 110) $calificacion = 'Meritorio';
			
			if ($calificacion == 120) $calificacion = 'Excelencia';
			
			if ($calificacion == 212) $calificacion = 'Equivalencia';
	
	
	
			if ($bg_celda == '#FFFFFF')
			{
				$bg_celda = '#FFFFFF';
			} else {
				$bg_celda = '#FFFFFF';
			}
	
			if ($periodo_actual != $periodos)
			{
				$periodo_actual = $periodos;
				echo '<TR><TD COLSPAN="5" BGCOLOR="#FFFFFF"><BR></TD></TR>';
			}
?>
<TR>
	<TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-1" FACE="Arial">
			<? echo $codasig_imp ?>
		</FONT>
	</TD>
	<TD WIDTH="320" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-1" FACE="Arial">
			<? echo $asignatura ?>
		</FONT>
	</TD>
	<TD WIDTH="80" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-1" FACE="Arial">
			<? echo $creditos ?>
		</FONT>
	</TD>
	<TD WIDTH="90" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-1" FACE="Arial">
			<? echo $calificacion ?>
		</FONT>
	</TD>
	<TD WIDTH="90" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
		<FONT SIZE="-1" FACE="Arial">
			<? echo $periodos ?>
		</FONT>
	</TD>
</TR>
<?
			}
		
			$alummo_reprobado = "";
			$imprimo_nota = "";

			$codacta_cd = "";
			$soy_cd = "";
			$codacta_sin_cd  = "";
			$codacta_last = "";

			$codacta_query3 = "";
			$cantidad = "";


			$creditos = "";
			$calificacion = "";

	}


	$sqlcmd = "DROP TABLE actas_notas_certif_egre_temp ";
	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
?>
</TABLE>

<TABLE BORDER="0" WIDTH="680" CELLSPACING="3" CELLPADDING="3">
<TR>
	<TD WIDTH="680" ALIGN="left" VALIGN="top" COLSPAN="2">
		<FONT SIZE="-2" FACE="Arial">
			NOTA: En caso de error u omisi&oacute;n, las actas son el &uacute;nico 
			documento v&aacute;lido y definitivo para cualquier reclamo u observaci&oacute;n.<BR>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="340" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial">
			<B>Caracas, <? echo fecha($fecha_de_hoy) ?></B><BR><BR>
		</FONT>
		

	</TD>
	<TD WIDTH="340" ALIGN="center" VALIGN="top">
		<TABLE BORDER="0" WIDTH="225" CELLSPACING="0" CELLPADDING="0">
		<TR>
			<TD WIDTH="340" ALIGN="center" VALIGN="top">
				<FONT SIZE="-1" FACE="Arial">
					<B>Indice Acad&eacute;mico: <? echo number_format(($notas/$total_creditos), 2, ',', '')  ?></B><BR>
				</FONT>
		
				<FONT SIZE="-1" FACE="Arial">
					<BR><BR><BR><B>Msc. Navidia Garc&iacute;a</B><BR>
					Coordinadora Acad&eacute;mica
				</FONT>
			</TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>

<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
