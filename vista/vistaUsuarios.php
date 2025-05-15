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
		if($listaRolesDelUsuario[$i]->__get('nombre')=="admin")$permisoParaEntrar=true;
	}
	if(!$permisoParaEntrar)header('Location: ../vista/menu.php');



$arregloRolesConsulta=[];

$objControlUsuario = new ControlEntidad('usuario');
$arregloUsuarios = $objControlUsuario->listar();
//var_dump($arregloUsuarios);

$objControlRol = new ControlEntidad('rol');
$arregloRoles = $objControlRol->listar();
//var_dump($arregloRoles);

//$boton = "";
//if (isset($_POST['bt'])) $boton = $_POST['bt']; //En PHP 5.x

//$boton = isset($_POST['bt']) ? $_POST['bt'] : ""; //En PHP 7

$boton = $_POST['bt'] ?? ''; // Captura el valor del botón
$email = $_POST['txtEmail'] ?? ''; // Captura el email del formulario
$contrasena = $_POST['txtContrasena'] ?? ''; // Captura la contraseña del formulario
$listbox1 = $_POST['listbox1'] ?? []; // Captura los roles seleccionados

switch ($boton) {
    case 'Guardar':
		// Se debería llamar a un procedimiento almacenado con control de transacciones
		//para guardar en las dos tablas 
		$datosUsuario = ['email' => $email, 'contrasena' => $contrasena];
		$objUsuario = new Entidad($datosUsuario);
		$objControlUsuario = new ControlEntidad('usuario');
		$objControlUsuario->guardar($objUsuario);
		if (!empty($listbox1)) {
			for ($i = 0; $i < count($listbox1); $i++) {
				$id = explode(";", $listbox1[$i])[0];
				$datosRolUsuario = ['fkemail' => $email, 'fkidrol' => $id];
				$objRolUsuario = new Entidad($datosRolUsuario);
	
				// Crear un objeto ControlEntidad para la tabla de roles de usuario
				$objControlRolUsuario = new ControlEntidad('rol_usuario');
				
				// Llamar al método guardar con el objeto Entidad
				$objControlRolUsuario->guardar($objRolUsuario);
			}
		}
		header('Location: vistaUsuarios.php');
		break;

    case 'Consultar':
		$datosUsuario=['email' => $email];
		$objUsuario = new Entidad($datosUsuario); 
		$objControlUsuario = new ControlEntidad('usuario');
		$objUsuario = $objControlUsuario->buscarPorId('email', $email);
		
		// Validar si $objUsuario es nulo antes de acceder a sus propiedades
		if ($objUsuario !== null) {
			$contrasena = $objUsuario->__get('contrasena');
			$objControlRolUsuario = new ControlEntidad('rol_usuario');
			$sql = "SELECT rol.id as id, rol.nombre as nombre
					FROM rol_usuario INNER JOIN rol ON rol_usuario.fkidrol = rol.id
					WHERE fkemail = ?";
			$parametros = [$email];
			$arregloRolesConsulta = $objControlRolUsuario->consultar($sql, $parametros);
		} else {
			// Manejar el caso en que $objUsuario es nulo
			echo "El usuario no se encontró.";
		}
		break;
    case 'Modificar':
		// Se debería llamar a un procedimiento almacenado con control de transacciones
		//para modificar en las dos tablas
		//1. modifica en tabla principal    
        $datosUsuario=['email' => $email, 'contrasena' => $contrasena];
        $objUsuario=new Entidad($datosUsuario);
        $objControlUsuario = new ControlEntidad('usuario');
        $objControlUsuario->modificar('email', $email, $objUsuario);

        //2. borrar todos los registros asociados de la tabla principal en la tabla intermedia
        $objControlRolUsuario = new ControlEntidad('rol_usuario');
        $objControlRolUsuario->borrar('fkemail',$email);

        //3. tomar del select múltiple (listbox) y guardar los datos en la tabla intermedia
		if (!empty($listbox1)) {
			for ($i = 0; $i < count($listbox1); $i++) {
				$id = explode(";", $listbox1[$i])[0];
				$datosRolUsuario = ['fkemail' => $email, 'fkidrol' => $id];
				$objRolUsuario = new Entidad($datosRolUsuario);
	
				// Crear un objeto ControlEntidad para la tabla de roles de usuario
				$objControlRolUsuario = new ControlEntidad('rol_usuario');
				
				// Llamar al método guardar con el objeto Entidad
				$objControlRolUsuario->guardar($objRolUsuario);
			}
		}     
        header('Location: vistaUsuarios.php');
        break;
    case 'Borrar':
        $arrUsuario=['email' => $email];
        $objUsuario = new Entidad($arrUsuario);
        $objControlUsuario= new ControlEntidad('usuario');
        $objControlUsuario->borrar('email', $email);
        header('Location: vistaUsuarios.php');
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
						<h2 class="miEstilo">Gestión <b>Usuarios</b></h2>
					</div>
					<div class="col-sm-6">
						<a href="#crudModal" class="btn btn-primary" data-toggle="modal"><i class="material-icons">&#xE84E;</i> <span>Gestión Usuarios</span></a>
						
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
						<th>Email</th>
						<th>Contraseña</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for($i = 0; $i < count($arregloUsuarios); $i++){
					?>
						<tr>
							<td>
								<span class="custom-checkbox">
									<input type="checkbox" id="checkbox1" name="options[]" value="1">
									<label for="checkbox1"></label>
								</span>
							</td>
							<td><?php echo $arregloUsuarios[$i]->__get('email');?></td>
							<td><?php echo $arregloUsuarios[$i]->__get('contrasena');?></td>
							<td>
								<a href="#editEmployeeModal" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
								<a href="#deleteEmployeeModal" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
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
<!-- crud Modal HTML -->
<div id="crudModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="vistaUsuarios.php" method="post">
				<div class="modal-header">						
					<h4 class="modal-title">Usuario</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de usuario</a>
							</li>
							<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#menu1">Roles por usuario</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div id="home" class="container tab-pane active"><br>
							<div class="form-group">
								<label>Email</label>
									<input type="email" id="txtEmail" name="txtEmail" class="form-control" value="<?php echo $email ?>">
								</div>
								<div class="form-group">
									<label>Contraseña</label>
									<input type="text" id="txtContrasena" name="txtContrasena" class="form-control" value="<?php echo $contrasena ?>">
								</div>
								<div class="form-group">
									<input type="submit" id="btnGuardar" name="bt" class="btn btn-success" value="Guardar">
									<input type="submit" id="btnConsultar" name="bt" class="btn btn-success" value="Consultar">
									<input type="submit" id="btnModificar" name="bt" class="btn btn-warning" value="Modificar">
									<input type="submit" id="btnBorrar" name="bt" class="btn btn-warning" value="Borrar">
								</div>
							</div>
							<div id="menu1" class="container tab-pane fade"><br>
							<div class="container">
								<div class="form-group">
									<label for="combobox1">Todos los roles</label>
								<select class="form-control" id="combobox1" name="combobox1">
									<?php for($i=0; $i<count($arregloRoles); $i++){ ?>
									<option value="<?php echo $arregloRoles[$i]->__get('id').";". $arregloRoles[$i]->__get('nombre'); ?>">
										<?php echo $arregloRoles[$i]->__get('id').";". $arregloRoles[$i]->__get('nombre'); ?>
									</option>
									<?php } ?>
								</select>
								<br>
									<label for="listbox1">Roles específicos del usuario</label>
								<select multiple class="form-control" id="listbox1" name="listbox1[]">
									<?php for($i=0;$i<count($arregloRolesConsulta);$i++){ ?>
										<option value="<?php echo $arregloRolesConsulta[$i]->__get('id').";". $arregloRolesConsulta[$i]->__get('nombre'); ?>" >
                                        <?php echo $arregloRolesConsulta[$i]->__get('id').";". $arregloRolesConsulta[$i]->__get('nombre'); ?>
										</option>
									<?php } ?>
								</select>
								</div>
									<div class="form-group">
										<button type="button" id="btnAgregarItem" name="bt" class="btn btn-success" onclick="agregarItem('combobox1', 'listbox1')">Agregar Item</button>
										<button type="button" id="btnRemoverItem" name="bt" class="btn btn-success" onclick="removerItem('listbox1')">Remover Item</button>
									</div>
								</div>
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