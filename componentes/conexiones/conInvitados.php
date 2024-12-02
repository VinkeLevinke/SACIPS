<?php
include '../../componentes/conexiones/conexionbd.php';
session_start();

if (isset($_POST['nombreusuario']) && isset($_POST['clave'])) {
    $nombreusuario = $_POST['nombreusuario'];
    $clave = $_POST['clave'];

    // Encriptar la clave usando MD5
    $clave_hash = md5($clave);

    // Consulta SQL preparada para verificar las credenciales
    $sql = "SELECT p.nombre, p.apellido, p.cedula, p.telefono, u.tipo_usuario, u.nombre_usuario, u.correo, u.id_usuario, p.id_Personas
            FROM usuarios u
            INNER JOIN personas p ON u.id_persona = p.id_Personas
            WHERE u.clave = ? AND u.nombre_usuario = ?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $clave_hash, $nombreusuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['apellido'] = $row['apellido'];
        $_SESSION['cedula'] = $row['cedula'];
        $_SESSION['telefono'] = $row['telefono'];
        $_SESSION['correo'] = $row['correo'];
        $_SESSION['tipo_usuario'] = $row['tipo_usuario'];
        $_SESSION['id_persona'] = $row['id_Personas']; 
        $_SESSION['id_usuario'] = $row['id_usuario']; 
        $_SESSION['nombre_usuario'] = $row['nombre_usuario']; 
        header("Location: ../../invitados.php");
        exit();
    } else {
        $_SESSION['error'] = 'Autenticación Incorrecta';
        header("Location: ../../loginInvitados.php");
        exit();
    }
} else {
    $_SESSION['error'] = 'Por favor, ingresa cédula y clave.';
    header("Location: ../../loginInvitados.php");
    exit();
}


?>
