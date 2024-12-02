<?php

$servidor = "localhost";
$usuariobd = "root";
$conts = "";
$baseDato = "sacips_bd";

// Establecer la zona horaria a "America/Caracas"
date_default_timezone_set('America/Caracas');

$con = mysqli_connect($servidor, $usuariobd, $conts, $baseDato);


session_start();

header('Content-Type: application/json');

$response = array('success' => false, 'message' => 'Ocurrió un error al procesar la solicitud.');

if (!empty($_POST['claveAnterior']) && !empty($_POST['nuevaClave'])) {
    $claveAnterior = md5($_POST['claveAnterior']); // Encriptar la clave anterior con md5
    $nuevaClave = md5($_POST['nuevaClave']); // Encriptar la nueva clave con md5
    $idPersona = $_SESSION['id_persona']; // Asumimos que tienes el ID de la persona en la sesión

    // Utilizar declaraciones preparadas para evitar inyección SQL
    $sql = "SELECT clave FROM usuarios WHERE id_persona = ?";
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("i", $idPersona);
        $stmt->execute();
        $stmt->bind_result($claveActual);
        $stmt->fetch();
        $stmt->close();

        // Verificar la clave anterior
        if ($claveActual === $claveAnterior) {
            $sqlUpdate = "UPDATE usuarios SET clave = ? WHERE id_persona = ?";
            if ($stmtUpdate = $con->prepare($sqlUpdate)) {
                $stmtUpdate->bind_param("si", $nuevaClave, $idPersona);
                if ($stmtUpdate->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Clave actualizada correctamente.';
                } else {
                    $response['message'] = 'Error al actualizar la clave.';
                }
                $stmtUpdate->close();
            } else {
                $response['message'] = 'Error al preparar la declaración para la actualización.';
            }
        } else {
            $response['message'] = 'La clave anterior no es correcta.';
        }
    } else {
        $response['message'] = 'Error al preparar la declaración de selección.';
    }
} else {
    $response['message'] = 'Datos incompletos.';
}

echo json_encode($response);
?>

