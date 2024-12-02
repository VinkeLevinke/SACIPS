<?php
$dsn = 'mysql:host=localhost;dbname=sacips_bd';
$usuario = 'root'; // Cambia esto por tu usuario
$pass = ''; // Cambia esto por tu contraseña

try {
    $conex = new PDO($dsn, $usuario, $pass);
    $conex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tipo = $_GET['tipo'];
    $query = "
        SELECT p.nombre, p.apellido, p.cedula, p.telefono, u.correo 
        FROM usuarios u 
        JOIN personas p ON u.id_persona = p.id_Personas 
        JOIN tipo_usuario t ON u.tipo_usuario = t.id 
        WHERE t.tipo_usuario = :tipo";

    $stmt = $conex->prepare($query);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($usuarios);

} catch (PDOException $e) {
    echo 'Conexión fallida: ' . $e->getMessage();
    exit();
}
?>
