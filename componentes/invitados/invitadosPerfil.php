<?php
session_start();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SACIPS | PERFIL</title>
    <link rel="stylesheet" href="./style/user.css">
    <link rel="stylesheet" href="./style/modales.css">
    <link rel="stylesheet" href="./style/descargar_manuales.css">
</head>

<body>

    <div class="perfilBody">

        <div class="headerPerfil">
            <div class="navLogo">

            </div>

            <a href="./componentes/invitados/invitadosLogout.php" class="salirInv">Cerrar Sesión</a>
        </div>
        <div class="perfilFigura">
            <div class="perfilCuadro">

                <div class="fotoText">
                    <img class="imgTXT" src="./img/person.svg" alt="">
                    <p class="textoFoto">¡Bienvenido
                        <span><?php echo ucfirst($_SESSION['nombre']) . "!"; ?></span>
                    </p>
                </div>

            </div>
        </div>

        <div class="sobreTi">


            <div class="cajas">

                <div class="cajaSeparacion">

                    <div class="infoCaja">
                        <div class="textoCaja">
                            <p>Sobre ti</p>
                        </div>
                        <div class="infoBr">
                            <label for="">Nombre:</label>
                            <div class="br"></div>
                            <input readonly type="text"
                                value="<?php echo ucfirst($_SESSION['nombre']) . ' ' . ucfirst($_SESSION['apellido']); ?>">
                        </div>
                        <div class="infoBr">
                            <label for="">Cédula:</label>
                            <div class="br"></div>
                            <input readonly type="text" value="<?php echo $_SESSION['cedula'] ?>">
                        </div>
                        <div class="infoBr">
                            <label for="">Usuario: </label>
                            <div class="br"></div>
                            <input readonly type="text" value="<?php echo $_SESSION['nombre_usuario'] ?>">
                        </div>
                    </div>


                </div>
            </div>



            <div class="cajas">
                <div class="cajaSeparacion">
                    <div class="textoCaja">
                        <p>Teléfono</p>
                        <input type="number" name="telefono" id="telefonoPerfil"
                            value="<?php echo $_SESSION['telefono']; ?>" readonly>
                    </div>
                    <button type="button" class="imgContainerPerfil" id="telefonoAbrir" onclick="abrirModal_telefono()">
                        <img src="./img/editar.png" alt="" class="imgCaja">
                    </button>
                </div>
            </div>


            <div class="cajas">
                <div class="cajaSeparacion">
                    <div class="textoCaja">
                        <p>Correo Electrónico</p>
                        <input type="email" name="correoPerfil" id="correoPerfil"
                            value="<?php echo $_SESSION['correo']; ?>">
                    </div>
                    <button type="button" class="imgContainerPerfil" id="correoAbrir" onclick="abrirModal_correo()">
                        <img src="./img/editar.png" alt="" class="imgCaja">
                    </button>
                </div>
            </div>

            <div class="cajas">
                <div class="cajaSeparacion">
                    <div class="textoCaja">
                        <p>Cambiar Clave</p>
                        <input type="password" name="clave" id="cambioClave"
                            value="<?php echo "**********" ?>" readonly>
                    </div>
                    <button type="button" class="imgContainerPerfil" id="telefonoAbrir" onclick="abrirModal_clave()">
                        <img src="./img/editar.png" alt="" class="imgCaja">
                    </button>
                </div>
            </div>
            <!-- MODALES DE APERTURA -->



            <!-- Modal para editar correo electrónico -->
            <span class="modal" id="correoModal">
                <div class="modal-content">
                    <span class="close" onclick="cerrarModal_correo();">×</span>
                    <div class="tituloModal">
                        <h2 class="modTitulo">Editar Correo Electrónico</h2>
                        <img src="./img/email.svg" alt="">
                    </div>
                    <p>Como usuario afiliado, debe esperar a que un administrador apruebe el cambio de información</p>
                    <div class="mensajeAdvertencia" id="mensajeRespuestaCorreo"></div>

                    <form id="formulariomodalCorreo">
                        <div class="brForm">
                            <div>
                                <label for="nuevoCorreo">Correo Electrónico</label>
                                <input type="email" name="correoEditar" id="nuevoCorreo"
                                    value="<?php echo $_SESSION['correo'] ?>">
                            </div>
                        </div>
                        <div class="brForm">
                            <div>
                                <label for="confirmarCorreo">Confirmar Correo</label>
                                <input type="email" name="correoEditar" id="confirmarCorreo"
                                    placeholder="<?php echo $_SESSION['correo'] ?>">
                            </div>
                        </div>
                        <div class="brFormBTN">

                            <button type="button" onclick="submitCorreo();">Continuar</button>
                        </div>
                    </form>
                </div>
            </span>


            <span class="modal" id="telefonoModal">
                <div class="modal-content">
                    <span class="close" onclick="cerrarModal_telefono();">×</span>
                    <div class="tituloModal">
                        <h2 class="modTitulo">Cambiar numero telefónico</h2>
                        <img src="./img/phone.svg" alt="">
                    </div>
                    <div>
                        <p>Como usuario afiliado, debe esperar a que un administrador apruebe el cambio de información
                        </p>
                    </div>
                    <div class="mensajeAdvertencia" id="mensajeRespuestaTelefono"></div>

                    <form id="formulariomodalTelefono">
                        <div class="brForm">
                            <div>
                                <label for="nuevoTelefono">Teléfono</label>
                                <input type="tel" name="telefonoEditar" id="nuevoTelefono"
                                    value="<?php echo $_SESSION['telefono'] ?>">
                            </div>
                        </div>
                        <div class="brFormBTN">

                            <button type="button" onclick="submitTelefono();">Continuar</button>
                        </div>
                    </form>
                </div>
            </span>

            <span class="modal" id="claveModal">
                <div class="modal-content">
                    <span class="close" onclick="cerrarModal_clave();">×</span>
                    <div class="tituloModal">
                        <h2 class="modTitulo">Cambiar Clave/Contraseña</h2>
                        <img src="./img/clave.svg" alt="">
                    </div>
                    <div>
                        <p>Tu clave es importante, recuerda siempre anotarla y no olvidarla.</p>
                    </div>
                    <div class="mensajeAdvertencia" id="mensajeRespuestaClave"></div>

                    <form id="formulariomodalClave">
                        <div class="brForm">
                            <div>
                                <label for="claveAnterior">Clave anterior</label>
                                <input type="password" name="claveAnterior" id="claveAnterior" value="">
                            </div>
                        </div>
                        <div class="brForm">
                            <div>
                                <label for="nuevaClave">Nueva clave</label>
                                <input type="password" name="nuevaClave" id="nuevaClave" value="">
                            </div>
                        </div>
                        <div class="brForm">
                            <div>
                                <label for="confirmClave">Confirmar nueva clave</label>
                                <input type="password" name="confirmClave" id="confirmClave" value="">
                            </div>
                        </div>
                        <div class="brFormBTN">

                            <button type="button" class="btnContinuar" onclick="submitClave();">Continuar</button>
                        </div>
                    </form>
                </div>
            </span>

            <footer>
                <button class="btn-download" onclick="ManualUsuarioAfi_Invi()">Descargar Manual de Usuario </button>
            </footer>




            <script src="./js/ajax.js"></script>
</body>

</html>