<?php
include "../../componentes/conexiones/conexionbd.php";
session_start();
$id = $_POST['id'];
$tipo_aporte = $_POST['tipo_aporte'];
$tipo_cedula = $_POST['tipo_cedula'];
$tipo_banco = $_POST['tipo_banco'];
$tipo_telefono = $_POST['tipo_telefono'];
$monto = $_POST['monto'];
$fechaAporte = $_POST['fechaAporte'];
$referencia = $_POST['referencia'];
$concepto = $_POST['concepto'];
$estado = $_POST['estado'];
$tipoOperacion = $_POST['tipoOperacion'];

// Added for image
$imagenBase64 = $_POST['imagen'] ?? ''; // Capture the base64 image if it exists


// Formatear la fecha y hora en AM/PM
$fechaAporteFormato = date("d-m-Y h:i A", strtotime($fechaAporte));

// Consulta para obtener el nombre del tipo de operación
$query = "SELECT tipo FROM tipo_operacion WHERE id_tipoOperacion = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('i', $tipoOperacion);
$stmt->execute();
$stmt->bind_result($nombreTipoOperacion);
$stmt->fetch();
$stmt->close();


// Obtener nombre del banco
$queryBanco = "SELECT nombre_banco FROM banco WHERE id_banco = ?";
$stmtBanco = $con->prepare($queryBanco);
$stmtBanco->bind_param('s', $tipo_banco); // Suponiendo que tipo_banco es un string
$stmtBanco->execute();
$stmtBanco->bind_result($nombreBanco);
$stmtBanco->fetch();
$stmtBanco->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="style/afiliadosAporte.css">
    <link rel="stylesheet" href="../../style/afiliadosAporte.css">
    <link rel="stylesheet" href="./style/modales.css">
</head>

<body>
    <header>
        <?php include "../../componentes/template/aflHeader.php"; ?>
    </header>
    <div class="cuadroFormulario">
        <form action="./componentes/afiliados/procesarPago.php" id="formularioAfiliados" method="POST" enctype="multipart/form-data">
            <div class="btnVolver">
                <label for="btnVolverReciboconsulta">
                    <img src="./img/volver.png" alt="" class="reciboAfiliado_imgVolver">
                    <p class="reciboAfiliado_volverText">volver</p>
                </label>
                <input onclick="confirm('¿Seguro que desea salir?')" class="link-btn" type="button" id="btnVolverReciboconsulta" value="volver" hidden>
            </div>

            <p class="reciboAfiliado_aSubtitulo">Referencia de pago</p>
            <div class="reciboAfiliado_aHeader"></div>
            <div class="reciboAfiliado_fecha">
                <p class="reciboAfiliado_fechaRecibo"><b>Fecha de transacción: <?php echo $fechaAporteFormato; ?></b></p>
            </div>

            <div class="reciboAfiliado_monto">
                <label for="montoInput">Monto</label>
                <input type="text" id="montoInput" value="Bs <?php echo $monto; ?>" name="monto" required>
            </div>
            <div class="reciboAfiliado_estado">
                <label for="concepto">Estado</label>
                <input type="text" value="<?php echo $estado; ?>" readonly>
            </div>

            <div class="reciboAfiliado_banco">
                <label for="banco">Banco</label>
                <input type="text" value="<?php echo $tipo_banco; ?>" readonly>
            </div>
            <div class="reciboAfiliado_telefono">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" value="<?php echo $tipo_telefono; ?>" readonly required>
            </div>
            <div class="reciboAfiliado_documento">
                <label for="cedula">Cédula</label>
                <input type="text" id="cedula" name="cedula" value="<?php echo $tipo_cedula; ?>" readonly required>
            </div>
            <div class="reciboAfiliado_numReferencia">
                <label for="referencia">Numero de Referencia</label>
                <input type="text" name="referencia" value="<?php echo $referencia; ?>" readonly required>
            </div>
            <div class="reciboAfiliado_metodo_pago">
                <label for="metodoPago">Método de pago</label>
                <input type="text" value="<?php echo $nombreTipoOperacion; ?>" readonly>
            </div>
            <div class="reciboAfiliado_donacion">
                <label for="donacion">Tipo de Aporte</label>
                <input type="text" value="<?php echo $tipo_aporte; ?>" readonly>
            </div>
            <div class="reciboAfiliado_concepto">
                <label for="concepto">Por concepto</label>
                <textarea class="reciboAfiliado_txtAreaRecibo" readonly><?php echo $concepto; ?></textarea>
            </div>
            <div class="reciboAfiliado_donacion">
                <label for="id">Numero de movimiento</label>
                <input type="text" value="<?php echo $id; ?>" readonly>
            </div>

            <button type="button" class="ReciboCapture" onclick="mostrarCapture(<?php echo $id; ?>)">
            <img src="./img/capture-white.svg" class="capture" alt="">
            <p>Mostrar Capture</p>
        </button>

        </form>
    </div>

    <!-- Ventana modal -->
    <div id="modalCapture" class="modal">
        <span class="close" onclick="cerrarModalCapture()">×</span>

        <div class="modal-dialog modal-dialog-centered">


            <div class="imageContainer" id="imageContainer">
                <img id="captureImage" src="" alt="Capture" class="zoomable-image" onclick="toggleZoom(event)" />

            </div>
        </div>
    </div>
</body>

</html>
