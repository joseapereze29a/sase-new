<?
session_start();
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '123456789';
$DATABASE_NAME = 'cippsv_ce';

// Conectar con mysql_pconnect()
$con = mysql_pconnect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS);

if (!$con) {
    exit('Failed to connect to MySQL: ' . mysql_error());
}

// Seleccionar la base de datos
mysql_select_db($DATABASE_NAME, $con);

//include ($_SERVER["DOCUMENT_ROOT"]."/sace/includes/conexion.php");
if (!isset($_POST['username'], $_POST['password'])) {
    // Could not get the data that should have been sent
    exit('Please fill both the username and password fields!');
}
$username = mysql_real_escape_string($_POST['username']); // Protección contra SQL Injection
$query = "SELECT id, pass, usuario, rol FROM usuarios_sace WHERE user = '$username'";
$result = mysql_query($query, $con);

if (!$result) {
    exit('Error en la consulta: ' . mysql_error());
}

if (mysql_num_rows($result) > 0) {
    $row = mysql_fetch_assoc($result);
    $id = $row['id'];
    $pass = $row['pass'];
    $rol = $row['rol'];
    $usuario = $row['usuario'];

    if ($_POST['password'] === $pass) {
        session_regenerate_id();
        $_SESSION['account_loggedin'] = TRUE;
        $_SESSION['account_rol'] = $rol;
        $_SESSION['account_id'] = $id;
        header('Location: home.php');
    } else {
        echo 'Incorrect username and/or password!';
    }
} else {
    echo 'Incorrect username and/or password!';
}

?>