<?php
session_start();

// Redirigir a permisos admin si el usuario no está autenticado
if (!isset($_SESSION["id_usuarios"])) {
    include_once "../../componentes/conexiones/permisosAdmin.php";
}

// Mostrar errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Obtener el precio del dólar
$sqlPrecio = "SELECT precio FROM dolar_diario ORDER BY fecha DESC, hora_actualizacion DESC LIMIT 1";
$resultadoPrecio = $conex->query($sqlPrecio);
$precioDolar = 0;

if ($resultadoPrecio && $resultadoPrecio->num_rows > 0) {
    $rowPrecio = $resultadoPrecio->fetch_assoc();
    $precioDolar = $rowPrecio['precio'];
} else {
    echo "No se pudo obtener el precio del dólar.";
}

// Obtener el offset y asegurarse de que sea válido
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$offset = max(0, $offset); // Asegúrate que nunca sea negativo

// Obtener el número total de filas para cada tipo de aporte
$totalAportesPatronales = $conex->query("SELECT COUNT(*) as total FROM aportes_patronales")->fetch_assoc()['total'];
$totalAportesDonaciones = $conex->query("SELECT COUNT(*) as total FROM aportes_donaciones")->fetch_assoc()['total'];
$totalAportesAfiliados = $conex->query("SELECT COUNT(*) as total FROM aportes_afiliados")->fetch_assoc()['total'];
$totalRows = $totalAportesPatronales + $totalAportesDonaciones + $totalAportesAfiliados;

// Validar offset antes de usarlo en la consulta SQL
if ($offset >= $totalRows) {
    $offset = max(0, $totalRows - 6); // Asegúrate de que el offset no exceda el total 
}

// Consultas SQL para cada tipo de aporte
$searchQuery = isset($_GET['searchInput']) ? $conex->real_escape_string($_GET['searchInput']) : '';

// Consulta para donaciones
$sqlDonaciones = "
SELECT 
    'Donación' AS tipo,
    tipo_usuario,
    id_AportesDona AS id,
    benefactor,
    ap.telefono AS telefono,
    COALESCE(NULLIF(ap.beneficiario, ''), CONCAT(p.nombre, ' ', p.apellido)) AS beneficiario,
    t.tipo AS tipo_operacion,
    ap.tipo_operacion AS metodo_pago,
    ap.origen AS banco_ref,
    b.nombre_banco AS banco,
    montoRecibido AS monto,
    concepto,
    fechaAporte AS fecha,
    referencia,
    estado, ap.cedula AS cedula,
    tipo_usuario AS usuario
FROM aportes_donaciones ap
LEFT JOIN personas p ON ap.id_persona = p.id_personas 
LEFT JOIN tipo_operacion t ON ap.tipo_operacion = t.id_tipoOperacion 
LEFT JOIN banco b ON ap.origen = b.id_banco 
WHERE benefactor LIKE '%$searchQuery%' OR concepto LIKE '%$searchQuery%'
LIMIT $offset, 5
";

// Consulta para aportes patronales
$sqlPatronales = "
SELECT 
    'Aporte Patronal' AS tipo,
    id_aportesPatron AS id,
    procedencia AS beneficiario,
    t.tipo AS tipo_operacion,
    b.nombre_banco AS banco,
    monto,

    concepto,
    fechaEmision AS fecha,
    nro_cuenta AS referencia,
    '' AS estado,
    tipo_usuario AS usuario
FROM aportes_patronales 
INNER JOIN tipo_operacion t ON aportes_patronales.tipo_operacion = t.id_tipoOperacion 
INNER JOIN banco b ON aportes_patronales.banco = b.id_banco 
WHERE procedencia LIKE '%$searchQuery%' OR concepto LIKE '%$searchQuery%'
LIMIT $offset, 5
";

// Consulta para aportes afiliados
$sqlAfiliados = "
SELECT *,
    CONCAT(nombre, ' ', apellido) AS benefactor,
    t.tipo AS tipo_operacion, b.nombre_banco AS banco 
