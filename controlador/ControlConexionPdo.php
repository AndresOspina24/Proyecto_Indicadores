<?php
class ControlConexionPdo {
    // Propiedad para almacenar la conexión a la base de datos.
    private $conn;
    
    // En el constructor, inicializamos la conexión como null.
    function __construct() {
        $this->conn = null;
    }

    // Método para abrir una conexión a la base de datos utilizando PDO.
    function abrirBd($servidor, $usuario, $password, $db, $port) {
        try {
            // Construyendo el Data Source Name (DSN) para PDO, especificando el tipo de BD (mysql),
            // el host, el nombre de la base de datos, y el puerto.
            $dsn = "mysql:host={$servidor};dbname={$db};port={$port}";
            // Creando una nueva instancia de PDO, que abre una conexión a la base de datos.
            $this->conn = new PDO($dsn, $usuario, $password);
            // Configurando PDO para que lance excepciones cuando ocurra un error.
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Capturando y mostrando cualquier error de conexión.
            echo "ERROR AL CONECTARSE AL SERVIDOR: " . $e->getMessage() . "\n";
            // Termina el script si no se puede conectar.
            exit();
        }
    }

    // Método para cerrar la conexión a la base de datos.
    function cerrarBd() {
        // Anulando la propiedad de conexión, que cierra la conexión a la base de datos.
        $this->conn = null;
    }

// Método para ejecutar comandos SQL que no sean consultas (por ejemplo, INSERT, UPDATE, DELETE).
function ejecutarComandoSql($sql, $parametros = []) {
    try {
        // Prepara la instrucción SQL con los placeholders.
        $stmt = $this->conn->prepare($sql);

        // Ejecuta la instrucción SQL con los parámetros proporcionados.
        $resultado = $stmt->execute($parametros);

        // Verifica si la ejecución tuvo éxito y retorna verdadero (true).
        if ($resultado !== false) {
            return true;
        } else {
            // Lanza una excepción si hubo un problema en la ejecución.
            throw new Exception("Error en la ejecución del comando SQL.");
        }
    } catch (PDOException $e) {
        // Captura y muestra cualquier error que pueda ocurrir durante la ejecución del comando SQL.
        echo "Error al ejecutar el comando SQL: " . $e->getMessage() . "\n";
        // Retorna falso (false) en caso de error.
        return false;
    }
}

    // Método para ejecutar comandos SQL que son consultas (por ejemplo, SELECT).
    function ejecutarSelect($sql, $params = []) {
        try {
            // Preparando la consulta SQL.
            $stmt = $this->conn->prepare($sql);

            // Ejecutando la consulta con los parámetros.
            $stmt->execute($params);

            // Recuperando todas las filas del resultado de la consulta.
            // FETCH_ASSOC hace que el array de resultados use nombres de columnas como claves.
            $recordSet = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Retorna el conjunto de resultados como un array asociativo.
            return $recordSet;
        } catch (PDOException $e) {
            // Capturando y mostrando cualquier error que pueda ocurrir durante la ejecución de la consulta.
            echo "ERROR: " . $e->getMessage() . "\n";
            // Retorna false en caso de error.
            return false;
        }
    }

}

?>
