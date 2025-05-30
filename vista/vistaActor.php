<?php
ob_start();
?>
<?php
include '../controlador/configBd.php';
include '../controlador/ControlEntidad.php';
include '../controlador/ControlConexionPdo.php';
include '../modelo/Entidad.php';
include 'funciones_roles.php';
session_start();
if ($_SESSION['email'] == null) {
    header('Location: ../index.php');
    exit();
}

$permisoParaEntrar = false;
$listaRolesDelUsuario = $_SESSION['listaRolesDelUsuario'] ?? [];
$vistaActual = "Actor";

if (esAdmin($listaRolesDelUsuario)) {
    $permisoParaEntrar = true;
} elseif (esVerificador($listaRolesDelUsuario) && $vistaActual != "Roles" && $vistaActual != "Usuarios") {
    $permisoParaEntrar = true;
} elseif (esValidador($listaRolesDelUsuario) && $vistaActual != "Roles" && $vistaActual != "Usuarios") {
    $permisoParaEntrar = true;
}

if (!$permisoParaEntrar) {
    header('Location: ../vista/menu.php');
    exit();
}

$arregloActorConsulta = [];

$objControlActor = new ControlEntidad('actor');
$arregloActor = $objControlActor->listar();

$boton = $_POST['bt'] ?? '';
$id = $_POST['txtId'] ?? '';
$nombre = $_POST['txtNombre'] ?? '';
$fkidtipoactor = $_POST['txtfkidtipoactor'] ?? '';

switch ($boton) {
    case 'Guardar':
        if (esAdmin($listaRolesDelUsuario)) {
            $datosActor = ['id' => $id, 'nombre' => $nombre, 'fkidtipoactor' => $fkidtipoactor];
            $objActor = new Entidad($datosActor);
            $objControlActor = new ControlEntidad('actor');
            try {
                $objControlActor->guardar($objActor);
                header('Location: vistaActor.php');
                exit();
            } catch (Exception $e) {
                echo "<script>alert('Error al Guardar: " . addslashes($e->getMessage()) . "'); window.location.href='vistaActor.php';</script>";
            }
        } else {
            echo "<script>alert('No tienes permiso para Guardar.'); window.location.href='vistaActor.php';</script>";
        }
        break;

    case 'Consultar':
        $datosActor = ['id' => $id];
        $objActor = new Entidad($datosActor);
        $objControlActor = new ControlEntidad('actor');
        $objActor = $objControlActor->buscarPorId('id', $id);
        if ($objActor !== null) {
            $nombre = $objActor->__get('nombre');
            $fkidtipoactor = $objActor->__get('fkidtipoactor');
        } else {
            echo "<script>alert('El Actor con ID " . addslashes($id) . " no se encontró.');</script>";
            $nombre = '';
            $fkidtipoactor = '';
        }
        break;

    case 'Modificar':
        if (esAdmin($listaRolesDelUsuario) || (esValidador($listaRolesDelUsuario) && $vistaActual != "Roles" && $vistaActual != "Usuarios")) {
            $datosActor = ['id' => $id, 'nombre' => $nombre, 'fkidtipoactor' => $fkidtipoactor];
            $objActor = new Entidad($datosActor);
            $objControlActor = new ControlEntidad('actor');
            try {
                $objControlActor->modificar(['id'], [$id], $objActor); 
                header('Location: vistaActor.php');
                exit();
            } catch (Exception $e) {
                echo "<script>alert('Error al Modificar: " . addslashes($e->getMessage()) . "'); window.location.href='vistaActor.php';</script>";
            }
        } else {
            echo "<script>alert('No tienes permiso para Modificar.'); window.location.href='vistaActor.php';</script>";
        }
        break;

    case 'Borrar':
        if (esAdmin($listaRolesDelUsuario)) {
            $objControlActor = new ControlEntidad('actor');
            try {
                 $objControlActor = new ControlEntidad('actor');
        $objControlActor->borrar(['id'], [$id]); // Ajusta esta llamada según la firma real de tu método borrar
                header('Location: vistaActor.php');
                exit();
            } catch (Exception $e) {
                echo "<script>alert('Error al Borrar: " . addslashes($e->getMessage()) . "'); window.location.href='vistaActor.php';</script>";
            }
        } else {
            echo "<script>alert('No tienes permiso para Borrar.'); window.location.href='vistaActor.php';</script>";
        }
        break;

    default:
        // Lógica por defecto, si es necesaria (ej. cuando la página carga por primera vez)
        break;
}

