<?php
include "../../componentes/conexiones/conexionbd.php"; // Conexión a la base de datos
session_start(); // Iniciamos sesión

if (!isset($_SESSION['id_persona']) || !is_numeric($_SESSION['id_persona'])) {
    die('ID persona no válido.');
}

$id_persona = $_SESSION['id_persona'];
$limit = 4; // Cantidad de filas por página
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$offset = max(0, $offset); // Asegurarse de que el offset no sea negativo

// Obtener el precio del dólar
$sqlPrecio = "SELECT precio FROM dolar_diario ORDER BY fecha DESC, hora_actualizacion DESC LIMIT 1";
$resultadoPrecio = $con->query($sqlPrecio);
$precioDolar = ($resultadoPrecio && $resultadoPrecio->num_rows > 0) ? floatval($resultadoPrecio->fetch_assoc()['precio']) : 1;

// Lógica de filtro
$referencia = isset($_GET['referencia']) ? $_GET['referencia'] : '';
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$monto = isset($_GET['monto']) ? $_GET['monto'] : '';
$estatus = isset($_GET['estatus']) ? $_GET['estatus'] : '';



function convertDateTimeToAMPM($dateTime) {
    $timestamp = strtotime($dateTime);
    return date('d/m/Y h:i A', $timestamp);

}

$sqlFilters = "SELECT * FROM aportes_donaciones WHERE id_persona = $id_persona";

// Aplicar filtros
if ($referencia) {
    $sqlFilters .= " AND referencia LIKE '%" . mysqli_real_escape_string($con, $referencia) . "%'";
}
if ($fecha) {
    $sqlFilters .= " AND DATE(fechaAporte) = '" . mysqli_real_escape_string($con, $fecha) . "'";
}
if ($monto) {
    $montoSanitizado = mysqli_real_escape_string($con, str_replace(',', '.', $monto));
    $sqlFilters .= " AND REPLACE(REPLACE(montoRecibido, '.', ''), ',', '') = '$montoSanitizado'";
}
if ($estatus) {
    $sqlFilters .= " AND estado = '" . mysqli_real_escape_string($con, $estatus) . "'";
}

// Agregar paginación
$sqlFilters .= " ORDER BY fechaAporte DESC LIMIT $offset, $limit";

$resultmovs = mysqli_query($con, $sqlFilters);
?>

<div id="formularioMovs">
    <table>
        <thead>
            <tr>
                <th class="t-head">Aporte</th>
                <th class="t-head">Monto</th>
                <th class="t-head">Fecha</th>
                <th class="t-head">Nro Referencia</th>
                <th class="t-head">Concepto</th>
                <th class="t-head">Estatus</th>
                <th class="t-head">Referencia</th>
                <th class="t-head">Recibo</th>
                
            </tr>
        </thead>
        <tbody>
            <?php while ($rowmovs = mysqli_fetch_assoc($resultmovs)): ?>
                <tr>
                    <td class='t-body'>Donación</td>
                    <td>
                        <?php
                        $montoRecibido = floatval(str_replace(',', '.', str_replace('.', '', $rowmovs['montoRecibido'])));
                        ?>
                        Bs <?php echo number_format($montoRecibido, 2); ?><br>
                        $<?php echo number_format($montoRecibido / $precioDolar, 2); ?> USD
                    </td>
                    <td class='t-body'><?php echo convertDateTimeToAMPM($rowmovs['fechaAporte']); ?></td>
                    <td class='t-body'><?php echo htmlspecialchars($rowmovs['referencia']); ?></td>
                    <td class='t-body'><?php echo htmlspecialchars($rowmovs['concepto']); ?></td>
                    <td class='t-body'><span class='<?php echo strtolower($rowmovs['estado']); ?>'><?php echo htmlspecialchars($rowmovs['estado']); ?></span></td>
                    <td class='t-body'>
                        <button class='mandarGet' onclick='mostrarDatos(this)'>Mostrar</button>
                    </td>
                    <td class='t-body'>
                        <?php if ($rowmovs['estado'] == 'Aprobado'): ?>
                            <a onclick="reciboInvitado(<?php echo $rowmovs['id_AportesDona']; ?>)" class='btnRecibo'>
                                <img src='./img/recibo.png' class='reciboImg'>
                            </a>
                        <?php else: ?>
                            <td></td>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
