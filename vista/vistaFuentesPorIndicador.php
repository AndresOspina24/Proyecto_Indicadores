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

$objControlFuentePorIndicador = new ControlEntidad('fuentesporindicador');
$arreglo = $objControlFuentePorIndicador->listar();

$boton = $_POST['bt'] ?? '';
$fkidfuente = $_POST['txtidFuente'] ?? '';
$fkidindicador = $_POST['txtIndicador'] ?? '';

$boton = $_POST['bt'] ?? '';

switch ($boton) {
    case 'Guardar':
        $datos = ['fkidindicador' => $fkidindicador, 'fkidfuente' => $fkidfuente];
        $obj = new Entidad($datos);
        $objControl = new ControlEntidad('fuentesporindicador');
        $objControl->guardar($obj); 
        break;

    case 'Borrar':
        $datosFuentePorIndicador = ['fkidfuente' => $fkidfuente, 'fkidindicador' => $fkidindicador];
        $objFuentePorIndicador = new Entidad($datosFuentePorIndicador);
        $objControlFuentePorIndicador->borrar('fkidfuente', $fkidfuente, 'fkidindicador', $fkidindicador);
        header('Location: vistaFuentesPorIndicador.php');
        break;
}

$arregloFuentesConsulta = [];
$objcontrolFuente = new ControlEntidad('fuente');
$arregloFuentes = $objcontrolFuente->listar();
foreach ($arregloFuentes as $fuen) {
    $arregloFuentes[$fuen->__get('id')] = $fuen->__get('nombre');
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
                        <h2 class="miEstilo">Gestión <b>FuentesPorIndicador</b></h2>
                    </div>
                    <div class="col-sm-6">
                        <a href="#crudModal" class="btn btn-primary" data-toggle="modal">
                            <i class="material-icons">&#xE84E;</i> <span>Gestión F</span>
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
                        <th>Fuente</th>
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
                            <td><?= $arregloFuentes[$item->__get('fkidfuente')] ?? 'Desconocido' ?></td>
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
			<form action="vistaFuentesPorIndicador.php" method="post">
				<div class="modal-header">						
					<h4 class="modal-title">FuentePorIndicador</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de FuentePorIndicador</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div id="home" class="container tab-pane active"><br>
							<div class="form-group">
								<label>Fuente</label>
									<Select id="txtidFuente" name="txtidFuente" class="form-control">
									<option value="" selected disabled>Seleccionar</option>	
									<?php $arregloFuentes = $objcontrolFuente->listar(); ?>
									<?php foreach ($arregloFuentes as $fuen): ?>
									<option value= <?php echo $fuen->__get('id') ?? 'Desconocido'?>>
									<?= $fuen->__get('nombre') ?? 'Sin nombre' ?>     
									</option>										
									<?php endforeach; ?> 
									</Select>
								</div>
								<div class="form-group">
									<label>Indicador </label>
									<Select id="txtIndicador" name="txtIndicador" class="form-control">	
									<option value="" selected disabled>Seleccionar</option>								
									<?php $arregloIndicadores = $objcontrolIndicador->listar(); ?>
									<?php foreach ($arregloIndicadores as $ind): ?>
									<option value= <?php echo $ind->__get('id') ?? 'Desconocido'?>>
									<?= $ind->__get('nombre') ?? 'Sin nombre' ?>     
									</option>										
									<?php endforeach; ?> 	
									</select>	
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
					<h4 class="modal-title">FuentePorIndicador</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de FuentePorIndicador</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div id="home" class="container tab-pane active"><br>
							<div class="form-group">
								<label>Fuente</label>
									<input type="text" id="txtidFuente" name="txtidFuente" class="form-control" value="<?php echo $fkidfuente ?>">
								</div>
								<div class="form-group">
                                <label>Indicador</label>
                                <select type="text" id="txtIndicador" name="txtIndicador" class="form-control" value="<?php echo $fkidindicador ?>">
								</select>
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
		<form action="vistaFuentesPorIndicador.php" method="post">
				<div class="modal-header">						
					<h4 class="modal-title">FuentePorIndicador</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de FuentePorIndicador</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div id="home" class="container tab-pane active"><br>
							<div class="form-group">
								<label>Fuente</label>
									<input type="text" id="txtidFuente" name="txtidFuente" class="form-control" value="<?php echo $fkidfuente ?>">
								</div>
								<div class="form-group">
                                <label>Indicador</label>
                                <input type="text" id="txtIndicador" name="txtIndicador" class="form-control" value="<?php echo $fkidindicador ?>">
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

