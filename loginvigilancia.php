<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SACIPS | Ingresar - vigilante</title>
    <link rel="stylesheet" href="style/login-style.css">
    <link rel="icon" href="img/IPSPUPTYAB-LOGO.ico">
</head>

<body>
    <div class="f-bg"></div>
    <div class="formulario">
        <div class="mover">
            <form method="POST" action="./componentes/conexiones/convigilancia.php">
                <div>
                    <div class="acceder">
                        <div class="im-logo">
                            <img draggable="false" src="./img/IPSPUPTYAB-LOGO.ico" alt="">
                            <p draggable="false">Instituto de Prevision social</p>
                        </div>
                        <h1 draggable="false" class="ac">Consejo de Vigilancia</h1>
                    </div>
                    <div class="mov-inputs">
                        <div class="input">
                            <img draggable="false" src="img/456212.png" alt="">
                            <input type="text" placeholder="Nombre de usuario" id="nombre_usuario" name="nombre_usuario"
                                required>
                        </div>
                        <div class="input">
                            <img draggable="false" src="./img/7903535.png" alt="">
                            <input type="password" placeholder="Clave" id="password" name="clave" required>
                        </div>
                        <?php
                        if (isset($_SESSION['error'])) {
                            echo '<p style="color:red;">' . $_SESSION['error'] . '</p>';
                            unset($_SESSION['error']); // Elimina el mensaje de error después de mostrarlo
                        }
                        ?>
                        <a href="">Olvido su clave?</a>
                    </div>

                    <button onclick="click_boton();" id="boton">Ingresar</button>
                </div>
            </form>

            <a href="index.php" class="volver">
                <img src="img/home.png">
                <p>Volver a Inicio</p>
            </a>
        </div>
    </div>
</body>

</html>