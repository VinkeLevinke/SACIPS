<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=sacips_bd", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tipo = strtoupper($_POST['metodoPago']); // Convertir a mayúsculas
        $categoria_pago = strtoupper($_POST['categoriaPago']); // Convertir a mayúsculas

        $stmt = $conn->prepare("INSERT INTO tipo_operacion (tipo, categoria_pago) VALUES (:tipo, :categoria_pago)");
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_pago', $categoria_pago, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo $conn->lastInsertId(); // Devuelve el ID del nuevo registro
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
