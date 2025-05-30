<?php
ob_start();

include '../controlador/configBd.php';
include '../controlador/ControlEntidad.php';
include '../controlador/ControlConexionPdo.php';
include '../modelo/Entidad.php';
include 'funciones_roles.php';
session_start();
if($_SESSION['email'] == null) header('Location: ../index.php');

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
// Validar roles
//$permisoParaEntrar = false;
//foreach($_SESSION['listaRolesDelUsuario'] as $rol){
//    if($rol->__get('nombre') == "admin" || $rol->__get('nombre') == "Verificador") $permisoParaEntrar = true;
//}
//if(!$permisoParaEntrar) header('Location: ../vista/menu.php');

// Listar entidades relacionadas
$objControlIndicador = new ControlEntidad('indicador');
$arreglo = $objControlIndicador->listar();

$boton = $_POST['bt'] ?? '';
$id = $_POST['txtId'] ?? '';
$codigo = $_POST['txtCodigo'] ?? '';
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
		if ($esAdmin) {
        $datos = ['id'=>$id, 'codigo' => $codigo,  'nombre' => $nombre, 'objetivo' => $objetivo, 'alcance' => $alcance, 'formula' => $formula,
        'fkidtipoindicador' => $fkidtipoindicador, 'fkidunidadmedicion' =>$fkidunidadmedicion, 'meta' =>$meta, 
        'fkidsentido' => $fkidsentido, 'fkidfrecuencia' => $fkidfrecuencia, 'fkidarticulo' => $fkidarticulo, 
        'fkidliteral' => $fkidliteral, 'fkidnumeral' => $fkidnumeral, 'fkidparagrafo' => $fkidparagrafo];
        $objIndicador = new Entidad($datos);
        $objControlIndicador = new ControlEntidad('indicador');
        $objControlIndicador->guardar($objIndicador);
        header('Location: vistaIndicador.php');
		 } else {
            echo "<script>alert('No tienes permiso para Guardar.'); window.location.href='vistaRepresentacionVisual.php';</script>";
        }
    
        break;

    case 'Consultar':
        // No necesitamos control de acceso aquí, ya que la página en sí ya lo tiene.
        $datos = ['id'=>$id, 'codigo' => $codigo,  'nombre' => $nombre, 'objetivo' => $objetivo, 'alcance' => $alcance, 'formula' => $formula,
        'fkidtipoindicador' => $fkidtipoindicador, 'fkidunidadmedicion' =>$fkidunidadmedicion, 'meta' =>$meta, 
        'fkidsentido' => $fkidsentido, 'fkidfrecuencia' => $fkidfrecuencia, 'fkidarticulo' => $fkidarticulo, 
        'fkidliteral' => $fkidliteral, 'fkidnumeral' => $fkidnumeral, 'fkidparagrafo' => $fkidparagrafo];
        $obj = new Entidad($datos); 
        $objControIndicador = new ControlEntidad('indicador');
        $obj = $objControlIndicador->buscarPorId('id', $id);
		 if ($obj !== null) {
			$codigo= $obj->__get('codigo');
            $nombre = $obj->__get('nombre');
			$objetivo = $obj->__get('objetivo');
			$alcance= $obj->__get('alcance');
			$formula = $obj->__get('formula');
            $fkidtipoindicador = $obj->__get('fkidtipoindicador');
			$fkidunidadmedicion = $obj->__get('fkidunidadmedicion');
			$meta = $obj->__get('meta');
			$fkidsentido = $obj->__get('fkidsentido');
			$fkidfrecuencia = $obj->__get('fkidfrecuencia');
			$fkidarticulo = $obj->__get('fkidarticulo');
			$fkidliteral = $obj->__get('fkidliteral');
			$fkidnumeral = $obj->__get('fkidnumeral');
			$fkidparagrafo = $obj->__get('fkidparagrafo');
        } else {
            echo "El Tipo Indicador no se encontró.";
        }
        // ...
        break;
    case 'Modificar':
        if (!esAdmin($listaRolesDelUsuario) && !esValidador($listaRolesDelUsuario)) {
            echo "<script>alert('No tienes permiso para modificar indicadores.'); window.location='vistaIndicador.php';</script>";
            break;
        }
        $datos = ['id'=>$id, 'codigo' => $codigo, 'nombre' => $nombre, 'objetivo' => $objetivo, 'alcance' => $alcance, 'formula' => $formula,
        'fkidtipoindicador' => $fkidtipoindicador, 'fkidunidadmedicion' =>$fkidunidadmedicion, 'meta' =>$meta, 
        'fkidsentido' => $fkidsentido, 'fkidfrecuencia' => $fkidfrecuencia, 'fkidarticulo' => $fkidarticulo, 
        'fkidliteral' => $fkidliteral, 'fkidnumeral' => $fkidnumeral, 'fkidparagrafo' => $fkidparagrafo];
        $obj = new Entidad($datos);
        $objControl = new ControlEntidad('indicador');
        $objControl->modificar(['id'], [$id], $obj);
        header('Location: vistaIndicador.php');
        break;
    case 'Borrar':
        if (!esAdmin($listaRolesDelUsuario)) {
            echo "<script>alert('No tienes permiso para borrar indicadores.'); window.location='vistaIndicador.php';</script>";
            break;
        }
    
        $objControlIndicador = new ControlEntidad('indicador');
        $objControlIndicador->borrar(['id'], [$id]);
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
    $arregloFrecuencias[$f->__get('id')] = $f->__get('nombre');
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
                        <?php if (($esAdmin or $esValidador or $esVerificador)): ?>
                            <a href="#crudModal" class="btn btn-primary" data-toggle="modal"><i
                                    class="material-icons">&#xE84E;</i> <span>Gestión</span></a>
                        <?php endif; ?>
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
							<th>Codigo</th>
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
                            <th>Paragrafo</th>
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
								<td><?= $item->__get('codigo') ?></td>
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

                                <?php if (($esAdmin == true) || ($esValidador == true )): ?><a href="#editar" class="edit" data-toggle="modal"
                                       data-id="<?= $item->__get('id') ?>"
									   data-codigo="<?= $item->__get('codigo') ?>"
                                       data-nombre="<?= $item->__get('nombre') ?>"
									   data-objetivo="<?= $item->__get('objetivo') ?>"
									   data-alcance="<?= $item->__get('alcance') ?>"
									   data-formula="<?= $item->__get('formula') ?>"
									   data-fkidtipoindicador="<?= $item->__get('fkidtipoindicador') ?>"
									   data-fkidunidadmedicion="<?= $item->__get('fkidunidadmedicion') ?>"  
									   data-fkidsentido="<?= $item->__get('fkidsentido') ?>"
									   data-fkidfrecuencia="<?= $item->__get('fkidfrecuencia') ?>"
									   data-meta="<?= $item->__get('meta') ?>"
									   data-fkidarticulo="<?= $item->__get('fkidarticulo') ?>"
									   data-fkidliteral="<?= $item->__get('fkidliteral') ?>"
									   data-fkidnumeral="<?= $item->__get('fkidnumeral') ?>"
									   data-fkidparagrafo="<?= $item->__get('fkidparagrafo') ?>"  >
                                        <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
                                    </a>
									<?php endif; ?>
									 <?php if ($esAdmin): ?>
                                    <a href="#borrar" class="delete" data-toggle="modal"
									data-id="<?= $item->__get('id') ?>"
									   data-codigo="<?= $item->__get('codigo') ?>"
                                       data-nombre="<?= $item->__get('nombre') ?>"
									   data-objetivo="<?= $item->__get('objetivo') ?>"
									   data-alcance="<?= $item->__get('alcance') ?>"
									   data-formula="<?= $item->__get('formula') ?>"
									   data-fkidtipoindicador="<?= $item->__get('fkidtipoindicador') ?>"
									   data-fkidunidadmedicion="<?= $item->__get('fkidunidadmedicion') ?>"  
									   data-fkidsentido="<?= $item->__get('fkidsentido') ?>"
									   data-fkidfrecuencia="<?= $item->__get('fkidfrecuencia') ?>"
									   data-meta="<?= $item->__get('meta') ?>"
									   data-fkidarticulo="<?= $item->__get('fkidarticulo') ?>"
									   data-fkidliteral="<?= $item->__get('fkidliteral') ?>"
									   data-fkidnumeral="<?= $item->__get('fkidnumeral') ?>"
									   data-fkidparagrafo="<?= $item->__get('fkidparagrafo') ?>" ><i class="material-icons" data-toggle="tooltip">&#xE872;</i></a>
                                     <?php endif; ?>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>        
