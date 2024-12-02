<?php
include 'componentes/conexiones/conexionbd.php';
session_start();

if (!$_SESSION['tipo_usuario'] || $_SESSION['tipo_usuario'] != 1) {
    header("Location: ./componentes/afiliados/afiliadosLogout.php");
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./style/tablas.css">
    <link rel="stylesheet" href="../style/tablas.css">
    <link rel="stylesheet" href="./style/style-afiliados2.css">
    <link rel="stylesheet" href="./style/style.css">
    <script src="./js/jquery-3.7.1.min.js"></script>
    <title>SACIPS | Afiliados</title>

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
            color: #fff;
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


    <section class="bl-all">

        <div class="barra-lateral">
            <div class="bl-mg">

                <div class="adminHeader" onclick="expandirMenu(this)" style=" padding: 0 0 4.8px 0;">

                    <div class="adminSeparation">
                        <img src="img/UserGestion.png" class="admHeaderImg">
                        <div class="text">
                            <p class="aNombre" id="tipo_user"><?php echo ucfirst($_SESSION['nombre']); ?></p>
                            <h5 class="tUsuario"><?php if ($_SESSION['tipo_usuario'] == 1) {
                                                        echo 'Afiliado';
                                                    }; ?></h5>
                        </div>
                    </div>
                    <div class="burgerMenuImg" ></div>
                </div>

                <div class="bl-body">


                <button id="aPerfil" class="b-bl"><img src="img/member.png" class="ico-bl" alt="">
                <p>Accesibilidad</p>
            </button>
            <p class="p_tittle gestionar">Gestionar</p>
            <div class="hr">
                <hr>
            </div>
            <button id="aMovimientos" class="b-bl"><img src="img/movs.png" class="ico-bl" alt="">
                <p>Consultar</p>
            </button>
            <button id="aAportar" class="b-bl"><img src="img/wallet.png" class="ico-bl" alt="">
                <p>Aportar</p>
            </button>


                </div>




            </div>
        </div>


        <!-- aquí esta el contenido completo lo que aparece en el medio, osea todo xd -->

        <div id="af-container" class="bl-container-2">
            <?php
            ini_set('display_errors', 0);

            ?>
            <script>
                ////////////////Funcion para que funcionen los pdfs//////////////////////////////
                let valor = 0;

                function reciboAfiliados(valor) {
                    document.cookie = "Recibo_id=" + valor;
                    window.location.href = './componentes/reportes/recibo_afiliado.php';
                }


                //----------Aqui termina la funcion.-----------//


                $(document).ready(function() {
                    const originalCorreo = "<?php echo $_SESSION['correo']; ?>";
                    const originalTelefono = "<?php echo $_SESSION['telefono']; ?>";
                    const bloqueoTiempo = 90 * 60 * 1000; // 90 minutos en milisegundos

                    function esBloqueado(ultimoEnvio) {
                        if (!ultimoEnvio) return false;
                        const ahora = new Date().getTime();
                        return (ahora - ultimoEnvio) < bloqueoTiempo;
                    }

                    window.submitCorreo = function() {
                        const ultimoEnvioCorreo = localStorage.getItem('ultimoEnvioCorreo');
                        if (esBloqueado(ultimoEnvioCorreo)) {
                            $("#mensajeRespuestaCorreo").text(
                                "Debe esperar una hora y media antes de enviar otra solicitud de cambio de correo."
                            );
                            return;
                        }

                        let nuevoCorreo = $("#nuevoCorreo").val();
                        let confirmarCorreo = $("#confirmarCorreo").val();
                        let mensajeRespuestaCorreo = $("#mensajeRespuestaCorreo");

                        mensajeRespuestaCorreo.text(""); // Limpiar el mensaje antes de las validaciones

                        if (!nuevoCorreo || !confirmarCorreo) {
                            mensajeRespuestaCorreo.text(
                                "Por favor, complete ambos campos de correo electrónico.");
                            return;
                        }

                        if (nuevoCorreo !== confirmarCorreo) {
                            mensajeRespuestaCorreo.text("Los correos electrónicos no coinciden.");
                            return;
                        }

                        if (nuevoCorreo === originalCorreo) {
                            mensajeRespuestaCorreo.text("Dato repetido: Correo electrónico duplicado.");
                            return;
                        }

                        $.ajax({
                            type: "POST",
                            url: "./componentes/conexiones/cambio_correo/solicitar_cambio_correo.php",
                            data: {
                                nuevoCorreo: nuevoCorreo
                            },
                            dataType: "json",
                            success: function(response) {
                                console.log("AJAX Success Response:",
                                    response); // Para ver la respuesta en la consola
                                if (response.success) {
                                    localStorage.setItem('ultimoEnvioCorreo', new Date().getTime());
                                    mensajeRespuestaCorreo.text(
                                        "Actualización de correo enviada al administrador, en espera de su aprobación"
                                    );
                                } else {
                                    mensajeRespuestaCorreo.text(response.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log("Mensaje:", xhr
                                    .responseText); // Para ver el error en la consola
                                mensajeRespuestaCorreo.text("Error al enviar la solicitud.");
                            }
                        });
                    };

                    window.submitTelefono = function() {
                        const ultimoEnvioTelefono = localStorage.getItem('ultimoEnvioTelefono');
                        if (esBloqueado(ultimoEnvioTelefono)) {
                            $("#mensajeRespuestaTelefono").text(
                                "Debe esperar una hora y media antes de enviar otra solicitud de cambio de teléfono."
                            );
                            return;
                        }

                        let nuevoTelefono = $("#nuevoTelefono").val();
                        let mensajeRespuestaTelefono = $("#mensajeRespuestaTelefono");

                        mensajeRespuestaTelefono.text(""); // Limpiar el mensaje antes de las validaciones

                        if (!nuevoTelefono) {
                            mensajeRespuestaTelefono.text("Por favor, complete el campo de teléfono.");
                            return;
                        }

                        if (nuevoTelefono === originalTelefono) {
                            mensajeRespuestaTelefono.text("Dato repetido: Número de teléfono duplicado.");
                            return;
                        }

                        $.ajax({
                            type: "POST",
                            url: "./componentes/conexiones/cambio_correo/solicitar_cambio_telefono.php",
                            data: {
                                nuevoTelefono: nuevoTelefono
                            },
                            dataType: "json",


                            success: function(response) {
                                console.log("AJAX Success Response:",
                                    response); // Para ver la respuesta en la consola
                                if (response.success) {
                                    localStorage.setItem('ultimoEnvioTelefono', new Date()
                                        .getTime());
                                    mensajeRespuestaTelefono.text(
                                        "Actualización de teléfono enviada al administrador, en espera de su aprobación"
                                    );
                                } else {
                                    mensajeRespuestaTelefono.text(response.message);
                                }
                            },
                            error: function(xhr, status, error) {

                                mensajeRespuestaTelefono.text("Error al enviar la solicitud.");
                            }
                        });
                    };
                });

                function submitClave() {
                    let claveAnterior = $("#claveAnterior").val();
                    let nuevaClave = $("#nuevaClave").val();
                    let confirmClave = $("#confirmClave").val();
                    let mensajeRespuestaClave = $("#mensajeRespuestaClave");

                    mensajeRespuestaClave.text(""); // Limpiar el mensaje antes de las validaciones

                    if (!claveAnterior || !nuevaClave || !confirmClave) {
                        mensajeRespuestaClave.text("Por favor, complete todos los campos.");
                        return;
                    }

                    if (nuevaClave !== confirmClave) {
                        mensajeRespuestaClave.text("Las nuevas claves no coinciden.");
                        return;
                    }

                    $.ajax({
                        type: "POST",
                        url: "./componentes/conexiones/cambio_clave/cambiar_clave.php",
                        data: {
                            claveAnterior: claveAnterior,
                            nuevaClave: nuevaClave
                        },
                        dataType: "json",
                        success: function(response) {
                            console.log("Success Response:", response); // Para ver la respuesta en la consola
                            if (response.success) {
                                mensajeRespuestaClave.text("Clave actualizada correctamente.");
                            } else {
                                mensajeRespuestaClave.text(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log("Mensaje:", xhr.responseText); // Para ver el error en la consola
                            mensajeRespuestaClave.text("Error al enviar la solicitud.");
                        }
                    });
                }
            </script>
            </header>

        </div> <!-- bl-container-2 -->

    </section> <!-- barra lateral -->

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

    <script src="./js/ajax.js"></script>

    <footer>

    </footer>
</body>

</html>