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
if ($_SESSION['email'] == null)
    header('Location: ../index.php');

$permisoParaEntrar = false;
$listaRolesDelUsuario = $_SESSION['listaRolesDelUsuario'];
$vistaActual = "TipoIndicador"; // Asegurar que la vista actual está correctamente definida
foreach ($listaRolesDelUsuario as $rol) {
    if (esAdmin($listaRolesDelUsuario)) {
        $permisoParaEntrar = true;
        break;
    } elseif (esVerificador($listaRolesDelUsuario) && $vistaActual != "Roles" && $vistaActual != "Usuarios") {
        $permisoParaEntrar = true;
        break;
    } elseif (esValidador($listaRolesDelUsuario) && $vistaActual != "Roles" && $vistaActual != "Usuarios") {
        $permisoParaEntrar = true;
        break;
    }
}
if (!$permisoParaEntrar)
    header('Location: ../vista/menu.php');
?>
<?php
$arregloTipoIndicadorConsulta = [];

$objControlTipoIndicador = new ControlEntidad('tipoindicador');
$arregloTipoIndicador = $objControlTipoIndicador->listar();
//var_dump($arregloRoles);

//$boton = "";
//if (isset($_POST['bt'])) $boton = $_POST['bt']; //En PHP 5.x

//$boton = isset($_POST['bt']) ? $_POST['bt'] : ""; //En PHP 7

$boton = $_POST['bt'] ?? ''; // Captura el valor del botón
$id = $_POST['txtId'] ?? ''; // Captura el email del formulario
$nombre = $_POST['txtNombre'] ?? ''; // Captura la contraseña del formulario
$listbox1 = $_POST['listbox1'] ?? []; // Captura los roles seleccionados

switch ($boton) {
    case 'Guardar':
        if (esAdmin($listaRolesDelUsuario)) {
            // Se debería llamar a un procedimiento almacenado con control de transacciones
            //para guardar en las dos tablas 
            $datosTipoIndicador = ['id' => $id, 'nombre' => $nombre];
            $objTipoIndicador = new Entidad($datosTipoIndicador);
            $objControlTipoIndicador = new ControlEntidad('tipoindicador');
            $objTipoIndicador->guardar($objTipoIndicador);
            header('Location: vistaTipoIndicador.php');
        } else {
            echo "<script>alert('No tienes permiso para Guardar.'); window.location.href='vistaTipoIndicador.php';</script>";
        }
        break;

    case 'Consultar':
        if (esAdmin($listaRolesDelUsuario) || (esVerificador($listaRolesDelUsuario) && $vistaActual != "Roles" && $vistaActual != "Usuarios") || (esValidador($listaRolesDelUsuario) && $vistaActual != "Roles" && $vistaActual != "Usuarios")) {
            $datosTipoIndicador = ['id' => $id];
            $objTipoIndicador = new Entidad($datosTipoIndicador);
            $objControlTipoIndicador = new ControlEntidad('tipoindicador');
            $objTipoIndicador = $objControlTipoIndicador->buscarPorId('id', $id);
            if ($objTipoIndicador !== null) {
                $nombre = $objTipoIndicador->__get('nombre');
            } else {
                // Manejar el caso en que $objUsuario es nulo
                echo "El Tipo Indicador no se encontró."; // Mejorar el mensaje
            }
        } else {
            echo "<script>alert('No tienes permiso para Consultar.'); window.location.href='vistaTipoIndicador.php';</script>";
        }
        break;
    case 'Modificar':
        if (esAdmin($listaRolesDelUsuario) || (esValidador($listaRolesDelUsuario) && $vistaActual != "Roles" && $vistaActual != "Usuarios")) {
            // Se debería llamar a un procedimiento almacenado con control de transacciones
            //para modificar en las dos tablas
            //1. modifica en tabla principal    
            $datosTipoIndicador = ['id' => $id, 'nombre' => $nombre];
            $objTipoIndicador = new Entidad($datosTipoIndicador);
            $objControlTipoIndicador = new ControlEntidad('tipoindicador');
            $objControlTipoIndicador->modificar('id', $id, $objTipoIndicador);
            header('Location: vistaTipoIndicador.php');
        } else {
            echo "<script>alert('No tienes permiso para Modificar.'); window.location.href='vistaTipoIndicador.php';</script>";
        }
        break;
    case 'Borrar':
        if (esAdmin($listaRolesDelUsuario)) {
            $datosTipoIndicador = ['id' => $id];
            $objTipoIndicador = new Entidad($datosTipoIndicador);
            $objControlTipoIndicador = new ControlEntidad('tipoindicador');
            $objControlTipoIndicador->borrar('id', $id);
            header('Location: vistaTipoIndicador.php');
        } else {
            echo "<script>alert('No tienes permiso para Borrar.'); window.location.href='vistaTipoIndicador.php';</script>";
        }
        break;

    default:
        // Lógica por defecto, si es necesaria
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
                        <?php if (esAdmin($listaRolesDelUsuario)): ?>
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
                                <?php if (esAdmin($listaRolesDelUsuario) || (esValidador($listaRolesDelUsuario) && $vistaActual != "Roles" && $vistaActual != "Usuarios")): ?>
                                    <a href="#editar" class="edit" data-toggle="modal"><i class="material-icons"
                                            data-toggle="tooltip" title="Edit">&#xE254;</i></a>
                                <?php endif; ?>
                                <?php if (esAdmin($listaRolesDelUsuario)): ?>
                                    <a href="#borrar" class="delete" data-toggle="modal"><i class="material-icons"
                                            data-toggle="tooltip" title="Delete">&#xE872;</i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

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
                                    <input type="text" id="txtId" name="txtId" class="form-control"
                                        value="<?php echo $id ?>">
                                </div>
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" id="txtNombre" name="txtNombre" class="form-control"
                                        value="<?php echo $nombre ?>">
                                </div>
                                <div class="form-group">
                                    <input type="submit" id="btnGuardar" name="bt" class="btn btn-success" value="Guardar"
                                        <?php if (!esAdmin($listaRolesDelUsuario)) echo "disabled"; ?>>
                                    <input type="submit" id="btnConsultar" name="bt" class="btn btn-success"
                                        value="Consultar">
                                    <input type="submit" id="btnModificar" name="bt" class="btn btn-warning"
                                        value="Modificar"
                                        <?php if (!esAdmin($listaRolesDelUsuario) && !esValidador($listaRolesDelUsuario)) echo "disabled"; ?>>
                                    <input type="submit" id="btnBorrar" name="bt" class="btn btn-warning" value="Borrar"
                                        <?php if (!esAdmin($listaRolesDelUsuario)) echo "disabled"; ?>>
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
                                    <input type="text" id="txtId" name="txtId" class="form-control"
                                        value="<?php echo $id ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" id="txtNombre" name="txtNombre" class="form-control"
                                        value="<?php echo $nombre ?>">
                                </div>
                                <div class="form-group">
                                    <input type="submit" id="btnModificar" name="bt" class="btn btn-warning"
                                        value="Modificar"
                                        <?php if (!esAdmin($listaRolesDelUsuario) && !esValidador($listaRolesDelUsuario)) echo "disabled"; ?>>
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
                                    <input type="text" id="txtId" name="txtId" class="form-control"
                                        value="<?php echo $id ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" id="txtNombre" name="txtNombre" class="form-control"
                                        value="<?php echo $nombre ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <input type="submit" id="btnBorrar" name="bt" class="btn btn-warning" value="Borrar"
                                        <?php if (!esAdmin($listaRolesDelUsuario)) echo "disabled"; ?>>
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