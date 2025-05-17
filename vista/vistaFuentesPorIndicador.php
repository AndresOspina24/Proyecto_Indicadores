<?php
ob_start();
include '../controlador/configBd.php';
include '../controlador/ControlEntidad.php';
include '../controlador/ControlConexionPdo.php';
include '../modelo/Entidad.php';
session_start();
if ($_SESSION['email'] == null) {
    header('Location: ../index.php');
    exit();
}

$permisoParaEntrar = false;
$esAdmin = false;
$esVerificador = false;
$esValidador = false;

$listaRolesDelUsuario = $_SESSION['listaRolesDelUsuario'] ?? [];

foreach ($listaRolesDelUsuario as $rol) {
    $rolNombre = $rol->__get('nombre');
    if ($rolNombre == "admin") {
        $esAdmin = true;
        $permisoParaEntrar = true;
    }
    if ($rolNombre == "Verificador") {
        $esVerificador = true;
        $permisoParaEntrar = true;
    }
    if ($rolNombre == "Validador") {
        $esValidador = true;
        $permisoParaEntrar = true;
    }
}

if (!$permisoParaEntrar) {
    header('Location: ../vista/menu.php');
    exit();
}

$objControlFuentePorIndicador = new ControlEntidad('fuentesporindicador');
$arreglo = $objControlFuentePorIndicador->listar();

$boton = $_POST['bt'] ?? '';

//  ¡VALIDACIÓN!  (Mucho más robusta)
$fkidfuente = filter_input(INPUT_POST, 'txtidFuente', FILTER_VALIDATE_INT);
$fkidindicador = filter_input(INPUT_POST, 'txtIndicador', FILTER_VALIDATE_INT);
$fkidfuenteOriginal = filter_input(INPUT_POST, 'txtidFuenteOriginal', FILTER_VALIDATE_INT);
$fkidindicadorOriginal = filter_input(INPUT_POST, 'txtidIndicadorOriginal', FILTER_VALIDATE_INT);

//  Si la validación falla,  ¡NO continues!
if ($boton != '' && ($fkidfuente === false || $fkidindicador === false || $fkidfuenteOriginal === false || $fkidindicadorOriginal === false) ) {
    echo "<p class='alert alert-danger'>Error: Datos de entrada inválidos.</p>";
    exit();  //  O die();  para detener la ejecución completamente
}


switch ($boton) {
    case 'Guardar':
        if ($esAdmin) {
            $datos = ['fkidindicador' => $fkidindicador, 'fkidfuente' => $fkidfuente];
            $obj = new Entidad($datos);
            $objControl = new ControlEntidad('fuentesporindicador');
            $objControl->guardar($obj);
            header('Location: vistaFuentesPorIndicador.php');
            exit();
        } else {
            echo "<p class='alert alert-danger'>Error: Permiso denegado para realizar esta acción (Guardar).</p>";
        }
        break;

    case 'Consultar':
        if ($esAdmin || $esVerificador || $esValidador) {
            // Lógica para consultar un registro si txtidFuente y txtIndicador están llenos
            // **Implementación pendiente**
        } else {
            echo "<p class='alert alert-danger'>Error: Permiso denegado para realizar esta acción (Consultar).</p>";
        }
        break;

    case 'Modificar':
        if ($esAdmin || $esValidador) {
            // Lógica para modificar el registro
            $datosNuevos = ['fkidindicador' => $fkidindicador, 'fkidfuente' => $fkidfuente];
            $objNuevos = new Entidad($datosNuevos);
            $objControl = new ControlEntidad('fuentesporindicador');

            // Llamada a la función modificar con clave primaria compuesta
            $objControl->modificar(
                ['fkidfuente', 'fkidindicador'],
                [$fkidfuenteOriginal, $fkidindicadorOriginal],
                $objNuevos
            );  // No necesitamos asignar a $resultado

            header('Location: vistaFuentesPorIndicador.php');
            exit();

        } else {
            echo "<p class='alert alert-danger'>Error: Permiso denegado para realizar esta acción (Modificar).</p>";
        }
        break;

    case 'Borrar':
        if ($esAdmin) {
            try {
                $objControlFuentePorIndicador->borrar(
                    ['fkidfuente', 'fkidindicador'],
                    [$fkidfuente, $fkidindicador]
                );
            } catch (Exception $e) {
                echo "<p class='alert alert-danger'>Error al borrar: " . $e->getMessage() . "</p>";
                break;
            }
            header('Location: vistaFuentesPorIndicador.php');
            exit();
        } else {
            echo "<p class='alert alert-danger'>Error: Permiso denegado para realizar esta acción (Borrar).</p>";
        }
        break;

    default:
        break;
}

