<?php
session_start();

$servidor = "localhost";
$usuariobd = "root";
$conts = "";
$baseDato = "sacips_bd";

// Establecer la zona horaria a "America/Caracas"
date_default_timezone_set('America/Caracas');

$con = mysqli_connect($servidor, $usuariobd, $conts, $baseDato);

if (!$con) {
    die("Conexión fallida: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idSolicitud = $_POST['idSolicitud'];
    $accion = $_POST['accion'];

    if ($accion === 'aprobado') {
        $stmt = $con->prepare("UPDATE personas p JOIN solicitudes_cambio_telefono sct ON p.id_Personas = sct.id_persona SET p.telefono = sct.nuevo_telefono, sct.estado = 'aprobado' WHERE sct.id = ?");
    } else {
        $stmt = $con->prepare("UPDATE solicitudes_cambio_telefono SET estado = 'rechazado' WHERE id = ?");
    }

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $con->error);
    }

    $stmt->bind_param("i", $idSolicitud);

    if ($stmt->execute()) {
        echo "Solicitud " . ($accion === 'aprobado' ? "aprobada" : "rechazada") . " exitosamente.";
    } else {
        echo "Error al manejar la solicitud.";
    }

    $stmt->close();
}

$con->close();
?>
