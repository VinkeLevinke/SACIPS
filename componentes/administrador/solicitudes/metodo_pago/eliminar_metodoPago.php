<?php
$conn = new mysqli("localhost", "root", "", "sacips_bd");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_tipoOperacion'];
    
    $stmt = $conn->prepare("DELETE FROM tipo_operacion WHERE id_tipoOperacion = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo 'success'; // Devuelve 'success' si la eliminación fue exitosa
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
