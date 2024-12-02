<?php

include "../../componentes/conexiones/conexionbd.php";
session_start();
if (!isset($_SESSION["id_usuarios"])) {
    include_once "../../componentes/conexiones/permisosAdmin.php";
}

$id_usuario = $_COOKIE['User_ID'];
$nombre;
$tipo_usuario;
$correo;
$telefono;
$correo;
$img_perfil;
$apellido;
$ci_rif;

//esta vaina es seria señol bill 'esta vaina guarda la imagen(foto de perfil)'
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['image_true'])) {
    if ($_FILES['imagen_perfil']['error'] === UPLOAD_ERR_OK) {
        $tempPath = $_FILES['imagen_perfil']['tmp_name'];
        $targetPath = '../../img/Usuarios/' . $_FILES['imagen_perfil']['name'];
        move_uploaded_file($tempPath, $targetPath);
        $name_img = $_FILES['imagen_perfil']['name'];
        $sql = "UPDATE usuario_admins SET img='$name_img' WHERE id_usuarios= $id_usuario ";
        if (mysqli_query($con, $sql)) {
            echo '<script>window.location.href = "../../Admin.php";</script>';
        } else {
            echo "Error: " . mysqli_error($con);
        }
    } else {
        echo 'Error al subir la imagen';
    }
}

