<?php

// Variables globales
$precio = '';

// Establecer la zona horaria a "America/Caracas"
date_default_timezone_set('America/Caracas');

function mostrarFechaActual() {
    // Establecer la zona horaria a "America/Caracas"
    date_default_timezone_set('America/Caracas');
    
    // Obtener la fecha y la hora actual
    $fechaActual = new DateTime();
    
    // Formatear la fecha y la hora
    $fechaFormateada = $fechaActual->format('d-m-Y'); // Formato de fecha YYYY-MM-DD
    $horaFormateada = $fechaActual->format('h:i A'); // Formato de hora AM/PM

    return [
        'fecha' => $fechaFormateada,
        'hora' => $horaFormateada
    ];
}

// Llamar a la función para obtener la fecha y hora actual
$fechaHoraActual = mostrarFechaActual();


/* Lo cambié a PDO para que funcione también con cualquier gestor de base de datos XD*/
try {
    $con = new PDO("mysql:host=localhost;dbname=sacips_bd", "root", "");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

$monto = ['Ingreso' => [], 'Egreso' => []];
$concepto = ['Ingreso' => [], 'Egreso' => []];
$fecha = ['Ingreso' => [], 'Egreso' => []];
/* inicializa los arrays para los montos numéricos */
$montoNumerico = ['Ingreso' => [], 'Egreso' => []];

// Inicializa los montos numéricos
$montoNumerico['Ingreso'] = [];
$montoNumerico['Egreso'] = [];

$sql = "
SELECT monto, concepto, fechaAporte AS fecha, estado, usuario AS tipo_usuario
FROM aportes_afiliados
WHERE (usuario NOT IN ('Invitado', 'Afiliado') OR estado = 'Aprobado')
UNION ALL 

SELECT montoRecibido AS monto, concepto, fechaAporte AS fecha, estado, tipo_usuario
FROM aportes_donaciones
WHERE (tipo_usuario NOT IN ('Invitado', 'Afiliado') OR estado = 'Aprobado')

UNION ALL

SELECT monto, concepto, fechaEmision AS fecha, '' AS estado, '' AS tipo_usuario
FROM aportes_patronales";


$resultado = $con->query($sql);

foreach ($resultado as $row) {
    // Verifica si el tipo de usuario es Afiliado o Invitado y el estado no es Aprobado
    if (($row['tipo_usuario'] === 'Invitado' || $row['tipo_usuario'] === 'Afiliado') && $row['estado'] !== 'Aprobado') {
        continue; // Salta este registro
    }

    // Extraemos el monto
    $montoLimpio = preg_replace('/[^\d,]/', '', $row['monto']);
    $montoLimpio = str_replace(',', '.', $montoLimpio); // Convertimos la coma decimal a punto
    $montoNumerico['Ingreso'][] = (float) $montoLimpio; // Guardamos como float

    // Guarda el texto original para mostrar en la tabla
    $monto['Ingreso'][] = $row['monto'];
    $concepto['Ingreso'][] = $row['concepto'];
    $fecha['Ingreso'][] = $row['fecha'];
    $estado['Ingreso'][] = $row['estado'];
    $estado['Ingreso'][] = $row['tipo_usuario'];
}



// Obtener egresos
$sqli = 'SELECT monto, rConcept, fechaPagoEgreso FROM registrar_egreso';
$resultado2 = $con->query($sqli);

foreach ($resultado2 as $row) {
    // Extrae el monto numérico usando expresión regular
    $montoLimpio = preg_replace('/[^\d,]/', '', $row['monto']);
    $montoLimpio = str_replace(',', '.', $montoLimpio); // Convertimos la coma decimal a punto
    $montoNumerico['Egreso'][] = (float) $montoLimpio; // Guardamos como float

    // Guarda el texto original para mostrar en la tabla
    $monto['Egreso'][] = $row['monto'];
    $concepto['Egreso'][] = $row['rConcept'];
    $fecha['Egreso'][] = $row['fechaPagoEgreso'];
}

$totalIngresoMesActual = 0;
$currentMonth = date('m');
$currentYear = date('Y');

foreach ($fecha['Ingreso'] as $index => $fechaIngreso) {
    $fechaObject = new DateTime($fechaIngreso);

    if ($fechaObject->format('m') === $currentMonth && $fechaObject->format('Y') === $currentYear) {
        $totalIngresoMesActual += $montoNumerico['Ingreso'][$index];
    }
}



/* Funciones para obtener y actualizar el precio del dólar */
function obtenerPrecioDolar()
{
    // Obtener la hora actual
    $horaActual = new DateTime();
    
    // Definir rangos de tiempo permitidos
    $rango1_inicio = new DateTime('09:29');
    $rango1_fin = new DateTime('09:35');
    $rango2_inicio = new DateTime('13:29');
    $rango2_fin = new DateTime('13:35');

    // Verificar si la hora actual está dentro de los rangos permitidos
    if (
        ($horaActual >= $rango1_inicio && $horaActual <= $rango1_fin) ||
        ($horaActual >= $rango2_inicio && $horaActual <= $rango2_fin)
    ) {
        $apiKey = "23e0fe3dcc9753cf9a77722b"; /* Llave de la Api*/
        $apiUrl = "https://v6.exchangerate-api.com/v6/$apiKey/latest/USD";

        $response = @file_get_contents($apiUrl);
        if ($response === FALSE) {
            return false; // Si no hay respuesta
        }

        $data = json_decode($response, true);
        return isset($data['conversion_rates']['VES']) ? $data['conversion_rates']['VES'] : false;
    } 
}

function obtenerUltimoPrecio($con)
{
    $sql = "SELECT precio FROM dolar_diario ORDER BY id DESC LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return $stmt->fetchColumn();
    }
    return null; // Cambiado a null para mejor manejo
}

