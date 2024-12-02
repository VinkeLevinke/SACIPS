<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuentas institución</title>
    <link rel="stylesheet" href="./style/mostrarCuenta.css">
</head>
<body class="bodyMostrarCuenta">
    <div>
        <h3 class="cuenta-titulo">Cuentas de la institución</h3>
    </div>
    <section class="cuenta-seccion">
        <?php
        require '../../componentes/conexiones/conInfo_ipspuptyab.php';

        // Verificar si hay resultados y mostrarlos
        if ($resultCuentas->num_rows > 0) {
            while ($row = $resultCuentas->fetch_assoc()) {
                echo '<div class="cuenta-item">';
                echo '<h3 class="cuenta-titulo">' . htmlspecialchars($row['propietario_cuenta']) . '</h3>';
                echo '<p class="cuenta-info"><strong>Tipo de operación:</strong> ' . htmlspecialchars($row['nombre_tipo_operacion']) . '</p>';
                echo '<p class="cuenta-info"><strong>Banco:</strong> ' . htmlspecialchars($row['nombre_banco']) . '</p>';
                echo '<p class="cuenta-info"><strong>Número de Cuenta:</strong> ' . htmlspecialchars($row['numero_cuenta']) . '</p>';
                echo '<p class="cuenta-info"><strong>Tipo de Cuenta:</strong> ' . htmlspecialchars($row['tipo_cuenta']) . '</p>';
                echo '<p class="cuenta-info"><strong>Teléfono:</strong> ' . htmlspecialchars($row['telefono_cuenta']) . '</p>';
                echo '<p class="cuenta-info"><strong>Información Adicional:</strong> ' . htmlspecialchars($row['informacion_adicional']) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No se encontraron cuentas.</p>';
        }

        // Cerrar la conexión
        $db->closeConnection();
        ?>
    </section>
</body>
</html>
