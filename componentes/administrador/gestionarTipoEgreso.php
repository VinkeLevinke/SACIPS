<?php
// Conexión a la base de datos
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
$sqlTotal = "SELECT COUNT(*) as total FROM tipo_egreso";
$resultTotal = $conn->query($sqlTotal);
$totalFilas = $resultTotal->fetch_assoc()['total'];
$totalPaginas = ceil($totalFilas / $filasPorPagina);

// Consultamos las filas de la página actual
$sql = "SELECT * FROM tipo_egreso LIMIT $offset, $filasPorPagina";
$result = $conn->query($sql);

// Contar tipos de egreso
$sqlCountTipoEgreso = "SELECT COUNT(*) as total FROM tipo_egreso";
$resultCount = $conn->query($sqlCountTipoEgreso);
$countTipoEgreso = $resultCount->fetch_assoc()['total'];

// Manejo de formularios
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_TipoEgreso = $_POST["id_TipoEgreso"];
    $tipo = $_POST["tipo"];
    $codigo = $_POST["codeEgreso"];
    $accion = $_POST["accion"];

    if ($accion == "agregar_tipo") {
        $sql = "INSERT INTO tipo_egreso (tipo) VALUES ('$tipo')";
    } elseif ($accion == "editar_tipo") {
        $sql = "UPDATE tipo_egreso SET codigo_egreso = '$codigo', tipo = '$tipo' WHERE id_TipoEgreso = $id_TipoEgreso";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<script>window.location.href='../../admin.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestor de Tipos de Egreso</title>
    <link rel="stylesheet" href="./style/modales.css">
    <link rel="stylesheet" href="./style/style.css"> <!-- El CSS general, como en metodoPago -->
    <script src="script_tipo_egreso.js"></script>
    
    <style>
         * {
            box-sizing: border-box;
        }

        .bodyEgreso {

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

<body class="bodyEgreso">
    <div class="container">
        <header class="header">
            <h1>Tipos de Egreso</h1>
            <button type="button" onclick="abrirAddEgreso()" class="btn-add">
                <img src="./img/aggBank.svg" alt="Agregar" class="img-add">
                <span>Registrar Egreso</span>
            </button>
        </header>

        <!-- Contador de tipos de egreso -->
        <div class="contadorCategorias">
            <p>Total de Tipos de Egreso: <strong id="contadorEgreso"><?php echo $countTipoEgreso; ?></strong></p>
        </div>

        <!-- Filtros de Búsqueda -->
        <div class="filter-container">
            <input type="text" id="searchInput" class="filter-input" placeholder="Buscar Tipo de Egreso..."
                onkeyup="filtrarTablaEgreso()">
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código de Egreso</th>
                        <th>Tipo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tablaMostrar">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr data-id='{$row['id_TipoEgreso']}'>
                                <td>{$row['id_TipoEgreso']}</td>
                                <td>{$row['codigo_egreso']}</td>
                                <td>{$row['tipo']}</td>
                                <td>
                                    <button type='button' onclick=\"abrirModalEgreso({$row['id_TipoEgreso']}, {$row['codigo_egreso']}, '{$row['tipo']}')\">Modificar</button>
                                    <button type='button' onclick=\"abrirModalEliminarEgreso({$row['id_TipoEgreso']}, '{$row['tipo']}')\" class='btn-delete'>Remover</button>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No hay tipos de egreso registrados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="pagination">
            <?php if ($paginaActual > 1): ?>
                <a href="javascript:void(0)" onclick="cambiarPaginEgreso(<?php echo $paginaActual - 1; ?>)">&laquo; Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <a href="javascript:void(0)" onclick="cambiarPaginaEgreso(<?php echo $i; ?>)"
                    class="<?php echo ($i == $paginaActual) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($paginaActual < $totalPaginas): ?>
                <a href="javascript:void(0)" onclick="cambiarPaginaEgreso(<?php echo $paginaActual + 1; ?>)">Siguiente &raquo;</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modales -->
    <!-- Modal Editar -->
    <div id="modalEgreso" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalEgreso()">&times;</span>
            <h2>Modificar Tipo de Egreso</h2>
            <form action="./componentes/administrador/gestionarTipoEgreso.php" method="post">
                <input type="hidden" name="id_TipoEgreso" id="id_TipoEgreso_modal">
                <input class="codigoEgreso" type="number" id="codeEgresoInput_modal" name="codeEgreso" placeholder="Código">
                <input type="text" name="tipo" id="tipo_modal" placeholder="Tipo de egreso" required>
                <button type="submit" name="accion" value="editar_tipo">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div id="modalEliminarEgreso" class="modalEliminar">
        <div class="modalEliminar-content modal-content">
            <span class="close" onclick="cerrarModalEliminarEgreso()">×</span>
            <h2>Eliminar Tipo de Egreso</h2>
            <p>¿Estás seguro de que deseas eliminar el tipo de egreso: <strong id="tipo_eliminar"></strong>?</p>
            <form id="formEliminarEgreso">
                <div class="buttonsModals">
                    <input type="hidden" name="id_TipoEgreso" id="id_TipoEgreso_eliminar">
                    <button type="button" onclick="cerrarModalEliminarEgreso()">Cancelar</button>
                    <button type="submit" onclick="eliminarEgreso()" class="btn-delete">Eliminar</button>
                </div>
            </form>
            <div id="mensajeRespuesta"></div>
        </div>
    </div>

    <div id="modalMensajeEgreso" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalMensajeEgreso()">X</span>
            <h2>Egreso Eliminado</h2>
            <div id="mensajeSecundario"></div>
        </div>
    </div>

    <div id="modalMensajeEgreso_agg" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarMoadlMensajeEgreso_agg()">X</span>
            <h2>Egreso Agregado</h2>
            <div id="egresoAgregado"></div>
        </div>
    </div>

    <div id="modalEgresoAgg" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalAddEgreso()">×</span>
            <h2>Registrar Tipo de Egreso</h2>
            <div id="mensajeRespuesta"></div>
            <form id="agregarNuevoEgreso">
                <label for="codeEgresoInput">Tipo de egreso</label>
                <input class="codigoEgreso" type="number" id="codeEgresoInput" name="codeEgreso" placeholder="Código">

                <input type="text" id="tipoEgresoInput" name="tipoEgreso" placeholder="Nombre del egreso">
                <div class="brFormBTN">
                <button type="button" onclick="aggNewEgreso()">Registrar</button>
                            </div>
               
            </form>
        
        </div>
    </div>
    
    <script>
       
   
    </script>
</body>

</html>

<?php
$conn->close();
?>
