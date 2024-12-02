<?php

include('../conexiones/conexionbd.php');

//===//datos//===//
$Nombre = $_POST['nombre'];
$Apellido = $_POST['apellido'];
$Cedula = $_POST['cedula'];
$Telefono = $_POST['telefono'];
$Nombre_Usuario = $_POST['nombre_usuario'];
$Password = md5($_POST['password']);
$Correo = $_POST['email'];
$mensaje='';

// Inserción en la tabla `personas`
$sql_personas = "INSERT INTO personas (nombre, apellido, cedula,telefono) 
                VALUES ('$Nombre','$Apellido','$Cedula','$Telefono')";

// Iniciar una transacción
$con->begin_transaction();

try {
    // Inserción en la tabla `personas`
    if ($con->query($sql_personas) === TRUE) {
        
        $id_persona = $con->insert_id;
        
        // Inserción en la tabla `usuarios`
        $sql_usuarios = "INSERT INTO usuarios (id_persona, nombre_usuario, tipo_usuario, clave, correo) 
                        VALUES ('$id_persona','$Nombre_Usuario','2','$Password','$Correo')";
        
        if ($con->query($sql_usuarios) === TRUE) {
            $con->commit();
            $mensaje = 'Su registro se realizo con exito';
        } else {
            $mensaje = 'UPS! Ocurrio un error al crear la cuenta';
        }

    } else {
        $mensaje = 'UPS! Ocurrio un error al crear la cuenta';
    }

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $con->rollback();
    echo $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <style>
        *{
            margin: 0px;
            padding: 0px;
            font-family: arial;
            font-weight: 100;
            box-sizing: border-box;
        }
        body{
            width: 100%;
            height: 100vh;
            background-image: url('../../img/fondo-bienvenida.png');
            background-size: 100% 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        h1{
            font-size: 5vw;
            width: 80%;
            text-align: center;
        }
        p{
            font-size: 2vw;
            color: gray;
            margin: 10px;
        }
        :root{
            --color: #410fb4;
        }
        button{
            height: 40px;
            font-size: 1.5vw;
            border: solid 2px var(--color);
            background-color: white;
            padding: 0px 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 20px;
            position: relative;
            padding-left: 20px;
            color: var(--color);
            margin: 0px 10px;
            cursor: pointer;
            transition: 0.6s all;
        }
        button div{
            position: absolute;
            border: solid 0px;
            background-color: var(--color);
            height: 25px;
            width: 25px;
            right: 5px;
            border-radius: 20px;
            transition: 0.6s all;
        }
        button img{
            margin-left: 10px;
            height: 25px;
            width: 25px;
            padding: 4px;
            display: flex;
            justify-content: center;
            filter: brightness(0) saturate(100%) invert(100%) sepia(100%) saturate(13%) hue-rotate(237deg) brightness(104%) contrast(104%);
            align-items: center;
            transition: 0.6s all;
        }
        button:hover{
            background-color: var(--color);
            color: white;
        }
        button:hover img{
            filter: brightness(0) saturate(100%) invert(12%) sepia(69%) saturate(5842%) hue-rotate(260deg) brightness(80%) contrast(110%);
        }
        button:hover div{
            background-color: white;
        }
        .botonera{
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .btn{
            background-color: var(--color);
            color: white;
        }
        .btn img{
            filter: brightness(0) saturate(100%) invert(12%) sepia(69%) saturate(5842%) hue-rotate(260deg) brightness(80%) contrast(110%);    
        }
        .btn div{
            background-color: white;
        }
        .btn:hover{
            background-color: white;
            color: var(--color);
        }
        .btn:hover img{
            filter: brightness(0) saturate(100%) invert(100%) sepia(100%) saturate(13%) hue-rotate(237deg) brightness(104%) contrast(104%);
        }
        .btn:hover div{
            background-color: var(--color);
        }
        a{
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php
    echo '<h1>Gracias ' . $_POST['nombre'] . ' ' . $_POST['apellido'] . ' ahora eres parte de nuestra comunidad</h1>';
    echo '<p>'.$mensaje.'</p>';
    ?>
    <div class="botonera">
        <a href='../../loginInvitados.php'><button class="btn">Iniciar sesion<div></div><img src="../../img/flecha.png" alt=""></button></a>
        <a href='../../index.php'><button>Volver a Home<div></div><img src="../../img/flecha.png" alt=""></button></a>
    </div>
</body>

</html>