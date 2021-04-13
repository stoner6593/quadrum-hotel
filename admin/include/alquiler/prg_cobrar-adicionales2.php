<?php
session_start();
include "../../config.php";
include "../functions.php";
echo "<pre>"; print_r($_GET);
//--------------------------------------------------------------
$xidhabitacion = $_GET['idhabitacion'];
$xidalquiler = $_GET['idalquiler'];

$formapago1 = $_GET['formapago1']; //Visa
$formapago2 = $_GET['formapago2']; //Efectivo
$formapago3 = $_GET['formapago3']; //Master Card
$idalquilerdetalle = $_GET['idalquilerdetalle'];

$xmonto1 = $_GET['monto1']; //Monto pago visa
$xmonto2 = $_GET['monto2']; //Monto pago Efectivo
$xmonto3 = $_GET['monto3']; //Monto pago Efectivo

if($formapago2 == "efectivo"){
	$xmontoefectivo = $_GET['monto2'];
	//$xmontovisa = 0;
	
}
if($formapago1 == "visa"){
	//$xmontoefectivo = 0;
	$xmontovisa = $_GET['monto1'];
}
if($formapago3 == "mastercard"){
	//$xmontoefectivo = 0;
	$xmontomastercard = $_GET['monto3'];
}


$xidturno = $_SESSION['idturno'];
$idusuario = $_SESSION['xyzidusuario'];


//Alquiler Adicional
$consulta="update al_venta_detalle set
	estadopago = '1',
	totalefectivo = '$xmontoefectivo',
	totalvisa = '$xmontovisa',
	
	idturno = '$xidturno',
	idusuario = '$idusuario'
	where idalquilerdetalle = '$idalquilerdetalle' and idalquiler = '$xidalquiler'";

if($mysqli->query($consulta) == 1){}else{
			printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
		}

 $xmonto=$xmontoefectivo + $xmontovisa;

$consultaturno = "update ingresosturno set
		totalhabitacion = totalhabitacion + $xmonto,
		
		totalefectivo = totalefectivo + $xmontoefectivo,
		totalvisa = totalvisa + $xmontovisa
		
		where idturno = '$xidturno'";
		if($mysqli->query($consultaturno) == 1){}else{
			printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
		}




$mysqli->close();	
$_SESSION['msgerror'] = $Men;

header("Location: ../../alquilar-detalle.php?idhabitacion=$xidhabitacion&idalquiler=$xidalquiler"); exit;
//************************************************************
?>