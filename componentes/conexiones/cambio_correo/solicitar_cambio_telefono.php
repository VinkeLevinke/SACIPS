<?php
session_start();
$servidor = "localhost";
$usuariobd = "root";
$conts = "";
$baseDato = "sacips_bd";

// Establecer la zona horaria a "America/Caracas"
date_default_timezone_set('America/Caracas');

$con = mysqli_connect($servidor, $usuariobd, $conts, $baseDato);

header('Content-Type: application/json'); // Asegúrate de enviar la cabecera correcta 

$response = array("success" => false, "message" => ""); // inicializa la respuesta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nuevoTelefono'])) {
        $nuevoTelefono = $_POST['nuevoTelefono'];
        $idUsuario = $_SESSION['id_persona']; // Asumiendo que el ID del usuario está almacenado en la sesión

        $stmt = $con->prepare("INSERT INTO solicitudes_cambio_telefono (id_persona, nuevo_telefono) VALUES (?, ?)");
        $stmt->bind_param("is", $idUsuario, $nuevoTelefono);

        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Solicitud enviada. Espera la aprobación del administrador.";
        } else {
            $response["message"] = "Error al enviar la solicitud.";
        }

        $stmt->close();
    } else {
        $response["message"] = "No se recibió el nuevo teléfono.";
    }
} else {
    $response["message"] = "Método de solicitud no permitido.";
}

echo json_encode($response); // Enviar la respuesta como JSON
$con->close();
?>
