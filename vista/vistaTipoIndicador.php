<?php
ob_start();
include '../controlador/configBd.php';
include '../controlador/ControlEntidad.php';
include '../controlador/ControlConexionPdo.php';
include '../modelo/Entidad.php';
include 'funciones_roles.php';
session_start();
if ($_SESSION['email'] == null)
    header('Location: ../index.php');

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

if (!$permisoParaEntrar)
    header('Location: ../vista/menu.php');

$arregloTipoIndicadorConsulta = [];

$objControlTipoIndicador = new ControlEntidad('tipoindicador');
$arregloTipoIndicador = $objControlTipoIndicador->listar();

$boton = $_POST['bt'] ?? '';
$id = $_POST['txtId'] ?? '';
$nombre = $_POST['txtNombre'] ?? '';

switch ($boton) {
    case 'Guardar':
        if ($esAdmin) {
            $datosTipoIndicador = ['id' => $id, 'nombre' => $nombre];
            $objTipoIndicador = new Entidad($datosTipoIndicador);
            $objControlTipoIndicador = new ControlEntidad('tipoindicador');
            $objControlTipoIndicador->guardar($objTipoIndicador);
            header('Location: vistaTipoIndicador.php');
        } else {
            echo "<script>alert('No tienes permiso para Guardar.'); window.location.href='vistaTipoIndicador.php';</script>";
        }
        break;

    case 'Consultar':
        $datosTipoIndicador = ['id' => $id];
        $objTipoIndicador = new Entidad($datosTipoIndicador);
        $objControlTipoIndicador = new ControlEntidad('tipoindicador');
        $objTipoIndicador = $objControlTipoIndicador->buscarPorId('id', $id);
        if ($objTipoIndicador !== null) {
            $nombre = $objTipoIndicador->__get('nombre');
        } else {
            echo "El Tipo Indicador no se encontró.";
        }
        break;

    case 'Modificar':
        if ($esAdmin || $esValidador) {
            $datosTipoIndicador = ['id' => $id, 'nombre' => $nombre];
            $objTipoIndicador = new Entidad($datosTipoIndicador);
            $objControlTipoIndicador = new ControlEntidad('tipoindicador');
            //  CORREGIDO: Pasar un array asociativo con la clave primaria y su valor
            $objControlTipoIndicador->modificar(['id'], [$id], $objTipoIndicador);
            header('Location: vistaTipoIndicador.php');
        } else {
            echo "<script>alert('No tienes permiso para Modificar.'); window.location.href='vistaTipoIndicador.php';</script>";
        }
        break;

    case 'Borrar':
        if ($esAdmin) {
            $objControlTipoIndicador = new ControlEntidad('tipoindicador');
            // Corregido: borrar espera un array de valores para la clave primaria
            $objControlTipoIndicador->borrar(['id'], [$id]);
            header('Location: vistaTipoIndicador.php');
        } else {
            echo "<script>alert('No tienes permiso para Borrar.'); window.location.href='vistaTipoIndicador.php';</script>";
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
                        <h2 class="miEstilo">Gestión <b>Tipo Indicador</b></h2>
                    </div>
                    <div class="col-sm-6">
                        <?php if ($esAdmin): ?>
                            <a href="#crudModal" class="btn btn-primary" data-toggle="modal"><i
                                    class="material-icons">&#xE84E;</i> <span>Gestión Tipo Indicador</span></a>
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
                        <th>Nombre</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($arregloTipoIndicador as $i): ?>
                        <tr>
                            <td>
                                <span class="custom-checkbox">
                                    <input type="checkbox" id="checkbox1" name="options[]" value="1">
                                    <label for="checkbox1"></label>
                                </span>
                            </td>
                            <td><?php echo $i->__get('id'); ?></td>
                            <td><?php echo $i->__get('nombre'); ?></td>
                            <td>
                                <?php if ($esAdmin || $esValidador): ?>
                                    <a href="#editar" class="edit" data-toggle="modal"
                                       data-id="<?= $i->__get('id') ?>"
                                       data-nombre="<?= $i->__get('nombre') ?>">
                                        <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($esAdmin): ?>
                                    <a href="#borrar" class="delete" data-toggle="modal"
                                       data-id="<?= $i->__get('id') ?>"
                                       data-nombre="<?= $i->__get('nombre') ?>">
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

<?php if ($esAdmin): ?>
    <div id="crudModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="vistaTipoIndicador.php" method="post">
                    <div class="modal-header">
                        <h4 class="modal-title">Tipo Indicador</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#home">Datos de Tipo Indicador</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="home" class="container tab-pane active"><br>
                                    <div class="form-group">
                                        <label>Id</label>
                                        <input type="text" id="txtId" name="txtId" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" id="txtNombre" name="txtNombre" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" id="btnGuardar" name="bt" class="btn btn-success" value="Guardar">
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
<?php endif; ?>


<?php if ($esAdmin || $esValidador): ?>
    <div id="editar" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="vistaTipoIndicador.php" method="post">
                    <div class="modal-header">
                        <h4 class="modal-title">Tipo Indicador</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#home">Datos de Tipo Indicador</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="home" class="container tab-pane active"><br>
                                    <div class="form-group">
                                        <label>Id</label>
                                        <input type="text" id="edit_txtId" name="txtId" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" id="edit_txtNombre" name="txtNombre" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" id="btnModificar" name="bt" class="btn btn-warning"
                                            value="Modificar">
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
<?php endif; ?>

<?php if ($esAdmin): ?>
    <div id="borrar" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="vistaTipoIndicador.php" method="post">
                    <div class="modal-header">
                        <h4 class="modal-title">Tipo Indicador</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#home">Datos de Tipo Indicador</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="home" class="container tab-pane active"><br>
                                    <div class="form-group">
                                        <label>Id</label>
                                        <input type="text" id="delete_txtId" name="txtId" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" id="delete_txtNombre" name="txtNombre" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" id="btnBorrar" name="bt" class="btn btn-warning" value="Borrar">
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
<?php endif; ?>

<script>
    $('#editar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nombre = button.data('nombre');
        var modal = $(this);
        modal.find('#edit_txtId').val(id);
        modal.find('#edit_txtNombre').val(nombre);
    });

    $('#borrar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nombre = button.data('nombre');
        var modal = $(this);
        modal.find('#delete_txtId').val(id);
        modal.find('#delete_txtNombre').val(nombre);
    });
</script>

<?php include "../vista/basePie.html" ?>
<?php
ob_end_flush();
?>