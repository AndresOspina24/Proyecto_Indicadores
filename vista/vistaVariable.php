<?php
ob_start();
?>
<?php 
    include '../controlador/configBd.php';
    include '../controlador/ControlEntidad.php';
    include '../controlador/ControlConexionPdo.php';
    include '../modelo/Entidad.php';
    session_start();
    if($_SESSION['email']==null)header('Location: ../index.php');

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

$arregloVariableConsulta=[];

$objControlVariable = new ControlEntidad('variable');
$arregloVariable = $objControlVariable->listar();

$boton = $_POST['bt'] ?? '';
$id = $_POST['txtId'] ?? '';
$nombre = $_POST['txtNombre'] ?? '';
$fecha = $_POST['txtFecha'] ?? '';
$fkemailusuario = $_POST['txtfkemailusuario'] ?? '';

switch ($boton) {
    case 'Guardar':
        if ($esAdmin) {
            $datosVariable = ['id' => $id, 'nombre' => $nombre, 'fechacreacion' => $fecha, 'fkemailusuario' => $fkemailusuario];
            $objVariable= new Entidad($datosVariable);
            $objControlVariable = new ControlEntidad('variable');
            try {
                $objControlVariable->guardar($objVariable);
                header('Location: vistaVariable.php');
                exit();
            } catch (Exception $e) {
                echo "<script>alert('Error al Guardar: " . addslashes($e->getMessage()) . "'); window.location.href='vistaVariable.php';</script>";
            }
        } else {
            echo "<script>alert('No tienes permiso para Guardar.'); window.location.href='vistaVariable.php';</script>";
        }
        break;

    case 'Consultar':
        $datosVariable=['id' => $id];
        $objVariable = new Entidad($datosVariable); 
        $objControlVariable = new ControlEntidad('variable');
        $objVariable = $objControlVariable->buscarPorId('id', $id);
        if ($objVariable !== null) {
            $nombre = $objVariable->__get('nombre');
            $fecha = $objVariable->__get('fechacreacion');
            $fkemailusuario =$objVariable->__get('fkemailusuario');
        } else {
            echo "<script>alert('La Variable con ID " . htmlspecialchars($id) . " no se encontró.');</script>";
            $nombre = '';
            $fecha = '';
            $fkemailusuario = '';
        }
        break;

    case 'Modificar':
        if ($esAdmin || $esValidador) {
            $datosVariable = ['id' => $id, 'nombre' => $nombre, 'fechacreacion' => $fecha, 'fkemailusuario' => $fkemailusuario];
            $objVariable=new Entidad($datosVariable);
            $objControlVariable = new ControlEntidad('variable');
            try {
                $objControlVariable->modificar(['id'], [$id], $objVariable);
                header('Location: vistaVariable.php');
                exit();
            } catch (Exception $e) {
                echo "<script>alert('Error al Modificar: " . addslashes($e->getMessage()) . "'); window.location.href='vistaVariable.php';</script>";
            }
        } else {
            echo "<script>alert('No tienes permiso para Modificar.'); window.location.href='vistaVariable.php';</script>";
        }
        break;

    case 'Borrar':
        if ($esAdmin) {
            $objVariable= new ControlEntidad('variable');
        $objControlVariable->borrar(['id'], [$id]);
        header('Location: vistaVariable.php');
        } else {
            echo "<script>alert('No tienes permiso para Borrar.'); window.location.href='vistaVariable.php';</script>";
        }
        break;

    default:
        // Lógica por defecto, si es necesaria
        break;
}

