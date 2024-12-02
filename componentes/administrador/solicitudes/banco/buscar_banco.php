<?php
$dsn = 'mysql:host=localhost;dbname=sacips_bd';
$usuario = 'root';
$pass = '';

try {
    $conex = new PDO($dsn, $usuario, $pass);
    $conex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Conexion fallida: ' . $e->getMessage();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = $_POST['query'];
    $criterio = $_POST['criterio'];
    $sql = "SELECT * FROM banco WHERE ";

    if ($criterio === "ID") {
        $sql .= "id LIKE :query";
    } elseif ($criterio === "Código") {
        $sql .= "id_banco LIKE :query";
    } elseif ($criterio === "Nombre") {
        $sql .= "nombre_banco LIKE :query";
    }

    try {
        $stmt = $conex->prepare($sql);
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="bancoShape" data-id="' . $row['id'] . '">
                    <div class="admBancos">
                        <img src="./img/bank.svg" alt="" class="bancoImg">
                        <div class="bancoTexto">
                            <div class="bancos">
                                <p>ID:</p>
                                <p class="inputText" data-type="id_banco">' . $row['id'] . '</p>
                            </div>
                            <div class="bancos">
                                <p>Código:</p>
                                <p class="inputText" data-type="id_banco">' . $row['id_banco'] . '</p>
                            </div>
                            <div class="bancos">
                                <p>Banco:</p>
                                <p class="inputText nombre_banco" data-type="nombre_banco">' . $row['nombre_banco'] . '</p>
                            </div>
                        </div>
                        <div class="editBox">
                 <button type="button" onclick="abrirModalBanco(' . $row['id'] . ', \'' . $row['id_banco'] . '\', \'' . $row['nombre_banco'] . '\'); cerrarBuscarBanco()">Editar</button>

                    <div></div>
                    <button type="button" class="bankDelete" onclick="abrirModalEliminarBanco(' . $row['id'] . ', \'' . $row['id_banco'] . '\', \'' . $row['nombre_banco'] . '\')">Eliminar</button>
                  </div>
                    </div>
                  </div>
                  ';
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>