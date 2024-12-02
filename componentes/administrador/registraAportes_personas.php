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
    <title>Registrar aportes de personas</title>
    <link rel="stylesheet" href="./style/reportes_transaccion.css">
    <link rel="stylesheet" href="./style/modales.css">
</head>

<body>

    <section class="repEgresos">
        <div class="repEgresosShape">
            <div class="titleheader">
                <p>Registrar Aporte</p>
            </div>
            <hr>
            <div class="formShape">
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" id="formularioEgreso"
                    class="formularioAportePersonas" enctype="multipart/form-data"
                    onsubmit="validarFormularioDonacion(event)">


                    <input type="hidden" value="<?php echo $_SESSION['id_persona'] = $_SESSION['id_usuarios'] ?>"
                        name='idPersona'>

                    <div class="form-group">
                        <label for="tipoAporte">Tipo de Aporte</label>
                        <select id="tipoAporte" name="tipoAporte" required>
                            <option value="">Seleccione el Tipo de aporte</option>
                            <option value="Aportes Patronales"
                                <?= isset($_POST['tipoAporte']) && $_POST['tipoAporte'] == 'Aportes Patronales' ? 'selected' : '' ?>>
                                Aporte Patronal
                            </option>
                            <option value="Donación"
                                <?= isset($_POST['tipoAporte']) && $_POST['tipoAporte'] == 'Donación' ? 'selected' : '' ?>>
                                Donación
                            </option>
                            <option value="Aportar por Personas"
                                <?= isset($_POST['tipoAporte']) && $_POST['tipoAporte'] == 'Aportar por Personas' ? 'selected' : '' ?>>
                                Aportar por Personas
                            </option>
                        </select>
                    </div>

                    <div class="br">
                        <label for="nombreDonante">A nombre de:</label>
                        <input type="text" name="nombreDonante" id="nombreDonante">
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

                    <div class="br">
                        <label for="beneficiario">Beneficiario</label>
                        <input type="text" name="beneficiario" id="beneficiario" required="">
                    </div>


                    <div class="br" id="aporteTipoContainer">
                        <label for="aporteTipo">Tipo de Aporte</label>
                        <select id="aporteTipo" name="aporteTipo">
                            <option value="donacion">Aporte Donación</option>
                            <option value="estatuto">Aporte Estatuto</option>
                        </select>
                    </div>



                    <div class="br" id="referenciaContainer">
                        <label for="referencia">Número de Referencia</label>
                        <input type="text" id="referencia" name="referencia" required>
                    </div>


                    <div class="br">



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



                    <div class="form-group" id="usuarioTipoContainer">
                        <label for="tipoUsuario">Seleccione Tipo de Usuario</label>
                        <select id="tipoUsuario" name="tipoUsuario" onchange="mostrarModalTipoUsuario()" required>
                            <option value="">Seleccione el Tipo de Usuario</option>
                            <option value="afiliado">Afiliado</option>
                            <option value="invitado">Invitado</option>
                        </select>
                    </div>

                    <div class="br">
                        <label for="montoInput">Monto</label>

                        <input name="monto" placeholder="Bs.S&nbsp;0,00" type="text" id="montoInput"
                            oninput="formatearMonto()" required="">
                        <!--Monto en Bs de la institución con el formateo de numeros con decimales-->
                    </div>

                    <div class="br">
                        <label for="fechaPagoEgreso">Fecha de Emisión</label>
                        <input class="fechaAct" type="datetime-local" value="<?php echo date('Y-m-d\TH:i:s'); ?>"
                            name="fechaPagoIngreso" required readonly>
                        <!--Tambien hagan que la fecha se actualice mediante pasen los dias-->
                    </div>

                    <!--El select para seleccionar los bancos solo debe aparecer cuando en el select de tipo de operacion
                uno selecciona Transferencia de fondos. PDT SI UNO SELECCIONA EFECTIVO SOLO DEBE QUEDARSE ASI-->
                    <div class="br bankcontenedor" id="contenedorBanco">
                        <?php
                        //conexión.
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
                                    echo "<option value='" . $row['id_banco'] . "'>" . $row['id_banco'] . " - " . $row['nombre_banco'] . "</option>";
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

                    <div class="br">

                        <textarea name="concepto" id="rConcept" placeholder="Por Concepto de" required=""></textarea>
                    </div>

                    <div class="br">
                        <label for="comprobante">Adjuntar comprobante</label>
                        <input type="file" id="comprobante" onchange="previsualizarImagenRapida(event)"
                            accept="image/*">
                        <div id="imagenPrevia" style="display: none;">

                            <img id="imagenPreviaSrc" src="" alt="Imagen previa" style="max-width: 100%; height: auto;">
                        </div>
                    </div>



                    <div class="buttonsReport">
                        <button id="volverAportes" class="volverEgresos link-btn" type="button">Volver</button>
                        <button name="btn" class="submitReport" type="submit">Procesar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>




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



   

    <!-- Modal para buscar a la persona -->
    <div id="modalBuscarPersona" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalUsuario()">×</span>
            <h2>Buscar Persona</h2>
            <label for="busqueda">Buscar por:</label>
            <select id="filtroBusq" onchange="mostrarCampoBusqueda()">
                <option value="id_persona">ID Persona</option>
                <option value="nombre_usuario">Nombre de Usuario</option>
                <option value="nombreApellido">Nombre y Apellido</option>
                <option value="cedula">Cédula</option>
                <option value="correo">Correo</option>
            </select>

            <!-- Campo de entrada para buscar -->
            <input type="text" id="campoBusqueda" placeholder="Ingrese su búsqueda...">
            <button type="button" onclick="buscarPersona()">Buscar</button>

            <div id="resultadoBusqueda"></div>
        </div>
    </div>

    <!-- Modal para seleccionar a la persona -->
    <div id="modalSeleccionarPersona" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal('modalSeleccionarPersona')">×</span>
            <h2>Seleccionar Persona</h2>
            <div id="listaPersonas"></div>
        </div>
    </div>

</body>

</html>