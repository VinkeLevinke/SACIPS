<?php
include "../../componentes/conexiones/conexionbd.php";

header('Content-Type: application/json');  // Asegura que la respuesta sea JSON

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$responseData = ["success" => false, "message" => ""];

try {
    if ($_POST["dato"] == 'insertar_archivo') {
        $id_persona = $_POST['id_persona'];
        $estado = 'Pendiente';
        $usuario = $_POST['usuario'];

        if ($usuario == 1) {
            $usuario = 'Afiliado';
        } elseif ($usuario == 2) {
            $usuario = 'Invitado';
        }

        $monto = $_POST['monto'];
        $telefono = $_POST['telefono'];
        $cedula = $_POST['cedula'];
        $referencia = $_POST['referencia'];
        $fecha = $_POST['fecha_actual'];
        $concepto = $_POST['concepto'];
        $banco = $_POST['banco'];
        $tipoOperacion = $_POST['tipoOperacion']; // Añadido tipo de operación

        $benefactor = $_POST['benefactor'];
        $beneficiario = $_POST['beneficiario'];


        // Obtener nombre y apellido desde la tabla personas
        $sqlPersona = "SELECT nombre, apellido, correo FROM personas INNER JOIN usuarios ON personas.id_Personas = usuarios.id_persona WHERE personas.id_Personas = ?";
        $stmtPersona = $con->prepare($sqlPersona);
        $stmtPersona->bind_param('i', $id_persona);
        $stmtPersona->execute();
        $resultPersona = $stmtPersona->get_result();

        if ($resultPersona->num_rows > 0) {
            $rowPersona = $resultPersona->fetch_assoc();
            $nombre = $rowPersona['nombre'];
            $apellido = $rowPersona['apellido'];
            $correoUsuario = $rowPersona['correo']; // Correo del usuario
        } else {
            throw new Exception('No se encontró la persona con ID: ' . $id_persona);
        }

        if (!empty($_FILES['capture']['tmp_name'])) {
            $archivos = $_FILES['capture'];
            $numArchivos = count($archivos['tmp_name']);
            $allowedFormats = ['image/png', 'image/jpeg', 'image/jpg'];
            $i = 0;

            // Preparar la consulta SQL con declaraciones preparadas
            $sql = "INSERT INTO aportes_donaciones (id_persona, tipo_usuario, benefactor, origen, montoRecibido, concepto, fechaAporte, referencia, estado, capture, telefono, cedula, tipo_operacion, beneficiario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $con->prepare($sql);

            if ($stmt === false) {
                throw new Exception("Error en la preparación de la declaración: " . $con->error);
            }

            $successful = true; // Variable para controlar el estado de éxito de la inserción

            while ($i < $numArchivos) {
                if (!empty($archivos['tmp_name'][$i])) {
                    $contenidoImagen = file_get_contents($archivos['tmp_name'][$i]);
                    $imageBase64 = base64_encode($contenidoImagen);

                    $stmt->bind_param(
                        "ssssssssssssss",
                        $id_persona,
                        $usuario,
                        $benefactor,
                        $banco,
                        $monto,
                        $concepto,
                        $fecha,
                        $referencia,
                        $estado,
                        $imageBase64,
                        $telefono,
                        $cedula,
                        $tipoOperacion,// Incluye tipo de operación en la consulta
                        $beneficiario
                    );

                    if (!$stmt->execute()) {
                        $successful = false; // Marca el fallo si la declaración no se ejecuta
                        break; // Sal de la búsqueda de archivos si ocurre un error
                    }
                }
                $i++;
            }

            if ($successful) {
                $responseData = [
                    "success" => true,
                    "estado" => $estado,
                    "id_persona" => $id_persona,
                    "banco" => $banco,
                    "monto" => $monto,
                    "concepto" => $concepto,
                    "fecha" => $fecha,
                    "referencia" => $referencia,
                    "telefono" => $telefono,
                    "cedula" => $cedula,
                    "usuario" => $usuario,
                    "tipoOperacion" => $tipoOperacion, // Añadir al JSON
                    "nombre" => $nombre, // Añadir nombre al JSON
                    "apellido" => $apellido, // Añadir apellido al JSON
                    "formData" => $_POST
                ];

                // Enviar correo a los administradores
                $adminEmails = [];
                $sqlAdmins = "SELECT correo FROM usuario_admins";
                $result = $con->query($sqlAdmins);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        if (filter_var($row['correo'], FILTER_VALIDATE_EMAIL)) {
                            $adminEmails[] = $row['correo'];
                        }
                    }

                    if (count($adminEmails) > 0) {
                        $subject = 'Nuevo aporte realizado por ' . $nombre . ' ' . $apellido;
                        $message = "Un nuevo aporte ha sido realizado por $nombre $apellido.\nDetalles del aporte:\nBanco: $banco\nMonto: $monto\nFecha: $fecha\nConcepto: $concepto";

                        foreach ($adminEmails as $adminEmail) {
                            $data = array(
                                'to' => $adminEmail,
                                'subject' => $subject,
                                'text' => $message
                            );

                            $options = array(
                                'http' => array(
                                    'header' => "Content-Type: application/json\r\n",
                                    'method' => 'POST',
                                    'content' => json_encode($data)
                                )
                            );

                            $context = stream_context_create($options);
                            $result = file_get_contents('https://test-nodejs-fcya.onrender.com/send-email', false, $context);

                            if ($result === FALSE) {
                                throw new Exception('Error al enviar el correo a los administradores.');
                            }
                        }
                    } else {
                        throw new Exception('No se encontraron correos válidos de administradores.');
                    }
                } else {
                    throw new Exception('No se encontraron administradores para enviar el correo.');
                }

                // Enviar correo al usuario sobre el estado del aporte
                if (!empty($correoUsuario)) {
                    $estadoAporte = 'Pendiente';  // El estado inicial del aporte

                    // Llamada al endpoint de Node.js para enviar el correo de notificación
                    $data = array(
                        'correo' => $correoUsuario,
                        'nombre' => $nombre . ' ' . $apellido,
                        'monto' => $monto,
                        'estado' => $estadoAporte
                    );

                    $options = array(
                        'http' => array(
                            'header' => "Content-Type: application/json\r\n",
                            'method' => 'POST',
                            'content' => json_encode($data)
                        )
                    );

                    $context = stream_context_create($options);
                    $result = file_get_contents('https://test-nodejs-fcya.onrender.com/send-approval-email', false, $context);  // Asegúrate de que la URL sea correcta

                    if ($result === FALSE) {
                        throw new Exception('Error al enviar el correo de notificación al usuario.');
                    }
                }

            } else {
                throw new Exception("Error en la base de datos: " . $stmt->error);
            }
        } else {
            // Handle the case when there are no files uploaded
            $responseData = [
                "estado" => $estado,
                "id_persona" => $id_persona,
                "banco" => $banco,
                "monto" => $monto,
                "concepto" => $concepto,
                "fecha" => $fecha,
                "referencia" => $referencia,
                "telefono" => $telefono,
                "cedula" => $cedula,
                "usuario" => $usuario,
                "tipoOperacion" => $tipoOperacion, // Añadir al JSON
                "nombre" => $nombre, // Añadir nombre al JSON
                "apellido" => $apellido, // Añadir apellido al JSON
                "formData" => $_POST
            ];
        }
    }
} catch (Exception $e) {
    $responseData['message'] = $e->getMessage();
}

echo json_encode($responseData, JSON_UNESCAPED_UNICODE);
?>
