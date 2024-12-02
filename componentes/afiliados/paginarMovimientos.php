
<?php
include "../../componentes/conexiones/conexionbd.php";
session_start();

$id_persona = $_SESSION['id_persona'];
$totalRows = mysqli_num_rows(mysqli_query($con, "SELECT * FROM aportes_afiliados WHERE id_persona = $id_persona"));
$limit = 4;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$offset = max(0, $offset);

$query = "SELECT * FROM aportes_afiliados WHERE id_persona = $id_persona ORDER BY fechaAporte DESC LIMIT $offset, $limit";
$resultmovs = mysqli_query($con, $query);

$sqlPrecio = "SELECT precio FROM dolar_diario ORDER BY fecha DESC, hora_actualizacion DESC LIMIT 1";
$resultadoPrecio = $con->query($sqlPrecio);
$precioDolar = ($resultadoPrecio && $resultadoPrecio->num_rows > 0) ? floatval($resultadoPrecio->fetch_assoc()['precio']) : 1;

function convertDateTimeToAMPM($dateTime) {
    $timestamp = strtotime($dateTime);
    return date('d/m/Y h:i A', $timestamp);
}
?>

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
                <td class='t-body'><?php echo htmlspecialchars($rowmovs['tipo_aporte']); ?></td>
                <td> Bs. <?php echo number_format(floatval(str_replace(',', '.', $rowmovs['monto'])), 2); ?> <br> $<?php echo number_format(floatval(str_replace(',', '.', $rowmovs['monto'])) / $precioDolar, 2); ?> USD </td>
                <td class='t-body'><?php echo convertDateTimeToAMPM($rowmovs['fechaAporte']); ?></td>
                <td class='t-body'><?php echo htmlspecialchars($rowmovs['referencia']); ?></td>
                <td class='t-body'><?php echo htmlspecialchars($rowmovs['concepto']); ?></td>
                <td class='t-body'><?php echo htmlspecialchars($rowmovs['estado']); ?></td>
                <td class='t-body'><button class='mandarGet' onclick='mostrarDatos(this)'>Mostrar</button></td>
                <td class='t-body'>
                    <?php if ($rowmovs['estado'] == 'Aprobado'): ?>
                        <a onclick='reciboAfiliados(<?php echo $rowmovs['id_aporte']; ?>)' class='btnRecibo'><img src='./img/recibo.png' class='reciboImg'></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
