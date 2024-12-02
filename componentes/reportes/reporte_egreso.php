<?php
    $id=$_COOKIE['reporte_id'];
    $tipo=4;
    include "../../componentes/conexiones/conexionbd.php";
    
    $consul="SELECT * FROM registrar_egreso WHERE id = $id";
    $sql=mysqli_query($con,$consul);
    $Consulta=mysqli_fetch_assoc($sql);

?>
<!-- Cuerpo del reporte y lo que se vera en el archivo PDF -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte|Egreso</title>
    <style>
        * {
            font-family: arial;
            margin: 10px;
            padding: 0px;
        }

        #content {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-direction: column;
            width: 595px;
            /*Ojo esto es importante para que no se deforme!*/
            margin: auto;
            height: 842px;
            overflow: hidden;
            background-color: white;
        }

        #content div {
            width: 500px;
            background-color: #dbdbdb;
            border-radius: 10px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-direction: column;
        }
        #content .cintillo{
            height: 115px;
            width: auto;
            margin: 0px;
        }

        hr{
            width: 90%;
        }
        body{
            background-color: #dbdbdb;
        }

        @font-face {
            font-family: Exo Thin;
            src: url(../../fonts/Exo-VariableFont_wght.ttf);
        }
        @page {
            size: 8.5in 11in;
            margin: 1in;
        }

        body{
        font-size: 12pt;
        }

        *{
            font-family: "Exo Thin", sans-serif;
            text-align: center;
            letter-spacing: 1.2px;
        }
        .name{
            font-size: 12pt;
            font-weight: bold;
            margin: 0%;
        }
        .text{
            font-size: 9pt;
            font-weight: 600;
        }

        h3{
            font-size: 12px;
        }

        p{
            margin-bottom: 0px;
            padding-bottom: 0px;
        }

        .subrayado{
            color: white;
            border-top: 4px solid  #636fce;
            border-top-left-radius: 12%;
            border-top-right-radius: 12%;
            font-size: 12pt;
        }

        .ESPACIO{
            margin-top: 20%;
        }

        .esquina1{
            border-top:1px #757575 solid;
            border-left: 1px #757575 solid;
            border-top-left-radius:5px;
        }

        .esquina2{
            border-top:1px #757575 solid;
            border-right: 1px #757575 solid;
            border-top-right-radius:5px;
        }

        .esquina3{
            border-bottom:1px #757575 solid;
            border-left: 1px #757575 solid;
            border-bottom-left-radius:5px;
        }

        .esquina4{
            border-bottom:1px #757575 solid;
            border-right: 1px #757575 solid;
            border-bottom-right-radius:5px;
        }
        .izqui{
            border-left: 1px #757575 solid;
        }
        .dere{
            border-right: 1px #757575 solid;
        }

        .bottom{
            border-bottom: 1px #757575 solid;
        }

        .red{
            color: red;
        }

    </style>
</head>
<body> 
    <div id="content">
        <img class="cintillo" src="../../img/cintillo_IPSP2.jpg" alt="Cintillo Institucional">
        <table>
        <thead class="borde">
            <tr >
                <th colspan="2"><p >REPORTE DE EGRESO</p></th>
                <th><p class="text red">N°</p></th>
            </tr>
            <tr>
                <th colspan="3" class="subrayado">h</th>
            </tr>
        </thead>
        <tbody class="content">
            <tr>
                <td class="esquina1 bottom"><h2 class="text">CONCEPTO:</h2></td>
                <td colspan="2" class="text esquina2 bottom"><h3><?php echo $Consulta['rConcept'];?></h3></td>
            </tr>
            <tr>
                <td class="izqui bottom"><h2 class="text ">DESTINATARIO:</h2></td>
                <td colspan="2" class="text dere bottom"><h3> <?php echo $Consulta['beneficiario']; ?></h3></td>
            </tr>
            <tr>
                <td class="izqui bottom"><h2 class="text">FECHA DEL EGRESO:</h2></td>
                <td colspan="2" class="text dere bottom"><h3> <?php echo $Consulta['fechaPagoEgreso'] ?></h3></td>
            </tr>
            <tr>
                <td class="izqui bottom"><h2 class="text">MONTO:</h2></td>
                <td colspan="2" class="text dere bottom"><h3> <?php echo $Consulta['monto'] ?></h3></td>
            </tr>
            <tr>
                <td class="esquina3">
                    <h2 class="text">TIPO DE APORTE: <br><?php echo $Consulta['tipoOperacion'] ?></h2>
                
                </td>
                <td class="bottom izqui dere">
                    <h2 class="text">REFERENCIA: <br><?php echo $Consulta['nro_cuenta'] ?></h2>
                
                </td>
                <td class="esquina4">
                    <h2 class="text">BANCO: <br><?php echo $Consulta['banco'] ?></h2>
                    
                </td>
            </tr>
            
            <tr>
                <td colspan="3"><h3 class="text ESPACIO">FIRMA AUTORIZADA <br> DIRECCION DE FINANZAS</h3></td>
            </tr>
        </tbody>
    </table>

    </div>
    <button onclick="generatePDF()">Generar PDF</button>

    <!--aqui tienes los links tanto de jsPDF como de html2canvas-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>


    <script>
        //-->funcion que vincula todo<--//
        function generatePDF() {
            html2canvas(document.getElementById('content'),{ scale: 2 }).then(canvas => {
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
                doc.save('Reporte.pdf');
            });
        }
    </script>
    
    
</body>
</html>