<?php
session_start();
if (!isset($_SESSION["id_usuarios"])) {
    include_once "../../componentes/conexiones/permisosAdmin.php";
}

?>

<!DOCTYPE html>
<html lang="es">
<!--modulazo del p´causa gilberga-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="style/style-afiliados2.css">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/admin.css">
    <title>SACIPS | Egresos</title>
</head>

<body>
    <?php
    include("../../componentes/template/admHeader.php");
    ?>
    <div class="box_content">
        <button id="admCrearUsuarios" class="btn_box">

            <img src="img/agUsuarios.png" alt="" class="imgBox">

            <div>
                <h3>Registrar Personas</h3>

            </div>
        </button>

        <button id="admGstUsuarios" class="btn_box tab-button">

            <img src="img/consultarUsuarios.png" alt="" class="imgBox">

            <div>
                <h3>Consultar Personas</h3>

            </div>
        </button>

        <button id="admSolicitudes" class="btn_box tab-button">

            <img src="img/solicitud.svg" alt="" class="imgBox">

            <div>
                <h3>Solicitudes de Cambio</h3>
             
            </div>
        </button>
    </div>

    </section>
</body>

</html>