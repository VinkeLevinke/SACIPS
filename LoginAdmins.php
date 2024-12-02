<?php
include('./componentes/conexiones/conexionbd.php');

if ($con) {
    $sql = "SELECT * FROM usuario_admins";
    $resultado = mysqli_query($con, $sql);
    $i = 0;
    echo "<script> const usuario = []; const id_usuario = []; const clave = [];</script>";
    while ($row = $resultado->fetch_assoc()) {
        echo  "<script>usuario[" . $i . "]='" . $row['nombre'] . "';</script>";
        echo  "<script>id_usuario[" . $i . "]='" . $row['id_usuarios'] . "';</script>";
        if ($row['clave'] == '') {
            echo "<script>clave[" . $i . "]='nulo';</script>";
        } else {
            echo "<script>clave[" . $i . "]='" . $row['clave'] . "';</script>";
        }
        $i += 1;
    }
    echo "<script>console.log('Id:'+id_usuario[0]+clave[0]);</script>";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SACIPS | Ingresar - Afiliado</title>
    <link rel="stylesheet" href="style/login-style.css">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="f-bg"></div>
    <div class="formularioAdmin">
        <div class="moverAdmin">
            <form class="formAdmin" method="post" action="Admin.php" id="formAdmin">
                <div>
                    <div class="acceder">
                        <div class="im-logo">
                            <img draggable="false" src="img/IPSPUPTYAB-LOGO.ico" alt="">
                            <p draggable="false">Instituto de Prevision social</p>
                        </div>
                        <h1 draggable="false" class="ac">Personal Administrativo</h1>
                    </div>
                    <div class="mov-inputs">
                        <div class="input"><img draggable="false" src="img/456212.png" alt="">
                            <input type="text" placeholder="Ingrese el nombre de Usuario" id="nombre" required>
                        </div>
                        <div class="input"><img draggable="false" src="img/password.png" alt="">
                            <input type="password" placeholder="Ingrese la Clave" id="password" required>
                        </div>
                        <p id="aviso"></p>
                        <input type="hidden" id="id_usuario" name="id_usuario" value="">
                        <a href="recuperarusuarioclave.php
                        .php">Olvido su clave?</a>
                    </div>
                    <button onclick="click_boton(event)" id="boton" aria-hidden="true">Ingresar</button>
                </div>
            </form>

            <a href="index.php" class="volver">
                <img src="img/home.png">
                <p>Volver a Inicio</p>
            </a>
        </div>
    </div>
    
    <script src="./js/login-json.js"></script>
</body>
</html>
