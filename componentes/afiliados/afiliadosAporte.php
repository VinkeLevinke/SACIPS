    <?php
    include "../../componentes/conexiones/conexionbd.php";
    session_start();

    $id_persona = $_SESSION['id_persona'];
$totalRows = mysqli_num_rows(mysqli_query($con, "SELECT * FROM aportes_afiliados WHERE id_persona = $id_persona"));

$limit = 4; // Cantidad de filas por página
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$offset = max(0, $offset); // Asegurar que el offset no sea negativo



$query = "SELECT * FROM aportes_afiliados WHERE id_persona = $id_persona ORDER BY fechaAporte DESC LIMIT $offset, $limit";
$resultmovs = mysqli_query($con, $query);


function convertDateTimeToAMPM($dateTime)
{
  $timestamp = strtotime($dateTime); // Convertimos la fecha a timestamp
  return date('d/m/Y h:i A', $timestamp); // Formateamos la fecha a AM/PM
}



    // consulta a la base de datos para bancos 
    $sql = "SELECT id_banco, nombre_banco FROM banco";
    $result = $con->query($sql);

    $sqlTo = "SELECT * FROM tipo_operacion WHERE categoria_pago = 'DIGITAL'";
    $resultTo = $con->query($sqlTo);
    $options = "";

    if ($resultTo->num_rows > 0) {
        while ($rowTo = $resultTo->fetch_assoc()) {
            $options .= '<option value="' . $rowTo['id_tipoOperacion'] . '" data-categoria-pago="' . $rowTo['categoria_pago'] . '">' . $rowTo['tipo'] . '</option>';
        }
    }


    
/* La lógica de deuda */


/* FIN DEUDA */


    ?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style/afiliadosAporte.css">
    </head>

    <body class="bodyAportes">



        <header>
            <?php include "../../componentes/template/aflHeader.php"; ?>
        </header>
        
        <div class="formularioShape">
            <div class="cuadroFormulario">
                <form id="formularioAfiliados" method="POST" enctype="multipart/form-data">
                    <p class="aTitulo">Enviar Aporte</p>
                    <input type="hidden" name="dato" value="insertar_archivo">
                    <input type="hidden" name="nombre" value="<?php echo $_SESSION['nombre'] ?>">
                    <input type="hidden" name="apellido" value="<?php echo $_SESSION['apellido'] ?>">
                    <input type="hidden" name="id_persona" value="<?php echo $_SESSION['id_persona']; ?>">
                    <input type="hidden" name="usuario" value="<?php echo $_SESSION['tipo_usuario']; ?>">

                    <div class="formContent">
                        <div class="formLeft">
                            <div class="banco">
                                <label for="banco">Banco</label>
                                <select id="banco" name="banco">
                                    <option value="" selected disabled>Seleccione un banco</option>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                        $idBanco = $row['id_banco'];
                                        $nombreBanco = $row['nombre_banco'];
                                        echo "<option value=\"$idBanco\">$idBanco - $nombreBanco</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="telefono">
                                <label for="telefono">Teléfono</label>
                                <input type="number" placeholder="Ingrese el número telefónico" name="telefono"
                                    required>
                            </div>
                            <div class="documento">
                                <label for="cedula">Cédula</label>
                                <input type="number" id="cedula" placeholder="Ingrese la Cédula" name="cedula"
                                    value="<?php echo $_SESSION['cedula']; ?>" readonly required>
                            </div>
                            <div class="numReferencia">
                                <label for="referencia">Referencia</label>
                                <input type="number" placeholder="Ingrese el número de su Referencia" name="referencia"
                                    required>
                            </div>
                            <div class="fecha">
                                <input hidden type="datetime-local" value="<?php echo date('Y-m-d\TH:i:s'); ?>"
                                    name="fecha_actual">
                            </div>
                            <div class="metodoPago">
                                <label for="tipoOperacion">Método de Pago</label>
                                <select id="tipoOperacion" name="tipoOperacion" required>
                                    <option value="" disabled selected>Operación</option>
                                    <?php echo $options; ?>
                                </select>
                            </div>
                            <div class="banco">
                                <label for="donacion">Tipo de Aporte</label>
                                <select name="tipo_aporte" id="tipo_aporte">
                                    <option disabled selected>Seleccione el Tipo de aporte</option>
                                    <option value="Estatuto">Estatuto</option>
                                    <option value="Donacion">Donación</option>
                                </select>
                            </div>
                            <div class="monto">
                                <label for="montoInput">Monto</label>
                                <input type="text" id="montoInput" oninput="formatearMonto()" placeholder="Bs 0,00"
                                    name="monto" required>

                            </div>
                            <div class="concepto">
                                <label for="concepto">Por concepto</label>
                                <textarea id="concepto" placeholder="Ingrese el concepto de la operación"
                                    name="concepto"></textarea>
                            </div>
                        </div>
                        <div class="formRight">

                            <div class="subirReferencia">
                                <label id="img" class="subirImagen" for="imagen">
                                    <img src="img/capture-white.svg" alt="" class="imglabel">
                                    <p>Capture</p>
                                </label>
                            </div>

                            <input type="file" name="capture[]" id="imagen" multiple required hidden>


                            <div class="mostrarimg">
                                <img id="imagenPrevia" src="#" alt="Previsualización de la imagen"
                                    style="display: none;">
                            </div>

                        </div>
                    </div>

                    <div class="btnEnviar">
                        <input id="aSubmit" type="submit" name="enviar" value="Confirmar"
                            onclick="submitFormAfiliado(); return false;">
                    </div>
                </form>
            </div>
        </div>
        <section>
            <?php include "../../componentes/template/mostrarCuentas.php"; ?>
        </section>

    </body>

    </html>