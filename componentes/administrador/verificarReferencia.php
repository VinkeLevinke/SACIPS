<?php
include '../../componentes/conexiones/conexionbd.php';
session_start();

if(!isset($_SESSION["id_usuarios"])){
include_once"../../componentes/conexiones/permisosAdmin.php";
}

if (isset($_POST['id']) && isset($_POST['accion'])) {
    $id = $_POST['id'];
    $accion = $_POST['accion']; // 'aprobar' o 'declinar'

    $estado = ($accion == 'aprobar') ? 'Aprobado' : 'Declinado';

    $sql = "UPDATE aportes_afiliados SET estado = '$estado' WHERE id_aporte = $id";
    if ($conex->query($sql) === TRUE) {
        echo "Referencia actualizada correctamente";
    } else {
        echo "Error al actualizar la referencia: " . $conex->error;
    }
}

$conex->close();
?>
