<?php
include '../../componentes/conexiones/conexionbd.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claveActual = md5($_POST['claveActual']);
    $claveNueva = md5($_POST['claveNueva']);

    // Obtener la clave actual del usuario logueado en la base de datos
    $idPersona = $_SESSION['id_persona'];
    $query = "SELECT clave FROM usuarios WHERE id_persona = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $idPersona);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    if ($usuario && $usuario['clave'] === $claveActual) {
        // Actualizar la clave en la base de datos con la nueva clave en MD5
        $query = "UPDATE usuarios SET clave = ? WHERE id_persona = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("si", $claveNueva, $idPersona);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Clave actualizada exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la clave.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'La clave actual es incorrecta.']);
    }
}
