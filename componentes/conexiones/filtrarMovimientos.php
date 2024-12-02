<?php
include "../../componentes/conexiones/conexionbd.php"; // Incluimos la conexión a la base de datos
session_start(); // Iniciamos la sesión para poder acceder a las variables de sesión

$id_persona = $_SESSION['id_persona']; // Obtenemos el ID de la persona desde la sesión
$totalRows = mysqli_num_rows(mysqli_query($con, "SELECT * FROM aportes_afiliados WHERE id_persona = $id_persona")); // Contamos el total de filas de aportes para esta persona
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0; // Determinamos el desplazamiento (offset) para paginar los resultados

// Asegurémonos de que el offset no sea negativo
$offset = max(0, $offset);

// Consulta SQL para obtener los últimos 4 aportes de la persona, ordenados por fecha
$query = "SELECT * FROM aportes_afiliados WHERE id_persona = $id_persona ORDER BY fechaAporte DESC LIMIT $offset, 4";
$resultmovs = mysqli_query($con, $query); // Ejecutamos la consulta

// Consulta para obtener el precio del dólar
$sqlPrecio = "SELECT precio FROM dolar_diario ORDER BY fecha DESC, hora_actualizacion DESC LIMIT 1";
$resultadoPrecio = $con->query($sqlPrecio);
$precioDolar = 0; // Inicializamos el precio del dólar

// Verificamos si hay resultados para el precio del dólar
if ($resultadoPrecio && $resultadoPrecio->num_rows > 0) {
  $rowPrecio = $resultadoPrecio->fetch_assoc(); // Obtenemos la fila del resultado
  $precioDolar = floatval($rowPrecio['precio']); // Convertimos el precio a float
}

// Función para convertir fecha a formato AM/PM
function convertDateTimeToAMPM($dateTime) {
  $timestamp = strtotime($dateTime); // Convertimos la fecha a timestamp
  return date('d/m/Y h:i A', $timestamp); // Formateamos la fecha a AM/PM
}

// Obtener y calcular mensualidad y deuda
$sql_aportes = "SELECT * FROM aportes_afiliados WHERE id_persona = $id_persona";
$sql_persona = "SELECT * FROM usuarios WHERE id_persona = $id_persona";
$result_aportes = $con->query($sql_aportes);
$result_persona = $con->query($sql_persona);
$mensualidad = 0;

while ($row = $result_aportes->fetch_assoc()) {
  if ($row['tipo_aporte'] == 'Estatuto' && $row['estado'] == 'Aprobado') {
    $mensualidad += $row['usd_ref'];
  }
}
while ($row = $result_persona->fetch_assoc()) {
  $MesIngreso = $row['fechaIngreso'];
}
$MesIngreso = substr($MesIngreso, 0, 10);
$MesActual = date('Y-m-d');

$datetimeIngreso = new DateTime($MesIngreso);
$datetimeActual = new DateTime($MesActual);
$diferencia = $datetimeIngreso->diff($datetimeActual);
$mesesTranscurridos = ($diferencia->y * 12) + $diferencia->m + 1;

// Calcular deuda en bolívares
$sql_dolar = "SELECT precio FROM dolar_diario";
$dolar_diario = $con->query($sql_dolar);
$rowDolar = $dolar_diario->fetch_assoc();
$montoDolar = $rowDolar['precio'];

$totalBolivar = '';
$mesajeDeuda = '';
if (($mesesTranscurridos * 2) > $mensualidad) {
  $deuda = ($mesesTranscurridos * 2) - $mensualidad;
  $mesajeDeuda = 'Debe <span class="d-dl-act"> $' . number_format($deuda, 2) . ' USD</span>';
  $totalBolivar = number_format($montoDolar * $deuda, 2);
} elseif (($mesesTranscurridos * 2) == $mensualidad) {
  $mesajeDeuda = 'La deuda mensual de $2 está saldada</h1>';
  $totalBolivar = '0,00';
} else {
  $abono = $mensualidad - ($mesesTranscurridos * 2);
  $mesajeDeuda = 'Ha abonado <span class="d-dl-act"> $' . number_format($abono, 2) . ' USD</span>';
  $totalBolivar = number_format($montoDolar * $abono, 2);
}

