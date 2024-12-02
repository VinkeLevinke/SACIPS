<?php
// Configuración de conexión a la base de datos
$dsn = 'mysql:host=localhost;dbname=sacips_bd'; // Data Source Name
$usuario = 'root'; // Usuario de la base de datos
$pass = ''; // Contraseña de la base de datos

// Intentamos establecer la conexión a la base de datos
try {
    // Crear una nueva conexión de PDO
    $conex = new PDO($dsn, $usuario, $pass);

    // Configurar el modo de errores de PDO para que lance excepciones
    $conex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Manejo del error de conexión
    echo 'Conexion fallida: ' . $e->getMessage();
    exit(); // Terminar la ejecución en caso de fallo
}

// Manejo de la paginación
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0; // Obtener el número de página actual
$limit = 8; // Número de resultados por página
$offset = $page * $limit; // Calcular el desplazamiento (offset)

// Consulta para contar el total de egresos
$totalQuery = "SELECT COUNT(*) FROM registrar_egreso";
$totalStmt = $conex->query($totalQuery); // Ejecutar la consulta
$total = $totalStmt->fetchColumn(); // Obtener el total de egresos
$totalPages = ceil($total / $limit); // Calcular el total de páginas

// Consulta para obtener los egresos con paginación
$query = "SELECT *, 'Egreso' AS tipo FROM registrar_egreso ORDER BY fechaPagoEgreso DESC LIMIT :limit OFFSET :offset";
$stmt = $conex->prepare($query); // Preparar la consulta
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT); // Vincular el límite
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT); // Vincular el offset
$stmt->execute(); // Ejecutar la consulta

// Generar la tabla de resultados
echo "<table class='tableMovsVigilancia'>
        <thead>
            <th>ID</th>
            <th>Monto</th>
            <th>Concepto</th>
            <th>Beneficiario</th>
            <th>Fecha de Pago</th>
            <th>Tipo de Operación</th>
            <th>Tipo de Egreso</th>
            <th>Banco</th>
            <th>Número de Cuenta</th>
            <th></th>
        </thead>
        <tbody>";

// Iterar sobre los resultados y mostrarlos en la tabla
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['monto']}</td>
            <td>{$row['rConcept']}</td>
            <td>{$row['beneficiario']}</td>
            <td>{$row['fechaPagoEgreso']}</td>
            <td>{$row['tipoOperacion']}</td>
            <td>{$row['tipo_egreso']}</td>
            <td>{$row['banco']}</td>
            <td>{$row['nro_cuenta']}</td>
            <td><button type='button' onclick='abrirModalReciboPago(\"{$row['id']}\", \"{$row['monto']}\", \"{$row['rConcept']}\", \"{$row['fechaPagoEgreso']}\", \"{$row['beneficiario']}\", \"egreso\")'>REVISAR</button></td>
        </tr>";
}



echo "</tbody></table>"; // Cerrar la tabla
?>