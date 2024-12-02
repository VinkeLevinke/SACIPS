<?php
include('../conexiones/conexionbd.php');

// Verificar si se han enviado datos a través del método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = mysqli_real_escape_string($con, $_POST['cedula']);
    $correo = mysqli_real_escape_string($con, $_POST['correo']);
    $pregunta1 = mysqli_real_escape_string($con, $_POST['pregunta1']);
    $respuesta1 = mysqli_real_escape_string($con, $_POST['respuesta1']);
    $pregunta2 = mysqli_real_escape_string($con, $_POST['pregunta2']);
    $respuesta2 = mysqli_real_escape_string($con, $_POST['respuesta2']);
    $pregunta3 = mysqli_real_escape_string($con, $_POST['pregunta3']);
    $respuesta3 = mysqli_real_escape_string($con, $_POST['respuesta3']);

    $errores = [];

    // Verificar si la cédula ya está registrada
    $sql_cedula = "SELECT * FROM personas WHERE cedula = '$cedula'";
    $result_cedula = $con->query($sql_cedula);
    if ($result_cedula->num_rows > 0) {
        $errores[] = 'La cédula ya está registrada.';
    }

    // Verificar si el correo electrónico ya está registrado
    $sql_correo = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $result_correo = $con->query($sql_correo);
    if ($result_correo->num_rows > 0) {
        $errores[] = 'El correo electrónico ya está registrado.';
    }

    // Verificar si las preguntas de seguridad y respuestas ya existen
    $sql_preguntas = "SELECT * FROM usuarios WHERE 
        (PreguntaSeguridad1 = '$pregunta1' AND respuesta1 = '$respuesta1') OR 
        (PreguntaSeguridad2 = '$pregunta2' AND respuesta2 = '$respuesta2') OR 
        (PreguntaSeguridad3 = '$pregunta3' AND respuesta3 = '$respuesta3')";

    $result_preguntas = $con->query($sql_preguntas);
    if ($result_preguntas->num_rows > 0) {
        $errores[] = 'Las preguntas de seguridad y sus respuestas ya existen.';
    }

    // En caso de no haber errores, indica que la validación fue exitosa sin mensajes adicionales
    if (empty($errores)) {
        http_response_code(204); // No content
    } else {
        // Si no hay errores, aquí podrías añadir la lógica para registrar al usuario.
        http_response_code(204); // No content
    }
} else {
    // Si no es una solicitud POST, envía un mensaje de error
    header('Content-Type: text/plain');
    echo 'Método no permitido';
}
?>