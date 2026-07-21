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
</TABLE>

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
	<TD WIDTH="100" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Codigo
		</FONT>
	</TD>
	<TD WIDTH="320" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Nombre de la Asignatura
		</FONT>
	</TD>
	<TD WIDTH="80" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Creditos
		</FONT>
	</TD>
	<TD WIDTH="90" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Nota
		</FONT>
	</TD>
	<TD WIDTH="90" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Periodo
		</FONT>
	</TD>
</TR>
<?
	$notas = "";
	$total_creditos = "";



	$sqlcmd = "SELECT codasig, asignatura, creditos, periodos, codasig_imp "
			. "FROM pensum_estudios "
			. "WHERE codsede='$codsede' AND pensum_estudios.codopest='$codopest' AND pensum_estudios.status='Activa' "
			. "ORDER BY periodos, codasig ";

#codopest


	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

	while ($registro = mysql_fetch_object($query))
	{
		$codasig = $registro->codasig;
		$asignatura = $registro->asignatura;
		$creditos = $registro->creditos;
		$periodos = $registro->periodos;
		$codasig_imp = $registro->codasig_imp;



		$sqlcmd2 = "SELECT count(*) AS cantidad "
				 . "FROM registro_actas, record_notas "
				 . "WHERE registro_actas.codcohorte='$codcohorte' AND registro_actas.codasig='$codasig' AND "
				 . "record_notas.cedula='$cedula' AND registro_actas.codacta=record_notas.codacta ";

		$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

		while ($registro2 = mysql_fetch_object($query2))
		{
			$cantidad_registro_actas = $registro2->cantidad;
		}


		if ($cantidad_registro_actas > 0)
		{

			$sqlcmd3 = "SELECT record_notas.codacta, record_notas.calificacion "
					 . "FROM registro_actas, record_notas "
					 . "WHERE registro_actas.codcohorte='$codcohorte' AND registro_actas.codasig='$codasig' AND "
					 . "record_notas.cedula='$cedula' AND registro_actas.codacta=record_notas.codacta "
					 . "ORDER BY codacta ";

			$query3 = mysql_db_query(DB_DATABASE,"$sqlcmd3");

			while ($registro3 = mysql_fetch_object($query3))
			{
				$codacta = $registro3->codacta;
				$calificacion = $registro3->calificacion;
				$arreglo_calificiaciones[] = $registro3->calificacion;
				
				if ( ($calificacion >= 1) AND ($calificacion <= 20) )
				{
					$notas = $notas + ($calificacion * $creditos);
					
					$total_creditos = $total_creditos + $creditos;
				}
			}


		}


		$sqlcmd2 = "SELECT count(*) AS cantidad "
				 . "FROM multiactas, record_notas "
				 . "WHERE multiactas.codcohorte='$codcohorte' AND multiactas.codasig='$codasig' AND "
				 . "record_notas.cedula='$cedula' and multiactas.mid=record_notas.mid ";

		$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");

		while ($registro2 = mysql_fetch_object($query2))
		{
			$cantidad_multiactas = $registro2->cantidad;
		}


		if ($cantidad_multiactas > 0)
		{

			$sqlcmd3 = "SELECT record_notas.codacta, record_notas.calificacion "
					 . "FROM multiactas, record_notas "
					 . "WHERE multiactas.codcohorte='$codcohorte' AND multiactas.codasig='$codasig' AND "
					 . "record_notas.cedula='$cedula' AND multiactas.mid=record_notas.mid "
					 . "ORDER BY record_notas.codacta ";

			$query3 = mysql_db_query(DB_DATABASE,"$sqlcmd3");

			while ($registro3 = mysql_fetch_object($query3))
			{
				$codacta = $registro3->codacta;
				$calificacion = $registro3->calificacion;
				$arreglo_calificiaciones[] = $registro3->calificacion;
				
				if ( ($calificacion >= 1) AND ($calificacion <= 20) )
				{
					$notas = $notas + ($calificacion * $creditos);
					
					$total_creditos = $total_creditos + $creditos;
				}
			}


		}


		if ( ($cantidad_registro_actas < 1) AND ($cantidad_multiactas < 1) )	$status_materia_pendiente = 1;

	
		if ( ($status_materia_pendiente) OR ($calificacion < 15) OR ($calificacion == 404) OR ($calificacion == 99) ) $calificacion = 'Pendiente';


		if ($calificacion == 'Pendiente')	$sin_merito = TRUE;
		
		#if ($calificacion == 404) $calificacion = 'No Curs&oacute;';

		#if ($calificacion ==  99) $calificacion = 'Reprobado';

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

			if ($codopest == 'MC/var/wwwM')
			{
				if ( ($periodos == 4) AND (! $periodo_4_sexologia_medica) )
				{
					$periodo_4_sexologia_medica = TRUE;
					$salto_pagina_ahora = TRUE;
					echo '</TABLE> <BR><BR><BR><BR><BR>	<TABLE BORDER="0" WIDTH="680" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000000">';
				}
			}
			
			$periodo_actual = $periodos;

			if (! $salto_pagina_ahora)
			{
				echo '<TR><TD COLSPAN="5" BGCOLOR="#FFFFFF"><BR></TD></TR>';

			} else {

				$salto_pagina_ahora = FALSE;
			}
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
		

		$codacta = '';
		$calificacion = '';

		$cantidad_registro_actas = '';
		$cantidad_multiactas = '';
		$status_materia_pendiente = '';


		
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
			<B>Caracas, <? echo fecha($fecha_de_hoy) ?></B><BR>
		</FONT>

<FONT SIZE="-1" FACE="Arial">
<BR><BR><BR><B>Lic. Sim&oacute;n Gonz&aacute;lez</B><BR>
Jefe de Control de Estudios
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
					<BR><BR><BR><B>Dra. Navidia Garc&iacute;a</B><BR>
					Coordinadora Acad&eacute;mica
				</FONT>
			</TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>

<?
/*

for ($i=0; $i<(count($arreglo_calificiaciones)); $i++)
{
	echo "$i: $arreglo_calificiaciones[$i] <BR>";
	if ($arreglo_calificiaciones[$i] < 20)	$ya_no_es_magna_cumlaude = TRUE;
	if ($arreglo_calificiaciones[$i] < 19)	$ya_no_es_suma_cumlaude = TRUE;
	if ($arreglo_calificiaciones[$i] < 18)	$ya_no_es_cumlaude = TRUE;
}


if ($sin_merito != 1)
{
echo "ya_no_es_magna_cumlaude: $ya_no_es_magna_cumlaude <BR>";
echo "ya_no_es_suma_cumlaude: $ya_no_es_suma_cumlaude <BR>";
echo "ya_no_es_cumlaude: $ya_no_es_cumlaude <BR>";
echo "EOF <BR>";
}
*/

	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
