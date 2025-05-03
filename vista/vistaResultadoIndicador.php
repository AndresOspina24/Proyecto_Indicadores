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

$objControlResultadoIndicador = new ControlEntidad('resultadoindicador');
$arreglo = $objControlResultadoIndicador->listar();

$boton = $_POST['bt'] ?? '';
$id = $_POST['txtId'] ?? '';
$fkidindicador = $_POST['txtIndicador'] ?? '';
$resultado = $_POST['txtResultado'] ?? '';
$fechacalculo = $_POST['txtFechaCalculo'] ?? '';

switch ($boton) {
    case 'Guardar':
        $datos = [    'fkidindicador' => $fkidindicador,
        'resultado' => $resultado,
        'fechacalculo' => $fechacalculo];
        $obj = new Entidad($datos);
		$objControlResultadoIndicador = new ControlEntidad('resultadoindicador');
        $objControlResultadoIndicador->guardar($obj);
        header('Location: vistaResultadoIndicador.php');
        break;
    case 'Borrar':
        $datos = ['id'=> $id, 'fkidindicador' => $fkidindicador,
        'resultado' => $resultado,
        'fechacalculo' => $fechacalculo];
        $obj = new Entidad($datos);
        $objControlResultadoIndicador->borrar('id', $id, 'fkidindicador', $fkidindicador, 'resultado', $resultado,'fechacalculo', $fechacalculo);
        header('Location: vistaResultadoIndicador.php');
        break;
}


$arregloIndicadoresConsulta = [];
$objcontrolIndicador = new ControlEntidad('indicador');
$arregloIndicadores = $objcontrolIndicador->listar();
foreach ($arregloIndicadores as $ind) {
    $arregloIndicadores[$ind->__get('id')] = $ind->__get('nombre');
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
                        <h2 class="miEstilo">Gestión <b>Resultado Indicador</b></h2>
                    </div>
                    <div class="col-sm-6">
                        <a href="#crudModal" class="btn btn-primary" data-toggle="modal">
                            <i class="material-icons">&#xE84E;</i> <span>Gestión R.I</span>
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
						<th>ID</th>
						<th>Resultado Indicador</th>
						<th>Fecha</th>
                        <th>Indicador</th>
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
							<td><?= $item->__get('id');  ?></td>
							<td><?= $item->__get('resultado')  ?></td>
							<td><?= $item->__get('fechacalculo')  ?></td>
                            <td><?= $arregloIndicadores[$item->__get('fkidindicador')] ?? 'Desconocido' ?></td>
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
			<form action="vistaResultadoIndicador.php" method="post">
				<div class="modal-header">						
					<h4 class="modal-title">Resultado Indicador</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de Resultado Indicador</a>
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
								<label>Indicador</label>
									<input type="text" id="txtIndicador" name="txtIndicador" class="form-control" value="<?php echo $fkidindicador ?>">
								</div>
                            <div class="form-group">
								<label>Resultado</label>
									<input type="text" id="txtResultado" name="txtResultado" class="form-control" value="">
								</div>
                            <div class="form-group">
								<label>Fecha de Calculo</label>
									<input type="text" id="txtFechaCalculo" name="txtFechaCalculo" class="form-control" value="">
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
					<h4 class="modal-title">ResultadoIndicador</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de ResultadoIndicador</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div id="home" class="container tab-pane active"><br>
							<div class="form-group">
								<label>Indicador</label>
									<input type="text" id="txtId" name="txtId" class="form-control" value="<?php echo $fkidindicador ?>">
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
		<form action="vistaResultadoIndicador.php" method="post">
				<div class="modal-header">						
					<h4 class="modal-title">ResultadoIndicador</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de ResultadoIndicador</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div id="home" class="container tab-pane active"><br>
							<div class="form-group">
								<label>Indicador</label>
									<input type="text" id="txtRepresenVisual" name="txtRepresenVisual" class="form-control" value="<?php echo $fkidindicador ?>">
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
