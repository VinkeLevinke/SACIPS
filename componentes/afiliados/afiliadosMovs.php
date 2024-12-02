<?php
include "../../componentes/conexiones/conexionbd.php"; // Incluimos la conexión a la base de datos
session_start(); // Iniciamos la sesión para poder acceder a las variables de sesión

$id_persona = $_SESSION['id_persona'];
$totalRows = mysqli_num_rows(mysqli_query($con, "SELECT * FROM aportes_afiliados WHERE id_persona = $id_persona"));

$limit = 4; // Cantidad de filas por página
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$offset = max(0, $offset); // Asegurar que el offset no sea negativo

$query = "SELECT * FROM aportes_afiliados WHERE id_persona = $id_persona ORDER BY fechaAporte DESC LIMIT $offset, $limit";
$resultmovs = mysqli_query($con, $query);

function convertDateTimeToAMPM($dateTime)
{
  $timestamp = strtotime($dateTime); // Convertimos la fecha a timestamp
  return date('d/m/Y h:i A', $timestamp); // Formateamos la fecha a AM/PM
}

/* La lógica de deuda */

$dolarQuery = "SELECT precio FROM dolar_diario ORDER BY fecha DESC, hora_actualizacion DESC LIMIT 1";
$dolarResult = mysqli_query($con, $dolarQuery);
$precioDolarRow = mysqli_fetch_assoc($dolarResult);
$precioDolar = floatval(str_replace(',', '.', $precioDolarRow['precio']));

$userQuery = "SELECT fechaIngreso FROM usuarios WHERE id_persona = $id_persona";
$userResult = mysqli_query($con, $userQuery);
$userRow = mysqli_fetch_assoc($userResult);
$fechaIngreso = new DateTime($userRow['fechaIngreso']);
$fechaActual = new DateTime();
$userQuery = "SELECT * FROM usuarios WHERE id_persona = $id_persona";


if ($fechaIngreso->format('d') > 1) {
  $mesesDeuda = $fechaActual->diff($fechaIngreso)->m + ($fechaActual->diff($fechaIngreso)->y * 12) + 1;
} else {
  $mesesDeuda = $fechaActual->diff($fechaIngreso)->m + ($fechaActual->diff($fechaIngreso)->y * 12);
}

$dolarMensual = 2; // Suponiendo que $dolarMensual es el costo mensual en dólares
$deudaTotalDolares = $dolarMensual * $mesesDeuda;
$deudaTotalBs = round($deudaTotalDolares * $precioDolar, 0); // Redondear a 2 decimales


$pagoTotal = 0;
$pagoQuery = "SELECT SUM(monto) AS totalPagado FROM aportes_afiliados WHERE id_persona = $id_persona AND estado = 'Aprobado'";
$pagoResult = mysqli_query($con, $pagoQuery);
$pagoRow = mysqli_fetch_assoc($pagoResult);
if ($pagoRow['totalPagado']) {
  $pagoTotal = floatval(str_replace('.', ',', preg_replace('/[^\d,]/', '', $pagoRow['totalPagado'])));
}


$nuevaDeudaBs = max(0, $deudaTotalBs - $pagoTotal); // Quitar el redondeo
$deudaEnDolaresCrudo = $nuevaDeudaBs / $precioDolar;


$deudaEnDolares = number_format($deudaEnDolaresCrudo, 2, ',', '');
$deudaEnBolivares = number_format($nuevaDeudaBs, 2, ',', '');

if ($precioDolar > 0) {
  $deudaEnDolaresCrudo = $nuevaDeudaBs / $precioDolar;
} else {
  $deudaEnDolaresCrudo = 0;
}



/* FIN DEUDA */

// Lógica de filtro (se mantiene igual)
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

$filteredResult = mysqli_query($con, $sqlFilters);
$totalFilteredRows = mysqli_num_rows($filteredResult); // Total de filas después de aplicar filtros

$sqlPersona = "SELECT * FROM usuarios WHERE id_persona= $id_persona";
$resultPersona = mysqli_query($con, $sqlPersona);

$Dolar = $precioDolar;
$MesIngreso = '';
$saldo = 0;
$saldoMensual = 2;
$mensualidadPagada = 0;

while ($row = $resultPersona->fetch_assoc()) {
  $MesIngreso = $row['fechaIngreso'];
  $saldo = $row['saldo'];
  $mensualidadPagada = $row['mensualidad'];
}




