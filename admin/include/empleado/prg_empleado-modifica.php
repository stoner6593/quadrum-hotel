<?php
session_start();
include "../../config.php";
include "../functions.php";
//*****************************************************
$xidusuario = $_POST['txtidusuario'];

$xtxtnombre = mayuscula($_POST['txtNombres']);
$xtxtappaterno = mayuscula($_POST['txtApPaterno']);
$xtxtapmaterno = mayuscula($_POST['txtApMaterno']);
$xtxtdocumento = $_POST['txtdocumento'];
$xtxtSexo = $_POST['txtSexo'];
$xtxtCargo = $_POST['txtCargo'];
$xtxtTipoDocumento = $_POST['tipo_documento'];
$xtxtEmail = $_POST['txtEmail'];
$xestado = 1;



	$consulta="UPDATE empleado
SET
emp_apaterno = '$xtxtappaterno',
emp_amaterno = '$xtxtapmaterno',
emp_nombres = '$xtxtnombre',
id_doc_identidad = $xtxtTipoDocumento,
nro_doc_identidad = '$xtxtdocumento',
emp_sexo = '$xtxtSexo',
emp_email = '$xtxtEmail',
cargo_id = $xtxtCargo,
emp_estado = $xestado
WHERE idempleado = $xidusuario;";


if($mysqli->query($consulta)){
	$Men = "Los datos fueron guardados satisfactoriamente.";
}else{
	$Men = "Ha fallado, los datos no han sido actualizados.";
}
	
$mysqli->close();
$_SESSION['msgerror'] = $Men;
header("Location: ../../empleado.php"); exit;
//************************************************************
?>