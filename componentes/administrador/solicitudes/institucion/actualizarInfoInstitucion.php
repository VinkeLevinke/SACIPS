<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sacips_bd";

try {
    // Conectar a la base de datos
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtener los datos del formulario
        $razon_social = $_POST['razon_social'] ?? null;
        $siglas = $_POST['siglas'] ?? null;
        $direccion = $_POST['direccion'] ?? null;
        $rif_institucion = $_POST['rif_institucion'] ?? null;
        $telefono = $_POST['telefono'] ?? null;
        $correo = $_POST['correo'] ?? null;

        // Manejar la imagen de la firma digital
        $firma_digital = null;
        if (isset($_FILES['firma_digital']) && $_FILES['firma_digital']['error'] == 0) {
            $firma_digital = file_get_contents($_FILES['firma_digital']['tmp_name']);
            $firma_digital = base64_encode($firma_digital); // Convertir a base64 para almacenar en longtext
        }

        // Recuperar los datos actuales desde la base de datos para comparación
        $stmt = $conn->prepare("SELECT * FROM ipspuptyab_info WHERE id=1");
        $stmt->execute();
        $currentData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Variables para almacenar las actualizaciones
        $updates = [];
        $params = [];

        // Compara los datos enviados con los actuales y agrega a la lista de actualizaciones
        if ($razon_social !== $currentData['razon_social']) {
            $updates[] = "razon_social=:razon_social";
            $params[':razon_social'] = $razon_social;
        }
        
        if ($siglas !== $currentData['siglas']) {
            $updates[] = "siglas=:siglas";
            $params[':siglas'] = $siglas;
        }

        if ($direccion !== $currentData['direccion']) {
            $updates[] = "direccion=:direccion";
            $params[':direccion'] = $direccion;
        }

        if ($rif_institucion !== $currentData['rif_institucion']) {
            $updates[] = "rif_institucion=:rif_institucion";
            $params[':rif_institucion'] = $rif_institucion;
        }

        if ($telefono !== $currentData['telefono']) {
            $updates[] = "telefono=:telefono";
            $params[':telefono'] = $telefono;
        }

        if ($correo !== $currentData['correo']) {
            $updates[] = "correo=:correo";
            $params[':correo'] = $correo;
        }

        if ($firma_digital !== $currentData['firma_digital']) {
            $updates[] = "firma_digital=:firma_digital";
            $params[':firma_digital'] = $firma_digital;
        }

        // Si hay actualizaciones, construir y ejecutar la consulta
        if (count($updates) > 0) {
            // Construir la consulta
            $sql = "UPDATE ipspuptyab_info SET " . implode(", ", $updates) . " WHERE id=1";

            // Preparar y ejecutar la consulta
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            echo "Información actualizada correctamente.";
        } else {
            echo "No se realizaron cambios.";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
$conn = null;
?>
