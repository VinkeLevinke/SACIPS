<?php
$servidor = "localhost";
$usuariobd = "root";
$conts = "";
$baseDato = "sacips_bd";

try {
    // Establecer conexión PDO
    $conex = new PDO("mysql:host=$servidor;dbname=$baseDato", $usuariobd, $conts);
    // Establecer el modo de error de PDO a excepción
    $conex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Conexión fallida: " . $e->getMessage();
}

$sqlBD = "SELECT * FROM banco ORDER BY id_banco";
$resultado = $conex->query($sqlBD); // Usamos PDO::query en lugar de mysqli_query
?>

<!DOCTYPE html>
<lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Bancos</title>
    <link rel="stylesheet" href="../../style/tablas.css">
    <link rel="stylesheet" href="./style/modales.css">
    <script src="ajax.js"></script>
</head>
<style>

    
.btn-generico, .btn-imagenes {
    border: none;
    color: white;
    background-color: #636fce;
    letter-spacing: 1.2px;
    cursor: pointer;
    padding: 0.6em; 
}



.btn-busqueda {
  
    background-color: #636fce;
    letter-spacing: 1.2px;
    cursor: pointer;
    padding: 0.5% 1%;
    border-radius: 4px;
    position: fixed;
    bottom: 0;
    margin-bottom: 2%;
    border: none;
    color: white;
    transition:  .3s ease-in;
}

.btn-busqueda:hover {
    background-color: #4752a2;
    transform: scale(120%);
}


</style>
<body>
    <?php include "../../componentes/template/admHeader.php"; ?>
    <div class="bancoBody">

        <div class="cajaBotonBanco">
            <p class="titulo-movs">Gestion de bancos</p>


            <button onclick="abrirAdd_banco()" type="button" class='btn-imagenes'><img src="./img/aggBank.svg" alt="">
                <p>Registrar banco</p>
            </button>
        </div>

        <div class="busquedaBanco">
            <button type="button" class="btn-busqueda" onclick="abrirBuscarBanco()">
                <img src="./img/Lupa.svg" alt="">
                <p>Buscar</p>
            </button>
        </div>

        <div class="ConsultaBancos">
            <?php while ($row = $resultado->fetch(PDO::FETCH_ASSOC)): ?>

            <div id="bancoInfo" class="bancoShape" data-id="<?php echo $row['id']; ?>">
                <div class="admBancos">
                    <img src="./img/bank.svg" alt="" class="bancoImg">


                    <div class="bancoTexto">

                        <div class="bancos">
                            <p>ID:</p>
                            <div></div>
                            <p class="inputText" data-type="id"><?php echo $row['id']; ?></p>
                        </div>

                        <div class="bancos">
                            <p>Código:</p>
                            <div></div>
                            <p class="inputText" data-type="id_banco"><?php echo $row['id_banco']; ?></p>
                        </div>

                        <div class="bancos">
                            <p>Banco:</p>
                            <div></div>
                            <p class="inputText nombre_banco" data-type="nombre_banco">
                                <?php echo $row['nombre_banco']; ?></p>
                        </div>
                    </div>

                    <div class="editBox">
                        <button type="button"
                            onclick="abrirModalBanco(<?= $row['id'] ?>, '<?= $row['id_banco'] ?>', '<?= $row['nombre_banco'] ?>')">Editar</button>
                        <div></div>
                        <button type="button" class="bankDelete"
                            onclick="abrirModalEliminarBanco('<?= $row['id'] ?>', '<?= $row['id_banco'] ?>', '<?= $row['nombre_banco'] ?>')">Eliminar</button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>


    </div>

    <div id="modal_nuevoBanco" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal_banco()">×</span>
            <h2>Registrar Nuevo Banco</h2>

            <form id="agregarNuevoBanco">
            <div class="bancoshape">
                <div class="brmodal">
                    <label for="idBanco">Código de banco</label>
                  
                    <label for="nombreBanco">Nombre del Banco</label>
                </div>
                <div class="brmodal">
                <input type="text" id="idBanco" name="idBanco" placeholder="Código de Banco">
                    <input type="text" id="nombreBanco" name="nombreBanco" placeholder="Nombre del banco">
                </div>

                
               </div>
               <div class="modalbuttons">
                <button type="button" onclick="aggNewBanco()">Registrar</button>
                </div>
            </form>


        </div>
    </div>

    <div id="modal_editarBanco"  class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalBanco()">×</span>
            <h2>Editar Banco</h2>
          
            <form id="editarBancoForm">

            <div class="bancoshape">

            <div class="brmodal">
                    <input type="hidden" name="id_bancoTable" id="id_bancoTable">
                    <label for="idBancoEditar">Código de banco</label>
                    <label for="nombreBancoEditar">Nombre del Banco</label>
                    
                </div>
                <div class="brmodal">
                <input type="text" id="idBancoEditar" name="idBanco" placeholder="Código de Banco">
                    <input type="text" id="nombreBancoEditar" name="nombreBanco"
                        placeholder="Nombre completo del banco">
                </div>
                
                </div>
                <div class="brFormBTN">
                <button type="button" onclick="editNewBanco()">Guardar Cambios</button>
                </div>
            </form>
            <div id="mensajeRespuestaMetodoPago"></div>
        </div>
    </div>


    <div id="modalMensaje_general" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarMensaje_general()">X</span>
            <h2>Operación exitosa!</h2>
            <div id="mensajeBanco_agg" class="mensajeBanco_agg"></div>
        </div>
    </div>
</body>

<div id="modal_BuscarBanco" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarBuscarBanco()">×</span>
        <h2>Buscar Banco</h2>
        <form id="buscarBancoForm">
            <select name="criterio" id="criterio">
                <option disabled>Buscar por</option>
                <option value="ID">ID</option>
                <option value="Código">Código de banco</option>
                <option value="Nombre" selected>Nombre de banco</option>
            </select>
            <div></div>
            <input type="text" id="busquedaBanco" placeholder="Escribe para buscar...">
           
            
        </form>
        <hr>
        <p>Resultado:</p>
        <div class="resultadoBusquedaBanco" id="resultadoBusquedaBanco"></div>
    </div>
</div>

<div id="modal_eliminarBanco" class="modal">
    
    <div class="modal-content">
        <span class="close" onclick="cerrarModalEliminarBanco()">×</span>
        <h2>Eliminar Banco</h2>
        <p>¿Estás seguro de que deseas eliminar el banco con el código <span id="idBancoEliminar">

        </span> y nombre <span id="nombreBancoEliminar"></span>?</p>
        <input type="hidden" name="id_bancoTable" id="id_bancoTable">
        <div class="brFormBTN">


        <button class="eliminar" type="button" onclick="eliminarBanco()">Eliminar</button>

        </div>
        
    </div>
</div>


</html>