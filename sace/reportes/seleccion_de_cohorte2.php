<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_alumnos_por_cohorte.php");


$sqlcmd = "SELECT ciudad, edo_prov FROM directorio_cippsv WHERE codsede='$codsede' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$ciudad = $registro->ciudad;
	$edo_prov = $registro->edo_prov;
}



$sqlcmd = "SELECT fecha_inicio FROM cohortes WHERE codsede='$codsede' ORDER BY fecha_inicio LIMIT 1 ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$primera_cohorte = $registro->fecha_inicio;
}



$sqlcmd = "SELECT count(*) AS cantidad_cohortes FROM cohortes WHERE codsede='$codsede' ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$cantidad_cohortes = $registro->cantidad_cohortes;
}

?>
<HTML>
<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.3 - MacOS X">
</HEAD>
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

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n de Cohorte</B></FONT> 
		
	</TD>
</TR>
</TABLE>


<BR><BR>


<FONT FACE="Verdana,Arial,Geneva" COLOR="#000099">
<B>Seleccione la Cohorte a la cual desea ver los Reportes</B>
</FONT>

<BR><BR><BR>

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
			<B>Primera Cohorte</B>
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
			<? echo fecha ($primera_cohorte) ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Total Cohortes:</B> &nbsp;  <? echo $cantidad_cohortes ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR><BR>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
	<A HREF="seleccion_de_cohorte.php?codsede=<? echo $codsede ?>">Viendo Reporte Por A&ntilde;o</A> | 
	Viendo Reporte por Especialidad o Postgrado
</FONT>

<BR><BR>

<?
$sqlcmd = "SELECT codopest, mencion_especialidad, tipo "
		. "FROM oportunidades_estudio "
		. "WHERE codsede='$codsede' "
		. "ORDER BY codopest ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

#echo "$sqlcmd<BR>";

