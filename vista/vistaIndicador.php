<?php
ob_start();
include '../controlador/configBd.php';
include '../controlador/ControlEntidad.php';
include '../controlador/ControlConexionPdo.php';
include '../modelo/Entidad.php';
session_start();
if($_SESSION['email'] == null) header('Location: ../index.php');

// Validar roles
$permisoParaEntrar = false;
foreach($_SESSION['listaRolesDelUsuario'] as $rol){
    if($rol->__get('nombre') == "admin" || $rol->__get('nombre') == "Verificador") $permisoParaEntrar = true;
}
if(!$permisoParaEntrar) header('Location: ../vista/menu.php');

// Listar entidades relacionadas
$objControlIndicador = new ControlEntidad('indicador');
$arreglo = $objControlIndicador->listar();

$boton = $_POST['bt'] ?? '';
$id = $_POST['txtId'] ?? '';
$nombre = $_POST['txtNombre'] ?? '';
$objetivo = $_POST['txtObjetivo'] ?? '';
$alcance = $_POST['txtAlcance'] ?? '';
$formula = $_POST['txtFormula'] ?? '';
$meta = $_POST['txtMeta'] ?? '';
$fkidtipoindicador = $_POST['txtTipo'] ?? '';
$fkidunidadmedicion = $_POST['txtUnidad'] ?? '';
$fkidsentido = $_POST['txtSentido'] ?? '';
$fkidfrecuencia = $_POST['txtFrecuencia'] ?? '';
$fkidarticulo = $_POST['txtArticulo'] ?? '';
$fkidliteral = $_POST['txtLiteral'] ?? '';
$fkidnumeral = $_POST['txtNumeral'] ?? '';
$fkidparagrafo = $_POST['txtParagrafo'] ?? '';


switch($boton){
    case 'Guardar':
        $datos = ['id'=>$id, 'nombre' => $nombre, 'objetivo' => $objetivo, 'alcance' => $alcance, 'formula' => $formula,
            'fkidtipoindicador' => $fkidtipoindicador, 'fkidunidadmedicion' =>$fkidunidadmedicion, 'meta' =>$meta, 
            'fkidsentido' => $fkidsentido, 'fkidfrecuencia' => $fkidfrecuencia, 'fkidarticulo' => $fkidarticulo, 
            'fkidliteral' => $fkidliteral, 'fkidnumeral' => $fkidnumeral, 'fkidparagrafo' => $fkidparagrafo];
        $obj = new Entidad($datos);
        $objControlIndicador = new ControlEntidad('indicador');
        $objControlIndicador->guardar($obj);
        header('Location: vistaIndicador.php');
        break;

        case 'Consultar':
            $datos=['id' => $id];
            $obj = new Entidad($datos); 
            $objControIndicador = new ControlEntidad('indicador');
            $obj = $objControlIndicador->buscarPorId('id', $id);
            if ($obj !== null) {
                $nombre = $obj->__get('nombre');
            } else {
                // Manejar el caso en que $objUsuario es nulo
                echo "El usuario no se encontró.";
            }
            break;
    case 'Modificar':
		// Se debería llamar a un procedimiento almacenado con control de transacciones
		//para modificar en las dos tablas
		//1. modifica en tabla principal    
        $datosTipoActor = ['id' => $id, 'nombre' => $nombre];
        $objTipoActor=new Entidad($datosTipoActor);
        $objControlTipoActor = new ControlEntidad('tipoactor');
        $objControlTipoActor->modificar('id', $id, $objTipoActor);
		header('Location: vistaIndicador.php');
        break;
        case 'Modificar':
            // Se debería llamar a un procedimiento almacenado con control de transacciones
            //para modificar en las dos tablas
            //1. modifica en tabla principal    
            $datos = ['id'=>$id, 'nombre' => $nombre, 'objetivo' => $objetivo, 'alcance' => $alcance, 'formula' => $formula,
            'fkidtipoindicador' => $fkidtipoindicador, 'fkidunidadmedicion' =>$fkidunidadmedicion, 'meta' =>$meta, 
            'fkidsentido' => $fkidsentido, 'fkidfrecuencia' => $fkidfrecuencia, 'fkidarticulo' => $fkidarticulo, 
            'fkidliteral' => $fkidliteral, 'fkidnumeral' => $fkidnumeral, 'fkidparagrafo' => $fkidparagrafo];
            $obj=new Entidad($datos);
            $objControl = new ControlEntidad('indicador');
            $objControl->modificar('id', $id, $obj);
            header('Location: vistaIndicador.php');
            break;
            case 'Borrar':
                $datos = ['id'=>$id, 'nombre' => $nombre, 'objetivo' => $objetivo, 'alcance' => $alcance, 'formula' => $formula,
            'fkidtipoindicador' => $fkidtipoindicador, 'fkidunidadmedicion' =>$fkidunidadmedicion, 'meta' =>$meta, 
            'fkidsentido' => $fkidsentido, 'fkidfrecuencia' => $fkidfrecuencia, 'fkidarticulo' => $fkidarticulo, 
            'fkidliteral' => $fkidliteral, 'fkidnumeral' => $fkidnumeral, 'fkidparagrafo' => $fkidparagrafo];
                $obj = new Entidad($datos);
                $objControlIndicador->borrar('id', $id, 'nombre' , $nombre, 'objetivo' , $objetivo, 'alcance' , $alcance, 'formula' , $formula,
            'fkidtipoindicador' , $fkidtipoindicador, 'fkidunidadmedicion', $fkidunidadmedicion, 'meta' , $meta, 
            'fkidsentido' , $fkidsentido, 'fkidfrecuencia',  $fkidfrecuencia, 'fkidarticulo', $fkidarticulo, 
            'fkidliteral' , $fkidliteral, 'fkidnumeral', $fkidnumeral, 'fkidparagrafo', $fkidparagrafo);
                header('Location: vistaIndicador.php');
                break;
}

