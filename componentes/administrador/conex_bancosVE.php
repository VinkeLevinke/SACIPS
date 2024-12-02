<?php
//conexión.
    $servername = "localhost";
    $username = "root";
    $password = "";
    $bdname = "sacips_bd";
    $conexion = new mysqli($servername, $username, $password, $bdname);

    $sqlBD = "SELECT * FROM banco";
    $resultado = mysqli_query($conexion, $sqlBD);

?>