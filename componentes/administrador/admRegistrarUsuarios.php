<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <link rel="stylesheet" href="./style/registrar_usuario.css">
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

<body class="body">
    <div class="formShapeRegistro">
        <div class="registro-container">

            <h1 class="registro-titulo">Registro de Persona</h1>
            <form id="registro-form" action="./componentes/administrador/RegistrarUsuarios.php" method="POST">

               
                <?php
                $total_sections = 8; // Cambia este valor si agregas o quitas secciones
                $mensaje = [
                    'Información personal',
                    'Información de contacto',
                    'Nombre de usuario y clave',
                    'Datos Personales 3/3',
                    'Ubicación | Genero | Fecha de nacimiento', 
                    'Pregunta de Seguridad 1/3',
                    'Pregunta de Seguridad 2/3',
                    'Pregunta de Seguridad 3/3'
                ]; // Asegúrate de que este array contenga los mensajes correspondientes
                ?>
                <div class="contenedor">
                    <div class="tipo_usuario">
                        <!-- Sección del tipo de usuario -->

                        <button type="button" class="registro-boton" id="btnTipoUsuario"
                            onclick="abrirModalTipoUsuario()">Seleccione Tipo de Usuario</button>
                        <input type="hidden" id="tipo_usuario" name="tipo_usuario" required>
                    </div>
                    <hr>
                    <div class="registro-secciones">

                        <div class="registro-seccion active" id="seccion1">

                            <h1 class="paso-titulo">Información personal</h1>


                            <label for="nombre" class="registro-label">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" class="registro-input" required>

                            <label for="apellido" class="registro-label">Apellido:</label>
                            <input type="text" id="apellido" name="apellido" class="registro-input" required>

                            <label for="cedula" class="registro-label">Cédula:</label>
                            <input type="text" id="cedula" name="cedula" class="registro-input" required>

                            <div class="Pagina">
                                <h1>1/8</h1>
                            </div>

                        </div>

                        <div class="registro-seccion" id="seccion2">
                            <h1 class="paso-titulo">Información de contacto</h1>

                            <label for="correo" class="registro-label">Correo Electrónico:</label>
                            <input type="email" id="correo" name="correo" class="registro-input" required>


                            <label for="telefono" class="registro-label">Teléfono:</label>
                            <input type="tel" id="telefono" name="telefono" class="registro-input" required>
                            <div class="Pagina">
                                <h1>2/8</h1>
                            </div>
                        </div>


                        <div class="registro-seccion" id="seccion3">
                            <h1 class="paso-titulo">Nombre de usuario y clave</h1>


                            <label for="usuario" class="registro-label">Nombre de Usuario:</label>
                            <input type="text" id="usuario" name="usuario" class="registro-input" required>

                            <label for="clave" class="registro-label">Clave:</label>
                            <input type="password" id="clave" name="clave" class="registro-input" required>

                            <label for="clave" class="registro-label">Confirmar clave:</label>
                            <input type="password" id="confirmarClave" name="confirmarClave" class="registro-input"
                                required>



                            <div class="Pagina">
                                <h1>3/8</h1>
                            </div>
                        </div>



                        <div class="registro-seccion" id="seccion4">
                            <h1 class="paso-titulo">Datos Personales 3/3</h1>
                            <label for="estatusLaboral" class="registro-label">Estatus laboral:</label>
                            <input type="text" id="estatusLaboral" name="estatusLaboral" class="registro-input"
                                required>

                            <label for="condicionSalud" class="registro-label">Condición de salud:</label>
                            <input type="text" id="condicionSalud" name="condicionSalud" class="registro-input"
                                required>

                            <label for="estadoCivil" class="registro-label">Estado civil:</label>
                            <select id="estadoCivil" name="estadoCivil" class="registro-select" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="Soltero/a">Soltero/a</option>
                                <option value="Casado/a">Casado/a</option>
                                <option value="Divorciado/a">Divorciado/a</option>
                                <option value="Viudo/a">Viudo/a</option>
                            </select>


                            <div class="Pagina">
                                <h1>4/8</h1>
                            </div>
                        </div>

                        <div class="registro-seccion" id="seccion5">
                            <h1 class="paso-titulo">Ubicación | Genero | Fecha de nacimiento</h1>
                            <label for="genero" class="registro-label">Genero:</label>
                            <select id="genero" name="genero" class="registro-select" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>


                            <label for="direccion" class="registro-label">Dirección</label>
                            <input type="text" id="direccion" name="direccion" class="registro-input" required>

                            <label for="fecha_nacimiento" class="registro-label">Fecha de nacimiento:</label>
                            <input type="date" class="registro-input" name="fecha_nacimiento" id="fecha_nacimiento">

                            <div class="Pagina">
                                <h1>5/8</h1>
                            </div>
                        </div>



                        <div class="registro-seccion" id="seccion6">
                            <h1 class="paso-titulo">Preguntas de Seguridad 1/3</h1>
                            <label for="pregunta1" class="registro-label">Pregunta de Seguridad 1:</label>
                            <input type="text" id="pregunta1" name="pregunta1" class="registro-input" required>

                            <label for="respuesta1" class="registro-label">Respuesta:</label>
                            <input type="text" id="respuesta1" name="respuesta1" class="registro-input" required>

                            <div class="Pagina">
                                <h1>6/8</h1>
                            </div>
                        </div>

                        <div class="registro-seccion" id="seccion7">
                            <h1 class="paso-titulo">Preguntas de Seguridad 2/3</h1>
                            <label for="pregunta2" class="registro-label">Pregunta de Seguridad 2:</label>
                            <input type="text" id="pregunta2" name="pregunta2" class="registro-input" required>

                            <label for="respuesta2" class="registro-label">Respuesta:</label>
                            <input type="text" id="respuesta2" name="respuesta2" class="registro-input" required>

                            <div class="Pagina">
                                <h1>7/8</h1>
                            </div>
                        </div>

                        <div class="registro-seccion" id="seccion8">
                            <h1 class="paso-titulo">Preguntas de Seguridad 3/3</h1>
                            <label for="pregunta3" class="registro-label">Pregunta de Seguridad 3:</label>
                            <input type="text" id="pregunta3" name="pregunta3" class="registro-input" required>

                            <label for="respuesta3" class="registro-label">Respuesta:</label>
                            <input type="text" id="respuesta3" name="respuesta3" class="registro-input" required>

                            <button type="button" class="registro-boton" onclick="registrarUsuario()">Registrar</button>
                            <hr>
                            <div class="Pagina">
                                <h1>8/8</h1>
                            </div>

                        </div>


                    </div>
                </div>

                <!-- En el Selector del Paginado, reemplaza el bucle actual -->
                <div class="Selector">
                    <div  class="pgn_button Flecha_L" data-text="Anterior" onclick="prevSection()"><img
                            src="././img/lowArrow.png" alt=""></div>
                            <div style="display: none;" class="pgn_button" data-text="Primera Página" onclick="Paginado('primera')">
                        <p >...</p>
                    </div>
                    <?php
                    for ($i = 1; $i <= $total_sections; $i++) {
                        $activeClass = ($i == 1) ? 'PaginaActiva' : '';
                        echo '<div class="pgn_button ' . $activeClass . '" data-text="' . $mensaje[$i - 1] . '" onclick="Paginado(`' . $i . '`)"><p>' . $i . '</p></div>';
                    }
                    ?>
                    
                    <div  style="display: none;" class="pgn_button" data-text="Última Página" onclick="Paginado('ultima')">
                        <p>...</p>
                    </div>
                    <div class="pgn_button Flecha_R" data-text="Siguiente" onclick="nextSection()"><img
                            src="././img/lowArrow.png" alt=""></div>
                </div>

            </form>

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
    <div id="modalUsuarioRegistro" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal_registroUsuario()">×</span>
            <h2>Error al registrar</h2>
            <hr>
            <br>
            <div id="alertaErrores"></div> <!-- Este se usará para mostrar los errores -->
            <br>
            <hr>
            <div class="buttonDelete">
                <button type="button" onclick="cerrarModal_registroUsuario()">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="modalTipoUsuario" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalTipoUsuario()">×</span>
            <h2>Seleccione el Tipo de Usuario</h2>
            <div class="tipoUsuariosBtn">
                <button type="button" class="option-button" onclick="selectTipoUsuario(1, 'Afiliado')">Afiliado</button>
                <div></div>
                <button type="button" class="option-button" onclick="selectTipoUsuario(2, 'Director')">Director</button>
                <div></div>
                <button type="button" class="option-button" onclick="selectTipoUsuario(3, 'Invitado')">Invitado</button>
                <div></div>
                <button type="button" class="option-button"
                    onclick="selectTipoUsuario(4, 'Vigilancia')">Vigilancia</button>
            </div>
        </div>
    </div>





    <script src="script.js"></script>
</body>

</html>