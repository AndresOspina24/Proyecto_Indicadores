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
$listaRolesDelUsuario = $_SESSION['listaRolesDelUsuario'] ?? [];
$esAdmin = false;
$esVerificador = false;
$esValidador = false;

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

$objControlRepresentacionVisualPorIndicador = new ControlEntidad('represenvisualporindicador');
$arreglo = $objControlRepresentacionVisualPorIndicador->listar();

$boton = $_POST['bt'] ?? '';

//  ¡VALIDACIÓN!
$fkidindicador = filter_input(INPUT_POST, 'txtIndicador', FILTER_VALIDATE_INT);
$fkidrepresenvisual = filter_input(INPUT_POST, 'txtRepresenVisual', FILTER_VALIDATE_INT);
$fkidindicadorOriginal = filter_input(INPUT_POST, 'txtIndicadorOriginal', FILTER_VALIDATE_INT);
$fkidrepresenvisualOriginal = filter_input(INPUT_POST, 'txtRepresenVisualOriginal', FILTER_VALIDATE_INT);

if ($boton != '' && ($fkidindicador === false || $fkidrepresenvisual === false || ($boton == 'Modificar' && ($fkidindicadorOriginal === false || $fkidrepresenvisualOriginal === false)))) {
    echo "<p class='alert alert-danger'>Error: Datos de entrada inválidos.</p>";
    exit();
}

switch ($boton) {
    case 'Guardar':
        if ($esAdmin) {
            $datos = ['fkidindicador' => $fkidindicador, 'fkidrepresenvisual' => $fkidrepresenvisual];
            $obj = new Entidad($datos);
            $objControlRepresentacionVisualPorIndicador->guardar($obj);
            header('Location: vistaRepresentacionVisualPorIndicador.php');
        } else {
            echo "<p class='alert alert-danger'>Error: Permiso denegado para realizar esta acción (Guardar).</p>";
        }
        break;

    case 'Modificar':
        if ($esAdmin || $esValidador) {
            $datosNuevos = ['fkidindicador' => $fkidindicador, 'fkidrepresenvisual' => $fkidrepresenvisual];
            $objNuevos = new Entidad($datosNuevos);

            $objControlRepresentacionVisualPorIndicador->modificar(
                ['fkidindicador', 'fkidrepresenvisual'],
                [$fkidindicadorOriginal, $fkidrepresenvisualOriginal],
                $objNuevos
            );

            header('Location: vistaRepresentacionVisualPorIndicador.php');
        } else {
            echo "<p class='alert alert-danger'>Error: Permiso denegado para realizar esta acción (Modificar).</p>";
        }
        break;

    case 'Borrar':
        if ($esAdmin) {
            $objControlRepresentacionVisualPorIndicador->borrar(
                ['fkidindicador', 'fkidrepresenvisual'],
                [$fkidindicador, $fkidrepresenvisual]
            );
            header('Location: vistaRepresentacionVisualPorIndicador.php');
        } else {
            echo "<p class='alert alert-danger'>Error: Permiso denegado para realizar esta acción (Borrar).</p>";
        }
        break;
}

$arregloIndicadoresConsulta = [];
$objcontrolIndicador = new ControlEntidad('indicador');
$arregloIndicadores = $objcontrolIndicador->listar();
$mapaIndicadores = [];
foreach ($arregloIndicadores as $ind) {
    $mapaIndicadores[$ind->__get('id')] = $ind->__get('nombre');
}

