<?php
$id = $_COOKIE['Recibo_Dona'];
include "../../componentes/conexiones/conexionbd.php";
session_start();

// Primero obtenemos los detalles de la donación
$consul = "
    SELECT   
        'Donación' AS tipo,   
        id_AportesDona AS id,   
        benefactor,
        -- Usamos directamente el campo beneficiario de la tabla aportes_donaciones
        COALESCE(ap.beneficiario, CONCAT(p.nombre, ' ', p.apellido)) AS beneficiario,   
        t.tipo AS tipo_operacion,  
        b.nombre_banco AS banco,  -- Aquí obtenemos el nombre del banco
        montoRecibido AS monto,  
        concepto,   
        fechaAporte AS fecha,   
        referencia,   
        estado,   
        tipo_usuario AS usuario  
    FROM aportes_donaciones ap  
    LEFT JOIN personas p ON ap.id_persona = p.id_personas  
    LEFT JOIN tipo_operacion t ON ap.tipo_operacion = t.id_tipoOperacion   
    LEFT JOIN banco b ON ap.origen = b.id_banco  -- Relacionamos 'origen' con 'id_banco'
    WHERE ap.id_AportesDona = $id";


$sql = mysqli_query($con, $consul);
$Consulta = mysqli_fetch_assoc($sql);

// Consulta para obtener el RIF y la firma digital de la tabla ipspuptyab_info
$consultaInstitucion = "SELECT * FROM ipspuptyab_info LIMIT 1";
$resultadoInstitucion = mysqli_query($con, $consultaInstitucion);
$institucion = mysqli_fetch_assoc($resultadoInstitucion);

// Obtener la firma digital (en formato base64)
$firmaDigital = $institucion['firma_digital'];

ob_start();
?>
<!-- Cuerpo del Recibo y lo que se vera en el archivo PDF -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style/recibosPDF.css">
    <title>Recibos|Donacion</title>
</head>

<body>
    <div id="content">
        <img class="cintillo" src="../../img/cintillo.png" alt="Cintillo Institucional">
        <table>
            <thead class="borde">
                <tr>
                    <th colspan="2">
                        <p>RECIBO DE INGRESO</p>
                    </th>
                    <th>
                        <p class="text red"><?php echo "N°" . "" . $Consulta['id'] ?></p>
                    </th>
                </tr>
                <tr>
                    <th colspan="3" class="subrayado">h</th>
                </tr>
            </thead>
            <tbody class="content">
                <tr>
                    <td class="esquina1 bottom">
                        <h2 class="text">RECIBIMOS DE:</h2>
                    </td>
                    <td colspan="2" class="text esquina2 bottom">
                        <h3><?php echo $Consulta['benefactor']; ?></h3>
                    </td>
                </tr>
                <tr>
                    <td class="izqui bottom">
                        <h2 class="text ">LA SUMA DE:</h2>
                    </td>
                    <td colspan="2" class="text dere bottom">
                        <h3> <?php echo $Consulta['monto']; ?></h3>
                    </td>
                </tr>
                <tr>
                    <td class="izqui bottom">
                        <h2 class="text">POR CONCEPTO DE:</h2>
                    </td>
                    <td colspan="2" class="text dere bottom">
                        <h3> <?php echo $Consulta['concepto'] ?></h3>
                    </td>
                </tr>

                <tr>
                    <td class="esquina3">
                        <h2 class="text">TIPO DE APORTE: <br><?php echo 'Donación'; ?></h2>
                    </td>
                    <td class="bottom izqui dere">
                        <h2 class="text">Beneficiario: <br><?php echo $Consulta['beneficiario'] ?></h2>

                    </td>
                    <td class="esquina4">
                        <h2 class="text">BANCO: <br><?php echo $Consulta['banco'] ?></h2>
                    </td>
                </tr>
                <tr>
                    <td class="izqui bottom">
                        <h2 class="text">FECHA DEL APORTE:</h2>
                    </td>
                    <td colspan="2" class="text dere bottom">
                        <h3> <?php echo $Consulta['fecha']  ?></h3>
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <h3 class="text ESPACIO">FIRMA AUTORIZADA <br> DIRECCION DE FINANZAS</h3>
                        <p class="firma-digital">
                            <span>
                                <!-- Mostrar la firma digital -->
                                <img id="previsualizacionFirma" src="data:image/png;base64,<?php echo htmlspecialchars($firmaDigital, ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 30%; margin-top: 10px;" alt="Previsualización de firma">
                            </span>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
    <button class="btn_inicio" onclick="redirigirAlAdmin()">Volver al incio</button>
    <button class="pdfButton" onclick="generatePDF()">Generar PDF</button>

    <!--aqui tienes los links tanto de jsPDF como de html2canvas-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>


    <script>
        function redirigirAlAdmin() {
            // Redirigir a aflliados.php y recargar la página
            window.location.href = '../../Admin.php';
        }

        //-->funcion que vincula todo<--//
        function generatePDF() {
            html2canvas(document.getElementById('content'), {
                scale: 2
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png', 1.0);
                const {
                    jsPDF
                } = window.jspdf;

                // Crear un documento PDF con tamaño A4
                const doc = new jsPDF('p', 'mm', 'a4');

                // Obtener las dimensiones del A4 en mm
                const pdfWidth = 210; // Ancho A4 en mm
                const pdfHeight = 297; // Alto A4 en mm

                // Obtener dimensiones del canvas en px
                const imgWidth = canvas.width;
                const imgHeight = canvas.height;

                // Ajustar imagen para que se ajuste al tamaño del A4
                const pdfImgWidth = pdfWidth;
                const pdfImgHeight = (imgHeight * pdfImgWidth) / imgWidth;

                // Centrar la imagen en el PDF
                const xOffset = (pdfWidth - pdfImgWidth) / 2;
                const yOffset = (pdfHeight - pdfImgHeight) / 2;

                // Agregar imagen al PDF
                doc.addImage(imgData, 'PNG', xOffset, yOffset, pdfImgWidth, pdfImgHeight);
                doc.save('Recibo.pdf');
            });
        }
    </script>


</body>

</html>