    <?php


    // Definir las constantes de conexión a la base de datos
    define('DB_SERVER', 'localhost'); // Servidor de la base de datos
    define('DB_USERNAME', 'root');     // Nombre de usuario de la base de datos
    define('DB_PASSWORD', '');         // Contraseña de la base de datos (vacía en este caso)
    define('DB_DATABASE', 'sacips_bd'); // Nombre de la base de datos

    // Crear una clase para manejar la conexión a la base de datos
    class sacips_bd {
        private $connection;

        // Constructor para establecer la conexión a la base de datos
        public function __construct() {
            // Intenta establecer conexión usando MySQLi
            $this->connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

            // Verificar la conexión
            if ($this->connection->connect_error) {
                die("Conexión fallida: " . $this->connection->connect_error); // Muestra un mensaje de error si falla
            }
        }

        // Método para obtener la conexión actual
        public function getConnection() {
            return $this->connection; // Devuelve la conexión
        }

        // Método para cerrar la conexión
        public function closeConnection() {
            if ($this->connection) {
                $this->connection->close(); // Cierra la conexión si está abierta
            }
        }
    }

    // Crear una instancia de la clase Database
    $db = new sacips_bd();
    $connection = $db->getConnection(); // Obtener la conexión

    // Consultar todas las tablas: ipspuptyab_cuentas, ipspuptyab_info, ipspuptyab_sistema
    $queryCuentas = "
        SELECT 
            c.*, 
            b.nombre_banco,
            b.id_banco,
            t.tipo AS nombre_tipo_operacion
        FROM 
            ipspuptyab_cuentas c 
        LEFT JOIN 
            banco b ON c.banco = b.id_banco
        LEFT JOIN 
            tipo_operacion t ON c.formato_cuenta = t.id_tipoOperacion
        ORDER BY 
            b.nombre_banco DESC
    ";



    $queryInfo = "SELECT * FROM ipspuptyab_info";
    $querySistema = "SELECT * FROM ipspuptyab_sistema";
    $queryBancos = "SELECT * FROM banco";
    $queryMetodoPago = "SELECT * FROM tipo_operacion WHERE categoria_pago = 'DIGITAL'";

    $queryPersonas = "SELECT * FROM personas p
    LEFT JOIN
    usuarios u ON p.id_Personas = u.id_persona";


    // Ejemplo de ejecución de consultas
    $resultCuentas = $connection->query($queryCuentas);
    $resultInfo = $connection->query($queryInfo);
    $resultSistema = $connection->query($querySistema);
    $resultBanco = $connection->query($queryBancos);
    $resultMetodoPago = $connection->query($queryMetodoPago);
    $resultPersona = $connection->query($queryPersonas);



    // // Procesar resultados de la consulta de cuentas
    // if ($resultCuentas->num_rows > 0) {
    //     while ($row = $resultCuentas->fetch_assoc()) {
    //         // Procesar cada fila (ejemplo: imprimir)
    //         echo "Cuenta: " . $row['formato_cuenta'] . "<br>"; // Cambiar según la columna que necesites mostrar
    //     }
    // }

    // // Procesar resultados de la consulta de info
    // if ($resultInfo->num_rows > 0) {
    //     while ($row = $resultInfo->fetch_assoc()) {
    //         // Procesar cada fila (ejemplo: imprimir)
    //         echo "Información: " . $row['razon_social'] . "<br>"; // Cambiar según la columna que necesites mostrar
    //     }
    // }

    // // Procesar resultados de la consulta de sistema
    // if ($resultSistema->num_rows > 0) {
    //     while ($row = $resultSistema->fetch_assoc()) {
    //         // Procesar cada fila (ejemplo: imprimir)
    //         echo "Sistema: " . $row['nombre_sistema'] . "<br>"; // Cambiar según la columna que necesites mostrar
    //     }
    // }

    //Cerrar la conexión (opcional, pero recomendable)
    ?>
