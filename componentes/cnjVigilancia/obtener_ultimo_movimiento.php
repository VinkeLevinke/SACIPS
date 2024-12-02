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

session_start();

// Verificamos si el usuario está autenticado
if (!$_SESSION['tipo_usuario'] || $_SESSION['tipo_usuario'] != 4) {
    exit("Acceso denegado");
}

// Consulta para obtener el último movimiento reciente
$query = "
    SELECT fechaAporte AS fecha, banco, usuario, tipo_aporte AS tipo, concepto
    FROM aportes_afiliados
    UNION ALL
    SELECT fechaAporte AS fecha, origen AS banco, tipo_usuario AS usuario, 'Donación' AS tipo, concepto
    FROM aportes_donaciones
    UNION ALL
    SELECT fechaEmision AS fecha, banco, 'SISTEMA' AS usuario, 'Aporte Patronal' AS tipo, concepto
    FROM aportes_patronales
    UNION ALL
    SELECT fechaPagoEgreso AS fecha, banco, beneficiario AS usuario, tipo_egreso AS tipo, rConcept AS concepto
    FROM registrar_egreso
    ORDER BY fecha DESC
    limit 1
";

$result = $conex->query($query);
$row = $result->fetch(PDO::FETCH_ASSOC);

if ($row) {
    // Mostrar los detalles de la última transacción
    echo "<div class='ultimaTransaccion'>";
    echo "<h3>Última Transacción</h3>";
    echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s', strtotime($row['fecha'])) . "</p>";
    echo "<p><strong>Banco:</strong> " . htmlspecialchars($row['banco']) . "</p>";
    echo "<p><strong>Usuario:</strong> " . htmlspecialchars($row['usuario']) . "</p>";
    echo "<p><strong>Tipo de Aporte:</strong> " . htmlspecialchars($row['tipo']) . "</p>";
    echo "<p><strong>Concepto:</strong> " . htmlspecialchars($row['concepto']) . "</p>";
    echo "</div>";
} else {
    echo "<p>No hay transacciones recientes.</p>";
}

$conex = null;
?>