FROM aportes_afiliados 
INNER JOIN tipo_operacion t ON aportes_afiliados.tipo_operacion = t.id_tipoOperacion 
INNER JOIN banco b ON aportes_afiliados.banco = b.id_banco 
WHERE CONCAT(nombre, ' ', apellido) LIKE '%$searchQuery%' OR concepto LIKE '%$searchQuery%'
LIMIT $offset, 5
";

// Ejecutar consultas
$resultDonaciones = $conex->query($sqlDonaciones);
$resultPatronales = $conex->query($sqlPatronales);
$resultAfiliados = $conex->query($sqlAfiliados);

// Cerrar conexión
$conex->close();
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

    <!-- Header -->
    <?php include("../../componentes/template/admHeader.php"); ?>

    <div class="aporteConsulta-container">
        <div class="aporteConsulta-filter-container">
            <select id="tipoAporteSelect" onchange="aporteConsultaTipoChange()">
                <option value="" disabled>Filtrar por</option>
                <option value="general">General</option> <!-- Cambiado de afiliados a general -->
                <option value="afiliados" selected>Aportes Afiliados</option>
                <option value="patronales">Aportes Patronales</option>
                <option value="donaciones">Donaciones</option>
            </select>





            <button class="btn" onclick="abrirModalFiltroMes()">Filtrar por mes</button>
        </div>

        <!-- Aportes Afiliados -->
        <div class="aporteConsulta-aporte-container" id="aportesAfiliados">
            <table class="aporteConsulta-table">
                <caption class="aporteConsulta-title">Aportes Afiliados</caption>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Emisor</th>
                        <th>Operación</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Banco</th>
                        <th>Concepto</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($rowAfiliados = $resultAfiliados->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $rowAfiliados['usuario']; ?></td>
                            <td><?php echo $rowAfiliados['benefactor']; ?></td>
                            <td><?php echo $rowAfiliados['tipo_operacion']; ?></td>
                            <td>
                                <?php
                                $fechaAporte = new DateTime($rowAfiliados['fechaAporte']);
                                echo $fechaAporte->format('d/m/Y h:i A');
                                ?>
                            </td>
                            <td>Bs <?php echo $rowAfiliados['monto']; ?></td>
                            <td><?php echo $rowAfiliados['banco']; ?></td>
                            <td><?php echo $rowAfiliados['concepto']; ?></td>
                            <td><span
                                    class="<?php echo strtolower($rowAfiliados['estado']); ?>"><?php echo $rowAfiliados['estado']; ?></span>
                            </td>
                            <td>
                                <?php if ($rowAfiliados['estado'] == 'Pendiente'): ?>
                                    <button class='btn' type='button'
                                        onclick="verificarAporte(<?php echo $rowAfiliados['id_aporte']; ?>, '<?php echo $rowAfiliados['tipo']; ?>', '<?php echo $rowAfiliados['usuario']; ?>')">Verificar</button>
                                        <?php elseif($rowAfiliados['estado'] == 'Aprobado'):?>
                                             <button class='mandarGet' onclick='adminAfiliadosMostrarRef(this)' 
                        data-tipo_aporte='<?php echo htmlspecialchars($rowAfiliados['tipo_aporte']); ?>' 
                        data-tipo_id='<?php echo htmlspecialchars($rowAfiliados['id_aporte']); ?>' 
                        data-tipo_nombre='<?php echo htmlspecialchars($rowAfiliados['nombre']); ?>' 
                        data-tipo_apellido='<?php echo htmlspecialchars($rowAfiliados['apellido']); ?>' 
                        data-tipo_cedula='<?php echo htmlspecialchars($rowAfiliados['cedula']); ?>' 
                        data-tipo_banco='<?php echo htmlspecialchars($rowAfiliados['banco']); ?>' 
                        data-tipo_telefono='<?php echo htmlspecialchars($rowAfiliados['telefono']); ?>' 
                        data-monto='<?php echo htmlspecialchars($rowAfiliados['monto']); ?>' 
                        data-fechaAporte='<?php echo htmlspecialchars($rowAfiliados['fechaAporte']); ?>' 
                        data-referencia='<?php echo htmlspecialchars($rowAfiliados['referencia']); ?>' 
                        data-concepto='<?php echo htmlspecialchars($rowAfiliados['concepto']); ?>' 
                        data-estado='<?php echo htmlspecialchars($rowAfiliados['estado']); ?>' 
                        data-tipoOperacion='<?php echo htmlspecialchars($rowAfiliados['tipo_operacion']); ?>'
                        data-imagen='<?php echo base64_encode($rowAfiliados['capture']); ?>'>Mostrar</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Aportes Patronales -->
        <div class="aporteConsulta-aporte-container" id="aportesPatronales" style="display:none;">
            <table class="aporteConsulta-table">
                <caption class="aporteConsulta-title">Aportes Patronales</caption>
                <thead>
                    <tr>
                        <th>Razón Social</th>
                        <th>Operación</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Banco</th>
                        <th>Concepto</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($rowPatronales = $resultPatronales->fetch_assoc()): ?>
                        <tr>
                            <td  style="white-space: nowrap; overflow: hidden;"><?php echo $rowPatronales['beneficiario']; ?></td>
                            <td><?php echo $rowPatronales['tipo_operacion']; ?></td>

                            Copiar
                            <td>
                                <?php
                                $fechaPatronal = new DateTime($rowPatronales['fecha']);
                                echo $fechaPatronal->format('d/m/Y h:i A');
                                ?>
                            </td>
                            <td>Bs <?php echo $rowPatronales['monto']; ?></td>
                            <td><?php echo $rowPatronales['banco']; ?></td>
                            <td><?php echo $rowPatronales['concepto']; ?></td>
                            <td><?php echo $rowPatronales['estado'] ?: 'N/A'; ?></td>
                            <td>
                                <a onclick="reciboAporteP(<?php echo $rowPatronales['id']; ?>)" class='btnRecibo'>
                                    <img src='./img/recibo.svg' class='reciboImg' alt="Recibo">
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Donaciones -->
        <div class="aporteConsulta-aporte-container" id="aportesDonaciones" style="display:none;">
            <table class="aporteConsulta-table">
                <caption class="aporteConsulta-title">Donaciones</caption>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Emisor</th>
                        <th>Beneficiario</th>
                        <th>Cedula</th>
                        <th>Operación</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Banco</th>
                        <th>Concepto</th>
                        <th>Estado</th>
                        <th></th>

                    </tr>
                </thead>
                <tbody>
                    <?php while ($rowDonaciones = $resultDonaciones->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $rowDonaciones['tipo_usuario']; ?></td>
                            <td><?php echo $rowDonaciones['benefactor']; ?></td>
                            <td><?php echo $rowDonaciones['beneficiario']; ?></td>
                            <td><?php echo $rowDonaciones['cedula']; ?></td>
                            <td><?php echo $rowDonaciones['tipo_operacion']; ?></td>
                            <td>
                                <?php
                                $fechaDonacion = new DateTime($rowDonaciones['fecha']);
                                echo $fechaDonacion->format('d/m/Y h:i A');
                                ?>
                            </td>

                            <td>Bs <?php echo $rowDonaciones['monto']; ?></td>
                            <td><?php echo $rowDonaciones['banco']; ?></td>
                            <td><?php echo $rowDonaciones['concepto']; ?></td>
                            <td><span
                                    class="<?php echo strtolower($rowDonaciones['estado']); ?>"><?php echo $rowDonaciones['estado']; ?></span>
                            </td>
                            <td>
                                <?php

                                if ($rowDonaciones['estado'] == 'Pendiente' && $rowDonaciones['usuario'] == 'Invitado'): ?>

                                    <button class='btn' type='button'
                                        onclick="verificarAporte(<?php echo $rowDonaciones['id']; ?>,  '<?php echo $rowDonaciones['tipo']; ?>', '<?php echo $rowDonaciones['usuario']; ?>')">Verificar</button>


                                <?php elseif ($rowDonaciones['usuario'] != 'Invitado'): ?>
                                    <a onclick="reciboDonacion(<?php echo $rowDonaciones['id']; ?>)" class='btnRecibo'>
                                        <img src='./img/recibo.svg' class='reciboImg' alt="Recibo">
                                    </a>

                                <?php elseif ($rowDonaciones['usuario'] == 'Invitado' && $rowDonaciones['estado'] == 'Aprobado'): ?>

                                    <button onclick='referenciaInvitadoAdmin(event)' class='mandarGetinv'
                                        data-tipo_aporte='Donación' data-tipo_cedula='<?php echo $rowDonaciones['cedula']; ?>'
                                        data-tipo_banco='<?php echo $rowDonaciones['banco_ref']; ?>'
                                        data-tipo_telefono='<?php echo $rowDonaciones['telefono']; ?>'
                                        data-monto='<?php echo $rowDonaciones['monto']; ?>'
                                        data-tipo_id='<?php echo $rowDonaciones['id']; ?>'
                                        data-tipo_benefactor='<?php echo $rowDonaciones['benefactor']; ?>'
                                        data-tipo_beneficiario='<?php echo $rowDonaciones['beneficiario']; ?>'
                                        data-fechaAporte='<?php echo $rowDonaciones['fecha']; ?>'
                                        data-referencia='<?php echo $rowDonaciones['referencia']; ?>'
                                        data-concepto='<?php echo $rowDonaciones['concepto']; ?>'
                                        data-estado='<?php echo $rowDonaciones['estado']; ?>'
                                        data-tipoOperacion='<?php echo $rowDonaciones['metodo_pago']; ?>'>
                                        Mostrar
                                    </button>

                                <?php endif; ?>


                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class=" aporteConsulta-pagination">
            <button class="btn">Anterior</button>
            <button class="btn">Siguiente</button>
        </div>
        <button class="btn" onclick="window.open('./componentes/administrador/resportes/prueba.php', '_blank')">Descargar Reporte PDF</button>


        <!-- <div class="aporteConsulta-pagination">
            <button class="btn" onclick="aporteConsultaDescargar('pdf')">Descargar PDF</button>
            <button class="btn" onclick="aporteConsultaDescargar('excel')">Descargar Excel</button>
        </div> -->
        <!-- Mostrar los contadores generales -->
        <!-- Mostrar los contadores generales -->
        <div class="contadores">
            <div class="contador-card">
                <h3>Aportes Patronales</h3>
                <p><?php echo $totalAportesPatronales; ?></p>
            </div>
            <div class="contador-card">
                <h3>Aportes Afiliados</h3>
                <p><?php echo $totalAportesAfiliados; ?></p>
            </div>
            <div class="contador-card">
                <h3>Donaciones</h3>
                <p><?php echo $totalAportesDonaciones; ?></p>
            </div>
            <div class="contador-card total">
                <h3>Total de Aportes</h3>
                <p><?php echo $totalRows; ?></p>
            </div>
        </div>


    </div>


    <script src="./js/jquery-3.7.1.min.js"></script>
    <script>
        // Aquí puedes incluir cualquier script adicional si es necesario
    </script>

    <!-- Modales -->
    <!-- Modales -->
    <div id="modal_filtroMes" class="modal">
        <div class="modal-content"> <span class="close" onclick="cerrarModalFiltroMes()">×</span> 
        
        <div class="estilosFiltros">
           
        <select id="mesInicio"
                onchange="filtrarPorMes(false)">
                <option value="" disabled selected>Mes Inicio</option>
                <option value="01">Enero</option>
                <option value="02">Febrero</option>
                <option value="03">Marzo</option>
                <option value="04">Abril</option>
                <option value="05">Mayo</option>
                <option value="06">Junio</option>
                <option value="07">Julio</option>
                <option value="08">Agosto</option>
                <option value="09">Septiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
            </select>
            <select id="mesFin" onchange="filtrarPorMes(true)">
                <div class="filtrado">
                    <option value="" disabled selected>Mes Fin</option>
                    <option value="01">Enero</option>
                    <option value="02">Febrero</option>
                    <option value="03">Marzo</option>
                    <option value="04">Abril</option>
                    <option value="05">Mayo</option>
                    <option value="06">Junio</option>
                    <option value="07">Julio</option>
                    <option value="08">Agosto</option>
                    <option value="09">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </div>
            </select>
        </div>
         
        </div>
    </div>
</body>

</html>