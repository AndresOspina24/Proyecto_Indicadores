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

$objControlResponsablesPorIndicador = new ControlEntidad('responsablesporindicador');
$arreglo = $objControlResponsablesPorIndicador->listar();

$boton = $_POST['bt'] ?? '';
$fkidresponsable = $_POST['txtidresponsable'] ?? '';
$fkidindicador = $_POST['txtIndicador'] ?? '';
$fecha_asignacion= $_POST['txtFecha'] ?? '';

$boton = $_POST['bt'] ?? '';

switch ($boton) {
    case 'Guardar':
        $datos = ['fkidresponsable' => $fkidresponsable, 'fkidindicador' => $fkidindicador, 'fechaasignacion' => $fecha_asignacion];
        $obj = new Entidad($datos);
        $objControl = new ControlEntidad('responsablesporindicador');
        $objControl->guardar($obj); 
        break;

    case 'Borrar':
        $datosResponsablesPorIndicador = ['fkidresponsable' => $fkidresponsable, 'fkidindicador' => $fkidindicador, 'fechaasignacion' => $fecha_asignacion];
        $objResponsablesPorIndicador = new Entidad($datosResponsablesPorIndicador);
        $objControlResponsablesPorIndicador->borrar('fkidresponsable', $fkidresponsable, 'fkidindicador', $fkidindicador, 'fechaasignacion', $fecha_asignacion);
        header('Location: vistaResponsablesPorIndicador.php');
        break;
}

$arregloResponsablesConsulta = [];
$objcontrolResponsable = new ControlEntidad('actor');
$arregloResponsable = $objcontrolResponsable->listar();
foreach ($arregloResponsable as $res) {
    $arregloResponsable[$res->__get('id')] = $res->__get('nombre');
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
                        <h2 class="miEstilo">Gestión <b>ResponsablesPorIndicador</b></h2>
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
                        <th>Responsable</th>
                        <th>Indicador</th>
                        <th>Fecha Asignacion</th>
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
                            <td><?= $arregloResponsable[$item->__get('fkidresponsable')] ?? 'Desconocido' ?></td>
                            <td><?= $arregloIndicadores[$item->__get('fkidindicador')] ?? 'Desconocido' ?></td>
                            <td><?= $item->__get('fechaasignacion');?></td>
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
			<form action="vistaResponsablesPorIndicador.php" method="post">
				<div class="modal-header">						
					<h4 class="modal-title">ResponsablePorIndicador</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de ResponsablePorIndicador</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div id="home" class="container tab-pane active"><br>
							<div class="form-group">
								<label>Responsable</label>
									<Select id="txtidresponsable" name="txtidresponsable" class="form-control">
									<option value="" selected disabled>Seleccionar</option>	
									<?php $arregloResponsable = $objcontrolResponsable->listar(); ?>
									<?php foreach ($arregloResponsable as $res): ?>
									<option value= <?php echo $res->__get('id') ?? 'Desconocido'?>>
									<?= $res->__get('nombre') ?? 'Sin nombre' ?>     
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
									<label>Fecha Asignacion </label>
									<input type="text" id="txtFecha" name="txtFecha" class="form-control" value="<?php echo $fecha_asignacion ?>">
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
					<h4 class="modal-title">ResponsablePorIndicador</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de ResponsablePorIndicador</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div id="home" class="container tab-pane active"><br>
							<div class="form-group">
								<label>Responsable</label>
									<input type="text" id="txtidresponsable" name="txtidresponsable" class="form-control" value="<?php echo $fkidresponsable ?>">
								</div>
								<div class="form-group">
                                <label>Indicador</label>
                                <input type="text" id="txtIndicador" name="txtIndicador" class="form-control" value="<?php echo $fkidindicador ?>">
								</div>
                                <div class="form-group">
                                <label>Fecha Asignacion</label>
                                <input type="text" id="txtFecha" name="txtFecha" class="form-control" value="<?php echo $fecha_asignacion ?>">
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
		<form action="vistaResponsablesPorIndicador.php" method="post">
				<div class="modal-header">						
					<h4 class="modal-title">ResponsablePorIndicador</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de ResponsablePorIndicador</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div id="home" class="container tab-pane active"><br>
							<div class="form-group">
								<label>Responsable</label>
									<input type="text" id="txtidresponsable" name="txtidresponsable" class="form-control" value="<?php echo $fkidresponsable ?>">
								</div>
								<div class="form-group">
                                <label>Indicador</label>
                                <input type="text" id="txtIndicador" name="txtIndicador" class="form-control" value="<?php echo $fkidindicador ?>">
								</div>
                                <div class="form-group">
                                <label>Fecha Asignacion</label>
                                <input type="text" id="txtFecha" name="txtFecha" class="form-control" value="<?php echo $fecha_asignacion ?>">
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

