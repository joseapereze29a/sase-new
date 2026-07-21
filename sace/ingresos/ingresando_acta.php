<?
session_start();
include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_fecha.php");

include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/conexion.php");

include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/arreglo_meses_dias.php");

include($_SERVER["DOCUMENT_ROOT"] . "/sace/includes/funcion_datos_profesor.php");
$_codcohorte = $_GET['_codcohorte'];
$_codasig = $_GET['_codasig'];
$_cd = $_GET['_cd'];
#if ($continuar_a) echo "continuar_a";

if (($continuar_f) or ($continuar_a)) {

	// VALIDACIÓN DE CÉDULAS (solo números)
	for ($i = 1; $i < 21; $i++) {
		$cedula = 'cedula_' . $i;
		if ($$cedula) {
			if (!preg_match("/^[0-9]+$/", $$cedula)) {
				$error_1 = 1;
				$error1[] = $i;
			}
		}
	}

	// VERIFICA SI HAY AL MENOS UNA CÉDULA INGRESADA
	$si_hay_info = 0;
	for ($i = 1; $i < 21; $i++) {
		$cedula = 'cedula_' . $i;
		if ($$cedula) $si_hay_info = 1;
	}
	if (!$si_hay_info) $error_8 = 1;

	// VERIFICA NOTAS Y CALIFICACIONES
	if ((!$error_1) && (!$error_8)) {
		for ($i = 1; $i < 21; $i++) {
			$cedula = 'cedula_' . $i;
			$nota = 'nota_' . $i;
			$calificacion = 'calificacion_' . $i;

			if ($$cedula) {
				if ((!$$nota) && (!$$calificacion)) {
					$error_2 = 1;
					$error2[] = $i;
				}
			}
		}
	}

	// ERROR: Nota y calificación al mismo tiempo
	if ((!$error_1) && (!$error_2) && (!$error_8)) {
		for ($i = 1; $i < 21; $i++) {
			$cedula = 'cedula_' . $i;
			$nota = 'nota_' . $i;
			$calificacion = 'calificacion_' . $i;

			if ($$cedula) {
				if (($$nota) && ($$calificacion)) {
					$error_6 = 1;
					$error6[] = $i;
				}
			}
		}
	}

	// ERROR: Nota fuera de rango
	if ((!$error_1) && (!$error_2) && (!$error_6) && (!$error_8)) {
		for ($i = 1; $i < 21; $i++) {
			$cedula = 'cedula_' . $i;
			$nota = 'nota_' . $i;

			if (($$cedula) && ($$nota)) {
				if (($$nota < 1) or ($$nota > 20)) {
					$error_7 = 1;
					$error7[] = $i;
				}
			}
		}
	}

	// ERROR: CÉDULAS REPETIDAS
	if ((!$error_1) && (!$error_2) && (!$error_6) && (!$error_7) && (!$error_8)) {
		for ($i = 1; $i < 21; $i++) {
			$iguales = 0;
			$cedula = 'cedula_' . $i;

			for ($j = 1; $j < 21; $j++) {
				$cedula2 = 'cedula_' . $j;
				if (($$cedula) && ($$cedula2)) {
					if ($$cedula == $$cedula2) $iguales++;
					if ($iguales > 1) {
						$error_3 = 1;
						$error3[] = $i;
					}
				}
			}
		}
	}

	// ERROR: Fecha incompleta si no hay notas existentes
	if ((!$error_1) && (!$error_2) && (!$error_3) && (!$error_6) && (!$error_7) && (!$error_8)) {
		$sqlcmd = "SELECT count(*) as cantidad FROM record_notas WHERE codacta='$_codacta'";
		$result = mysqli_query($conexion, $sqlcmd);
		$row = mysqli_fetch_assoc($result);
		$cantidad_error_5 = $row['cantidad'];

		if ($cantidad_error_5 < 1) {
			if (($_dia && (!$_mes || !$_ano)) || ($_mes && (!$_dia || !$_ano)) || ($_ano && (!$_dia || !$_mes))) {
				$error_5 = 1;
			}
		}
	}

	// ERROR 4: Verifica si las cédulas ya existen
	if ((!$error_1) && (!$error_2) && (!$error_3) && (!$error_6) && (!$error_7) && (!$error_5) && (!$error_8) && $_codacta) {
		for ($i = 1; $i < 21; $i++) {
			$cedula = 'cedula_' . $i;
			if ($$cedula) {
				$sqlcmd = "SELECT count(*) as cantidad FROM record_notas WHERE codacta='$_codacta' AND cedula='" . mysqli_real_escape_string($conexion, $$cedula) . "'";
				$result = mysqli_query($conexion, $sqlcmd);
				$row = mysqli_fetch_assoc($result);
				$cantidad = $row['cantidad'];
				if ($cantidad > 0) {
					$error_4 = 1;
					$error4[] = $i;
				}
			}
		}
	}

	// SI NO HAY ERRORES, PROCESA INSERT DE ACTAS Y NOTAS
	if ((!$error_1) && (!$error_2) && (!$error_3) && (!$error_6) && (!$error_7) && (!$error_4) && (!$error_5) && (!$error_8)) {

		// GENERAR CODACTA FINAL
		list($_codcohorte_1, $_codcohorte_2) = explode("-", $_codcohorte);
		list($_codasig_1, $_codasig_2) = explode("-", $_codasig);
		$_codasig_2_last_dos_digitos = substr($_codasig_2, -2);
		$_codacta_final = $_codcohorte_1 . $_codcohorte_2 . "-" . $_codasig_2_last_dos_digitos;

		if ($_cd) {
			$num_cd = substr($_cd, -1);
			$_codacta_final .= "CD" . $num_cd;
		}

		$_fecha_aprobacion = (($_ano && $_mes && $_dia) ? $_ano . '-' . $_mes . '-' . $_dia : NULL);
		$_ci_profesor_db = ($_ci_profesor == "" ? NULL : $_ci_profesor);

		// INSERTAR O ACTUALIZAR ACTA
		$stmt = mysqli_prepare($conexion, "INSERT INTO registro_actas (codcohorte, codasig, codacta, cedula_profesor, fecha_aprobacion, fecha_creacion, operador_creacion, host_creacion) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
		mysqli_stmt_bind_param($stmt, "ssissss", $_codcohorte, $_codasig, $_codacta_final, $_ci_profesor_db, $_fecha_aprobacion, $PHP_AUTH_USER, $REMOTE_ADDR);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		// INSERTAR NOTAS
		for ($i = 1; $i < 21; $i++) {
			$cedula = 'cedula_' . $i;
			$nota = 'nota_' . $i;
			$calificacion = 'calificacion_' . $i;

			if ($$cedula) {
				$meto_calif = (($$nota >= 1 && $$nota <= 20) ? $$nota : $$calificacion);
				if ($meto_calif) {
					$stmt = mysqli_prepare($conexion, "INSERT INTO record_notas (codacta, cedula, calificacion, fecha_creacion, operador_creacion, host_creacion) VALUES (?, ?, ?, NOW(), ?, ?)");
					mysqli_stmt_bind_param($stmt, "sisss", $_codacta_final, $$cedula, $meto_calif, $PHP_AUTH_USER, $REMOTE_ADDR);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_close($stmt);
				}
			}
		}

		// REDIRECCIONAR SEGÚN BOTÓN
		if ($continuar_f) {
			$url = "ingreso_finalizado.php?_codcohorte=" . $_codcohorte;
			if ($_cd) $url .= "&_cd=" . substr($_cd, -1);
			header("Location: $url");
			exit;
		}
		if ($continuar_a) {
			$url = "ingresando_acta.php?_codcohorte=" . $_codcohorte . "&_codasig=" . $_codasig . "&_codacta=" . $_codacta_final . "&_ok=1";
			if ($_cd) $url .= "&_cd=" . substr($_cd, -1);
			header("Location: $url");
			exit;
		}
	}
}




/*
			<INPUT TYPE="submit" NAME="continuar_f" VALUE="&nbsp; Continuar y Finalizar &nbsp;">
	
			<INPUT TYPE="submit" NAME="continuar_a" VALUE="&nbsp; Continuar y Agregar Mas &nbsp;">
	*/




$sqlcmd = "SELECT 
                d.modalidad,
                d.ciudad,
                d.edo_prov,
                o.tipo,
                o.mencion_especialidad,
                c.fecha_inicio,
                c.codsede,
                c.codopest
           FROM cohortes c
           INNER JOIN oportunidades_estudio o ON c.codsede = o.codsede AND c.codopest = o.codopest
           INNER JOIN directorio_cippsv d ON o.codsede = d.codsede
           WHERE c.codcohorte = ? 
           LIMIT 1";

if ($stmt = mysqli_prepare($conexion, $sqlcmd)) {
	mysqli_stmt_bind_param($stmt, "s", $_codcohorte);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result(
		$stmt,
		$modalidad,
		$ciudad,
		$edo_prov,
		$tipo,
		$mencion_especialidad,
		$fecha_inicio,
		$codsede,
		$codopest
	);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);
} else {
	echo "<p style='color:red;'>Error en la consulta: " . mysqli_error($conexion) . "</p>";
}

