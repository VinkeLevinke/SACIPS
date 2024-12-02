<?php
// Iniciar la sesión
session_start();

// Si no existe la sesión de usuario, incluir archivo de permisos
if (!isset($_SESSION["id_usuarios"])) {
    include_once "../../componentes/conexiones/permisosAdmin.php";
}

// Configuración de la conexión a la base de datos
$dsn = 'mysql:host=localhost;dbname=sacips_bd';
$usuario = 'root'; // Cambia esto por tu usuario
$pass = ''; // Cambia esto por tu contraseña
$monto = [];
$monto['egreso'] = 0;
$monto['ingreso'] = 0;

try {
    // Crear una instancia de PDO para conexión a la base de datos
    $conex = new PDO($dsn, $usuario, $pass);
    $conex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener los movimientos utilizando UNION para combinar diferentes tipos de registros
    $query = "
    SELECT 'Aporte' AS tipo, a.nombre, a.apellido, ap.monto, 
           ap.fechaAporte AS fecha, ap.concepto, ap.referencia, ap.vigilante_comentario 
    FROM aportes_afiliados ap 
    JOIN personas a ON ap.id_persona = a.id_Personas 
    
    UNION ALL 
    
    SELECT 'Donación' AS tipo, a.nombre, a.apellido, ad.montoRecibido AS monto, 
           ad.fechaAporte AS fecha, ad.concepto, ad.referencia, ad.vigilante_comentario 
    FROM aportes_donaciones ad 
    JOIN personas a ON ad.id_persona = a.id_Personas 

    UNION ALL 
    
    SELECT 'Donación' AS tipo, benefactor AS nombre, '' AS apellido, ad.montoRecibido AS monto, 
           ad.fechaAporte AS fecha, ad.concepto, ad.referencia, ad.vigilante_comentario 
    FROM aportes_donaciones ad 
    WHERE tipo_usuario = 'Sistema'  -- Condición para sistema
    
    UNION ALL 
    
    SELECT 'Aporte patronal' AS tipo, ap.procedencia, NULL AS apellido, 
           ap.monto, ap.fechaEmision AS fecha, ap.concepto, ap.nro_cuenta AS referencia, NULL 
    FROM aportes_patronales ap
    
    UNION ALL
     
    SELECT 'Egreso' AS tipo, re.beneficiario AS nombre, NULL AS apellido, 
           re.monto, re.fechaPagoEgreso AS fecha, re.rConcept AS concepto, re.referencia, 
           re.vigilante_comentario 
    FROM registrar_egreso re
    ORDER BY fecha DESC";

    // Preparar y ejecutar la consulta de movimientos
    $stmt = $conex->prepare($query);
    $stmt->execute();
    $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consultas para contar el número de usuarios de cada tipo
    $queryAfiliados = "SELECT COUNT(*) AS count FROM usuarios WHERE tipo_usuario = 1";
    $queryInvitados = "SELECT COUNT(*) AS count FROM usuarios WHERE tipo_usuario = 2";
    $queryDirectores = "SELECT COUNT(*) AS count FROM usuarios WHERE tipo_usuario = 3";
    $queryVigilancia = "SELECT COUNT(*) AS count FROM usuarios WHERE tipo_usuario = 4";

    // Ejecutar y obtener el conteo de usuarios
    $countAfiliados = $conex->query($queryAfiliados)->fetch(PDO::FETCH_ASSOC)['count'];
    $countInvitados = $conex->query($queryInvitados)->fetch(PDO::FETCH_ASSOC)['count'];
    $countDirectores = $conex->query($queryDirectores)->fetch(PDO::FETCH_ASSOC)['count'];
    $countVigilancia = $conex->query($queryVigilancia)->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    // Mostrar mensaje de error en caso de fallar la conexión
    echo 'Conexión fallida: ' . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Tablero</title>
    <link rel="stylesheet" href="style/admDashboard.css">
    <link rel="stylesheet" href="./style/modales.css">
    <link rel="stylesheet" href="./style/tablas.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script></script>
    <style>
    .modal,
    .modalEliminar {
        background-color: #00000000;
    }

    .estadistica {
        position: relative;

    }
    </style>
</head>

<body>
    <?php include("../../componentes/template/admHeader.php"); ?>
    <section class="sectionHeader">
        <h2>Personas Registradas</h2>
        <div class="estadisticas">
            <div class="estadistica" onclick="mostrarModalDb('afiliados')">
                <img src="./img/afiliado.svg" alt="Afiliados">
                <p>Afiliados: <?= $countAfiliados ?></p>
            </div>
            <div class="estadistica" onclick="mostrarModalDb('invitados')">
                <img src="./img/invitado.svg" alt="Invitados">
                <p>Invitados: <?= $countInvitados ?></p>
            </div>
            <div class="estadistica" onclick="mostrarModalDb('directores')">
                <img src="img/directores.svg" alt="Directores">
                <p>Directores: <?= $countDirectores ?></p>
            </div>
            <div class="estadistica" onclick="mostrarModalDb('vigilancia')">
                <img src="img/vigilancia.svg" alt="Vigilancia">
                <p>Vigilancia: <?= $countVigilancia ?></p>
            </div>
        </div>
    </section>

    <section class="movimientosGenerales">
        <div class="tablaMovimientosGenerales">
            <p class="h1MovsTitle">Movimientos</p>
            <div class="filters">
                <input type="date" id='inicio' class="date-input">
                <input type="date" id='fin' class="date-input">
                <button onclick="filtrarPorRango()" class="filter-button">Filtrar</button>
            </div>
            

            <div class="tableDashboard">
                <table class="tableMovsGeneral">
                    <thead>
                        <tr>
                            <th class="t-head"></th>
                            <th class="t-head">Receptor</th>
                            <th class="t-head">Por monto</th>
                            <th class="t-head">Fecha y Hora</th>
                            <th class="t-head">Concepto</th>
                            <th class="t-head">Referencia</th>
                            <th class="t-head">Observaciones / vigilantes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $contador = 1;?>
                        <?php foreach ($movimientos as $mov): ?>
                        <tr>
                            <?php
                                // Definir la clase para el tipo de movimiento
                                $claseMovimiento = '';
                                if (in_array($mov['tipo'], ['Ingreso', 'Aporte', 'Aporte patronal', 'Donación'])) {
                                    $claseMovimiento = 'movIngreso'; // Si es ingreso
                                } else {
                                    $claseMovimiento = 'movEgreso';  // Si es egreso
                                }
                                ?>
                            <td class="<?= $claseMovimiento ?>">
                                <?= htmlspecialchars($mov['tipo'] ?? '') ?>
                            </td>
                            <td>
                                <?= !empty($mov['nombre']) ? htmlspecialchars($mov['nombre'] . ' ' . $mov['apellido']) : 'N/A'; ?>
                            </td>
                            <td class="<?= $claseMovimiento ?>">
                                <?= htmlspecialchars($mov['monto'] ?? ''); ?>

                            </td>
                            <td id='<?php echo "fecha_".$contador; ?> fecha'>
                                <?php if (!empty($mov['fecha'])):
                                        $fechaFormateada = date('d/m/Y h:i:s A', strtotime($mov['fecha']));
                                        echo htmlspecialchars($fechaFormateada);
                                        
                                    else:
                                        echo 'Fecha no disponible';
                                    endif; $contador++;?>
                            </td>
                            <td class="conceptMovs"><?= htmlspecialchars($mov['concepto'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($mov['referencia'] ?? ''); ?></td>
                            <td>
                                <?php if (!empty($mov['vigilante_comentario'])): ?>
                                <button class="buttonVigilancia" onclick="abrirModalVigilancia(
                                    '<?= htmlspecialchars($mov['tipo'] ?? '') ?>', 
                                    '<?= htmlspecialchars($mov['nombre'] . ' ' . $mov['apellido'] ?? '') ?>', 
                                    'BsS <?= htmlspecialchars($mov['monto'] ?? ''); ?>', 
                                    '<?= !empty($mov['fecha']) ? date('d/m/Y h:i:s A', strtotime($mov['fecha'])) : 'Fecha no disponible'; ?>', 
                                    '<?= htmlspecialchars($mov['concepto'] ?? ''); ?>', 
                                    '<?= htmlspecialchars($mov['vigilante_comentario'] ?? '', ENT_QUOTES); ?>')">
                                    <img class="imgVigilancia" src="img/ojo.png" alt="">

                                </button>
                                <?php endif; ?>
                                <?php
                                    if ($mov['tipo'] == 'Aporte' || $mov['tipo'] == 'Donación' || $mov['tipo'] == 'Aporte patronal') {
                                        $SoloMonto = '';
                                        $SoloMonto = $mov['monto'];
                                        $SoloMonto =  preg_replace('/[^\d,]/', '', $SoloMonto);
                                        $SoloMonto = str_replace(',', '.', $SoloMonto);
                                        $monto['ingreso'] += floatval($SoloMonto);
                                    } else if ($mov['tipo'] == 'Egreso') {
                                        $SoloMonto = '';
                                        $SoloMonto = $mov['monto'];
                                        $SoloMonto = str_replace(',', '.', $SoloMonto);
                                        $monto['egreso'] += floatval($SoloMonto);
                                    }
                                    ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total">
                    <h3>Total Ingreso: <?php echo number_format($monto['ingreso'], 2, ',', '.'); ?></h3>
                    <h3>Total Egreso: <?php echo number_format($monto['egreso'], 2, ',', '.'); ?></h3>
                </div>
            </div>
        </div>
    </section>


    <!-- Modal para comentarios del vigilante -->
    <div id="modalVigilancia" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalVigilancia()">×</span>
            <div class="headerUpdate">
                <h2>Comentario del Vigilante</h2>
            </div>
            <hr>
            <div class="reciboContenido">
                <h3>Detalles</h3>
                <p><strong>Aporte:</strong> <span id="modalTipo"></span></p>
                <p><strong>Beneficiario:</strong> <span id="modalBeneficiario"></span></p>
                <p><strong>Monto:</strong> <span id="modalMonto"></span></p>
                <p><strong>Fecha:</strong> <span id="modalFecha"></span></p>
                <p><strong>Concepto:</strong> <span id="modalConcepto"></span></p>
                <hr>
                <h3>Comentario del Vigilante</h3>
                <p id="modalComentario"></p>
            </div>
        </div>
    </div>

    <!-- Modal general -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalDb()">&times;</span>
            <h2 id="modal-title"></h2>
            <div id="modal-body"></div>
        </div>
    </div>
</body>

</html>