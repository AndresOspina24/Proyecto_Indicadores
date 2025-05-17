<?php
ob_start();
?>
<?php
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

?>
<?php
$arregloUnidadMedicionConsulta = [];

$objControlUnidadMedicion = new ControlEntidad('unidadmedicion');
$arregloUnidadMedicion = $objControlUnidadMedicion->listar();

$boton = $_POST['bt'] ?? '';
$id = $_POST['txtId'] ?? '';
$descripcion = $_POST['txtDescripcion'] ?? '';

switch ($boton) {
    case 'Guardar':
        if ($esAdmin) {
            $datosUnidadMedicion = ['id' => $id, 'descripcion' => $descripcion];
            $objUnidadMedicion = new Entidad($datosUnidadMedicion);
            $objControlUnidadMedicion = new ControlEntidad('unidadmedicion');
            $objControlUnidadMedicion->guardar($objUnidadMedicion);
            header('Location: vistaUnidadMedicion.php');
        } else {
            echo "<p class='alert alert-danger'>No tiene permiso para Guardar.</p>";
        }
        break;

    case 'Consultar':
        // Todos los roles permitidos pueden Consultar, no necesitamos validación aquí
        $datosUnidadMedicion = ['id' => $id];
        $objUnidadMedicion = new Entidad($datosUnidadMedicion);
        $objControlUnidadMedicion = new ControlEntidad('unidadmedicion');
        $objControlUnidadMedicion = $objControlUnidadMedicion->buscarPorId('id', $id);
        if ($objUnidadMedicion !== null) {
            $descripcion = $objUnidadMedicion->__get('descripcion');
        } else {
            echo "La unidad de medición no se encontró.";
        }
        break;

    case 'Modificar':
        if ($esAdmin || $esValidador) {
            $datosUnidadMedicion = ['id' => $id, 'descripcion' => $descripcion];
            $objUnidadMedicion = new Entidad($datosUnidadMedicion);
            $objControlUnidadMedicion = new ControlEntidad('unidadmedicion');
            $objControlUnidadMedicion->modificar(['id'], [$id], $objUnidadMedicion); // Modificado para usar arrays
            header('Location: vistaUnidadMedicion.php');
        } else {
            echo "<p class='alert alert-danger'>No tiene permiso para Modificar.</p>";
        }
        break;

    case 'Borrar':
        if ($esAdmin) {
            $datosUnidadMedicion = ['id' => $id];
            $objUnidadMedicion = new Entidad($datosUnidadMedicion);
            $objControlUnidadMedicion = new ControlEntidad('unidadmedicion');
            $objControlUnidadMedicion->borrar(['id'], [$id]); // Modificado para usar arrays
            header('Location: vistaUnidadMedicion.php');
        } else {
            echo "<p class='alert alert-danger'>No tiene permiso para Borrar.</p>";
        }
        break;

    default:
        break;
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
                        <h2 class="miEstilo">Gestión <b>Unidades Medición</b></h2>
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
                        <th>Id</th>
                        <th>Descripcion</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i < count($arregloUnidadMedicion); $i++) {
                    ?>
                        <tr>
                            <td>
                                <span class="custom-checkbox">
                                    <input type="checkbox" id="checkbox1" name="options[]" value="1">
                                    <label for="checkbox1"></label>
                                </span>
                            </td>
                            <td><?php echo $arregloUnidadMedicion[$i]->__get('id'); ?></td>
                            <td><?php echo $arregloUnidadMedicion[$i]->__get('descripcion'); ?></td>
                            <td>
                                <?php if ($esAdmin || $esValidador): ?>
                                    <a href="#editar" class="edit" data-toggle="modal" data-id="<?php echo $arregloUnidadMedicion[$i]->__get('id'); ?>" data-descripcion="<?php echo $arregloUnidadMedicion[$i]->__get('descripcion'); ?>"><i class="material-icons"
                                            data-toggle="tooltip" title="Editar">&#xE254;</i></a>
                                <?php endif; ?>
                                <?php if ($esAdmin): ?>
                                    <a href="#borrar" class="delete" data-toggle="modal" data-id="<?php echo $arregloUnidadMedicion[$i]->__get('id'); ?>"><i class="material-icons"
                                            data-toggle="tooltip" title="Borrar">&#xE872;</i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <div class="clearfix">
                <div class="hint-text">Showing <b>5</b> out of <b>25</b> entries</div>
                <ul class="pagination">
                    <li class="page-item disabled"><a href="#">Previous</a></li>
                    <li class="page-item"><a href="#" class="page-link">1</a></li>
                    <li class="page-item"><a href="#" class="page-link">2</a></li>
                    <li class="page-item active"><a href="#" class="page-link">3</a></li>
                    <li class="page-item"><a href="#" class="page-link">4</a></li>
                    <li class="page-item"><a href="#" class="page-link">5</a></li>
                    <li class="page-item"><a href="#" class="page-link">Next</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="crudModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="vistaUnidadMedicion.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Unidad Medición</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home">Datos de Unidades Medición</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="home" class="container tab-pane active"><br>
                                <div class="form-group">
                                    <label>Id</label>
                                    <input type="text" id="txtId" name="txtId" class="form-control"
                                        value="<?php echo $id ?>" <?php if (!$esAdmin) echo "readonly"; ?>>
                                </div>
                                <div class="form-group">
                                    <label>Descripción </label>
                                    <input type="text" id="txtDescripcion" name="txtDescripcion" class="form-control"
                                        value="<?php echo $descripcion ?>">
                                </div>
                                <div class="form-group">
                                    <?php if ($esAdmin): ?>
                                        <input type="submit" id="btnGuardar" name="bt" class="btn btn-success"
                                            value="Guardar">
                                    <?php endif; ?>
                                    <input type="submit" id="btnConsultar" name="bt" class="btn btn-success"
                                        value="Consultar">
                                    <?php if ($esAdmin || $esValidador): ?>
                                        <input type="submit" id="btnModificar" name="bt" class="btn btn-warning"
                                            value="Modificar">
                                    <?php endif; ?>
                                    <?php if ($esAdmin): ?>
                                        <input type="submit" id="btnBorrar" name="bt" class="btn btn-warning"
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

<div id="editar" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="vistaUnidadMedicion.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Unidad Medicion</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home">Datos de Unidades Medición</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="home" class="container tab-pane active"><br>
                                <div class="form-group">
                                    <label>Id</label>
                                    <input type="text" id="edit_txtId" name="txtId" class="form-control"
                                        value="<?php echo $id ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" id="edit_txtDescripcion" name="txtDescripcion" class="form-control"
                                        value="<?php echo $descripcion ?>">
                                </div>
                                <div class="form-group">
                                    <input type="hidden" id="edit_txtIdOriginal" name="txtIdOriginal" value="">
                                    <?php if ($esAdmin || $esValidador): ?>
                                        <input type="submit" id="btnModificar" name="bt" class="btn btn-warning"
                                            value="Modificar">
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

<div id="borrar" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="vistaUnidadMedicion.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Unidad Medicion</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home">Datos de Unidades Medición</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="home" class="container tab-pane active"><br>
                                <div class="form-group">
                                    <label>Id</label>
                                    <input type="text" id="delete_txtId" name="txtId" class="form-control"
                                        value="<?php echo $id ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" id="delete_txtDescripcion" name="txtDescripcion" class="form-control"
                                        value="<?php echo $descripcion ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <?php if ($esAdmin): ?>
                                        <input type="submit" id="btnBorrar" name="bt" class="btn btn-warning"
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

<?php include "../vista/basePie.html" ?>
<?php
ob_end_flush();
?>

<script>
    $('#editar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var descripcion = button.data('descripcion');
        var modal = $(this);
        modal.find('#edit_txtId').val(id);
        modal.find('#edit_txtDescripcion').val(descripcion);
        modal.find('#edit_txtIdOriginal').val(id); // Importante para la cláusula WHERE
    });

    $('#borrar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var descripcion = button.data('descripcion');
        var modal = $(this);
        modal.find('#delete_txtId').val(id);
        modal.find('#delete_txtDescripcion').val(descripcion);
    });
</script>