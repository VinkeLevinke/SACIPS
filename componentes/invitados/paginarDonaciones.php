<?php
include "../../componentes/conexiones/conexionbd.php";
session_start();

// Verificación similar al archivo anterior
if (!isset($_SESSION['id_persona']) || !is_numeric($_SESSION['id_persona'])) {
    die('ID persona no válido.');
}

$id_persona = $_SESSION['id_persona'];
$limit = 4;

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$offset = max(0, $offset);

// Obtener donaciones paginadas
$query = "SELECT * FROM aportes_donaciones WHERE id_persona = ? ORDER BY fechaAporte DESC LIMIT ?, ?";
$stmt = $con->prepare($query);
$stmt->bind_param("iii", $id_persona, $offset, $limit);
$stmt->execute();
$resultmovs = $stmt->get_result();

$sqlPrecio = "SELECT precio FROM dolar_diario ORDER BY fecha DESC, hora_actualizacion DESC LIMIT 1";
$resultadoPrecio = $con->query($sqlPrecio);
$precioDolar = ($resultadoPrecio && $resultadoPrecio->num_rows > 0) ? floatval($resultadoPrecio->fetch_assoc()['precio']) : 1;

function convertDateTimeToAMPM($dateTime) {
    $timestamp = strtotime($dateTime);
    return date('d/m/Y h:i A', $timestamp);
}
?>

<table>
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
    <?php while ($rowmovs = mysqli_fetch_assoc($resultmovs)): ?>
        <tr>
            <td class='t-body'> Donación </td>
            <td> Bs. <?php echo number_format(floatval(str_replace(',', '.', $rowmovs['montoRecibido'])), 2); ?> <br> $<?php echo number_format((floatval(str_replace(',', '.', $rowmovs['montoRecibido']))) / $precioDolar, 2); ?> USD</td>
            <td class='t-body'> <?php echo convertDateTimeToAMPM($rowmovs['fechaAporte']); ?> </td>
            <td class='t-body'> <?php echo htmlspecialchars($rowmovs['referencia']); ?> </td>
            <td class='t-body'> <?php echo htmlspecialchars($rowmovs['concepto']); ?> </td>
            <td class='t-body'> <span class='<?php echo strtolower($rowmovs['estado']); ?>'><?php echo htmlspecialchars($rowmovs['estado']); ?></span></td>
            <td class='t-body'><button class='mandarGetinv' data-atributos...>Mostrar</button></td>
            <td class='t-body'>
                <?php if ($rowmovs['estado'] == 'Aprobado'): ?>
                    <a onclick='reciboInvitado(<?php echo $rowmovs['id_AportesDona']; ?>)' class='btnRecibo'><img src='./img/recibo.png' class='reciboImg'></a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
