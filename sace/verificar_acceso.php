<?
session_start();
if (!isset($_SESSION['autenticado'])) {
    header("Location: login.php"); // Redirigir a login si no está autenticado
    exit();
}
?>