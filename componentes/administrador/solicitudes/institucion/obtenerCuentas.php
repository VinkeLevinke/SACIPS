<?php  
// getCuentas.php  

// Configuración de la base de datos  
$dsn = 'mysql:host=localhost;dbname=sacips_bd;charset=utf8';  
$username = 'root';  
$password = '';  

try {  
    // Crear una nueva conexión PDO  
    $pdo = new PDO($dsn, $username, $password);  
    // Configurar el modo de error de PDO para que lance excepciones  
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
    
    // Consulta SQL  
    $query = "SELECT * FROM ipspuptyab_cuentas";  
    $stmt = $pdo->query($query);  

    // Obtener resultados  
    $cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);  
    
    // Enviar respuesta en formato JSON  
    header('Content-Type: application/json');  
    echo json_encode($cuentas);  

} catch (PDOException $e) {  
    // Manejo de errores  
    echo 'Error: ' . $e->getMessage();  
}  
?>