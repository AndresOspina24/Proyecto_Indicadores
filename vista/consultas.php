<?php include "../vista/base_ini_head.html" ?>
<?php include "../vista/base_ini_body.html" ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultas de Indicadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="p-12">
<div class="container">
    <h2 class="mb-4">Consultas de Indicadores</h2>

    <!-- Botones -->
    <?php for ($i = 1; $i <= 7; $i++): ?>
        <button class="btn btn-primary m-2" data-bs-toggle="modal" data-bs-target="#modal<?= $i ?>">Consulta <?= $i ?></button>
    <?php endfor; ?>

    <?php
    // Conexión a la BD
    $conn = new mysqli("localhost", "root", "", "bd_indicadores");
    if ($conn->connect_error) {
        die("<div class='alert alert-danger'>Conexión fallida: " . $conn->connect_error . "</div>");
    }

    // Consultas SQL
    $consultas = [
        "SELECT i.id, i.codigo, i.nombre, i.objetivo, i.formula, i.meta, s.nombre AS nombre_sentido, u.descripcion AS descripcion_unidad
         FROM indicador i
         JOIN sentido s ON i.fkidsentido = s.id
         JOIN unidadmedicion u ON i.fkidunidadmedicion = u.id",

        "SELECT i.id, i.nombre, i.codigo, i.objetivo, i.formula, i.meta, rv.nombre AS nombre_representacion_visual
         FROM indicador i
         JOIN represenvisualporindicador rpi ON i.id = rpi.fkidindicador
         JOIN represenvisual rv ON rpi.fkidrepresenvisual = rv.id",

        "SELECT i.id, i.nombre, i.codigo, i.objetivo, i.formula, i.meta, a.nombre AS nombre_responsable, ta.nombre AS tipo_actor
         FROM indicador i
         JOIN responsablesporindicador rpi ON i.id = rpi.fkidindicador
         JOIN actor a ON rpi.fkidresponsable = a.id
         JOIN tipoactor ta ON a.fkidtipoactor = ta.id",

        "SELECT i.id, i.nombre, i.codigo, i.objetivo, i.formula, i.meta, f.nombre AS nombre_fuente
         FROM indicador i
         JOIN fuentesporindicador fpi ON i.id = fpi.fkidindicador
         JOIN fuente f ON fpi.fkidfuente = f.id",

        "SELECT i.id, i.nombre, i.codigo, i.objetivo, i.formula, i.meta, v.nombre AS nombre_variable, vpi.dato, vpi.fechadato
         FROM indicador i
         JOIN variablesporindicador vpi ON i.id = vpi.fkidindicador
         JOIN variable v ON vpi.fkidvariable = v.id",

        "SELECT i.id, i.nombre, i.codigo, i.objetivo, i.formula, i.meta, ti.nombre AS nombre_tipo_indicador
         FROM indicador i
         JOIN tipoindicador ti ON i.fkidtipoindicador = ti.id",

        "SELECT u.email, u.nombre, r.nombre AS nombre_rol
         FROM usuario u
         JOIN rolporusuario rpu ON u.email = rpu.fkemailusuario
         JOIN rol r ON rpu.fkidrol = r.id"
    ];

    // Función para mostrar tabla HTML
    function renderTabla($conn, $sql) {
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered table-hover'>";
            echo "<thead class='table-light'><tr>";
            while ($campo = $result->fetch_field()) {
                echo "<th>" . htmlspecialchars($campo->name) . "</th>";
            }
            echo "</tr></thead><tbody>";
            while ($fila = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($fila as $valor) {
                    echo "<td>" . htmlspecialchars($valor) . "</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "<div class='alert alert-warning'>No se encontraron resultados.</div>";
        }
    }

    // Generar los 7 modales
    for ($i = 1; $i <= 7; $i++):
    ?>
    <div class="modal fade" id="modal<?= $i ?>" tabindex="-1" aria-labelledby="modal<?= $i ?>Label" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal<?= $i ?>Label">Resultado de Consulta <?= $i ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <?php renderTabla($conn, $consultas[$i - 1]); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <?php endfor; ?>

    <?php $conn->close(); ?>
</div>
</body>
</html>
