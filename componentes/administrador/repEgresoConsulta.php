<?php
session_start();
if(!isset($_SESSION["id_usuarios"])){
    include_once "../../componentes/conexiones/permisosAdmin.php";
}

// Conexión usando PDO
$dsn = 'mysql:host=localhost;dbname=sacips_bd;charset=utf8';
$username = 'root';
$password = '';
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $conex = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Número total de filas
$totalRows = $conex->query("SELECT COUNT(*) FROM registrar_egreso")->fetchColumn();

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

$sqlBD = "
    SELECT 
        re.monto, 
        re.rConcept AS concepto, 
        re.beneficiario, 
        re.fechaPagoEgreso AS fecha,
        re.tipoOperacion, 
        re.tipo_egreso, 
        re.banco, 
        re.nro_cuenta, 
        te.tipo AS tipo_egreso, 
        toe.tipo AS tipo_operacion_nombre, -- Esto es lo que necesitas para el nombre
        te.codigo_egreso 
    FROM 
        registrar_egreso re 
    JOIN 
        tipo_egreso te ON re.tipo_egreso = te.tipo
    LEFT JOIN 
        tipo_operacion toe ON re.tipoOperacion = toe.id_tipoOperacion -- Asegúrate de usar el nombre correcto de la columna
    ORDER BY 
        re.fechaPagoEgreso DESC 
    LIMIT :offset, 5
";


$stmt = $conex->prepare($sqlBD);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$resultado = $stmt->fetchAll();

// Precio del dólar
$sqlPrecio = "SELECT precio FROM dolar_diario ORDER BY fecha DESC, hora_actualizacion DESC LIMIT 1";
$resultadoPrecio = $conex->query($sqlPrecio);
$precioDolar = $resultadoPrecio->fetchColumn();
$precioDolar = floatval($precioDolar);

$monto = array();

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./style/tablas.css">
    <link rel="stylesheet" href="./style/modales.css">
    <title>SACIPS | Administrador</title>
</head>

<body>
    <header>
        <?php include "../../componentes/template/admHeader.php"; ?>
    </header>

    <div class="tab-movs">
        <div class="barra-botones">
            <p class="titulo-movs">EGRESOS</p>
            <div class="b-Nmovs">
                <?php
                    // Calculando el offset anterior y el siguiente
                    $prevOffset = max(0, $offset - 5);
                    $nextOffset = min($totalRows, $offset + 5); // Cambiamos $totalRows - 5 a $totalRows 

                    // Ahora, establecemos hasNext en función del total de registros y la posición actual
                    $hasNext = ($nextOffset < $totalRows);
                ?>
                <input class="inpuTable" type="button"
                    onclick="ajax('./componentes/administrador/repEgresoConsulta.php?offset=<?php echo $prevOffset; ?>')"
                    value="Anterior" <?php echo ($offset <= 0) ? 'disabled' : ''; ?>>

                <input class="inpuTable" type="button"
                    onclick="ajax('./componentes/administrador/repEgresoConsulta.php?offset=<?php echo $hasNext ? $nextOffset : $offset; ?>')"
                    value="Siguiente" <?php echo !$hasNext ? 'disabled' : ''; ?>>
            </div>
        </div>

        <form class="tableEgreso" action="">
            <table>
                <tr>
                    <th class="t-head">Código</th>
                    <th class="t-head">Tipo de egreso</th>
                    <th class="t-head">Beneficiario</th>
                    <th class="t-head">Fecha / Hora</th>
                    <th class="t-head">Tipo Operación</th>
                    <th class="t-head">Banco</th>
                    <th class="t-head">Monto</th>
                    <th class="t-head"></th>
                </tr>

                <?php
                
                foreach ($resultado as $i => $row) {
                    $monto[$i] = $row['monto'];
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['codigo_egreso']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tipo_egreso']) . "</td>"; // Tipo de egreso
                    echo "<td>" . htmlspecialchars($row['beneficiario']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tipo_operacion_nombre']) . "</td>"; // Nombre de la operación
                    echo "<td>" . htmlspecialchars($row['banco']) . "</td>";
                
                    echo "<td class='montoInput'>" . htmlspecialchars($monto[$i]) . "<br>";
                    $valor = $monto[$i];
                    $solo_valor = preg_replace('/[^\d,]/', '', $valor);  
                    $solo_valor = str_replace(',', '.', $solo_valor);
                    $valor_float = (float) $solo_valor;
                    echo "$" . number_format($valor_float / $precioDolar, 2) . "</td>";
                    echo "<td><button onclick=\"ModalEgresoResivo('{$row['codigo_egreso']}', '{$row['tipo_egreso']}', '{$row['beneficiario']}', '{$row['fecha']}', '{$row['tipo_operacion_nombre']}', '{$row['banco']}','{$row['nro_cuenta']}', '{$row['concepto']}', '{$monto[$i]}')\">Vista completa</button></td>";
                    echo "</tr>";
                }
                
                ?>
            </table>
        </form>
    </div>

   

    <div id="modalEgreso" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalEgreso()">&times;</span>

            <div class="titleEgresoRecibo"></div>
            <div class="brEgresoTipo">
                <div class="brEgresoRecibo">
                    <label for="">Código</label>
                    <p id="codigoEgresoInput"></p>
                </div>
                <div class="brEgresoRecibo">
                    <label for="">Tipo de egreso</label>
                    <p id='tipoInput'></p>
                </div>
            </div>

            <div class="reciboEgreso">
                <div class="brEgresoRecibo">
                    <label for="montoInput">Monto</label>
                    <input type="text" id="montoInput" readonly>
                </div>
                <div class="brEgresoRecibo">
                    <label for="beneficiarioInput">Beneficiario</label>
                    <input type="text" id="beneficiarioInput" readonly>
                </div>
                <div class="brEgresoRecibo">
                    <label for="fechaInput">Fecha emitida</label>
                    <input type="text" id="fechaInput" readonly>
                </div>
                <div class="brEgresoRecibo">
                    <label for="tipoOperacionInput">Método de pago</label>
                    <input type="text" id="tipoOperacionInput" readonly>
                </div>
                <div class="brEgresoRecibo">
                    <label for="bancoInput">Banco</label>
                    <input type="text" id="bancoInput" readonly>
                </div>
                <div class="brEgresoRecibo">
                    <label for="nroCuenta">Número de cuenta</label>
                    <input type="text" id="nroCuenta" readonly>
                </div>
                <div class="brEgresoRecibo">
                    <label for="conceptoInput">Concepto</label>
                    <textarea id="conceptoInput" readonly></textarea>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Aquí podrías incluir tu lógica de JavaScript para manejar AJAX y los modales
    </script>
</body>

</html>