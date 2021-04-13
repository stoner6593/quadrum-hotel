<?php
session_start();
include "../../config.php";
include "../functions.php";

//--------------------------------------------------------------
$xidprimario = $_POST['txtidprimario'];

$txtfecha = $_POST['txtfecha'];
$txttipo = $_POST['txttipo'];
$txtdescripcion = mayuscula($_POST['txtdescripcion']);

$txtpreciodia = $_POST['txtpreciodia'];
$txthoraespecial1 = $_POST['txthoraespecial1'];
$txthoraespecial2 = $_POST['txthoraespecial2'];
$txthoradic = $_POST['txthoradic'];
$txthorahuespedadic = $_POST['txthorahuespedadic'];
//*****************************************************

	
$consulta="update tarifa_especial set
	
	descripcion_tarifa = '$txtdescripcion',
	fecha_tarifa = '$txtfecha',
	idtipo = '$txttipo',
	precio_dia = '$txtpreciodia',
	precio_hora_1 = '$txthoraespecial1',
	precio_hora_2 = '$txthoraespecial2',
	precio_hora_adicional = '$txthoradic',
	precio_huesped_adicional = '$txthorahuespedadic'

	where id_tarifa = '$xidprimario'";

if($mysqli->query($consulta)){
		$Men = "Los datos fueron guardados satisfactoriamente.";
}else{
		$Men = "Ha fallado, los datos no han sido actualizados.";
}
$mysqli->close();	
$_SESSION['msgerror'] = $Men;
header("Location: ../../tarifa-especial.php"); exit;
//************************************************************
?>