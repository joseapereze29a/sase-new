<?php
session_start();
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/creditos.php");
include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");

// Inicializar variables
$mensaje = '';
$codsede = isset($_GET['codsede']) ? $_GET['codsede'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codsede_post = $_POST['codsede'];
    $codopest = trim($_POST['codopest']);
    $tipo = $_POST['tipo'];
    $mencion_especialidad = trim($_POST['mencion_especialidad']);
    $periodos = trim($_POST['periodos']);
    $actividad_especial_final = trim($_POST['actividad_especial_final']);
    $creditos = trim($_POST['creditos']);
    $titulo_a_otorgar = trim($_POST['titulo_a_otorgar']);

    // Validaciones básicas
    if (!is_numeric($creditos)) {
        $mensaje = "El campo 'Créditos' debe ser numérico.";
    } elseif ($codsede_post == '') {
        $mensaje = "Debe seleccionar una sede.";
    } elseif ($codopest == '') {
        $mensaje = "Debe ingresar el código del postgrado.";
    } else {
        $sedes_a_insertar = array();

        if ($codsede_post == 'todas') {
            $sql_sedes = "SELECT codsede FROM directorio_cippsv";
            $query_sedes = mysqli_query($conexion, $sql_sedes);
            while ($sede = mysqli_fetch_object($query_sedes)) {
                $sedes_a_insertar[] = $sede->codsede;
            }
        } else {
            $sedes_a_insertar[] = $codsede_post;
        }

        $errores = 0;
        foreach ($sedes_a_insertar as $cod) {
            $sql_insert = "INSERT INTO oportunidades_estudio (codsede, codopest, tipo, mencion_especialidad, periodos, actividad_especial_final, creditos, titulo_a_otorgar)
                           VALUES ('$cod', '$codopest', '$tipo', '$mencion_especialidad', '$periodos', '$actividad_especial_final', '$creditos', '$titulo_a_otorgar')";
            if (!mysqli_query($conexion, $sql_insert)) {
                $errores++;
            }
        }

        if ($errores == 0) {
            $mensaje = "Registro insertado correctamente.";
        } else {
            $mensaje = "Ocurrieron errores al insertar uno o más registros.";
        }
    }
}
?>

<HTML>
<HEAD>
    <TITLE>CIPPSV Web Site | Ingreso de Oportunidad de Estudio</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000" LINK="#0000FF" ALINK="#0000FF" VLINK="#0000FF">
<CENTER>

<?php include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/encabezado.php"); ?>

<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="1">
<TR>
    <TD WIDTH="100%" ALIGN="left" VALIGN="top">
        <A HREF="../"><FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Home</B></FONT></A>
        <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000">:</FONT> 
        <FONT SIZE="-2" FACE="Verdana,Arial,Geneva" COLOR="#000000"><B>Ingresar Oportunidad de Estudio</B></FONT>
    </TD>
</TR>
</TABLE>

<BR><BR>
<?php if ($mensaje != '') { ?>
    <FONT FACE="Verdana,Arial,Geneva" COLOR="#FF0000"><B><?php echo $mensaje; ?></B></FONT><BR><BR>
<?php } ?>

<FORM METHOD="post" ACTION="ingresar_oportunidad_estudio.php?codsede=<?php echo htmlspecialchars($codsede); ?>">
<TABLE BORDER="0" WIDTH="600" CELLSPACING="1" CELLPADDING="3" BGCOLOR="#000099">
<TR>
    <TD COLSPAN="2" BGCOLOR="#FFFFFF">
        <FONT FACE="Verdana,Arial,Geneva" COLOR="#000099"><B>Formulario de Oportunidad de Estudio</B></FONT>
    </TD>
</TR>

<TR BGCOLOR="#FFFFFF">
    <TD><FONT SIZE="-1" FACE="Verdana,Arial,Geneva">Sede:</FONT></TD>
    <TD>
        <SELECT NAME="codsede">
        <?php
        if ($codsede == 'todas') {
            echo "<OPTION VALUE=\"todas\" SELECTED>TODAS LAS SEDES</OPTION>";
        }

        $sql = "SELECT codsede, ciudad FROM directorio_cippsv ORDER BY ciudad";
        $query = mysqli_query($conexion, $sql);
        while ($row = mysqli_fetch_object($query)) {
            $selected = ($codsede == $row->codsede) ? "SELECTED" : "";
            echo "<OPTION VALUE=\"$row->codsede\" $selected>$row->ciudad</OPTION>";
        }
        ?>
        </SELECT>
    </TD>
</TR>

<TR BGCOLOR="#FFFFFF">
    <TD><FONT SIZE="-1" FACE="Verdana,Arial,Geneva">Código del Postgrado:</FONT></TD>
    <TD><INPUT TYPE="text" NAME="codopest" SIZE="20"></TD>
</TR>

<TR BGCOLOR="#FFFFFF">
    <TD><FONT SIZE="-1" FACE="Verdana,Arial,Geneva">Tipo:</FONT></TD>
    <TD>
        <SELECT NAME="tipo">
            <OPTION VALUE="Maestria">Maestría</OPTION>
            <OPTION VALUE="Doctorado">Doctorado</OPTION>
            <OPTION VALUE="Diplomado">Diplomado</OPTION>
            <OPTION VALUE="Especialización">Especialización</OPTION>
        </SELECT>
    </TD>
</TR>

<TR BGCOLOR="#FFFFFF">
    <TD><FONT SIZE="-1" FACE="Verdana,Arial,Geneva">Mención o Especialidad:</FONT></TD>
    <TD><INPUT TYPE="text" NAME="mencion_especialidad" SIZE="50"></TD>
</TR>

<TR BGCOLOR="#FFFFFF">
    <TD><FONT SIZE="-1" FACE="Verdana,Arial,Geneva">Periodos:</FONT></TD>
    <TD><INPUT TYPE="text" NAME="periodos" SIZE="20"></TD>
</TR>

<TR BGCOLOR="#FFFFFF">
    <TD><FONT SIZE="-1" FACE="Verdana,Arial,Geneva">Actividad Especial Final:</FONT></TD>
    <TD><INPUT TYPE="text" NAME="actividad_especial_final" SIZE="40"></TD>
</TR>

<TR BGCOLOR="#FFFFFF">
    <TD><FONT SIZE="-1" FACE="Verdana,Arial,Geneva">Créditos:</FONT></TD>
    <TD><INPUT TYPE="text" NAME="creditos" SIZE="10"></TD>
</TR>

<TR BGCOLOR="#FFFFFF">
    <TD><FONT SIZE="-1" FACE="Verdana,Arial,Geneva">Título a Otorgar:</FONT></TD>
    <TD><INPUT TYPE="text" NAME="titulo_a_otorgar" SIZE="50"></TD>
</TR>

<TR BGCOLOR="#FFFFFF">
    <TD COLSPAN="2" ALIGN="center">
        <INPUT TYPE="submit" VALUE="Guardar">
    </TD>
</TR>
</TABLE>
</FORM>

<BR><BR>

<?php #include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/pie_de_pagina.php"); ?>

</CENTER>
</BODY>
</HTML>