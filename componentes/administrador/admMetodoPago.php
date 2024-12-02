<?php
$conn = new mysqli("localhost", "root", "", "sacips_bd");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}


// Definimos la cantidad de filas por página
$filasPorPagina = 5;

// Determinamos la página actual
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($paginaActual < 1) $paginaActual = 1;
$offset = ($paginaActual - 1) * $filasPorPagina;

// Consultamos el total de filas
$sqlTotal = "SELECT COUNT(*) as total FROM tipo_operacion";
$resultTotal = $conn->query($sqlTotal);
$totalFilas = $resultTotal->fetch_assoc()['total'];
$totalPaginas = ceil($totalFilas / $filasPorPagina);

// Consultamos las filas de la página actual
$sql = "SELECT * FROM tipo_operacion LIMIT $offset, $filasPorPagina";
$result = $conn->query($sql);

// Parte para contar métodos de pago por categoría
$sqlCountDigital = "SELECT COUNT(*) as total FROM tipo_operacion WHERE categoria_pago='DIGITAL'";
$sqlCountFisico = "SELECT COUNT(*) as total FROM tipo_operacion WHERE categoria_pago='FISICO'";

$resultDigital = $conn->query($sqlCountDigital);
$resultFisico = $conn->query($sqlCountFisico);

$countDigital = $resultDigital->fetch_assoc()['total'];
$countFisico = $resultFisico->fetch_assoc()['total'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_tipoOperacion = $_POST["id_tipoOperacion"];
    $tipo = strtoupper($_POST["tipo"]);
    $categoriaPago = strtoupper($_POST["categoriaPagoEditar"]); // Agregar la categoría de pago
    $accion = $_POST["accion"];

    // Inicializamos un arreglo para las actualizaciones
    $updateQueries = [];

    if ($accion == "editar_tipo") {
        // Verificamos si se debe actualizar el tipo
        if (!empty($tipo)) {
            $updateQueries[] = "tipo='$tipo'";
        }

        // Verificamos si se debe actualizar la categoría de pago
        if (!empty($categoriaPago)) {
            $updateQueries[] = "categoria_pago='$categoriaPago'";
        }

        if (count($updateQueries) > 0) {
            $sql = "UPDATE tipo_operacion SET " . implode(", ", $updateQueries) . " WHERE id_tipoOperacion=$id_tipoOperacion";

            if ($conn->query($sql) === TRUE) {
                echo "<script>window.location.href='../../admin.php';</script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

if (isset($_GET['pagina'])) {
    // Aquí retorna sólo los resultados y la paginación en formato JSON
    $response = [];

    // Generar HTML para la tabla
    $html = '';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $html .= "<tr data-id='{$row['id_tipoOperacion']}' data-categoria='{$row['categoria_pago']}'>
                            <td>{$row['id_tipoOperacion']}</td>
                            <td>{$row['tipo']}</td>
                            <td>{$row['categoria_pago']}</td>
                            <td>
                                <button type='button' class='btn-edit' onclick=\"abrirModal({$row['id_tipoOperacion']}, '{$row['tipo']}')\">Modificar</button>
                                <button type='button' class='btn-delete' onclick=\"abrirModalEliminar({$row['id_tipoOperacion']}, '{$row['tipo']}')\">Remover</button>
                            </td>
                        </tr>";
        }
    } else {
        $html .= "<tr><td colspan='4'>No hay métodos de pago registrados</td></tr>";
    }

    $response['html'] = $html;

    // Generar el HTML para paginación
    $paginationHtml = '';
    if ($paginaActual > 1) {
        $paginationHtml .= "<a href='javascript:void(0)' onclick='cambiarPagina(" . ($paginaActual - 1) . ")'>&laquo; Anterior</a>";
    }

    for ($i = 1; $i <= $totalPaginas; $i++) {
        $paginationHtml .= "<a href='javascript:void(0)' onclick='cambiarPagina($i)' class='" . ($i == $paginaActual ? 'active' : '') . "'>$i</a>";
    }

    if ($paginaActual < $totalPaginas) {
        $paginationHtml .= "<a href='javascript:void(0)' onclick='cambiarPagina(" . ($paginaActual + 1) . ")'>Siguiente &raquo;</a>";
    }

    $response['pagination'] = $paginationHtml;

    // Este es el montón de respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit; // Asegúrate de terminar aquí para no imprimir nada más
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Métodos de Pago</title>
    <link rel="stylesheet" href="./style/modales.css">

    <script src="script.js"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        .bodyMetodoPago {

            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Agregar este estilo para los selects en tu CSS existente */

        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: white;
            color: #333;
            font-size: 16px;
            appearance: none;
            /* Eliminar el estilo del select por defecto */
            background-image: url('./img/lowArrow.svg');
            /* Asegúrate de agregar la flecha si deseas */
            background-repeat: no-repeat;
            background-position: right 10px center;
            /* Colocar la flecha a la derecha */
            background-size: 15px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        /* Estilo al hacer hover */
        select:hover {
            border-color: #0078d4;
            /* Cambiar el color del borde al pasar el mouse */
            box-shadow: 0 0 5px rgba(0, 120, 212, 0.5);
            /* Sombra sutil */
        }

        /* Estilo al recibir foco */
        select:focus {
            border-color: #0078d4;
            /* Cambiar el color del borde al recibir foco */
            box-shadow: 0 0 5px rgba(0, 120, 212, 0.5);
            /* Sombra sutil */
            outline: none;
            /* Remover contorno por defecto */
        }

        h1 {
            font-size: 24px;
            margin: 0;
        }

        .btn-add {
            background-color: #636fce;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .btn-add img {
            margin-right: 8px;
        }

        .table-container {
            margin-top: 20px;
            overflow-x: auto;
            /* Para permitir el scroll horizontal si es necesario */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background-color: #636fce;
            color: white;
            padding: 10px;
        }

        tbody td {
            border-bottom: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }



        input[type="text"],
        input[type="radio"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="radio"] {
            width: auto;
        }

        /* Estilo para mobile */
        @media (max-width: 600px) {
            .table-container {
                overflow-x: scroll;
            }
        }

        .filter-container {
            margin: 20px 0;
        }

        .filter-input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
            width: calc(60% - 10px);
        }

        .filter-button {
            padding: 10px 15px;
            background-color: #0078d4;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .contadorCategorias {
            padding: 10px;
            background-color: #636fce;
            color: white;
            border-radius: 5px;
            margin-top: 10px;
            text-align: center;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px 0;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .pagination a {
            margin: 0 10px;
            text-decoration: none;
            font-weight: bold;
            color: #0078d4;
            padding: 10px 15px;
            background-color: #e7eef3;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination a.active {
            background-color: #636fce;
            color: white;
            pointer-events: none;
            /* Desactiva el evento clic en el botón activo */
        }

        .pagination a:hover {
            background-color: #d2d8e1;
            color: #0056a1;
            text-decoration: none;
        }

        .pagination a:first-child {
            margin-left: 0;
            /* Elimina margen izquierdo del primer elemento */
        }

        .pagination a:last-child {
            margin-right: 0;
            /* Elimina margen derecho del último elemento */
        }



        .transition-container {
            position: relative;
            overflow: hidden;
        }

        .table-transition {
   transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
   opacity: 1; /* Asegúrate de que el estado inicial es visible */
}

.table-enter {
   transform: translateX(0);
   opacity: 1;
}

.table-leave {
   transform: translateX(-100%);
   opacity: 0; /* Oculte el contenido */
}

    </style>
</head>

<body class="bodyMetodoPago">
    <div class="container">
        <header class="header">
            <h1>Método de Pago</h1>
            <button type="button" onclick="abrirAdd_MetdoPago()" class="btn-add">
                <img src="./img/add.svg" alt="Agregar" class="img-add">
                <span>Nuevo Registro</span>
            </button>
        </header>

        <!-- Nueva sección para mostrar conteos -->
        <div class="contadorCategorias">
            <p>Métodos de Pago Digital: <strong id="contadorDigital"><?php echo $countDigital; ?></strong></p>
            <p>Métodos de Pago Físico: <strong id="contadorFisico"><?php echo $countFisico; ?></strong></p>
        </div>

        <!-- Filtros de Búsqueda -->
        <div class="filter-container">
            <input type="text" id="searchInput" class="filter-input" placeholder="Buscar Método de Pago..."
                onkeyup="filtrarTabla()">
            <select id="categoryFilter" onchange="filtrarTabla()">
                <option value="">Todas las categorías</option>
                <option value="DIGITAL">Digital</option>
                <option value="FISICO">Físico</option>
            </select>
        </div>


        <div class="table-container">
            <div class="transition-container" id="tableContent">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Categoría</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody class="table-transition" id="tablaMostrarMetodoPago">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr data-id='{$row['id_tipoOperacion']}' data-categoria='{$row['categoria_pago']}'>
                        <td>{$row['id_tipoOperacion']}</td>
                        <td>{$row['tipo']}</td>
                        <td>{$row['categoria_pago']}</td>
                        <td>
                            <button type='button' class='btn-edit' onclick=\"abrirModalEditar({$row['id_tipoOperacion']}, '{$row['tipo']}')\">Modificar</button>
                            <button type='button' class='btn-delete' onclick=\"abrirModalEliminar({$row['id_tipoOperacion']}, '{$row['tipo']}')\">Remover</button>
                        </td>
                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No hay métodos de pago registrados</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="pagination">
            <?php if ($paginaActual > 1): ?>
                <a href="javascript:void(0)" onclick="cambiarPagina(<?php echo $paginaActual - 1; ?>)">&laquo; Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <a href="javascript:void(0)" onclick="cambiarPagina(<?php echo $i; ?>)"
                    class="<?php echo ($i == $paginaActual) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($paginaActual < $totalPaginas): ?>
                <a href="javascript:void(0)" onclick="cambiarPagina(<?php echo $paginaActual + 1; ?>)">Siguiente &raquo;</a>
            <?php endif; ?>
        </div>


    </div>

    <!-- Modals -->

    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal_editarMp()">&times;</span>
            <h2>Modificar Método de Pago</h2>
            <form action="./componentes/administrador/admMetodoPago.php" method="post">
                <input type="hidden" name="id_tipoOperacion" id="id_tipoOperacion_modal">
                <input type="text" name="tipo" id="tipo_modal" placeholder="Tipo de operación" required>
                <div class="radio-group">
                    <div class="radio1">
                        <label for="digital">Digital</label>
                        <input type="radio" id="digital" name="categoriaPagoEditar" value="DIGITAL" required>
                    </div>

                    <div class="radio2">
                        <label for="fisico">Físico</label>
                        <input type="radio" id="fisico" name="categoriaPagoEditar" value="FISICO" required>
                    </div>
                </div>
                <button type="submit" name="accion" value="editar_tipo" class="btn-submit">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <div id="modalEliminar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalEliminar()">×</span>
            <h2>Eliminar Método de Pago</h2>
            <p>¿Estás seguro de que deseas eliminar el método de pago: <strong id="tipo_eliminar"></strong>?</p>
            <form action="./componentes/administrador/admMetodoPago.php" method="post">
                <input type="hidden" name="id_tipoOperacion" id="id_tipoOperacion_eliminar">
                <div class="buttonsModals">
                    
                <button type="button" onclick="cerrarModalEliminar()" class="btn-cancel">Cancelar</button>
                    <button type="button" onclick="eliminarMetodoPago()" class="btn-delete">Eliminar</button>
                   
                </div>

            </form>
        </div>
    </div>

    <div id="modal_MetodoPago" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal_mp()">×</span>
            <h2>Registrar Método de Pago</h2>
            <form id="agregarNuevoMetodoPago">
                <label for="metodoPagoInput">Agregar Nuevo Método de Pago</label>
                <input type="text" id="metodoPagoInput" name="metodoPago" placeholder="Método de Pago" required>

                <label>Categoría del Pago</label>
                <div class="radio-group">
                    <div class="radio1">

                        <label for="digital">Digital</label>
                        <input type="radio" id="digital" name="categoriaPago" value="DIGITAL" required>
                    </div>

                    <div class="radio2">

                        <label for="fisico">Físico</label>
                        <input type="radio" id="fisico" name="categoriaPago" value="FISICO" required>
                    </div>

                </div>

                <button type="button" onclick="aggNewMetodoPago()">Agregar</button>
            </form>
            <div id="mensajeRespuestaMetodoPago"></div>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>