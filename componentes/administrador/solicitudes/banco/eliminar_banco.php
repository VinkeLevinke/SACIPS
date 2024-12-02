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

    // Preparamos la consulta SQL para eliminar el banco
    $sql = "DELETE FROM banco WHERE id = :id";
    try {
        $stmt = $conex->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        echo "Banco eliminado correctamente";
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>