//include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");
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

					<A HREF="ingreso_acta.php?_codcohorte=<? echo $_codcohorte ?>">
						<FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Selecci&oacute;n del Acta</B></FONT>
					</A>

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
						<?php echo $ciudad ?>
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

		<FORM ACTION="ingresando_acta.php" METHOD="post" id="formActa">

			<TABLE BORDER="0" WIDTH="600" CELLSPACING="2" CELLPADDING="2">
				<TR>
					<TD WIDTH="300" ALIGN="left" VALIGN="top">
						<FONT SIZE="-1" FACE="Verdana,Arial,Geneva"><B>Fecha de Aprobaci&oacute;n del Acta</B></FONT>
					</TD>
					<TD WIDTH="300" ALIGN="left" VALIGN="top">
						<FONT SIZE="-1" FACE="Verdana,Arial,Geneva"><B>C.I. del Profesor</B></FONT>
					</TD>
				</TR>
				<TR>
					<TD WIDTH="300" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF" id="tdFecha">
						<SELECT NAME="_dia" id="dia">
							<OPTION VALUE="">D&iacute;a
								<? for ($i = 1; $i < 32; $i++): ?>
							<OPTION VALUE="<?= $i ?>" <?= ($_dia == $i ? 'SELECTED' : '') ?>><?= $i ?></OPTION>
						<? endfor; ?>
						</SELECT>
						<SELECT NAME="_mes" id="mes">
							<OPTION VALUE="">Mes
								<? for ($i = 1; $i < 13; $i++): ?>
							<OPTION VALUE="<?= $i ?>" <?= ($_mes == $i ? 'SELECTED' : '') ?>><?= $meses[$i] ?></OPTION>
						<? endfor; ?>
						</SELECT>
						<SELECT NAME="_ano" id="ano">
							<OPTION VALUE="">A&ntilde;o
								<? for ($i = 1975; $i <= date('Y'); $i++): ?>
							<OPTION VALUE="<?= $i ?>" <?= ($_ano == $i ? 'SELECTED' : '') ?>><?= $i ?></OPTION>
						<? endfor; ?>
						</SELECT>
					</TD>
					<TD WIDTH="300" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
						<INPUT TYPE="text" NAME="_ci_profesor" VALUE="<?= $_ci_profesor ?>" SIZE="10" MAXLENGTH="9">
						&nbsp;
						<A HREF="javascript:popup('_blank','buscar_profesor.php',650,400)">
							<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#3300FF">Buscar Profesor</FONT>
						</A>
					</TD>
				</TR>
			</TABLE>

			<BR>

			<TABLE BORDER="0" WIDTH="450" CELLSPACING="2" CELLPADDING="2" BGCOLOR="#000099">
				<TR>
					<TD WIDTH="100" ALIGN="center" VALIGN="top">
						<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF"><B>N&uacute;m.</B></FONT>
					</TD>
					<TD WIDTH="150" ALIGN="left" VALIGN="top">
						<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF">&nbsp;&nbsp;<B>C&eacute;dula</B></FONT>
					</TD>
					<TD WIDTH="100" ALIGN="center" VALIGN="top">
						<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF"><B>Nota</B></FONT>
					</TD>
					<TD WIDTH="150" ALIGN="center" VALIGN="top">
						<FONT SIZE="-1" FACE="Verdana,Arial,Geneva" COLOR="#FFFFFF"><B>Calificaci&oacute;n</B></FONT>
					</TD>
				</TR>
			</TABLE>

			<BR>

			<? for ($i = 1; $i <= 20; $i++):
				$cedula_var = 'cedula_' . $i;
				$nota_var = 'nota_' . $i;
				$cal_var = 'calificacion_' . $i;
			?>
				<TABLE BORDER="0" WIDTH="450" CELLSPACING="2" CELLPADDING="2">
					<TR>
						<TD WIDTH="100" ALIGN="right" VALIGN="top">
							<FONT SIZE="-1" FACE="Verdana,Arial,Geneva"><?= $i ?></FONT>
						</TD>
						<TD WIDTH="150" ALIGN="left" VALIGN="top" BGCOLOR="#FFFFFF">
							<INPUT TYPE="text" NAME="cedula_<?= $i ?>" VALUE="<?= ${$cedula_var} ?>" SIZE="11" MAXLENGTH="9" class="inputCedula">
						</TD>
						<TD WIDTH="100" ALIGN="center" VALIGN="top" BGCOLOR="#FFFFFF">
							<INPUT TYPE="text" NAME="nota_<?= $i ?>" VALUE="<?= ${$nota_var} ?>" SIZE="4" MAXLENGTH="2" class="inputNota" data-index="<?= $i ?>">
						</TD>
						<TD WIDTH="150" ALIGN="center" VALIGN="top" BGCOLOR="#FFFFFF">
							<SELECT NAME="calificacion_<?= $i ?>" class="selectCalif" data-index="<?= $i ?>">
								<OPTION VALUE=""> - -
								<OPTION VALUE="404" <?= (${$cal_var} == '404' ? 'SELECTED' : '') ?>>No Curs&oacute;</OPTION>
								<OPTION VALUE="99" <?= (${$cal_var} == '99' ? 'SELECTED' : '') ?>>Reprobado</OPTION>
								<OPTION VALUE="100" <?= (${$cal_var} == '100' ? 'SELECTED' : '') ?>>Aprobado</OPTION>
								<OPTION VALUE="110" <?= (${$cal_var} == '110' ? 'SELECTED' : '') ?>>Meritorio</OPTION>
								<OPTION VALUE="120" <?= (${$cal_var} == '120' ? 'SELECTED' : '') ?>>Excelencia</OPTION>
								<OPTION VALUE="212" <?= (${$cal_var} == '212' ? 'SELECTED' : '') ?>>Equivalencia</OPTION>
							</SELECT>
						</TD>
					</TR>
				</TABLE>
			<? endfor; ?>

			<BR>

			<TABLE BORDER="0" WIDTH="620" CELLSPACING="7" CELLPADDING="2">
				<TR>
					<TD WIDTH="620" ALIGN="right" VALIGN="top">
						<FONT SIZE="-1" COLOR="#000099" FACE="Verdana,Arial,Geneva">
							Permite cerrar el Acta e ir a otra Secci&oacute;n ->
						</FONT>
						<INPUT TYPE="submit" NAME="continuar_f" VALUE="&nbsp; Continuar y Finalizar &nbsp;">
					</TD>
				</TR>
				<TR>
					<TD WIDTH="620" ALIGN="right" VALIGN="top">
						<FONT SIZE="-1" COLOR="#000099" FACE="Verdana,Arial,Geneva">
							Permite agregar mas Estudiantes a esta Acta ->
						</FONT>
						<INPUT TYPE="submit" NAME="continuar_a" VALUE="&nbsp; Continuar y Agregar Mas &nbsp;">
					</TD>
				</TR>
			</TABLE>

		</FORM>

		<script>
			// Validación dinámica y auto-calificación
			document.querySelectorAll('.inputNota').forEach(function(input) {
				input.addEventListener('input', function() {
					const val = parseInt(this.value);
					const index = this.dataset.index;
					const select = document.querySelector('select[name="calificacion_' + index + '"]');
					if (val >= 1 && val <= 20) {
						select.value = 100; // Aprobado por defecto si hay nota
					} else {
						select.value = '';
					}
					if (isNaN(val) || val < 1 || val > 20) {
						this.style.backgroundColor = '#FFCCCC';
					} else {
						this.style.backgroundColor = '#FFFFFF';
					}
				});
			});
		</script>
		<?
		#include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php");
		?>

	</CENTER>

</BODY>

</HTML>