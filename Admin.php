<?php
include("./componentes/conexiones/conexionbd.php");
session_start();

if (!isset($_COOKIE['User_ID'])) {
    header("Location: ./index.php");
    exit();
}

$id_usuaio = $_COOKIE['User_ID'];
$sql = "SELECT * FROM usuario_admins WHERE id_usuarios='$id_usuaio'";
$resultado = mysqli_query($con, $sql);

if ($row = $resultado->fetch_assoc()) {
    $nombre = $row['nombre'];
    $apellido = $row['apellido'];
    $tipo_usuario = $row['usuario'];
    $correo = $row['correo'];
    $img_perfil = $row['img'];
    $privilegios = $row['permisos'];
    $_SESSION['permisos'] = $privilegios;
    $_SESSION['id_usuarios'] = $id_usuaio;
} else {
    // Si no se encuentra el usuario, redirige al index
    header("Location: ./index.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

   
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="./style/style-afiliados2.css">
    <link rel="stylesheet" href="./style/style.css">
    <link rel="stylesheet" href="./style/admDashboard.css">
    <link rel="stylesheet" href="./style/admin.css">
    <script src="./js/jquery-3.7.1.min.js"></script>

    <title>SACIPS | Administración</title>

</head>

<body>

    <section class="bl-all">

        <div class="barra-lateral">
            <div class="bl-mg">
                <div class="bl-header">

                    <div class="adminHeader" onclick="expandirMenu(this)" >
                        <div class="adminSeparation">
                            <?php
                            if ($img_perfil != '') {
                                echo '<img src="img/Usuarios/' . $img_perfil . '" class="admHeaderImg">';
                            } else if ($img_perfil == '') {
                                echo '<img src="img/UserGestion.png" class="admHeaderImg">';
                            }
                            ?>

                            <div class="text">
                                <?php
                                echo "<p class='adminNombre'>" . $nombre . " " . $apellido . "</p>";
                                if ($tipo_usuario == md5('Desarrollador')) {
                                    echo "<h5 class='p1'>Desarrollador</h5>";
                                } else if ($tipo_usuario == md5('Super Admin')) {
                                    echo "<h5 class='p2'>Super Admin</h5>";
                                } else if ($tipo_usuario == md5('Admin')) {
                                    echo "<h5 class='p2'>Administrador</h5>";
                                } else if ($tipo_usuario == md5('Director')) {
                                    echo "<h5 class='p2'>Director</h5>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="burgerMenuImg"></div>
                </div>


                <div class="bl-body">

                    <button id="admSettings" class="b-bl"><img src="img/profile.svg" class="ico-bl" alt="">
                        <p>Perfil</p>
                    </button>

                    <button id="admPersonalizar" class="b-bl"><img src="img/settings.svg" class="ico-bl" alt="">
                        <p>Accesibilidad </p>
                    </button>

                    <p class="p_tittle">Gestionar</p>
                    <div class="hr">
                        <hr>
                    </div>
                    <button id="admGeneral" class="b-bl"><img src="img/dashboard.svg" class="ico-bl" alt="">
                        <p>General</p>
                    </button>
                    <button id="admUsuarios" class="b-bl"><img src="img/personas.svg" class="ico-bl" alt="">
                        <p>Personas</p>
                    </button>
                    <button id="admAdministrar" class="b-bl"><img src="img/administrar.svg" class="ico-bl" alt="">
                        <p>Administrar</p>
                    </button>
                    <button id="admAportes" class="b-bl"><img src="img/aportes.svg" class="ico-bl" alt="">
                        <p>Aporte</p>
                    </button>
                    <button id="admEgresos" class="b-bl"><img src="img/egreso.svg" class="ico-bl" alt="">
                        <p>Egreso</p>
                    </button>
                    <button id="Programador" class="b-bl" style="display: none;"><img src="/img/mantenimiento.png"
                            class="ico-bl" alt="">
                        <p>Mantenimiento</p>
                    </button>
                </div>


                <footer class="bl-footer">
                    <div class="t-footer">
                        <div class="closeSesion">
                            <button class="b-bl" onclick="CloseSesion();">
                                <img src="img/exit.png" class="ico-bl" alt="">
                                <p>Cerrar Sesión</p>
                            </button>
                        </div>

                    </div>


                </footer>


            </div>
        </div>


        <!-- aquí esta el contenido completo lo que aparece en el medio, osea todo xd -->

        <div id="af-container" class="bl-container-2">



        </div> <!-- aquí termina -->
        <div class="Restriccion">
            <div>
                <h1>Alerta de Seguridad!</h1>
                <img src="img/alerta.png" alt="">
                <p>por favor inicia sesion para continuar</p>
                <a href="index.php">iniciar sessión</a>
            </div>
        </div>
    </section>

    <script src="./js/ajax.js">


    </script>

    <script>
        // Función para ver el recibo de Aporte Patronal
        function reciboAporteP(valor) {
            document.cookie = "ReciboAporteP=" + valor;
            window.location.href = './componentes/reportes/recibo_aporteP.php';
        }

        // Función para ver el recibo de Donación
        function reciboDonacion(valor) {
            document.cookie = "Recibo_Dona=" + valor;
            window.location.href = './componentes/reportes/recibo_Donacion.php';
        }
    </script>
</body>

</html>

<?php
if (!$_COOKIE['User_ID']) {


?>

    <style>
        .Restriccion {
            display: flex;
        }
    </style>
<?php

    include_once "./componentes/conexiones/permisosAdmin.php";
}

?>