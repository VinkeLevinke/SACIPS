<?php

session_start();
if (!isset($_SESSION["id_usuarios"])) {
    include_once "../../componentes/conexiones/permisosAdmin.php";
}


include('../conexiones/conexionbd.php');

// Primera consulta: obtener todos los usuarios en la tabla `personas`
$sql_personas = "SELECT id_Personas, nombre, apellido, cedula, telefono FROM personas";
$result_personas = $con->query($sql_personas);

// Comprobar si la consulta se realizó correctamente
if ($result_personas === FALSE) {
    die("Error en la consulta: " . $con->error);
}

// Almacenar datos de personas
$personas = [];
if ($result_personas->num_rows > 0) {
    while ($row = $result_personas->fetch_assoc()) {
        $personas[] = $row; // Almacena cada persona en un array
    }
} else {
    echo "No hay personas registradas.";
}

// Segunda consulta: obtener usuarios relacionados
$sql_usuarios = "SELECT u.id_persona, u.nombre_usuario, u.tipo_usuario, u.correo FROM usuarios u Where u.tipo_usuario = 4";
$result_usuarios = $con->query($sql_usuarios);

// Comprobar si la consulta se realizó correctamente
if ($result_usuarios === FALSE) {
    die("Error en la consulta: " . $con->error);
}

// Almacenar datos de usuarios
$usuarios = [];
if ($result_usuarios->num_rows > 0) {
    while ($row = $result_usuarios->fetch_assoc()) {
        $usuarios[] = $row; // Almacena cada usuario en un array
    }
} else {
    echo "No hay usuarios registrados.";
}

$con->close();
?>



<!DOCTYPE html>
<html lang="es">
<!--modulazo del p´causa gilberga-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">

    <title>SACIPS | Egresos</title>
    <link rel="stylesheet" href="../style/admin.css">
</head>