$arregloFuentesConsulta = [];
$objcontrolFuente = new ControlEntidad('fuente');
$arregloFuentes = $objcontrolFuente->listar();
$mapaFuentes = [];
foreach ($arregloFuentes as $fuen) {
    $mapaFuentes[$fuen->__get('id')] = $fuen->__get('nombre');
}

$arregloIndicadoresConsulta = [];
$objcontrolIndicador = new ControlEntidad('indicador');
$arregloIndicadores = $objcontrolIndicador->listar();
$mapaIndicadores = [];
foreach ($arregloIndicadores as $ind) {
    $mapaIndicadores[$ind->__get('id')] = $ind->__get('nombre');
}

?>

<?php include "../vista/base_ini_head.html" ?>
<?php include "../vista/base_ini_body.html" ?>

<div class="container-xl">
    <div class="table-responsive">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-6">
                        <h2 class="miEstilo">Gestión <b>FuentesPorIndicador</b></h2>
                    </div>
                    <div class="col-sm-6">
                        <?php if ($esAdmin): ?>
                            <a href="#crudModal" class="btn btn-primary" data-toggle="modal">
                                <i class="material-icons">&#xE84E;</i> <span>Gestión F</span>
                            </a>
                        <?php endif; ?>
                        </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>
                            <span class="custom-checkbox">
                                <input type="checkbox" id="selectAll">
                                <label for="selectAll"></label>
                            </span>
                        </th>
                        <th>Fuente</th>
                        <th>Indicador</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($arreglo as $item): ?>
                        <tr>
                            <td>
                                <span class="custom-checkbox">
                                    <input type="checkbox" name="options[]" value="<?= $item->__get('fkidfuente') . ';' . $item->__get('fkidindicador') ?>">
                                    <label></label>
                                </span>
                            </td>
                            <td><?= $mapaFuentes[$item->__get('fkidfuente')] ?? 'Desconocido' ?></td>
                            <td><?= $mapaIndicadores[$item->__get('fkidindicador')] ?? 'Desconocido' ?></td>
                            <td>
                                <?php if ($esAdmin || $esValidador): ?>
                                    <a href="#editar" class="edit" data-toggle="modal"
                                       data-fkidfuente="<?= $item->__get('fkidfuente') ?>"
                                       data-fkidindicador="<?= $item->__get('fkidindicador') ?>">
                                        <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($esAdmin): ?>
                                     <a href="#borrar" class="delete" data-toggle="modal"
                                       data-fkidfuente="<?= $item->__get('fkidfuente') ?>"
                                       data-fkidindicador="<?= $item->__get('fkidindicador') ?>">
                                        <i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($esVerificador && !$esAdmin && !$esValidador): ?>
                                    <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="clearfix">
                <div class="hint-text">Showing <b><?= count($arreglo) ?></b> entries</div>
                 <ul class="pagination">
                    <li class="page-item disabled"><a href="#">Previous</a></li>
                    <li class="page-item active"><a href="#" class="page-link">1</a></li>
                    <li class="page-item disabled"><a href="#">Next</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include "../vista/basePie.html" ?>
<?php ob_end_flush(); ?>


