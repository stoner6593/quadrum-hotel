<?php
session_start();
include "../../config.php";
include "../functions.php";

//--------------------------------------------------------------
$txtidhabitacion = $_POST['txtidhabitacion'];
$cmbEmpleado = $_POST['cmbEmpleado'];
$txtFechaInicio = $_POST['txtFechaInicio'];
//$txtFechaFin = $_POST['txtFechaFin'];
$cmbTipoM = $_POST["cmbTipoM"];

$fh=explode(" ",$txtFechaInicio);
$f1=explode("/",$fh[0]);
$newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];
//print_r ($xFechaDiaAdicional);
$fechaInicio = $newfecha1." ".$fh[1].":00";

$fh=explode(" ",$txtFechaFin);
$f1=explode("/",$fh[0]);
$newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];
//print_r ($xFechaDiaAdicional);
$fechaFin = $newfecha1." ".$fh[1].":00";

$txtobs = mayuscula($_POST['txtobs']);
//*****************************************************

$insertMant="INSERT INTO hab_mantenimiento(idhabitacion,idempleado,fecha_inicio,fecha_fin,idtipo,observacion)
VALUES('$txtidhabitacion','$cmbEmpleado','$fechaInicio',now(),'$cmbTipoM','$txtobs');";

$mysqli->query($insertMant);

$consulta="update hab_venta set	
		idestado = 1,
		idalquiler = 0
		where idhabitacion = '$txtidhabitacion'";

if($mysqli->query($consulta)){
    $Men = "Los datos fueron guardados satisfactoriamente.";
}else{
    $Men = "Ha fallado, los datos no han sido registrados.";
}


$mysqli->close();	
$_SESSION['msgerror'] = $Men;
header("Location: ../../control-mantenimiento.php"); exit;
//************************************************************
?>