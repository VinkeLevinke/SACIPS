<?php

$dsn = 'mysql:host=localhost;dbname=sacips_bd';
$usuario = 'root'; // Cambia esto por tu usuario
$pass = ''; // Cambia esto por tu contraseña

try {
    // Crear una instancia de PDO
    $conex = new PDO($dsn, $usuario, $pass);
    $conex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Conexion fallida: ' . $e->getMessage();
    exit();
}

// Manejo de la adición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_banco = $_POST['idBanco'];
    $nombre_banco = strtoupper($_POST['nombreBanco']); // Convertimos a mayúsculas

    // Preparamos la consulta SQL
    $sql = "INSERT INTO banco (id_banco, nombre_banco) VALUES (:id_banco, :nombre_banco)";

    // Usamos try/catch para manejar excepciones
    try {
        $stmt = $conex->prepare($sql);
        $stmt->bindParam(':id_banco', $id_banco);
        $stmt->bindParam(':nombre_banco', $nombre_banco);

        // Ejecutamos la consulta
        $stmt->execute();

        // Retornamos el ID del nuevo banco
        echo $conex->lastInsertId();
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>
