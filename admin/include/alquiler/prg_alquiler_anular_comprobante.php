<?php
session_start();
include "../../config.php";
include "../functions.php";

//--------------------------------------------------------------
$idalquiler = $_POST['txtidalquiler'];
$tipoventa = $_POST['txttipoventa'];
$txtmotivo = mayuscula($_POST['txtmotivo']);
//*****************************************************

if($tipoventa=='1'){
	$consulta="update al_venta set	
		anulado = 1,
		anulado_motivo = '$txtmotivo'
		where idalquiler = '$idalquiler'";

	if($mysqli->query($consulta)){
			$Men = "Los datos fueron guardados satisfactoriamente.";
	}else{
			$Men = "Ha fallado, los datos no han sido registrados.";
	}
}else{
	$consulta="update venta set	
		anulado = 1,
		anulado_motivo = '$txtmotivo'
		where idventa = '$idalquiler'";

	if($mysqli->query($consulta)){
			$Men = "Los datos fueron guardados satisfactoriamente.";
	}else{
			$Men = "Ha fallado, los datos no han sido registrados.";
	}

}

$mysqli->close();	
$_SESSION['msgerror'] = $Men;
header("Location: ../../resumendiario.php"); exit;
//************************************************************
?>