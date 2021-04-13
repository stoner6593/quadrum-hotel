<?php
session_start();
include "../../config.php";
include "../functions.php";

//--------------------------------------------------------------
$idhabitacion = $_GET['$idhabitacion'];

	
$consulta="update hab_venta set	
	idestado = 1,
	idalquiler = 0	
	where idhabitacion = '$idhabitacion' ";

if($mysqli->query($consulta)){
		$Men = "Los datos fueron guardados satisfactoriamente.";
}else{
		$Men = "Ha fallado, los datos no han sido registrados.";
}
$mysqli->close();	
$_SESSION['msgerror'] = $Men;
header("Location: ../../habitaciones.php"); exit;
//************************************************************
?>