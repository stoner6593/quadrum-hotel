<?php
session_start();
include "../../config.php";
include "../functions.php";

//--------------------------------------------------------------
$TblMax = $mysqli->query("select max(idproducto) from producto");
$Contador = $TblMax->fetch_row();
$xidprimario = $Contador['0'] + 1 ;

$txtcodigo = mayuscula($_POST['txtcodigo']);
$txtnombre = mayuscula($_POST['txtnombre']);
$txtcantidad = isset($_POST['txtcantidad']) ? $_POST['txtcantidad'] : 0;
$txtcantidadminima = isset($_POST['txtcantidadminima']) ? $_POST['txtcantidadminima'] : 0 ;
$txtprecio = isset($_POST['txtprecio']) ? $_POST['txtprecio'] : 0;
$txtprecioventa = $_POST['txtprecioventa'];
$txtprecioventapersonal = isset($_POST['txtprecioventapersonal']) ? $_POST['txtprecioventapersonal'] : 0;
$txtdescripcion = mayuscula($_POST['txtdescripcion']);
$xestado = 1;





//*****************************************************

$consulta="insert producto (
	
	idproducto,
	codigo,
	nombre,
	cantidad,
	cantidadminima,
	precio,
	precioventa,
	descripcion,
	estado,
	preciopersonal,
	inicialturno ,
	vendidoturno,
	compradoturno,
	orden
		
) values (

	'$xidprimario',
	'$txtcodigo',
	'$txtnombre',
	'$txtcantidad',
	'$txtcantidadminima',
	'$txtprecio',
	'$txtprecioventa',
	'$txtdescripcion',
	'$xestado',
	'$txtprecioventapersonal',
	0,0,0,0

		
	)";
	
if($mysqli->query($consulta)){
	$Men = "Los datos fueron guardados satisfactoriamente.";
}else{
	$Men = "Ha fallado, los datos no han sido registrados.";
}

$mysqli->close();	
$_SESSION['msgerror'] = $Men;
header("Location: ../../productos.php"); exit;
//--------------------------------------------------------------
?>