<?php
session_start();
include "../../config.php";
include "../functions.php";

//*****************************************************
//$TblMax = $mysqli->query("select max(user_id) from usuario");
//$Contador = $TblMax->fetch_row();
//$xidusuario = $Contador['0'] + 1 ;

$xtxtnombre = mayuscula($_POST['txtNombres']);
$xtxtappaterno = mayuscula($_POST['txtApPaterno']);
$xtxtapmaterno = mayuscula($_POST['txtApMaterno']);
$xtxtdocumento = $_POST['txtdocumento'];
$xtxtSexo = $_POST['txtSexo'];
$xtxtCargo = $_POST['txtCargo'];
$xtxtTipoDocumento = $_POST['tipo_documento'];
$xtxtEmail = $_POST['txtEmail'];
$xestado = 1;

$consulta="INSERT INTO empleado
    (emp_apaterno,emp_amaterno,emp_nombres,id_doc_identidad,
    nro_doc_identidad,emp_sexo,emp_email,cargo_id,emp_estado)
	values (
	'$xtxtappaterno',
	'$xtxtapmaterno',
	'$xtxtnombre',
	$xtxtTipoDocumento,
	'$xtxtdocumento',
	'$xtxtSexo',
	'$xtxtEmail',
	$xtxtCargo,
	$xestado
	)";

	if($mysqli->query($consulta)){
			$Men = "Los datos fueron guardados satisfactoriamente.";
	}else{
			$Men = "Ha fallado, los datos no han sido registrados.";
	}

$mysqli->close();
$_SESSION['msgerror'] = $Men;
header("Location: ../../empleado.php"); exit;
//************************************************************
?>