</div>


<?php if (($esAdmin or $esValidador or $esVerificador)): ?>
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
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#home">Datos de Indicador</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="home" class="container tab-pane active"><br>
                                <div class="form-group">
                                    <label>Id</label>
                                    <input type="text" id="txtId" name="txtId" class="form-control" value="<?php echo $id ?>">
                                </div>
                                <div class="form-group">
                                    <label>Codigo</label>
                                    <input type="text" id="txtCodigo" name="txtCodigo" class="form-control" value="<?php echo $codigo ?>">
                                </div>
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" id="txtNombre" name="txtNombre" class="form-control" value="<?php echo $nombre ?>">
                                </div>
                                <div class="form-group">
                                    <label>Objetivo</label>
                                    <input type="text" id="txtObjetivo" name="txtObjetivo" class="form-control" value="<?php echo $objetivo ?>">
                                </div>
                                <div class="form-group">
                                    <label>Alcance</label>
                                    <input type="text" id="txtAlcance" name="txtAlcance" class="form-control" value="<?php echo $alcance ?>">
                                </div>
                                <div class="form-group">
                                    <label>Fórmula</label>
                                    <input type="text" id="txtFormula" name="txtFormula" class="form-control" value="<?php echo $formula ?>">
                                </div>
                                <div class="form-group">
                                    <label>TipoIndicador</label>
                                    <select id="txtTipo" name="txtTipo" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php $arregloTipoIndicadores = $objcontrolTipoIndicador->listar(); ?>
                                        <?php foreach ($arregloTipoIndicadores as $td): ?>
                                            <option value="<?php echo $td->__get('id'); ?>" 
                                                <?= ($td->__get('id') == $fkidtipoindicador) ? 'selected' : '' ?>>
                                                <?php echo $td->__get('nombre') ?? 'Sin nombre'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>UnidadMedicion</label>
                                    <select id="txtUnidad" name="txtUnidad" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php $arregloUnidadesMedicion = $objcontrolUnidadesMedicion->listar(); ?>
                                        <?php foreach ($arregloUnidadesMedicion as $um): ?>
                                            <option value="<?php echo $um->__get('id'); ?>"
                                                <?= ($um->__get('id') == $fkidunidadmedicion) ? 'selected' : '' ?>>
                                                <?php echo $um->__get('descripcion') ?? 'Sin descripcion'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Sentido</label>
                                    <select id="txtSentido" name="txtSentido" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php $arregloSentido = $objcontrolSentido->listar(); ?>
                                        <?php foreach ($arregloSentido as $se): ?>
                                            <option value="<?php echo $se->__get('id'); ?>"
                                                <?= ($se->__get('id') == $fkidsentido) ? 'selected' : '' ?>>
                                                <?php echo $se->__get('nombre') ?? 'Sin nombre'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Frecuencia</label>
                                    <select id="txtFrecuencia" name="txtFrecuencia" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php $arregloFrecuencias = $objcontrolFrecuencia->listar(); ?>
                                        <?php foreach ($arregloFrecuencias as $fr): ?>
                                            <option value="<?php echo $fr->__get('id'); ?>"
                                                <?= ($fr->__get('id') == $fkidfrecuencia) ? 'selected' : '' ?>>
                                                <?php echo $fr->__get('nombre') ?? 'Sin nombre'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Meta</label>
                                    <input type="text" id="txtMeta" name="txtMeta" class="form-control" value="<?php echo $meta ?>">
                                </div>
                                <div class="form-group">
                                    <label>Artículo</label>
                                    <select id="txtArticulo" name="txtArticulo" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php $arregloArticulos = $objcontrolArticulos->listar(); ?>
                                        <?php foreach ($arregloArticulos as $ar): ?>
                                            <option value="<?php echo $ar->__get('id'); ?>"
                                                <?= ($ar->__get('id') == $fkidarticulo) ? 'selected' : '' ?>>
                                                <?php echo $ar->__get('nombre') ?? 'Sin nombre'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Literal</label>
                                    <select id="txtLiteral" name="txtLiteral" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php $arregloLiteral = $objcontrolLiteral->listar(); ?>
                                        <?php foreach ($arregloLiteral as $li): ?>
                                            <option value="<?php echo $li->__get('id'); ?>"
                                                <?= ($li->__get('id') == $fkidliteral) ? 'selected' : '' ?>>
                                                <?php echo $li->__get('descripcion') ?? 'Sin descripcion'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Numeral</label>
                                    <select id="txtNumeral" name="txtNumeral" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php $arregloNumeral = $objcontrolNumeral->listar(); ?>
                                        <?php foreach ($arregloNumeral as $num): ?>
                                            <option value="<?php echo $num->__get('id'); ?>"
                                                <?= ($num->__get('id') == $fkidnumeral) ? 'selected' : '' ?>>
                                                <?php echo $num->__get('descripcion') ?? 'Sin descripcion'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Parágrafo</label>
                                    <select id="txtParagrafo" name="txtParagrafo" class="form-control">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <?php $arregloParagrafo = $objcontrolParagrafo->listar(); ?>
                                        <?php foreach ($arregloParagrafo as $par): ?>
                                            <option value="<?php echo $par->__get('id'); ?>"
                                                <?= ($par->__get('id') == $fkidparagrafo) ? 'selected' : '' ?>>
                                                <?php echo $par->__get('descripcion') ?? 'Sin descripcion'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <?php if ($esAdmin): ?>
                                        <input type="submit" id="btnGuardar" name="bt" class="btn btn-success" value="Guardar">
                                    <?php endif; ?>
                                    <input type="submit" id="btnConsultar" name="bt" class="btn btn-success" value="Consultar">
                                </div>
                            </div>
                            <div id="menu1" class="container tab-pane fade"><br>
                                <!-- Aquí otras pestañas si las hay -->
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


