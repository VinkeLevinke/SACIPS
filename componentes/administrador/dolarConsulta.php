<?php
// Conexión
$servername = "localhost";
$username = "root";
$password = "";
$bdname = "sacips_bd";
$conex = new mysqli($servername, $username, $password, $bdname);

// Verifica la conexión
if ($conex->connect_error) {
    die("Conexión fallida: " . $conex->connect_error);
}

// Establecer la zona horaria a "America/Caracas"
date_default_timezone_set('America/Caracas');

// Manejo de la eliminación
if (isset($_POST['delete'])) {
    $sql = "DELETE FROM dolar_diario WHERE id=1"; // Siempre eliminar ID=1
    if ($conex->query($sql) === TRUE) {
        echo "<script>alert('Precio eliminado'); location.assign('../../admin.php');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conex->error;
    }
}

// Manejo de la edición
if (isset($_POST['edit'])) {
    $precio_dolar = $_POST['precio_dolar']; // Solo se edita el precio
    $fecha_actualizacion = date('Y-m-d'); // Fecha actual
    $hora_actualizacion = date('H:i:s'); // Hora actual

    // Imprimir valores para depuración
    echo "Precio a actualizar: $precio_dolar<br>";

    // Actualizar el precio y la fecha y hora de actualización
    $sql = "UPDATE dolar_diario SET precio='$precio_dolar', fecha='$fecha_actualizacion', hora_actualizacion='$hora_actualizacion' WHERE id=1"; // Siempre actualizar ID=1
    if ($conex->query($sql) === TRUE) {
        echo "<script>alert('Precio actualizado'); location.assign('../../admin.php');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conex->error; // Captura de error
    }
}

// Consulta para mostrar el precio del dólar
$sqlBD = "SELECT * FROM dolar_diario WHERE id=1"; // Solo obtener ID=1
$resultado = mysqli_query($conex, $sqlBD);
$row = mysqli_fetch_assoc($resultado);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Precio del Dólar</title>
    <link rel="stylesheet" href="../../style/style-afiliados2.css">
    <style>
        /* Estilo del modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 500px;
            display: flex;
            flex-direction: column;
            margin-top: 20%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        h1 {
            width: 100%;
            height: 100px;
            background-color: white;
            margin: 0px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0px 0px 12px 1px gray;
            margin-bottom: 20px;
        }
        table {
            margin-top: 20px;
            width: 95%;
        }
    </style>
</head>
<body>
    <?php include "../../componentes/template/admHeader.php"; ?>
    <h1>Administrar Precio del Dólar</h1>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Precio del Dólar</th>
                    <th>Fecha</th>
                    <th>Hora de Actualización</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $row['precio'] ?></td>
                    <td><?= $row['fecha'] ?></td>
                    <td><?= $row['hora_actualizacion'] ?></td>
                    <td>
                        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                            <button type="button" onclick="openModal('<?= $row['precio'] ?>')">Editar</button>
                            <button type="button" onclick="openDeleteModal()">Eliminar</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal para editar -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                <label for="precio_dolar">Precio del Dólar:</label>
                <input type="text" id="precio_dolar" name="precio_dolar" required>
                <button name="edit" type="submit">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <!-- Modal para confirmar eliminación -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <p>¿Está seguro de que desea eliminar el precio del dólar?</p>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                <button name="delete" type="submit">Sí, eliminar</button>
                <button type="button" onclick="closeDeleteModal()">Cancelar</button>
            </form>
        </div>
    </div>
</body>
</html>
