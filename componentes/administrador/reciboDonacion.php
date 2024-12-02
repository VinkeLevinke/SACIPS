<?php
include '../../componentes/conexiones/conexionbd.php';
session_start();



if (!isset($_SESSION["id_usuarios"])) {
    include_once "../../componentes/conexiones/permisosAdmin.php";
}


if (isset($_GET['id']) && isset($_GET['tipo'])) {
    $id = $_GET['id'];
    $tipo = $_GET['tipo'];
    $usuario = $_GET['usuario'];
    // Obtener los datos del aporte o ingreso
    if ($usuario == 'Afiliado') {

        $sql = "SELECT monto, banco, CONCAT(nombre, ' ', apellido) AS beneficiario,
        usuario, cedula, telefono, referencia, fechaAporte AS fecha,  concepto
         FROM aportes_afiliados 
         WHERE id_aporte = $id";
    } elseif ($usuario == 'Invitado') {
        $sql = "SELECT montoRecibido AS monto, origen AS banco, 
        CONCAT(p.nombre, ' ', p.apellido) AS beneficiario, tipo_usuario AS usuario, ad.cedula AS cedula,
        ad.telefono AS telefono, ad.referencia AS referencia, ad.fechaAporte AS fecha, ad.concepto AS concepto
         
        FROM aportes_donaciones ad INNER JOIN personas p ON ad.id_persona = p.id_Personas
         WHERE ad.id_AportesDona = $id";
    }

    $result = $con->query($sql);
    $data = $result->fetch_assoc();
} else {
    echo "ID o tipo no especificado.";
    exit;
}

?>