function actualizarPrecioDolar($con, $precio)
{
    $sql = "UPDATE dolar_diario SET precio = ?, fecha = ?, hora_actualizacion = ? WHERE id = 1";
    $stmt = $con->prepare($sql);

    $horaActualizacion = date('H:i:s');
    $fechaHoy = date('Y-m-d');
    $stmt->execute([$precio, $fechaHoy, $horaActualizacion]);
}

/* Lógica para obtener el precio, aplicando restricción horaria */
$precioApi = obtenerPrecioDolar();
$horaActual = date('H:i');

$hoy = date('Y-m-d');
$ultimoPrecio = obtenerUltimoPrecio($con);
$precioActualizado = false;

if (
    (($horaActual >= '09:29' && $horaActual <= '09:35') || ($horaActual >= '13:29' && $horaActual <= '13:35')) && 
    $precioApi !== false
) {
    $precio = $precioApi; // Obtener el precio de la API
    actualizarPrecioDolar($con, $precio); // Actualiza en la base de datos
    $precioActualizado = true; // Marca que se ha actualizado el precio
} else if ($ultimoPrecio === null || $ultimoPrecio === false) {
    // Si no hay un precio disponible, consulta la API si hoy no se ha actualizado el precio
    $sqlCheck = "SELECT COUNT(*) FROM dolar_diario WHERE fecha = ?";
    $stmt = $con->prepare($sqlCheck);
    $stmt->execute([$hoy]);
    $count = $stmt->fetchColumn();

    if ($count == 0 && $precioApi !== false) {
        $precio = $precioApi; // Obtener el precio de la API
        actualizarPrecioDolar($con, $precio); // Actualiza en la base de datos
        $precioActualizado = true; // Marca que se ha actualizado el precio
    }
}

// Si no se ha actualizado el precio, asignamos el último precio
if (!$precioActualizado) {
    $precio = $ultimoPrecio !== null ? $ultimoPrecio : "No disponible"; // Usar el último precio
}


// Obtener datos del sistema
$sqlSistema = 'SELECT nombre_sistema, titulo_sistema, subtitulo_sistema, logo_sistema FROM ipspuptyab_sistema LIMIT 1';
$resultadoSistema = $con->query($sqlSistema);

// Los valores estarán en un solo registro, así que usamos fetch
$sistemaInfo = $resultadoSistema->fetch(PDO::FETCH_ASSOC);

// Asegúrate de que no esté vacío
if (!$sistemaInfo) {
    die("No se encontró información del sistema.");
}

