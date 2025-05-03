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

	$permisoParaEntrar=false;
	$listaRolesDelUsuario=$_SESSION['listaRolesDelUsuario'];
	for($i=0;$i<count($listaRolesDelUsuario);$i++){
		if($listaRolesDelUsuario[$i]->__get('nombre')=="admin" || $listaRolesDelUsuario[$i]->__get('nombre')=="Verificador")$permisoParaEntrar=true;
	}
	if(!$permisoParaEntrar)header('Location: ../vista/menu.php');

?>
<?php
$arregloActorConsulta=[];

$objControlActor = new ControlEntidad('actor');
$arregloActor = $objControlActor->listar();
//var_dump($arregloRoles);

//$boton = "";
//if (isset($_POST['bt'])) $boton = $_POST['bt']; //En PHP 5.x

//$boton = isset($_POST['bt']) ? $_POST['bt'] : ""; //En PHP 7

$boton = $_POST['bt'] ?? ''; // Captura el valor del botón
$id = $_POST['txtId'] ?? ''; // Captura el email del formulario
$nombre = $_POST['txtNombre'] ?? ''; // Captura la contraseña del formulario
$fkidtipoactor = $_POST['txtfkidtipoactor'] ?? '';
$listbox1 = $_POST['listbox1'] ?? []; // Captura los roles seleccionados

switch ($boton) {
    case 'Guardar':
		// Se debería llamar a un procedimiento almacenado con control de transacciones
		//para guardar en las dos tablas 
		$datosActor = ['id' => $id, 'nombre' => $nombre, 'fkidtipoactor' => $fkidtipoactor];
		$objActor= new Entidad($datosActor);
		$objControlActor = new ControlEntidad('actor');
		$objControlActor->guardar($objActor);
		header('Location: vistaActor.php');
		break;

    case 'Consultar':
		$datosActor=['id' => $id];
		$objActor = new Entidad($datosActor); 
		$objControlActor = new ControlEntidad('actor');
		$objActor = $objControlActor->buscarPorId('id', $id);
		if ($objActor !== null) {
			$nombre = $objActor->__get('nombre');
		} else {
			// Manejar el caso en que $objUsuario es nulo
			echo "El usuario no se encontró.";
		}
		break;
    case 'Modificar':
		// Se debería llamar a un procedimiento almacenado con control de transacciones
		//para modificar en las dos tablas
		//1. modifica en tabla principal    
        $datosActor = ['id' => $id, 'nombre' => $nombre, 'fkidtipoactor' => $fkidtipoactor];
        $objActor=new Entidad($datosActor);
        $objControlActor = new ControlEntidad('actor');
        $objControlActor->modificar('id', $id, $objActor);
		header('Location: vistaActor.php');
        break;
    case 'Borrar':
        $datosActor=['id' => $id, 'nombre' => $nombre, 'fkidtipoactor' => $fkidtipoactor];
        $objActor = new Entidad($datosActor);
        $objControlActor->borrar('id', $id, 'nombre', $nombre, 'fkidtipoactor', $fkidtipoactor);
		header('Location: vistaActor.php');
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
						<h2 class="miEstilo">Gestión <b>Actor</b></h2>
					</div>
					<div class="col-sm-6">
						<a href="#crudModal" class="btn btn-primary" data-toggle="modal"><i class="material-icons">&#xE84E;</i> <span>Gestión Actor</span></a>
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
                        <th>ID tipo Actor</th>
                        <th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for($i = 0; $i < count($arregloActor); $i++){
					?>
						<tr>
							<td>
								<span class="custom-checkbox">
									<input type="checkbox" id="checkbox1" name="options[]" value="1">
									<label for="checkbox1"></label>
								</span>
							</td>
							<td><?php echo $arregloActor[$i]->__get('id');?></td>
							<td><?php echo $arregloActor[$i]->__get('nombre');?></td>
                            <td><?php echo $arregloActor[$i]->__get('fkidtipoactor');?></td>
							<td>
								<a href="#editar" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip">&#xE254;</i></a>
								<a href="#borrar" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip">&#xE872;</i></a>
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
			<form action="vistaActor.php" method="post">
				<div class="modal-header">						
					<h4 class="modal-title">Actor</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de Actor</a>
							</li>
						</ul>
						<!-- Tab panes -->
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
                                <div class="form-group">
									<label>fkidtipoactor</label>
									<input type="text" id="txtfkidtipoactor" name="txtfkidtipoactor" class="form-control" value="<?php echo $fkidtipoactor ?>">
								</div>
								<div class="form-group">
									<input type="submit" id="btnGuardar" name="bt" class="btn btn-success" value="Guardar">
									<input type="submit" id="btnConsultar" name="bt" class="btn btn-success" value="Consultar">
									<input type="submit" id="btnModificar" name="bt" class="btn btn-warning" value="Modificar">
									<input type="submit" id="btnBorrar" name="bt" class="btn btn-warning" value="Borrar">
								</div>
							</div>
							<div id="menu1" class="container tab-pane fade"><br>

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
			<form action="vistaActor.php" method="post">
				<div class="modal-header">						
					<h4 class="modal-title">Actor</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de Actor</a>
							</li>
						</ul>
						<!-- Tab panes -->
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
                                <div class="form-group">
									<label>fkidtipoactor</label>
									<input type="text" id="txtfkidtipoactor" name="txtfkidtipoactor" class="form-control" value="<?php echo $fkidtipoactor ?>">
								</div>
								<div class="form-group">
									<input type="submit" id="btnModificar" name="bt" class="btn btn-warning" value="Modificar">
								</div>
							</div>
							<div id="menu1" class="container tab-pane fade"><br>

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
			<form action="vistaActor.php" method="post">
				<div class="modal-header">						
					<h4 class="modal-title">Actor</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de Actor</a>
							</li>
						</ul>
						<!-- Tab panes -->
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
                                <div class="form-group">
									<label>fkidtipoactor</label>
									<input type="text" id="txtfkidtipoactor" name="txtfkidtipoactor" class="form-control" value="<?php echo $fkidtipoactor ?>">
								</div>
								<div class="form-group">
									<input type="submit" id="btnGuardar" name="bt" class="btn btn-success" value="Guardar">
									<input type="submit" id="btnConsultar" name="bt" class="btn btn-success" value="Consultar">
									<input type="submit" id="btnModificar" name="bt" class="btn btn-warning" value="Modificar">
									<input type="submit" id="btnBorrar" name="bt" class="btn btn-warning" value="Borrar">
								</div>
							</div>
							<div id="menu1" class="container tab-pane fade"><br>

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