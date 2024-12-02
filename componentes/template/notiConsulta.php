<?php
 include("../../componentes/conexiones/conexionbd.php");

if ($con->connect_error) {
    die("Conexión fallida: " . $con->connect_error);
}

$sqli = "SELECT p.nombre AS nombre, n.descripcion, n.fecha, n.leida, u.tipo_usuario, u.id_persona
         FROM notificaciones n
         INNER JOIN personas p ON n.id_persona = p.id_Personas
         INNER JOIN usuarios u ON p.id_Personas = u.id_persona";


$resultado = mysqli_query($con, $sqli);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($con));
}

$filas = [];
while ($fila = mysqli_fetch_array($resultado)) {
    $filas[] = $fila;
}

echo json_encode($filas); // Devuelve las notificaciones en formato JSON
?>
