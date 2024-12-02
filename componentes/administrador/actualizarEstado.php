<?php
include '../../componentes/conexiones/conexionbd.php';
session_start();

if (!isset($_SESSION["id_usuarios"])) {
    include_once "../../componentes/conexiones/permisosAdmin.php";
}

function GuardarSaldo($id_aporte, $con) {
    // Consulta para obtener id_persona y monto desde la tabla aportes
    $sql = "SELECT id_persona, monto FROM aportes_afiliados WHERE id_aporte = $id_aporte";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        // Obtener los datos de la fila resultante
        $row = $result->fetch_assoc();
        $id_persona = $row['id_persona'];
        $monto = floatval(str_replace(',', '.', $row['monto']));

        // Consulta para obtener el saldo actual del usuario
        $saldo_sql = "SELECT saldo FROM usuarios WHERE id_persona = $id_persona";
        $saldo_result = $con->query($saldo_sql);

        if ($saldo_result->num_rows > 0) {
            // Obtener el saldo actual
            $saldo_row = $saldo_result->fetch_assoc();
            $saldo_actual = floatval($saldo_row['saldo']);

            // Sumar el nuevo monto al saldo actual
            $nuevo_saldo = $saldo_actual + $monto;

            // Asegurarse de que el nuevo saldo esté formateado correctamente
            $nuevo_saldo_formateado = number_format($nuevo_saldo, 2, '.', '');

            // Actualizar el saldo en la tabla usuarios
            $update_sql = "UPDATE usuarios SET saldo = $nuevo_saldo_formateado WHERE id_persona = $id_persona";

            if ($con->query($update_sql) === TRUE) {
                echo "Saldo actualizado exitosamente para el usuario con ID $id_persona";
            } else {
                echo "Error al actualizar el saldo: " . $con->error;
            }
        } else {
            echo "No se encontró ningún usuario con id_persona = $id_persona";
        }
    } else {
        echo "No se encontró ningún registro con id_aporte = $id_aporte";
    }
}


if (isset($_POST['id']) && isset($_POST['accion']) && isset($_POST['tipo']) && isset($_POST['usuario']) && isset($_POST['fecha'])) {
    $id = $_POST['id'];
    echo $id;
    $accion = $_POST['accion']; // 'aprobar' o 'declinar'
    $tipo = $_POST['tipo'];
    $usuario = $_POST['usuario'];
    $fecha = $_POST['fecha'];

    $estado = ($accion == 'aprobar') ? 'Aprobado' : 'Declinado';

    if ($usuario == 'Afiliado') {
        $sql = "UPDATE aportes_afiliados SET estado = '$estado' WHERE id_aporte = $id";
        if ($accion == 'aprobar') {
            GuardarSaldo($id, $con);
        }
    } elseif ($usuario == 'Invitado') {
        $sql = "UPDATE aportes_donaciones SET estado = '$estado', fechaRecibido = '$fecha' WHERE id_AportesDona = $id";
    }

    if ($con->query($sql) === TRUE) {
        // Obtener los datos del usuario desde las tablas correctas
        if ($usuario == 'Afiliado') {
            $sqlUsuario = "SELECT u.correo, CONCAT(p.nombre, ' ', p.apellido) AS nombre, a.monto 
                           FROM usuarios u 
                           JOIN personas p ON u.id_persona = p.id_Personas 
                           JOIN aportes_afiliados a ON a.id_persona = p.id_Personas 
                           WHERE a.id_aporte = $id AND u.tipo_usuario = 1";
        } elseif ($usuario == 'Invitado') {
            $sqlUsuario = "SELECT u.correo, CONCAT(p.nombre, ' ', p.apellido) AS nombre, ad.montoRecibido AS monto 
                           FROM usuarios u 
                           JOIN personas p ON u.id_persona = p.id_Personas 
                           JOIN aportes_donaciones ad ON ad.id_persona = p.id_Personas 
                           WHERE ad.id_AportesDona = $id AND u.tipo_usuario = 2";
        }

        $resultUsuario = $con->query($sqlUsuario);

        if ($resultUsuario->num_rows > 0) {
            $row = $resultUsuario->fetch_assoc();
            $correo = $row['correo'];
            $nombre = $row['nombre'];
            $monto = $row['monto'];

            // Enviar los datos a Node.js para enviar el correo
            $data = array(
                'correo' => $correo,
                'nombre' => $nombre,
                'monto' => $monto,
                'estado' => $estado
            );

            $options = array(
                'http' => array(
                    'header' => "Content-Type: application/json\r\n",
                    'method' => 'POST',
                    'content' => json_encode($data)
                )
            );

            $context = stream_context_create($options);
            $result = file_get_contents('https://test-nodejs-fcya.onrender.com/send-approval-email', false, $context);

            if ($result === FALSE) {
                echo 'Error al enviar el correo.';
            } else {
                echo 'Correo enviado correctamente.';
            }
        } else {
            echo 'No se encontraron datos del usuario.';
        }
    } else {
        echo "Error al actualizar la referencia: " . $con->error;
    }
} else {
    echo "ID o tipo no especificado.";
}

$con->close();
