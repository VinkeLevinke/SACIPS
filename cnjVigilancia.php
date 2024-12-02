<!-- Modulo de los afiliaddos(Gabriel)-->

<?php
include 'componentes/conexiones/conexionbd.php';
session_start();

if (!$_SESSION['tipo_usuario'] || $_SESSION['tipo_usuario'] != 4) {
    header("Location: ./componentes/cnjVigilancia/vigilanciaLogout.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/IPSPUPTYAB-LOGO.ico" type="image/x-icon">
    <link rel="stylesheet" href="./style/vigilancia.css">
    <link rel="stylesheet" href="./style/modales.css">
    <title>SACIPS | Vigilancia</title>
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

<body>
    <header class='headerVigilancia'>

        <div class="navPerfil" id="perfilPointer">

            <div class="perfil" onclick="abrirModalPerfil()">

                <img src="./img/UserGestion.png" alt="" class="perfilVgImg">

                <div class="navPerfil-text">

                    <p class="h1"><?php echo $_SESSION['nombre'] . " " . $_SESSION['apellido']; ?></p>

                    <p class="h3"><?php
                                    if ($_SESSION['tipo_usuario'] == 4) {
                                        echo 'Vigilante';
                                    }; ?>
                    </p>

                </div>
            </div>

        </div>
        <h1 class="tituloVigilancia">Consejo de Vigilancia</h1>

        <div class="navSacipsLogo">
            <p>SACIPS</p>
            <img src="./img/IPSPUPTYAB-LOGO.ico" alt="" class="sacipsLogo">

        </div>
    </header>
    <div class="navButtons">
        <button type="button" id="btn-aporte" onclick="cambiarTab('aporte')"><img src="./img/aporteV.png" alt="">
            <p>Aporte</p>
        </button>
        <div class="br"></div>
        <button type="button" id="btn-egreso" onclick="cambiarTab('egreso')"><img src="./img/egresoV.png" alt="">
            <p>Egreso</p>
        </button>
    </div>


    <section class="bodyVgl">


        <div class="tablasVistas">
            <!-- TABLA Vistas-->



            <div class="vistaReciente">
                <div class="vistaHeader">
                    <h2>Movimiento reciente</h2>
                </div>
                <!-- TABLA VIGILANCIA-->
                <div class="tablaVista" id="contenidoUltimoMovimiento">
                    <!-- Aquí se cargarán los datos de la última transacción -->
                </div>
            </div>



            <!-- TABLA VIGILANCIA-->

            <div class="brBody"></div>


            <div class="tablaVigilancia">

                <div class="navTituloVigi">

                    <h2>Movimientos | Aportes</h2>

                    <div class="offset">
                        <button type="button">&lt;</button>
                        <p>&nbsp;1 |&nbsp;</p>
                        <button type="button">&gt;</button>
                    </div>

                </div>

                <!-- TABLA VIGILANCIA-->
                <div id="tablaContainer">
                    <table class="tableMovsVigilancia">
                        <!-- tableMovsVigilancia -->
                        <thead>
                            <th>Aporte</th>
                            <th>Fecha / Hora</th>
                            <th>Monto</th>
                            <th>Tipo</th>
                            <th>Realizado por</th>
                            <th>Banco</th>
                            <th>Nro Cuenta</th>
                            <th>Referencia</th>
                            <th>Concepto</th>
                            <th>Estado</th>
                            <th></th>

                        </thead>
                        <tbody>
                            <tr>
                                <th></th>
                                <!-- Aqui quiero que muestre que tipo de aporte fue hecho, si por Donacion, estatuto o aporte patronal -->
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td> <button type="button">REVISAR</button></td>
                            </tr>
                        </tbody>
                    </table> <!-- tableMovsVigilancia -->

                </div>
            </div><!-- TABLA VIGILANCIA-->

            <div class="brBody"></div>

            <div class="vistaReciente">
                <!-- TABLA VIGILANCIA-->

                <div class="dolarPrecio">
                    <p>Precio dolar:</p>
                    <p>al BCV:</p>
                    <p>42.90</p>
                    <p>ultima actualización</p>
                    <p>30/10/2024 09:30:00 AM</p>
                </div>
                <hr>
                <div class="busqueda">

                    <button type="button">
                        <img src="./img/Lupa.svg" alt="">
                        <p>Hacer una busqueda</p>
                    </button>
                </div>

            </div><!-- TABLA VIGILANCIA-->


        </div> <!-- TABLA vistas-->



    </section>


    <div id="abrirModalPerfil" class="modal">

        <div class="modal-content"> <span class="close" onclick="cerrarModalPerfil()">×</span>
            <div class="headerUpdate">
                <h2>Perfil</h2>
            </div>
            <hr>

            <div class="cajaCompleta">

                <div class="boxUpdate">
                    <h2>Nombre y Apellido</h2>
                    <div class="cajaPerfil">
                        <div class="cajaBoton">
                            <p id="nombre"><?php echo $_SESSION['nombre'] . " " . $_SESSION['apellido']; ?></p>
                            <button type="button" id="btn-edit"
                                onclick="modalUpdate('Nombre', '<?php echo $_SESSION['nombre']; ?>', '<?php echo $_SESSION['apellido']; ?>')"><img
                                    src="./img/editar.png" alt=""></button>
                        </div>
                    </div>
                </div>

                <div class="boxUpdate">
                    <h2>Nombre de usuario</h2>
                    <div class="cajaPerfil">
                        <div class="cajaBoton">
                            <p id="nombre"><?php echo $_SESSION['nombre_usuario']; ?></p>
                            <button type="button" id="btn-edit"
                                onclick="modalUpdate('Nombre', '<?php echo $_SESSION['nombre']; ?>', '<?php echo $_SESSION['apellido']; ?>')"><img
                                    src="./img/editar.png" alt=""></button>
                        </div>
                    </div>
                </div>

                <div class="boxUpdate">
                    <h2>Cédula</h2>
                    <div class="cajaPerfil">
                        <div class="cajaBoton">
                            <p id="cedula"><?php echo $_SESSION['cedula']; ?></p> <button type="button" id="btn-edit"
                                onclick="modalUpdate('Cédula', '<?php echo $_SESSION['cedula']; ?>')"><img
                                    src="./img/editar.png" alt=""></button>
                        </div>
                    </div>
                </div>

                <div class="boxUpdate">
                    <h2>Correo</h2>
                    <div class="cajaPerfil">
                        <div class="cajaBoton">
                            <p id="correo"><?php echo $_SESSION['correo']; ?></p> <button type="button" id="btn-edit"
                                onclick="modalUpdate('Correo', '<?php echo $_SESSION['correo']; ?>')"><img
                                    src="./img/editar.png" alt=""></button>
                        </div>
                    </div>
                </div>

                <div class="boxUpdate">
                    <h2>Telefono</h2>
                    <div class="cajaPerfil">
                        <div class="cajaBoton">
                            <p id="telefono"><?php echo $_SESSION['telefono']; ?></p> <button type="button"
                                id="btn-edit"
                                onclick="modalUpdate('Telefono', '<?php echo $_SESSION['telefono']; ?>')"><img
                                    src="./img/editar.png" alt=""></button>
                        </div>
                    </div>
                </div>

                <div class="boxUpdate">
                    <h2>Cambiar clave</h2>
                    <div class="cajaPerfil">
                        <div class="cajaBoton">
                            <p id="clave">**********</p>

                            <button type="button" id="btn-edit" onclick="abrirModalCambiarClave()"><img
                                    src="./img/editar.png" alt="">
                            </button>

                        </div>
                    </div>
                </div>

                <div class="logout">
                    <button id="logout_vig" onclick="cerrarSesion()">
                        <img src="./img/logout.svg" alt="">
                        <p>Cerrar sesión</p>
                    </button>
                </div>
            </div><!-- Modal content -->

        </div>
    </div>

    <div id="modalReciboPago" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalReciboPago()">×</span>
            <div class="headerUpdate">
                <h2>Comentar</h2>
            </div>
            <hr>
            <div class="reciboContenido">
                <h3>Detalles del Recibo</h3>
                <p><strong>ID:</strong> <span id="reciboID"></span></p>
                <p><strong>Monto:</strong> <span id="reciboMontoPago"></span></p>
                <p><strong>Concepto:</strong> <span id="reciboConceptoPago"></span></p>
                <p><strong>Fecha:</strong> <span id="reciboFechaPago"></span></p>
                <p><strong>Hora:</strong> <span id="reciboHoraPago"></span></p>
                <p><strong>Realizado por:</strong> <span id="reciboRealizadoPorPago"></span></p>
                <hr>
                <h3>Comentarios</h3>
                <textarea id="comentarioVigilantePago" rows="4" placeholder="Escriba su comentario aquí..."></textarea>
                <hr>
                <h3>Tipo de Recibo</h3>
                <select id="tipoRecibo" disabled>
                    <option value="aporte_afiliado">Aporte Afiliado</option>
                    <option value="aporte_donacion">Aporte Donación</option>
                    <option value="aporte_patronal">Aporte Patronal</option>
                    <option value="egreso">Egreso</option>
                </select>
                <div class="button-container">
                    <button type="button" id="btn-comentar-pago" onclick="guardarComentarioPago()">Guardar
                        Comentario</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal para actualizar información -->
    <div class="act">
        <div id="formActualizar" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarModalActualizar()">×</span>
                <p>Actualizar:</p>
                <form id="actualizarInfoVigilante">
                    <div class="brUpdate">
                        <div class="inputGroup">
                            <input id="inputId-persona" type="hidden" value="<?php echo $_SESSION['id_persona']; ?>">
                            <label for="inputActualizar" id="labelActualizar"></label>
                            <input type="text" id="inputActualizar">
                        </div>
                        <div class="inputGroup" id="apellidoGroup" style="display: none;">
                            <label for="inputApellido" id="labelApellido">Actualizar Apellido</label>
                            <input type="text" id="inputApellido">
                        </div>
                        <div class="btn-update">
                            <button type="button" class="btn-closed" onclick="cerrarModalActualizar()">Cerrar</button>
                            <button type="button" onclick="guardarCambios()">Guardar</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para cambiar clave -->
        <div id="modalCambiarClave" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarModalCambiarClave()">×</span>
                <h2>Cambiar Clave</h2>
                <form id="formCambiarClave">
                    <div class="inputGroup">
                        <label for="claveActual">Clave Actual:</label>
                        <input type="password" id="claveActual" required>
                    </div>
                    <div class="inputGroup">
                        <label for="claveNueva">Nueva Clave:</label>
                        <input type="password" id="claveNueva" required>
                    </div>
                    <div class="inputGroup">
                        <label for="confirmarClave">Confirmar Nueva Clave:</label>
                        <input type="password" id="confirmarClave" required>
                    </div>
                    <div class="btn-update">
                        <button type="button" class="btn-closed" onclick="cerrarModalCambiarClave()">Cerrar</button>
                        <button type="button" onclick="guardarCambioClave()">Guardar</button>
                    </div>
                </form>
            </div>
        </div>


    </div>

    <div id="hacerBusqueda" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalEgreso()">×</span>
            <h2>Buscar</h2>

            <div id="mensajeRespuesta"></div>
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
     
    <!-- VENTANAS MODALES FIN -->
    <script src="./js/menus.js"></script>
    <script src="./js/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function() {
            // Cargar la última pestaña visible
            let lastTab = localStorage.getItem('lastTab') || 'aporte';
            cambiarTab(lastTab);
        });

        function cambiarTab(tab) {
            localStorage.setItem('lastTab', tab);

            // Animación de desvanecimiento
            $('#tablaContainer').fadeOut(100, function() {
                if (tab === 'aporte') {
                    $('#btn-aporte').addClass('active');
                    $('#btn-egreso').removeClass('active');
                    obtenerAportes();
                    $('.navTituloVigi h2').text('Movimientos | Aportes'); // Cambia el título
                } else {
                    $('#btn-egreso').addClass('active');
                    $('#btn-aporte').removeClass('active');
                    obtenerEgresos();
                    $('.navTituloVigi h2').text('Movimientos | Egresos'); // Cambia el título
                }
                $('#tablaContainer').fadeIn(100); // Vuelve a mostrar la tabla
            });
        }


        function obtenerAportes() {
            $.ajax({
                url: './componentes/cnjVigilancia/obtener_aportes.php',
                method: 'GET',
                success: function(data) {
                    $('#tablaContainer').html(data);
                },
                error: function() {
                    $('#tablaContainer').html('<p>Error al cargar los datos de aportes.</p>');
                }
            });
        }

        function obtenerEgresos() {
            $.ajax({
                url: './componentes/cnjVigilancia/obtener_egresos.php',
                method: 'GET',
                success: function(data) {
                    $('#tablaContainer').html(data);
                },
                error: function() {
                    $('#tablaContainer').html('<p>Error al cargar los datos de egresos.</p>');
                }
            });
        }


        $(document).ready(function() {
            // Cargar la última pestaña visible
            let lastTab = localStorage.getItem('lastTab') || 'aporte';
            cambiarTab(lastTab);

            // Cargar el último movimiento al inicio
            obtenerUltimoMovimiento();
        });

        function obtenerUltimoMovimiento() {
            $.ajax({
                url: './componentes/cnjVigilancia/obtener_ultimo_movimiento.php',
                method: 'GET',
                success: function(data) {
                    $('#contenidoUltimoMovimiento').html(data);
                },
                error: function() {
                    $('#contenidoUltimoMovimiento').html(
                        '<p>Error al cargar los datos del último movimiento.</p>');
                }
            });
        }
    </script>
</body>

</html>