$arregloRepresenVisualConsulta = [];
$objcontrolRepresenVisual = new ControlEntidad('represenvisual');
$arregloRepresenVisuales = $objcontrolRepresenVisual->listar();
$mapaRepresenVisuales = [];
foreach ($arregloRepresenVisuales as $vis) {
    $mapaRepresenVisuales[$vis->__get('id')] = $vis->__get('nombre');
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
                        <h2 class="miEstilo">Gestión <b>RepresentacionVisualPorIndicador</b></h2>
                    </div>
                    <div class="col-sm-6">
                       <?php if ($esAdmin): ?>
                            <a href="#crudModal" class="btn btn-primary" data-toggle="modal"><i
                                    class="material-icons">&#xE84E;</i> <span>Gestión</span></a>
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
                        <th>Indicador</th>
                        <th>Representación Visual</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($arreglo as $item): ?>
                        <tr>
                            <td>
                                <span class="custom-checkbox">
                                    <input type="checkbox" name="options[]" value="<?= $item->__get('fkidindicador') . ';' . $item->__get('fkidrepresenvisual') ?>">
                                    <label></label>
                                </span>
                            </td>
                            <td><?= $mapaIndicadores[$item->__get('fkidindicador')] ?? 'Desconocido' ?></td>
                            <td><?= $mapaRepresenVisuales[$item->__get('fkidrepresenvisual')] ?? 'Desconocido' ?></td>
                            <td>
                                <a href="#editar" class="edit" data-toggle="modal"
                                   data-fkidindicador="<?= $item->__get('fkidindicador') ?>"
                                   data-fkidrepresenvisual="<?= $item->__get('fkidrepresenvisual') ?>">
                                    <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
                                </a>
                                <?php if ($esAdmin): ?>
                                    <a href="#borrar" class="delete" data-toggle="modal"
                                       data-fkidindicador="<?= $item->__get('fkidindicador') ?>"
                                       data-fkidrepresenvisual="<?= $item->__get('fkidrepresenvisual') ?>">
                                        <i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "../vista/basePie.html" ?>
<?php ob_end_flush(); ?>

<div id="crudModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="vistaRepresentacionVisualPorIndicador.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">RepresenVisualPorIndicador</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home">Datos de RepresenVisualPorIndicador</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="home" class="container tab-pane active"><br>
                                <div class="form-group">
                                    <label>Indicador</label>
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
                                    <label>RepresenVisual </label>
                                    <select id="txtRepresenVisual" name="txtRepresenVisual" class="form-control" >
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php foreach ($arregloRepresenVisuales as $re): ?>
                                            <option value="<?= $re->__get('id') ?>">
                                                <?= $re->__get('nombre') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php if ($esAdmin): ?>
                                    <div class="form-group">
                                        <input type="submit" id="btnGuardar" name="bt" class="btn btn-success" value="Guardar">
                                    </div>
                                <?php endif; ?>
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
            <form action="vistaRepresentacionVisualPorIndicador.php" method="post">
                <input type="hidden" name="bt" value="Modificar">
                <input type="hidden" name="txtIndicadorOriginal" id="edit_txtIndicadorOriginal">
                <input type="hidden" name="txtRepresenVisualOriginal" id="edit_txtRepresenVisualOriginal">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Representación Visual por Indicador</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Indicador</label>
                        <select id="edit_txtIndicador" name="txtIndicador" class="form-control">
                            <option value="" selected disabled>Seleccionar</option>
                            <?php foreach ($arregloIndicadores as $ind): ?>
                                <option value="<?= $ind->__get('id') ?>">
                                    <?= $ind->__get('nombre') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Representación Visual</label>
                        <select id="edit_txtRepresenVisual" name="txtRepresenVisual" class="form-control">
                            <option value="" selected disabled>Seleccionar</option>
                            <?php foreach ($arregloRepresenVisuales as $re): ?>
                                <option value="<?= $re->__get('id') ?>">
                                    <?= $re->__get('nombre') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                    <?php if ($esAdmin || $esValidador): ?>
                        <input type="submit" class="btn btn-warning" value="Modificar">
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="borrar" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="vistaRepresentacionVisualPorIndicador.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Borrar RepresentacionVisualPorIndicador</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar este registro?</p>
                    <p class="text-warning"><small>Esta acción no se puede deshacer.</small></p>
                    <input type="hidden" id="delete_txtIndicador" name="txtIndicador">
                    <input type="hidden" id="delete_txtRepresenVisual" name="txtRepresenVisual">
                    <input type="hidden" name="bt" value="Borrar">
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                    <?php if ($esAdmin): ?>
                        <input type="submit" class="btn btn-danger" value="Borrar">
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
        var fkidindicador = button.data('fkidindicador'); // Extrae info de los atributos data-*
        var fkidrepresenvisual = button.data('fkidrepresenvisual');
        var modal = $(this);
        modal.find('#edit_txtIndicador').val(fkidindicador);
        modal.find('#edit_txtRepresenVisual').val(fkidrepresenvisual);
        modal.find('#edit_txtIndicadorOriginal').val(fkidindicador); // Campos ocultos para el WHERE en la modificación
        modal.find('#edit_txtRepresenVisualOriginal').val(fkidrepresenvisual);
    });

    // JavaScript para pasar los IDs al modal de borrado
    $('#borrar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var fkidindicador = button.data('fkidindicador');
        var fkidrepresenvisual = button.data('fkidrepresenvisual');
        var modal = $(this);
        modal.find('#delete_txtIndicador').val(fkidindicador);
        modal.find('#delete_txtRepresenVisual').val(fkidrepresenvisual);
    });
</script>

<?php ob_end_flush(); ?>