<?php

class ControlEntidad {
    private $tabla;  // El nombre de la tabla con la que trabajará la instancia.

    // Constructor que recibe el nombre de la tabla.
    function __construct($nombreTabla) {
        $this->tabla = $nombreTabla;
    }

    public function guardar(Entidad $entidad) {
        $controlConexion = new ControlConexionPdo();
        $controlConexion->abrirBd($GLOBALS['serv'], $GLOBALS['usua'], $GLOBALS['pass'], $GLOBALS['bdat'], $GLOBALS['port']); 
        try {
            $propiedades = $entidad->obtenerPropiedades();
            $campos = array_keys($propiedades);
            $valores = array_values($propiedades);
    
            // Crear placeholders para la sentencia preparada
            $placeholders = implode(", ", array_fill(0, count($campos), '?'));
    
            $sql = "INSERT INTO {$this->tabla} (" . implode(", ", $campos) . ") VALUES ({$placeholders})";
    
            // Ejecutar la consulta con la sentencia preparada
            $controlConexion->ejecutarComandoSql($sql, $valores);
        } catch (PDOException $e) {
            // Manejo de excepciones y errores
            throw new Exception("Error al guardar en {$this->tabla}: " . $e->getMessage());
        } finally {
            // Asegurar que la conexión se cierre siempre
            $controlConexion->cerrarBd();
        }
    }
    
    public function modificar($clavePrimaria, $valor, Entidad $entidad) {
        $controlConexion = new ControlConexionPdo();
        $controlConexion->abrirBd($GLOBALS['serv'], $GLOBALS['usua'], $GLOBALS['pass'], $GLOBALS['bdat'], $GLOBALS['port']); 
        try {
            // Obtener las propiedades a través del método correcto
            $propiedades = $entidad->obtenerPropiedades();
            
            $actualizaciones = [];
            $valores = [];
            foreach ($propiedades as $campo => $valorCampo) {
                if ($campo !== $clavePrimaria) { // Evitar incluir la clave primaria en la actualización
                    $actualizaciones[] = "{$campo} = ?";
                    $valores[] = $valorCampo;
                }
            }

            $valores[] = $valor; // Añadir al final el valor de la clave primaria para la condición WHERE.
            $camposStr = implode(", ", $actualizaciones); // Generar string para la consulta SQL

            $sql = "UPDATE {$this->tabla} SET {$camposStr} WHERE {$clavePrimaria} = ?";
            $controlConexion->ejecutarComandoSql($sql, $valores);
        } catch (PDOException $e) {
            echo "Error al actualizar en {$this->tabla}: " . $e->getMessage();
        } finally {
            $controlConexion->cerrarBd();
        }
    }

public function borrar($clavePrimaria, $valor) {
    $controlConexion = new ControlConexionPdo();
    $controlConexion->abrirBd($GLOBALS['serv'], $GLOBALS['usua'], $GLOBALS['pass'], $GLOBALS['bdat'], $GLOBALS['port']); 

    try {
        $sql = "DELETE FROM {$this->tabla} WHERE {$clavePrimaria} = ?";
        $controlConexion->ejecutarComandoSql($sql, [$valor]);
    } catch (PDOException $e) {
        echo "Error al eliminar en {$this->tabla}: " . $e->getMessage();
    } finally {
        $controlConexion->cerrarBd();
    }
}

// Método para buscar una entidad por su clave primaria.
public function buscarPorId($clavePrimaria, $valor) {
    $controlConexion = new ControlConexionPdo();
    $controlConexion->abrirBd($GLOBALS['serv'], $GLOBALS['usua'], $GLOBALS['pass'], $GLOBALS['bdat'], $GLOBALS['port']); 

    try {
        $sql = "SELECT * FROM {$this->tabla} WHERE {$clavePrimaria} = ?";
        $resultado = $controlConexion->ejecutarSelect($sql, [$valor]);

        if ($resultado) {
            return new Entidad($resultado[0]); // Devuelve un objeto Entidad si hay resultados.
        } else {
            return null; // Devuelve null si no hay resultados.
        }
    } catch (PDOException $e) {
        echo "Error al buscar en {$this->tabla}: " . $e->getMessage();
        return null; // Devuelve null en caso de error.
    } finally {
        $controlConexion->cerrarBd();
    }
}

    public function listar() {
        $controlConexion = new ControlConexionPdo();
        $controlConexion->abrirBd($GLOBALS['serv'], $GLOBALS['usua'], $GLOBALS['pass'], $GLOBALS['bdat'], $GLOBALS['port']); 
    
        try {
            $sql = "SELECT * FROM {$this->tabla}";
            $resultado = $controlConexion->ejecutarSelect($sql);
    
            $entidades = []; 
            foreach ($resultado as $fila) {
                $entidad = new Entidad($fila); 
                $entidades[] = $entidad;
            }
    
            return $entidades;
        } catch (PDOException $e) {
            echo "Error al obtener datos de {$this->tabla}: " . $e->getMessage();
            return [];
        } finally {
            $controlConexion->cerrarBd();
        }
    }
    public function consultar($sql, $parametros = []) {
        /*
            Atención
            Este método se puede usar así:
            ejemplo1:
            $sql = "SELECT usuarios.id, usuarios.nombre, pedidos.numero_pedido 
            FROM usuarios 
            INNER JOIN pedidos ON usuarios.id = pedidos.id_usuario 
            WHERE usuarios.nombre = ? AND pedidos.estado = ?";
            $parametros = ['Juan', 'entregado'];
            $resultados = $controlEntidad->consultar($sql, $parametros);
    
            ejemplo2:
            $sql = "SELECT id, nombre, estado FROM usuarios WHERE nombre = ? AND estado = ?";
            $parametros = ['Juan', 'activo'];
            $resultados = $controlEntidad->consultar($sql, $parametros);
    
            ejemplo3:
            $sql = "SELECT rol.id, rol.nombre 
            FROM rol_usuario INNER JOIN rol ON rol_usuario.fkidrol = rol.id
            WHERE fkemail = ?";
            $parametros = ['correo@ejemplo.com'];
    
            $resultados = $controlEntidad->consultar($sql, $parametros);
        */
        try {
            // instancia de ControlConexionPdo
            $controlConexion = new ControlConexionPdo();
            //  credenciales en ControlConexionPdo
            $controlConexion->abrirBd($GLOBALS['serv'], $GLOBALS['usua'], $GLOBALS['pass'], $GLOBALS['bdat'], $GLOBALS['port']); 
            // Ejecuta la consulta SQL con los parámetros proporcionados
            $resultados = $controlConexion->ejecutarSelect($sql, $parametros);
            // Cierra la conexión a la base de datos
            $controlConexion->cerrarBd();
            $entidades = []; // Un arreglo para almacenar las instancias de Entidad
            foreach ($resultados as $fila) {
                $entidad = new Entidad($fila); // Suponiendo que $fila contiene un array asociativo
                $entidades[] = $entidad;
            }
            return $entidades; // Devuelve un arreglo de objetos Entidad.
        } catch (PDOException $e) {
            // Maneja cualquier excepción que pueda ocurrir durante la ejecución de la consulta
            echo "Error al consultar: " . $e->getMessage();
            return [];
        }
    }  

}
?>
