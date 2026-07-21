<?
session_start();
$cedula=$_GET['cedula'];
$codcohorte=$_GET['codcohorte'];

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

//include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");
require_once dirname(__FILE__) . '/../includes/conexion.php';


### Busco los Datos Basicos Personales en la Base de Datos

// 2.1) Preparar la consulta
$sql1 = "
  SELECT apellidos, nombres, fecha_nacimiento
  FROM datos_personales
  WHERE cedula = ?
";
$stmt1 = mysqli_prepare($conexion, $sql1)
    or die('Error prepare datos_personales: ' . mysqli_error($conexion));

// 2.2) Ejecutar y vincular resultado
mysqli_stmt_bind_param($stmt1, 's', $cedula);
mysqli_stmt_execute($stmt1);
mysqli_stmt_bind_result($stmt1, $apellidos_db, $nombres_db, $fecha_nac_db);

// 2.3) Obtener y formatear
if (mysqli_stmt_fetch($stmt1)) {
    // Nombres y apellidos en Title Case UTF-8
    $apellidos_nombres = '';
    if ($nombres_db || $apellidos_db) {
        $apellidos_nombres = trim(
            mb_convert_case($nombres_db, MB_CASE_TITLE, 'UTF-8')
          . ' '
          . mb_convert_case($apellidos_db, MB_CASE_TITLE, 'UTF-8')
        );
    }
    if (!$apellidos_nombres) {
        $apellidos_nombres = 'No Existe Registro';
    }

    // Fecha de nacimiento
    if (empty($fecha_nac_db) || $fecha_nac_db === '0000-00-00') {
        $fecha_nacimiento = 'No Existe Registro';
    } else {
        $fecha_nacimiento = date('d/m/Y', strtotime($fecha_nac_db));
    }
} else {
    // Sin registro
    $apellidos_nombres = 'No Existe Registro';
    $fecha_nacimiento  = 'No Existe Registro';
}

mysqli_stmt_close($stmt1);

// 3.1) Preparar la consulta
$sql2 = "
  SELECT
    dc.ciudad,
    oe.tipo,
    oe.mencion_especialidad,
    oe.codopest,
    oe.codsede,
    oe.periodos,
    oe.creditos,
    DATE_FORMAT(cohortes.fecha_inicio, '%Y') AS fecha_inicio
  FROM cohortes
  JOIN oportunidades_estudio oe
    ON cohortes.codsede  = oe.codsede
   AND cohortes.codopest = oe.codopest
  JOIN directorio_cippsv dc
    ON cohortes.codsede = dc.codsede
  WHERE cohortes.codcohorte = ?
";
$stmt2 = mysqli_prepare($conexion, $sql2)
    or die('Error prepare cohortes: ' . mysqli_error($conexion));

// 3.2) Ejecutar y vincular
mysqli_stmt_bind_param($stmt2, 's', $codcohorte);
mysqli_stmt_execute($stmt2);
mysqli_stmt_bind_result(
    $stmt2,
    $ciudad_db,
    $tipo_db,
    $mencion_db,
    $codopest_db,
    $codsede_db,
    $periodos_db,
    $creditos_db,
    $fecha_inicio_db
);

// 3.3) Obtener y formatear
if (mysqli_stmt_fetch($stmt2)) {
    // Ciudad y codsede/codopest directos
    $ciudad = $ciudad_db;
    $codsede = $codsede_db;
    $codopest = $codopest_db;

    // Fecha de inicio (año)
    $fecha_inicio = ($fecha_inicio_db === '0000' || !$fecha_inicio_db)
                  ? 'No Existe Registro'
                  : $fecha_inicio_db;

    // Traducir tipo de programa
    if ($tipo_db === 'Especializacion') {
        $tipo = 'Programa de Especialización en';
    } elseif ($tipo_db === 'Maestria') {
        $tipo = 'Programa de Maestría en';
    } else {
        $tipo = $tipo_db;
    }

    $mencion_especialidad = $mencion_db;
    $periodos = $periodos_db;
    $creditos = $creditos_db;
} else {
    // Sin cohorte
    $ciudad               = 'No Existe Registro';
    $tipo                 = '';
    $mencion_especialidad = '';
    $codopest             = '';
    $codsede              = '';
    $periodos             = '';
    $creditos             = '';
    $fecha_inicio         = 'No Existe Registro';
}

mysqli_stmt_close($stmt2);












/*$sqlcmd = "SELECT apellidos, nombres, fecha_nacimiento "
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

if ($tipo == 'Maestria') $tipo = "Programa Maestria en ";*/

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
			Oficina de Control de Estudios<br/>
			Notas Certificadas
			</B>
		</FONT>
		
		<BR>
		
		<FONT SIZE="-1" FACE="Arial">
			<B><I>&nbsp;</I></B><BR>
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
			ESCALA DE CALIFICACIONES UNO A VEINTE (01-20) PUNTOS. NOTA M&Iacute;NIMA APROBATORIA QUINCE (15) PUNTOS.
		</FONT>
	</TD>
