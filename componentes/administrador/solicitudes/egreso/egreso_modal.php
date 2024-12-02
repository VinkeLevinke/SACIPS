<?php
$conn = new mysqli("localhost", "root", "", "sacips_bd");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipoEgreso = $_POST['tipoEgreso'];
    $codeEgreso = $_POST['codeEgreso'];

    $stmt = $conn->prepare("INSERT INTO tipo_egreso (codigo_egreso, tipo) VALUES (?, ?)");
    $stmt->bind_param("ss", $codeEgreso, $tipoEgreso); // Elimina la coma extra entre los tipos de datos

    if ($stmt->execute()) {
        echo $stmt->insert_id; // Devuelve el ID del nuevo registro
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>
