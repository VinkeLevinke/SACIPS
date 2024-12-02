<?php 
session_start();
if(!isset($_SESSION["id_usuarios"])){
include_once"../../componentes/conexiones/permisosAdmin.php";
}
?>

<!DOCTYPE html>
<html lang="es">
<!--modulazo del p´causa gilberga-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="style/admin.css">
    <title>SACIPS | Administración</title>
</head>

<body>
    <?php
    include ("../../componentes/template/admHeader.php");
    ?>
    <div class="box_content">
        <!-- <button id="admConAportesPatronales" class="btn_box">

                    <img src="img/aportePatronal.png" alt="" class="imgBox">
                    
                    <div>
                        <h3>Registrar aportes patronales</h3>
                        
                    </div>
                </button> -->
        <button id="admConDonaciones" class="btn_box">

            <img src="img/donacion.png" alt="" class="imgBox">

            <div>
                <h3>Registrar Aportes</h3>

            </div>
        </button>

        <button id="admConAportes" class="btn_box">

            <img src="img/ConfirmarAporte.png" alt="" class="imgBox">

            <div>

                <h3>Consultar Aportes</h3>

            </div>
        </button>



    </div>


</body>

</html>