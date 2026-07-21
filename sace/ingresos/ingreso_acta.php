<?
session_start();

//include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/creditos.php");

include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_fecha.php");

//include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");
require_once dirname(__FILE__) . '/../includes/conexion.php';

include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_construyo_codacta.php");

$_codcohorte = $_GET['_codcohorte'];

if (isset($_POST['continuar']) || isset($_POST['cancelar'])) {

	$_codcohorte = isset($_POST['_codcohorte']) ? $_POST['_codcohorte'] : '';

	if (isset($_POST['cancelar'])) {
		$url = "seleccion_acta.php?_codcohorte=" . urlencode($_codcohorte);
		header("Location: $url");
		exit;
	}

	$asignatura_seleccion   = isset($_POST['asignatura_seleccion']) ? $_POST['asignatura_seleccion'] : '';
	$modalidad_seleccion    = isset($_POST['modalidad_seleccion']) ? $_POST['modalidad_seleccion'] : '';
	$multiple               = isset($_POST['multiple']) ? $_POST['multiple'] : 'NO';

	// Validaciones
	$error_asignatura = empty($asignatura_seleccion) ? 1 : 0;
	$error_modalidad  = empty($modalidad_seleccion) ? 1 : 0;

	if (!$error_asignatura && !$error_modalidad) {

		// Construir codacta
		$modalidad_seleccion_cd = ($modalidad_seleccion != 'Lineal') ? substr($modalidad_seleccion, -1) : '';
		$codacta_final = construyo_codacta($_codcohorte, $asignatura_seleccion, $modalidad_seleccion_cd);

		// Función auxiliar para verificar existencia de codacta en una tabla
		function existe_codacta($conexion, $tabla, $codacta)
		{
			$sql = "SELECT COUNT(*) AS cantidad FROM $tabla WHERE codacta = ?";
			if ($stmt = mysqli_prepare($conexion, $sql)) {
				mysqli_stmt_bind_param($stmt, "s", $codacta);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_bind_result($stmt, $cantidad);
				mysqli_stmt_fetch($stmt);
				mysqli_stmt_close($stmt);
				return $cantidad;
			}
			return 0;
		}

		// Revisar registro_actas
		$cantidad = existe_codacta($conexion, 'registro_actas', $codacta_final);
		if ($cantidad > 0) {
			$url = "ingresando_acta.php?_codcohorte=" . urlencode($_codcohorte)
				. "&_codasig=" . urlencode($asignatura_seleccion)
				. "&_codacta=" . urlencode($codacta_final);
			if ($modalidad_seleccion != 'Lineal') $url .= "&_cd=" . substr($modalidad_seleccion, -1);
			header("Location: $url");
			exit;
		}

		// Revisar multiactas
		$cantidad_multiactas = existe_codacta($conexion, 'multiactas', $codacta_final);
		if ($cantidad_multiactas > 0) {
			$url = "seleccion_multiacta.php?_codcohorte=" . urlencode($_codcohorte)
				. "&_codasig=" . urlencode($asignatura_seleccion);
			if ($modalidad_seleccion != 'Lineal') $url .= "&_cd=" . substr($modalidad_seleccion, -1);
			header("Location: $url");
			exit;
		}

		// Si no existe y es única
		if ($multiple == 'NO') {
			$url = "ingresando_acta.php?_codcohorte=" . urlencode($_codcohorte)
				. "&_codasig=" . urlencode($asignatura_seleccion);
			if ($modalidad_seleccion != 'Lineal') $url .= "&_cd=" . substr($modalidad_seleccion, -1);
			header("Location: $url");
			exit;
		}

		// Si es múltiple, revisar si la asignatura soporta
		$sqlcmd = "SELECT pensum_estudios.multiacta
                   FROM pensum_estudios
                   INNER JOIN cohortes ON cohortes.codsede = pensum_estudios.codsede 
                                      AND cohortes.codopest = pensum_estudios.codopest
                   WHERE cohortes.codcohorte = ? AND pensum_estudios.codasig = ?
                   ORDER BY pensum_estudios.periodos, pensum_estudios.codasig
                   LIMIT 1";

		if ($stmt = mysqli_prepare($conexion, $sqlcmd)) {
			mysqli_stmt_bind_param($stmt, "ss", $_codcohorte, $asignatura_seleccion);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $multiacta);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_close($stmt);

			if ($multiacta === 'si') {
				$url = "seleccion_multiacta.php?_codcohorte=" . urlencode($_codcohorte)
					. "&_codasig=" . urlencode($asignatura_seleccion);
				if ($modalidad_seleccion != 'Lineal') $url .= "&_cd=" . substr($modalidad_seleccion, -1);
				header("Location: $url");
				exit;
			} else {
				$error_multipleacta_seleccion = 1;
			}
		}
	}
}
/*if ($cancelar) {
	$url = "seleccion_acta.php?_codcohorte=" . $_codcohorte;

	header("Location: $url");
	exit;
}*/

