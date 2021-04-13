<?php
session_start();
include "../../config.php";
include "../functions.php";

$nombrecliente = $_GET['nombrecliente'];
$idhabitacion = $_GET['idhabitacion'];
$idalquiler = $_GET['idalquiler'];
$idcliente = $_GET['idcliente'];
$idtipo = $_POST['cboTipoHuesped'];

$txttipodoc = $_POST['txttipodoc'];
$sexo = $_POST['sexo'];
$txtnacionalidad = $_POST['txtnacionalidad'];


//--------------------------------------------------------------
$TblMax = $mysqli->query("select max(idpersona) from al_venta_personaadicional");
$Contador = $TblMax->fetch_row();
$xidprimario = $Contador['0'] + 1 ;

$xnombre = $_POST['txtnombre'];
$xdni = $_POST['txtdni'];
$xnacimiento = Cfecha($_POST['txtnacimiento']);

$consulta="insert al_venta_personaadicional (
	idpersona,
	idalquiler,
	idcliente,
	nombre,
	dni,
	nacimiento,
	id_tipo,

	tipo_doc,
	sexo,
	nacionalidad

	) values (

	'$xidprimario',
	'$idalquiler',
	'$idcliente',
	'$xnombre',
	'$xdni',
	'$xnacimiento',
	'$idtipo',

	'$txttipodoc',
	'$sexo',
	'$txtnacionalidad'
)";
if($mysqli->query($consulta)){}



$mysqli->close();
$_SESSION['msgerror'] = $Men;
header("Location: ../../persona-adicional.php?idhabitacion=$idhabitacion&idalquiler=$idalquiler&idcliente=3"); exit;
//************************************************************
?>
