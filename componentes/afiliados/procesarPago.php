<?php
include "../../componentes/conexiones/conexionbd.php";

header('Content-Type: application/json');  // Asegura que la respuesta sea JSON

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$responseData = ["success" => false, "message" => ""];

try {
    if ($_POST["dato"] == 'insertar_archivo') {
        // Obtener información del formulario
        $id_persona = $_POST['id_persona'];
        $estado = 'Pendiente';
        $usuario = $_POST['usuario'];

        if ($usuario == 1) {
            $usuario = 'Afiliado';
        } elseif ($usuario == 2) {
            $usuario = 'Invitado';
        }

        $tipoAporte = $_POST['tipo_aporte'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $monto = $_POST['monto'];
        $telefono = $_POST['telefono'];
        $cedula = $_POST['cedula'];
        $referencia = $_POST['referencia'];
        $fechaActual = $_POST['fecha_actual'];
        $concepto = $_POST['concepto'];
        $banco = $_POST['banco'];
        $tipoOperacion = $_POST['tipoOperacion'];

       //------------// XD // ---------------//
       $montoBolivar = $_POST['monto'];
       $montoLimpio = str_replace(['Bs ', '.', ','], ['', '', '.'], $montoBolivar);
       $montoNumerico = (float)$montoLimpio; // Esto debe ser un número puro
       

        $sql_dolar = "SELECT precio FROM dolar_diario ORDER BY fecha DESC, hora_actualizacion DESC LIMIT 1"; // Actualiza la consulta para obtener el último precio del dólar
        $dolar_result = $con->query($sql_dolar);
        $PrecioDolar = $dolar_result ? $dolar_result->fetch_assoc()['precio'] : 0; // Usa el último precio
        if ($PrecioDolar > 0) {
            $USD_ref = number_format($montoNumerico / $PrecioDolar, 2); // Asegúrate de que la referencia en dólares esté correcta
        } else {
            $responseData['message'] = "Error al obtener el precio del dólar.";
            echo json_encode($responseData);
            exit;
        }

        // Verifica si se subieron archivos
        if (!empty($_FILES['capture']['tmp_name'])) {
            $archivos = $_FILES['capture'];
            $numArchivos = count($archivos['tmp_name']);
            $allowedFormats = ['image/png', 'image/jpeg', 'image/jpg'];
            $i = 0;

            // Preparar la consulta SQL con declaraciones preparadas
            $sql = "INSERT INTO aportes_afiliados (id_persona, usuario, tipo_aporte, fechaAporte, nombre, apellido, monto, banco, telefono, cedula, referencia, concepto, estado, capture, tipo_operacion, usd_ref) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
                        "ssssssssssssssss",
                        $id_persona,
                        $usuario,
                        $tipoAporte,
                        $fechaActual,
                        $nombre,
                        $apellido,
                        $monto,
                        $banco,
                        $telefono,
                        $cedula,
                        $referencia,
                        $concepto,
                        $estado,
                        $imageBase64,
                        $tipoOperacion,
                        $USD_ref
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
                    "message" => "Pago procesado correctamente.",
                    "id_persona" => $id_persona,
                    "usuario" => $usuario,
                    "tipo_aporte" => $tipoAporte,
                    "tipo_operacion" => $tipoOperacion,
                    "fechaAporte" => $fechaActual,
                    "nombre" => $nombre,
                    "apellido" => $apellido,
                    "monto" => $monto,
                    "telefono" => $telefono,
                    "cedula" => $cedula,
                    "referencia" => $referencia,
                    "concepto" => $concepto,
                    "banco" => $banco,
                    "usd_ref" => $USD_ref
                ];

                // Enviar correo a todos los administradores desde la tabla usuario_admins
                // Obtener los correos de los administradores desde la tabla usuario_admins
                $adminEmails = [];
                $sqlAdmins = "SELECT correo FROM usuario_admins";
                $result = $con->query($sqlAdmins);

                // Verificamos si se obtuvieron correos de administradores
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Verificar si el correo es válido antes de agregarlo
                        if (filter_var($row['correo'], FILTER_VALIDATE_EMAIL)) {
                            $adminEmails[] = $row['correo'];
                        }
                    }

                    // Verificar si hay correos de administradores
                    if (count($adminEmails) > 0) {
                        // Enviar el correo a cada administrador
                        $subject = 'Nuevo aporte realizado por un afiliado';
                        $message = "Un nuevo aporte ha sido realizado por $nombre $apellido.\nDetalles del aporte:\nBanco: $banco\nMonto: $monto\nFecha: $fechaActual\nConcepto: $concepto";

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

                // Enviar correo al afiliado notificando que su aporte será aprobado en breve
                // Obtener el correo del afiliado
                $sqlAfiliado = "SELECT correo FROM usuarios WHERE id_persona = ?";
                $stmtAfiliado = $con->prepare($sqlAfiliado);
                $stmtAfiliado->bind_param('i', $id_persona);
                $stmtAfiliado->execute();
                $resultAfiliado = $stmtAfiliado->get_result();

                if ($resultAfiliado->num_rows > 0) {
                    $rowAfiliado = $resultAfiliado->fetch_assoc();
                    $afiliadoEmail = $rowAfiliado['correo'];

                    $subjectAfiliado = 'Aporte recibido, en breve será aprobado';
                    $messageAfiliado = "Hola $nombre,\n\nTu aporte de $monto ha sido recibido y será aprobado o declinado en breve. Te notificaremos el estado de tu aporte.\n\nGracias por tu colaboración.\n\nSaludos,\nSacips.";

                    $dataAfiliado = array(
                        'to' => $afiliadoEmail,
                        'subject' => $subjectAfiliado,
                        'text' => $messageAfiliado
                    );

                    $optionsAfiliado = array(
                        'http' => array(
                            'header' => "Content-Type: application/json\r\n",
                            'method' => 'POST',
                            'content' => json_encode($dataAfiliado)
                        )
                    );

                    $contextAfiliado = stream_context_create($optionsAfiliado);
                    $resultAfiliado = file_get_contents('https://test-nodejs-fcya.onrender.com/send-email', false, $contextAfiliado);

                    if ($resultAfiliado === FALSE) {
                        throw new Exception('Error al enviar el correo al afiliado.');
                    }
                }
            } else {
                throw new Exception("Error en la base de datos: " . $stmt->error);
            }
        }
    }
} catch (Exception $e) {
    $responseData['message'] = $e->getMessage();
}

echo json_encode($responseData);

?>