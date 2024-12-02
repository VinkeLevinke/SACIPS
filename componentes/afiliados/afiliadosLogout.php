<?php
session_start();
if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 1) {
    // Limpiar la sesión solo si es de afiliados
    $_SESSION = array();

    // Eliminar cookies de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Opcional: Eliminar cualquier otra cookie que tu aplicación haya podido establecer
    if (isset($_COOKIE['nombre_cookie'])) {
        setcookie('nombre_cookie', '', time() - 3600, '/'); // Cambia 'nombre_cookie' por el real
    }
    
    // Asegúrate de que no haya datos sensible en la sesión
    session_destroy();

    // Opcional: Invalidar la sesión en el servidor (por si se está usando un método de manejo avanzado)
    // Puedes implementar lógica aquí para invalidar en bases de datos si fuera necesario
}

// Redireccionar al usuario con un JavaScript que borra el almacenamiento local
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cerrar Sesión</title>
</head>
<body>
    <script>
        // Limpiar almacenamiento local y de sesión
        localStorage.clear();
        sessionStorage.clear();

        // Redirigir a la página de inicio de sesión
        window.location.href = '../../loginAfiliados.php';
    </script>
</body>
</html>