<?php if (($esAdmin or $esValidador)): ?>
<div id="editar" class="modal fade">
	<div class="modal-dialog">
            <div class="modal-content">
                <form action="vistaIndicador.php" method="post">
                    <div class="modal-header">
                        <h4 class="modal-title">Indicador</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#home">Datos de Indicador</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="home" class="container tab-pane active"><br>
                                    <div class="form-group">
						<label>Id</label>
						<input type="text" id="edit_txtId" name="txtId" class="form-control"  value="<?php echo $id?>"readonly>
					</div>
					<div class="form-group">
						<label>Codigo</label>
						<input type="text" id="edit_txtCodigo" name="txtCodigo" class="form-control" value="<?php echo $codigo ?>">
					</div>
					<div class="form-group">
						<label>Nombre</label>
						<input type="text" id="edit_txtNombre" name="txtNombre" class="form-control" value="<?php echo $nombre ?>" >
					</div>
					<div class="form-group">
						<label>Objetivo</label>
						<input type="text" id="edit_txtobjetivo" name="txtObjetivo" class="form-control" value="<?php echo $objetivo ?>" >
					</div>
					<div class="form-group">
						<label>Alcance</label>
						<input type="text" id="edit_txtAlcance" name="txtAlcance" class="form-control" value="<?php echo $alcance ?>">
					</div>
					<div class="form-group">
						<label>Fórmula</label>
						<input type="text" id="edit_txtFormula" name="txtFormula" class="form-control" value="<?php echo $formula ?>">
					</div>
					<div class="form-group">
						<label>TipoIndicador</label>
						<select id="edit_txtTipo" name="txtTipo" class="form-control">
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloTipoIndicadores = $objcontrolTipoIndicador->listar(); ?>
							<?php foreach ($arregloTipoIndicadores as $td): ?>
								<option value="<?php echo $td->__get('id') ?>">
									<?= $td->__get('nombre') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>UnidadMedicion</label>
						<select id="edit_txtUnidad" name="txtUnidad" class="form-control">
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloUnidadesMedicion = $objcontrolUnidadesMedicion->listar(); ?>
							<?php foreach ($arregloUnidadesMedicion as $um): ?>
								<option value="<?php echo $um->__get('id') ?>">
									<?= $um->__get('descripcion') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Sentido</label>
						<select id="edit_txtSentido" name="txtSentido" class="form-control">
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloSentido = $objcontrolSentido->listar(); ?>
							<?php foreach ($arregloSentido as $se): ?>
								<option value="<?php echo $se->__get('id') ?>">
									<?= $se->__get('nombre') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Frecuencia</label>
						<select id="edit_txtFrecuencia" name="txtFrecuencia" class="form-control">
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloFrecuencias = $objcontrolFrecuencia->listar(); ?>
							<?php foreach ($arregloFrecuencias as $fr): ?>
								<option value="<?php echo $fr->__get('id') ?>">
									<?= $fr->__get('nombre') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Meta</label>
						<input type="text" id="edit_txtMeta" name="txtMeta" class="form-control" >
					</div>
					<div class="form-group">
						<label>Artículo</label>
						<select id="edit_txtArticulo" name="txtArticulo" class="form-control">
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloArticulos = $objcontrolArticulos->listar(); ?>
							<?php foreach ($arregloArticulos as $ar): ?>
								<option value="<?php echo $ar->__get('id') ?>">
									<?= $ar->__get('nombre') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Literal</label>
						<select id="edit_txtLiteral" name="txtLiteral" class="form-control">
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloLiteral = $objcontrolLiteral->listar(); ?>
							<?php foreach ($arregloLiteral as $li): ?>
								<option value="<?php echo $li->__get('id') ?>">
									<?= $li->__get('descripcion') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Numeral</label>
						<select id="edit_txtNumeral" name="txtNumeral" class="form-control">
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloNumeral = $objcontrolNumeral->listar(); ?>
							<?php foreach ($arregloNumeral as $num): ?>
								<option value="<?php echo $num->__get('id') ?>">
									<?= $num->__get('descripcion') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Parágrafo</label>
						<select id="edit_txtParagrafo" name="txtParagrafo" class="form-control">
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloParagrafo = $objcontrolParagrafo->listar(); ?>
							<?php foreach ($arregloParagrafo as $par): ?>
								<option value="<?php echo $par->__get('id') ?>">
									<?= $par->__get('descripcion') ?>
								</option>
							<?php endforeach; ?>
						</select>
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


