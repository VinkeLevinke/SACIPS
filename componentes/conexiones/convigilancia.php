<?php
include '../../componentes/conexiones/conexionbd.php';
session_start();

if (isset($_POST['nombre_usuario']) && isset($_POST['clave'])) {
    $nombre_usuario = $_POST['nombre_usuario'];
    $clave = $_POST['clave'];

    // Hash de la clave ingresada (MD5)
    $clave_hash = md5($clave);

    // Consulta SQL para verificar las credenciales
    // Utilizando sentencias preparadas para prevenir inyección SQL.
    $stmt = $con->prepare("SELECT p.nombre, p.apellido, p.cedula, p.telefono, u.tipo_usuario, u.nombre_usuario, u.correo, p.id_Personas
                            FROM usuarios u
                            INNER JOIN personas p ON u.id_persona = p.id_Personas
                            WHERE u.nombre_usuario = ? AND u.clave = ?");
    
    $stmt->bind_param("ss", $nombre_usuario, $clave_hash); // Bind variables to the prepared statement
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Inicio de sesión exitoso
        $row = $result->fetch_assoc();
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['apellido'] = $row['apellido'];
        $_SESSION['tipo_usuario'] = $row['tipo_usuario'];
        $_SESSION['id_persona'] = $row['id_Personas']; 
        $_SESSION['cedula'] = $row['cedula']; 
        $_SESSION['correo'] = $row['correo'];
        $_SESSION['telefono'] = $row['telefono'];
        $_SESSION['nombre_usuario'] = $row['nombre_usuario'];

        // Redirigir según el tipo de usuario
        if ($_SESSION['tipo_usuario'] == 4) {
            header("Location: ../../cnjVigilancia.php");
        } else {
            $_SESSION['error'] = 'ERROR: No está registrado con este rol';
            header("Location: ../../loginvigilancia.php");
        }
    } else {
        $_SESSION['error'] = 'Autenticación Incorrecta';
        header("Location: ../../loginvigilancia.php"); // Redirige de vuelta a la página de login
    }

    $stmt->close(); // Cerrar el statement
} else {
    $_SESSION['error'] = 'Por favor, ingresa nombre de usuario y clave.';
    header("Location: ../../loginvigilancia.php"); // Redirige de vuelta a la página de login
}

$con->close();
?>
