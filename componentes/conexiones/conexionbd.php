<?php
$servidor = "localhost";
$usuariobd = "root";
$conts = "";
$baseDato = "sacips_bd";

// Establecer la zona horaria a "America/Caracas"
date_default_timezone_set('America/Caracas');

$con = mysqli_connect($servidor, $usuariobd, $conts, $baseDato);

if (!$con) {
    die("Conexión fallida: " . mysqli_connect_error());
}

if (!function_exists('obtenerDatosSistema')) {
    function obtenerDatosSistema($con) {
        // Definir la consulta SQL
        $sqlSistema = 'SELECT nombre_sistema, titulo_sistema, subtitulo_sistema, logo_sistema FROM ipspuptyab_sistema LIMIT 1';

        // Ejecutar la consulta dependiendo del tipo de conexión
        if ($con instanceof PDO) {
            $resultadoSistema = $con->query($sqlSistema);
            $sistemaInfo = $resultadoSistema->fetch(PDO::FETCH_ASSOC);
        } else if ($con instanceof mysqli) {
            $resultadoSistema = $con->query($sqlSistema);
            $sistemaInfo = $resultadoSistema->fetch_assoc();
        } else {
            die("Tipo de conexión no soportado.");
        }

        // Asegúrate de que no esté vacío
        if (!$sistemaInfo) {
            die("No se encontró información del sistema.");
        }

        // Aquí asignaremos las variables correspondientes
        return [
            'nombre_sistema' => $sistemaInfo['nombre_sistema'],
            'titulo_sistema' => $sistemaInfo['titulo_sistema'],
            'subtitulo_sistema' => $sistemaInfo['subtitulo_sistema'],
            'logo_sistema' => $sistemaInfo['logo_sistema'] // base64
        ];
    }
}



/*
// Uso de la función (ejemplo)

if ($con instanceof PDO || $con instanceof mysqli) {
    $sistemaData = obtenerDatosSistema($con);
    
    // Variables
    $nombre_sistema = $sistemaData['nombre_sistema'];
    $titulo_sistema = $sistemaData['titulo_sistema'];
    $subtitulo_sistema = $sistemaData['subtitulo_sistema'];
    $logo_sistema = $sistemaData['logo_sistema']; // base64
} else {
    die("Conexión no válida.");
}
    
*/
