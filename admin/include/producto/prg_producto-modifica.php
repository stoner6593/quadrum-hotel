<?php
session_start();
include "../../config.php";
include "../functions.php";

//--------------------------------------------------------------
$xidprimario = $_POST['txtidprimario'];

$txtcodigo = mayuscula($_POST['txtcodigo']);
$txtnombre = mayuscula($_POST['txtnombre']);
$txtcantidad = isset($_POST['txtcantidad']) ? $_POST['txtcantidad'] : 0;
$txtcantidadminima = isset($_POST['txtcantidadminima']) ? $_POST['txtcantidadminima'] : 0 ;
$txtprecio = isset($_POST['txtprecio']) ? $_POST['txtprecio'] : 0;
$txtprecioventa = $_POST['txtprecioventa'];
$txtprecioventapersonal = isset($_POST['txtprecioventapersonal']) ? $_POST['txtprecioventapersonal'] : 0;
$txtdescripcion = mayuscula($_POST['txtdescripcion']);
//*****************************************************

	
$consulta="update producto set
	
	codigo = '$txtcodigo',
	nombre = '$txtnombre',
	cantidad = '$txtcantidad',
	cantidadminima = '$txtcantidadminima',
	precio = '$txtprecio',
	precioventa = '$txtprecioventa',
	descripcion = '$txtdescripcion',
	preciopersonal = '$txtprecioventapersonal'

	where idproducto = '$xidprimario'";

if($mysqli->query($consulta)){
		$Men = "Los datos fueron guardados satisfactoriamente.";
}else{
		$Men = "Ha fallado, los datos no han sido actualizados.";
}
$mysqli->close();	
$_SESSION['msgerror'] = $Men;
header("Location: ../../productos.php"); exit;
//************************************************************
?>