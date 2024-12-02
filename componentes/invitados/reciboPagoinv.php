<?php
include "../../componentes/conexiones/conexionbd.php";
session_start();

$formData = json_decode($_GET['data'], true);

$fechaAporte = $formData['fecha'];

$monto = $formData['monto'];
$telefono = $formData['telefono'];
$cedula = $formData['cedula'];
$referencia = $formData['referencia'];
$concepto = $formData['concepto'];
$banco = $formData['banco'];
$tipoAporte = 'Donación';
$tipoOperacion = $formData['tipoOperacion'];

// Formatear la fecha y hora en AM/PM
$fechaAporteFormato = date("d-m-Y h:i A", strtotime($fechaAporte));

$query = "SELECT tipo FROM tipo_operacion WHERE id_tipoOperacion = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('i', $tipoOperacion);
$stmt->execute();
$stmt->bind_result($nombreTipoOperacion);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="style/afiliadosAporte.css">
    <link rel="stylesheet" href="../../style/afiliadosAporte.css">
</head>

<body>
    <header>
        <?php include "../../componentes/template/aflHeader.php"; ?>
    </header>

    <div class="cuadroFormulario">
        <form action="./componentes/afiliados/procesarPago.php" id="formularioAfiliados" method="POST" enctype="multipart/form-data">
            <div class="btnVolver">
                <label for="volverAporteInv">
                    <img src="./img/volver.png" alt="" class="reciboAfiliado_imgVolver">
                    <p class="reciboAfiliado_volverText">volver</p>
                </label>
                <input type="button" class="link-btn" id="volverAporteInv" value="volver" hidden>
            </div>


            <p class="reciboAfiliado_aTitulo">APORTE REALIZADO</p>
            <p class="reciboAfiliado_aSubtitulo">Referencia de pago</p>
            <div class="reciboAfiliado_aHeader"></div>
            <div class="reciboAfiliado_fecha">
                <p class="reciboAfiliado_fechaRecibo"><b>Fecha de transacción <?php echo $fechaAporteFormato; ?></b></p>
            </div>

            <!-- Sección del Monto -->
            <div class="reciboAfiliado_monto">
                <label for="montoInput">Monto</label>
                <input type="text" value="Bs <?php echo $monto; ?>" readonly>
            </div>


            <div class="reciboAfiliado_banco">
                <label for="banco">Banco</label>
                <input type="text" value="<?php echo $banco; ?>" readonly>
            </div>

            <div class="reciboAfiliado_nombre">
                <label for="nombre">Nombre</label>
                <input type="text" value="<?php echo $_SESSION['nombre']; ?>" readonly>
            </div>

            <div class="reciboAfiliado_apellido">
                <label for="apellido">Apellido</label>
                <input type="text" value="<?php echo $_SESSION['apellido']; ?>" readonly>
            </div>

            <div class="reciboAfiliado_telefono">
                <label for="telefono">Teléfono</label>
                <input type="text" value="<?php echo $telefono; ?>" readonly>
            </div>

            <div class="reciboAfiliado_documento">
                <label for="cedula">Cédula</label>
                <input type="text" value="<?php echo $cedula; ?>" readonly>
            </div>

            <div class="reciboAfiliado_numReferencia">
                <label for="referencia">Referencia</label>
                <input type="text" value="<?php echo $referencia; ?>" readonly>
            </div>

            <div class="reciboAfiliado_tipoOperacion">
                <label for="tipoOperacion">Tipo de Operación</label>
                <input type="text" value="<?php echo $nombreTipoOperacion; ?>" readonly>
            </div>

            <div class="reciboAfiliado_donacion">
                <label for="donacion">Tipo de Aporte</label>
                <input type="text" value="<?php echo $tipoAporte; ?>" readonly>
            </div>

            <div class="reciboAfiliado_concepto">
                <label for="concepto">Por concepto</label>
                <textarea class="reciboAfiliado_txtAreaRecibo" readonly><?php echo $concepto; ?></textarea>
            </div>
        </form>
    </div>
</body>

</html>