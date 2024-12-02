<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "sacips_bd");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener el ID del usuario desde una cookie
$id_usuario = 0;
if (isset($_COOKIE['IdUserSelect'])) {
    $id_usuario = $_COOKIE['IdUserSelect'];
}

// Comprobar si se ha enviado el formulario para guardar los cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cedula = $_POST['cedula'];
    $telefono = $_POST['telefono'];
    $estatus_laboral = $_POST['estatus_laboral'];
    $condicion_salud = $_POST['condicion_salud'];
    $genero = $_POST['genero'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $estado_civil = $_POST['estado_civil'];
    $direccion = $_POST['direccion'];

    $nombre_usuario = $_POST['nombre_usuario'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $clave = $_POST['clave']; // Nueva contraseña
    $PreguntaSeguridad1 = $_POST['PreguntaSeguridad1'];
    $respuesta1 = $_POST['respuesta1'];
    $PreguntaSeguridad2 = $_POST['PreguntaSeguridad2'];
    $respuesta2 = $_POST['respuesta2'];
    $PreguntaSeguridad3 = $_POST['PreguntaSeguridad3'];
    $respuesta3 = $_POST['respuesta3'];
    $correo = $_POST['correo'];

    // Si el usuario ha ingresado una nueva contraseña, actualizamos la clave
    if (!empty($clave)) {
        // Encriptamos la nueva contraseña
        $clave = md5($clave);  // Cambia esto por un hash más seguro si es necesario
    } else {
        // Si no se ha ingresado una nueva contraseña, dejamos la contraseña sin cambios
        $clave = $_POST['clave_vieja']; // Mantener la contraseña vieja (en MD5)
    }

    // Actualizar la tabla personas
    $sql_update_persona = "UPDATE personas 
                           SET nombre='$nombre', apellido='$apellido', cedula='$cedula', telefono='$telefono', 
                               estatus_laboral='$estatus_laboral', condicion_salud='$condicion_salud', 
                               genero='$genero', fecha_nacimiento='$fecha_nacimiento', estado_civil='$estado_civil', 
                               direccion='$direccion' 
                           WHERE id_Personas=$id_usuario";
    if (!$conexion->query($sql_update_persona)) {
        die("Error al actualizar la persona: " . $conexion->error);
    }

    // Actualizar la tabla usuarios (si la contraseña fue cambiada)
    $sql_update_usuario = "UPDATE usuarios 
                           SET nombre_usuario='$nombre_usuario', tipo_usuario='$tipo_usuario', clave='$clave', 
                               PreguntaSeguridad1='$PreguntaSeguridad1', respuesta1='$respuesta1', 
                               PreguntaSeguridad2='$PreguntaSeguridad2', respuesta2='$respuesta2', 
                               PreguntaSeguridad3='$PreguntaSeguridad3', respuesta3='$respuesta3', correo='$correo' 
                           WHERE id_persona=$id_usuario";
    if (!$conexion->query($sql_update_usuario)) {
        die("Error al actualizar el usuario: " . $conexion->error);
    }

    // Redirigir a la página de administración
    echo '<script>window.location.href = "../../Admin.php";</script>';
}

// Consultar los datos del usuario
$sql_usuario = "SELECT * FROM usuarios WHERE id_persona = $id_usuario";
$result_usuario = $conexion->query($sql_usuario);

if ($result_usuario->num_rows > 0) {
    $usuario = $result_usuario->fetch_assoc();

    // Consultar los datos de la persona asociada
    $id_persona = $usuario['id_persona'];
    $sql_persona = "SELECT * FROM personas WHERE id_Personas = $id_persona";
    $result_persona = $conexion->query($sql_persona);

    if ($result_persona->num_rows > 0) {
        $persona = $result_persona->fetch_assoc();
    } else {
        die("No se encontró la persona con id_Personas = $id_persona");
    }
} else {
    die("No se encontró el usuario con id_usuario = $id_usuario");
}

// Cerrar la conexión
$conexion->close();
?>




<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <style>
        .container {
            width: 100%;
            height: 100vh;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            flex-wrap: wrap;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            position: relative;
        }

        h2 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 10px;
            color: #555;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .container button {
            width: 49.5%;
            padding: 10px;
            margin-top: 20px;
            background-color: #5552ff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.2s;
        }

        .container button:hover {
            background-color: #3878f8;
        }

        .editarUsuarios {
            width: 49.5%
        }

        .editarUsuarios button {
            margin-top: 10px;
            width: 100%;
            background-color: #f44336;
            margin-top: 20px;
        }

        .editarUsuarios button:hover {
            background-color: #da190b;
        }

        .flex {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: 0px;
            position: relative;
            margin-bottom: 10px;
        }

        .flex label {
            width: 49.5%;
            margin-top: 0px;
        }

        .flex input,
        .flex select {
            width: 49.5%;
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <div class="container">
        <form method="POST" action="./componentes/administrador/editarUsuario.php">
            <h2>Editar Usuario</h2>

            <!-- Datos de la Persona -->
            <div class="flex">
                <label for="nombre">Nombre:</label>
                <label for="apellido">Apellido:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo $persona['nombre']; ?>" required>
                <input type="text" id="apellido" name="apellido" value="<?php echo $persona['apellido']; ?>" required>
            </div>
            <div class="flex">
                <label for="cedula">Cédula:</label>
                <label for="telefono">Teléfono:</label>
                <input type="text" id="cedula" name="cedula" value="<?php echo $persona['cedula']; ?>" required>
                <input type="text" id="telefono" name="telefono" value="<?php echo $persona['telefono']; ?>" required>
            </div>
            <div class="flex">
                <label for="estatus_laboral">Estatus Laboral:</label>
                <label for="condicion_salud">Condición de Salud:</label>
                <input type="text" id="estatus_laboral" name="estatus_laboral" value="<?php echo $persona['estatus_laboral']; ?>">
                <input type="text" id="condicion_salud" name="condicion_salud" value="<?php echo $persona['condicion_salud']; ?>">
            </div>
            <div class="flex">
                <label for="genero">Género:</label>
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="text" id="genero" name="genero" value="<?php echo $persona['genero']; ?>">
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $persona['fecha_nacimiento']; ?>">
            </div>
            <div class="flex">
                <label for="estado_civil">Estado Civil:</label>
                <label for="direccion">Dirección:</label>
                <input type="text" id="estado_civil" name="estado_civil" value="<?php echo $persona['estado_civil']; ?>">
                <input type="text" id="direccion" name="direccion" value="<?php echo $persona['direccion']; ?>">
            </div>

            <!-- Datos del Usuario -->
            <div class="flex">
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <label for="tipo_usuario">Tipo de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?php echo $usuario['nombre_usuario']; ?>" required>
                <select id="tipo_usuario" name="tipo_usuario" required>
                    <option value="1" <?php if ($usuario['tipo_usuario'] == 1) echo 'selected'; ?>>Afiliado</option>
                    <option value="2" <?php if ($usuario['tipo_usuario'] == 2) echo 'selected'; ?>>Invitado</option>
                    <option value="3" <?php if ($usuario['tipo_usuario'] == 3) echo 'selected'; ?>>Director</option>
                </select>
            </div>

            <!-- Clave (Contraseña) -->
            <div class="flex">
                <label for="clave">Contraseña:</label>
                <label for="clave1">Confirmar Contraseña:</label>
                <input type="password" id="clave" name="clave" placeholder="Nueva contraseña">
                <input type="password" id="clave1" name="clave1" placeholder="Confirmar nueva contraseña">
            </div>

            <!-- Campo oculto para enviar la contraseña vieja (en MD5) si no se cambia -->
            <input type="hidden" name="clave_vieja" value="<?php echo $usuario['clave']; ?>">

            <!-- Preguntas de seguridad -->
            <div class="flex">
                <label for="PreguntaSeguridad1">Pregunta de Seguridad 1:</label>
                <label for="respuesta1">Respuesta 1:</label>
                <input type="text" id="PreguntaSeguridad1" name="PreguntaSeguridad1" value="<?php echo $usuario['PreguntaSeguridad1']; ?>" required>
                <input type="text" id="respuesta1" name="respuesta1" value="<?php echo $usuario['respuesta1']; ?>" required>
            </div>
            <div class="flex">
                <label for="PreguntaSeguridad2">Pregunta de Seguridad 2:</label>
                <label for="respuesta2">Respuesta 2:</label>
                <input type="text" id="PreguntaSeguridad2" name="PreguntaSeguridad2" value="<?php echo $usuario['PreguntaSeguridad2']; ?>" required>
                <input type="text" id="respuesta2" name="respuesta2" value="<?php echo $usuario['respuesta2']; ?>" required>
            </div>
            <div class="flex">
                <label for="PreguntaSeguridad3">Pregunta de Seguridad 3:</label>
                <label for="respuesta3">Respuesta 3:</label>
                <input type="text" id="PreguntaSeguridad3" name="PreguntaSeguridad3" value="<?php echo $usuario['PreguntaSeguridad3']; ?>" required>
                <input type="text" id="respuesta3" name="respuesta3" value="<?php echo $usuario['respuesta3']; ?>" required>
            </div>

            <!-- Correo -->
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" value="<?php echo $usuario['correo']; ?>" required>

            <!-- Botones -->
            <div class="flex">
                <button type="submit" class="send">Guardar Cambios</button>
                <button type="button" id="admGstUsuarios" class="link-btn" >Volver</button>
            </div>
        </form>
    </div>


</body>

</html>