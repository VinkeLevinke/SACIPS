<?php
include "../../componentes/conexiones/conexionbd.php"; // Conexión a la base de datos
session_start(); // Iniciamos sesión

// Verificar que id_persona esté definido y sea un número  
if (!isset($_SESSION['id_persona']) || !is_numeric($_SESSION['id_persona'])) {
    die('ID persona no válido.');
}

$id_persona = $_SESSION['id_persona'];
$limit = 4; // Cantidad de filas por página
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$offset = max(0, $offset); // Asegurarse de que el offset no sea negativo


$totalRowsQuery = "SELECT COUNT(*) AS total FROM aportes_donaciones WHERE id_persona = $id_persona";
$totalRowsResult = mysqli_query($con, $totalRowsQuery);
$totalRows = mysqli_fetch_assoc($totalRowsResult)['total'];


$query = "SELECT * FROM aportes_donaciones WHERE id_persona = ? ORDER BY fechaAporte DESC LIMIT ?, ?";
$stmt = $con->prepare($query);
$stmt->bind_param("iii", $id_persona, $offset, $limit);
$stmt->execute();
$resultmovs = $stmt->get_result();

// Obtener precio del dólar
$sqlPrecio = "SELECT precio FROM dolar_diario ORDER BY fecha DESC, hora_actualizacion DESC LIMIT 1";
$resultadoPrecio = $con->query($sqlPrecio);
$precioDolar = ($resultadoPrecio && $resultadoPrecio->num_rows > 0) ? floatval($resultadoPrecio->fetch_assoc()['precio']) : 1;

function convertDateTimeToAMPM($dateTime)
{
    $timestamp = strtotime($dateTime);
    return date('d/m/Y h:i A', $timestamp);


    /* FILTROS */

    /* Lógica de filtro */

    // Lógica de filtro
    $referencia = isset($_GET['referencia']) ? $_GET['referencia'] : '';
    $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
    $monto = isset($_GET['monto']) ? $_GET['monto'] : '';
    $estatus = isset($_GET['estatus']) ? $_GET['estatus'] : '';

    $sqlFilters = "SELECT * FROM aportes_donaciones WHERE id_persona = $id_persona";

    if ($referencia) {
        $sqlFilters .= " AND referencia LIKE '%" . mysqli_real_escape_string($con, $referencia) . "%'";
    }
    if ($fecha) {
        $sqlFilters .= " AND DATE(fechaAporte) = '" . mysqli_real_escape_string($con, $fecha) . "'";
    }
    if ($monto) {
        $sqlFilters .= " AND REPLACE(REPLACE(montoRecibido, '.', ''), ',', '') = '" . mysqli_real_escape_string($con, str_replace(',', '.', $monto)) . "'";
    }
    if ($estatus) {
        $sqlFilters .= " AND estado = '" . mysqli_real_escape_string($con, $estatus) . "'";
    }

    $resultmovs = mysqli_query($con, $sqlFilters);
    /* FILTROS */
}
?>

<head>
    <link rel="stylesheet" href="../style/tablas.css">
</head>

<header>
    <?php include "../../componentes/template/aflHeader.php"; ?>


    <div class="inicio-afiliados"></div>
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
                <input type="button" value="Filtrar" onclick="invitados_invitadosFiltrar()" />
                <div class="barra-botones">

                    <div class="b-Nmovs">
                        <input type="hidden" id="offset" value="<?php echo $offset; ?>">
                        <input class="inpuTable" type="button" value="Anterior" onclick="invitadosAHanterior()">
                        <input class="inpuTable" type="button" value="Siguiente"
                            onclick="invitadosAHsiguiente(<?php echo $totalRows; ?>)">
                    </div>
                </div>
            </div>


            <div id="formularioMovs">
                <table>
                    <tr>
                        <th class="t-head">Aporte</th>
                        <th class="t-head">Monto</th>
                        <th class="t-head">Fecha</th>
                        <th class="t-head">Nro Referencia</th>
                        <th class="t-head">Concepto</th>
                        <th class="t-head">Estatus</th>
                        <th class="t-head"></th>
                        <th class="t-head">Recibo</th>
                    </tr>
                    <?php
                    $i = 0;
                    while ($rowmovs = mysqli_fetch_assoc($resultmovs)) {
                        $i++;
                        $monto[$i] = $rowmovs['montoRecibido'];
                        echo "<tr>";
                        echo "<td class='t-body'> Donación </th>";
                        echo "<td>Bs " . $monto[$i] . "<br>";

                        // Procesar el monto  
                        $valor = $monto[$i];
                        $solo_valor = preg_replace('/[^\d,]/', '', $valor);
                        $solo_valor = str_replace(',', '.', $solo_valor);
                        $valor_float = (float) $solo_valor;
                        echo "$" . number_format($valor_float / $precioDolar, 2) . " USD</td>";
                        echo "<td class='t-body'>" . $rowmovs['fechaAporte'] . "</td>";
                        echo "<td class='t-body'>" . $rowmovs['referencia'] . "</td>";
                        echo "<td class='t-body'>" . $rowmovs['concepto'] . "</td>";

                        $estadoClass = '';
                        if ($rowmovs['estado'] == 'Aprobado') {
                            $estadoClass = 'aprobado';
                        } elseif ($rowmovs['estado'] == 'Declinado') {
                            $estadoClass = 'declinado';
                        } elseif ($rowmovs['estado'] == 'Pendiente') {
                            $estadoClass = 'pendiente';
                        }

                        echo "<td class='t-body'><span class='$estadoClass'>" . $rowmovs['estado'] . "</span></td>";
                        echo "<td class='t-body'>
                        <button onclick='handleButtonClick(event)'
                        class='mandarGetinv'
                        data-tipo_aporte='Donación'
                        data-tipo_cedula='" . $rowmovs['cedula'] . "'
                        data-tipo_banco='" . $rowmovs['origen'] . "'
                        data-tipo_telefono='" . $rowmovs['telefono'] . "'
                        data-monto='" . $rowmovs['montoRecibido'] . "'
                        data-tipo_id='" . $rowmovs['id_AportesDona'] . "'
                        data-tipo_benefactor='" . $rowmovs['benefactor'] . "'
                        data-tipo_beneficiario='" . $rowmovs['beneficiario'] . "'
                        data-fechaAporte='" . $rowmovs['fechaAporte'] . "'
                        data-referencia='" . $rowmovs['referencia'] . "'
                        data-concepto='" . $rowmovs['concepto'] . "'
                        data-estado='" . $rowmovs['estado'] . "'
                        data-tipoOperacion='" . $rowmovs['tipo_operacion'] . "'>Mostrar</button>
                        </td>";

                        if ($estadoClass == 'aprobado') {
                            echo "<td class='t-body'><a onclick=\"reciboInvitado(" . $rowmovs['id_AportesDona'] . ")\" class='btnRecibo'><img src='./img/recibo.png' class='reciboImg'></a></td>";
                        } else {
                            echo "<td></td>";
                        }

                        echo "</tr>";




                        /*
                         data-tipo_id = '" ; $rowmovs['id_AportesDona'] . "'
                            data-tipo_cedula='" . $rowmovs['cedula'] . "' 

                            data-tipo_benefactor='" . $rowmovs['benefactor'] . "' 
                            data-tipo_beneficiario='" . $rowmovs['beneficiario'] . "'
                        
                        
                        
                        */
                    }
                    ?>
                </table>
            </div>
        </form>
    </div>
</header>

<section>
    <?php include "../../componentes/template/mostrarCuentas.php"; ?>
</section>