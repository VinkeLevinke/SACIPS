<?php
session_start();

// Verificamos si el usuario es del tipo que deseamos (tipo_usuario === 2)
if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 2) {
    // Limpiamos todas las variables de sesión
    $_SESSION = array();

    // Si se están utilizando cookies para la sesión, las eliminamos
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();

        // Configuramos la cookie para que expire en el pasado
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destruimos la sesión
    session_destroy();

    // Eliminamos manualmente cualquier otra cookie relacionada que hayas usado
    if (isset($_COOKIE['nombre_de_tu_cookie'])) {
        setcookie('nombre_de_tu_cookie', '', time() - 3600, '/');
    }

    // Aquí puedes agregar más cookies que desees eliminar

    // Añadir una función para borrar todas las cookies
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach ($cookies as $cookie) {
            $cookie = explode('=', $cookie);
            setcookie(trim($cookie[0]), '', time() - 3600, '/');
        }
    }
}

// Redireccionamos a la página de login
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cerrar Sesión</title>
</head>
<body>
    <script>
        // Limpiamos el almacenamiento local y de sesión del cliente
        localStorage.clear();
        sessionStorage.clear();

        // Redireccionamos al usuario a la página de login
        window.location.href = '../../loginInvitados.php';
    </script>
</body>
</html>
