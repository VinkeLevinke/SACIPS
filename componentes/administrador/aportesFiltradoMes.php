<?php
// Conexión a la base de datos  
$servername = "localhost";
$username = "root";
$password = "";
$bdname = "sacips_bd";
$conex = new mysqli($servername, $username, $password, $bdname);

// Verificar conexión  
if ($conex->connect_error) {
    die("Conexión fallida: " . $conex->connect_error);
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener parámetros de la consulta
$tipo = $_GET['tipo'];
$mesInicio = $_GET['mesInicio'];
$mesFin = $_GET['mesFin'];

// Validar y formar la consulta correspondiente
if ($tipo === 'afiliados') {
    $sql = "SELECT *, CONCAT(nombre, ' ', apellido) AS benefactor, t.tipo AS tipo_operacion,  b.nombre_banco AS banco 
            FROM aportes_afiliados 
            INNER JOIN tipo_operacion t ON aportes_afiliados.tipo_operacion = t.id_tipoOperacion 
             LEFT JOIN banco b ON aportes_afiliados.banco = b.id_banco 
            WHERE MONTH(fechaAporte) BETWEEN ? AND ?";
} elseif ($tipo === 'patronales') {
    $sql = "SELECT *, t.tipo AS tipo_operacion,  b.nombre_banco AS banco
            FROM aportes_patronales 
            INNER JOIN tipo_operacion t ON aportes_patronales.tipo_operacion = t.id_tipoOperacion 
            LEFT JOIN banco b ON aportes_patronales.banco = b.id_banco 
            WHERE MONTH(fechaEmision) BETWEEN ? AND ?";
} elseif ($tipo === 'donaciones') {
    $sql = "SELECT *, t.tipo AS tipo_operacion, b.nombre_banco AS origen, aportes_donaciones.origen AS banco_ref,
    montoRecibido AS monto, fechaAporte AS fecha, tipo_operacion AS metodo_pago 
            FROM aportes_donaciones 
            INNER JOIN tipo_operacion t ON aportes_donaciones.tipo_operacion = t.id_tipoOperacion 
            LEFT JOIN banco b ON aportes_donaciones.origen = b.id_banco 
            WHERE MONTH(fechaAporte) BETWEEN ? AND ?";
}

$stmt = $conex->prepare($sql);
$stmt->bind_param("ii", $mesInicio, $mesFin);
$stmt->execute();
$result = $stmt->get_result();

// Contador de filas
$numRows = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./style/aportesConsulta.css">
    <link rel="stylesheet" href="./style/modales.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Gestión de Aportes</title>
</head>

<body class="aporteConsulta-body">
    <?php
    // Contador de filas
    $numRows = $result->num_rows;
    ?>



    <?php
    // Construir la tabla de resultados
    echo '<table class="aporteConsulta-table">';
    echo '<thead>';
    echo '<tr>';

    // Definir encabezados de la tabla según el tipo
    if ($tipo === 'afiliados') {
        echo '<th>Emisor</th>';
        echo '<th>Tipo de Operación</th>';
        echo '<th>Fecha</th>';
        echo '<th>Monto</th>';
        echo '<th>Banco</th>';
        echo '<th>Concepto</th>';
        echo '<th>Estado</th>';
        echo '<th></th>';
    } elseif ($tipo === 'patronales') {
        echo '<th>Razón Social</th>';
        echo '<th>Tipo de Operación</th>';
        echo '<th>Fecha</th>';
        echo '<th>Monto</th>';
        echo '<th>Banco</th>';
        echo '<th>Concepto</th>';
        echo '<th>Estado</th>';
        echo '<th></th>';
    } elseif ($tipo === 'donaciones') {
        echo '<th>Emisor</th>';
        echo '<th>Beneficiario</th>';
        echo '<th>Tipo de Operación</th>';
        echo '<th>Fecha</th>';
        echo '<th>Monto</th>';
        echo '<th>Banco</th>';
        echo '<th>Concepto</th>';
        echo '<th>Estado</th>';
        echo '<th></th>';
    }

    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Llenar la tabla con datos
    if ($numRows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';

            if ($tipo === 'afiliados') {
                echo '<td>' . $row['benefactor'] . '</td>';
                echo '<td style="white-space: nowrap; overflow: hidden;">' . $row['tipo_operacion'] . '</td>';
                echo '<td>' . $row['fechaAporte'] . '</td>';
                echo '<td> Bs' . $row['monto'] . '</td>';
                echo '<td>' . $row['banco'] . '</td>';
                echo '<td style="white-space: nowrap; overflow: hidden;">' . $row['concepto'] . '</td>';
                echo '<td><span class="' . strtolower($row['estado']) . '">' . $row['estado'] . '</span></td>';
                echo '<td>';
                if ($row['estado'] === 'Pendiente') {
                    echo '<button class="btn" type="button" onclick="verificarAporte(' . $row['id_aporte'] . ', \'' . $row['tipo'] . '\', \'' . $row['usuario'] . '\')">Verificar</button>';
                }
                echo '</td>';
            } elseif ($tipo === 'patronales') {
                echo '<td>' . $row['procedencia'] . '</td>';
                echo '<td style="white-space: nowrap; overflow: hidden;">' . $row['tipo_operacion'] . '</td>';
                echo '<td>' . $row['fechaEmision'] . '</td>';
                echo '<td> Bs' . $row['monto'] . '</td>';
                echo '<td>' . $row['banco'] . '</td>';
                echo '<td style="white-space: nowrap; overflow: hidden;">' . $row['concepto'] . '</td>';
                echo '<td>' . ($row['estado'] ?: 'N/A') . '</td>';
                echo '<td><a class="btn"><img src="img/recibo.svg"></a></td>';
            } elseif ($tipo === 'donaciones') {
                echo '<td>' . $row['benefactor'] . '</td>';
                echo '<td style="white-space: nowrap; overflow: hidden;">' . $row['beneficiario'] . '</td>';
                echo '<td style="white-space: nowrap; overflow: hidden;">' . $row['tipo_operacion'] . '</td>';
                echo '<td>' . $row['fecha'] . '</td>';
                echo '<td> Bs' . $row['monto'] . '</td>';
                echo '<td>' . $row['origen'] . '</td>';
                echo '<td style="white-space: nowrap; overflow: hidden;">' . $row['concepto'] . '</td>';
                echo '<td><span class="' . strtolower($row['estado']) . '">' . $row['estado'] . '</span></td>';
                echo '<td>';

                // Aquí es donde implementamos las condiciones
                if ($row['estado'] == 'Pendiente' && $row['tipo_usuario'] == 'Invitado') { 
                    echo '<button class="btn" type="button"
                            onclick="verificarAporte(' . $row['id'] . ', \'' . $row['tipo'] . '\', \'' . $row['tipo_usuario'] . '\')">Verificar</button>';
                } elseif ($row['tipo_usuario'] != 'Invitado') {
                    echo '<a onclick="reciboDonacion(' . $row['id'] . ')" class="btnRecibo">
                            <img src="./img/recibo.svg" class="reciboImg" alt="Recibo"></a>';
                } elseif ($row['tipo_usuario'] == 'Invitado' && $row['estado'] == 'Aprobado') {
                    echo '<button onclick="referenciaInvitadoAdmin(event)" class="mandarGetinv"
                            data-tipo_aporte="Donación" 
                            data-tipo_cedula="' . $row['cedula'] . '"
                            data-tipo_banco="' . $row['banco_ref'] . '"
                            data-tipo_telefono="' . $row['telefono'] . '"
                            data-monto="' . $row['monto'] . '"
                            data-tipo_id="' . $row['id'] . '"
                            data-tipo_benefactor="' . $row['benefactor'] . '"
                            data-tipo_beneficiario="' . $row['beneficiario'] . '"
                            data-fechaAporte="' . $row['fecha'] . '"
                            data-referencia="' . $row['referencia'] . '"
                            data-concepto="' . $row['concepto'] . '"
                            data-estado="' . $row['estado'] . '"
                            data-tipoOperacion="' . $row['metodo_pago'] . '">Mostrar
                          </button>';
                }
        
                echo '</td>';
            }

            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="8">No se encontraron filas referentes a los meses seleccionados</td></tr>';
    }

    echo '</tbody></table>';

    $stmt->close();
    $conex->close();
    ?>

    <div class="contador-card">
        <h3>Total de registros</h3>
        <p><?php echo $numRows; ?></p>
        <div class="fechas-seleccionadas">
            <h4>Filtrado por Fechas</h4>
            <p></p> <!-- Este es donde se actualizará la fecha -->
        </div>
    </div>

</body>

</html>