<?php
// buscarPersona.php
session_start();
$conn = new mysqli("localhost", "root", "", "sacips_bd");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener los datos directamente del POST
$filtro = $_POST['filtro'];
$busqueda = $conn->real_escape_string($_POST['busqueda']);

$sql = "";
switch ($filtro) {
    case 'id_persona':
    case 'correo':
        $sql = "SELECT * FROM personas WHERE $filtro LIKE '%$busqueda%'";
        break;
    case 'nombre_usuario':
        $sql = "SELECT p.* FROM personas p INNER JOIN usuarios u ON p.id_persona = u.id_persona WHERE u.nombre_usuario LIKE '%$busqueda%'";
        break;
    case 'nombreApellido':
        $sql = "SELECT * FROM personas WHERE CONCAT(nombre, ' ', apellido) LIKE '%$busqueda%'";
        break;
    case 'cedula':
        $tipoCedula = $_POST['cedulaTipo'];

        $sql = "SELECT * FROM personas WHERE cedula LIKE '%$busqueda%' AND cedula LIKE '$tipoCedula%'";

        break;

    default:
        $personas = 'Filtro no válido.';
        echo $personas;
        exit;
}

$result = $conn->query($sql);
$personas = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $personas .= '<div>' . 
                     'Nombre: ' . htmlspecialchars($row['nombre']) . ' ' . 
                     htmlspecialchars($row['apellido']) . 
                     ', Cédula: ' . htmlspecialchars($row['cedula']) . 
                     '</div>';
    }
} else {
    $personas = 'No se encontraron resultados.';
}

$conn->close();
echo $personas;
?>
