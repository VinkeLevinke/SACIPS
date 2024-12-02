<?php


session_start();
if (!isset($_SESSION["id_usuarios"])) {
    include_once "../../componentes/conexiones/permisosAdmin.php";
}


$servername = "localhost";
$username = "root";
$password = "";
$bdname = "sacips_bd";
$conexion = new mysqli($servername, $username, $password, $bdname);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $monto = $_POST['monto'];
    $rConcept = $_POST['rConcept'];
    $beneficiario = $_POST['beneficiario'];
    $fechaPagoEgreso = $_POST['fechaPagoEgreso'];
    $tipoOperacion = $_POST['tipoOperacion'];
    $banco = $_POST['banco'];
    $nro_cuenta = $_POST['nro_cuenta'];
    $tipo_egreso = $_POST['tipoEgreso'];

    $stmt = $conexion->prepare("INSERT INTO registrar_egreso (monto, rConcept, beneficiario, fechaPagoEgreso, tipoOperacion, tipo_egreso, banco, nro_cuenta) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $monto, $rConcept, $beneficiario, $fechaPagoEgreso, $tipoOperacion, $tipo_egreso, $banco, $nro_cuenta);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Egreso registrado correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar el egreso.']);
    }

    $stmt->close();
}

$conexion->close();
?>