$sqlcmd = "SELECT directorio_cippsv.modalidad, directorio_cippsv.ciudad, directorio_cippsv.edo_prov,
                  oportunidades_estudio.tipo, oportunidades_estudio.mencion_especialidad,
                  cohortes.fecha_inicio, cohortes.codsede, cohortes.codopest
           FROM directorio_cippsv, oportunidades_estudio, cohortes
           WHERE cohortes.codcohorte='" . mysqli_real_escape_string($conexion, $_codcohorte) . "'
           AND cohortes.codsede=oportunidades_estudio.codsede
           AND cohortes.codopest=oportunidades_estudio.codopest
           AND oportunidades_estudio.codsede=directorio_cippsv.codsede";

if ($query = mysqli_query($conexion, $sqlcmd)) {
	while ($registro = mysqli_fetch_object($query)) {
		$codsede = $registro->codsede;
		$codopest = $registro->codopest;
		$modalidad = $registro->modalidad;
		$ciudad = $registro->ciudad;
		$edo_prov = $registro->edo_prov;
		$tipo = $registro->tipo;
		$mencion_especialidad = $registro->mencion_especialidad;
		$fecha_inicio = $registro->fecha_inicio;
	}
	mysqli_free_result($query);
}
?>
<HTML>

<HEAD>
	<TITLE>CIPPSV Web Site | Sistema de Control de Estudios</TITLE>
	<META NAME="generator" CONTENT="BBEdit 6.5.2 - MacOS X">
</HEAD>

