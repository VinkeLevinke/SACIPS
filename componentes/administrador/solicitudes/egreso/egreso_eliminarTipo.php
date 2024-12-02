<?php 

$conn = new mysqli("localhost", "root", "", "sacips_bd");


if (isset($_POST['id_TipoEgreso']) && $_POST['accion'] === 'eliminar_tipo') {
    $idTipoEgreso = $_POST['id_TipoEgreso'];

    // Consulta para eliminar el tipo de egreso
    $sql = "DELETE FROM tipo_egreso WHERE id_TipoEgreso = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idTipoEgreso);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
    $conn->close();
}
?>
