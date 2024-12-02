<?php

$dsn = 'mysql:host=localhost;dbname=sacips_bd';
$usuario = 'root';
$pass = '';

try {
    $conex = new PDO($dsn, $usuario, $pass);
    $conex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Conexion fallida: ' . $e->getMessage();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_bancoTable'];
    $id_banco = $_POST['idBanco']; 
    $nombre_banco = $_POST['nombreBanco'];


    $sql = "UPDATE banco SET id_banco = :id_banco, nombre_banco = :nombre_banco WHERE id = :id";

    try {
        $stmt = $conex->prepare($sql);
     
        $stmt->bindParam(':id_banco', $id_banco);
        $stmt->bindParam(':nombre_banco', $nombre_banco);
        $stmt->bindParam(':id', $id);
    
    
        $stmt->execute();
    
      
        echo "Banco actualizado correctamente";
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
    
}
?>