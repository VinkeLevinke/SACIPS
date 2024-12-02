<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sacips_bd";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Inicializar las variables
        $nombre_sistema = $_POST['nombreSistemaUpdate'] ?? null;
        $titulo_sistema = $_POST['tituloSistemaUpdate'] ?? null;
        $subtitulo_sistema = $_POST['subtituloSistemaUpdate'] ?? null;

        // Procesar logo
        $logo_sistema = null;
        if (isset($_FILES['logoSistemaInput']) && $_FILES['logoSistemaInput']['error'] == 0) {
            $logo_sistema = file_get_contents($_FILES['logoSistemaInput']['tmp_name']);
            $logo_sistema = base64_encode($logo_sistema); // Convertir a base64 para almacenar en longtext
        }

        // Construir la consulta SQL dinámicamente
        $sql = "UPDATE ipspuptyab_sistema SET ";
        $params = [];

        if ($nombre_sistema !== null) {
            $sql .= "nombre_sistema=:nombre_sistema, ";
            $params[':nombre_sistema'] = $nombre_sistema;
        }
        if ($titulo_sistema !== null) {
            $sql .= "titulo_sistema=:titulo_sistema, ";
            $params[':titulo_sistema'] = $titulo_sistema;
        }
        if ($subtitulo_sistema !== null) {
            $sql .= "subtitulo_sistema=:subtitulo_sistema, ";
            $params[':subtitulo_sistema'] = $subtitulo_sistema;
        }
        if ($logo_sistema !== null) {
            $sql .= "logo_sistema=:logo_sistema, ";
            $params[':logo_sistema'] = $logo_sistema;
        }

        // Remover la última coma y espacio
        $sql = rtrim($sql, ', ');
        $sql .= " WHERE id_info=1"; // Cambia el id según sea necesario

        // Preparar y ejecutar la consulta
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['success' => true, 'message' => 'Información actualizada correctamente.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => "Error: " . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => "Error: " . $e->getMessage()]);
}

$conn = null;
