<?php
// Incluir el archivo de conexión a la base de datos
include('../conexiones/conexionbd.php');

// Obtener datos del formulario
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$cedula = $_POST['cedula'];
$telefono = $_POST['telefono'];
$correo = $_POST['correo'];
$pregunta1 = $_POST['pregunta1'];
$respuesta1 = $_POST['respuesta1'];
$pregunta2 = $_POST['pregunta2'];
$respuesta2 = $_POST['respuesta2'];
$pregunta3 = $_POST['pregunta3'];
$respuesta3 = $_POST['respuesta3'];
$clave = ('Admin123'); // Asegúrate de que la clave esté encriptada de manera segura
$tipo = md5('Admin');

// Inserción en la tabla `usuario_admins`
$sql = "INSERT INTO usuario_admins (usuario, nombre, apellido, cedula, clave, correo, telefono, permisos, preguntaSeguridad1, respuestaSeguridad1, preguntaSeguridad2, respuestaSeguridad2, preguntaSeguridad3, respuestaSeguridad3)
        VALUES ('$tipo', '$nombre', '$apellido', '$cedula', '$clave', '$correo', '$telefono', '0000000000000', '$pregunta1', '$respuesta1', '$pregunta2', '$respuesta2', '$pregunta3', '$respuesta3')";

if ($con->query($sql) === TRUE) {
    echo "<p>Registro exitoso.</p>";
    header("Location: ../../Admin.php");
} else {
    echo "<p>Error al registrar: " . $con->error . "</p>"; // Muestra el error específico
}
$con->close();
?>