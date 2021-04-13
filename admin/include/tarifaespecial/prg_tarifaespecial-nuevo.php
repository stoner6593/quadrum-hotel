<?php
session_start();
include "../../config.php";
include "../functions.php";

//--------------------------------------------------------------

$txtfecha = $_POST['txtfecha'];
$txttipo = $_POST['txttipo'];
$txtdescripcion = mayuscula($_POST['txtdescripcion']);

$txtpreciodia = isset($_POST['txtpreciodia']) ? $_POST['txtpreciodia'] : 0;
$txthoraespecial1 = isset($_POST['txthoraespecial1']) ? $_POST['txthoraespecial1'] : 0;
$txthoraespecial2 = isset($_POST['txthoraespecial2']) ? $_POST['txthoraespecial2'] : 0;
$txthoradic = isset($_POST['txthoradic']) ? $_POST['txthoradic'] : 0;
$txthorahuespedadic = isset($_POST['txthorahuespedadic']) ? $_POST['txthorahuespedadic'] : 0;

$xestado = 1;





//*****************************************************

$consulta="INSERT INTO tarifa_especial
(
descripcion_tarifa,
fecha_tarifa,
idtipo,
precio_dia,
precio_hora_1,
precio_hora_2,
precio_hora_adicional,
precio_huesped_adicional)
VALUES (
	'$txtdescripcion',
	'$txtfecha',
	'$txttipo',
	
	$txtpreciodia,
	$txthoraespecial1,
	$txthoraespecial2,
	$txthoradic,
	$txthorahuespedadic		
	)";
	
if($mysqli->query($consulta)){
	$Men = "Los datos fueron guardados satisfactoriamente.";
}else{
	$Men = "Ha fallado, los datos no han sido registrados.";
}

$mysqli->close();	
$_SESSION['msgerror'] = $Men;
//header("Location: ../../tarifa-especial.php"); exit;
//--------------------------------------------------------------
?>