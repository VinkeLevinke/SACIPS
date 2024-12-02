<?php
date_default_timezone_set('America/Caracas');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $tipoRif = $_POST['tipoRif'];
    $rifNumero = $_POST['rif'];
    $rifCompleto = $tipoRif . '-' . $rifNumero; // Combinar tipo y número del RIF

    // Conectar a la base de datos utilizando PDO
    $dsn = 'mysql:host=localhost;dbname=sacips_bd;charset=utf8';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO aportes_patronales (
                  fechaEmision, procedencia, rif, tipo_operacion, banco, referencia, 
                    nro_cuenta, tipo_usuario, monto, concepto, estado, vigilante_comentario, comprobante
                ) VALUES (
                    :fechaEmision, :procedencia, :rif, :tipo_operacion, :banco, :referencia, 
                    :nro_cuenta, :tipo_usuario, :monto, :concepto, :estado, :vigilante_comentario, :comprobante
                )";

        $stmt = $pdo->prepare($sql);
        
        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] == UPLOAD_ERR_OK) {
            $fileData = file_get_contents($_FILES['comprobante']['tmp_name']);
            $comprobante = base64_encode($fileData);
        } else {
            $comprobante = null;
        }

        $stmt->execute([
           
            ':fechaEmision' => $_POST['fechaPagoIngreso'],
            ':procedencia' => $_POST['razonSocial'],
            ':rif' => $rifCompleto,
            ':tipo_operacion' => $_POST['tipoOperacion'],
            ':banco' => $_POST['banco'],
            ':referencia' => $_POST['referencia_transaccion'],
            ':nro_cuenta' => $_POST['nro_cuenta'],
            ':tipo_usuario' => 'Administrador', 
            ':monto' => $_POST['monto'],
            ':concepto' => $_POST['concepto'],
            ':estado' => 'Cargado',
            ':vigilante_comentario' => 'N/A', 
            ':comprobante' => $comprobante
        ]);

        echo "<script>alert('Aporte agregado correctamente.');  location.assign('../../Admin.php');</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