// Filtros
$referencia = isset($_GET['referencia']) ? $_GET['referencia'] : '';
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$monto = isset($_GET['monto']) ? $_GET['monto'] : '';
$estatus = isset($_GET['estatus']) ? $_GET['estatus'] : '';

$sqlFilters = "SELECT * FROM aportes_afiliados WHERE id_persona = $id_persona";

if ($referencia) {
  $sqlFilters .= " AND referencia LIKE '%" . mysqli_real_escape_string($con, $referencia) . "%'";
}
if ($fecha) {
  $sqlFilters .= " AND DATE(fechaAporte) = '" . mysqli_real_escape_string($con, $fecha) . "'";
}
if ($monto) {
  $sqlFilters .= " AND REPLACE(REPLACE(monto, '.', ''), ',', '') = '" . mysqli_real_escape_string($con, str_replace(',', '.', $monto)) . "'";
}
if ($estatus) {
  $sqlFilters .= " AND estado = '" . mysqli_real_escape_string($con, $estatus) . "'";
}

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
            <?php
            while ($rowmovs = mysqli_fetch_assoc($resultmovs)) {
              echo "<tr>";
              echo "<td class='t-body'>" . htmlspecialchars($rowmovs['tipo_aporte']) . "</td>";
              $monto = floatval(str_replace(',', '.', preg_replace('/[^\d,]/', '', $rowmovs['monto'])));
              echo "<td> Bs. " . number_format($monto, 2) . "   <br> $" . number_format($monto / $precioDolar, 2) . " USD </td>";
              echo "<td class='t-body'>" . convertDateTimeToAMPM($rowmovs['fechaAporte']) . "</td>";
              echo "<td class='t-body'>" . htmlspecialchars($rowmovs['referencia']) . "</td>";
              echo "<td class='t-body'>" . htmlspecialchars($rowmovs['concepto']) . "</td>";

              $estadoClass = $rowmovs['estado'] == 'Aprobado' ? 'aprobado' : ($rowmovs['estado'] == 'Declinado' ? 'declinado' : 'pendiente');
              echo "<td class='t-body'><span class='$estadoClass'>" . htmlspecialchars($rowmovs['estado']) . "</span></td>";

              echo "<td class='t-body'><button class='mandarGet' onclick='mostrarDatos(this)' 
                              data-tipo_aporte='" . htmlspecialchars($rowmovs['tipo_aporte']) . "' 
                              data-tipo_nombre='" . htmlspecialchars($rowmovs['nombre']) . "' 
                              data-tipo_apellido='" . htmlspecialchars($rowmovs['apellido']) . "' 
                              data-tipo_cedula='" . htmlspecialchars($rowmovs['cedula']) . "' 
                              data-tipo_banco='" . htmlspecialchars($rowmovs['banco']) . "' 
                              data-tipo_telefono='" . htmlspecialchars($rowmovs['telefono']) . "' 
                              data-monto='" . htmlspecialchars($rowmovs['monto']) . "' 
                              data-fechaAporte='" . htmlspecialchars($rowmovs['fechaAporte']) . "' 
                              data-referencia='" . htmlspecialchars($rowmovs['referencia']) . "' 
                              data-concepto='" . htmlspecialchars($rowmovs['concepto']) . "' 
                              data-estado='" . htmlspecialchars($rowmovs['estado']) . "' 
                              data-tipoOperacion='" . htmlspecialchars($rowmovs['tipo_operacion']) . "'>Mostrar</button></td>";

              if ($estadoClass == 'aprobado') {
                echo "<td class='t-body'><a onclick='reciboAfiliados(" . $rowmovs['id_aporte'] . ")' class='btnRecibo'><img src='./img/recibo.png' class='reciboImg'></a></td>";
              } else {
                echo "<td></td>";
              }

              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </div>