<?php
include '../../componentes/conexiones/conexionbd.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Consulta para obtener el capture
    $sql = "SELECT capture FROM aportes_afiliados WHERE id_aporte = $id
    UNION ALL 
    SELECT capture FROM aportes_donaciones where id_AportesDona = $id";

    $result = $con->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'capture' => $row['capture']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No capture found']);
    }
    
} else {
    echo json_encode(['success' => false]);
}
