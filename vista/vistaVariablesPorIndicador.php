<?php
ob_start();
include '../controlador/configBd.php';
include '../controlador/ControlEntidad.php';
include '../controlador/ControlConexionPdo.php';
include '../modelo/Entidad.php';
session_start();
if ($_SESSION['email'] == null) header('Location: ../index.php');

$permisoParaEntrar = false;
$listaRolesDelUsuario = $_SESSION['listaRolesDelUsuario'];
for ($i = 0; $i < count($listaRolesDelUsuario); $i++) {
    if ($listaRolesDelUsuario[$i]->__get('nombre') == "admin" || $listaRolesDelUsuario[$i]->__get('nombre') == "Verificador")
        $permisoParaEntrar = true;
}
if (!$permisoParaEntrar) header('Location: ../vista/menu.php');

$objControlVariablesPorIndicador = new ControlEntidad('variablesporindicador');
$arreglo = $objControlVariablesPorIndicador->listar();

$boton = $_POST['bt'] ?? '';
$fkidvariable = $_POST['txtfkidvariable'] ?? '';
$fkidindicador = $_POST['txtfkidindicador'] ?? '';
$fkemailusuario = $_POST['txtfkemailusuario'] ?? '';
$listbox1 = $_POST['listbox1'] ?? []; // Captura los roles seleccionados


switch ($boton) {
    case 'Guardar':
        $datos = ['fkidvariable' => $fkidvariable, 'fkidindicador' => $fkidindicador, 'fkemailusuario' => $fkemailusuario];
        $obj = new Entidad($datos);
        $objControlVariablesPorIndicador->guardar($obj);
        header('Location: vistaVariablesPorIndicador.php');
        break;
    case 'Borrar':
        $datos = ['fkidvariable' => $fkidvariable, 'fkidindicador' => $fkidindicador, 'fkemailusuario' => $fkemailusuario];
        $obj = new Entidad($datos);
        $objControlVariablesPorIndicador->borrar('fkidvariable', $fkidvariable, 'fkidindicador', $fkidindicador, 'fkemailusuario', $fkemailusuario);
        header('Location: vistaVariablesPorIndicador.php');
        break;
}


$arregloVariableConsulta = [];
$objcontrolVariable= new ControlEntidad('variable');
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
                        <a href="#crudModal" class="btn btn-primary" data-toggle="modal">
                            <i class="material-icons">&#xE84E;</i> <span>Gestión V.I</span>
                        </a>
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
                        <th>Id Variable</th>
                        <th>Id Indicador</th>
                        <th>Usuario</th>
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
                            <td><?= $arregloVariables[$item->__get('fkidvariable')] ?? 'Desconocido' ?></td>
                            <td><?= $arregloIndicadores[$item->__get('fkidindicador')] ?? 'Desconocido' ?></td>
                            <td><?= $item->__get('fkemailusuario') ?></td>
                            <td>
                                <a href="#editar" class="edit" data-toggle="modal"><i class="material-icons" data-toggle="tooltip">&#xE254;</i></a>
                                <a href="#borrar" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip">&#xE872;</i></a>
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
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de Variables Por Indicador</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div id="home" class="container tab-pane active"><br>
							<div class="form-group">
								<label>Variable</label>
									<input type="text" id="txtfkidvariable" name="txtfkidvariable" class="form-control" value="<?php echo $fkidvariable ?>">
								</div>
								<div class="form-group">
									<label>Indicador </label>
									<input type="text" id="txtfkidindicador" name="txtfkidindicador" class="form-control" value="<?php echo $fkidindicador ?>">
								</div>
                                <div class="form-group">
									<label>Usuario </label>
									<input type="text" id="txtfkemailusuario" name="txtfkemailusuario" class="form-control" value="<?php echo $fkemailusuario ?>">
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
				<div class="modal-header">						
					<h4 class="modal-title">Variables Por Indicador</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de Variable Por Indicador</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div id="home" class="container tab-pane active"><br>
							<div class="form-group">
                                <label>Variable</label>
									<input type="text" id="txtfkidvariable" name="txtfkidvariable" class="form-control" value="<?php echo $fkidvariable ?>">
								</div>
								<div class="form-group">
								<label>Indicador </label>
									<input type="text" id="txtfkidindicador" name="txtfkidindicador" class="form-control" value="<?php echo $fkidindicador ?>">
								</div>
                                <div class="form-group">
									<label>Email/Usuario </label>
									<input type="text" id="txtfkemailusuario" name="txtfkemailusuario" class="form-control" value="<?php echo $fkemailusuario ?>">
								</div>
								<div class="form-group">
									<input type="submit" id="btnModificar" name="bt" class="btn btn-warning" value="Modificar">
								</div>
							</div>
							<div id="menu2" class="container tab-pane fade"><br>

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
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de Variables Por Indicador</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div id="home" class="container tab-pane active"><br>
							<div class="form-group">
                                <label>Variable</label>
									<input type="text" id="txtfkidvariable" name="txtfkidvariable" class="form-control" value="<?php echo $fkidvariable ?>">
								</div>
								<div class="form-group">
								<label>Indicador </label>
									<input type="text" id="txtfkidindicador" name="txtfkidindicador" class="form-control" value="<?php echo $fkidindicador ?>">
								</div>
                                <div class="form-group">
									<label>Email/Usuario </label>
									<input type="text" id="txtfkemailusuario" name="txtfkemailusuario" class="form-control" value="<?php echo $fkemailusuario ?>">
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