<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "sacips_bd");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener la ID del usuario desde una variable, por ejemplo, una cookie
$id_Personas = $_COOKIE['IdUserSelect'];


// Preparar la consulta DELETE
if($stmt = $conn->prepare("DELETE FROM `personas` WHERE id_Personas = ?")) {
    $stmt->bind_param("i", $id_Personas);
    if($stmt->execute()) {
        echo '<script>window.location.href = "../../Admin.php";</script>';
    } else {
        echo "Error al eliminar el usuario: " . $stmt->error;
    }
    $stmt->close();
} else {
    // Manejar error en la preparación de la consulta
    echo "Error preparando la consulta: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>