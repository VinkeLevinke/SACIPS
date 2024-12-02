<?php
include("../../componentes/administrador/repEgresoForm.php");
date_default_timezone_set('America/Caracas');



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




$sql = "SELECT * FROM tipo_egreso";
$result = $conn->query($sql);

$tipo_egreso = "";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tipo_egreso .= "<option value='{$row['tipo']}'> {$row['codigo_egreso']} - {$row['tipo']}     </option>";
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
    <!-- aquí estoy reportando que tipo de egreso se hara -->

    <?php
    include "../../componentes/template/admHeader.php";
    ?>


    <section class="repEgresos">
        <div class="repEgresosShape">
            <div class="titleheader">
                <p>Registrar Egreso</p>
            </div>
            <hr>
            <div class="formShape">
            <form method="post" id="formularioEgreso" class="formulario_egreso">


                    <div class="brtAporte">
                        <label hidden for="tipoAporte">Tipo de Aporte</label>
                        <select hidden id="tipoAporte" name="tipoAporte" required>
                            <option value="">Seleccione el Tipo de aporte</option>
                            <option value="Egreso" selected>Egreso</option>
                        </select>
                    </div>
                    <div class="brtAporte">
                        <label for="tipoOperacion">Seleccione el Metodo de Pago</label>
                        <div class="brFormSelect">
                            <select id="tipoOperacion" name="tipoOperacion" required>
                                <option value="" disabled selected>Operación</option>
                                <?php echo $options; ?>
                            </select>
                            <button type="button" onclick="abrirAdd_MetdoPago()" class="add_tipo" id="add_metodopago"
                                value="add_metodopago" name="add_metodopago">
                                <img src="./img/add.svg" alt="" class="img_add">
                                <p class="add_tipo-texto"></p>
                            </button>
                        </div>
                    </div>
                    <div class="brtAporte">
                        <label for="montoInput">Monto</label>
                        <input name="monto" placeholder="Bs.S&nbsp;0,00" type="text" id="montoInput"
                            oninput="formatearMonto()" required>
                    </div>
                    <div class="brtAporte">
                        <textarea name="rConcept" id="rConcept" placeholder="Por Concepto de" required></textarea>
                    </div>
                    <div class="brtAporte">
                        <label for="tipoEgreso">Seleccione el tipo de egreso</label>
                        <div class="brFormSelect">
                            <select id="tipoEgreso" name="tipoEgreso" required>
                                <option value="" disabled selected>Tipo de Egreso</option>
                                <?php echo $tipo_egreso; ?>
                            </select>
                            <button type="button" onclick="abrirAddEgreso()" class="add_tipo" id="add_metodopago"
                                value="add_metodopago" name="add_metodopago">
                                <img src="./img/add.svg" alt="" class="img_add">
                                <p class="add_tipo-texto"></p>
                            </button>
                        </div>
                    </div>
                    <div class="brtAporte">
                        <label for="beneficiario">Beneficiario</label>
                        <input type="text" name="beneficiario" id="beneficiario" required>
                    </div>
                    <div class="brtAporte">
                        <label for="fechaPagoEgreso">Fecha de Emisión</label>
                        <input class="fechaAct" type="datetime-local" value="<?php echo date('Y-m-d\TH:i:s'); ?>"
                            name="fechaPagoEgreso" required readonly>
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
                        <label for="banco">Bancos:</label>
                        <div class="brFormSelect">
                            <select id="banco" name="banco" onchange="updateCodigoBanco()">
                                <option value="">Selecciona un banco</option>
                                <?php
                                while ($row = $resultado->fetch_assoc()) {
                                    echo "<option value='" . $row['id_banco'] . " - " . $row['nombre_banco'] . "'>" . $row['id_banco'] . " - " . $row['nombre_banco'] . "</option>";
                                }
                                ?>
                            </select>
                            <button type="button" onclick="abrirAdd_banco()" class="add_tipo" id="add_metodopago"
                                value="add_metodopago" name="add_metodopago">
                                <img src="./img/add.svg" alt="" class="img_add">
                                <p class="add_tipo-texto"></p>
                            </button>
                        </div>
                    </div>
                    <div class="br" id='codigoBanco'>
                        <label class="codigoBanco" for="nro_cuenta" id="codigo_banco"></label>
                        <input type="number" id="nro_cuenta" name="nro_cuenta" placeholder="Ingrese su nro de cuenta"
                            maxlength="21" oninput="validateAccountNumber()">
                    </div>
                    <div class="buttonsReport">
                        <button id="volverEgreso" class="volverEgresos link-btn" type="button">Volver</button>
                        <div class="volverBr"></div>
                        <button name="btn" class="submitReport" type="button" onclick="validarYEnviarFormulario(this.form, './componentes/administrador/repEgresoForm.php')">Procesar</button>


                    </div>
                </form>
            </div>
        </div>
    </section>



    <div id="modalEgresoAgg" class="modal">
        <div class="modal-content">
            <span class="close" onclick=" cerrarModalAddEgreso()">×</span>
            <h2>Registrar Tipo de Egreso</h2>
            <div id="mensajeRespuesta"></div>
            <form id="agregarNuevoEgreso">
                <label for="codeEgresoInput">Tipo de egreso</label>
                <input class="codigoEgreso" type="number" id="codeEgresoInput" name="codeEgreso" placeholder="Código">

                <input type="text" id="tipoEgresoInput" name="tipoEgreso" placeholder="Nombre del egreso">
                <div class="brFormBTN">
                <button type="button" onclick="aggNewEgreso()">Registrar</button>
                            </div>
               
            </form>
        
        </div>
    </div>

    <div id="modalErrores" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrar_errores()">×</span>
            <div id="mensaje_Error"></div>
        </div>
    </div>



     <div id="modal_MetodoPago" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal_mp()">×</span>
            <h2>Registrar Método de Pago</h2>
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

                <button type="button" onclick="aggNewMetodoPago()">Regustrar</button>
            </form>
            <div id="mensajeRespuestaMetodoPago"></div>
        </div>
    </div>







    <div id="modalMensajeEgreso_agg" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarMoadlMensajeEgreso_agg()">X</span>
            <h2>Se ha agregado exitosamente!</h2>
            <div id="mensajeSecundario"></div>
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
            <div class="mensajeSecundario"></div> <!-- Manten este div -->
        </div>
    </div>


</body>

</html>