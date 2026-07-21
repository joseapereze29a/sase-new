<?
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_fecha.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/arreglo_meses_dias.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_construyo_codacta.php");

include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_datos_profesor.php");

if (! $_codacta)	$_codacta = construyo_codacta($_codcohorte,$_codasig,$_cd);


if ( ($continuar_f) OR ($continuar_a) )
{

	for ($i=1; $i<6; $i++)
	{
	
		$cedula = 'cedula_' . $i;

		if ($$cedula)	if (! ereg ("^[0-9]+$", $$cedula) )
		{
			$error_1 = 1;
			$error1[] = $i;
		}
	}



	for ($i=1; $i<6; $i++)
	{
		$cedula = 'cedula_' . $i;

		if ($$cedula)	$si_hay_info = 1;
	}
	
	for ($i=1; $i<6; $i++)
	{
		if (! $si_hay_info)
		$error_8 = 1;
	}
	



	if ( (! $error_1) AND (! $error_8) )
	{
	
		for ($i=1; $i<6; $i++)
		{
		
			$cedula = 'cedula_' . $i;
			$nota = 'nota_' . $i;
			$calificacion = 'calificacion_' . $i;
	
	
			if ($$cedula)
			{
					if ( (! ($$nota)) AND (! ($$calificacion)) )
					{
						$error_2 = 1;
						$error2[] = $i;
					}
			}

		}

	}


	if ( (! $error_1) AND (! $error_2) AND (! $error_8) )
	{
	
		for ($i=1; $i<6; $i++)
		{
		
			$cedula = 'cedula_' . $i;
			$nota = 'nota_' . $i;
			$calificacion = 'calificacion_' . $i;
	
	
			if ($$cedula)
			{			
					if ( ($$nota) AND ($$calificacion) )
					{
						$error_6 = 1;
						$error6[] = $i;
					}
			}

		}

	}


	if ( (! $error_1) AND (! $error_2) AND (! $error_6) AND (! $error_8) )
	{
	
		for ($i=1; $i<6; $i++)
		{
		
			$cedula = 'cedula_' . $i;
			$nota = 'nota_' . $i;
	
	
			if ( ($$cedula) AND ($$nota) )
			{
					if ( ($$nota < 1) OR ($$nota > 20) )
					{
						$error_7 = 1;
						$error7[] = $i;
					}
			}

		}

	}


	if ( (! $error_1) AND (! $error_2) AND (! $error_6) AND (! $error_7) AND (! $error_8) )
	{
			for ($i=1; $i<6; $i++)
			{
				$iguales = 0;
				$cedula = 'cedula_' . $i;
		
					for ($j=1; $j<6; $j++)
					{
					
						$cedula2 = 'cedula_' . $j;
			
							if (($$cedula) AND ($$cedula2) )
							{
									if ($$cedula == $$cedula2) $iguales ++;
									if ($iguales >1)
									{
										$error_3 = 1;
										$error3[] = $i;
									}
				
							}
			
					}
				
			}
	}



	if ( (! $error_1) AND (! $error_2) AND (! $error_3) AND (! $error_6) AND (! $error_7) AND (! $error_8) )
	{
		/*
			$sqlcmd = "SELECT count(*) as cantidad FROM record_notas WHERE codacta='$_codacta'";
			
			$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
			
			while ($registro = mysql_fetch_object($query))
			{
				$cantidad_error_5 = $registro->cantidad;
			}
		*/
		
			if ($cantidad_error_5 < 1)
			{
				if ( ($_dia) AND ( (! $_mes) OR (! $_ano) ) )	$error_5 = 1;
				if ( ($_mes) AND ( (! $_dia) OR (! $_ano) ) )	$error_5 = 1;
				if ( ($_ano) AND ( (! $_dia) OR (! $_mes) ) )	$error_5 = 1;
			}

	}



	if ( (! $error_1) AND (! $error_2) AND (! $error_3) AND (! $error_6) AND (! $error_7) AND (! $error_5) AND (! $error_8) )
	{

				if ($_codacta)
				{
				
							for ($i=1; $i<6; $i++)
							{
							
										$cedula = 'cedula_' . $i;
								
										if ($$cedula)
										{
							
													$sqlcmd = "SELECT count(*) as cantidad "
															. "FROM record_notas "
															. "WHERE codacta='$_codacta' AND cedula='" . $$cedula. "' ";

													$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
									
													while ($registro = mysql_fetch_object($query))
													{
														$cantidad = $registro->cantidad;
													}
							
							
													if ($cantidad > 0)
													{
														$error_4 = 1;
														$error4[] = $i;
													}
					
										}
					
							}
	
				}

	}



	if ( (! $error_1) AND (! $error_2) AND (! $error_3) AND (! $error_6) AND (! $error_7) AND (! $error_4) AND (! $error_5) AND (! $error_8) )
	{

				$query_codacta = $_codacta;

				$sqlcmd = "SELECT count(*) as cantidad "
						. "FROM multiactas "
						. "WHERE codacta='$query_codacta' and mid='$mid' ";

				$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

				while ($registro = mysql_fetch_object($query))
				{
					$cantidad = $registro->cantidad;
				}


				if ($cantidad > 0)
				{
							#echo 'Existe el acta, entonces, agrego los datos.<BR><BR>';
		
							for ($i=1; $i<6; $i++)
							{
							
									$cedula = 'cedula_' . $i;
									$nota = 'nota_' . $i;
									$calificacion = 'calificacion_' . $i;
			
			
									if ($$cedula)
									{

										if (	( ($$nota >= 1) AND ($$nota < 21) ) OR ( ($$calificacion >= 1) AND ($$calificacion < 405) )		)
										{
											if ($$nota) $meto_calif = $$nota;
											if ($$calificacion) $meto_calif = $$calificacion;
											
											$sqlcmd = "INSERT INTO record_notas (codacta, cedula, calificacion, fecha_creacion, operador_creacion, host_creacion, mid) VALUES ("
													. "'$query_codacta', '" . $$cedula . "', '" . $meto_calif . "', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR', '$mid')";
				
				
											#echo "$sqlcmd<BR><BR>";
											$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
											
											$meto_calif = '';
			
										}

									}
			
							}


							if ($continuar_f)
							{
									$url = "ingreso_finalizado.php?_codcohorte=" . $_codcohorte;
									
									$num_cd = substr("$_cd", -1);
									
									if ($_cd) $url = $url . '&_cd=' . $num_cd;
									
									header("Location: $url");
									exit;
							}


							if ($continuar_a)
							{		
									$url = "ingresando_multiacta.php?_codcohorte=" . $_codcohorte . "&_codasig=" . $_codasig . "&_codacta=" . $_codacta . "&_ok=1";
									$url = $url . '&mid=' . $mid;
									
									$num_cd = substr("$_cd", -1);
									
									if ($_cd) $url = $url . '&_cd=' . $num_cd;
									
									header("Location: $url");
									exit;
							}


				} else {


							#echo 'Creo el acta y luego agrego los datos.<BR><BR>';
							#echo 'Genero el Acta<BR>';
							
							# PPALTCI99-II		Separa por el Guion, bajo un SPLIT
							
							# en una variable agarro el primero y segundo y los uno en uno solo y meto un guion.
							
							# Luego voy a agarrar "TC-016" y hago un split, tomando del segundo, solo los ultimos 2 digitos.
							
							### Debo hacer una funcion con esta vaina, Despues
							
							list($_codcohorte_1,$_codcohorte_2) = split ("-", $_codcohorte);
							
							list($_codasig_1,$_codasig_2) = split ("-", $_codasig);
							
							$_codasig_2_last_dos_digitos = substr($_codasig_2, -2);
							
							$_codacta_final = $_codcohorte_1 . $_codcohorte_2 . "-" . $_codasig_2_last_dos_digitos;
							
							$num_cd = substr("$_cd", -1);
							
							if ($_cd) $_codacta_final = $_codacta_final . "CD" . $num_cd;
							
							if ($_ci_profesor1 == "") $_ci_profesor1 = 0;
							if ($_ci_profesor2 == "") $_ci_profesor2 = 0;
							if ($_ci_profesor3 == "") $_ci_profesor3 = 0;
							if ($_ci_profesor4 == "") $_ci_profesor4 = 0;
							if ($_ci_profesor5 == "") $_ci_profesor5 = 0;
							
							if ( ($_ano) AND ($_mes) AND ($_dia) )
							{
								$_fecha_aprobacion = $_ano . '-'. $_mes . '-' . $_dia;
							
							} else {
							
								$_fecha_aprobacion = '0000-00-00';
							}
		
							$sqlcmd = "INSERT INTO multiactas (codcohorte, codasig, codacta, cedula_profesor1, cedula_profesor2, cedula_profesor3, "
									. "cedula_profesor4, cedula_profesor5, fecha_aprobacion, fecha_creacion, operador_creacion, host_creacion) "
									. "VALUES ("
									. "'$_codcohorte', '$_codasig', '$_codacta_final', '$_ci_profesor1', '$_ci_profesor2', '$_ci_profesor3', "
									. "'$_ci_profesor4', '$_ci_profesor5', '$_fecha_aprobacion', NOW(), '$PHP_AUTH_USER', '$REMOTE_ADDR')";
		
							#echo "$sqlcmd<BR><BR>";
							$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
							
							$mid = mysql_insert_id();
							
							if ($_fecha_aprobacion == '0000-00-00')
							{
							
								$sqlcmd = "UPDATE multiactas SET fecha_aprobacion=NULL WHERE codacta='$_codacta_final' AND mid='$mid' ";

								$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
							
							}


							if ($_ci_profesor1 == 0)
							{
								$sqlcmd = "UPDATE multiactas SET cedula_profesor1=NULL WHERE codacta='$_codacta_final' AND mid='$mid' ";

								$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
							}

							if ($_ci_profesor2 == 0)
							{
								$sqlcmd = "UPDATE multiactas SET cedula_profesor2=NULL WHERE codacta='$_codacta_final' AND mid='$mid' ";

								$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
							}

							if ($_ci_profesor3 == 0)
							{
								$sqlcmd = "UPDATE multiactas SET cedula_profesor3=NULL WHERE codacta='$_codacta_final' AND mid='$mid' ";

								$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
							}

							if ($_ci_profesor4 == 0)
							{
								$sqlcmd = "UPDATE multiactas SET cedula_profesor4=NULL WHERE codacta='$_codacta_final' AND mid='$mid' ";

								$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
							}
	
							if ($_ci_profesor5 == 0)
							{
								$sqlcmd = "UPDATE multiactas SET cedula_profesor5=NULL WHERE codacta='$_codacta_final' AND mid='$mid' ";

								$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");
							}


							#echo 'AHORA Existe el acta, entonces, agrego los datos.<BR><BR>';

							if ($_ci_profesor1 == 0) $cedula_profesor1 = "";
							if ($_ci_profesor2 == 0) $cedula_profesor2 = "";
							if ($_ci_profesor3 == 0) $cedula_profesor3 = "";
							if ($_ci_profesor4 == 0) $cedula_profesor4 = "";
							if ($_ci_profesor5 == 0) $cedula_profesor5 = "";

							for ($i=1; $i<6; $i++)
							{
							
									$cedula = 'cedula_' . $i;
									$nota = 'nota_' . $i;
									$calificacion = 'calificacion_' . $i;
			
			
									if ($$cedula)
									{

										if (	( ($$nota >= 1) AND ($$nota < 21) ) OR ( ($$calificacion >= 1) AND ($$calificacion < 405) )		)
										{

												if ($$nota) $meto_calif = $$nota;
												if ($$calificacion) $meto_calif = $$calificacion;

												$sqlcmd = "INSERT INTO record_notas (codacta, cedula, calificacion, fecha_creacion, operador_creacion, "
														. "host_creacion, mid) VALUES ("
														. "'$_codacta_final', '" . $$cedula . "', '" . $meto_calif . "', NOW(), '$PHP_AUTH_USER', "
														. "'$REMOTE_ADDR', '$mid')";
				
				
												#echo "$sqlcmd<BR><BR>";
												$resultado = mysql_db_query(DB_DATABASE,"$sqlcmd");

										}
			
									}
		
		
							}
	
	
							if ($continuar_f)
							{
									$url = "ingreso_finalizado.php?_codcohorte=" . $_codcohorte;
									
									$num_cd = substr("$_cd", -1);
									
									if ($_cd) $url = $url . '&_cd=' . $num_cd;
									
									header("Location: $url");
									exit;
							}


							if ($continuar_a)
							{		
									$url = "ingresando_multiacta.php?_codcohorte=" . $_codcohorte . "&_codasig=" . $_codasig . "&_codacta=" . $_codacta_final . "&_ok=1";
									
									$url = $url . '&mid=' . $mid;
									
									$num_cd = substr("$_cd", -1);
									
									if ($_cd) $url = $url . '&_cd=' . $num_cd;
									
									header("Location: $url");
									exit;
							}


				}

		}

}
		


	
	/*
			<INPUT TYPE="submit" NAME="continuar_f" VALUE="&nbsp; Continuar y Finalizar &nbsp;">
	
			<INPUT TYPE="submit" NAME="continuar_a" VALUE="&nbsp; Continuar y Agregar Mas &nbsp;">
	*/
	



