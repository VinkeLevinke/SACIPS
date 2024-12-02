<?php
// Configuración de la base de datos
$host = 'localhost'; 
$db = 'sacips_bd'; 
$user = 'root'; 
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $e->getMessage()]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tituloCuenta = $_POST['tituloCuenta'] ?? '';
    $metodoPago = $_POST['metodoPago'] ?? '';
    $banco = $_POST['banco'] ?? '';
    $numeroCuenta = $_POST['numeroCuenta'] ?? '';
    $tipoCuenta = $_POST['tipoCuenta'] ?? '';
    $cedulaRif = $_POST['cedula_rif'] ?? '';
    $telefonoCuenta = $_POST['telefonoCuenta'] ?? '';
    $infoAdicional = $_POST['infoAdicional'] ?? '';

    // Validar que los campos no estén vacíos
    if (empty($tituloCuenta) || empty($metodoPago) || empty($banco) || empty($numeroCuenta) || empty($tipoCuenta) || empty($cedulaRif) || empty($telefonoCuenta)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, complete todos los campos obligatorios.']);
        exit();
    }

    // Consulta SQL
    $sql = "INSERT INTO ipspuptyab_cuentas (propietario_cuenta, formato_cuenta, banco, numero_cuenta, tipo_cuenta, cedula_rif, telefono_cuenta, informacion_adicional) VALUES (:tituloCuenta, :metodoPago, :banco, :numeroCuenta, :tipoCuenta, :cedulaRif, :telefonoCuenta, :infoAdicional)";
    
    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':tituloCuenta', $tituloCuenta);
    $stmt->bindParam(':metodoPago', $metodoPago);
    $stmt->bindParam(':banco', $banco);
    $stmt->bindParam(':numeroCuenta', $numeroCuenta);
    $stmt->bindParam(':tipoCuenta', $tipoCuenta);
    $stmt->bindParam(':cedulaRif', $cedulaRif);
    $stmt->bindParam(':telefonoCuenta', $telefonoCuenta);
    $stmt->bindParam(':infoAdicional', $infoAdicional);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cuenta registrada exitosamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al registrar la cuenta.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}

$pdo = null;
?>
