<?php
class ControlEntidad {
    // ... [El código previo  se mantiene igual]

    // Método para actualizar una entidad existente considerando una clave compuesta.
    public function actualizar($clavesPrimarias, Entidad $entidad) {
        try {
            $campos = [];
            $valores = [];
            $condiciones = [];

            foreach (get_object_vars($entidad) as $campo => $valor) {
                // Solo se actualizan los campos que no forman parte de la clave primaria.
                if (!array_key_exists($campo, $clavesPrimarias)) {
                    $campos[] = "{$campo} = ?";
                    $valores[] = $valor;
                }
            }

            foreach ($clavesPrimarias as $clave => $valor) {
                $condiciones[] = "{$clave} = ?";
                $valores[] = $valor; // Los valores de las claves van al final porque se utilizan en el WHERE.
            }

            $camposStr = implode(", ", $campos);
            $condicionesStr = implode(" AND ", $condiciones);

            // Preparación de la consulta.
            $stmt = $this->conexion->prepare("UPDATE {$this->tabla} SET {$camposStr} WHERE {$condicionesStr}");

            // Ejecución de la consulta.
            $stmt->execute($valores);
        } catch (PDOException $e) {
            echo "Error al actualizar en {$this->tabla}: " . $e->getMessage();
        }
    }

    // Método para eliminar una entidad por su clave primaria compuesta.
    public function eliminar($clavesPrimarias) {
        try {
            $condiciones = [];
            $valores = [];

            foreach ($clavesPrimarias as $clave => $valor) {
                $condiciones[] = "{$clave} = ?";
                $valores[] = $valor;
            }

            $condicionesStr = implode(" AND ", $condiciones);

            // Preparación de la consulta.
            $stmt = $this->conexion->prepare("DELETE FROM {$this->tabla} WHERE {$condicionesStr}");

            // Ejecución de la consulta.
            $stmt->execute($valores);
        } catch (PDOException $e) {
            echo "Error al eliminar en {$this->tabla}: " . $e->getMessage();
        }
    }

    // Método para buscar una entidad por su clave primaria compuesta.
    public function buscarPorClavesPrimarias($clavesPrimarias) {
        try {
            $condiciones = [];
            $valores = [];

            foreach ($clavesPrimarias as $clave => $valor) {
                $condiciones[] = "{$clave} = ?";
                $valores[] = $valor;
            }

            $condicionesStr = implode(" AND ", $condiciones);

            // Preparación de la consulta.
            $stmt = $this->conexion->prepare("SELECT * FROM {$this->tabla} WHERE {$condicionesStr}");

            // Ejecución de la consulta.
            $stmt->execute($valores);

            // Retorno de resultados.
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar en {$this->tabla}: " . $e->getMessage();
        }
    }

    // ... [El resto de tu clase se mantiene igual]
}
//$clavesPrimarias = ['id' => 123, 'tipo' => 'general'];
?>
