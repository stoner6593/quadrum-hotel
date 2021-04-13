<?php
session_start();
include "../../config.php";
include "../functions.php";

//--------------------------------------------------------------

$xidalquiler = $_GET['idalquiler'];
$xidhabitacion = $_GET['idhabitacion'];

$xfecharealsalidahuesped = $_GET['fechasalidahuesped'];
$fh=explode(" ",$xfecharealsalidahuesped);
$f1=explode("-",$fh[0]);
$newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];
//print_r ($xFechaDiaAdicional);
$finalfechahasta = $newfecha1." ".$fh[1].":00";

$consulta="update hab_venta set
	idestado = 4,
	ultimocambio = now(),
	idalquiler = 0
	where idhabitacion = '$xidhabitacion'";

if($mysqli->query($consulta)){
		$Men = "Los datos fueron guardados satisfactoriamente.";
		$consultaalq = "update al_venta set
			estadoalquiler = 0,
			fecharealsalidahuesped = '$finalfechahasta',
			fecharealsalida = now()
			where idalquiler = '$xidalquiler'";
		if($mysqli->query($consultaalq)){}
}else{
		$Men = "Ha fallado, los datos no han sido registrados.";
}
$mysqli->close();	
//$_SESSION['msgerror'] = $Men;
header("Location: ../../control-habitaciones.php"); exit;
//************************************************************
?>