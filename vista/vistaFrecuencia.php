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

if (!$permisoParaEntrar)
    header('Location: ../vista/menu.php');

?>
<?php
$arregloFrecuencia=[];

$objControlFrecuencia = new ControlEntidad('frecuencia');
$arregloFrecuencia = $objControlFrecuencia->listar();
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
		// Se debería llamar a un procedimiento almacenado con control de transacciones
		//para guardar en las dos tablas 
		$datosFrecuencia = ['id' => $id, 'nombre' => $nombre];
		$objFrecuencia= new Entidad($datosFrecuencia);
		$objControlFrecuencia = new ControlEntidad('frecuencia');
		$objControlFrecuencia->guardar($objFrecuencia);
		header('Location: vistaFrecuencia.php');
		break;

     case 'Consultar':
        $datosFrecuencia = ['id' => $id];
        $objFrecuencia = new Entidad($datosFrecuencia);
        $objControlFrecuencia = new ControlEntidad('frecuencia');
        $objFrecuencia = $objControlFrecuencia->buscarPorId('id', $id);
        if ($objFrecuencia !== null) {
            $nombre = $objFrecuencia->__get('nombre');
        } else {
            echo "La Representacion Visual no se encontró.";
        }
        break;
    case 'Modificar':
		// Se debería llamar a un procedimiento almacenado con control de transacciones
		//para modificar en las dos tablas
		//1. modifica en tabla principal    
        $datosFrecuencia = ['id' => $id, 'nombre' => $nombre];
        $objFrecuencia=new Entidad($datosFrecuencia);
        $objControlFrecuencia = new ControlEntidad('frecuencia');
        $objControlFrecuencia->modificar(['id'], [$id], $objFrecuencia);
		header('Location: vistaFrecuencia.php');
        break;
    case 'Borrar':
        $datosFrecuencia=['id' => $id];
        $objFrecuencia = new Entidad($datosFrecuencia);
        $objControlFrecuencia= new ControlEntidad('frecuencia');
        $objControlFrecuencia->borrar('id', $id);
		header('Location: vistaFrecuencia.php');
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
						<h2 class="miEstilo">Gestión <b>Frecuencia</b></h2>
					</div>
					<div class="col-sm-6"><?php if ($esAdmin or $esValidador or $esVerificador): ?>
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
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($arregloFrecuencia as $i):?>
						<tr>
							<td>
								<span class="custom-checkbox">
									<input type="checkbox" id="checkbox1" name="options[]" value="1">
									<label for="checkbox1"></label>
								</span>
							</td>
							<td><?php echo $i->__get('id');?></td>
							<td><?php echo $i->__get('nombre');?></td>
							<td>
								 <?php if ($esAdmin or $esValidador): ?>
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


<?php if ($esAdmin or $esValidador or $esVerificador): ?>
    <div id="crudModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="vistaFrecuencia.php" method="post">
                    <div class="modal-header">
                        <h4 class="modal-title">Frecuencia</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#home">Datos</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="home" class="container tab-pane active"><br>
                                  <div class="form-group">
                                        <label>Id</label>
                                        <input type="text" id="txtId" name="txtId" class="form-control" value="<?php echo $id ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" id="txtNombre" name="txtNombre" class="form-control" value="<?php echo $nombre ?>">
                                    </div>
                                     <?php if ($esAdmin): ?>
                                    <div class="form-group">
                                        <input type="submit" id="btnGuardar" name="bt" class="btn btn-success" value="Guardar">
                                      <?php endif; ?>  
                                        <input type="submit" id="btnConsultar" name="bt" class="btn btn-success" value="Consultar">
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
                <form action="vistaFrecuencia.php" method="post">
                    <div class="modal-header">
                        <h4 class="modal-title">Frecuencia</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#home">Datos</a>
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
                <form action="vistaFrecuencia.php" method="post">
                    <div class="modal-header">
                        <h4 class="modal-title">Tipo Actor</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#home">Datos</a>
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
                                        <input type="text" id="delete_txtNombre" name="txtNombre" class="form-control"
                                            readonly>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" id="btnBorrar" name="bt" class="btn btn-danger"
                                            value="Borrar">
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

