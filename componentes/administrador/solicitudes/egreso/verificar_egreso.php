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

$tipoEgreso = $_POST['tipoEgreso'];
$codeEgreso = $_POST['codeEgreso'];

$query = "SELECT COUNT(*) AS count FROM tipo_egreso WHERE codigo_egreso = ? AND tipo = ?";
$stmt = $conex->prepare($query);
$stmt->bindValue(1, $codeEgreso, PDO::PARAM_STR);
$stmt->bindValue(2, $tipoEgreso, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row['count'] > 0) {
    echo 'existe';
} else {
    echo 'no_existe';
}

$stmt->closeCursor();
$conex = null;
?>