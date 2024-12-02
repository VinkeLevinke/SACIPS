<?php
include("../../componentes/conexiones/conexionbd.php");

session_start();
session_destroy();

// Eliminar todas las cookies
if (isset($_SERVER["HTTP_COOKIE"])) {
    $cookies = explode('; ', $_SERVER["HTTP_COOKIE"]);
    foreach ($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = $parts[0];
        setcookie($name, '', time() - 3600, '/'); // establece la cookie a expirar
    }
}

$con->close();

// Redirecciona a la página de inicio y ejecuta JavaScript
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deslogueo</title>
    <script type="text/javascript">
        // Limpiar el LocalStorage y SessionStorage
        localStorage.clear();
        sessionStorage.clear();

        // Redirecciona después de un breve retardo para asegurar que se limpien los datos
        setTimeout(function() {
            window.location.href = './index.php';
        }, 100); // 100 ms de retardo
    </script>
</head>
<body>
</body>
</html>