$arregloTipoIndicadoresConsulta = [];
$objcontrolTipoIndicador = new ControlEntidad('tipoindicador');
$arregloTipoIndicadores = $objcontrolTipoIndicador->listar();
foreach ($arregloTipoIndicadores as $td) {
    $arregloTipoIndicadores[$td->__get('id')] = $td->__get('nombre');
}


$arregloUnidadesMedicionConsulta = [];
$objcontrolUnidadesMedicion = new ControlEntidad('unidadmedicion');
$arregloUnidadesMedicion = $objcontrolUnidadesMedicion->listar();
foreach ($arregloUnidadesMedicion as $um) {
    $arregloUnidadesMedicion[$um->__get('id')] = $um->__get('descripcion');
}

$arregloSentidoConsulta = [];
$objcontrolSentido = new ControlEntidad('sentido');
$arregloSentido = $objcontrolSentido->listar();
foreach ($arregloSentido as $se) {
    $arregloSentido[$se->__get('id')] = $se->__get('nombre');
}
$arregloFrecuenciasConsulta = [];
$objcontrolFrecuencia = new ControlEntidad('frecuencia');
$arregloFrecuencias = $objcontrolFrecuencia->listar();
foreach ($arregloFrecuencias as $f) {
    $arregloFrecuencias[$f->__get('id')] = $td->__get('nombre');
}

$arregloArticulosConsulta = [];
$objcontrolArticulos = new ControlEntidad('articulo');
$arregloArticulos = $objcontrolArticulos->listar();
foreach ($arregloArticulos as $ar) {
    $arregloArticulos[$ar->__get('id')] = $ar->__get('nombre');
}

$arregloLiteralConsulta = [];
$objcontrolLiteral = new ControlEntidad('literal');
$arregloLiteral = $objcontrolLiteral->listar();
foreach ($arregloLiteral as $l) {
    $arregloLiteral[$l->__get('id')] = $l->__get('descripcion');
}


$arregloNumeralConsulta = [];
$objcontrolNumeral = new ControlEntidad('numeral');
$arregloNumeral = $objcontrolNumeral->listar();
foreach ($arregloNumeral as $nu) {
    $arregloNumeral[$nu->__get('id')] = $nu->__get('descripcion');
}

