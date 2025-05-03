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


// Procesamiento CRUD
$boton = $_POST['bt'] ?? '';
$id = $_POST['txtId'] ?? '';
$nombre = $_POST['txtNombre'] ?? '';
$objetivo = $_POST['txtObjetivo'] ?? '';
$alcance = $_POST['txtAlcance'] ?? '';
$formula = $_POST['txtFormula'] ?? '';
$meta = $_POST['txtMeta'] ?? '';
$tipo = $_POST['txtTipo'] ?? '';
$unidad = $_POST['txtUnidad'] ?? '';
$sentido = $_POST['txtSentido'] ?? '';
$frecuencia = $_POST['txtFrecuencia'] ?? '';
$articulo = $_POST['txtArticulo'] ?? '';
$literal = $_POST['txtLiteral'] ?? '';
$numeral = $_POST['txtNumeral'] ?? '';
$paragrafo = $_POST['txtParagrafo'] ?? '';

$control = new ControlEntidad('indicador');
$arregloIndicadoresConsulta = [];
$objcontrolIndicador = new ControlEntidad('indicador');
$arregloIndicadores = $objcontrolIndicador->listar();
foreach ($arregloIndicadores as $ind) {
    $arregloIndicadores[$ind->__get('id')] = $ind->__get('nombre');
}

switch($boton){
    case 'Guardar':
        $datos = compact('id', 'nombre', 'objetivo', 'alcance', 'formula', 'meta',
            'fkidtipoindicador', 'unidad', 'sentido', 'frecuencia', 'articulo', 'literal', 'numeral', 'paragrafo');
        $control->guardar(new Entidad($datos));
        break;
    case 'Modificar':
        $datos = compact('id', 'nombre', 'objetivo', 'alcance', 'formula', 'meta',
            'fkidtipoindicador', 'unidad', 'sentido', 'frecuencia', 'articulo', 'literal', 'numeral', 'paragrafo');
        $control->modificar(new Entidad($datos), 'id', $id);
        break;
    case 'Borrar':
        $control->borrar('id', $id);
        break;
    case 'Consultar':
        $obj = $control->consultar('id', $id);
        if ($obj) {
            $nombre = $obj->__get('nombre');
            $objetivo = $obj->__get('objetivo');
            $alcance = $obj->__get('alcance');
            $formula = $obj->__get('formula');
            $meta = $obj->__get('meta');
            $tipo = $obj->__get('idtipoindicador');
            $unidad = $obj->__get('idunidadmedicion');
            $sentido = $obj->__get('idsentido');
            $frecuencia = $obj->__get('idfrecuencia');
            $articulo = $obj->__get('idarticulo');
            $literal = $obj->__get('idliteral');
            $numeral = $obj->__get('idnumeral');
            $paragrafo = $obj->__get('idparagrafo');
        }
        break;
}

$lista = $control->listar();
?>

<?php include "../vista/base_ini_head.html" ?>
<?php include "../vista/base_ini_body.html" ?>

<div class="container-xl">
    <div class="table-responsive">
        <div class="table-wrapper">
            <div class="table-title">
                <h2>Gestión <b>Indicadores</b></h2>
            </div>
            <form method="post">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Objetivo</th>
                            <th>Alcance</th>
                            <th>Fórmula</th>
                            <th>Meta</th>
                            <th>Tipo</th>
                            <th>Unidad</th>
                            <th>Sentido</th>
                            <th>Frecuencia</th>
                            <th>Artículo</th>
                            <th>Literal</th>
                            <th>Numeral</th>
                            <th>Parágrafo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista as $item): ?>
                            <tr>
                                <td><?= $item->__get('nombre') ?></td>
                                <td><?= $item->__get('objetivo') ?></td>
                                <td><?= $item->__get('alcance') ?></td>
                                <td><?= $item->__get('formula') ?></td>
                                <td><?= $item->__get('meta') ?></td>
                                <td><?= $arregloTipo[$item->__get('idtipoindicador')] ?? 'Desconocido' ?></td>
                                <td><?= $arregloUnidad[$item->__get('idunidadmedicion')] ?? 'Desconocido' ?></td>
                                <td><?= $arregloSentido[$item->__get('idsentido')] ?? 'Desconocido' ?></td>
                                <td><?= $arregloFrecuencia[$item->__get('idfrecuencia')] ?? 'Desconocido' ?></td>
                                <td><?= $arregloArticulo[$item->__get('idarticulo')] ?? 'Desconocido' ?></td>
                                <td><?= $arregloLiteral[$item->__get('idliteral')] ?? 'Desconocido' ?></td>
                                <td><?= $arregloNumeral[$item->__get('idnumeral')] ?? 'Desconocido' ?></td>
                                <td><?= $arregloParagrafo[$item->__get('idparagrafo')] ?? 'Desconocido' ?></td>
                                <td>
                                    <button name="bt" value="Consultar" class="btn btn-info btn-sm">Consultar</button>
                                    <button name="bt" value="Modificar" class="btn btn-warning btn-sm">Modificar</button>
                                    <button name="bt" value="Borrar" class="btn btn-danger btn-sm">Borrar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- Aquí puedes agregar el formulario modal de edición si lo necesitas -->
            </form>
        </div>
    </div>
</div>

<?php include "../vista/basePie.html" ?>
<?php ob_end_flush(); ?>
