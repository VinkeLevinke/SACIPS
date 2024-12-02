<?php
session_start();
if (!isset($_SESSION["id_usuarios"])) {
    include_once "../../componentes/conexiones/permisosAdmin.php";
}

include('../conexiones/conexionbd.php');

// Primer consulta: obtener todos los usuarios de la tabla `personas`
$sql_personas = "SELECT id_Personas, nombre, apellido, cedula, telefono FROM personas WHERE 1=1"; // Agregado WHERE 1=1 para facilitar futuras condiciones
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
}

// Segunda consulta: obtener usuarios relacionados
$sql_usuarios = "SELECT u.id_persona, u.nombre_usuario, u.tipo_usuario, u.correo FROM usuarios u";
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
}


$Password = '';
$conn = new mysqli("localhost", "root", "", "sacips_bd");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener la ID del usuario desde la cookie
$user_id = $_COOKIE['User_ID'];

if ($stmt = $conn->prepare("SELECT clave FROM usuario_admins WHERE id_usuarios = ?")) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($Password);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Error preparando la consulta: " . $conn->error;
}


// Contar usuarios por tipo
$sql_contar_usuarios = "SELECT tipo_usuario, COUNT(*) as cantidad FROM usuarios GROUP BY tipo_usuario";
$result_contar_usuarios = $con->query($sql_contar_usuarios);

// Arreglo para guardar el conteo
$conteo_usuarios = [
    'Afiliado' => 0,
    'Vigilante' => 0,
    'Invitado' => 0,
    'Director' => 0
];

if ($result_contar_usuarios && $result_contar_usuarios->num_rows > 0) {
    while ($row = $result_contar_usuarios->fetch_assoc()) {
        switch ($row['tipo_usuario']) {
            case 1:
                $conteo_usuarios['Afiliado'] += $row['cantidad'];
                break;
            case 2:
                $conteo_usuarios['Invitado'] += $row['cantidad'];
                break;
            case 3:
                $conteo_usuarios['Director'] += $row['cantidad'];
                break;
            case 4:
                $conteo_usuarios['Vigilante'] += $row['cantidad'];
                break;
        }
    }
}



