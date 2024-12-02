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
    $id_persona = $_POST['id_persona'];
    $tipoCampo = $_POST['tipoCampo'];
    $nuevoValor = $_POST['nuevoValor'];
    $apellido = isset($_POST['apellido']) ? $_POST['apellido'] : null;

    try {
        switch ($tipoCampo) {
            case 'Nombre':
                if ($apellido === null) {
                    echo "Apellido no puede ser nulo.";
                    exit();
                }
                $sql_update = "UPDATE personas SET nombre = :nombre, apellido = :apellido WHERE id_personas = :id_persona";
                $stmt = $conex->prepare($sql_update);
                $stmt->bindParam(':nombre', $nuevoValor);
                $stmt->bindParam(':apellido', $apellido);
                $stmt->bindParam(':id_persona', $id_persona);
                break;
            case 'Cédula':
                $sql_update = "UPDATE personas SET cedula = :cedula WHERE id_personas = :id_persona";
                $stmt = $conex->prepare($sql_update);
                $stmt->bindParam(':cedula', $nuevoValor);
                $stmt->bindParam(':id_persona', $id_persona);
                break;
            case 'Telefono':
                $sql_update = "UPDATE personas SET telefono = :telefono WHERE id_personas = :id_persona";
                $stmt = $conex->prepare($sql_update);
                $stmt->bindParam(':telefono', $nuevoValor);
                $stmt->bindParam(':id_persona', $id_persona);
                break;
            case 'Correo':
                $sql_update = "UPDATE usuarios SET correo = :correo WHERE id_persona = :id_persona";
                $stmt = $conex->prepare($sql_update);
                $stmt->bindParam(':correo', $nuevoValor);
                $stmt->bindParam(':id_persona', $id_persona);
                break;
            default:
                echo "Tipo de campo no reconocido.";
                exit();
        }
        $stmt->execute();
        echo "Datos actualizados correctamente";
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>