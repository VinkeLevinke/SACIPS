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

$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;
$limit = 8; // Número de resultados por página
$offset = $page * $limit; // Calcular el offset

// Consulta para obtener todos los aportes junto con sus comentarios
$query = "
    SELECT a.id_aporte,
           DATE_FORMAT(a.fechaAporte, '%Y-%m-%d') AS fecha,
           DATE_FORMAT(a.fechaAporte, '%h:%i:%s %p') AS hora,
           a.monto,
           a.usuario AS usuario,
           CASE
               WHEN a.tipo_aporte = 'donacion' THEN 'Donación'
               WHEN a.tipo_aporte = 'estatuto' THEN 'Estatuto'
               WHEN a.tipo_aporte = 'patronal' THEN 'Aporte Patronal'
           END AS tipo,
           CONCAT(a.nombre, ' ', a.apellido) AS realizado_por,
           a.banco,
           a.cedula,
           a.referencia,
           a.concepto,
           a.estado,
           a.vigilante_comentario
    FROM aportes_afiliados a
    UNION ALL
    SELECT ad.id_AportesDona,
           DATE_FORMAT(ad.fechaAporte, '%Y-%m-%d') AS fecha,
           DATE_FORMAT(ad.fechaAporte, '%h:%i:%s %p') AS hora,
           ad.montoRecibido AS monto,
           ad.tipo_usuario AS usuario,
           'Donación' AS tipo,
           ad.benefactor AS realizado_por,
           ad.origen AS banco,
           ad.cedula,
           ad.referencia,
           ad.concepto,
           ad.estado,
           ad.vigilante_comentario
    FROM aportes_donaciones ad
    UNION ALL
    SELECT ap.id_AportesPatron,
           DATE_FORMAT(ap.fechaEmision, '%Y-%m-%d') AS fecha,
           DATE_FORMAT(ap.fechaEmision, '%h:%i:%s %p') AS hora,
           ap.monto,
           ap.tipo_usuario AS usuario,
           'Aporte Patronal' AS tipo,
           ap.procedencia AS realizado_por,
           ap.banco,
           NULL AS cedula,
           NULL AS referencia,
           ap.concepto,
           ap.estado,
           ap.vigilante_comentario
    FROM aportes_patronales ap
    ORDER BY fecha DESC
    LIMIT :limit OFFSET :offset
";

// Preparar y ejecutar la consulta
$stmt = $conex->prepare($query);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

echo "<table class='tableMovsVigilancia'>
        <thead>
            <th>Aporte</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Monto</th>
            <th>Tipo</th>
            <th>Realizado por</th>
            <th>Banco</th>
            <th>Cédula</th>
            <th>Referencia</th>
            <th>Concepto</th>
            <th>Estado</th>
            <th>Comentarios</th>
            <th></th>
        </thead>
        <tbody>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['tipo']}</td>
            <td>{$row['fecha']}</td>
            <td>{$row['hora']}</td>
            <td>{$row['monto']}</td>
            <td>{$row['usuario']}</td>
            <td>{$row['realizado_por']}</td>
            <td>{$row['banco']}</td>
            <td>{$row['cedula']}</td>
            <td>{$row['referencia']}</td>
            <td>{$row['concepto']}</td>
            <td>{$row['estado']}</td>
            <td>{$row['vigilante_comentario']}</td>
            <td><button type='button' onclick='abrirModalReciboPago(\"{$row['id_aporte']}\", \"{$row['monto']}\", \"{$row['concepto']}\", \"{$row['fecha']} {$row['hora']}\", \"{$row['realizado_por']}\", \"{$row['tipo']}\")'>REVISAR</button></td>
        </tr>";
}

echo "</tbody></table>";

?>  