$arregloParagrafoConsulta = [];
$objcontrolParagrafo = new ControlEntidad('paragrafo');
$arregloParagrafo = $objcontrolParagrafo->listar();
foreach ($arregloParagrafo as $pr) {
    $arregloParagrafo[$pr->__get('id')] = $pr->__get('descripcion');
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
                        <h2 class="miEstilo">Gestión <b>Indicador</b></h2>
                    </div>
                    <div class="col-sm-6">
                        <a href="#crudModal" class="btn btn-primary" data-toggle="modal">
                            <i class="material-icons">&#xE84E;</i> <span>Gestión I</span>
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
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Objetivo</th>
                            <th>Alcance</th>
                            <th>Fórmula</th>
                            <th>TipoIndicador</th>
                            <th>UnidadMedicion</th>
                            <th>Sentido</th>
                            <th>Frecuencia</th>
							<th>Meta</th>
                            <th>Artículo</th>
                            <th>Literal</th>
                            <th>Numeral</th>
                            <th>Parágrafo</th>
                            <th>Acciones</th>
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
                                <td><?= $item->__get('id') ?></td>
                                <td><?= $item->__get('nombre') ?></td>
                                <td><?= $item->__get('objetivo') ?></td>
                                <td><?= $item->__get('alcance') ?></td>
                                <td><?= $item->__get('formula') ?></td>
                                <td><?= $arregloTipoIndicadores[$item->__get('fkidtipoindicador')] ?? 'Desconocido' ?></td>
                                <td><?= $arregloUnidadesMedicion[$item->__get('fkidunidadmedicion')] ?? 'Desconocido' ?></td>
                                <td><?= $arregloSentido[$item->__get('fkidsentido')] ?? 'Desconocido' ?></td>
                                <td><?= $arregloFrecuencias[$item->__get('fkidfrecuencia')] ?? 'Desconocido'?></td>
                                <td><?= $item->__get('meta') ?></td>
                                <td><?= $arregloArticulos[$item->__get('fkidarticulo')] ?? 'Desconocido'?></td>
                                <td><?= $arregloLiteral[$item->__get('fkidliteral')] ?? 'Desconocido'?></td>
                                <td><?= $arregloNumeral[$item->__get('fkidnumeral')] ?? 'Desconocido' ?></td>
                                <td><?= $arregloParagrafo[$item->__get('fkidparagrafo')] ?? 'Desconocido' ?></td>
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

<div id="crudModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="vistaIndicador.php" method="post">
				<div class="modal-header">						
					<h4 class="modal-title">Indicador</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de Indicador</a>
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
									<label>Nombre </label>
									<input type="text" id="txtNombre" name="txtNombre" class="form-control" value="<?php echo $nombre ?>">
								</div>
                                <div class="form-group">
									<label>Objetivo </label>
									<input type="text" id="txtObjetivo" name="txtObjetivo" class="form-control" value="<?php echo $objetivo ?>">
								</div>
                                <div class="form-group">
									<label>Alcance </label>
									<input type="text" id="txtAlcance" name="txtAlcance" class="form-control" value="<?php echo $alcance ?>">
								</div>
                                <div class="form-group">
									<label>Formula </label>
									<input type="text" id="txtFormula" name="txtFormula" class="form-control" value="<?php echo $formula ?>">
								</div>
                                <div class="form-group">
									<label>TipoIndicador </label>
									<Select id="txtTipo" name="txtTipo" class="form-control">
									<option value="" selected disabled>Seleccionar</option>	
									<?php $arregloTipoIndicadores = $objcontrolTipoIndicador->listar(); ?>
									<?php foreach ($arregloTipoIndicadores as $td): ?>
									<option value= <?php echo $td->__get('id') ?? 'Desconocido'?>> 
									<?= $td->__get('nombre') ?? 'Sin nombre' ?>  
									</option>										
									<?php endforeach; ?> 
									</select>
								</div>
                                <div class="form-group">
									<label>UnidadMedicion </label>
									<Select id="txtUnidad" name="txtUnidad" class="form-control">
									<option value="" selected disabled>Seleccionar</option>	
									<?php $arregloUnidadesMedicion = $objcontrolUnidadesMedicion->listar(); ?>
									<?php foreach ($arregloUnidadesMedicion as $um): ?>
									<option value= <?php echo $um->__get('id') ?? 'Desconocido'?>> 
									<?= $um->__get('descripcion') ?? 'Sin nombre' ?>  
									</option>										
									<?php endforeach; ?> 
									</select>
								</div>
                                <div class="form-group">
									<label>Meta </label>
									<input type="text" id="txtMeta" name="txtMeta" class="form-control" value="<?php echo $meta ?>">
								</div>
                                <div class="form-group">
									<label>Sentido </label>
									<select  id="txtSentido" name="txtSentido" class="form-control">
									<option value="" selected disabled>Seleccionar</option>	
									<?php $arregloSentido = $objcontrolSentido->listar(); ?>
									<?php foreach ($arregloSentido as $s): ?>
									<option value= <?php echo $s->__get('id') ?? 'Desconocido'?>> 
									<?= $s->__get('nombre') ?? 'Sin nombre' ?>  
									</option>										
									<?php endforeach; ?> 
									</select>
								</div>
                                <div class="form-group">
									<label>Frecuencia </label>
									<select  id="txtFrecuencia" name="txtFrecuencia" class="form-control">
									<option value="" selected disabled>Seleccionar</option>	
									<?php $arregloFrecuencias = $objcontrolFrecuencia->listar(); ?>
									<?php foreach ($arregloFrecuencias as $f): ?>
									<option value= <?php echo $f->__get('id') ?? 'Desconocido'?>> 
									<?= $f->__get('nombre') ?? 'Sin nombre' ?>  
									</option>										
									<?php endforeach; ?> 
									</select>
								</div>
                                <div class="form-group">
									<label>Articulo </label>
									<select id="txtArticulo" name="txtArticulo" class="form-control">
									<option value="" selected disabled>Seleccionar</option>	
									<?php $arregloArticulos = $objcontrolArticulos->listar(); ?>
									<?php foreach ($arregloArticulos as $ar): ?>
									<option value= <?php echo $ar->__get('id') ?? 'Desconocido'?>>
									<?= $ar->__get('nombre') ?? 'Sin nombre' ?>     
									</option>										
									<?php endforeach; ?> 
								</select>
								</div>
                                <div class="form-group">
									<label>Literal </label>
									<select id="txtLiteral" name="txtLiteral" class="form-control">
									<option value="" selected disabled>Seleccionar</option>	
									<?php $arregloLiteral = $objcontrolLiteral->listar(); ?>
									<?php foreach ($arregloLiteral as $L): ?>
									<option value= <?php echo $L->__get('id') ?? 'Desconocido'?>>
									<?= $L->__get('descripcion') ?? 'Sin nombre' ?>     
									</option>										
									<?php endforeach; ?> 
								</select>
								</div>
                                <div class="form-group">
									<label>Numeral </label>
									<select id="txtNumeral" name="txtNumeral" class="form-control">
									<option value="" selected disabled>Seleccionar</option>	
									<?php $arregloNumeral = $objcontrolNumeral->listar(); ?>
									<?php foreach ($arregloNumeral as $nu): ?>
									<option value= <?php echo $nu->__get('id') ?? 'Desconocido'?>>
									<?= $nu->__get('descripcion') ?? 'Sin nombre' ?>     
									</option>										
									<?php endforeach; ?> 
								</select>
								</div>
                                <div class="form-group">
									<label>Parágrafo </label>
									<select  id="txtParagrafo" name="txtParagrafo" class="form-control">
									<option value="" selected disabled>Seleccionar</option>	
									<?php $arregloParagrafo = $objcontrolParagrafo->listar(); ?>
									<?php foreach ($arregloParagrafo as $pr): ?>
									<option value= <?php echo $pr->__get('id') ?? 'Desconocido'?>>
									<?= $pr->__get('descripcion') ?? 'Sin nombre' ?>     
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
					<h4 class="modal-title">Indicador</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de Indicador</a>
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
									<label>Nombre </label>
									<input type="text" id="txtNombre" name="txtNombre" class="form-control" value="<?php echo $nombre ?>">
								</div>
                                <div class="form-group">
									<label>Objetivo </label>
									<input type="text" id="txtNombre" name="txtNombre" class="form-control" value="<?php echo $objetivo ?>">
								</div>
                                <div class="form-group">
									<label>Alcance </label>
									<input type="text" id="txtNombre" name="txtNombre" class="form-control" value="<?php echo $alcance ?>">
								</div>
                                <div class="form-group">
									<label>Formula </label>
									<input type="text" id="txtNombre" name="txtNombre" class="form-control" value="<?php echo $formula ?>">
								</div>
                                <div class="form-group">
									<label>TipoIndicador </label>
									<input type="text" id="txtNombre" name="txtNombre" class="form-control" value="<?php echo $fkidtipoindicador ?>">
								</div>
                                <div class="form-group">
									<label>UnidadMedicion </label>
									<input type="text" id="txtNombre" name="txtNombre" class="form-control" value="<?php echo $fkidunidadmedicion ?>">
								</div>
                                <div class="form-group">
									<label>Meta </label>
									<input type="text" id="txtNombre" name="txtNombre" class="form-control" value="<?php echo $meta ?>">
								</div>
                                <div class="form-group">
									<label>Sentido </label>
									<input type="text" id="txtNombre" name="txtNombre" class="form-control" value="<?php echo $fkidsentido ?>">
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
		<form action="vistaIndicador.php" method="post">
				<div class="modal-header">						
					<h4 class="modal-title">Indicador</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
						<div class="container">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#home">Datos de Indicador</a>
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