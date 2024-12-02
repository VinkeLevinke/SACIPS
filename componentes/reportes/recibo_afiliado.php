<?php
// Obtener datos del RIF de la institución y la firma digital
include "../../componentes/conexiones/conexionbd.php";

// Consulta para obtener el RIF y la firma digital de la tabla ipspuptyab_info
$consultaInstitucion = "SELECT * FROM ipspuptyab_info LIMIT 1";
$resultadoInstitucion = mysqli_query($con, $consultaInstitucion);
$institucion = mysqli_fetch_assoc($resultadoInstitucion);

// Consulta para obtener los datos del afiliado (como en tu código original)
$id = $_COOKIE['Recibo_id'];
$consul = "SELECT * FROM aportes_afiliados WHERE id_aporte = $id";
$sql = mysqli_query($con, $consul);
$Consulta = mysqli_fetch_assoc($sql);
$idpersona = $Consulta['id_persona'];
$datos = "SELECT * FROM personas WHERE id_Personas = $idpersona";
$datossql = mysqli_query($con, $datos);
$datosPer = mysqli_fetch_assoc($datossql);

// Obtener la firma digital (en formato base64)
$firmaDigital = $institucion['firma_digital'];
?>
<!-- Cuerpo del Recibo y lo que se vera en el archivo PDF -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style/recibosPDF.css">
    <title>Recibo|Afiliados</title>
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
                        <p class="text red"><?php echo "N°" . "" . $datosPer['id_Personas']; ?></p>
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
                        <h3><?php echo $datosPer['nombre'] . " " . $datosPer['apellido'] ?></h3>
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
                    <td class="izqui bottom">
                        <h2 class="text">TIPO DE APORTE: <br><?php echo $Consulta['tipo_aporte'] ?></h2>

                    </td>
                    <td class="bottom izqui dere">
                        <h2 class="text">REFERENCIA: <br><?php echo $Consulta['referencia'] ?></h2>

                    </td>
                    <td class="dere bottom">
                        <h2 class="text">BANCO: <br><?php echo $Consulta['banco'] ?></h2>

                    </td>
                </tr>
                <tr>
                    <td class="esquina3">
                        <h2 class="text">FECHA DEL APORTE: </h2>
                    </td>
                    <td class="esquina4" colspan="2"><?php echo $Consulta['fechaAporte'] ?></td>
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
    <button class="btn_inicio" onclick="redirigirAlAflliados()">Volver al incio</button>
    <button class="pdfButton" onclick="generatePDF()">Generar PDF</button>

    <!-- Enlaces a las librerías jsPDF y html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        function redirigirAlAflliados() {
            // Redirigir a aflliados.php y recargar la página
            window.location.href = '../../afiliados.php';
        }
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

                // Guardar el PDF
                doc.save('recibo.pdf');
            });
        }
    </script>
</body>

</html>