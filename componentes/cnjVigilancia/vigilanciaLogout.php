<?php
include '../../componentes/conexiones/conexionbd.php';
session_start();

// Destruir la sesión
session_destroy();

// Cerrar la conexión a la base de datos
$con->close();

// Borrar todas las cookies de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirigir al usuario a la página de inicio de sesión
header("Location: ../../loginvigilancia.php");
exit();
?>