</TR>
</TABLE>


<TABLE BORDER="0" WIDTH="680" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000000">
<TR>
	<TD WIDTH="100" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			C&oacute;digo
		</FONT>
	</TD>
	<TD WIDTH="320" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Nombre de la Asignatura
		</FONT>
	</TD>
	<TD WIDTH="80" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Cr&eacute;ditos
		</FONT>
	</TD>
	<TD WIDTH="90" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Nota
		</FONT>
	</TD>
	<TD WIDTH="90" ALIGN="center" VALIGN="top">
		<FONT SIZE="-1" FACE="Arial" COLOR="#FFFFFF">
			Per&iacute;odo
		</FONT>
	</TD>
</TR>
<?
	$notas = "";
	$total_creditos = "";

// Consulta unificada
$sql = "
 SELECT
   pe.codasig, pe.asignatura, pe.creditos, pe.periodos, pe.codasig_imp,
   sn.calificacion
 FROM pensum_estudios pe
 LEFT JOIN (
   SELECT ra.codasig, rn.calificacion
   FROM registro_actas ra
   JOIN record_notas rn
     ON ra.codacta = rn.codacta
    AND rn.cedula = ?
   WHERE ra.codcohorte = ?
   UNION ALL
   SELECT ma.codasig, rn.calificacion
   FROM multiactas ma
   JOIN record_notas rn
     ON ma.mid    = rn.mid
    AND rn.cedula = ?
   WHERE ma.codcohorte = ?
 ) AS sn ON sn.codasig = pe.codasig
 WHERE pe.codsede  = ?
   AND pe.codopest = ?
   AND pe.status   = 'Activa'
 ORDER BY pe.periodos, pe.codasig
";

$stmt = mysqli_prepare($conexion, $sql)
    or die('Prepare falló: '.mysqli_error($conexion));

// Bind placeholders: cedula, cohorte, cedula, cohorte, codsede, codopest
mysqli_stmt_bind_param($stmt,'ssssss',
    $cedula, $codcoh, $cedula, $codcoh, $codsede, $codopest
);

mysqli_stmt_execute($stmt);

// Bind resultados
mysqli_stmt_bind_result(
  $stmt,
  $codasig, $asig, $cred, $per, $codimp, $calRaw
);

// Map calificaciones
$mapCalif = array(
  404 => 'No Cursó',
   99 => 'Reprobado',
  100 => 'Aprobado',
  110 => 'Meritorio',
  120 => 'Excelencia',
  212 => 'Equivalencia'
);


$totP=0; $totC=0; $bg='#FFF';

// Crear tabla

while(mysqli_stmt_fetch($stmt)){
  // Formatear calif
  if($calRaw===null)      $cal='Pendiente';
  elseif(isset($map[$calRaw])) $cal=$map[$calRaw];
  else                    $cal=$calRaw;

  // Ponderado
  if(is_numeric($calRaw)&&$calRaw>=1&&$calRaw<=20){
    $totP += $calRaw*$cred;
    $totC += $cred;
  }

  // Color
  $bg=($bg=='#FFF')?'#EEE':'#FFF';

  // Imprimir fila
  echo "<tr style='background:$bg'>
    <td>$codimp</td>
    <td>$asig</td>
    <td align=center>$cred</td>
    <td align=center>$cal</td>
	<td align=center>$per</td>
    
  </tr>";
}

echo "</table>";
mysqli_stmt_close($stmt);

// Promedio
$prom = $totC?round($totP/$totC,2):0;
echo "<p><strong>Promedio:</strong> $prom</p>";







	/*$sqlcmd = "SELECT codasig, asignatura, creditos, periodos, codasig_imp "
			. "FROM pensum_estudios "
			. "WHERE codsede='$codsede' AND pensum_estudios.codopest='$codopest' AND pensum_estudios.status='Activa' "
			. "ORDER BY periodos, codasig ";

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
				
				if ( ($calificacion >= 1) AND ($calificacion <= 20) )
				{
					$notas = $notas + ($calificacion * $creditos);
					
					$total_creditos = $total_creditos + $creditos;
				}
			}


		}


		if ( ($cantidad_registro_actas < 1) AND ($cantidad_multiactas < 1) )	$status_materia_pendiente = 1;

	
		if ( ($status_materia_pendiente) OR ($calificacion < 15) OR ($calificacion == 404) OR ($calificacion == 99) ) $calificacion = 'Pendiente';


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

	}*/


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
<BR><BR><BR><B>TSU Mercedes Labrador</B><BR>
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
					<BR><BR><BR><B>Esp. Herman Y. Bandez S.</B><BR>
					Secretario
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

