<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/estiloHistorialusuarios.css">
    <title>Historial de Aportes</title>

</head>

<body class="body">
    <div class='historialAporte'>
        <div class='fechaSelector'>
            <form method="POST" action="">
                <label for="start_date">Fecha de inicio:</label>
                <input type="date" id="start_date" name="start_date" required>
                <label for="end_date">Fecha de fin:</label>
                <input type="date" id="end_date" name="end_date" required>
                <button type="submit">Filtrar</button>
            </form>
        </div>

        <?php
        // Conexión a la base de datos
        $conexion = new mysqli("localhost", "root", "", "sacips_bd");

        // Verificar la conexión
        if ($conexion->connect_error) {
            die("Conexión fallida: " . $conexion->connect_error);
        }

        // Leer el id_persona desde una cookie
        $id_persona = 0;
        if (isset($_COOKIE['IdUserSelect'])) {
            $id_persona = $_COOKIE['IdUserSelect'];
        }

        $datosPersona = [];
        $montoTotal = 0.0;
        $montoPendiente = 0.0;
        $montoDeclinado = 0.0;
        $montoAprobado = 0.0;
        $mensualidad = 0;
        $MesIngreso = '';
        $saldoMensual = 2;
        $saldo = 0;
        $mensualidadPagada = 0;
        $MesActual = date("Y-m-d");
        $Dolar = 0;
        $Bolivares = 0;

        $dolarSql = "SELECT precio FROM dolar_diario";
        $precioDolar = $conexion -> query($dolarSql);

        while($dolar = $precioDolar -> fetch_assoc()){
            $Dolar = number_format($dolar['precio'],2);
        }

    
        // Consultar el tipo de usuario en la tabla usuarios
        $sql_usuarios = "SELECT tipo_usuario FROM usuarios WHERE id_persona = $id_persona";
        $result_usuarios = $conexion->query($sql_usuarios);
        if ($result_usuarios === false) {
            die("Error en la consulta SQL para usuarios: " . $conexion->error);
        }

        if ($result_usuarios->num_rows > 0) {
            $tipo_usuario = $result_usuarios->fetch_assoc()['tipo_usuario'];

            // Verifica si se han enviado las fechas
            $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
            $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
            $date_filter = "";

            if ($start_date && $end_date) {
                $date_filter = " AND fechaAporte BETWEEN '$start_date' AND '$end_date'";
            }

            // Si el tipo de usuario es 1 (afiliado), consultar aportes_afiliados
            if ($tipo_usuario == 1) {
                $sql_afiliados = "SELECT * FROM aportes_afiliados WHERE id_persona = $id_persona" . $date_filter;
                $result_afiliados = $conexion->query($sql_afiliados);

                if ($result_afiliados === false) {
                    die("Error en la consulta SQL para aportes_afiliados: " . $conexion->error);
                }
                echo "<table border='1'>";
                echo "<tr>
                    <th>Usuario</th>
                    <th>Tipo Aporte</th>
                    <th>Fecha Aporte</th>
                    <th>Nombre Completo</th>
                    <th>Monto</th>
                    <th>Concepto</th>
                    <th>Estado</th>
                  </tr>";

                while ($row = $result_afiliados->fetch_assoc()) {
                    if ($row['tipo_aporte'] == 'Estatuto') {
                        if ($row['estado'] == 'Aprobado') {
                            $mensualidad += $row['usd_ref'];
                        }
                    }
                    $monto = str_replace(',', '.', str_replace('.', '', $row['monto']));
                    $monto = floatval(preg_replace('/[^0-9.]/', '', $monto));
                    $montoTotal += $monto;
                    if ($row['estado'] == 'Pendiente') {
                        $montoPendiente += $monto;
                    } elseif ($row['estado'] == 'Declinado') {
                        $montoDeclinado += $monto;
                    } elseif ($row['estado'] == 'Aprobado') {
                        $montoAprobado += $monto;
                    }
                    echo "<tr>
                        <td>{$row['usuario']}</td>
                        <td>{$row['tipo_aporte']}</td>
                        <td>{$row['fechaAporte']}</td>
                        <td>{$row['nombre']} {$row['apellido']}</td>
                        <td>{$row['monto']}</td>
                        <td>{$row['concepto']}</td>
                        <td>{$row['estado']}</td>
                      </tr>";
                }

                echo "</table>";
                echo "<div>";

                $sql_Fecha_ingrso = "SELECT * FROM usuarios WHERE id_persona = $id_persona";
                $result_DatosUsuarios = $conexion->query($sql_Fecha_ingrso);

                if ($result_DatosUsuarios === false) {
                    die("Error en la consulta SQL para usuarios: " . $conexion->error);
                }

                while ($row = $result_DatosUsuarios->fetch_assoc()) {
                    $MesIngreso = $row['fechaIngreso'];
                    $saldo = $row['saldo'];
                    $Bolivares = $row['saldo'];
                    $mensualidadPagada = $row['mensualidad'];
                }
                
                // Datos del usuario
                $datosUsuarios = array(
                    "FechaIngreso" => $MesIngreso,
                    "saldo" => $saldo / $Dolar,  // Convierte los Bs a $ //saldo en dolares
                    "mensualidad" => $saldoMensual,
                    "mesualidad_pagada" => $mensualidadPagada  // Mes del último pago completo (ejemplo: septiembre)
                );

                // Función para verificar y actualizar el pago de la mensualidad
                function verificar_y_actualizar_pago_mensualidad(&$datosUsuarios, $mes_actual)
                {
                    $fecha_actual = new DateTime();
                    $mes_ultimo_pago = $datosUsuarios['mesualidad_pagada'];
                    $saldo = $datosUsuarios['saldo'];
                    $mensualidad = $datosUsuarios['mensualidad'];
                    $mensaje = "";

                    // Verificar si es un nuevo mes desde el último pago
                    if ($mes_actual > $mes_ultimo_pago) {
                        $meses_de_diferencia = $mes_actual - $mes_ultimo_pago;

                        // Calcular la deuda acumulada
                        $deuda = $meses_de_diferencia * $mensualidad;

                        // Verificar si el saldo es suficiente para cubrir la deuda
                        if ($saldo >= $deuda) {
                            // Descontar la deuda del saldo
                            $datosUsuarios['saldo'] -= $deuda;
                            // Marcar el mes actual como pagado
                            $datosUsuarios['mesualidad_pagada'] = $mes_actual;
                            $mensaje = "Mensualidad de este mes y meses anteriores pagadas.";

                            //Actualizar la base de datos con el nuvo saldo y mes pagado
                            $SALDO = round($datosUsuarios['saldo'],2);
                            $MES_PAGO = $datosUsuarios['mesualidad_pagada'];
                            $PERSONA = $_COOKIE['IdUserSelect'];
                            $conex = new mysqli("localhost", "root", "", "sacips_bd");
                            $ActualizarMensualidad = "UPDATE usuarios SET saldo=$SALDO, mensualidad=$MES_PAGO WHERE id_persona= $PERSONA";
                            if ($conex->query($ActualizarMensualidad) === TRUE) {
                                echo "<br>Datos actualizados exitosamente";
                            } else {
                                echo "Error: " . $ActualizarMensualidad . "<br>" . $conex->error;
                            }
                        } else {

                            $meses_a_pagar = 0;
                            //ESTO SOLO EN CASO DE QUE SEA POR EJEMPLO ENERO MES 1 Y EL ULTIMO PAGO FUE EN NOVIEMBRE MES 11
                            //Se le suman 12 asi que serian (13-11) = 2 Es decir dos meses de diferencia
                            if($mes_actual < $mes_ultimo_pago){ 
                                for ($i = 1; $i <= (($mes_actual+12) - $mes_ultimo_pago); $i++) {
                                    if ($saldo >= ($mensualidad * $i)) {
                                        $meses_a_pagar += 1;
                                    }
                                }
                            }else{
                                for ($i = 1; $i <= ($mes_actual - $mes_ultimo_pago); $i++) {
                                    if ($saldo >= ($mensualidad * $i)) {
                                        $meses_a_pagar += 1;
                                    }
                                }
                            }
                            if ($meses_a_pagar >= 1) {
                                //Actualizar la base de datos con el nuvo saldo y mes pagado
                                $SALDO = round($saldo - ($mensualidad * $meses_a_pagar),2);
                                $MES_PAGO = $mes_ultimo_pago + $meses_a_pagar;
                                $PERSONA = $_COOKIE['IdUserSelect'];
                                $conex = new mysqli("localhost", "root", "", "sacips_bd");
                                $ActualizarMensualidad = "UPDATE usuarios SET saldo=$SALDO, mensualidad=$MES_PAGO WHERE id_persona= $PERSONA";
                                if ($conex->query($ActualizarMensualidad) === TRUE) {
                                    echo "<br>Datos actualizados exitosamente";
                                } else {
                                    echo "Error: " . $ActualizarMensualidad . "<br>" . $conex->error;
                                }
                            }

                            // Pagar con el saldo disponible y actualizar la deuda restante
                            $saldo -= $deuda;
                            if ($saldo < 0) {
                                $datosUsuarios['saldo'] = $saldo; // Saldo negativo indica deuda restante
                            } else {
                                $datosUsuarios['saldo'] = 0;
                            }
                            // Mantener el mes del último pago hasta que la deuda sea cubierta
                            $datosUsuarios['mesualidad_pagada'] = $mes_ultimo_pago;
                            $mensaje = "Saldo insuficiente para cubrir todas las mensualidades.<br>Se ha actualizado el saldo y la deuda restante.";
                        }
                    } else {
                        $mensaje = "Mensualidad de este mes ya está pagada.";
                    }
                    return $mensaje;
                }

                // Obtener el mes actual
                $MesActual = date('Y-m-d');
                $mes_actual = date('n');  // Mes actual en formato numérico (1-12)

                // Actualizar y verificar mensualidad
                $mensaje_actualizacion = verificar_y_actualizar_pago_mensualidad($datosUsuarios, $mes_actual);

                // Continuar con el resto del código
                $MesIngreso = substr($datosUsuarios['FechaIngreso'], 0, 10);
                echo "<div class='deuda'>";
                echo "Fecha de Ingreso: " . $MesIngreso . "<br>";
                echo "Fecha Actual: " . $MesActual . "<br>";

                $datetimeIngreso = new DateTime($MesIngreso);
                $datetimeActual = new DateTime($MesActual);

                // Calcular la diferencia del mes actual
                $diferencia = $datetimeIngreso->diff($datetimeActual);

                // Obtener la cantidad de meses transcurridos
                $mesesTranscurridos = ($diferencia->y * 12) + $diferencia->m;

                // Mostrar el resultado de meses transcurridos
                echo "Han pasado $mesesTranscurridos meses desde que se inscribió el Afiliado.<br>";
                $mesesTranscurridos += 1;

                // Calcular los días restantes para completar el próximo mes
                $proxMesIngreso = clone $datetimeIngreso;
                $proxMesIngreso->modify('+1 month');
                $proxMesIngreso->setDate($datetimeIngreso->format('Y'), $datetimeIngreso->format('m') + $mesesTranscurridos + 1, 1);
                $diferenciaDias = $proxMesIngreso->diff($datetimeActual)->format('%r%a');

                // Mostrar el resultado de días restantes
                echo "Faltan " . abs($diferenciaDias) . " días para el pago del siguiente mes.<br>";

                // Mostrar el estado actualizado
                if ($datosUsuarios['saldo'] < 0) {
                    echo '<h1 style="color:red;">Tiene un saldo pendiente de Bs '.round($datosUsuarios['saldo'] * $Dolar,2).' al cambio $' . round(abs($datosUsuarios['saldo']), 2) . '<br>' . $mensaje_actualizacion . '</h1>';
                } else if ($datosUsuarios['saldo'] == 0) {
                    echo '<h1 style="color:lightgreen;">La deuda mensual está saldada.<br>' . $mensaje_actualizacion . '</h1>';
                } else if ($datosUsuarios['saldo'] > 0) {
                    echo '<h1 style="color:lightblue;">Ha abonado Bs '.round($Dolar * $datosUsuarios['saldo'] , 2).' al cambio $' . round($datosUsuarios['saldo'], 2) . ' dólares.<br> El excedente se aplicará a futuros pagos. ' . $mensaje_actualizacion . '</h1>';
                }

                echo '</div>';
            }
            // Si el tipo de usuario es 2 (invitado), consultar aportes_donaciones
            else if ($tipo_usuario == 2) {
                $sql_donaciones = "SELECT * FROM aportes_donaciones WHERE id_persona = $id_persona" . $date_filter;
                $result_donaciones = $conexion->query($sql_donaciones);


                if ($result_donaciones === false) {
                    die("Error en la consulta SQL para aportes_donaciones: " . $conexion->error);
                }

                echo "<table border='1'>";
                echo "<tr>
                    <th>Tipo Usuario</th>
                    <th>Tipo Operación</th>
                    <th>Fecha Aporte</th>
                    <th></th>
                    <th>Monto Recibido</th>
                    <th>Concepto</th>
                    <th>Estado</th>
                  </tr>";

                while ($row = $result_donaciones->fetch_assoc()) {
                    $monto = str_replace(',', '.', str_replace('.', '', $row['montoRecibido']));
                    $monto = str_replace('Bs', '', $monto);
                    $monto = floatval(preg_replace('/[^0-9.]/', '', $monto));
                    $montoTotal += $monto;
                    if ($row['estado'] == 'Pendiente') {
                        $montoPendiente += $monto;
                    } elseif ($row['estado'] == 'Declinado') {
                        $montoDeclinado += $monto;
                    } elseif ($row['estado'] == 'Aprobado') {
                        $montoAprobado += $monto;
                    }
                    echo "<tr>
                        <td>{$row['tipo_usuario']}</td>
                        <td>{$row['tipo_operacion']}</td>
                        <td>{$row['fechaAporte']}</td>
                        <td></td>
                        <td>{$row['montoRecibido']}</td>
                        <td>{$row['concepto']}</td>
                        <td>{$row['estado']}</td>
                      </tr>";
                }

                echo "</table>";
            }

            // Mostrar montos totales
            echo "<div class='monto'><p>Total Aporte: Bs " . number_format($montoTotal, 2, '.', ',') . "</p>";
            echo "<p>Total Pendiente: Bs " . number_format($montoPendiente, 2, '.', ',') . "</p>";
            echo "<p>Total Declinado: Bs " . number_format($montoDeclinado, 2, '.', ',') . "</p>";
            echo "<p>Total Aprobado: Bs " . number_format($montoAprobado, 2, '.', ',') . "</p></div>";
            echo "</div>";
        } else {
            die("No se encontró el usuario con id_persona = $id_persona");
        }


        ?>






</body>

</html>