<!-- Muestra otros datos necesarios -->


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>recibo</title>
    <link rel="stylesheet" href="./style/modales.css">
    <style>
        #modal_loader {
            background: rgba(0, 0, 0, 0.8);
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1200;
            display: none;
        }

        .loader-content {
            text-align: center;
            color: black;
            background-color: #e5e5e5;
            padding: 30px;
            width: 30%;
            border-radius: 5px;
        }

        .face {
            position: relative;
            width: 60px;
            height: 60px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: float 2s ease-in-out infinite;
            margin: 0 auto 30px;
            margin-top: 30px;
        }

        .face::before {
            content: '';
            width: 100px;
            height: 100px;
            border: solid white 5px;
            position: absolute;
            border-radius: 100%;
            border-bottom: #00b6e4fa solid 5px;
            left: -20px;
            top: -20px;
            box-sizing: border-box;
            animation: carga 4s linear infinite;
            transition: 0.2s;
            box-shadow: 0px 0px 10px 0px #0016ff75;
        }

        .eye {
            position: absolute;
            top: 35%;
            width: 10px;
            height: 10px;
            background: #000;
            border-radius: 50%;
        }

        .eye.left {
            left: 25%;
        }

        .eye.right {
            right: 25%;
        }

        .mouth {
            position: absolute;
            bottom: 20%;
            left: 50%;
            width: 20px;
            height: 10px;
            background: #000;
            border-radius: 0 0 10px 10px;
            transform: translateX(-50%);
            animation: smile 4s linear infinite;
        }

        .wink {
            position: absolute;
            top: 30%;
            left: 25%;
            width: 10px;
            height: 5px;
            background: #fff;
            border-radius: 0%;
            transform: scaleX(0);
            opacity: 0;
            animation: wink 4s linear infinite;
        }

        .kiss {
            position: absolute;
            bottom: 20%;
            left: 50%;
            width: 10px;
            height: 10px;
            background: #000;
            border-radius: 50%;
            transform: scale(0);
            opacity: 0;
            animation: kiss 4s linear infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes smile {

            0%,
            100% {
                transform: translateX(-50%) translateY(0);
            }

            50% {
                transform: translateX(-50%) translateY(-4px);
            }
        }

        @keyframes carga {
            0% {
                transform: rotate(0deg);
                border-left: #00b6e4fa 5px solid;
                border-bottom: solid 5px white;
            }

            25% {
                /*transform: rotate(360deg);*/
                border-left: white 5px solid;
                border-top: #00b6e4fa 5px solid;

            }

            50% {
                border-right: #00b6e4fa 5px solid;
                border-top: solid 5px white;
                box-shadow: 0px 0px 20px 2px #0016ff75;
            }

            75% {
                border-right: white solid 5px;
                border-bottom: #00b6e4fa 5px solid;
            }

            100% {
                transform: rotate(360deg);
                border-left: #00b6e4fa 5px solid;
                border-bottom: solid 5px white;
            }
        }
    </style>
</head>


<?php require("../../componentes/template/admHeader.php") ?>
<div class="reciboShape">
    <form id="reciboForm">

        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="tipo" value="<?php echo $tipo; ?>">

        <h2 class="tituloRecibo">Comprobante de Operación</h2>



        <div class="porMonto">
            <p>Monto de la Operación</p>
            <input type="text" value="<?php echo $data['monto']; ?>" readonly>
        </div>

        <div class="labels">
            <label for="bancoReceptor">Banco Receptor</label>
            <input type="text" value="<?php echo $data['banco']; ?>" readonly>

        </div>

        <div class="labels">

            <label for="nombre">Receptor</label>
            <input type="text" value="<?php echo $data['beneficiario']; ?>" readonly>

        </div>

        <div class="labels">

            <label for="tipo">por</label>
            <input type="text" name="usuario" value="<?php echo $data['usuario'] ?>" readonly>
        </div>

        <div class="labels">

            <label for="nombre">Tipo</label>
            <input type="text" value="<?php echo $tipo; ?>" readonly>

        </div>



        <div class="labels">
            <label for="cedula">cédula</label>
            <input type="text" value="<?php echo $data['cedula'] ?>" readonly>

        </div>
        <div class="labels">

            <label for="telefono">Teléfono</label>
            <input type="text" value="<?php echo $data['telefono'] ?>" readonly>

        </div>
        <div class="labels">
            <label for="">Referencia</label>
            <input type="text" value="<?php echo $data['referencia'] ?>" readonly>

        </div>

        <div class="labels">
            <label for="fecha">fecha</label>
            <input type="text" name="fecha" value="<?php echo $data['fecha']; ?>" readonly>
        </div>



        <div class="labels">
            <label for="concepto">Por concepto</label>
            <textarea name="concepto" id="concepto" readonly> <?php echo $data['concepto']; ?></textarea>

        </div>


        <button type="button" class="mostrarCapture" onclick="mostrarCapture(<?php echo $id; ?>)">
            <img src="./img/capture.svg" class="capture" alt="">
            <p>Mostrar Capture</p>
        </button>

        <!-- <div class="validacion">
            <div id="divAprobar" class="aprobar">
                <p>¿Estás seguro de que deseas <span class="colorConfirm">Confirmar el Aporte</span>?</p>
                <div class="botonesValidar">
                    <button id='aprobar' type="button" onclick="actualizarEstado('aprobar')">Sí</button>
                    <button id="regresar">No</button>
                </div>
            </div>

            <div id="divDenegar" class="denegar">
                <p>¿Estás seguro de que deseas <span class="colorConfirm">Declinar el Aporte</span>?</p>
                <div class="botonesValidar">
                    <button onclick="alert('Se ha Declinado el Aporte')" id="continuarDenegar">Sí</button>
                    <button id="regresar">No</button>
                </div>
            </div>

        </div> -->

        <div class="buttonsconfirm">
            <input name="declinar" type="button" onclick="actualizarEstado('declinar')" value="Declinar"
                class="declinado">
            <input name="aprobar" type="button" id='aprobar' type="button" onclick="actualizarEstado('aprobar')"
                value="Aprobar">

        </div>

        <button type="button" onclick="confirm('¿Seguro que desea abandonar esta pagina?')" id="volverAportes"
            class="atras link-btn">

            <img src="./img/volver.png" alt="">
            <p>Volver</p>
        </button>

    </form>
    <!-- Ventana modal -->
    <div id="modalCapture" class="modal">
        <span class="close" onclick="cerrarModalCapture()">×</span>

        <div class="modal-dialog modal-dialog-centered">


            <div class="imageContainer" id="imageContainer">
                <img id="captureImage" src="" alt="Capture" class="zoomable-image" onclick="toggleZoom(event)" />

            </div>
        </div>
    </div>

</div>
<!-- //==Incio Modal De Carga==// -->
<div id="modal_loader">
    <div class="loader-content">
        <div class="face">
            <div class="eye left"></div>
            <div class="eye right"></div>
            <div class="mouth"></div>
            <div class="wink"></div>
            <div class="kiss"></div>
        </div>
        <p>Espere un momento, por favor. <br>Su solicitud está siendo procesada.</p>
    </div>
</div>
<!-- //==Fin Modal De Carga==// -->
</body>

</html>