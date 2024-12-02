<?php
include '../../componentes/conexiones/conexionbd.php';

$tipo = $_GET['tipo'] ?? 'general';

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

// Construir la consulta
if ($tipo === 'APORTES') {
    $sql = "SELECT
            'DONACIÓN' AS tipo,
            benefactor COLLATE utf8mb4_general_ci AS beneficiario,
            montoRecibido AS monto,
            tipo_operacion COLLATE utf8mb4_general_ci AS tipo_operacion,
            origen COLLATE utf8mb4_general_ci AS banco,
            concepto COLLATE utf8mb4_general_ci AS concepto,
            estado COLLATE utf8mb4_general_ci AS estado,
            fechaAporte AS fecha
        FROM aportes_donaciones
        UNION ALL
        SELECT
            CONCAT('APORTE POR', ' ', tipo_aporte COLLATE utf8mb4_general_ci) AS tipo,
            CONCAT(nombre COLLATE utf8mb4_general_ci, ' ', apellido COLLATE utf8mb4_general_ci) AS beneficiario,
            monto,
            'Transferencia de Fondos' AS tipo_operacion,
            banco COLLATE utf8mb4_general_ci AS banco,
            concepto COLLATE utf8mb4_general_ci AS concepto,
            estado COLLATE utf8mb4_general_ci AS estado,
            fechaAporte AS fecha
        FROM aportes_afiliados
        UNION ALL
        SELECT
            'APORTE PATRONAL' AS tipo,
            procedencia COLLATE utf8mb4_general_ci AS beneficiario,
            monto,
            tipo_operacion COLLATE utf8mb4_general_ci AS tipo_operacion,
            banco COLLATE utf8mb4_general_ci AS banco,
            concepto COLLATE utf8mb4_general_ci AS concepto,
            estado COLLATE utf8mb4_general_ci AS estado,
            fechaEmision AS fecha
        FROM aportes_patronales
        ORDER BY fecha DESC
        LIMIT $offset, $limit";

  
    $sqlCount = "SELECT COUNT(*) AS total FROM (SELECT id_AportesDona AS id FROM aportes_donaciones UNION ALL SELECT id_aporte AS id FROM aportes_afiliados UNION ALL SELECT id_AportesPatron as id FROM aportes_patronales) AS total";
}
elseif ($tipo === 'EGRESO') {
    $sql = "SELECT
                'EGRESO' AS tipo,
                beneficiario COLLATE utf8mb4_general_ci AS beneficiario,
                monto,
                tipoOperacion COLLATE utf8mb4_general_ci AS tipo_operacion,
                banco COLLATE utf8mb4_general_ci AS banco,
                rConcept COLLATE utf8mb4_general_ci AS concepto,
                Null AS estado,
                fechaPagoEgreso AS fecha
            FROM registrar_egreso
            ORDER BY fecha DESC
            LIMIT $offset, $limit";
    $sqlCount = "SELECT COUNT(*) AS total FROM registrar_egreso";
} else {
    $sql = "SELECT
            'DONACIÓN' AS tipo,
            benefactor COLLATE utf8mb4_general_ci AS beneficiario,
            montoRecibido AS monto,
            tipo_operacion COLLATE utf8mb4_general_ci AS tipo_operacion,
            origen COLLATE utf8mb4_general_ci AS banco,
            concepto COLLATE utf8mb4_general_ci AS concepto,
            estado COLLATE utf8mb4_general_ci AS estado,
            fechaAporte AS fecha
        FROM aportes_donaciones
        UNION ALL
        SELECT
            CONCAT('APORTE POR', ' ', tipo_aporte COLLATE utf8mb4_general_ci) AS tipo,
            CONCAT(nombre COLLATE utf8mb4_general_ci, ' ', apellido COLLATE utf8mb4_general_ci) AS beneficiario,
            monto,
            'Transferencia de Fondos' AS tipo_operacion,
            banco COLLATE utf8mb4_general_ci AS banco,
            concepto COLLATE utf8mb4_general_ci AS concepto,
            estado COLLATE utf8mb4_general_ci AS estado,
            fechaAporte AS fecha
        FROM aportes_afiliados
        UNION ALL
        SELECT
            'APORTE PATRONAL' AS tipo,
            procedencia COLLATE utf8mb4_general_ci AS beneficiario,
            monto,
            tipo_operacion COLLATE utf8mb4_general_ci AS tipo_operacion,
            banco COLLATE utf8mb4_general_ci AS banco,
            concepto COLLATE utf8mb4_general_ci AS concepto,
            estado COLLATE utf8mb4_general_ci AS estado,
            fechaEmision AS fecha
        FROM aportes_patronales
    

        ORDER BY fecha DESC
        LIMIT $offset, $limit";


    $sqlCount = "SELECT COUNT(*) AS total FROM (SELECT id_AportesDona AS id FROM aportes_donaciones UNION ALL SELECT id FROM registrar_egreso UNION ALL SELECT id_aporte AS id FROM aportes_afiliados UNION ALL SELECT id_AportesPatron as id FROM aportes_patronales) AS total";
}

// Obtener el total de filas
$resultadoCount = $con->query($sqlCount);
$totalFilas = $resultadoCount->fetch_assoc()['total'];
header('X-Total-Filas: ' . $totalFilas);

// Procesar el resultado
$resultado = $con->query($sql);

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        echo "<tr>
                <td>" . $fila['tipo'] . "</td>
                <td>" . $fila['beneficiario'] ."</td>
                <td>" . $fila['monto'] . "</td>     
                <td>" . $fila['tipo_operacion'] . "</td>
                <td>" . $fila['banco'] . "</td>
                <td>" . $fila['concepto'] . "</td>
                <td>" . $fila['estado'] . "</td>
                <td>" . $fila['fecha'] . "</td>
                <td><button id='verificar' type='button'>Verificar</button></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No se encontraron resultados</td></tr>";
}
$con->close();
?>
