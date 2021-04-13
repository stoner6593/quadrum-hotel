<?php
session_start();
include "../../config.php";
include "../functions.php";

//--------------------------------------------------------------
$TblMax = $mysqli->query("select max(idhabitacion) from hab_venta");
$Contador = $TblMax->fetch_row();
$xidprimario = $Contador['0'] + 1 ;

$xtxtpiso = $_POST['txtpiso'];
$xtxtnumero = $_POST['txtnumero'];
$xtxttipo = $_POST['txttipo'];
$xtxtestado = 1; //$_POST['txtestado'];
$xtxtpreciodiario = @$_POST['txtpreciodiario'];
$xtxtpreciohoras = @$_POST['txtpreciohoras'];

$xtxtnrohuespedes = $_POST['txtnrohuespedes'];
$xtxtnroadicional = $_POST['txtnroadicional'];

$xtxtcaracteristicas = mayuscula($_POST['txtcaracteristicas']);

$txtubicacion = isset($_POST['txtubicacion']) ? $_POST['txtubicacion'] : 0;

$txtpreciodiariodj = str_replace(',','',$_POST['txtpreciodiariodj']);
$txtpreciohorasdj = str_replace(',','',$_POST['txtpreciohorasdj']);
$txtpreciodiariovs = str_replace(',','',$_POST['txtpreciodiariovs']);
$txtpreciohorasvs = str_replace(',','',$_POST['txtpreciohorasvs']);

$txtpreciohoraadicional = str_replace(',','',$_POST['txtpreciohoraadicional']);
$txtpreciopersonaadicional = str_replace(',','',$_POST['txtpreciopersonaadicional']);
$txtprecio12=str_replace(',','',$_POST['txtprecio12']);
$txtprecio12vs=str_replace(',','',$_POST['txtprecio12vs']);

$txtpreciobooking=str_replace(',','',$_POST['txtpreciobooking']);
$txtprecioreservaweb=str_replace(',','',$_POST['txtprecioreservaweb']);


//Verificar Numero *******************************************
$sqlnumero = $mysqli->query("select idhabitacion, numero from hab_venta where numero = '$xtxtnumero'");
$num = $sqlnumero->num_rows;
if($num != 0){
	$_SESSION['msgerror'] = 'El número de habitación ya existe.';
	echo "<script type=\"text/javascript\">
           history.go(-1);
       </script>";
	exit;
}
//****

$consulta="insert hab_venta (
	
	idhabitacion,
	piso,
	numero,
	idtipo,
	
	nrohuespedes,
	nroadicional,
	caracteristicas,
	idestado,
	ubicacion,
	
	preciodiariodj,
	preciohorasdj,
	preciodiariovs,
	preciohorasvs,
	
	costopersonaadicional,
	costohoraadicional,
	precio12,
	precio12vs,
	preciobooking,
    precioreservaweb,
	idalquiler,
	costoingresoanticipado
			
) values (

	'$xidprimario',
	'$xtxtpiso',
	'$xtxtnumero',
	'$xtxttipo',

	'$xtxtnrohuespedes',
	'$xtxtnroadicional',
	'$xtxtcaracteristicas',
	'$xtxtestado',
	'$txtubicacion',
	
	'$txtpreciodiariodj',
	'$txtpreciohorasdj',
	'$txtpreciodiariovs',
	'$txtpreciohorasvs',
	
	'$txtpreciohoraadicional',
	'$txtpreciopersonaadicional',
	'$txtprecio12',
	'$txtprecio12vs',
	'$txtpreciobooking',
	'$txtprecioreservaweb',
	0,
	0
	
	)";
	
if($mysqli->query($consulta)){
	$Men = "Los datos fueron guardados satisfactoriamente.";
}else{
	$Men = "Ha fallado, los datos no han sido registrados.";
}

$mysqli->close();	
$_SESSION['msgerror'] = $Men;
header("Location: ../../habitaciones.php"); exit;
//--------------------------------------------------------------
?>