$arregloUsersConsulta = [];
$objcontrolUser = new ControlEntidad('usuario');
$arregloUser = $objcontrolUser->listar();
$mapUsers = [];
foreach ($arregloUser as $u) {
    $mapUsers[$u->__get('email')] = $u->__get('email');
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
                        <h2 class="miEstilo">Gestión <b>Variable</b></h2>
                    </div>
                    <div class="col-sm-6"> 
                        <?php 
                        // Show "Gestión" button if user is Admin OR Verificador
                        if ($esAdmin || $esVerificador or $esValidador): 
                        ?>
                            <a href="#crudModal" class="btn btn-primary" data-toggle="modal">
                                <i class="material-icons">&#xE84E;</i> <span>Gestión </span>
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
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($arregloVariable as $i):
                    ?>
                        <tr>
                            <td>
                                <span class="custom-checkbox">
                                    <input type="checkbox" id="checkbox<?php echo htmlspecialchars($i->__get('id')); ?>" name="options[]" value="<?php echo htmlspecialchars($i->__get('id')); ?>">
                                    <label for="checkbox<?php echo htmlspecialchars($i->__get('id')); ?>"></label>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($i->__get('id'));?></td>
                            <td><?php echo htmlspecialchars($i->__get('nombre'));?></td>
                            <td><?php echo htmlspecialchars($i->__get('fechacreacion'));?></td>
                            <td><?php echo htmlspecialchars($mapUsers[$i->__get('fkemailusuario')] ?? 'Desconocido');?></td>
                            <td>
                                <?php if ($esAdmin || $esValidador): ?>
                                    <a href="#editar" class="edit" data-toggle="modal"
                                       data-id="<?php echo htmlspecialchars($i->__get('id')); ?>"
                                       data-nombre="<?php echo htmlspecialchars($i->__get('nombre')); ?>"
                                       data-fecha="<?php echo htmlspecialchars($i->__get('fechacreacion')); ?>"
                                       data-fkemailusuario="<?php echo htmlspecialchars($i->__get('fkemailusuario')); ?>">
                                        <i class="material-icons" data-toggle="tooltip" title="Editar">&#xE254;</i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($esAdmin): ?>
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
                <div class="hint-text">Mostrando <b><?php echo count($arregloVariable) ?></b> de <b><?php echo count($arregloVariable) ?></b> entradas</div>
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
            <form action="vistaVariable.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Gestión de Variable</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home">Datos de Variable</a>
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
                                    <label>Fecha</label>
                                    <input type="date" id="txtFecha" name="txtFecha" class="form-control" value="<?php echo htmlspecialchars($fecha); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Usuario</label>
                                    <select id="txtfkemailusuario" name="txtfkemailusuario" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php foreach ($mapUsers as $userEmail): ?>
                                            <option value="<?php echo htmlspecialchars($userEmail); ?>" <?php echo ($userEmail == $fkemailusuario) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($userEmail); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <?php if ($esAdmin): ?>
                                        <input type="submit" id="btnGuardar" name="bt" class="btn btn-success" value="Guardar">
                                    <?php endif; ?>
                            
                                   
                                        <input type="submit" id="btnConsultar" name="bt" class="btn btn-primary" value="Consultar">
                                    
                                    <?php if ($esAdmin): ?>
                                        <input type="submit" id="btnBorrar" name="bt" class="btn btn-danger" value="Borrar">
                                    <?php endif; ?>
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
            <form action="vistaVariable.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Variable</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#editHome">Datos de Variable</a>
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
                                    <label>Fecha</label>
                                    <input type="date" id="edit_txtFecha" name="txtFecha" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Usuario</label>
                                    <select id="edit_txtfkemailusuario" name="txtfkemailusuario" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php foreach ($mapUsers as $userEmail): ?>
                                            <option value="<?php echo htmlspecialchars($userEmail); ?>">
                                                <?php echo htmlspecialchars($userEmail); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <?php if ($esAdmin || $esValidador): ?>
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
            <form action="vistaVariable.php" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Borrar Variable</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar este registro?</p>
                    <p class="text-warning"><small>Esta acción no se puede deshacer.</small></p>
                    <input type="hidden" id="delete_txtId" name="txtId">
                    <input type="hidden" name="txtNombre" value="">
                    <input type="hidden" name="txtFecha" value="">
                    <input type="hidden" name="txtfkemailusuario" value="">
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
                    <?php if ($esAdmin): ?>
                        <input type="submit" name="bt" class="btn btn-danger" value="Borrar">
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#editar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nombre = button.data('nombre');
        var fecha = button.data('fecha');
        var fkemailusuario = button.data('fkemailusuario');
        var modal = $(this);
        modal.find('#edit_txtId').val(id);
        modal.find('#edit_txtNombre').val(nombre);
        modal.find('#edit_txtFecha').val(fecha);
        modal.find('#edit_txtfkemailusuario').val(fkemailusuario);
        modal.find('#edit_txtfkemailusuario option').each(function() {
            if ($(this).val() == fkemailusuario) {
                $(this).prop('selected', true);
            } else {
                $(this).prop('selected', false);
            }
        });
    });

    $('#borrar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var modal = $(this);
        modal.find('#delete_txtId').val(id);
    });
</script>

<?php include "../vista/basePie.html" ?>
<?php
ob_end_flush();
?>