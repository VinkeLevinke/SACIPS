<?php
$dsn = 'mysql:host=localhost;dbname=sacips_bd';
$usuario = 'root'; 
$pass = ''; 

try {
    $conex = new PDO($dsn, $usuario, $pass);
    $conex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Conexion fallida: ' . $e->getMessage();
    exit();
}

$idReciboElement = $_POST['id_recibo'];
$comentario = $_POST['comentario'];
$tipoRecibo = $_POST['tipo_recibo'];

// Actualizar comentario en la base de datos
if ($tipoRecibo == 'aporte_afiliado') {
    $sql = "UPDATE aportes_afiliados SET vigilante_comentario = :comentario WHERE id_aporte = :id_recibo"; 
} elseif ($tipoRecibo == 'aporte_donacion') {
    $sql = "UPDATE aportes_donaciones SET vigilante_comentario = :comentario WHERE id_AportesDona = :id_recibo"; 
} elseif ($tipoRecibo == 'aporte_patronal') {
    $sql = "UPDATE aportes_patronales SET vigilante_comentario = :comentario WHERE id_AportesPatron = :id_recibo"; 
} elseif ($tipoRecibo == 'egreso') {
    $sql = "UPDATE registrar_egreso SET vigilante_comentario = :comentario WHERE id = :id_recibo"; 
}

$stmt = $conex->prepare($sql);
$stmt->bindParam(':comentario', $comentario);
$stmt->bindParam(':id_recibo', $idReciboElement);
$stmt->execute();

// Enviar notificación a los administradores
$sqlAdmins = "SELECT correo FROM usuario_admins";
$stmtAdmins = $conex->query($sqlAdmins);

// Recuperamos todos los correos de los administradores
$adminEmails = [];
while ($row = $stmtAdmins->fetch(PDO::FETCH_ASSOC)) {
    // Verificar que el correo sea válido antes de agregarlo
    if (filter_var($row['correo'], FILTER_VALIDATE_EMAIL)) {
        $adminEmails[] = $row['correo'];
    }
}

// Si hay administradores, enviarles un correo
if (count($adminEmails) > 0) {
    $subject = 'Nuevo comentario agregado a un pago';
    // Incluir el comentario en el mensaje
    $message = "Un nuevo comentario ha sido agregado a un movimiento. El comentario es el siguiente:\n\n"
                . "\"$comentario\"\n\n"
                . "Por favor, revisa la plataforma para más detalles.";

    // Enviar correo a todos los administradores
    foreach ($adminEmails as $adminEmail) {
        // Hacer la solicitud POST al servidor Node.js para enviar el correo
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
        $result = file_get_contents('https://test-nodejs-fcya.onrender.com/send-email', false, $context); // Ajusta la URL a tu servidor Node.js

        if ($result === FALSE) {
            // Manejar error si no se puede enviar el correo
            error_log("Error al enviar correo al administrador: " . $adminEmail);
        }
    }
} else {
    error_log("No se encontraron correos de administradores válidos.");
}

echo "Comentario guardado correctamente";
?>