$arregloTipoActoresConsulta = [];
$objcontrolTipoActores = new ControlEntidad('tipoactor');
$arregloTipoActores = $objcontrolTipoActores->listar();
$mapTipoActores = [];
foreach ($arregloTipoActores as $tac) {
    $mapTipoActores[$tac->__get('id')] = $tac->__get('nombre');
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
                        <h2 class="miEstilo">Gestión <b>Actor</b></h2>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        if (esAdmin($listaRolesDelUsuario) || esValidador($listaRolesDelUsuario) || esVerificador($listaRolesDelUsuario)):
                        ?>
                            <a href="#crudModal" class="btn btn-primary" data-toggle="modal"><i class="material-icons">&#xE84E;</i> <span>Gestión Actor</span></a>
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
                        <th>Tipo Actor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($arregloActor as $i):
                    ?>
                        <tr>
                            <td>
                                <span class="custom-checkbox">
                                    <input type="checkbox" id="checkbox<?php echo $i->__get('id'); ?>" name="options[]" value="<?php echo $i->__get('id'); ?>">
                                    <label for="checkbox<?php echo $i->__get('id'); ?>"></label>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($i->__get('id')); ?></td>
                            <td><?php echo htmlspecialchars($i->__get('nombre')); ?></td>
                            <td><?php echo htmlspecialchars($mapTipoActores[$i->__get('fkidtipoactor')] ?? 'Desconocido'); ?></td>
                            <td>
                                <?php if (esAdmin($listaRolesDelUsuario) || (esValidador($listaRolesDelUsuario) && $vistaActual != "Roles" && $vistaActual != "Usuarios")): ?>
                                    <a href="#editar" class="edit" data-toggle="modal"
                                       data-id="<?php echo htmlspecialchars($i->__get('id')); ?>"
                                       data-nombre="<?php echo htmlspecialchars($i->__get('nombre')); ?>"
                                       data-fkidtipoactor="<?php echo htmlspecialchars($i->__get('fkidtipoactor')); ?>">
                                        <i class="material-icons" data-toggle="tooltip" title="Editar">&#xE254;</i>
                                    </a>
                                <?php endif; ?>
                                <?php if (esAdmin($listaRolesDelUsuario)): ?>
                                    <a href="#borrar" class="delete" data-toggle="modal"
                                       data-id="<?php echo htmlspecialchars($i->__get('id')); ?>">
                                        <i class="material-icons" data-toggle="tooltip" title="Borrar">&#xE872;</i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="clearfix">
                <div class="hint-text">Mostrando <b><?php echo count($arregloActor) ?></b> de <b><?php echo count($arregloActor) ?></b> entradas</div>
                <ul class="pagination">
                    <li class="page-item disabled"><a href="#">Anterior</a></li>
                    <li class="page-item active"><a href="#" class="page-link">1</a></li>
                    <li class="page-item"><a href="#" class="page-link">Siguiente</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="crudModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="vistaActor.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Gestión de Actor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home">Datos de Actor</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="home" class="container tab-pane active"><br>
                                <div class="form-group">
                                    <label>Id</label>
                                    <input type="text" id="txtId" name="txtId" class="form-control" value="<?php echo htmlspecialchars($id); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" id="txtNombre" name="txtNombre" class="form-control" value="<?php echo htmlspecialchars($nombre); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Tipo Actor</label>
                                    <Select id="txtfkidtipoactor" name="txtfkidtipoactor" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php foreach ($mapTipoActores as $tipoId => $tipoNombre): ?>
                                            <option value="<?php echo htmlspecialchars($tipoId); ?>" <?php echo ($tipoId == $fkidtipoactor) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($tipoNombre); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </Select>
                                </div>
                                <div class="form-group">
                                    <?php if (esAdmin($listaRolesDelUsuario)): ?>
                                        <input type="submit" id="btnGuardar" name="bt" class="btn btn-success" value="Guardar">
                                    <?php endif; ?>
                                    <input type="submit" id="btnConsultar" name="bt" class="btn btn-primary" value="Consultar">
                                </div>
                            </div>
                            <div id="menu1" class="container tab-pane fade"><br></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editar" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="vistaActor.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Actor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#editHome">Datos de Actor</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="editHome" class="container tab-pane active"><br>
                                <div class="form-group">
                                    <label>Id</label>
                                    <input type="text" id="edit_txtId" name="txtId" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" id="edit_txtNombre" name="txtNombre" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Tipo Actor</label>
                                    <Select id="edit_txtfkidtipoactor" name="txtfkidtipoactor" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php foreach ($mapTipoActores as $tipoId => $tipoNombre): ?>
                                            <option value="<?php echo htmlspecialchars($tipoId); ?>">
                                                <?php echo htmlspecialchars($tipoNombre); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </Select>
                                </div>
                                <div class="form-group">
                                    <?php if (esAdmin($listaRolesDelUsuario) || (esValidador($listaRolesDelUsuario) && $vistaActual != "Roles" && $vistaActual != "Usuarios")): ?>
                                        <input type="submit" id="btnModificar" name="bt" class="btn btn-warning" value="Modificar">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                </div>
            </form>
        </div>
    </div>
</div>

<div id="borrar" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="vistaActor.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Borrar Actor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar este registro?</p>
                    <p class="text-warning"><small>Esta acción no se puede deshacer.</small></p>
                    <input type="hidden" id="delete_txtId" name="txtId">
                    <input type="hidden" name="txtNombre" value="">
                    <input type="hidden" name="txtfkidtipoactor" value="">
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                    <?php if (esAdmin($listaRolesDelUsuario)): ?>
                        <input type="submit" name="bt" class="btn btn-danger" value="Borrar">
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Script para rellenar el modal de Editar
    $('#editar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Botón que activó el modal
        var id = button.data('id');
        var nombre = button.data('nombre');
        var fkidtipoactor = button.data('fkidtipoactor');
        var modal = $(this);
        modal.find('#edit_txtId').val(id);
        modal.find('#edit_txtNombre').val(nombre);
        modal.find('#edit_txtfkidtipoactor').val(fkidtipoactor);
    });

    // Script para rellenar el modal de Borrar
    $('#borrar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var modal = $(this);
        modal.find('#delete_txtId').val(id);
    });
</script>



<?php
ob_end_flush();
?>