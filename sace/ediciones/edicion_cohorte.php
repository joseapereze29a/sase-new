<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/arreglo_meses_dias.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/arreglo_periodos_lectivos.php");


/*
+---------+----------+------------+--------------+-----------------+----------------+--------------------+-------------------+-----------------------+---------------+-------------------+
| codsede | codopest | codcohorte | fecha_inicio | periodo_lectivo | fecha_creacion | fecha_modificacion | operador_creacion | operador_modificacion | host_creacion | host_modificacion |
+---------+----------+------------+--------------+-----------------+----------------+--------------------+-------------------+-----------------------+---------------+-------------------+
| COC     | ESP-TCI  | COCTCI98-I | 1998-06-20   | 98-I            | NULL           | NULL               | NULL              | NULL                  | NULL          | NULL              |
+---------+----------+------------+--------------+-----------------+----------------+--------------------+-------------------+-----------------------+---------------+-------------------+
*/


if ( ( ($continuar) OR ($cancelar) ) AND ( ($_codsede) AND ($_codopest) AND ($_codopest) ) )
{

	if ($cancelar)
	{
		$url = "seleccion_cohorte.php?_codsede=" . $_codsede . "&_codopest=" . $_codopest;

		header("Location: $url");
		exit;
	}
	

	if ( (! $_dia) OR (! $_mes) OR (! $_ano) )  $error_fecha = 1;


	if (! $error_fecha)
	{

		$fecha_final = $_ano . '-' . $_mes . '-' . $_dia;

		if ($fecha_final != $fecha_inicio_anterior)
		{
		
			$sqlcmd = "UPDATE cohortes SET "
					. "fecha_inicio='$fecha_final', fecha_modificacion=NOW(), operador_modificacion='$PHP_AUTH_USER', "
					. "host_modificacion='$REMOTE_ADDR' "
					. "WHERE codcohorte='$_codcohorte' ";

			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
	
			$sqlcmd = "INSERT INTO ediciones_cohortes (codcohorte, fecha_modificacion, operador_modificacion, host_modificacion, operacion) VALUES ("
					. "'$_codcohorte', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR', "
					. "'Se modifico la fecha de la cohorte: $_codcohorte. La anterior era $fecha_inicio_anterior, la nueva $fecha_final.') "; 
	
			$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");

		}

		$url = "edicion_cohorte_finalizado.php?_codsede=" . $_codsede . "&_codopest=" . $_codopest;

		header("Location: $url");
		exit;

	}


}




$sqlcmd = "SELECT directorio_cippsv.modalidad, directorio_cippsv.ciudad, directorio_cippsv.edo_prov, "
		. "oportunidades_estudio.tipo, oportunidades_estudio.mencion_especialidad "
		. "FROM directorio_cippsv, oportunidades_estudio "
		. "WHERE oportunidades_estudio.codsede='$_codsede' AND oportunidades_estudio.codopest='$_codopest' AND "
		. "directorio_cippsv.codsede=oportunidades_estudio.codsede ";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$modalidad = $registro->modalidad;
	$ciudad = $registro->ciudad;
	$edo_prov = $registro->edo_prov;
	$tipo = $registro->tipo;
	$mencion_especialidad = $registro->mencion_especialidad;
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


<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
	<TD WIDTH="100%" ALIGN="left" VALIGN="top">
	
		<A HREF="../"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_de_sede.php"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n de Sede</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_postgrado.php?_codsede=<? echo $_codsede ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Postgrado</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_cohorte.php?_codsede=<? echo $_codsede ?>&_codopest=<? echo $_codopest ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Cohortes Existentes</B></FONT></A>
		
		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Edici&oacute;n de Cohorte</B></FONT>
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
			<B>Informaci&oacute;n sobre la Cohorte</B>
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
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Cohorte</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $_codcohorte ?>
		</FONT>
	</TD>
</TR>
</TABLE>


<?
if ($error_fecha) 
{
	echo '<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">';
	echo '<TR><TD WIDTH="600" ALIGN="left" VALIGN="top">';
	
	echo '<FONT FACE="Verdana,Arial,Geneva" COLOR="#FF0000">';
	echo '<B>Se ha encontrado algun Error al tratar de procesar el Acta</B>';
	echo '</FONT><BR><BR>';


	if ($error_fecha)
	{
		echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
		echo '&bull; Debe selecionar una <B>Fecha de Inicio de la Cohorte</B> V&aacute;lida, favor revisar.';
		echo '</FONT><BR>';
	}
	
/*
	if ($error_periodo_lectivo)
	{
		echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
		echo '&bull; N&uacute;mero de <B>Periodo Lectivo</B> Inv&aacute;lido, favor revisar.';
		echo '</FONT><BR>';
	}
*/

	echo '</TD></TR></TABLE>';
}



	$sqlcmd = "SELECT fecha_inicio, periodo_lectivo "
			. "FROM cohortes "
			. "WHERE codcohorte='$_codcohorte' ";

	/*
	+--------------+-----------------+
	| fecha_inicio | periodo_lectivo |
	+--------------+-----------------+
	| 1997-04-06   | 97-I            |
	+--------------+-----------------+
	*/


	$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
	
	while ($registro = mysql_fetch_object($query))
	{
		$fecha_inicio = $registro->fecha_inicio;
		$periodo_lectivo = $registro->periodo_lectivo;
	}

	$fecha_inicio_anterior = $fecha_inicio;


	list($_ano_anterior,$_mes_anterior,$_dia_anterior) = split ("-", $fecha_inicio_anterior);
	$fecha_inicio_anterior = ABS($_ano_anterior) . '-' . ABS($_mes_anterior) . '-' . ABS($_dia_anterior);
	

	list($_ano,$_mes,$_dia) = split ("-", $fecha_inicio);

	#list($periodo_lectivo_ano,$_periodo_lectivo) = split ("-", $periodo_lectivo);
?>


<FORM ACTION="edicion_cohorte.php" METHOD="post">

<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Fecha de Inicio de la Cohorte</B>
		</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<B>Periodo Lectivo</B>
		</FONT>
	</TD>
</TR>
<TR>
	<TD WIDTH="400" ALIGN="left" VALIGN="top">

				<SELECT NAME="_dia">
					<?
						#if (! $_dia) $_dia = date(j); 
			
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
						#if (! $_mes) $_mes = date(n);
			
						for($i=1; $i<13; $i++)
						{
							if ($_mes == $i)
							{
								echo '<OPTION VALUE="' . $i . '" SELECTED>' . "($i) - " . $meses[$i] . "\n";
							} else {
								echo '<OPTION VALUE="' . $i . '">' . "($i) - " . $meses[$i] . "\n";
							}
						}
					?>
				</SELECT>


				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
					<? echo $_ano ?>
				</FONT>
	</TD>
	<TD WIDTH="200" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $periodo_lectivo ?>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR><BR><BR>

<INPUT TYPE="hidden" NAME="_codsede" VALUE="<? echo $_codsede ?>">
<INPUT TYPE="hidden" NAME="_codopest" VALUE="<? echo $_codopest ?>">
<INPUT TYPE="hidden" NAME="_codcohorte" VALUE="<? echo $_codcohorte ?>">
<INPUT TYPE="hidden" NAME="_ano" VALUE="<? echo $_ano ?>">

<INPUT TYPE="hidden" NAME="fecha_inicio_anterior" VALUE="<? echo $fecha_inicio_anterior ?>">


<INPUT TYPE="submit" NAME="continuar" VALUE="Continuar">
<INPUT TYPE="submit" NAME="cancelar" VALUE="Cancelar">


</FORM>

<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
