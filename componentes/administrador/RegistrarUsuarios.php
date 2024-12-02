<?php
include('../conexiones/conexionbd.php');

// Datos enviados por POST
$Nombre = $_POST['nombre'];
$Apellido = $_POST['apellido'];
$Cedula = $_POST['cedula'];
$Telefono = $_POST['telefono'];
$Nombre_Usuario = $_POST['usuario'];
$Password = $_POST['clave'];
$Correo = $_POST['correo'];
$Tipo_Usuario = $_POST['tipo_usuario'];
$Pregunta1 = $_POST['pregunta1'];
$Respuesta1 = $_POST['respuesta1'];
$Pregunta2 = $_POST['pregunta2'];
$Respuesta2 = $_POST['respuesta2'];
$Pregunta3 = $_POST['pregunta3'];
$Respuesta3 = $_POST['respuesta3'];
$EstatusLaboral = $_POST['estatusLaboral'];
$CondicionSalud = $_POST['condicionSalud'];
$EstadoCivil = $_POST['estadoCivil'];
$Genero = $_POST['genero'];
$Direccion = $_POST['direccion'];
$FechaNacimiento = $_POST['fecha_nacimiento'];

$mensaje = '';
$fechaActual = date('Y-m-d H:i:s');
$Password_Hash = md5($Password);

// Inicia transacción
$con->begin_transaction();

try {
    // Insertar datos en la tabla personas
    $sql_personas = "INSERT INTO personas (nombre, apellido, cedula, telefono, estatus_laboral, condicion_salud, estado_civil, genero, direccion, fecha_nacimiento) 
                     VALUES ('$Nombre', '$Apellido', '$Cedula', '$Telefono', '$EstatusLaboral', '$CondicionSalud', '$EstadoCivil', '$Genero', '$Direccion', '$FechaNacimiento')";
    
    if ($con->query($sql_personas) === TRUE) {
        $id_persona = $con->insert_id;

        // Insertar datos en la tabla usuarios
        $sql_usuarios = "INSERT INTO usuarios (id_persona, nombre_usuario, tipo_usuario, clave, PreguntaSeguridad1, PreguntaSeguridad2, PreguntaSeguridad3, respuesta1, respuesta2, respuesta3, correo, fechaIngreso) 
                         VALUES ('$id_persona', '$Nombre_Usuario', '$Tipo_Usuario', '$Password_Hash', '$Pregunta1', '$Pregunta2', '$Pregunta3', '$Respuesta1', '$Respuesta2', '$Respuesta3', '$Correo', '$fechaActual')";

        if ($con->query($sql_usuarios) === TRUE) {
            // Confirmar transacción
            $con->commit();
            $mensaje = 'Su registro se realizó con éxito';
            echo json_encode(['success' => true, 'message' => $mensaje]); // Respuesta exitosa en formato JSON
        } else {
            throw new Exception("Error al crear la cuenta del usuario.");
        }
    } else {
        throw new Exception("Error al crear el registro de la persona.");
    }
} catch (Exception $e) {
    // Deshacer cambios en caso de error
    $con->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]); // Respuesta de error en formato JSON
}
?>
