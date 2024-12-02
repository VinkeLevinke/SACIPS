<?php
// Nombres de los meses en español
$nombresMeses = [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre'
];

include("../../../componentes/conexiones/conexionbd.php");

// Inicializar variables
$anio = date('Y'); // Año actual por defecto

// Consulta por defecto, sin filtros iniciales
$sql = "SELECT *, b.nombre_banco AS banco, tip.tipo AS metodo_pago, p.id_Personas AS id, af.fechaAporte AS fecha 
        FROM aportes_afiliados af 
        JOIN banco b ON af.banco = b.id_banco
        JOIN tipo_operacion tip ON af.tipo_operacion = tip.id_tipoOperacion
        JOIN personas p ON af.id_persona = p.id_Personas";

$result = $con->query($sql);

$queryInstitucion = "SELECT * FROM ipspuptyab_info";
$resultadoInstitucion = $con ->query($queryInstitucion);

// Recoger los datos en un array para poder usarlo en JavaScript
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}


$datos_institucion = [];
while ($institucion = $resultadoInstitucion->fetch_assoc()) {
    $datos_institucion[] = $institucion;
}
 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../style/reportes.css">
    <link rel="stylesheet" href="../../../style/modales.css">
    <title>Reportes por afiliado</title>
</head>

<body>

    <div class="filtros">
        <h3>Filtrar fechas</h3>
        <label for="mes_inicio">Mes Inicio:</label>
        <select id="mes_inicio" required>
            <option value="" disabled selected> Ingrese el mes Inicial</option>
            <?php foreach ($nombresMeses as $m => $nombre) { ?>
            <option value="<?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>">
                <?php echo $nombre; ?>
            </option>
            <?php } ?>
        </select>

        <label for="mes_fin">Mes Fin:</label>
        <select id="mes_fin" required>
            <option value="" disabled selected> Ingrese el mes final</option>
            <?php foreach ($nombresMeses as $m => $nombre) { ?>
            <option value="<?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>">
                <?php echo $nombre; ?>
            </option>
            <?php } ?>
        </select>

        <label for="anio">Año:</label>
        <input type="number" id="anio" value="<?php echo $anio; ?>" min="2000" max="<?php echo date('Y'); ?>" required>

        <button id="filterButton">Filtrar</button>
    </div>



    <?php /* LO QUE APARECERÁ EN EL PDF AL GENERARSE*/ ?>
    <section id="content">
    <div class="headerDocumento">
        <div class="cintillo">
            <img src="../../../img/IPSPUPTYAB-LOGO.png" alt="Logo Izquierda" class="cintilloImg">
        </div>
        <div class="tituloInstucion">
            <?php foreach ($datos_institucion as $institucion) { ?>
            <p><?php echo $institucion['razon_social']; ?></p>
            <?php } ?>
        </div>
        <div class="logo_ipsp">
            <img src="../../../img/IPSPUPTYAB-LOGO.png" alt="Logo Derecha" class="cintilloImg">
        </div>
    </div>

            <div class="rif">
                <p><?php echo $institucion['rif_institucion'];?></p>
            </div>
    <div class="bodyReporte">
        <div class="tablaReportes">
            <div class="meses">
                <p id="selectedMeses"></p>
                <p id="resultCount"></p>
            </div>
            <table id="resultTable">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Emisor</th>
                        <th>Cédula</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                        <th>Metodo de Pago</th>
                        <th>Banco</th>
                        <th>Concepto</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($data as $row) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nombre'] . ' ' . $row['apellido']; ?></td>
                        <td><?php echo $row['cedula']; ?></td>
                        <td>Bs <?php echo $row['monto']; ?></td>
                        <td><?php echo (new DateTime($row['fechaAporte']))->format('d/m/Y h:i A'); ?></td>
                        <td><?php echo $row['metodo_pago']; ?></td>
                        <td><?php echo $row['banco']; ?></td>
                        <td><?php echo $row['concepto']; ?></td>
                        <td><?php echo $row['estado']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

    <?php /* LO QUE APARECERÁ EN EL PDF AL GENERARSE FIN*/ ?>

    <?php /* Modales */ ?>
    <div id="mensajesAlerta" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarAlerta_general()">X</span>
            <h2>Advertencia!</h2>
            <div class="mensajeAlerta"></div> <!-- Manten este div -->
        </div>
    </div>
    <?php /* FIN modales */ ?>

    <button class="btn_inicio" onclick="redirigirAlAflliados()">Volver al inicio</button>
    <button class="pdfButton" onclick="generatePDF()">Generar PDF</button>
    <!-- Enlaces a las librerías jsPDF y html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
    // Datos capturados desde PHP
    const data = <?php echo json_encode($data); ?>;

    document.getElementById('filterButton').addEventListener('click', function() {
        const mesInicio = parseInt(document.getElementById('mes_inicio').value);
        const mesFin = parseInt(document.getElementById('mes_fin').value);
        const anio = document.getElementById('anio').value;
        let alertMessages = [];

        // Limpiar tabla actual
        const tableBody = document.getElementById('tableBody');
        tableBody.innerHTML = '';

        // Validaciones
        if (!mesInicio || !mesFin) {
            alertMessages.push("Por favor, seleccione ambos meses.");
        } else if (mesFin < mesInicio) {
            alertMessages.push("El mes de fin no puede ser anterior al mes de inicio.");
        }

        if (alertMessages.length > 0) {
            document.querySelector('.mensajeAlerta').innerHTML = alertMessages.join('<br>');
            abrirAlerta_general();
            return; // Salir de la función si hay errores
        }

        // Filtrar datos
        const filteredData = data.filter(row => {
            const fecha = new Date(row.fechaAporte);
            const mes = fecha.getMonth() + 1; // getMonth() devuelve 0-11

            return fecha.getFullYear() == anio && mes >= mesInicio && mes <= mesFin;
        });

        // Verificar si hay datos filtrados
        if (filteredData.length === 0) {
            document.querySelector('.mensajeAlerta').innerHTML =
                "No se han encontrado resultados en los meses seleccionados.";
            document.getElementById('resultCount').innerText = "Total de resultados: 0"; // Actualizar contador
            abrirAlerta_general();
            return; // Salir si no hay registros después de filtrar
        }

        // Mostrar datos filtrados
        filteredData.forEach(row => {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                    <td>${row.id}</td>
                    <td>${row.nombre} ${row.apellido}</td>
                    <td>${row.cedula}</td>
                    <td>${row.monto}</td>
                    <td>${new Date(row.fechaAporte).toLocaleString('es-ES')}</td>
                    <td>${row.metodo_pago}</td>
                    <td>${row.banco}</td>
                    <td>${row.concepto}</td>
                    <td>${row.estado}</td>
                `;
            tableBody.appendChild(newRow);
        });

        // Mostrar el rango de meses seleccionados
        const mesInicioNombre = <?php echo json_encode($nombresMeses); ?>[mesInicio];
        const mesFinNombre = <?php echo json_encode($nombresMeses); ?>[mesFin];
        document.getElementById('selectedMeses').innerText =
            `Desde: ${mesInicioNombre} Hasta: ${mesFinNombre} del año ${anio}`;

        // Actualizar el contador de resultados
        document.getElementById('resultCount').innerText = `Total de resultados: ${filteredData.length}`;
    });

    function redirigirAlAflliados() {
        // Redirigir a aflliados.php y recargar la página
        window.location.href = '../../afiliados.php';
    }

    function generatePDF() {
    // Verificar si hay filas en la tabla
    const tableBody = document.getElementById('tableBody');
    if (tableBody.rows.length === 0) {
        document.querySelector('.mensajeAlerta').innerHTML =
            "Por favor, seleccione las fechas correctamente antes de descargar.";
        abrirAlerta_general();
        return; // Salir si no hay filas
    }

    html2canvas(document.getElementById('content'), {
        scale: 2
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png', 1.0);
        const { jsPDF } = window.jspdf;

        // Crear un documento PDF con tamaño A4
        const doc = new jsPDF('p', 'mm', 'a4');

        // Conservar proporciones y escalar para que se ajuste a A4
        const pdfWidth = 210; // Ancho A4 en mm
        const pdfHeight = 297; // Alto A4 en mm
        const imgWidth = canvas.width;
        const imgHeight = canvas.height;

        const pdfImgWidth = pdfWidth;
        const pdfImgHeight = (imgHeight * pdfImgWidth) / imgWidth;

        // Centrar la imagen en el PDF
        const xOffset = (pdfWidth - pdfImgWidth) / 2;
        const yOffset = (pdfHeight - pdfImgHeight) / 2;

        // Agregar imagen al PDF
        doc.addImage(imgData, 'PNG', xOffset, yOffset, pdfImgWidth, pdfImgHeight);

        // Guardar el PDF
        doc.save('recibo.pdf');
    });
}


    // Modal
    function cerrarAlerta_general() {
        let modal = document.getElementById('mensajesAlerta');
        modal.classList.remove('show'); // Remover clase para el efecto visual
        setTimeout(function() {
            modal.style.display = 'none'; // Ocultar modal después del efecto
        }, 300); // Duración del efecto al cerrar
    }

    function abrirAlerta_general() {
        let modal = document.getElementById('mensajesAlerta');

        modal.style.display = 'block'; // Mostrar modal
        setTimeout(function() {
            modal.classList.add('show'); // Agregar clase para el efecto visual
        }, 10); // Duración del efecto al cerrar
    }
    </script>
</body>

</html>