while ($registro = mysql_fetch_object($query))
{
	$codopest = $registro->codopest;
	$mencion_especialidad = $registro->mencion_especialidad;
	$tipo = $registro->tipo;


	$sqlcmd2 = "SELECT count(*) AS cantidad_por_opest "
			 . "FROM cohortes "
			 . "WHERE codsede='$codsede' AND codopest='$codopest' ";

	$query2 = mysql_db_query(DB_DATABASE,"$sqlcmd2");
	
	while ($registro2 = mysql_fetch_object($query2))
	{
		$cantidad_por_opest = $registro2->cantidad_por_opest;
	}

#echo "cantidad_por_opest $cantidad_por_opest<BR>";

	if ($cantidad_por_opest > 0)
	{

?>
		<BR><BR>
		
		<TABLE BORDER="0" WIDTH="500" CELLSPACING="1" CELLPADDING="2">
		<TR>
			<TD WIDTH="500" ALIGN="left" VALIGN="top">
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
					<B><? echo $tipo . ' ' . $mencion_especialidad ?></B><BR>
				</FONT>
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
					Total Cohortes: <? echo $cantidad_por_opest ?>
				</FONT>
			</TD>
		</TR>
		</TABLE>


		<TABLE BORDER="0" WIDTH="470" CELLSPACING="1" CELLPADDING="2" BGCOLOR="#000099">
		<TR>
			<TD WIDTH="50" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
					<B>N&uacute;m.</B>
				</FONT>
			</TD>
			<TD WIDTH="200" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
					<B>Fecha de Inicio</B>
				</FONT>
			</TD>
			<TD WIDTH="75" ALIGN="right" VALIGN="top" BGCOLOR="#000099">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
					<B>Actas</B>
				</FONT>
			</TD>
			<TD WIDTH="75" ALIGN="right" VALIGN="top" BGCOLOR="#000099">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
					<B>Notas</B>
				</FONT>
			</TD>
			<TD WIDTH="70" ALIGN="right" VALIGN="top" BGCOLOR="#000099">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
					<B>Alumnos</B>
				</FONT>
			</TD>
		</TR>
	
<?
		$sqlcmd3 = "SELECT codcohorte, fecha_inicio "
				 . "FROM cohortes "
				 . "WHERE codsede='$codsede' AND codopest='$codopest' "
				 . "ORDER BY fecha_inicio ";

#echo "$sqlcmd3<BR>";

		$query3 = mysql_db_query(DB_DATABASE,"$sqlcmd3");
	
		while ($registro3 = mysql_fetch_object($query3))
		{
			$contador++;
	
			$codcohorte = $registro3->codcohorte;
			$fecha_inicio = $registro3->fecha_inicio;


			$sqlcmd4 = "SELECT count(*) as cantidad_actas FROM registro_actas WHERE codcohorte='$codcohorte' ";

			$query4 = mysql_db_query(DB_DATABASE,"$sqlcmd4");
					
			while ($registro4 = mysql_fetch_object($query4))
			{
				$cantidad_actas = $registro4->cantidad_actas;
			}
	
			$cantidad_actas_total = $cantidad_actas_total + $cantidad_actas;


			######################################################################

			$sqlcmd4 = "SELECT count(*) as cantidad_actas FROM multiactas WHERE codcohorte='$codcohorte' ";

			$query4 = mysql_db_query(DB_DATABASE,"$sqlcmd4");
					
			while ($registro4 = mysql_fetch_object($query4))
			{
				$cantidad_actas = $registro4->cantidad_actas;
			}
	
			$cantidad_actas_total = $cantidad_actas_total + $cantidad_actas;


			### Para la tabla 'registro_actas'

			$sqlcmd5 = "select count(*) as cantidad_notas "
					 . "FROM registro_actas, record_notas "
					 . "WHERE registro_actas.codcohorte='$codcohorte' AND registro_actas.codacta=record_notas.codacta "
					 . "GROUP BY registro_actas.codacta ";
			
			$query5 = mysql_db_query(DB_DATABASE,"$sqlcmd5");
			
			while ($registro5 = mysql_fetch_object($query5))
			{
		
				$cantidad_notas = $registro5->cantidad_notas;
	
				$cantidad_notas_acumuladas = $cantidad_notas_acumuladas + $cantidad_notas;
			}


			### Para la tabla 'multiactas'
	
			$sqlcmd5 = "SELECT count(*) as cantidad_notas "
					 . "FROM multiactas, record_notas "
					 . "WHERE multiactas.codcohorte='$codcohorte' AND multiactas.codacta=record_notas.codacta "
					 . "AND multiactas.mid=record_notas.mid "
					 . "GROUP BY multiactas.codacta ";
	
			$query5 = mysql_db_query(DB_DATABASE,"$sqlcmd5");
			
			while ($registro5 = mysql_fetch_object($query5))
			{
		
				$cantidad_notas = $registro5->cantidad_notas;
	
				$cantidad_notas_acumuladas = $cantidad_notas_acumuladas + $cantidad_notas;
			}


			$cantidad_notas_acumuladas_total = $cantidad_notas_acumuladas_total + $cantidad_notas_acumuladas;
	
			if ($ano_cohortes != $ano_cohortes_anterior)
			{
				$imprimo_ano_cohortes = 1;
				#echo "ano: $ano_cohortes<BR>";
				$ano_cohortes_anterior = $ano_cohortes;
				$cantidad_por_ano_anterior = 0;
			}
		
			if ($cantidad_por_ano != $cantidad_por_ano_anterior)
			{
				$imprimo_cantidad_por_ano = 1;
				#echo "cantidad en este ano: $cantidad_por_ano<BR>";
				$cantidad_por_ano_anterior = $cantidad_por_ano;
			}
		
	
			if ($imprimo_ano_cohortes == $imprimo_ano_cohortes)
			{

				$imprimo_ano_cohortes = 0;

			}


			$alumnos_por_cohorte_var = alumnos_por_cohorte($codcohorte);

			$alumnos_por_cohorte_var_total = $alumnos_por_cohorte_var_total + $alumnos_por_cohorte_var;



			if ($bg_celda == '#CCCCCC')
			{
				$bg_celda = '#FFFFFF';
			} else {
				$bg_celda = '#CCCCCC';
			}
?>
			<TR>
				<TD WIDTH="50" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo $contador ?>
					</FONT>
				</TD>
				<TD WIDTH="200" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo fecha($fecha_inicio) ?>
					</FONT>
				</TD>
				<TD WIDTH="75" ALIGN="right" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo $cantidad_actas ?>
					</FONT>
				</TD>
				<TD WIDTH="75" ALIGN="right" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo $cantidad_notas_acumuladas ?>
					</FONT>
				</TD>
				<TD WIDTH="70" ALIGN="right" VALIGN="top" BGCOLOR="<? echo $bg_celda ?>">
					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva">
						<? echo $alumnos_por_cohorte_var ?>
					</FONT>
				</TD>
			</TR>
<?
#echo "$contador | $mencion_especialidad | cantidad actas $cantidad_actas | cantidad notas $cantidad_notas_acumuladas <BR><BR>";
			$cantidad_notas_acumuladas = 0;

		
		}
?>
		</TABLE>

			
		<TABLE BORDER="0" WIDTH="470" CELLSPACING="1" CELLPADDING="2">
		<TR>
			<TD WIDTH="250" ALIGN="right" VALIGN="top">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000099">
					<B>TOTAL</B> &nbsp; 
				</FONT>
			</TD>
			<TD WIDTH="75" ALIGN="right" VALIGN="top">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000099">
					<B><? echo $cantidad_actas_total ?>
				</FONT>
			</TD>
			<TD WIDTH="75" ALIGN="right" VALIGN="top">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000099">
					<B><? echo $cantidad_notas_acumuladas_total ?>
				</FONT>
			</TD>
			<TD WIDTH="70" ALIGN="right" VALIGN="top">
				<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000099">
					<B><? echo $alumnos_por_cohorte_var_total ?>
				</FONT>
			</TD>
		</TR>
		</TABLE>

<?

		##########################################################################
		### Calculo los Totales Finales ##########################################
		
		$cantidad_actas_total_final = $cantidad_actas_total_final + $cantidad_actas_total;
	
		$cantidad_notas_acumuladas_total_final = $cantidad_notas_acumuladas_total_final + $cantidad_notas_acumuladas_total;
	
		$alumnos_por_cohorte_var_total_final = $alumnos_por_cohorte_var_total_final + $alumnos_por_cohorte_var_total;
	
		##########################################################################
	


		#echo "$cantidad_actas_total | $cantidad_notas_acumuladas_total <BR>";
		
		$cantidad_actas_total = 0;
		
		$cantidad_notas_acumuladas_total = 0;
	
		$bg_celda = '';
		
		$contador = 0;
		
		$alumnos_por_cohorte_var_total = 0;
	
	}


}

?>

<BR>

<TABLE BORDER="0" WIDTH="350" CELLSPACING="1" CELLPADDING="2">
<TR>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
			<B>TOTAL ACTAS</B>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
			<B><? echo $cantidad_actas_total_final ?>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
			<B>TOTAL NOTAS</B>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
			<B><? echo $cantidad_notas_acumuladas_total_final ?>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="250" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
			<B>TOTAL ESTUDIANTES</B>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#000099">
			<B><? echo $alumnos_por_cohorte_var_total_final ?>
		</FONT>
	</TD>
</TR>
</TABLE>


<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