$sqlcmd = "SELECT directorio_cippsv.modalidad, directorio_cippsv.ciudad, directorio_cippsv.edo_prov, oportunidades_estudio.tipo, "
		. "oportunidades_estudio.mencion_especialidad, cohortes.fecha_inicio, cohortes.codsede, cohortes.codopest "
		. "FROM directorio_cippsv, oportunidades_estudio, cohortes "
		. "WHERE cohortes.codcohorte='$_codcohorte' AND cohortes.codsede=oportunidades_estudio.codsede AND "
		. "cohortes.codopest=oportunidades_estudio.codopest AND oportunidades_estudio.codsede=directorio_cippsv.codsede ";

#echo "$sqlcmd<BR>";
/*
+-----------+---------+----------+-----------+----------------------------+--------------+
| modalidad | ciudad  | edo_prov | tipo      | mencion_especialidad       | fecha_inicio |
+-----------+---------+----------+-----------+----------------------------+--------------+
| Nucleo    | Maracay | Aragua   | Postgrado | Orientaci—n de la Conducta | 1993-01-02   |
+-----------+---------+----------+-----------+----------------------------+--------------+
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

		<A HREF="seleccion_de_sede.php"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n de Sede</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_postgrado.php?_codsede=<? echo $codsede ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Postgrado</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_cohorte.php?_codsede=<? echo $codsede ?>&_codopest=<? echo $codopest ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Cohortes Existentes</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="seleccion_acta.php?_codcohorte=<? echo $_codcohorte ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Actas Existentes</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<A HREF="ingreso_acta.php?_codcohorte=<? echo $_codcohorte ?>"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Acta</B></FONT></A>

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 

		<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Ingreso de Notas</B></FONT>
	</TD>
</TR>
</TABLE>


<?
	#include ("$DOCUMENT_ROOT/includes/encabezado.php");
?>

<BR>

<IMG SRC="/sace/imagenes/titulos_de_home/titulo_ingreso.jpg" ALT="" WIDTH="380" HEIGHT="20" BORDER="0">

<BR><BR><BR>


<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="600" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Informaci&oacute;n sobre el Postgrado</B>
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

<FORM ACTION="ingresando_multiacta.php" METHOD="post">

<?
	if ($mid)
	{
	
		$sqlcmd = "SELECT count(*) as cantidad FROM multiactas WHERE codacta='$_codacta' AND mid='$mid' ";
		
		$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
		
		while ($registro = mysql_fetch_object($query))
		{
			$cantidad = $registro->cantidad;
		}
	
	
		if ($cantidad > 0)
		{
			$acta_existe = 1;
		
			$sqlcmd = "SELECT fecha_aprobacion FROM multiactas where codacta='$_codacta' AND mid='$mid' ";
			
			$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
			
			while ($registro = mysql_fetch_object($query))
			{
				$fecha_aprobacion = $registro->fecha_aprobacion;
			}
			
			#	include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/funcion_datos_profesor.php");
	
	
	
	
			$sqlcmd = "SELECT cedula_profesor1, cedula_profesor2, cedula_profesor3 "
					. "FROM multiactas "
					. "WHERE multiactas.codacta='$_codacta' AND mid='$mid' ";
			
			$query = mysql_db_query(DB_DATABASE,"$sqlcmd");
			
			while ($registro = mysql_fetch_object($query))
			{
				$cedula_profesor1 = $registro->cedula_profesor1;
				$cedula_profesor2 = $registro->cedula_profesor2;
				$cedula_profesor3 = $registro->cedula_profesor3;
			}


			$apellidos_nombres1 = datos_profesor($cedula_profesor1);
			$apellidos_nombres2 = datos_profesor($cedula_profesor2);
			$apellidos_nombres3 = datos_profesor($cedula_profesor3);

		}

	}
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
			<B>C.I. de los Profesores</B>
		</FONT>
	</TD>
</TR>
<?
	$error_color = '#FFFFFF';

	if ($error_5) $error_color = '#FF0000';
?>
<TR>
	<TD WIDTH="300" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $error_color ?>">
			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
	<?
		if ($acta_existe)
		{
			if ( ($fecha_aprobacion != NULL) AND ($fecha_aprobacion != '') )
			{
				echo fecha($fecha_aprobacion);

			} else {

				echo "No Tiene, o no Existe Registro";
			}

			echo '</FONT>';

		} else {

	?>
			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
				<SELECT NAME="_dia">
					<OPTION VALUE="" SELECTED>D&iacute;a
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
					<OPTION VALUE="" SELECTED>Mes
					<?
						#if (! $_mes) $_mes = date(n);
			
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
					<OPTION VALUE="" SELECTED>A&ntilde;o
					<?
						#if (! $_ano) $_ano = date(Y);
						
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
	<?
		}
	?>
	</TD>
	<TD WIDTH="300" ALIGN="left" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
		<?
			if ($acta_existe)
			{
	
				if ( ($apellidos_nombres1 != NULL) AND ($apellidos_nombres1 != '') )
				{
					if ($apellidos_nombres1)	echo $apellidos_nombres1 . '<BR>';
					if ($apellidos_nombres2)	echo $apellidos_nombres2 . '<BR>';
					if ($apellidos_nombres3)	echo $apellidos_nombres3 . '<BR>';
	
				} else {
	
					echo "No Existe Registro";
				}
	
			} else {
		?>
				<INPUT TYPE="text" NAME="_ci_profesor1" VALUE="<? echo $_ci_profesor1 ?>" SIZE="10" MAXLENGTH="9"> &nbsp;

			<A HREF="javascript:popup('_blank', 'buscar_profesor.php',650,400)">
			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#3300FF">Buscar Profesor</FONT></A><BR>
			
			<INPUT TYPE="text" NAME="_ci_profesor2" VALUE="<? echo $_ci_profesor2 ?>" SIZE="10" MAXLENGTH="9"><BR>
			<INPUT TYPE="text" NAME="_ci_profesor3" VALUE="<? echo $_ci_profesor3 ?>" SIZE="10" MAXLENGTH="9">

		<?
			}
		?>
		</FONT>
	</TD>
</TR>
</TABLE>


<BR>

<CENTER>
<?
		if ($_ok):
?>
				<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FF0000">
					<B>El Acta ha sido Ingresada Satisfactoriamente,<BR>si lo desea Ingrese m&aacute;s Calificaciones.</B>
				</FONT>
				
				<BR><BR>
<?
		endif;



if ( ($error_1) OR ($error_2) OR ($error_3) OR ($error_4) OR ($error_5) OR ($error_6) OR ($error_7) OR ($error_8) )
{
	echo '<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">';
	echo '<TR><TD WIDTH="600" ALIGN="left" VALIGN="top">';
	
	echo '<FONT FACE="Verdana,Arial,Geneva" COLOR="#FF0000">';
	echo '<B>Se ha encontrado algun Error al tratar de procesar el Acta</B>';
	echo '</FONT><BR><BR>';
}

if ($error_5)
{
	echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
	echo '&bull; Debe selecionar una Fecha de Aprobaci&oacute;n del Acta v&aacute;lida, favor revisar.';
	echo '</FONT><BR>';
}

if ($error_1)
{
	echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
	echo '&bull; N&uacute;mero de C&eacute;dula(s) de Identidad Inv&aacute;lida(s), favor revisar.';
	echo '</FONT><BR>';
}

if ($error_2)
{
	echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
	echo '&bull; C&eacute;dula(s) de Identidad sin Notas o Calificaci&oacute;n(es) Asociadas, favor revisar.';
	echo '</FONT><BR>';
}

if ($error_3)
{
	echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
	echo '&bull; N&uacute;mero de C&eacute;dula(s) de Identidad Repetidas, favor revisar.';
	echo '</FONT><BR>';
}

if ($error_4)
{
	echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
	echo '&bull; N&uacute;mero de C&eacute;dula(s) de Identidad ya existe en la Base de Datos, favor revisar.';
	echo '</FONT><BR>';
}

if ($error_6)
{
	echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
	echo '&bull; Para cada C&eacute;dula, Debe haber: o una Nota o una Calificaci&oacute;n Asociada, favor revisar.';
	echo '</FONT><BR>';
}

if ($error_7)
{
	echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
	echo '&bull; La Nota Debe ser de la Escala de 1 al 20 puntos, favor revisar.';
	echo '</FONT><BR>';
}

if ($error_8)
{
	echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
	echo '&bull; Debe existir por lo menos una C&eacute;dula de Identidad Ingresada.';
	echo '</FONT><BR>';
}

if ( ($error_1) OR ($error_2) OR ($error_3) OR ($error_4) OR ($error_5) OR ($error_6) OR ($error_7) OR ($error_8) )
{
	echo '</TD></TR></TABLE>';
}
?>
</CENTER>

<BR>

<?
$sqlcmd = "SELECT pensum_estudios.asignatura "
		. "FROM pensum_estudios, cohortes "
		. "WHERE cohortes.codcohorte='$_codcohorte' AND cohortes.codsede=pensum_estudios.codsede AND "
		. "cohortes.codopest=pensum_estudios.codopest AND pensum_estudios.codasig='$_codasig' ";
#echo "$sqlcmd<BR><BR>";

$query = mysql_db_query(DB_DATABASE,"$sqlcmd");

while ($registro = mysql_fetch_object($query))
{
	$asignatura = $registro->asignatura;
}
?>

<FONT FACE="Verdana,Arial,Geneva">
	<? echo $asignatura ?>
</FONT>

<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
	<? echo "($_codasig)" ?>
</FONT>

	<?
		if ($mid) :
	?>
			<A HREF="javascript:popup('_blank', 'detalle_multiacta.php?codacta=<? echo $_codacta ?>&mid=<? echo $mid ?>',650,600)">
			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#3300FF">ver</FONT></A>
	<?
		endif;
	?>


<BR>

<?
	if (! $_cd)
	{
		echo '<FONT FACE="Verdana,Arial,Geneva"><B>Lineal</B></FONT>';
	} else {
		echo '<FONT FACE="Verdana,Arial,Geneva"><B>Curso Dirigido</B></FONT>';
	}
?>

<BR>



<INPUT TYPE="hidden" NAME="_codcohorte" VALUE="<? echo $_codcohorte ?>">
<INPUT TYPE="hidden" NAME="_codasig" VALUE="<? echo $_codasig ?>">
<INPUT TYPE="hidden" NAME="_codacta" VALUE="<? echo $_codacta ?>">
<INPUT TYPE="hidden" NAME="mid" VALUE="<? echo $mid ?>">

<?
	if ($_cd) 
	{
		$num_cd = substr("$_cd", -1);

		echo '<INPUT TYPE="hidden" NAME="_cd" VALUE="' . $num_cd . '">';
	}
?>


<TABLE BORDER="0" WIDTH="450" CELLSPACING="2" CELLPADDING="2" BGCOLOR="#000099">
<TR>
	<TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>N&uacute;m.</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			&nbsp; &nbsp; <B>C&eacute;dula</B>
		</FONT>
	</TD>
	<TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Nota</B>
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="center" VALIGN="top" BGCOLOR="#000099">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
			<B>Calificaci&oacute;n</B>
		</FONT>
	</TD>
</TR>
</TABLE>

<BR>

<?

for ($i=1; $i<6; $i++)
{
	$error_color = '#FFFFFF';

	$cedula = 'cedula_' . $i;
	$nota = 'nota_' . $i;
	$calificacion = 'calificacion_' . $i;
	
?>
		<?
			for ($e=0; $e<20; $e++)
			{
				if ($error_1)	if ($i == $error1[$e]) $error_color = '#FF0000';
				if ($error_2)	if ($i == $error2[$e]) $error_color = '#FF0000';
				if ($error_3)	if ($i == $error3[$e]) $error_color = '#FF0000';
				if ($error_4)	if ($i == $error4[$e]) $error_color = '#FF0000';
				if ($error_6)	if ($i == $error6[$e]) $error_color = '#FF0000';
				if ($error_7)	if ($i == $error7[$e]) $error_color = '#FF0000';
			}
		?>
<TABLE BORDER="0" WIDTH="450" CELLSPACING="2" CELLPADDING="2">
<TR>
	<TD WIDTH="100" ALIGN="right" VALIGN="top">
		<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
			<? echo $i ?> &nbsp; &nbsp; &nbsp; 
		</FONT>
	</TD>
	<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="<? echo $error_color ?>">
		&nbsp; &nbsp; <INPUT TYPE="text" NAME="cedula_<? echo $i ?>" VALUE="<? echo $$cedula ?>" SIZE="11" MAXLENGTH="9">
	</TD>
	<TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $error_color ?>">
			<?
			/*
				$calificacion = 'calificacion_' . $i;
			
				for($j=1; $j<21; $j++)
				{
					if ($$calificacion == $j)
					{
						echo '<OPTION VALUE="' . $j . '" SELECTED>' . $j . "</OPTION>\n";
					} else {
						echo '<OPTION VALUE="' . $j . '">' . $j . "</OPTION>\n";
					}
				}
			*/
			?>

		<INPUT TYPE="text" NAME="nota_<? echo $i ?>" VALUE="<? echo $$nota ?>" SIZE="4" MAXLENGTH="2">

	</TD>
	<TD WIDTH="150" ALIGN="center" VALIGN="top" BGCOLOR="<? echo $error_color ?>">
		<SELECT NAME="calificacion_<? echo $i ?>">
			<OPTION VALUE=""> - -
			<?

				if ($$calificacion == '404')
				{
					echo '<OPTION VALUE="' . 404 . '" SELECTED>No Curs&oacute;</OPTION>' . "\n";
				} else {
					echo '<OPTION VALUE="' . 404 . '">No Curs&oacute;</OPTION>' . "\n";
				}


				#if ($$calificacion == '321')
				#{
				#	echo '<OPTION VALUE="' . 321 . '" SELECTED>Retir&oacute;' . "\n";
				#} else {
				#	echo '<OPTION VALUE="' . 321 . '">Retir&oacute;' . "\n";
				#}


				if ($$calificacion == '99')
				{
					echo '<OPTION VALUE="' . 99 . '" SELECTED>Reprobado</OPTION>' . "\n";
				} else {
					echo '<OPTION VALUE="' . 99 . '">Reprobado</OPTION>' . "\n";
				}


				if ($$calificacion == '100')
				{
					echo '<OPTION VALUE="' . 100 . '" SELECTED>Aprobado</OPTION>' . "\n";
				} else {
					echo '<OPTION VALUE="' . 100 . '">Aprobado</OPTION>' . "\n";
				}


				if ($$calificacion == '110')
				{
					echo '<OPTION VALUE="' . 110 . '" SELECTED>Meritorio</OPTION>' . "\n";
				} else {
					echo '<OPTION VALUE="' . 110 . '">Meritorio</OPTION>' . "\n";
				}

				if ($$calificacion == '120')
				{
					echo '<OPTION VALUE="' . 120 . '" SELECTED>Excelencia</OPTION>' . "\n";
				} else {
					echo '<OPTION VALUE="' . 120 . '">Excelencia' . "\n</OPTION>";
				}

				if ($$calificacion == '212')
				{
					echo '<OPTION VALUE="' . 212 . '" SELECTED>Equivalencia</OPTION>' . "\n";
				} else {
					echo '<OPTION VALUE="' . 212 . '">Equivalencia' . "\n</OPTION>";
				}

			?>
		</SELECT>
	</TD>
</TR>
</TABLE>
<?
}
?>

<BR>

<TABLE BORDER="0" WIDTH="620" CELLSPACING="7" CELLPADDING="2">
<TR>
	<TD WIDTH="620" ALIGN="right" VALIGN="top">
		<FONT SIZE="-1" COLOR="#000099" FACE="Verdana,Arial,Geneva">
			Permite cerrar el Acta e ir a otra Secci&oacute;n -> 
		</FONT>
		<INPUT TYPE="submit" NAME="continuar_f" VALUE="&nbsp; Continuar y Finalizar &nbsp;">
</TD></TR>
<TR><TD WIDTH="620" ALIGN="right" VALIGN="top">
		<FONT SIZE="-1" COLOR="#000099" FACE="Verdana,Arial,Geneva">
			Permite agregar mas Estudiantes a esta Acta -> 
		</FONT>
		<INPUT TYPE="submit" NAME="continuar_a" VALUE="&nbsp; Continuar y Agregar Mas &nbsp;">
	</TD>
</TR>
</TABLE>

</FORM>

<?
	#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
?>

</CENTER>

</BODY>
</HTML>