// Contadores para aportes
$aportesPorDonacionQuery = "SELECT COUNT(*) AS total_donacion FROM aportes_afiliados WHERE id_persona = $id_persona AND tipo_aporte = 'Donación'";
$aportesPorEstatutoQuery = "SELECT COUNT(*) AS total_estatuto FROM aportes_afiliados WHERE id_persona = $id_persona AND tipo_aporte = 'Estatuto'";

$aportesPorDonacionResult = mysqli_query($con, $aportesPorDonacionQuery);
$aportesPorEstatutoResult = mysqli_query($con, $aportesPorEstatutoQuery);

$aportesDonacionRow = mysqli_fetch_assoc($aportesPorDonacionResult);
$aportesEstatutoRow = mysqli_fetch_assoc($aportesPorEstatutoResult);

// Contadores para estados
$aprobadosQuery = "SELECT COUNT(*) AS total_aprobados FROM aportes_afiliados WHERE id_persona = $id_persona AND estado = 'Aprobado'";
$pendientesQuery = "SELECT COUNT(*) AS total_pendientes FROM aportes_afiliados WHERE id_persona = $id_persona AND estado = 'Pendiente'";
$declinadosQuery = "SELECT COUNT(*) AS total_declinados FROM aportes_afiliados WHERE id_persona = $id_persona AND estado = 'Declinado'";

$aprobadosResult = mysqli_query($con, $aprobadosQuery);
$pendientesResult = mysqli_query($con, $pendientesQuery);
$declinadosResult = mysqli_query($con, $declinadosQuery);