// Aquí asignaremos las variables correspondientes
$nombre_sistema = $sistemaInfo['nombre_sistema'];
$titulo_sistema = $sistemaInfo['titulo_sistema'];
$subtitulo_sistema = $sistemaInfo['subtitulo_sistema'];
$logo_sistema = $sistemaInfo['logo_sistema']; // base64

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SACIPS | Bienvenido</title>
    <link rel="stylesheet" href="style/Styleindex.css">
    <link rel="stylesheet" href="./style/modales.css">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <!--<script src="js/api-dolar.js"></script>-->
    <style>
        #modal_loader {
            background: rgba(0, 0, 0, 0.8);
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1200;
            display: none;
        }

        .loader-content {
            text-align: center;
            color: #fff;
            background-color: #e5e5e5;
            padding: 30px;
            width: 30%;
            border-radius: 5px;
        }

        .face {
            position: relative;
            width: 60px;
            height: 60px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: float 2s ease-in-out infinite;
            margin: 0 auto 30px;
            margin-top: 30px;
        }

        .face::before {
            content: '';
            width: 100px;
            height: 100px;
            border: solid white 5px;
            position: absolute;
            border-radius: 100%;
            border-bottom: #00b6e4fa solid 5px;
            left: -20px;
            top: -20px;
            box-sizing: border-box;
            animation: carga 4s linear infinite;
            transition: 0.2s;
            box-shadow: 0px 0px 10px 0px #0016ff75;
        }

        .eye {
            position: absolute;
            top: 35%;
            width: 10px;
            height: 10px;
            background: #000;
            border-radius: 50%;
        }

        .eye.left {
            left: 25%;
        }

        .eye.right {
            right: 25%;
        }

        .mouth {
            position: absolute;
            bottom: 20%;
            left: 50%;
            width: 20px;
            height: 10px;
            background: #000;
            border-radius: 0 0 10px 10px;
            transform: translateX(-50%);
            animation: smile 4s linear infinite;
        }

        .wink {
            position: absolute;
            top: 30%;
            left: 25%;
            width: 10px;
            height: 5px;
            background: #fff;
            border-radius: 0%;
            transform: scaleX(0);
            opacity: 0;
            animation: wink 4s linear infinite;
        }

        .kiss {
            position: absolute;
            bottom: 20%;
            left: 50%;
            width: 10px;
            height: 10px;
            background: #000;
            border-radius: 50%;
            transform: scale(0);
            opacity: 0;
            animation: kiss 4s linear infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes smile {

            0%,
            100% {
                transform: translateX(-50%) translateY(0);
            }

            50% {
                transform: translateX(-50%) translateY(-4px);
            }
        }

        @keyframes carga {
            0% {
                transform: rotate(0deg);
                border-left: #00b6e4fa 5px solid;
                border-bottom: solid 5px white;
            }

            25% {
                /*transform: rotate(360deg);*/
                border-left: white 5px solid;
                border-top: #00b6e4fa 5px solid;

            }

            50% {
                border-right: #00b6e4fa 5px solid;
                border-top: solid 5px white;
                box-shadow: 0px 0px 20px 2px #0016ff75;
            }

            75% {
                border-right: white solid 5px;
                border-bottom: #00b6e4fa 5px solid;
            }

            100% {
                transform: rotate(360deg);
                border-left: #00b6e4fa 5px solid;
                border-bottom: solid 5px white;
            }
        }
    </style>
</head>

<body>
    <header class="header" id="indexHeader">

        <a href="index.html#title" class="titleHead">
            <p class="h1Title"><?php echo htmlspecialchars($nombre_sistema); ?></p>
        </a>


        <nav>
    <p><?php echo "Fecha: " . $fechaHoraActual['fecha'] . " - Hora: " . $fechaHoraActual['hora']; ?></p>
</nav>


        <div class="dolarActual">
            <p class="dolarText">Dolar</p>
            <p>$<?php echo $precio; ?> BCV</p>
        </div>

    </header>

    <div id="title" class="textHeader">
        <div class="containerBg">
            <div class="mgContainer">
                <h1 class="title"><?php echo htmlspecialchars($titulo_sistema); ?></h1>
                <p class="subtitle"><?php echo htmlspecialchars($subtitulo_sistema); ?></p>

                <div class="ingComo">
                    <button class="ingresarBtn" id="ingresarBtn">
                        <p>Acceder Como</p><img class="btnImg" src="img/lowArrow.png" alt="">
                    </button>
                    <div class="menuDesplegable" id="menuDesplegable">
                        <ul>
                            <li><a href="loginAfiliados.php">AFILIADO</a></li>
                            <li><a href="LoginInvitados.php">INVITADO</a></li>
                            <li><a href="LoginAdmins.php">DIRECTOR</a></li>
                            <li><a href="loginvigilancia.php">VIGILANTE</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


        <div class="ImagenHeader">
            <img class="responsive-img" src="data:image/png;base64,<?php echo $logo_sistema; ?>"
                alt="Logo de <?php echo htmlspecialchars($nombre_sistema); ?>">
        </div>
    </div>


    <section class="bodyPage">

        <div class="listDescription">

            <a href="index.php#info" class="listShape">
                <div class="list">
                    <p class="listTitle">Sobre nosotros</p>
                    <hr>
                    <div class="listbody">
                        <p class="text-list-Bod">Da un pequeño vistazo a lo que hacemos</p>
                    </div>

                </div>
                <div class="ir">

                    <p>ir</p>
                    <img class="listGo" src="img/arrow.svg" alt="">

                </div>

            </a>


            <a href="registrar-invitado.html" class="listShape">
                <div class="list">
                    <p class="listTitle">Regístrate como invitado</p>
                    <hr>
                    <div class="listbody">
                        <p class="text-list-Bod"> Forma parte de la comunidad registrándote como invitado.</p>
                    </div>

                </div>
                <div class="ir">

                    <p>ir</p>
                    <img class="listGo" src="img/arrow.svg" alt="">

                </div>

            </a>


            <a href="index.php#Movimientos" class="listShape">
                <div class="list">
                    <p class="listTitle">Movimientos</p>
                    <hr>
                    <div class="listbody">
                        <p class="text-list-Bod">Observa como se maneja los fondos en la institución</p>
                    </div>

                </div>
                <div class="ir">

                    <p>ir</p>
                    <img class="listGo" src="img/arrow.svg" alt="">

                </div>

            </a>

        </div>

        </div>

    </section>



    
    <section id="aboutUs">
    <div id="info"></div>
    <h2 class="qHacemos">¿Qué hacemos?</h2>
    <div class="separaicionAboutUs">
    <div id="info" class="listInfoDescript">
        <div class="listInfoShape">
            <div class="listInfo">
                <img src="img/information.png" alt="" class="infoImg">
                <p>Su objetivo principal consiste en realizar cuantos actos se destinen a promover y desarrollar
                    la protección médico asistencial y estabilidad socioeconómica de sus afiliados y su grupo
                    familiar (Comisión de Revisión y Modificación Estatutaria, 2006).</p>
            </div>
        </div>

        <div class="mgInfo"></div>

        <div class="listInfoShape">
            <div class="listInfo">
                <img src="img/information.png" alt="" class="infoImg">
                <p>IPSPUPTYAB es una asociación de carácter civil exento de toda finalidad de
                    lucro, que tiene como objetivo promover y desarrollar la protección médico asistencial y
                    estabilidad socioeconómica de sus afiliados y su grupo familiar, así como actividades que
                    contribuyan al mejoramiento de las condiciones de vida de la comunidad en general.
                </p>
            </div>
        </div>
    </div>
    <img src="img/members.jpg" class="membersImg" alt="">
    </div>
</section>


    <div id="register"></div>
    <section id="RegistroInv">

    </section>

    <section id="Movimientos">
        <div class="buscador">
            <h1>Movimientos recientes de la institución</h1>
            <p>Buscar Por:</p>
            <select>
                <option value="">Monto</option>
                <option value="">Concepto</option>
                <option value="">Fecha / Hora</option>
            </select>
            <div class="search">
                <input type="text" id="buscador" placeholder="Escribe Para Buscar...">
                <button>Buscar</button>
            </div>
        </div>

        <div class="tableMovs" id="ingresos">
            <h1>Aporte</h1>
            <table>
                <thead>
                    <tr>
                        <th>Monto</th>
                        <th>Concepto</th>
                        <th>Fecha / hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                      for ($i = 0; $i < count($monto['Ingreso']); $i++) {
                    $fechaRaw = $fecha['Ingreso'][$i];
                    $fechaObject = new DateTime($fechaRaw);
                    $fechaFormateada = $fechaObject->format('Y-m-d');
                    $horaFormateada = $fechaObject->format('h:i A');

                    



                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($monto['Ingreso'][$i]) . '</td>';
                    echo '<td>' . htmlspecialchars($concepto['Ingreso'][$i]) . '</td>';
                    echo '<td>' . $fechaFormateada . ' ' . $horaFormateada . '</td>';
                    echo '</tr>';
                }
                
                    ?>
                </tbody>

            </table>
        </div>

        <div class="tableMovs" id="egresos">
            <h1>Egreso</h1>
            <table>
                <thead>
                    <tr>
                        <th>Monto</th>
                        <th>Concepto</th>
                        <th>Fecha / hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i < count($monto['Egreso']); $i++) {
                        $fechaRaw = $fecha['Egreso'][$i];
                        $fechaObject = new DateTime($fechaRaw);
                        $fechaFormateada = $fechaObject->format('Y-m-d');
                        $horaFormateada = $fechaObject->format('h:i A');

                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($monto['Egreso'][$i]) . '</td>';
                        echo '<td>' . htmlspecialchars($concepto['Egreso'][$i]) . '</td>';
                        echo '<td>' . $fechaFormateada . ' ' . $horaFormateada . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="total">
            <h3>Total Ingreso: <?php echo number_format($totalIngresoMesActual, 2, ',', '.'); ?></h3>
            <h3>Total Egreso: <?php echo number_format(array_sum($montoNumerico['Egreso']), 2, ',', '.'); ?></h3>
        </div>
    </section>






    <!DOCTYPE html>

    <div id="modal_loader">
        <div class="loader-content">
            <div class="face">
                <div class="eye left"></div>
                <div class="eye right"></div>
                <div class="mouth"></div>
                <div class="wink"></div>
                <div class="kiss"></div>
            </div>
            <p>Espere un momento, por favor. <br>Su solicitud está siendo procesada.</p>
        </div>
    </div>

    <footer>

    </footer>

    <script>
        window.onscroll = function () {
            scrollFunction()
        };

        function scrollFunction() {
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                document.querySelector(".ingresarbtn").style.display = "block";
            } else {
                document.querySelector(".ingresarbtn").style.display = "none";
            }
        }

        const ingresarBtn = document.getElementById("ingresarBtn");
        const menuDesplegable = document.getElementById("menuDesplegable");
        const menuLinks = menuDesplegable.querySelectorAll("a");

        let menuIsOpen = false;

        ingresarBtn.addEventListener("click", () => {
            if (!menuIsOpen) {
                menuDesplegable.style.display = "block";
                menuIsOpen = true;
            } else {
                menuDesplegable.style.display = "none";
                menuIsOpen = false;
            }
        });

        menuLinks.forEach(link => {
            link.addEventListener("click", () => {
                menuDesplegable.style.display = "none";
                menuIsOpen = false;
            });
        });

        const originalTexts = []; // Array para guardar los textos originales

        // Al cargar la página, guarda los textos originales
        document.querySelectorAll('#Movimientos tbody tr').forEach((fila, index) => {
            originalTexts[index] = Array.from(fila.querySelectorAll('td')).map(td => td.innerHTML);
        });

        document.getElementById('buscador').addEventListener('input', function () {
            const query = this.value.toLowerCase(); // Convierte a minúsculas para comparación
            const filas = document.querySelectorAll('#Movimientos tbody tr');

            filas.forEach((fila, index) => {
                const tds = fila.querySelectorAll('td'); // Selecciona todos los <td>
                let matchFound = false;

                tds.forEach((td, tdIndex) => {
                    const originalText = originalTexts[index][
                        tdIndex
                    ]; // Obtiene el texto original
                    const regex = new RegExp(`(${query})`, 'gi'); // Crea una expresión regular

                    if (query && originalText.toLowerCase().includes(query)) {
                        td.innerHTML = originalText.replace(regex,
                            '<span class="highlight">$1</span>'); // Resalta la coincidencia
                        matchFound = true; // Marca que se encontró una coincidencia
                    } else {
                        td.innerHTML = originalText; // Restablece el contenido original
                    }
                });

                fila.style.display = matchFound || !query ? 'table-row' :
                    'none'; // Muestra u oculta la fila
            });
        });




        /* modal */
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('modal');
            const modalContent = document.querySelector('.modal-contentApi');
            const modalClose = document.getElementById('modalCloseApi');

            // Cambia la condición a "precioApi" para verificar si está seteado como falso.
            if (<?php echo json_encode($precioApi === false); ?>) {
                modal.style.display = 'block'; // Muestra el modal
                modalContent.style.animation = 'slideDown 0.5s forwards'; // Aplica la animación de entrada
            }

            // Configuración para cerrar el modal al hacer clic en la "X"
            modalClose.onclick = function () {
                closeModal();
            }

            // Cierra el modal si se hace clic fuera de él
            window.onclick = function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            }

            function closeModal() {
                modalContent.style.animation = 'slideUp 0.5s forwards'; // Aplica la animación de salida
                setTimeout(() => {
                    modal.style.display = 'none'; // Oculta el modal después de la animación
                }, 500); // Tiempo igual a la duración de la transición
            }
        });
    </script>

    <!-- Modal para advertencia de API -->
    <div id="modal" class="modalApi" style="display:none;">

        <div class="modal-contentApi">
            <span class="closeModalApi" id="modalCloseApi">&times;</span>

            <div class="infoModal">
                <h3 class="apiAdveretencia">ALERTA | SACIPS</h3>
                <img class="imgApi" src="./img/apiWarning.svg" alt="">
                <p>No hay conexión a Internet. Mostrando precio anterior del dólar. </p>
                <span class="dolarPrecio"> $<?php echo $precio; ?></span>

            </div>
        </div>
    </div>

</body>

</html>