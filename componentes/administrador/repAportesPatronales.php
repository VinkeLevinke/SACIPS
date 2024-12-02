<?php
include("../../componentes/administrador/repAportesPatronalesForm.php");
date_default_timezone_set('America/Caracas');

session_start();
if (!isset($_SESSION["id_usuarios"])) {
    include_once "../../componentes/conexiones/permisosAdmin.php";
}

$conn = new mysqli("localhost", "root", "", "sacips_bd");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "SELECT * FROM tipo_operacion";
$result = $conn->query($sql);

$options = "";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $options .= '<option value="' . $row['id_tipoOperacion'] . '" data-categoria-pago="' . $row['categoria_pago'] . '">' . $row['tipo'] . '</option>';
    }
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportar Egreso</title>
    <link rel="stylesheet" href="./style/reportes_transaccion.css">
    <link rel="stylesheet" href="./style/modales.css">
</head>

<body>

    <?php include "../../componentes/template/admHeader.php";
    include "../../componentes/conexiones/conInfo_ipspuptyab.php";

    ?>

    <section class="repEgresos">
        <div class="repEgresosShape">
            <div class="titleheader">
                <p> Registrar Aporte Patronal</p>
            </div>
            <hr>

            <div class="formShape">
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" id="formularioEgreso"
                    enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="hidden" value="<?php echo $_SESSION['id_persona'] = $_SESSION['id_usuarios'] ?>"
                            name='idPersona'>
                        <label for="tipoAporte">Tipo de Aporte</label>
                        <select id="tipoAporte" name="tipoAporte" required>
                            <option value="">Seleccione el Tipo de aporte</option>
                            <option value="Aportes Patronales" <?= isset($_POST['tipoAporte']) && $_POST['tipoAporte'] == 'Aportes Patronales' ? 'selected' : '' ?>>Aporte Patronal</option>
                            <option value="Donación" <?= isset($_POST['tipoAporte']) && $_POST['tipoAporte'] == 'Donación' ? 'selected' : '' ?>>Donación</option>
                            <!-- <option value="Aportar por Personas" <?php /* isset($_POST['tipoAporte']) && $_POST['tipoAporte'] == 'Aportar por Personas' ? 'selected' : '' */ ?>
                                Aportar por Personas </option> -->


                        </select>
                    </div>
                    <div class="br">
                        <?php if ($resultInfo->num_rows > 0) {
                            while ($row = $resultInfo->fetch_assoc()) { ?>
                                <label for="beneficiario">Razón social</label>
                                <input type="text" name="razonSocial" id="razonsocial" required
                                    value="<?php echo $row['razon_social']; ?>?">

                            </div>

                            <div class="br">
                                <label for="rif">RIF</label>
                                <select id="tipoRif" name="tipoRif" required>
                                    <option value="J">J</option>
                                    <option value="V">V</option>
                                    <option value="G">G</option>
                                    <!-- Agregar otras opciones según sea necesario -->
                                </select>
                                <input type="text" name="rif" id="rif" required maxlength="8" pattern="\d{8}"
                                    placeholder="00000000">
                            </div>

                        <?php }
                        } ?>


                    <div class="br">
                        <label for="montoInput">Monto aportado</label>
                        <input name="monto" placeholder="Bs.S 0,00" type="text" id="montoInput"
                            oninput="formatearMonto()" required>
                    </div>


                    <div class="br">
                        <label for="fechaPagoEgreso">Fecha de Emisión del Procedimiento</label>
                        <input class="fechaAct" type="datetime-local" value="<?php echo date('Y-m-d\TH:i:s'); ?>"
                            name="fechaPagoIngreso" required readonly>
                    </div>


                    <div class="br">
                        <label for="tipoOperacion">Seleccione el Método de Pago </label>
                        <div class="brFormSelect">
                            <select id="tipoOperacion" name="tipoOperacion" required>
                                <option value="" disabled selected>Operación</option>
                                <?php echo $options; ?>
                            </select>
                            <button type="button" onclick="abrirAdd_MetdoPago()" class="add_tipo" id="add_metodopago">
                                <img src="./img/add.svg" alt="" class="img_add">
                                <p class="add_tipo-texto"></p>
                            </button>
                        </div>
                    </div>



                    <div class="br bankcontenedor" id="contenedorBanco">
                        <?php
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $bdname = "sacips_bd";
                        $conex = new mysqli($servername, $username, $password, $bdname);

                        $sqlBD = "SELECT * FROM banco";
                        $resultado = mysqli_query($conex, $sqlBD);
                        ?>


                        <label for="banco">Banco desde el cual se realizó el pago </label>


                        <div class="brFormSelect">
                            <select id="banco" name="banco" onchange="updateCodigoBanco()">
                                <option value="">Selecciona un banco</option>
                                <?php
                                while ($row = $resultado->fetch_assoc()) {
                                    echo "<option value='" . $row['id_banco'] . "'>" . $row['nombre_banco'] . "</option>";
                                }
                                ?>
                            </select>
                            <button type="button" onclick="abrirAdd_banco()" class="add_tipo" id="add_metodopago">
                                <img src="./img/add.svg" alt="" class="img_add">
                                <p class="add_tipo-texto"></p>
                            </button>
                        </div>
                    </div>

                    <div class="br" id='codigoBanco'>
                        <label class="codigoBanco" for="nro_cuenta" id="codigo_banco"></label>
                        <input type="number" id="nro_cuenta" name="nro_cuenta" placeholder="Ingrese su Número de Cuenta"
                            maxlength="21" oninput="validateAccountNumber()">
                    </div>

                    <div class="br">
                        <textarea name="concepto" id="rConcept" placeholder="Descripción del aporte o breve observación"
                            required></textarea>
                    </div>

                    <div class="br">
                        <label for="referencia">Referencia de transacción</label>
                        <input type="number" name="referencia_transaccion" id="referencia">
                    </div>

                    <div class="br">
                        <label class="botonImagen" for="comprobante"><img src="./img/capture.svg" alt="">
                            <p>subir comprobante</p>
                        </label>
                        <input type="file" hidden id="comprobante" name="comprobante"
                            onchange="previsualizarImagenRapida(event)" accept="image/*" required>
                        <div id="imagenPrevia" style="display: none;">
                            <img id="imagenPreviaSrc" src="" alt="Imagen previa" style="max-width: 100%; height: auto;">
                        </div>
                    </div>


                    <div class="buttonsReport">
                        <button id="volverAportes" class="volverEgresos link-btn" type="button">Volver</button>
                        <div class="volverBr"></div>
                        <button name="btn" class="submitReport" type="submit"
                            onclick="validarFormulario(event)">Procesar</button>
                    </div>
                </form>
            </div>
        </div>


        <div id="modal_MetodoPago" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarModal_mp()">×</span>
                <h2>Agregar Método de Pago</h2>
                <form id="agregarNuevoMetodoPago">
                    <label for="metodoPagoInput">Agregar Nuevo Método de Pago</label>
                    <input type="text" id="metodoPagoInput" name="metodoPago" placeholder="Método de Pago" required>

                    <label>Categoría del Pago</label>
                    <div class="radio-group">
                        <div class="radio1">

                            <label for="digital">Digital</label>
                            <input type="radio" id="digital" name="categoriaPago" value="DIGITAL" required>
                        </div>

                        <div class="radio2">

                            <label for="fisico">Físico</label>
                            <input type="radio" id="fisico" name="categoriaPago" value="FISICO" required>
                        </div>

                    </div>

                    <button type="button" onclick="aggNewMetodoPago()">Agregar</button>
                </form>
                <div id="mensajeRespuestaMetodoPago"></div>
            </div>
        </div>



        <div id="modal_nuevoBanco" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarModal_banco()">×</span>
                <h2>Agregar Nuevo Banco</h2>
                <form id="agregarNuevoBanco">
                    <div class="brmodal">
                        <label for="idBanco">Código de banco</label>
                        <input type="text" id="idBanco" name="idBanco" placeholder="Código de Banco">
                    </div>

                    <div class="brmodal">
                        <label for="nombreBanco">Nombre del Banco</label>
                        <input type="text" id="nombreBanco" name="nombreBanco" placeholder="Nombre completo del banco">
                    </div>
                    <button type="button" onclick="aggNewBanco()">Agregar</button>
                </form>
                <div id="mensajeRespuestaMetodoPago"></div>
            </div>
        </div>

        <div id="modalMensaje_general" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarMensaje_general()">X</span>
                <h2>Se ha agregado exitosamente!</h2>
                <div id="mensajeSecundario"></div>
                <div id="mensajeRespuestaMetodoPago"></div>
            </div>
        </div>

        <div id="modalMensajeEgreso_agg" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarMoadlMensajeEgreso_agg()">X</span>
                <h2>Se ha agregado exitosamente!</h2>
                <div id="mensajePago"></div>
            </div>
        </div>

    </section>

    <script src="/js/masOpciones.js"></script>

</body>

</html>