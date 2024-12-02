<?php
include("../../componentes/conexiones/conexionbd.php");

if ($con) {
    $sql = "SELECT * FROM usuario_admins";
    $resultado = mysqli_query($con, $sql);
}

session_start();
if (!isset($_SESSION["id_usuarios"])) {
    include_once "../../componentes/conexiones/permisosAdmin.php";
}

$permisos = $_SESSION['permisos'];
//variables
$id_user_edit = array();
$i = 0;
$permisos_user_edit = array();

if (isset($_POST['editar_permisos'])) {
    $nuevos_permisos = $_POST['editar_permisos'];
    $id_edit_user = $_POST['id_del_usuario'];

    $sql = "UPDATE usuario_admins SET permisos='$nuevos_permisos' WHERE id_usuarios='$id_edit_user' ";
    if (mysqli_query($con, $sql)) {
        echo '<script>window.location.href = "../../Admin.php";</script>';
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../style/admDashboard.css">
    <link rel="stylesheet" href="../../style/admin.css">
    <style>
    /* General */
    .body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f3f4f6;
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    /* Sección del moderador */
    .admModerador {
        width: 100%;
        max-width: 1200px;
        margin: auto;
        position: relative;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }



    .overflow {
        overflow-y: auto;
    }

    /* Botones */
    .button-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1%;
    }

    .btn-agregar,
    .ModPerfilButtons button,
    #boton_eviar,
    .deletePrioridad {
        background: #323b86;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 15px;
        cursor: pointer;
        transition: background 0.3s;
        margin: 5px;
    }

    .btn-agregar:hover,
    .ModPerfilButtons button:hover,
    #boton_eviar:hover,
    .deletePrioridad:hover {
        background: #1d2355;
    }

    /* Card de perfil del moderador */
    .admModPerfil {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f9f9f9;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    }

    .iconModUser {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 15px;
    }

    .textModProfile {
        display: flex;
        align-items: center;
        flex-grow: 1;
    }

    .textMod {
        margin-left: 15px;
    }

    .textMod h2 {
        margin: 0;
        font-size: 1.2em;
    }

    .hrMod {
        margin: 10px 0;
    }

    .hrMod hr {
        border: none;
        border-top: 1px solid #e0e0e0;
    }

    .pMod {
        margin: 5px 0;
        font-size: 0.9em;
        color: #666;
    }

    /* Contenedor de botones */
    .ModPerfilButtons {
        margin-left: auto;
    }

    /* Contenedor de Moderar Prioridad */
    /* Contenedor de Moderar Prioridad */
    .ModerarPrioridad {
        display: none;
        position: fixed;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(5px);
        overflow: hidden;
    }

    /* Ajusta la forma del contenedor para pantallas más pequeñas */
    .modPrioridadShape {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        /* Puedes usar un porcentaje para permitir que se ajuste a pantallas más pequeñas */
        max-width: 600px;
        /* Este valor puede permanecer igual */
        margin: 0 auto;
        /* Centra el contenedor */
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.5s forwards;
    }


    @keyframes slideIn {
        from {
            transform: translateY(-30px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Ajuste del contenedor de información */
    .infoOpcionPrioridad {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .iconModPrioridad {
        width: 70px;
        height: 70px;
        margin-right: 15px;
    }

    /* Margen para el texto */
    .infoTexto {
        flex-grow: 1;
    }

    /* Opciones de la moderación */
    .opcionPrioridad {
        overflow-y: auto;
        height: 50vh;
        max-height: 500px;
        margin-top: 20px;
    }

    .op_container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        background: #f0f0f0;
        border-radius: 5px;
        margin: 10px 0;
        position: relative;
    }

    .checkbox {
        margin-right: 10px;
        display: flex;
        align-items: center;
    }

    .checkbox_op {
        display: flex;
        align-items: center;
        width: 40px;
        height: 20px;
        background: #a0a0a0;
        border-radius: 20px;
        cursor: pointer;
        transition: background 0.3s;
        position: relative;
    }

    .checkbox_op div {
        width: 18px;
        height: 18px;
        background: white;
        border-radius: 50%;
        position: absolute;
        transition: transform 0.3s;
    }

    input[type="checkbox"] {
        display: none;
        /* Ocultamos el checkbox */
    }

    input[type="checkbox"]:checked~.checkbox_op {
        background: #5452ff;
    }

    input[type="checkbox"]:checked~.checkbox_op div {
        transform: translateX(20px);
    }

    /* Botones en el contenedor de permisos */
    .contenedor_botones {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    #boton_eviar {
        background: #323b86;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 15px;
        cursor: pointer;
        transition: background 0.3s;
    }

    #boton_eviar:hover {
        background: #1d2355;
    }

    .deletePrioridad {
        background: #d9534f;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 15px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .deletePrioridad:hover {
        background: #c9302c;
    }

    /* Estilo para el botón de cierre */
    .btnCierre {
        background: #ff5c5c;
        color: white;
        border: none;
        border-radius: 4px;
        width: 30px;
        height: 30px;
        font-size: 1.2em;
        cursor: pointer;
        position: absolute;
        top: 10px;
        right: 10px;
        transition: background 0.3s;
    }

    .btnCierre:hover {
        background: #ff1c1c;
    }

    /* Responsivo */
    @media (max-width: 600px) {
        .modPrioridadShape {
            width: 90%;
        }

        .admModerador {
            padding: 10px;
        }

        .admModPerfil {
            flex-direction: column;
            align-items: flex-start;
        }

        .textModProfile {
            margin-bottom: 10px;
        }

        .ModPerfilButtons button {
            width: 100%;
            margin-top: 10px;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateY(0);
            opacity: 1;
        }

        to {
            transform: translateY(-30px);
            opacity: 0;
        }
    }

/* Responsivo */
@media (max-width: 1280px) {
    .modPrioridadShape {
        width: 95%; /* Ajusta el ancho del contenedor en pantallas más pequeñas */
        padding: 10px; /* Reduce el padding si es necesario */
    }
}

/* Otros ajustes que pueden ser útiles */
@media (max-width: 720px) {
    .admModerador {
        padding: 10px; /* Ajusta el padding en pantallas más pequeñas */
    }

    .button-container {
        flex-direction: column; /* Cambia a columna si es necesario */
        align-items: stretch; /* Asegúrate de que los elementos se estiren */
    }

    .btn-agregar,
    #boton_eviar,
    .deletePrioridad {
        width: 100%; /* Botones responsivos en pantallas más pequeñas */
    }
}

    .moderarPrioridad-salir {
        animation: slideOut 0.5s forwards;
    }

    .moderarPrioridad-entrar {
        animation: slideIn 0.5s forwards;
    }


    /* Estilo para el botón de volver */
    #volverAmdUsuarios {
        background: #f3f4f6;
        color: #323b86;
        border: 1px solid #323b86;
        border-radius: 5px;
        padding: 10px 15px;
        cursor: pointer;
        transition: background 0.3s, color 0.3s;
        text-align: center;
        display: inline-block;
        margin-left: 15px;
        /* Espaciado entre los botones */
    }

    #volverAmdUsuarios:hover {
        background: #e0e0e0;
        color: white;
    }
    </style>
</head>

<body class="body">

    <section class="admModerador">
        <div class="atras">



        </div>


        <div class="button-container">
            <div id="volverAmdUsuarios" class="link-btn">Volver</div>
            <div type="button" class="btn-agregar link-btn" id="btn_agregar">Registrar Un Moderador</div>

        </div>
        <div class="overflow">
            <div class="admModShape">
                <?php
                while ($row = $resultado->fetch_assoc()) {
                    if ($permisos[12] == 1) {
                        $id_user_edit[$i] = $row['id_usuarios'];
                        $permisos_user_edit[$i] = $row['permisos'];
                    } else {
                        $id_user_edit[$i] = null;
                        $permisos_user_edit[$i] = null;
                    }

                    echo '<div class="admModPerfil" id="usuario_' . $id_user_edit[$i] . '">';
                    echo '    <div class="textModProfile">';
                    if ($row['img'] == '') {
                        echo '        <img src="img/UserGestion.png" alt="" class="iconModUser">';
                    } else {
                        echo '        <img src="img/Usuarios/' . $row['img'] . '" alt="" class="iconModUser">';
                    }
                    echo '        <div class="textMod">';
                    echo '            <h2>' . $row['nombre'] . ' ' . $row['apellido'] . '</h2>';
                    echo '            <div class="hrMod"><hr></div>';
                    if ($row['usuario'] == md5('Desarrollador')) {
                        echo '            <p class="pMod">Desarrollador</p>';
                    } else if ($row['usuario'] == md5('Super Admin')) {
                        echo '            <p class="pMod">Super Admin</p>';
                    } else if ($row['usuario'] == md5('Admin')) {
                        echo '            <p class="pMod">Administrador</p>';
                    }
                    echo '            <input type="hidden" id="permisos_user" value="' . $permisos_user_edit[$i] . '">';
                    echo '            <p id="telefono_usuario">' . $row['telefono'] . '</p>';
                    echo '            <p><span id="correo_usuario">' . $row['correo'] . '</span></p>';
                    echo '        </div>';
                    echo '    </div>';
                    if ($permisos[12] == 1) {
                        echo '    <div class="ModPerfilButtons">';
                        echo '        <button onclick="administrar_permisos(' . $id_user_edit[$i] . ')">Modificar Permisos</button>';
                        echo '    </div>';
                    }
                    echo '</div>';
                    $i += 1;
                }
                ?>
            </div> <!-- FIN DE admModShape -->
        </div>

        <?php if ($permisos[12] == 1) {
            echo '
  <div class="ModerarPrioridad" style="display:none;">
    <div class="modPrioridadShape">
        <span class="tituloGmo-container">
            <h3 class="tituloGmo">Administrar Permisos</h3>
            <br>
            <button id="btncerrarmod" onclick="cerrarContenedor()" class="btnCierre">X</button>
        </span>

        <form id="editar_permisos_de_usuario" method="POST" action="./componentes/administrador/admModeradores.php">
            <div class="infoOpcionPrioridad">
                <img src="img/UserGestion.png" alt="" class="iconModPrioridad">
                <div class="infoTexto">
                    <h3 id="nombre_user_edit">Nombre de Usuario</h3>
                    <p id="tipo_user_edit">Tipo de Usuario</p>
                    <p id="tlf_user_edit">(0000)-000-0000</p>
                    <p><span id="email_user_edit">correo_electronico@gmail.com</span></p>
                </div>
            </div>

            <div class="opcionPrioridad">
                <div class="checkbox">
                    <input type="checkbox" id="op-0" name="op-0" value="1">
                    <input type="checkbox" id="op-1" name="op-1" value="1">
                    <input type="checkbox" id="op-2" name="op-2" value="1">
                    <input type="checkbox" id="op-3" name="op-3" value="1">
                    <input type="checkbox" id="op-4" name="op-4" value="1">
                    <input type="checkbox" id="op-5" name="op-5" value="1">
                    <input type="checkbox" id="op-6" name="op-6" value="1">
                    <input type="checkbox" id="op-7" name="op-7" value="1">
                    <input type="checkbox" id="op-8" name="op-8" value="1">
                    <input type="checkbox" id="op-9" name="op-9" value="1">
                    <input type="checkbox" id="op-10" name="op-10" value="1">
                    <input type="checkbox" id="op-11" name="op-11" value="1">
                    <input type="checkbox" id="op-12" name="op-12" value="1">
                    <input type="hidden" id="id_del_usuario" name="id_del_usuario">
                    <input type="hidden" id="editar_permisos" name="editar_permisos">
                </div>

                <div class="op_container"><p>Referencia de Movimientos</p><label for="op-1" class="checkbox_op" onclick="check_on(1)"><div></div></label></div>
                <div class="op_container"><p>Crear usuarios</p><label for="op-0" class="checkbox_op" onclick="check_on(0)"><div></div></label></div>
                <div class="op_container"><p>Consultar usuarios</p><label for="op-2" class="checkbox_op" onclick="check_on(2)"><div></div></label></div>
                <div class="op_container"><p>Registrar usuarios</p><label for="op-3" class="checkbox_op" onclick="check_on(3)"><div></div></label></div>
                <div class="op_container"><p>Historial de aportes</p><label for="op-4" class="checkbox_op" onclick="check_on(4)"><div></div></label></div>
                <div class="op_container"><p>Editar usuarios</p><label for="op-5" class="checkbox_op" onclick="check_on(5)"><div></div></label></div>
                <div class="op_container"><p>Eliminar usuarios</p><label for="op-6" class="checkbox_op" onclick="check_on(6)"><div></div></label></div>
                <div class="op_container"><p>Registrar aportes</p><label for="op-7" class="checkbox_op" onclick="check_on(7)"><div></div></label></div>
                <div class="op_container"><p>Confirmar aportes</p><label for="op-8" class="checkbox_op" onclick="check_on(8)"><div></div></label></div>
                <div class="op_container"><p>Registro de donaciones</p><label for="op-9" class="checkbox_op" onclick="check_on(9)"><div></div></label></div>
                <div class="op_container"><p>Registro de ingreso</p><label for="op-10" class="checkbox_op" onclick="check_on(10)"><div></div></label></div>
                <div class="op_container"><p>Registro de egreso</p><label for="op-11" class="checkbox_op" onclick="check_on(11)"><div></div></label></div>
                <div class="op_container"><p>Establecer permisos</p><label for="op-12" class="checkbox_op" onclick="check_on(12)"><div></div></label></div>
            </div>
        </form>
        
        <div class="contenedor_botones">
   
            <button class="deletePrioridad" onclick="revocarPrivilegios();">Revocar los privilegios de Administrador</button>
            <div id="boton_eviar" onclick="enviar_form_opction();">Guardar Cambios</div>
        </div>
    </div>
</div>
';
        } else {
            echo '';
        }
        ?>
    </section> <!-- //////// FIN DE LA GESTION DE MODERADORES ////////// -->



</body>

</html>