<div id="crudModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="vistaFuentesPorIndicador.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">FuentePorIndicador</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#home-crud">Datos de FuentePorIndicador</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="home-crud" class="container tab-pane active"><br>
                                <div class="form-group">
                                    <label>Fuente</label>
                                    <Select id="txtidFuente" name="txtidFuente" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php foreach ($arregloFuentes as $fuen): ?>
                                        <option value="<?= $fuen->__get('id') ?>">
                                        <?= $fuen->__get('nombre') ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </Select>
                                </div>
                                <div class="form-group">
                                    <label>Indicador </label>
                                    <Select id="txtIndicador" name="txtIndicador" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php foreach ($arregloIndicadores as $ind): ?>
                                        <option value="<?= $ind->__get('id') ?>">
                                        <?= $ind->__get('nombre') ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </Select>
                                </div>
                                <div class="form-group">
                                    <?php if ($esAdmin): ?>
                                         <input type="submit" id="btnGuardar" name="bt" class="btn btn-success" value="Guardar">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div id="menu1" class="container tab-pane fade"><br>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editar" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="vistaFuentesPorIndicador.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Editar FuentePorIndicador</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home-editar">Datos de FuentePorIndicador</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="home-editar" class="container tab-pane active"><br>
                                <div class="form-group">
                                    <label>Fuente (ID)</label>
                                    <input type="text" id="edit_txtidFuente" name="txtidFuente" class="form-control" >
                                    <input type="hidden" id="edit_txtidFuenteOriginal" name="txtidFuenteOriginal">
                                </div>
                                <div class="form-group">
                                    <label>Indicador (ID)</label>
                                     <input type="text" id="edit_txtIndicador" name="txtIndicador" class="form-control" >
                                     <input type="hidden" id="edit_txtIndicadorOriginal" name="txtidIndicadorOriginal">
                                     </div>
                                <div class="form-group">
                                     <?php if ($esAdmin || $esValidador): ?>
                                          <input type="submit" id="btnModificar" name="bt" class="btn btn-warning" value="Modificar">
                                     <?php endif; ?>
                                </div>
                            </div>
                             <div id="menu2" class="container tab-pane fade"><br>
                                </div>
                        </div>
                    </div>
                </div>
                 <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                </div>
             </form>
        </div>
    </div>
</div>


<div id="borrar" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
             <form action="vistaFuentesPorIndicador.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Borrar FuentePorIndicador</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                     <p>¿Está seguro de que desea eliminar este registro?</p>
                     <p class="text-warning"><small>Esta acción no se puede deshacer.</small></p>
                     <input type="hidden" id="delete_txtidFuente" name="txtidFuente">
                     <input type="hidden" id="delete_txtIndicador" name="txtIndicador">
                </div>
                 <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                    <?php if ($esAdmin): ?>
                         <input type="submit" name="bt" class="btn btn-danger" value="Borrar">
                    <?php endif; ?>
                     </div>
             </form>
        </div>
    </div>
</div>

<script>
    // JavaScript para pasar los IDs al modal de edición
    $('#editar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Botón que activó el modal
        var fkidfuente = button.data('fkidfuente'); // Extrae info de los atributos data-*
        var fkidindicador = button.data('fkidindicador');
        var modal = $(this);
        modal.find('#edit_txtidFuente').val(fkidfuente);
        modal.find('#edit_txtIndicador').val(fkidindicador);
        modal.find('#edit_txtidFuenteOriginal').val(fkidfuente); // Campos ocultos para el WHERE en la modificación
        modal.find('#edit_txtIndicadorOriginal').val(fkidindicador);
    });

    // JavaScript para pasar los IDs al modal de borrado
    $('#borrar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var fkidfuente = button.data('fkidfuente');
        var fkidindicador = button.data('fkidindicador');
        var modal = $(this);
        modal.find('#delete_txtidFuente').val(fkidfuente);
        modal.find('#delete_txtIndicador').val(fkidindicador);
    });
</script>

<?php ob_end_flush(); ?>