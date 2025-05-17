<?php
ob_start();
include '../controlador/configBd.php';
include '../controlador/ControlEntidad.php';
include '../controlador/ControlConexionPdo.php';
include '../modelo/Entidad.php';
session_start();
if ($_SESSION['email'] == null) header('Location: ../index.php');

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

if (!$permisoParaEntrar) header('Location: ../vista/menu.php');

$objControlVariablesPorIndicador = new ControlEntidad('variablesporindicador');
$arreglo = $objControlVariablesPorIndicador->listar();

$boton = $_POST['bt'] ?? '';

$id = $_POST['txtId'] ?? '';
$fkidvariable = $_POST['txtfkidvariable'] ?? '';
$fkidindicador = $_POST['txtfkidindicador'] ?? '';
$fkemailusuario = $_POST['txtfkemailusuario'] ?? '';
$dato = $_POST['txtDato'] ?? '';
$fechadato = $_POST['txtFechaDato'] ?? '';
$listbox1 = $_POST['listbox1'] ?? []; // Captura los roles seleccionados


switch ($boton) {
    case 'Guardar':
        if ($esAdmin) {
            $datos = ['id' => $id, 'fkidvariable' => $fkidvariable, 'fkidindicador' => $fkidindicador, 'fkemailusuario' => $fkemailusuario, 'dato' => $dato, 'fechadato' => $fechadato];
            $obj = new Entidad($datos);
			$objControlVariablesPorIndicador = new ControlEntidad('variablesporindicador');
            $objControlVariablesPorIndicador->guardar($obj);
            header('Location: vistaVariablesPorIndicador.php');
        } else {
            echo "<p class='alert alert-danger'>No tiene permiso para Guardar.</p>";
        }
        break;
    case 'Borrar':
        if ($esAdmin) {
            $datos = ['id' => $id, 'fkidvariable' => $fkidvariable, 'fkidindicador' => $fkidindicador, 'fkemailusuario' => $fkemailusuario, 'dato' => $dato, 'fechadato' => $fechadato];
            $obj = new Entidad($datos);
			$objControlVariablesPorIndicador = new ControlEntidad('variablesporindicador');
            $objControlVariablesPorIndicador->borrar('id', $id, 'fkidvariable', $fkidvariable, 'fkidindicador', $fkidindicador, 'fkemailusuario', $fkemailusuario, 'dato', $dato, 'fechadato', $fechadato);
            header('Location: vistaVariablesPorIndicador.php');
        } else {
            echo "<p class='alert alert-danger'>No tiene permiso para Borrar.</p>";
        }
        break;
    case 'Modificar':
        if ($esAdmin || $esValidador) {
            $datos = ['id' => $id, 'fkidvariable' => $fkidvariable, 'fkidindicador' => $fkidindicador, 'fkemailusuario' => $fkemailusuario, 'dato' => $dato, 'fechadato' => $fechadato];
            $obj = new Entidad($datos);
			$objControlVariablesPorIndicador = new ControlEntidad('variablesporindicador');
            $objControlVariablesPorIndicador->modificar(['id', 'fkidvariable', 'fkidindicador', 'fkemailusuario'], [$id, $fkidvariable, $fkidindicador, $fkemailusuario], $obj);
            header('Location: vistaVariablesPorIndicador.php');
        } else {
            echo "<p class='alert alert-danger'>No tiene permiso para Modificar.</p>";
        }
        break;
}


$arregloVariableConsulta = [];
$objcontrolVariable = new ControlEntidad('variable');
$arregloVariables = $objcontrolVariable->listar();
foreach ($arregloVariables as $ind) {
    $arregloVariables[$ind->__get('id')] = $ind->__get('nombre');
}

$arregloIndicadorConsulta = [];
$objcontrolIndicador = new ControlEntidad('indicador');
$arregloIndicadores = $objcontrolIndicador->listar();
foreach ($arregloIndicadores as $vis) {
    $arregloIndicadores[$vis->__get('id')] = $vis->__get('nombre');
}