$conn->close();
$con->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
    <title>SACIPS | Egresos</title>
    <link rel="stylesheet" href="../style/admin.css">
    <style>
        /* Estilos para la interfaz (incluir los que proporciones) */
        #PermisoRequire {
            width: 100%;
            height: 100vh;
            position: absolute;
            z-index: 1000;
            left: 0px;
            top: 0px;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #00000020;
            display: none;
            opacity: 0;
            transition: 0.5s;
        }

        #PermisoRequire div {
            width: 500px;
            height: fit-content;
            background-color: white;
            box-sizing: border-box;
            padding: 2%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        #PermisoRequire div button {
            width: 49.5%;
            padding: 10px;
            margin-top: 10px;
            background-color: #5552ff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.2s;
        }

        #PermisoRequire div button:hover {
            background-color: #3878f8;
        }

        #PermisoRequire div input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            margin-top: 10px;
        }

        #PermisoRequire div #cancel,
        #PermisoRequire div #E-cancel {
            background-color: #f44336;
        }

        #PermisoRequire div #cancel:hover,
        #PermisoRequire div #E-cancel:hover {
            background-color: #da190b;
        }

        .error-message {
            margin-top: 10px !important;
            width: 100% !important;
        }

        #PermisoRequire #eliminar {
            display: none;
        }

        /* Contenedor de conteo de usuarios */
        .user-counts {
            margin: 20px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .user-counts h3 {
            margin: 0;
            font-size: 1.5rem;
            color: #2e2e2e;
        }
    </style>
</head>

<body class="bodyGstion">

<header class="headerGestion">
    <div class="headerGeneral">
        <div class="buttonsHeader">
            <button id="generalUsers" class="headerButtons general focus">General</button>
            <button id="gstModeradores" class="headerButtons link-btn">Moderadores</button>
        </div>
        <div class="inputs">
            <select id="userType" class="select-custom buscarpor" onchange="filterData()">
            <option value="" disabled selected>Buscar por tipo de usuario (opcional)</option>

                <option value="" >Todos</option>
                <option value="Afiliado">Afiliado</option>
                <option value="Vigilante">Vigilante</option>
                <option value="Director">Director</option>
                <option value="Invitado">Invitado</option>
            </select>
            <select class="select-custom buscarpor" id="filterBy" name="filterBy">
                <option value="" disabled selected>Seleccionar tipo de búsqueda</option>
                <option value="nombre">Nombre y Apellido</option>
                <option value="correo">Correo</option>
                <option value="cedula">Cédula</option>
            </select>
            <input class="busquedaHeader" type="text" placeholder="Búsqueda" oninput="filterData()">
        </div>
    </div>
</header>





    <div class="user-counts">
        <h3>Conteo de Usuarios Registrados</h3>
        <p>Afiliados: <?php echo $conteo_usuarios['Afiliado']; ?></p>
        <p>Vigilantes: <?php echo $conteo_usuarios['Vigilante']; ?></p>
        <p>Invitados: <?php echo $conteo_usuarios['Invitado']; ?></p>
        <p>Directores: <?php echo $conteo_usuarios['Director']; ?></p>
    </div>

    <section id="gstionBody" class="gstionBody">
        <div id="personasContainer">
            <?php
            if (!empty($personas)):
                foreach ($personas as $persona):
                    $usuarioEncontrado = null;
                    foreach ($usuarios as $usuario) {
                        if ($usuario['id_persona'] == $persona['id_Personas']) {
                            $usuarioEncontrado = $usuario;
                            break;
                        }
                    }

                    $tipoUsuario = 'N/A';
                    if ($usuarioEncontrado) {
                        switch ($usuarioEncontrado['tipo_usuario']) {
                            case 1:
                                $tipoUsuario = 'Afiliado';
                                break;
                            case 2:
                                $tipoUsuario = 'Invitado';
                                break;
                            case 3:
                                $tipoUsuario = 'Director';
                                break;
                            case 4:
                                $tipoUsuario = 'Vigilante';
                                break;
                            default:
                                $tipoUsuario = 'Desconocido';
                        }
                    }
                    ?>
                    <div class="afiliados" data-nombre="<?php echo strtolower($persona['nombre']); ?>"
                        data-apellido="<?php echo strtolower($persona['apellido']); ?>"
                        data-correo="<?php echo strtolower($usuarioEncontrado ? $usuarioEncontrado['correo'] : ''); ?>"
                        data-cedula="<?php echo strtolower($persona['cedula']); ?>"
                        data-tipo-usuario="<?php echo $tipoUsuario; ?>">
                        <!-- Agregado el nuevo atributo -->
                        <div class="datos">
                            <div class="datosSeparacion">
                                <img src="img/UserGestion.png" alt="" class="iconGstion">
                                <div class="listasSeparadas">
                                    <div class="listSeparation">
                                        <p class="titleType"><?php echo $tipoUsuario; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="datosAfiliados">
                                <div class="listSeparation">
                                    <p class="titleDatos">Tipo</p>
                                    <p class="tipoAfiliado"><?php echo $tipoUsuario; ?></p>
                                </div>
                                <div class="listSeparation">
                                    <p class="titleDatos">Nombre</p>
                                    <p class="nombre"><?php echo $persona['nombre'] . ' ' . $persona['apellido']; ?></p>
                                </div>
                                <div class="listSeparation">
                                    <p class="titleDatos">Correo</p>
                                    <p class="email"><?php echo ($usuarioEncontrado ? $usuarioEncontrado['correo'] : 'N/A'); ?>
                                    </p>
                                </div>
                                <div class="listSeparation">
                                    <p class="titleDatos">Teléfono</p>
                                    <p class="telefono"><?php echo $persona['telefono']; ?></p>
                                </div>
                                <div class="listSeparation">
                                    <p class="titleDatos">Cédula/RIF</p>
                                    <p class="cedula"><?php echo $persona['cedula']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="buttons">
                            <button onclick="HistorialAportes(<?php echo $persona['id_Personas']; ?>)">Historial de
                                Aportes</button>
                            <button class="editar"
                                onclick="EditarUsuarios(<?php echo $persona['id_Personas']; ?>, '`<?php echo $tipoUsuario; ?>`')">Editar</button>
                            <button class="eliminar"
                                onclick="EliminarUsuarios('`<?php echo $persona['id_Personas']; ?>`')">Eliminar</button>
                        </div>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>
        </div>

       
    

    </section>
    <div id="PermisoRequire">
        <div id="editar">
            <p>Se requieren permisos de administrador. Por favor, ingrese su contraseña de administrador para continuar
            </p>
            <input type="password" id="adminPassword">
            <p id="error-message" class="error-message"></p>
            <button id="cancel" onclick="editarAdmin('cancel');">Cancelar</button>
            <button id="continuar" onclick="editarAdmin('submit', true);">Continuar</button>
            <input type="hidden" id="hiddenPassword" value="<?php echo $Password; ?>">
        </div>
        <div id="eliminar">
            <p>¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.</p>
            <p id="error-message" class="error-message"></p>
            <button id="E-cancel" onclick="EliminarUser('cancel','');">Cancelar</button>
            <button id="E-continuar" onclick="EliminarUser('submit','');">Continuar</button>
        </div>
    </div>
</body>

</html>