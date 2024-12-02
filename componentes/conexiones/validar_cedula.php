<?php
require_once "../../componentes/conexiones/conInfo_ipspuptyab.php"; // Conexión a la base de datos

if (isset($_POST['cedula']) && !isset($_POST['metodo'])) {
    $cedula = $_POST['cedula'];
    $response = "false";

    // Consulta para verificar la cédula y obtener las preguntas de seguridad
    $stmt = $connection->prepare("SELECT p.id_Personas, u.PreguntaSeguridad1, u.PreguntaSeguridad2, u.PreguntaSeguridad3, u.respuesta1, u.respuesta2, u.respuesta3 FROM personas p JOIN usuarios u ON p.id_Personas = u.id_persona WHERE p.cedula = ?");
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response = "true;" . implode(";", array($row['PreguntaSeguridad1'], $row['PreguntaSeguridad2'], $row['PreguntaSeguridad3'], $row['respuesta1'], $row['respuesta2'], $row['respuesta3']));
    }
    $stmt->close();
    echo $response; // Enviar respuesta
    return;
}

// Validar preguntas de seguridad y actualizar clave o nombre de usuario
if (isset($_POST['metodo']) && isset($_POST['preguntaIndex']) && isset($_POST['respuesta']) && isset($_POST['cedula'])) {
    $metodo = $_POST['metodo'];
    $preguntaIndex = $_POST['preguntaIndex'];
    $respuesta = $_POST['respuesta'];
    $cedula = $_POST['cedula'];

    $stmt = $connection->prepare("SELECT u.respuesta1, u.respuesta2, u.respuesta3 FROM personas p JOIN usuarios u ON p.id_Personas = u.id_persona WHERE p.cedula = ?");
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $respuestasCorrectas = array($row['respuesta1'], $row['respuesta2'], $row['respuesta3']);

        if (strtolower($respuesta) === strtolower($respuestasCorrectas[$preguntaIndex])) {
            if ($metodo === "clave" && isset($_POST['nueva_clave'])) {
                // Gestion de actualización de clave
                $nueva_clave = md5($_POST['nueva_clave']);
                $stmt = $connection->prepare("UPDATE usuarios SET clave = ? WHERE id_persona = (SELECT id_Personas FROM personas WHERE cedula = ?)");
                $stmt->bind_param("ss", $nueva_clave, $cedula);
                if ($stmt->execute()) {
                    echo "clave_actualizada"; // Mensaje de éxito
                }
                 
            } elseif ($metodo === "nombre_usuario" && isset($_POST['nuevo_usuario'])) {
                $nuevo_usuario = $_POST['nuevo_usuario'];
                $stmt = $connection->prepare("UPDATE usuarios SET nombre_usuario = ? WHERE id_persona = (SELECT id_Personas FROM personas WHERE cedula = ?)");
                $stmt->bind_param("ss", $nuevo_usuario, $cedula);
                if ($stmt->execute()) {
                    echo "nombre_usuario_actualizado"; // Mensaje de éxito
                }
            }
            $stmt->close();
        }
    }
}