$arregloUsersConsulta = [];
$objcontrolUser = new ControlEntidad('usuario');
$arregloUser = $objcontrolUser->listar();
foreach ($arregloUser as $u) {
    $arregloUser[$u->__get('email')] = $u->__get('email');
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
                        <h2 class="miEstilo">Gestión <b>Variables por Indicador</b></h2>
                    </div>
                    <div class="col-sm-6">
                        <?php if ($esAdmin): ?>
                            <a href="#crudModal" class="btn btn-primary" data-toggle="modal">
                                <i class="material-icons">&#xE84E;</i> <span>Gestión</span>
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
                        <th>ID</th>
                        <th>Id Variable</th>
                        <th>Id Indicador</th>
                        <th>Usuario</th>
                        <th>Dato</th>
                        <th>Fecha Dato</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($arreglo as $item): ?>
                        <tr>
                            <td>
                                <span class="custom-checkbox">
                                    <input type="checkbox" name="options[]" value="1">
                                    <label></label>
                                </span>
                            </td>
                            <td><?= $item->__get('id') ?? 'Desconocido' ?></td>
                            <td><?= $arregloVariables[$item->__get('fkidvariable')] ?? 'Desconocido' ?></td>
                            <td><?= $arregloIndicadores[$item->__get('fkidindicador')] ?? 'Desconocido' ?></td>
                            <td><?= $arregloUser[$item->__get('fkemailusuario')] ?? 'Desconocido' ?></td>
                            <td><?= $item->__get('dato') ?? 'Sin Dato' ?></td>
                            <td><?= $item->__get('fechadato') ?? 'Sin Fecha' ?></td>
                            <td>
                                <?php if ($esAdmin || $esValidador): ?>
                                    <a href="#editar" class="edit" data-toggle="modal"
                                       data-id="<?= $item->__get('id') ?>"
                                       data-fkidvariable="<?= $item->__get('fkidvariable') ?>"
                                       data-fkidindicador="<?= $item->__get('fkidindicador') ?>"
                                       data-fkemailusuario="<?= $item->__get('fkemailusuario') ?>"
                                       data-dato="<?= $item->__get('dato') ?>"
                                       data-fechadato="<?= $item->__get('fechadato') ?>">
                                        <i class="material-icons" data-toggle="tooltip" title="Editar">&#xE254;</i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($esAdmin): ?>
                                    <a href="#borrar" class="delete" data-toggle="modal"
                                       data-id="<?= $item->__get('id') ?>"
                                       data-fkidvariable="<?= $item->__get('fkidvariable') ?>"
                                       data-fkidindicador="<?= $item->__get('fkidindicador') ?>"
                                       data-fkemailusuario="<?= $item->__get('fkemailusuario') ?>"
                                       data-dato="<?= $item->__get('dato') ?>"
                                       data-fechadato="<?= $item->__get('fechadato') ?>">
                                        <i class="material-icons" data-toggle="tooltip" title="Borrar">&#xE872;</i>
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
            <form action="vistaVariablesPorIndicador.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Variables Por Indicador</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home">Datos de Variables Por
                                    Indicador</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="home" class="container tab-pane active"><br>
							 <div class="form-group">
                                    <label>ID</label>
                                    <input type="text" id="txtId" name="txtId" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Variable</label>
                                    <Select id="txtfkidvariable" name="txtfkidvariable" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php $arregloVariables = $objcontrolVariable->listar(); ?>
                                        <?php foreach ($arregloVariables as $vis): ?>
                                            <option
                                                value=<?php echo $vis->__get('id') ?? 'Desconocido' ?>>
                                                <?= $vis->__get('nombre') ?? 'Sin nombre' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </Select>
                                </div>
                                <div class="form-group">
                                    <label>Indicador </label>
                                    <Select id="txtIndicador" name="txtIndicador" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php $arregloIndicadores = $objcontrolIndicador->listar(); ?>
                                        <?php foreach ($arregloIndicadores as $ind): ?>
                                            <option
                                                value=<?php echo $ind->__get('id') ?? 'Desconocido' ?>>
                                                <?= $ind->__get('nombre') ?? 'Sin nombre' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Usuario </label>
                                    <Select id="txtfkemailusuario" name="txtfkemailusuario" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php $arregloUser = $objcontrolUser->listar(); ?>
                                        <?php foreach ($arregloUser as $u): ?>
                                            <option
                                                value=<?php echo $u->__get('email') ?? 'Desconocido' ?>>
                                                <?= $u->__get('email') ?? 'Sin nombre' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </Select>
                                </div>
                                <div class="form-group">
                                    <label>Dato</label>
                                    <input type="text" id="txtDato" name="txtDato" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Fecha Dato</label>
                                    <input type="date" id="txtFechaDato" name="txtFechaDato" class="form-control">
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
            <form action="vistaVariablesPorIndicador.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Variables Por Indicador</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home">Datos de Variable Por
                                    Indicador</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="home" class="container tab-pane active"><br>
                                <div class="form-group">
                                    <label>ID</label>
                                    <input type="text" id="edit_txtId" name="txtId" class="form-control"
                                           value="<?php echo $id ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Variable</label>
                                    <input type="text" id="edit_txtfkidvariable" name="txtfkidvariable"
                                           class="form-control" value="<?php echo $fkidvariable ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Indicador </label>
                                    <input type="text" id="edit_txtfkidindicador" name="txtfkidindicador"
                                           class="form-control" value="<?php echo $fkidindicador ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Email/Usuario </label>
                                    <input type="text" id="edit_txtfkemailusuario" name="txtfkemailusuario"
                                           class="form-control" value="<?php echo $fkemailusuario ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Dato</label>
                                    <input type="text" id="edit_txtDato" name="txtDato" class="form-control"
                                           value="<?php echo $dato ?>">
                                </div>
                                <div class="form-group">
                                    <label>Fecha Dato</label>
                                    <input type="date" id="edit_txtFechaDato" name="txtFechaDato" class="form-control"
                                           value="<?php echo $fechadato ?>">
                                </div>
                                <div class="form-group">
                                    <input type="hidden" id="edit_txtIdOriginal" name="txtIdOriginal" value="">
                                    <input type="hidden" id="edit_txtfkidvariableOriginal" name="txtfkidvariableOriginal"
                                           value="">
                                    <input type="hidden" id="edit_txtfkidindicadorOriginal" name="txtfkidindicadorOriginal"
                                           value="">
                                    <input type="hidden" id="edit_txtfkemailusuarioOriginal" name="txtfkemailusuarioOriginal"
                                           value="">
                                    <?php if ($esAdmin || $esValidador): ?>
                                        <input type="submit" id="btnModificar" name="bt" class="btn btn-warning"
                                               value="Modificar">
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
            <form action="vistaVariablesPorIndicador.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Variables Por Indicador</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home">Datos de Variables Por
                                    Indicador</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="home" class="container tab-pane active"><br>
                                <div class="form-group">
                                    <label>ID</label>
                                    <input type="text" id="delete_txtId" name="txtId" class="form-control"
                                           value="<?php echo $id ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Variable</label>
                                    <input type="text" id="delete_txtfkidvariable" name="txtfkidvariable"
                                           class="form-control" value="<?php echo $fkidvariable ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Indicador </label>
                                    <input type="text" id="delete_txtfkidindicador" name="txtfkidindicador"
                                           class="form-control" value="<?php echo $fkidindicador ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Email/Usuario </label>
                                    <input type="text" id="delete_txtfkemailusuario" name="txtfkemailusuario"
                                           class="form-control" value="<?php echo $fkemailusuario ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Dato</label>
                                    <input type="text" id="delete_txtDato" name="txtDato" class="form-control"
                                           value="<?php echo $dato ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Fecha Dato</label>
                                    <input type="date" id="delete_txtFechaDato" name="txtFechaDato" class="form-control"
                                           value="<?php echo $fechadato ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <?php if ($esAdmin): ?>
                                        <input type="submit" id="btnBorrar" name="bt" class="btn btn-danger"
                                               value="Borrar">
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

<script>
    $('#editar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var fkidvariable = button.data('fkidvariable');
        var fkidindicador = button.data('fkidindicador');
        var fkemailusuario = button.data('fkemailusuario');
        var dato = button.data('dato');
        var fechadato = button.data('fechadato');
        var modal = $(this);
        modal.find('#edit_txtId').val(id);
        modal.find('#edit_txtfkidvariable').val(fkidvariable);
        modal.find('#edit_txtfkidindicador').val(fkidindicador);
        modal.find('#edit_txtfkemailusuario').val(fkemailusuario);
        modal.find('#edit_txtDato').val(dato);
        modal.find('#edit_txtFechaDato').val(fechadato);
        modal.find('#edit_txtIdOriginal').val(id);
        modal.find('#edit_txtfkidvariableOriginal').val(fkidvariable);
        modal.find('#edit_txtfkidindicadorOriginal').val(fkidindicador);
        modal.find('#edit_txtfkemailusuarioOriginal').val(fkemailusuario);
    });

    $('#borrar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var fkidvariable = button.data('fkidvariable');
        var fkidindicador = button.data('fkidindicador');
        var fkemailusuario = button.data('fkemailusuario');
        var dato = button.data('dato');
        var fechadato = button.data('fechadato');
        var modal = $(this);
        modal.find('#delete_txtId').val(id);
        modal.find('#delete_txtfkidvariable').val(fkidvariable);
        modal.find('#delete_txtfkidindicador').val(fkidindicador);
        modal.find('#delete_txtfkemailusuario').val(fkemailusuario);
        modal.find('#delete_txtDato').val(dato);
        modal.find('#delete_txtFechaDato').val(fechadato);
    });
</script>