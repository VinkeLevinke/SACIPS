<?php
include_once "../../componentes/conexiones/conInfo_ipspuptyab.php";

session_start();
if (!isset($_SESSION["id_usuarios"])) {
    include_once "../../componentes/conexiones/permisosAdmin.php";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalizar</title>
    <link rel="stylesheet" href="./style/modales.css">
    <link rel="stylesheet" href="./style/accesibilidad.css">
    <link rel="stylesheet" href="./style/descargar_manuales.css">
</head>

<body class="body-unique">


    <!-- Sección para actualizar el sistema -->
    <div class="section-update-system">
        <h2 class="title-update-system">Información del sistema</h2>
        <?php if ($resultSistema->num_rows > 0) {
            while ($row = $resultSistema->fetch_assoc()) { ?>
                <div class="system-info">
                    <input type="number" hidden value="<?php echo $row['id_info']; ?>">
                    <p class="system-name">
                        <strong>Nombre del Sistema:</strong><br>
                        <span
                            class="elegant-span"><?php echo htmlspecialchars($row['nombre_sistema'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </p>
                    <p class="system-title">
                        <strong>Título Descriptivo:</strong><br>
                        <span
                            class="elegant-span"><?php echo htmlspecialchars($row['titulo_sistema'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </p>
                    <p class="system-subtitle">
                        <strong>Subtítulo Descriptivo:</strong><br>
                        <span
                            class="elegant-span"><?php echo htmlspecialchars($row['subtitulo_sistema'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </p>
                    <p class="system-logo">
                        <strong>Logo:</strong><br>
                        <span><img
                                src="data:image/png;base64,<?php echo htmlspecialchars($row['logo_sistema'], ENT_QUOTES, 'UTF-8'); ?>"
                                alt="Logo" class="logo-image" style="max-width: 20%; margin-top: 10px;" id="currentLogo"></span>
                    </p>
                </div>
        <?php }
        } else {
            echo "<p>No se ha proporcionado información.</p>";
        } ?>
        <button class="button-update-sistem" id="openModal" onclick="abrirModal_actInfoSistema()">Actualizar
            Información</button>
    </div>





    <!-- Sección de información de la institución -->
    <div class="section-institution-bnkAccount">
        <h2 class="title-institution-info">Información de la Institución</h2>
        <?php if ($resultInfo->num_rows > 0) {
            while ($row = $resultInfo->fetch_assoc()) { ?>
                <div class="system-info">
                    <p class="label-razon-social">
                        <strong>Razón Social:</strong><br>
                        <span class="elegant-span"><?php echo htmlspecialchars($row['razon_social']); ?></span>
                    </p>
                    <p class="label-siglas">
                        <strong>Siglas:</strong><br>
                        <span class="elegant-span"><?php echo htmlspecialchars($row['siglas']); ?></span>
                    </p>
                    <p class="label-direccion">
                        <strong>Dirección:</strong><br>
                        <span class="elegant-span"><?php echo htmlspecialchars($row['direccion']); ?></span>
                    </p>
                    <p class="label-rif">
                        <strong>RIF:</strong><br>
                        <span class="elegant-span"><?php echo htmlspecialchars($row['rif_institucion']); ?></span>
                    </p>
                    <p class="label-email">
                        <strong>Correo Electrónico:</strong><br>
                        <span class="elegant-span"><?php echo htmlspecialchars($row['correo']); ?></span>
                    </p>
                    <p class="label-telefono">
                        <strong>Teléfonos:</strong><br>
                        <span class="elegant-span"><?php echo htmlspecialchars($row['telefono']); ?></span>
                    </p>
                    <p class="firma-digital">
                        <strong>Firma digital:</strong><br>
                        <span>
                            <img src="data:image/png;base64,<?php echo htmlspecialchars($row['firma_digital'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                style="max-width: 20%; margin-top: 10px;" alt="Previsualización de firma">
                        </span>
                    </p>

                </div>
        <?php }
        } else {
            echo "<p>No se ha proporcionado información.</p>";
        } ?>
        <button class="button-update-sistem" id="openModal" onclick="abrirModal_actInfoInstitucion()">Actualizar
            Información</button>
    </div>



    <!-- Sección de cuentas bancarias -->
    <div class="section-bank-accounts">
        <h2 class="title-bank-accounts">Cuentas bancarias que maneja la institución</h2>

        <form id="form_cuentasInstituto">
            <div class="bank-account-container">
                <?php if ($resultCuentas->num_rows > 0) {
                    $cuentas = $resultCuentas->fetch_all(MYSQLI_ASSOC);
                    $row = $cuentas[0]; // Muestra inicialmente la primera cuenta
                ?>
                    <div class="bank-account" id="bankAccount">
                        <div class="textGroup">
                            <input type="hidden" id="id_cuenta" value="<?php echo $row['id']; ?>">


                            <label>Propietario de la Cuenta:</label>
                            <input type="text" id="propietarioCuenta"
                                value="<?php echo htmlspecialchars($row['propietario_cuenta']); ?>" readonly>

                            <label>Método de pago:</label>
                            <input type="text" id="metodoPago"
                                value="<?php echo htmlspecialchars($row['nombre_tipo_operacion']); ?>" readonly>

                            <label>Nombre del Banco:</label>
                            <input type="text" id="nombreBanco"
                                value="<?php echo htmlspecialchars($row['nombre_banco']); ?>" readonly>

                            <label>Tipo de Cuenta:</label>
                            <input type="text" id="tipoCuenta" value="<?php echo htmlspecialchars($row['tipo_cuenta']); ?>"
                                readonly>

                            <label>Número de Cuenta:</label>
                            <input type="text" id="numeroCuenta"
                                value="<?php echo htmlspecialchars($row['numero_cuenta']); ?>" readonly>

                            <label>Cédula / RIF de la Cuenta:</label>
                            <input type="text" id="cedulaRif" value="<?php echo htmlspecialchars($row['cedula_rif']); ?>"
                                readonly>

                            <label>Teléfono de la Cuenta:</label>
                            <input type="text" id="telefonoCuenta"
                                value="<?php echo htmlspecialchars($row['telefono_cuenta']); ?>" readonly>

                            <label>Información Adicional:</label>
                            <input type="text" id="infoAdicional"
                                value="<?php echo htmlspecialchars($row['informacion_adicional']); ?>" readonly>
                        </div>

                        <div class="button-group">
                            <button type="button" class="button-update-account"
                                onclick="abrirModal_actCuentaInstitucion()">Actualizar Cuenta</button>
                            <button type="button" class="button-delete-account"
                                onclick="abrirModal_EliminarCuenta()">Eliminar Cuenta</button>

                        </div>
                    </div>

                    <input type="hidden" id="totalAccounts" value="<?php echo $resultCuentas->num_rows; ?>">
                    <input type="hidden" id="currentAccountIndex" value="0">
                    <input type="hidden" id="cuentasData" value='<?php echo json_encode($cuentas); ?>'>
                <?php } else { ?>
                    <p>No se encontraron cuentas bancarias.</p>
                <?php } ?>
            </div>

            <div class="navigation-buttons">
                <button type="button" class="button-nav" id="prevAccount"><img src="" alt=""><img
                        src="./img/flechasPerfil.svg" alt=""></button>
                <div class="navigation-info" id="navigationInfo">
                    <span id="currentNavigation"></span>
                </div>
                <button type="button" class="button-nav" id="nextAccount"><img src="./img/flechasPerfil.svg"
                        alt=""></button>
            </div>
            <br>
            <button type="button" class="button-add-account" onclick="abrirModal_eggCuenta()">Registrar Otra
                Cuenta</button>
        </form>


    </div>










    <!-- Apartado para los modales -->

    <!-- Modal para agregar cuenta bancaria de la institución -->
    <div class="modals">

        <div id="modal_eggCuenta" class="modal">
            <div class="modal-overlay"></div> <!-- Fondo oscuro agregado -->
            <div class="modal-content">
                <span class="close" onclick="cerrarModal_eggCuenta()">×</span>
                <h2>Agregar cuenta</h2>
                <hr>

                <form id="formAgregarCuenta" action="">
                    <div class="inputsForm">
                        <label for="tituloCuenta" class="label-titulo">A nombre de:</label>
                        <input type="text" id="tituloCuenta" class="input-titulo" required>

                        <label for="tipoOperacion" class="label-titulo">Seleccione el Método de Pago</label>

                        <div class="aggMetodoPago">
                            <select id="selectMetodoPago" class="input-banco" name="MetodoPago" required>
                                <option value="" disabled selected>Seleccione un método de pago </option>
                                <?php if ($resultMetodoPago && $resultMetodoPago->num_rows > 0) {
                                    foreach ($resultMetodoPago as $fila) { ?>
                                        <option value="<?php echo $fila['id_tipoOperacion']; ?>"> <?php echo $fila['tipo']; ?>
                                        </option>
                                <?php }
                                } else {
                                    echo "no se encontraron métodos de pago";
                                } ?>
                            </select>
                            <div class="btn-aggMetodoPago">
                                <button type="button" onclick="abrirAdd_MetdoPago()" class="add_tipo"
                                    id="add_metodopago" value="add_metodopago" name="add_metodopago">
                                    <img src="./img/add.svg" alt="" class="img_add">
                                    <p class="add_tipo-texto"></p>
                                </button>
                            </div>
                        </div>

                        <label for="selectBanco" class="label-banco">Nombre del Banco:</label>
                        <div class="aggBanco">
                            <select id="banco" class="input-banco" name="banco" onchange="updateCodigoBanco()" required>
                                <option value="" disabled selected>Seleccione un banco</option>
                                <?php if ($resultBanco && $resultBanco->num_rows > 0) {
                                    foreach ($resultBanco as $fila) { ?>
                                        <option value="<?php echo $fila['id_banco']; ?>"><?php echo $fila['nombre_banco']; ?>
                                        </option>
                                <?php }
                                } else {
                                    echo "No hay bancos disponibles.";
                                } ?>
                            </select>
                            <div class="agregarBanco">
                                <button type="button" onclick="abrirAdd_banco()" class="add_tipo" id="add_metodopago"
                                    value="add_metodopago" name="add_metodopago">
                                    <img src="./img/add.svg" alt="" class="img_add">
                                    <p class="add_tipo-texto"></p>
                                </button>
                            </div>
                        </div>

                        <label for="numeroCuenta" class="label-numero">Número de Cuenta:</label>
                        <input type="number" id="nro_cuenta" maxlength="21" oninput="validateAccountNumber()"
                            class="input-numero" required>

                        <div class="account-type-container">
                            <label class="label-tipo">Cuenta:</label>
                            <div class="checkbox-container">
                                <div>
                                    <label for="tipoCorriente">Corriente</label>
                                    <input type="checkbox" id="tipoCorriente" class="input-tipo" name="tipoCuenta"
                                        value="corriente" onclick="toggleCheckbox(this)">
                                </div>
                                <div>
                                    <label for="tipoAhorro">Ahorro</label>
                                    <input type="checkbox" id="tipoAhorro" class="input-tipo" name="tipoCuenta"
                                        value="ahorro" onclick="toggleCheckbox(this)">
                                </div>
                            </div>
                        </div>

                        <label for="cedulaRif" class="label-telefono">Cedula o Rif de la cuenta:</label>

                        <div style="display: flex; align-items: center;">
                            <select id="tipoRif" name="tipo_rif" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="V">V</option>
                                <option value="E">E</option>
                                <option value="J">J</option>
                                <option value="G">G</option>
                                <option value="P">P</option>
                            </select>
                            <input type="text" id="cedulaRif" name="cedula_rif" style="margin-left: 10px;" required>
                        </div>



                        <label for="selectTelefono" class="label-telefono">Teléfono asociado a la cuenta:</label>
                        <input type="text" id="telefonoCuenta" name="name_telefono">

                        <label for="informacion_adicional" class="label-info">Agregar información adicional
                            (opcional):</label>
                        <input type="text" id="informacion_adicional" name="info_adicional">
                    </div>
                </form>

                <hr>
                <div class="modalbuttons">
                    <button type="button" class='cancelar' onclick="cerrarModal_eggCuenta()">cancelar</button>
                    <button class="eliminar" type="button" onclick="agregarNuevaCuenta()">agregar</button>
                </div>
            </div>
        </div>



        <!-- Modal para actualizar cuenta bancaria de la institución -->
        <div class="modals">
            <div id="modal_actCuentaInstitucion" class="modal">
                <div class="modal-overlay"></div>
                <div class="modal-content">
                    <span class="close" onclick="cerrarModal_actCuentaInstitucion()">×</span>
                    <h2>Actualizar cuenta</h2>
                    <hr>

                    <form id="formActualizarCuenta" action="">
                        <div class="inputsForm">
                            <input type="hidden" id="idCuentaUpdate" value="">
                            <label for="tituloCuenta" class="label-titulo">A nombre de:</label>
                            <input type="text" id="tituloCuentaUpdate" class="input-titulo" required>



                            <label for="tipoOperacion" class="label-titulo">Seleccione el Método de Pago</label>
                            <div class="aggMetodoPago">
                                <select id="selectMetodoPagoUpdate" class="input-banco" name="MetodoPago" required>
                                    <option value="" disabled selected>Seleccione un método de pago </option>
                                    <?php if ($resultMetodoPago && $resultMetodoPago->num_rows > 0) {
                                        foreach ($resultMetodoPago as $fila) { ?>
                                            <option value="<?php echo $fila['id_tipoOperacion']; ?>">
                                                <?php echo htmlspecialchars($fila['tipo']); ?>
                                            </option>
                                    <?php }
                                    } ?>
                                </select>
                                <div class="btn-aggMetodoPago">
                                    <button type="button" onclick="abrirAdd_MetdoPago()" class="add_tipo"
                                        id="add_metodopago" value="add_metodopago" name="add_metodopago">
                                        <img src="./img/add.svg" alt="" class="img_add">
                                        <p class="add_tipo-texto"></p>
                                    </button>
                                </div>
                            </div>

                            <label for="selectBanco" class="label-banco">Nombre del Banco:</label>
                            <div class="aggBanco">
                                <select id="bancoUpdate" class="input-banco" name="banco" required>
                                    <option value="" disabled selected>Seleccione un banco</option>
                                    <?php if ($resultBanco && $resultBanco->num_rows > 0) {
                                        foreach ($resultBanco as $fila) { ?>
                                            <option value="<?php echo $fila['id_banco']; ?>">
                                                <?php echo htmlspecialchars($fila['nombre_banco']); ?>
                                            </option>
                                    <?php }
                                    } ?>
                                </select>
                                <div class="agregarBanco">
                                    <button type="button" onclick="abrirAdd_banco()" class="add_tipo"
                                        id="add_metodopago" value="add_metodopago" name="add_metodopago">
                                        <img src="./img/add.svg" alt="" class="img_add">
                                        <p class="add_tipo-texto"></p>
                                    </button>
                                </div>
                            </div>

                            <label for="numeroCuenta" class="label-numero">Número de Cuenta:</label>
                            <input type="number" id="nro_cuentaUpdate" class="input-numero" required>

                            <div class="account-type-container">
                                <label class="label-tipo">Cuenta:</label>
                                <div class="checkbox-container">
                                    <div>
                                        <label for="tipoCorrienteUpdate">Corriente</label>
                                        <input type="checkbox" id="tipoCorrienteUpdate" class="input-tipo"
                                            name="tipoCuenta" value="corriente">
                                    </div>
                                    <div>
                                        <label for="tipoAhorroUpdate">Ahorro</label>
                                        <input type="checkbox" id="tipoAhorroUpdate" class="input-tipo"
                                            name="tipoCuenta" value="ahorro">
                                    </div>
                                </div>
                            </div>

                            <label for="cedulaRifUpdate" class="label-telefono">Cedula o Rif de la cuenta:</label>
                            <div style="display: flex; align-items: center;">
                                <select id="tipoRifUpdate" name="tipo_rif" required>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="V">V</option>
                                    <option value="E">E</option>
                                    <option value="J">J</option>
                                    <option value="G">G</option>
                                    <option value="P">P</option>
                                </select>
                                <input type="text" id="cedulaRifUpdate" name="cedula_rif" style="margin-left: 10px;"
                                    required>
                            </div>

                            <label for="selectTelefonoUpdate" class="label-telefono">Teléfono asociado a la
                                cuenta:</label>
                            <input type="text" id="telefonoCuentaUpdate" name="name_telefono">

                            <label for="informacion_adicionalUpdate" class="label-info">Agregar información adicional
                                (opcional):</label>
                            <input type="text" id="informacion_adicionalUpdate" name="info_adicional">
                        </div>
                    </form>

                    <hr>
                    <div class="modalbuttons">
                        <button type="button" class='cancelar'
                            onclick="cerrarModal_actCuentaInstitucion()">cancelar</button>
                        <button class="eliminar" type="button" onclick="actCuentaInstitucion()">Actualizar</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal para actualizar la información del sistema -->
        <div class="modals">
            <div id="modal_actInfoSistema" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="cerrarModal_infoSistema()">×</span>
                    <h2>Actualizar información del sistema</h2>
                    <hr>
                    <form id="formUpdateSistema" enctype="multipart/form-data">
                        <?php
                        // Obtener datos de la tabla ipspuptyab_sistema
                        $querySistema = "SELECT * FROM ipspuptyab_sistema";
                        $resultSistema = $connection->query($querySistema);

                        if ($resultSistema->num_rows > 0) {
                            while ($row = $resultSistema->fetch_assoc()) {
                        ?>
                                <div class="form-group">
                                    <label for="nombreSistemaUpdate">Nombre del Sistema:</label>
                                    <input type="text" id="nombreSistemaUpdate" name="nombreSistemaUpdate"
                                        value="<?php echo htmlspecialchars($row['nombre_sistema'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="tituloSistemaUpdate">Título Descriptivo:</label>
                                    <input type="text" id="tituloSistemaUpdate" name="tituloSistemaUpdate"
                                        value="<?php echo htmlspecialchars($row['titulo_sistema'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="subtituloSistemaUpdate">Subtítulo Descriptivo:</label>
                                    <input type="text" id="subtituloSistemaUpdate" name="subtituloSistemaUpdate"
                                        value="<?php echo htmlspecialchars($row['subtitulo_sistema'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>

                                <div class="form-group">
                                    <div class="file-input-container">
                                        <label for="logoSistemaInput" class="custom-file-upload">
                                            <img src="./img/capture-white.svg" alt="Cargar Logo">
                                        </label>
                                        <input hidden type="file" id="logoSistemaInput" name="logoSistemaInput"
                                            onchange="previsualizarLogo(event)">
                                    </div>

                                    <div class="image-preview-container">
                                        <label for="logoSistemaPreview" style="width: 100%;"><!-- Se asegura que el label abarque el 100% -->
                                            <img id="logoSistemaPreview" class="logo-image-preview">
                                        </label>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo "<p>No se encontraron datos en la tabla.</p>";
                        }
                        ?>
                        <div class="buttonsModal">
                            <hr>
                            <div class="modalbuttons">
                                <button type="button" class="cancelar" onclick="cerrarModal_infoSistema()">Cancelar</button>
                                <button class="eliminar" type="button" onclick="actualizarInfoSistema()">Actualizar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Modal para actualizar la información de la institución -->
        <div id="modal_actInfoInstitucion" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarModal_infoIntitucion()">×</span>
                <h2>Actualizar información del instituto</h2>
                <hr>

                <form id="formUpdateInfo" enctype="multipart/form-data">
                    <?php
                    $queryInfo = "SELECT * FROM ipspuptyab_info";
                    $resultInfo = $connection->query($queryInfo);

                    if ($resultInfo && $resultInfo->num_rows > 0) {
                        $row = $resultInfo->fetch_assoc(); // Extrae solo un registro
                    ?>
                        <div class="form-group">
                            <label for="razon_social">Razón Social:</label>
                            <input type="text" id="razon_social" name="razon_social"
                                value="<?php echo htmlspecialchars($row['razon_social'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="siglas">Siglas:</label>
                            <input type="text" id="siglas" name="siglas"
                                value="<?php echo htmlspecialchars($row['siglas'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección:</label>
                            <input type="text" id="direccion" name="direccion"
                                value="<?php echo htmlspecialchars($row['direccion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="rif_institucion">RIF:</label>
                            <input type="text" id="rif_institucion" name="rif_institucion"
                                value="<?php echo htmlspecialchars($row['rif_institucion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfonos:</label>
                            <input type="text" id="telefono" name="telefono"
                                value="<?php echo htmlspecialchars($row['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo Electrónico:</label>
                            <input type="email" id="correo" name="correo"
                                value="<?php echo htmlspecialchars($row['correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="firma_digital" class="custom-file-upload">

                                <img src="./img/capture-white.svg" alt="Cargar Firma">
                            </label>
                            <input type="file" id="firma_digital" name="firma_digital"
                                accept=".png, .jpg, .jpeg, .ico, .svg" onchange="previsualizarFirma(event)"
                                style="display: none;">
                            <div class="image-preview-container">
                                <img id="previsualizacionFirma" class="logo-image-preview">
                            </div>
                        </div>
                    <?php } else { ?>
                        <p>No se encontraron datos en la tabla.</p>
                    <?php } ?>
                    <div class="buttonsModal">
                        <hr>
                        <div class="modalbuttons">
                            <button type="button" class='cancelar'
                                onclick="cerrarModal_infoIntitucion()">Cancelar</button>
                            <button class="eliminar" type="button"
                                onclick="actualizar_infoInstitucion()">Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>





        <!-- Modales extras -->

        <!-- agregar nuevo banco -->

        <div id="modal_nuevoBanco" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarModal_banco()">×</span>
                <h2>Registrar Nuevo Banco</h2>
                <form id="agregarNuevoBanco" onsubmit="return false;">
                    <div class="brmodal">
                        <label for="idBanco">Código de banco</label>
                        <input type="text" id="idBanco" name="idBanco" placeholder="Código de Banco" required>
                    </div>
                    <div class="brmodal">
                        <label for="nombreBanco">Nombre del Banco</label>
                        <input type="text" id="nombreBanco" name="nombreBanco" placeholder="Nombre completo del banco"
                            required>
                    </div>
                    <button type="button" onclick="aggNewBanco()">Agregar</button>
                </form>
                <div class="mensaje_banco"></div> <!-- Contenedor para mensajes -->
            </div>
        </div>


        <div id="modalMensaje_general" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarMensaje_general()">X</span>
                <h2>Se ha registrado exitosamente!</h2>
                <div class="mensajeSecundario"></div> <!-- Manten este div -->
            </div>
        </div>





        <!-- MODAL AGREGAR METODO DE PAGO -->

        <!-- MODAL METODO DE PAGO -->
        <div id="modal_MetodoPago" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarModal_mp()">×</span>
                <h2>Registrar Método de Pago</h2>
                <form id="agregarNuevoMetodoPago">
                    <label for="metodoPagoInput">Agregar Nuevo Método de Pago</label>
                    <div id="mensaje_metodoPago"></div>
                    <input type="text" id="metodoPagoInput" name="metodoPago" placeholder="Método de Pago">

                    <label for="categoriaPago">Categoría del Pago</label>
                    <div>
                        <input type="radio" id="digital" name="categoriaPago" value="DIGITAL">
                        <label for="digital">Digital</label>
                    </div>

                    <button type="button" onclick="aggNewMetodoPago()">Agregar</button>
                </form>
            </div>
        </div>

        <!-- Contenedor para los mensajes -->
        <div id="mensaje_metodoPago"></div>




        <!-- Contenedor para eliminar cuenta del sistema -->
        <div id="modal_eliminarCuenta" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarModal_EliminarCuenta()">×</span>
                <h2>Eliminar cuenta</h2>
                <p>¿Estás seguro de que deseas eliminar esta cuenta?</p>
                <input type="hidden" id="idCuentaEliminar" value=""> <!-- Campo oculto para el ID de la cuenta -->
                <div class="buttonDelete">
                    <button type="button" onclick="cerrarModal_EliminarCuenta()">No, cancelar</button>
                    <button type="button" onclick="confirmarEliminarCuenta()">Sí, eliminar</button>
                </div>


            </div>
        </div>
        <footer>
            <button class="btn-download" onclick="ManualUsuarioAdmin()">Descargar Manual de Usuario </button>
            <button class="btn-download" onclick="ManualSistemaAdmin()">Descargar Manual de Sistema </button>
        </footer>

</body>

</html>