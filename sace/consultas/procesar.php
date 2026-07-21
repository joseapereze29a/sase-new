<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cedula = trim($_POST['cedula']);

    // Guardamos en sesión (puede ser solo la cédula o los datos completos si prefieres)
    $_SESSION['cedula'] = $cedula;

    // Redirigimos al archivo que muestra los datos
    header("Location: consulta_por_cedula4.php?_orden=periodos");
    exit;
} else {
    // Acceso indebido por GET directo
    header("Location: ingreso_de_cedula.php");
    exit;
}