<?php if (($esAdmin)): ?>
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
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#home">Datos de Indicador</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="home" class="container tab-pane active"><br>
                                    <div class="form-group">
						<label>Id</label>
						<input type="text" id="delete_txtId" name="txtId" class="form-control"  value="<?php echo $id?>"readonly>
					</div>
					<div class="form-group">
						<label>Codigo</label>
						<input type="text" id="delete_txtCodigo" name="txtCodigo" class="form-control" value="<?php echo $codigo ?>"readonly>
					</div>
					<div class="form-group">
						<label>Nombre</label>
						<input type="text" id="delete_txtNombre" name="txtNombre" class="form-control" value="<?php echo $nombre ?>"readonly >
					</div>
					<div class="form-group">
						<label>Objetivo</label>
						<input type="text" id="delete_txtobjetivo" name="txtObjetivo" class="form-control" value="<?php echo $objetivo ?>" readonly>
					</div>
					<div class="form-group">
						<label>Alcance</label>
						<input type="text" id="delete_txtAlcance" name="txtAlcance" class="form-control" value="<?php echo $alcance ?>"readonly>
					</div>
					<div class="form-group">
						<label>Fórmula</label>
						<input type="text" id="delete_txtFormula" name="txtFormula" class="form-control" value="<?php echo $formula ?>"readonly>
					</div>
					<div class="form-group">
						<label>TipoIndicador</label>
						<select id="delete_txtTipo" name="txtTipo" class="form-control"readonly>
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloTipoIndicadores = $objcontrolTipoIndicador->listar(); ?>
							<?php foreach ($arregloTipoIndicadores as $td): ?>
								<option value="<?php echo $td->__get('id') ?>">
									<?= $td->__get('nombre') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>UnidadMedicion</label>
						<select id="delete_txtUnidad" name="txtUnidad" class="form-control"readonly>
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloUnidadesMedicion = $objcontrolUnidadesMedicion->listar(); ?>
							<?php foreach ($arregloUnidadesMedicion as $um): ?>
								<option value="<?php echo $um->__get('id') ?>">
									<?= $um->__get('descripcion') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Sentido</label>
						<select id="delete_txtSentido" name="txtSentido" class="form-control"readonly>
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloSentido = $objcontrolSentido->listar(); ?>
							<?php foreach ($arregloSentido as $se): ?>
								<option value="<?php echo $se->__get('id') ?>">
									<?= $se->__get('nombre') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Frecuencia</label>
						<select id="delete_txtFrecuencia" name="txtFrecuencia" class="form-control"readonly>
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloFrecuencias = $objcontrolFrecuencia->listar(); ?>
							<?php foreach ($arregloFrecuencias as $fr): ?>
								<option value="<?php echo $fr->__get('id') ?>">
									<?= $fr->__get('nombre') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Meta</label>
						<input type="text" id="delete_txtMeta" name="txtMeta" class="form-control" value="<?php echo $formula ?>"readonly >
					</div>
					<div class="form-group">
						<label>Artículo</label>
						<select id="delete_txtArticulo" name="txtArticulo" class="form-control"readonly>
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloArticulos = $objcontrolArticulos->listar(); ?>
							<?php foreach ($arregloArticulos as $ar): ?>
								<option value="<?php echo $ar->__get('id') ?>">
									<?= $ar->__get('nombre') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Literal</label>
						<select id="delete_txtLiteral" name="txtLiteral" class="form-control"readonly>
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloLiteral = $objcontrolLiteral->listar(); ?>
							<?php foreach ($arregloLiteral as $li): ?>
								<option value="<?php echo $li->__get('id') ?>">
									<?= $li->__get('descripcion') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Numeral</label>
						<select id="delete_txtNumeral" name="txtNumeral" class="form-control" readonly>
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloNumeral = $objcontrolNumeral->listar(); ?>
							<?php foreach ($arregloNumeral as $num): ?>
								<option value="<?php echo $num->__get('id') ?>">
									<?= $num->__get('descripcion') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Parágrafo</label>
						<select id="delete_txtParagrafo" name="txtParagrafo" class="form-control"readonly>
							<option value="" selected disabled>Seleccionar</option>
							<?php $arregloParagrafo = $objcontrolParagrafo->listar(); ?>
							<?php foreach ($arregloParagrafo as $par): ?>
								<option value="<?php echo $par->__get('id') ?>">
									<?= $par->__get('descripcion') ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
                                    <div class="form-group">
                                        <input type="submit" id="btnBorrar" name="bt" class="btn btn-warning"
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
		var codigo = button.data('codigo');
        var nombre = button.data('nombre');
		var objetivo = button.data('objetivo');
		var alcance = button.data('alcance');
		var formula = button.data('formula');
		var fkidtipoindicador = button.data('fkidtipoindicador');
		var  fkidunidadmedicion= button.data('fkidunidadmedicion');
		var fkidsentido = button.data('fkidsentido');
		var fkidfrecuencia = button.data('fkidfrecuencia');
		var meta = button.data('meta');
		var fkidarticulo = button.data('fkidarticulo');
		var fkidliteral = button.data('fkidliteral');
		var fkidnumeral = button.data('fkidnumeral');
		var fkidparagrafo = button.data('fkidparagrafo');
        var modal = $(this);
        modal.find('#edit_txtId').val(id);
		modal.find('#edit_txtCodigo').val(codigo);
        modal.find('#edit_txtNombre').val(nombre);
		modal.find('#edit_txtobjetivo').val(objetivo);
		modal.find('#edit_txtAlcance').val(alcance);
		modal.find('#edit_txtFormula').val(formula);
		modal.find('#edit_txtTipo').val(fkidtipoindicador);
		modal.find('#edit_txtUnidad').val(fkidunidadmedicion);
		modal.find('#edit_txtSentido').val(fkidsentido);
		modal.find('#edit_txtFrecuencia').val(fkidfrecuencia);
		modal.find('#edit_txtMeta').val(meta);
		modal.find('#edit_txtArticulo').val(fkidarticulo);
		modal.find('#edit_txtLiteral').val(fkidliteral);
		modal.find('#edit_txtNumeral').val(fkidnumeral);
		modal.find('#edit_txtParagrafo').val(fkidparagrafo);
    });

 $('#borrar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
          var button = $(event.relatedTarget);
        var id = button.data('id');
		var codigo = button.data('codigo');
        var nombre = button.data('nombre');
		var objetivo = button.data('objetivo');
		var alcance = button.data('alcance');
		var formula = button.data('formula');
		var fkidtipoindicador = button.data('fkidtipoindicador');
		var  fkidunidadmedicion= button.data('fkidunidadmedicion');
		var fkidsentido = button.data('fkidsentido');
		var fkidfrecuencia = button.data('fkidfrecuencia');
		var meta = button.data('meta');
		var fkidarticulo = button.data('fkidarticulo');
		var fkidliteral = button.data('fkidliteral');
		var fkidnumeral = button.data('fkidnumeral');
		var fkidparagrafo = button.data('fkidparagrafo');
        var modal = $(this);
        modal.find('#delete_txtId').val(id);
		modal.find('#delete_txtCodigo').val(codigo);
        modal.find('#delete_txtNombre').val(nombre);
		modal.find('#delete_txtobjetivo').val(objetivo);
		modal.find('#delete_txtAlcance').val(alcance);
		modal.find('#delete_txtFormula').val(formula);
		modal.find('#delete_txtTipo').val(fkidtipoindicador);
		modal.find('#delete_txtUnidad').val(fkidunidadmedicion);
		modal.find('#delete_txtSentido').val(fkidsentido);
		modal.find('#delete_txtFrecuencia').val(fkidfrecuencia);
		modal.find('#delete_txtMeta').val(meta);
		modal.find('#delete_txtArticulo').val(fkidarticulo);
		modal.find('#delete_txtLiteral').val(fkidliteral);
		modal.find('#delete_txtNumeral').val(fkidnumeral);
		modal.find('#delete_txtParagrafo').val(fkidparagrafo);
    });



  
</script>

<?php
ob_end_flush();
?>