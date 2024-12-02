<?php
// cierre_sesion.php

session_start();

// Eliminar todas las variables de sesión
$_SESSION = [];

// Verificar si hay cookies de sesión que queremos eliminar
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Eliminar las cookies relacionadas con el usuario
if (isset($_COOKIE['User_ID'])) {
    setcookie('User_ID', '', time() - 3600, '/'); // Eliminar User_ID
}

// Redirigir al usuario a la página de inicio
header("Location: ../../index.php");
exit();