$aprobadosRow = mysqli_fetch_assoc($aprobadosResult);
$pendientesRow = mysqli_fetch_assoc($pendientesResult);
$declinadosRow = mysqli_fetch_assoc($declinadosResult);
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Movimientos</title>
</head>
<header>
  <?php include "../../componentes/template/aflHeader.php"; // Incluimos el encabezado 
  ?>
  <div class="inicio-afiliados">
    <div class="bloque">
      <div class="header-deuda">
        <div class="deuda">

          <?php


          // Al mostrar la deuda pendiente
          

          // Imprimir la deuda pendiente correctamente formateada
          
          // Imprimir la deuda pendiente correctamente formateada
          
          // echo "<p class='d-dolares'>Deuda pendiente: $ {$deudaEnDolares}</p>";
          // echo "<p class='d-bs'>Deuda pendiente: Bs {$deudaEnBolivares}</p>";
          
          //prueba
          // Datos del usuario
          $datosUsuarios = array(
            "FechaIngreso" => $MesIngreso,
            "saldo" => $saldo / $Dolar,  // Convierte los Bs a $ //saldo en dolares
            "mensualidad" => $saldoMensual,
            "mesualidad_pagada" => $mensualidadPagada  // Mes del último pago completo (ejemplo: septiembre)
          );

          // Función para verificar y actualizar el pago de la mensualidad
          function verificar_y_actualizar_pago_mensualidad(&$datosUsuarios, $mes_actual, &$id_persona)
          {
            $fecha_actual = new DateTime();
            $mes_ultimo_pago = $datosUsuarios['mesualidad_pagada'];
            $saldo = $datosUsuarios['saldo'];
            $mensualidad = $datosUsuarios['mensualidad'];
            $mensaje = "";

            // Verificar si es un nuevo mes desde el último pago
            if ($mes_actual > $mes_ultimo_pago) {
              $meses_de_diferencia = $mes_actual - $mes_ultimo_pago;

              // Calcular la deuda acumulada
              $deuda = $meses_de_diferencia * $mensualidad;

              // Verificar si el saldo es suficiente para cubrir la deuda
              if ($saldo >= $deuda) {
                // Descontar la deuda del saldo
                $datosUsuarios['saldo'] -= $deuda;
                // Marcar el mes actual como pagado
                $datosUsuarios['mesualidad_pagada'] = $mes_actual;
                $mensaje = "Mensualidad de este mes y meses anteriores pagadas.";

                //Actualizar la base de datos con el nuvo saldo y mes pagado
                $SALDO = round($datosUsuarios['saldo'], 2);
                $MES_PAGO = $datosUsuarios['mesualidad_pagada'];
                $PERSONA = $id_persona;
                $conex = new mysqli("localhost", "root", "", "sacips_bd");
                $ActualizarMensualidad = "UPDATE usuarios SET saldo=$SALDO, mensualidad=$MES_PAGO WHERE id_persona= $PERSONA";
                if ($conex->query($ActualizarMensualidad) === TRUE) {
                  echo "<br>Datos actualizados exitosamente";
                } else {
                  echo "Error: " . $ActualizarMensualidad . "<br>" . $conex->error;
                }
              } else {

                $meses_a_pagar = 0;
                //ESTO SOLO EN CASO DE QUE SEA POR EJEMPLO ENERO MES 1 Y EL ULTIMO PAGO FUE EN NOVIEMBRE MES 11
                //Se le suman 12 asi que serian (13-11) = 2 Es decir dos meses de diferencia
                if ($mes_actual < $mes_ultimo_pago) {
                  for ($i = 1; $i <= (($mes_actual + 12) - $mes_ultimo_pago); $i++) {
                    if ($saldo >= ($mensualidad * $i)) {
                      $meses_a_pagar += 1;
                    }
                  }
                } else {
                  for ($i = 1; $i <= ($mes_actual - $mes_ultimo_pago); $i++) {
                    if ($saldo >= ($mensualidad * $i)) {
                      $meses_a_pagar += 1;
                    }
                  }
                }
                if ($meses_a_pagar >= 1) {
                  //Actualizar la base de datos con el nuvo saldo y mes pagado
                  $SALDO = round($saldo - ($mensualidad * $meses_a_pagar), 2);
                  $MES_PAGO = $mes_ultimo_pago + $meses_a_pagar;
                  $PERSONA = $id_persona;
                  $conex = new mysqli("localhost", "root", "", "sacips_bd");
                  $ActualizarMensualidad = "UPDATE usuarios SET saldo=$SALDO, mensualidad=$MES_PAGO WHERE id_persona= $PERSONA";
                  if ($conex->query($ActualizarMensualidad) === TRUE) {
                    //echo "<br>Datos actualizados exitosamente";
                  } else {
                    echo "Error: " . $ActualizarMensualidad . "<br>" . $conex->error;
                  }
                }

                // Pagar con el saldo disponible y actualizar la deuda restante
                $saldo -= $deuda;
                if ($saldo < 0) {
                  $datosUsuarios['saldo'] = $saldo; // Saldo negativo indica deuda restante
                } else {
                  $datosUsuarios['saldo'] = 0;
                }
                // Mantener el mes del último pago hasta que la deuda sea cubierta
                $datosUsuarios['mesualidad_pagada'] = $mes_ultimo_pago;
                $mensaje = "Saldo insuficiente para cubrir la mensualidad.";
              }
            } else {
              $mensaje = "Mensualidad de este mes ya está pagada.";
            }
            return $mensaje;
          }

          // Obtener el mes actual
          $MesActual = date('Y-m-d');
          $mes_actual = date('n');  // Mes actual en formato numérico (1-12)
          
          // Actualizar y verificar mensualidad
          $mensaje_actualizacion = verificar_y_actualizar_pago_mensualidad($datosUsuarios, $mes_actual, $id_persona);

          // Continuar con el resto del código
          $MesIngreso = substr($datosUsuarios['FechaIngreso'], 0, 10);
          // echo "<div class='deuda'>";
          // echo "Fecha de Ingreso: " . $MesIngreso . "<br>";
          // echo "Fecha Actual: " . $MesActual . "<br>";
          
          $datetimeIngreso = new DateTime($MesIngreso);
          $datetimeActual = new DateTime($MesActual);

          // Calcular la diferencia del mes actual
          $diferencia = $datetimeIngreso->diff($datetimeActual);

          // Obtener la cantidad de meses transcurridos
          $mesesTranscurridos = ($diferencia->y * 12) + $diferencia->m;

          // Mostrar el resultado de meses transcurridos
          // echo "Han pasado $mesesTranscurridos meses desde que se inscribió el Afiliado.<br>";
          $mesesTranscurridos += 1;

          // Calcular los días restantes para completar el próximo mes
          $proxMesIngreso = clone $datetimeIngreso;
          $proxMesIngreso->modify('+1 month');
          $proxMesIngreso->setDate($datetimeIngreso->format('Y'), $datetimeIngreso->format('m') + $mesesTranscurridos + 1, 1);
          $diferenciaDias = $proxMesIngreso->diff($datetimeActual)->format('%r%a');

          // Mostrar el resultado de días restantes
          // echo "Faltan " . abs($diferenciaDias) . " días para el pago del siguiente mes.<br>";
          
          // Mostrar el estado actualizado
          if ($datosUsuarios['saldo'] < 0) {
            echo '<h4 class="alertaAfiliado">Debe Bs ' . round($datosUsuarios['saldo'] * $Dolar, 2) . ' al cambio $' . round(abs($datosUsuarios['saldo']), 2) . '<br>' . $mensaje_actualizacion . '</h4>';
          } else if ($datosUsuarios['saldo'] == 0) {
            echo '<h4 class="pagadoAfiliado">Saldo de Bs ' . number_format($Dolar * $datosUsuarios['saldo'], 2) . ' <br>La deuda mensual está saldada.</h4>';
          } else if ($datosUsuarios['saldo'] > 0) {
            echo '<h4 class="pagadoAfiliado">Ha abonado Bs ' . round($Dolar * $datosUsuarios['saldo'], 2) . ' al cambio $' . round($datosUsuarios['saldo'], 2) . ' dólares.<br> El excedente se aplicará a futuros pagos. </h4>';
          }

          ?>

        </div>



        <?php

        if ($deudaEnDolares = $pagoTotal) {

          echo "<img class='correct' src='./img/correct.svg' alt=''>";
        } else {
          echo "<input id='aAportar' class='link-btn' type='button' value='Pagar'> <!-- Botón para pagar -->";
        }

        ?>

      </div>
    </div>
