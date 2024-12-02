<?php
// Conexión
$servername = "localhost";
$username = "root";
$password = "";
$bdname = "sacips_bd";
$conex = new mysqli($servername, $username, $password, $bdname);

// Verifica la conexión
if ($conex->connect_error) {
    die("Conexión fallida: " . $conex->connect_error);
}

$sqlBD = "SELECT * FROM banco";
$resultado = mysqli_query($conex, $sqlBD);

// Condición para que todo funcione
if (isset($_POST['btn'])) {
    // Usar los nombres correctos de los campos
    $id_banco = $_POST['codigoBanco'];
    $nombre_banco = $_POST['nombreBanco']; // Cambiado a 'nombreBanco'
    
    
    // SQL de inserción
    $sql = "INSERT INTO banco (id_banco, nombre_banco) VALUES('$id_banco', '$nombre_banco')"; // Quitado 'id_banco'
    
    if ($conex->query($sql) === TRUE) {
        if ($resultado) {
            echo "<script languaje='JavaScript'>
            alert('Nuevo banco registrado') 
            location.assign('../../Admin.php');
            </script>";
        } else {
            echo "<script languaje='JavaScript'> 
            location.assign('../../Admin.php');
            </script>";
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conex->error; // Mensaje de error en caso de fallo
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Banco</title>
    <link rel="stylesheet" href="../../style/admin.css">
</head>
<body>
    <?php include "../../componentes/template/admHeader.php"; ?>

    <section class="repEgresos">
        <div class="repEgresosShape">
            <div class="titleheader">
                <p>Registrar un Nuevo Banco</p>
            </div>
            
            <hr>
            <div class="formShape">
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" id="formularioEgreso">
                    <div class="brtAporte">
                        <label for="codigoBanco">Código del Banco</label>
                        <input type="text" name="codigoBanco" id="codigoBanco" placeholder="Ingrese el código del banco: Ejemplo: '0102'." required="">
                    </div>
                    <div class="brtAporte">
                        <label for="nombreBanco">Banco</label>
                        <input type="text" name="nombreBanco" id="nombreBanco" placeholder="Ingrese el nombre del banco: Ejemplo: 'Banco de Venezuela'." required="">
                    </div>
                    <button name="btn" class="submitReport" type="submit">Agregar Banco</button>
                </form>
            </div>
        </div>
    </section>
</body>
</html>