<body class="bodyGstion">

    <section id="gstionBody" class="gstionBody">

        <?php
        // Mostrar datos en el formato deseado
        if (!empty($personas)) {
            foreach ($personas as $persona) {
                // Buscar el usuario correspondiente
                $usuarioEncontrado = null;
                foreach ($usuarios as $usuario) {
                    if ($usuario['id_persona'] == $persona['id_Personas']) {
                        $usuarioEncontrado = $usuario;
                        break;
                    }
                }

                // Determinar el tipo de usuario
                $tipoUsuario = 'Vigilancia';
                       
                        

                // Mostrar la información en el formato de div
                echo '<div class="afiliados">';
                echo '<div class="datos">';
                echo '<div class="datosSeparacion">';
                echo '<img src="img/UserGestion.png" alt="" class="iconGstion">';
                echo '<div class="listasSeparadas">';
                echo '<div class="listSeparation">';
                echo '<p class="titleType">' . $tipoUsuario . '</p>'; // Mostrar el tipo de usuario
                echo '</div>';
                echo '<div class="listSeparation">';
                // echo '<p class="titleDatos">Estatuto</p>';
                // echo '<p class="estatuto">Pagado</p>'; // Ajusta según corresponda
                echo '</div>';
                echo '</div>'; // listasSeparadas
                echo '</div>'; // datosSeparacion

                echo '<div class="datosAfiliados">';
                echo '<div class="listSeparation">';
                echo '<p class="titleDatos">Tipo</p>';
                echo '<p class="tipoAfiliado">' . $tipoUsuario . '</p>';
                echo '</div>';
                echo '<div class="listSeparation">';
                echo '<p class="titleDatos">Nombre</p>';
                echo '<p class="nombre">' . $persona['nombre'] . ' ' . $persona['apellido'] . '</p>';
                echo '</div>';
                echo '<div class="listSeparation">';
                echo '<p class="titleDatos">Correo</p>';
                echo '<p class="email">' . ($usuarioEncontrado ? $usuarioEncontrado['correo'] : 'N/A') . '</p>';
                echo '</div>';
                echo '<div class="listSeparation">';
                echo '<p class="titleDatos">Telefono</p>';
                echo '<p class="telefono">' . $persona['telefono'] . '</p>';
                echo '</div>';
                echo '<div class="listSeparation">';
                echo '<p class="titleDatos">Cedula/Rif</p>';
                echo '<p class="cedula">' . $persona['cedula'] . '</p>';
                echo '</div>';
                echo '<div class="listSeparation">';
                // echo '<p class="titleDatos">Fecha de Ingreso</p>';
                // echo '<p class="fecha">' . ($usuarioEncontrado ? 'N/A' : 'N/A') . '</p>'; // Ajusta según corresponda
                echo '</div>';
                echo '</div>'; // datosAfiliados
                echo '</div>'; // datos

                echo '<div class="buttons">';
                echo '<button>Historial de Aportes</button>';
                echo '<button>Editar</button>';
                echo '<button class="eliminar">Eliminar</button>';
                echo '</div>'; // buttons
                echo '</div>'; // afiliados
            }
        } else {
            echo '<p>No hay afiliados registrados.</p>';
        }
        ?>
        <!--<div class="afiliados">


            <div class="datos">
                <div class="datosSeparacion">

                    <img src="img/UserGestion.png" alt="" class="iconGstion">


                    
                    <div class="listasSeparadas">

                        <div class="listSeparation">
                         
                            <p class="titleType">AFILIADO</p>
                        </div>
                        

                        <div class="listSeparation">
                            <p class="titleDatos">Estatuto</p>

                            <p class="estatuto">Pagado</p>
                        </div>
                    </div>
                </div>


                <div class="datosAfiliados">

                    <div class="listSeparation">
                        <p class="titleDatos">Tipo</p>
                        <p class="tipoAfiliado">Persona</p>
                    </div>

                    <div class="listSeparation">
                        <p class="titleDatos">Nombre</p>
                        <p class="nombre">Juan Biondi Paez</p>
                    </div>
                    
                    <div class="listSeparation">
                        <p class="titleDatos">Correo</p>
                        <p class="email">juanbiondi@gmail.com</p>
                    </div>

                

                    <div class="listSeparation">
                        <p class="titleDatos">Telefono</p>
                        <p class="telefono">0424-500-9703</p>
                    </div>




                    <div class="listSeparation">
                        <p class="titleDatos">Cedula/Rif</p>
                        <p class="cedula">V-00.000.00</p>
                    </div>


                    <div class="listSeparation">
                        <p class="titleDatos">Fecha de Ingreso</p>
                        <p class="fecha">00/00/0000</p>
                    </div>

                </div>
            </div>

            <div class="buttons">
                <button>Historial de Aportes</button>
                <button>Editar</button>
                <button class="eliminar">Eliminar</button>
            </div>
        </div>
        

         //aqui otro cuadro 

        <div class="afiliados">


            <div class="datos">
                <div class="datosSeparacion">

                    <img src="img/UserGestion.png" alt="" class="iconGstion">


                    
                    <div class="listasSeparadas">

                        <div class="listSeparation">
                         
                            <p class="titleType">INVITADO</p>
                        </div>
                        

                    </div>
                </div>


                <div class="datosAfiliados">

                    <div class="listSeparation">
                        <p class="titleDatos">Tipo</p>
                        <p class="tipoAfiliado">Servicio</p>
                    </div>

                    <div class="listSeparation">
                        <p class="titleDatos">Nombre</p>
                        <p class="nombre">Odontología Yaracuy</p>
                    </div>
                    
                    <div class="listSeparation">
                        <p class="titleDatos">Correo</p>
                        <p class="email">juanbiondi@gmail.com</p>
                    </div>

                

                    <div class="listSeparation">
                        <p class="titleDatos">Telefono</p>
                        <p class="telefono">0424-500-9703</p>
                    </div>




                    <div class="listSeparation">
                        <p class="titleDatos">Cedula/Rif</p>
                        <p class="cedula">V-00.000.00</p>
                    </div>


                    <div class="listSeparation">
                        <p class="titleDatos">Fecha de Ingreso</p>
                        <p class="fecha">00/00/0000</p>
                    </div>

                </div>
            </div>

            <div class="buttons">
                <button>Historial de Aportes</button>
                <button class="eliminar">Suspender</button>
            </div>
        </div>-->


    </section>
</body>

</html>