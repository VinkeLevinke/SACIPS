<?php
// actualizarCuenta.php
header('Content-Type: application/json');

$host = 'localhost';
$db = 'sacips_bd';
$user = 'root';
$pass = '';

$connection = new mysqli($host, $user, $pass, $db);

if ($connection->connect_error) {
    die(json_encode(['message' => 'Error de conexión: ' . $connection->connect_error]));
}

$data = json_decode(file_get_contents('php://input'), true);

// Validar que los datos requeridos están presentes
if (!isset($data['id']) || !isset($data['propietario']) || !isset($data['banco']) || 
    !isset($data['numeroCuenta']) || !isset($data['tipoCuenta']) || !isset($data['cedulaRif'])) {
    echo json_encode(['message' => 'Datos incompletos']);
    exit;
}

// Escapar los valores para prevenir inyección SQL
$id = $connection->real_escape_string($data['id']);
$propietario = $connection->real_escape_string($data['propietario']);
$banco = $connection->real_escape_string($data['banco']);
$numeroCuenta = $connection->real_escape_string($data['numeroCuenta']);
$tipoCuenta = $connection->real_escape_string($data['tipoCuenta']);
$cedulaRif = $connection->real_escape_string($data['cedulaRif']);
$telefono = $connection->real_escape_string($data['telefono']);
$informacionAdicional = $connection->real_escape_string($data['informacionAdicional']);

// Query para actualizar los datos
$query = "UPDATE ipspuptyab_cuentas SET propietario_cuenta='$propietario', banco='$banco', 
          numero_cuenta='$numeroCuenta', tipo_cuenta='$tipoCuenta', cedula_rif='$cedulaRif',
          telefono_cuenta='$telefono', informacion_adicional='$informacionAdicional' 
          WHERE id='$id'";

if ($connection->query($query) === TRUE) {
    echo json_encode(['message' => 'Cuenta actualizada con éxito']);
} else {
    echo json_encode(['message' => 'Error al actualizar la cuenta: ' . $connection->error]);
}

$connection->close();
?>
