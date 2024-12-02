<?php
date_default_timezone_set('America/Caracas');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idPersona = $_POST['idPersona'];
    $tipoAporte = $_POST['tipoAporte'];
    $nombreDonante = trim($_POST['nombreDonante']); // Trim para eliminar espacios
    $banco = $_POST['banco'];
    $tipoRif = $_POST['tipoRif'];
    $rifNumero = $_POST['rif'];
    $rifCompleto = $tipoRif . '-' . $rifNumero;
    $beneficiario = $_POST['beneficiario'];
    $tipoOperacion = $_POST['tipoOperacion'];
    $monto = $_POST['monto'];
    $concepto = $_POST['concepto'];
    $estado = 'Cargado por el administrador';
    $fechaAporte = date('Y-m-d H:i:s');
    $comprobante = null;

    // Validación para asegurarte que no estén vacíos los campos requeridos
    if (empty($nombreDonante) || empty($beneficiario) || empty($tipoOperacion) || empty($monto) || empty($concepto)) {
        echo "<script>alert('Por favor, completa todos los campos requeridos.');</script>";
        exit; // Termina la ejecución si hay campos vacíos
    }

    $dsn = 'mysql:host=localhost;dbname=sacips_bd;charset=utf8';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] == UPLOAD_ERR_OK) {
            $fileData = file_get_contents($_FILES['comprobante']['tmp_name']);
            $comprobante = base64_encode($fileData);
        }

        $sql = "INSERT INTO aportes_donaciones (
            id_persona, tipo_usuario, benefactor, beneficiario, tipo_operacion, origen,  montoRecibido,
            concepto, fechaAporte, estado, capture
        ) VALUES (
            :id_persona, :tipo_usuario, :benefactor, :beneficiario, :tipo_operacion, :banco, :montoRecibido,
            :concepto, :fechaAporte, :estado, :capture
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_persona' => $idPersona,
            ':tipo_usuario' => 'Administrador',
            ':benefactor' => $nombreDonante,
            ':beneficiario' => $beneficiario,
            ':tipo_operacion' => $tipoOperacion,
            ':banco' => $banco,
            ':montoRecibido' => $monto,
            ':concepto' => $concepto,
            ':fechaAporte' => $fechaAporte,
            ':estado' => $estado,
            ':capture' => $comprobante
        ]);

        echo "<script>alert('Aporte registrado correctamente.'); location.assign('../../Admin.php');</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
