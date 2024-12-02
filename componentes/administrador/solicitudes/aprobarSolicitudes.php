<?php
session_start();

$servidor = "localhost";
$usuariobd = "root";
$conts = "";
$baseDato = "sacips_bd";

// Establecer la zona horaria a "America/Caracas"
date_default_timezone_set('America/Caracas');

$con = mysqli_connect($servidor, $usuariobd, $conts, $baseDato);

if (!$con) {
    die("Conexión fallida: " . mysqli_connect_error());
}
mysqli_set_charset($con, "utf8mb4");

$sql = "
    SELECT  scc.id, u.tipo_usuario, p.nombre COLLATE utf8mb4_general_ci AS nombre, scc.nuevo_correo, NULL AS nuevo_telefono, scc.estado
    FROM solicitudes_cambio_correo scc
    JOIN usuarios u ON scc.id_usuario = u.id_usuario JOIN personas p ON u.id_persona = p.id_Personas
    WHERE scc.estado = 'pendiente'
    
    UNION ALL
    
    SELECT  sct.id, u.tipo_usuario, p.nombre COLLATE utf8mb4_general_ci AS nombre, NULL AS nuevo_correo, sct.nuevo_telefono, sct.estado
    FROM solicitudes_cambio_telefono sct
    JOIN personas p ON sct.id_persona = p.id_Personas JOIN usuarios u ON p.id_Personas = u.id_persona
    WHERE sct.estado = 'pendiente';
";
$result = $con->query($sql);



if ($result === false) {
    echo "Error en la consulta: " . mysqli_error($con);
} elseif (mysqli_num_rows($result) === 0) {
    // No hay resultados
    echo "<div class='solicitudShape'>";
    echo "<div class='notiSolicitud'>";
    echo "<div class='infoSolicitud'>";
    echo "<h1 class='soliTitle'> Sin Solicitudes Pendientes </h1>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<div class='solicitudShape'>";
    echo "<div class='notiSolicitud'>";
    // Hay resultados, procesalos
    while ($row = $result->fetch_assoc()) {

        echo "<hr>";
        echo "<div class='infoSolicitud'>";
        echo "<p class='textoNoti'> El Usuario: " . htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8') . "</p>";

        if ($row['tipo_usuario'] == 1) {
            echo "<p class='textoNoti'> Tipo: Afiliado </p>";
        } else if ($row['tipo_usuario'] == 2) {
            echo "<p class='textoNoti'> Tipo: Invitado </p>";
        } else if ($row['tipo_usuario'] == 3) {
            echo "<p class='textoNoti'> Tipo: Consejo de Vigilancia </p>";
        }
        ;

        if (!empty($row['nuevo_correo'])) {
            echo " <p class='textoNoti'> Quiere actualizar su correo a: " . htmlspecialchars($row['nuevo_correo'], ENT_QUOTES, 'UTF-8') . "</p>";
            echo "<div class='buttonSeparate'>";
            echo "<button class='rechazado' onclick=\"manejarSolicitud(" . $row['id'] . ", 'rechazado')\">Rechazar</button>";
            echo "<div class='buttonSeparate'> </div>";
            echo "<button class='aprobado' onclick=\"manejarSolicitud(" . $row['id'] . ", 'aprobado')\">Aprobar</button>";
            echo "</div>";
        }

        if (!empty($row['nuevo_telefono'])) {
            echo "<p class='textoNoti'> Quiere actualizar su teléfono a: " . htmlspecialchars($row['nuevo_telefono'], ENT_QUOTES, 'UTF-8') . "</p>";
            echo "<div class='buttonSeparate'>";

            echo "<button class='rechazado' onclick=\"manejarSolicitud_tf(" . $row['id'] . ", 'rechazado')\">Rechazar</button>";

            echo "<button class='aprobado' onclick=\"manejarSolicitud_tf(" . $row['id'] . ", 'aprobado')\">Aprobar</button>";
            echo "</div>";
        }



        echo "</div>";
        echo "<hr>";
    }
    echo "</div>";
    echo "</div>";
}
$con->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Solicitudes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;

        }

        .buttonSeparate {
            width: 100%;
            display: flex;
            justify-content: space-between;
            box-sizing: border-box;
        }

        .body {
            background-color: #f4f4f9;
            width: 100%;
            color: #333;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
        }

        .solicitudShape {
            position: absolute;
            top: 0;
            left: 40%;
            width: 100%;
            max-width: 600px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: 10px 0;
            padding: 20px;
        }

        .notiSolicitud {
            border-left: 4px solid #4caf50;
            padding-left: 16px;
        }

        .soliTitle {
            font-size: 24px;
            margin-bottom: 10px;
            color: #4caf50;
        }

        .infoSolicitud {
            margin: 15px 0;
        }

        .textoNoti {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .body button {
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 15px;

            font-size: 16px;
            cursor: pointer;
            margin-right: 5px;
            transition: background-color 0.3s;
        }

        .body button:hover {
            background-color: #45a049;
        }

        .rechazado {
            background-color: #a83e3e;
            color: white;
            padding: 1%;
            border: none;
            width: 20%;
        }


        .aprobado {
            background-color: #636fce;
            color: white;
            padding: 1%;
            border: none;
            width: 20%;
        }


        .rechazado:hover {
            background-color: #e53935;
        }

        /* Responsividad */
        @media (max-width: 600px) {
            .body {
                padding: 10px;
            }

            .solicitudShape {
                width: 100%;
                padding: 10px;
            }

            ..body button {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>


</head>

<body>



</body>

</html>