<!-- Contadores de Aportes -->
<div class="aportes-count">
    <h4>Aportes Totales:</h4>
    <div class="contadores">
        <div class="contador">
            <span class="contador-titulo">Por Donación:</span>
            <span class="contador-valor"><?php echo $aportesDonacionRow['total_donacion']; ?></span>
        </div>
        <div class="contador">
            <span class="contador-titulo">Por Estatuto:</span>
            <span class="contador-valor"><?php echo $aportesEstatutoRow['total_estatuto']; ?></span>
        </div>
    </div>
    <h4>Estatus:</h4>
    <div class="contadores">
        <div class="contador">
            <span class="contador-titulo">Aprobados:</span>
            <span class="contador-valor"><?php echo $aprobadosRow['total_aprobados']; ?></span>
        </div>
        <div class="contador">
            <span class="contador-titulo">Pendientes:</span>
            <span class="contador-valor"><?php echo $pendientesRow['total_pendientes']; ?></span>
        </div>
        <div class="contador">
            <span class="contador-titulo">Declinados:</span>
            <span class="contador-valor"><?php echo $declinadosRow['total_declinados']; ?></span>
        </div>
    </div>
</div>

    
  </div>

  <div class="tab-movs">
    <form id="filterForm">
      <div class="filtros">
        <input type="text" id="busquedaReferencia" placeholder="Buscar por Nro Referencia">
        <input type="date" id="busquedaFecha" placeholder="Buscar por Fecha">
        <input type="number" id="busquedaMonto" hidden placeholder="Buscar por Monto">
        <select id="busquedaEstatus">
          <option value="">General</option>
          <option value="Aprobado">Aprobado</option>
          <option value="Declinado">Declinado</option>
          <option value="Pendiente">Pendiente</option>
        </select>
        <input type="button" value="Filtrar" onclick="afiliados_invitadosFiltrar()" />
        <div class="barra-botones">

          <div class="b-Nmovs">
            <input type="hidden" id="offset" value="<?php echo $offset; ?>">
            <input class="inpuTable" type="button" value="Anterior" onclick="afiliadosAHanterior()">
            <input class="inpuTable" type="button" value="Siguiente"
              onclick="afiliadosAHsiguiente(<?php echo $totalRows; ?>)">
          </div>
        </div>
      </div>



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
                              data-tipo_id='" . htmlspecialchars($rowmovs['id_aporte']) . "' 
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
                              data-tipoOperacion='" . htmlspecialchars($rowmovs['tipo_operacion']) . "'
                              data-imagen='" . base64_encode($rowmovs['capture']) . "'>Mostrar</button></td>";


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
    </form>
  </div>
</header>




<section>
  <?php include "../../componentes/template/mostrarCuentas.php"; ?>
</section>