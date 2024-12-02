<?php
$servidor = "localhost";  
$usuariobd = "root";  
$conts = "";  
$baseDato = "sacips_bd";  
try {  
    $dsn = "mysql:host=$servidor;dbname=$baseDato;charset=utf8";  
    $usuarioPDO = $usuariobd;  
    $contraseñaPDO = $conts;  
    $conexion = new PDO($dsn, $usuarioPDO, $contraseñaPDO);  
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
} catch (PDOException $e) {  
    die("Conexión fallida con PDO: " . $e->getMessage());  
}  


// Obtener el JSON de la solicitud
$data = json_decode(file_get_contents("php://input"));

// Verificar si se recibió el ID
if (isset($data->id)) {
    $idCuenta = intval($data->id);
    
    // Preparar consulta para eliminar
    $sql = "DELETE FROM ipspuptyab_cuentas WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    
    if ($stmt->execute([$idCuenta])) {
        echo json_encode(["message" => "Cuenta eliminada con éxito."]);
    } else {
        echo json_encode(["message" => "Error al eliminar la cuenta."]);
    }
} else {
    echo json_encode(["message" => "ID no proporcionado."]);
}
?>
