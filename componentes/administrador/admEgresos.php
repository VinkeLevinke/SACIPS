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
                <button id="admReportEgresos" class="btn_box">

                    
                    <img src="img/contabilidad.png" alt="" class="imgBox">
                    
                    <div>
                        <h3>Registrar Egreso</h3>
                        
                    </div>
                </button>
                
                <button id="admGstEgresos" class="btn_box">
                    
                    <img src="img/egreso.png" alt="" class="imgBox">

                    <div>
                        <h3>Gestion de Egresos</h3>
                        
                    </div>
                </button>

                <button id="admTipoEgresos" class="btn_box">
                    
                    <img src="img/flujo-de-fondos.png" alt="" class="imgBox">

                    <div>
                        <h3>Gestionar tipo de Egreso</h3>
                        
                    </div>
                </button>
            </div>
   
    </section>
</body>

</html>