//este guarda/edita los datos del usuario 🦍
if (isset($_POST['active'])) {
    //datos editados 👽
    $nombre = $_POST['nombres'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $apellido = $_POST['apellidos'];
    $ci_rif = $_POST['ci'];
    $sql = "UPDATE usuario_admins SET nombre='$nombre', apellido='$apellido', cedula='$ci_rif', correo='$correo', telefono='$telefono' WHERE id_usuarios=$id_usuario";
    if (mysqli_query($con, $sql)) {
        echo '<script>window.location.href = "../../Admin.php";</script>';
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

if ($con) {
    $sql = "SELECT * FROM usuario_admins";
    $resultado = mysqli_query($con, $sql);
    $i = 0;
    //echo "<script> const usuario = [''];</script>";
    while ($row = $resultado->fetch_assoc()) {
        if ($row['id_usuarios'] == $id_usuario) {
            $nombre = $row['nombre'];
            $tipo_usuario = $row['usuario'];
            $telefono = $row['telefono'];
            $correo = $row['correo'];
            $img_perfil = $row['img'];
            $apellido = $row['apellido'];
            $ci_rif = $row['cedula'];
        }
        $i += 1;
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SACIPS | SETTING</title>
    
    <link rel="stylesheet" href="style/user.css">
    <style>

        .hdrPerfil {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .hdrPerfilShape {
            display: flex;
            flex-direction: column;
            align-items: center;
            box-sizing: border-box;
            width: 80% !important;
            border-radius: 3px !important;
            padding-top: 70px !important;
            position: relative;
            margin-top: 80px;
        }

        .img{
            position: absolute;
            top: -80px;
        }
        .img .agregar_img{
            right: 0px;
        }

        .img div{
            margin: 0px !important;
        }

        .hdrPerfilShape img {
            border-radius: 50%;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            margin: 0px !important;
        }

        .textPerfilHeader {
            flex: 1;
            text-align: center;
        }

        .title_edit {
            width: 80%;
            font-size: 24px;
            box-sizing: border-box;
        }

        .textPerfilHeader h2 {
            text-align: center;
        }

        .textPerfilHeader p {
            margin: 5px 0;
            color: #666;
            text-align: center;
        }

        .btnGstHeaderPerfil {
            display: flex;
            width: 80%;
            justify-content: center;
            align-items: center;
        }

        .buttonDashboard {
            background-color:  #525fbb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .buttonDashboard:hover {
            background-color: #0027c2c4;
        }

        .edit_datos {
            margin-top: 0px;
            box-sizing: border-box;
            width: 95%;
            padding-bottom: 20px;
            border: 0px;
            background-color: white;
            margin: 0px;
            border-radius: 3px !important;
            display: flex;
            flex-direction: column;
        }

        .edit_datos form {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            width: 100%;
        }
        .edit_datos h3{
            border-bottom: solid 2px;
            color: #3c3c3c;
        }
        .edit_datos form input[type="text"] {
            width: 48%;
            padding: 10px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            margin: 0px;
            background-color: white;
            height: 35px;
            border-radius: 2px;
        }

        .edit_datos form label {
            width: 48%;
            margin: 0px;
            font-weight: bold;
            color: #666;
        }

        .perfilSection {
            width: 100%;
            height:auto;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-direction: column;
            position: relative;
        }
        
    </style>
</head>

<body>
    <?php
    include("../../componentes/template/admHeader.php");
    ?>


    <div class="perfilSection">
        <div class="hdrPerfil">

            <div class="hdrPerfilShape perfil_user">

                <form class='img' method="POST" id="form" accept="image/*" enctype="multipart/form-data"
                    action="./componentes/administrador/admConfig.php">
                    <div>
                        <?php
                        if ($img_perfil != '') {
                            echo '<img src="./img/Usuarios/' . $img_perfil . '" alt="" class="iconGstHeader">';
                        } else {
                            echo '<img src="./img/UserGestion.png" alt="" class="iconGstHeader">';
                        }
                        ?>
                    </div>
                    <label class="agregar_img" for="imagen_perfil"><img src="img/camera.png"></label>
                    <input type="file" id="imagen_perfil" name="imagen_perfil">
                    <input type="hidden" name='image_true'>
                </form>

                <div class="textPerfilHeader">
                    <?php
                    echo "<h2>" . $nombre . " " . $apellido . "</h2>";
                    if ($tipo_usuario == md5('Desarrollador')) {
                        echo "<p>Desarrollador</p>";
                    } else if ($tipo_usuario == md5('Super Admin')) {
                        echo "<p>Super Admin</p>";
                    } else if ($tipo_usuario == md5('Admin')) {
                        echo "<p>Administrador</p>";
                    }
                    echo "<p>" . $telefono . "</p>";
                    echo "<p>" . $correo . "</p>";
                    ?>
                </div>

                <div class="btnGstHeaderPerfil">
                    <!-- <button class="buttonDashboard edit_info">
                            Editar Información
                        </button> -->
                    <button class="buttonDashboard" onclick="enviar_form();">
                        Guardar Cambios
                    </button>
                    <!-- <button class="buttonDashboard">
                            Eliminar foto de perfil
                        </button> -->
                </div>
            </div>
        </div>

        <div class="edit_datos">

            <h3>Editar Datos</h3>
            <form action="./componentes/administrador/admConfig.php" method="POST" id="edit_submit"
                enctype="multipart/form-data">
                <label for="">Nombres:</label>
                <label for="">Apellidos:</label>
                <input type="text" placeholder="Nombres" value="<?php echo $nombre ?>" id="nombres" name="nombres">
                <input type="text" placeholder="Apellidos" value="<?php echo $apellido ?>" id="apellidos"
                    name="apellidos">
                <label for="">Ci/Rif:</label>
                <label for="">Telefono:</label>
                <input type="text" placeholder="Ci/Rif" value="<?php echo $ci_rif; ?>" id="ci" name="ci">
                <input type="text" placeholder="Telefono" value="<?php echo $telefono ?>" id="telefono"
                    name="telefono">
                <label for="">Correo:</label>
                <label for="">Tipo Usuario:</label>
                <input type="text" placeholder="Correo" value="<?php echo $correo ?>" id="correo" name="correo">
                <?php
                if ($tipo_usuario == md5('Desarrollador')) {
                    echo "<input type='text' placeholder='Desarrollador'readonly>";
                } else if ($tipo_usuario == md5('Super Admin')) {
                    echo "<input type='text' placeholder='Super Admin' readonly>";
                } else if ($tipo_usuario == md5('Admin')) {
                    echo "<input type='text' placeholder='Administrador' readonly>";
                }
                ?>
                <input type="hidden" name="active" id="active">
            </form>
        </div>
        <section class="accesibilidad">
        </section>
    </div>

</body>

</html>