<script language="JavaScript">
	<!--
	function popup(windowname, url, w, h) {
		popupwin = window.open("", windowname, "toolbar=no,location=no,directories=no,status=no,menubar=no,width=" + w + ",height=" + h + ",resizable=1,scrollbars=1");
		popupwin.location = url;
	}
	//
	-->
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
							</TD>
							<TD WIDTH="470" ALIGN="center" VALIGN="middle" BGCOLOR="#000099">
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

					<A HREF="../">
						<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT>
					</A>

					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT>

					<A HREF="seleccion_de_sede.php">
						<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n de Sede</B></FONT>
					</A>

					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT>

					<A HREF="seleccion_postgrado.php?_codsede=<? echo $codsede ?>">
						<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Postgrado</B></FONT>
					</A>

					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT>

					<A HREF="seleccion_cohorte.php?_codsede=<? echo $codsede ?>&_codopest=<? echo $codopest ?>">
						<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Cohortes Existentes</B></FONT>
					</A>

					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT>

					<A HREF="seleccion_acta.php?_codcohorte=<? echo $_codcohorte ?>">
						<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Actas Existentes</B></FONT>
					</A>

					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT>

					<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Acta</B></FONT>
				</TD>
			</TR>
		</TABLE>

		<?
		#include ("$DOCUMENT_ROOT/includes/encabezado.php");
		?>

		<BR>

		<IMG SRC="/sace/imagenes/titulos_de_home/titulo_ingreso.jpg" ALT="" WIDTH="380" HEIGHT="20" BORDER="0">

		<BR><BR><BR>

		<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
			<TR>
				<TD WIDTH="710" ALIGN="left" VALIGN="top" BGCOLOR="#000099">
					<FONT FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">
						<B>Informaci&oacute;n sobre el Postgrado</B>
					</FONT>
				</TD>
			</TR>
		</TABLE>

		<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
			<TR>
				<TD WIDTH="260" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<B>Ciudad</B>
					</FONT>
				</TD>
				<TD WIDTH="250" ALIGN="left" VALIGN="top">
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
				<TD WIDTH="260" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<? echo $ciudad ?>
					</FONT>
				</TD>
				<TD WIDTH="250" ALIGN="left" VALIGN="top">
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

		<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
			<TR>
				<TD WIDTH="410" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<B>Menci&oacute;n o Especialidad</B>
					</FONT>
				</TD>
				<TD WIDTH="300" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<B>Tipo</B>
					</FONT>
				</TD>
			</TR>
			<TR>
				<TD WIDTH="410" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<? echo $mencion_especialidad ?>
					</FONT>
				</TD>
				<TD WIDTH="300" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<? echo $tipo ?>
					</FONT>
				</TD>
			</TR>
		</TABLE>

		<BR>

		<TABLE BORDER="0" WIDTH="710" CELLSPACING="2" CELLPADDING="2">
			<TR>
				<TD WIDTH="410" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<B>Fecha de Inicio</B>
					</FONT>
				</TD>
				<TD WIDTH="300" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<B>Cohorte</B>
					</FONT>
				</TD>
			</TR>
			<TR>
				<TD WIDTH="410" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<? echo fecha($fecha_inicio) ?>
					</FONT>
				</TD>
				<TD WIDTH="300" ALIGN="left" VALIGN="top">
					<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
						<? echo $_codcohorte ?>
					</FONT>
				</TD>
			</TR>
		</TABLE>

		<FORM ACTION="ingreso_acta.php" METHOD="POST">

			<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
				<B>Pensum de Estudios</B><BR>
				Seleccione una Asignatura, una Modalidad y el Tipo de Acta
			</FONT>

			<BR><BR>

			<?
			if (($error_asignatura) or ($error_modalidad) or ($error_multipleacta_seleccion)) {
				echo '<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">';
				echo '<TR><TD WIDTH="600" ALIGN="left" VALIGN="top">';

				echo '<FONT FACE="Verdana,Arial,Geneva" COLOR="#FF0000">';
				echo '<B>Se ha encontrado algun Error al tratar de procesar el Acta</B>';
				echo '</FONT><BR><BR>';


				if ($error_asignatura) {
					echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
					echo '&bull; Debe selecionar una <B>Asignatura</B> del Pensum de Estudio, favor revisar.';
					echo '</FONT><BR>';
				}

				if ($error_modalidad) {
					echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
					echo '&bull; Debe seleccionar una <B>Modalidad del Acta</B>, favor revisar.';
					echo '</FONT><BR>';
				}

				if ($error_multipleacta_seleccion) {
					echo '<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">';
					echo '&bull; La Asignatura seleccionadana no soporta <B>Actas Multiples</B>, favor revisar.';
					echo '</FONT><BR>';
				}

				echo '</TD></TR></TABLE>';
			}
			?>
			<BR>

			<TABLE BORDER="0" WIDTH="750" CELLSPACING="2" CELLPADDING="2">
				<TR>
					<TD WIDTH="450" ALIGN="left" VALIGN="top">
						<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
							<B>Asignatura</B>
						</FONT>
					</TD>
					<TD WIDTH="150" ALIGN="left" VALIGN="top">
						<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
							<B>Modalidad</B>
						</FONT>
					</TD>
					<TD WIDTH="150" ALIGN="left" VALIGN="top">
						<FONT SIZE="-1" FACE="Verdana,Arial,Geneva">
							<B>Tipo de Acta</B>
						</FONT>
					</TD>
				</TR>
				<TR>
					<TD WIDTH="450" ALIGN="left" VALIGN="top">
						<SELECT NAME="asignatura_seleccion">
							<?php
							if (!$asignatura_seleccion) {
								echo '<OPTION VALUE="" SELECTED></OPTION>';
							} else {
								echo '<OPTION VALUE=""></OPTION>';
							}

							$sqlcmd = "SELECT pensum_estudios.codasig, pensum_estudios.asignatura
						   FROM pensum_estudios, cohortes
						   WHERE cohortes.codsede=pensum_estudios.codsede 
						   AND cohortes.codopest=pensum_estudios.codopest 
						   AND pensum_estudios.status='Activa' 
						   AND cohortes.codcohorte='" . mysqli_real_escape_string($conexion, $_codcohorte) . "'
						   ORDER BY pensum_estudios.periodos, pensum_estudios.codasig";

							if ($query = mysqli_query($conexion, $sqlcmd)) {
								while ($registro = mysqli_fetch_object($query)) {
									$codasig = $registro->codasig;
									$asignatura = $registro->asignatura;

									if ($codasig == $asignatura_seleccion) {
										echo '<OPTION VALUE="' . $codasig . '" SELECTED>' . $asignatura . '</OPTION>';
									} else {
										echo '<OPTION VALUE="' . $codasig . '">' . $asignatura . '</OPTION>';
									}
								}
								mysqli_free_result($query);
							}
							?>
						</SELECT>
					</TD>
					<TD WIDTH="150" ALIGN="left" VALIGN="top">
						<SELECT NAME="modalidad_seleccion">
							<?php
							if (!$modalidad_seleccion) {
								echo '<OPTION VALUE="" SELECTED></OPTION>';
							} else {
								echo '<OPTION VALUE=""></OPTION>';
							}

							$modalidades = array(
								"Lineal" => "Lineal",
								"CD1" => "Curso D. 1",
								"CD2" => "Curso D. 2",
								"CD3" => "Curso D. 3",
								"CD4" => "Curso D. 4",
								"CD5" => "Curso D. 5"
							);

							foreach ($modalidades as $valor => $texto) {
								if ($modalidad_seleccion == $valor) {
									echo '<OPTION VALUE="' . $valor . '" SELECTED>' . $texto . '</OPTION>';
								} else {
									echo '<OPTION VALUE="' . $valor . '">' . $texto . '</OPTION>';
								}
							}
							?>
						</SELECT>
					</TD>
					<TD WIDTH="150" ALIGN="left" VALIGN="top">
						<SELECT NAME="multiple">
							<?php
							if ((!$multiple) || ($multiple == 'NO')) {
								echo '<OPTION VALUE="NO" SELECTED>Unica</OPTION>';
								echo '<OPTION VALUE="SI">Multiple</OPTION>';
							} else {
								echo '<OPTION VALUE="NO">Unica</OPTION>';
								echo '<OPTION VALUE="SI" SELECTED>Multiple</OPTION>';
							}
							?>
						</SELECT>
					</TD>
				</TR>
			</TABLE>
			<BR><BR>

			<INPUT TYPE="hidden" NAME="_codcohorte" VALUE="<? echo $_codcohorte ?>">

			<INPUT TYPE="submit" NAME="continuar" VALUE="Continuar">
			<INPUT TYPE="submit" NAME="cancelar" VALUE="Cancelar">

			<BR><BR>

		</FORM>

		<?
		#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
		?>

	</CENTER>

</BODY>

</HTML>