<?php
session_start(); // Iniciar sesión
session_destroy(); // Eliminar todas las variables de sesión
header("Location: ../index.php"); // Redirigir a la página principal
exit();
?>
