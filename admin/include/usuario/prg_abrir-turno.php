<?php
session_start();
include "../../config.php";
include "../functions.php";

//*****************************************************
$TblMax = $mysqli->query("select max(idturno) from ingresosturno");
$Contador = $TblMax->fetch_row();

$xidturno = $Contador['0'] + 1 ;
$xidusuario = $_GET['idusuario'];
$xturno = $_POST['txtturno'];

$xfechaapertura = date("Y-m-d H:i:s");
$xestadoturno = 1;

$consulta = "insert ingresosturno(
	idturno,
	totalhabitacion,
	totaladicional,
	totalproducto,
	totalefectivo,
	totalvisa,
	totalmastercard,
	idusuario,
	fechaapertura,
	fechacierre,
	estadoturno,
	totalcompras,
	totalgastos,
	turno,
	totalmastercardanulado

	) values (
	'$xidturno',
	'0.00',
	'0.00',
	'0.00',
	'0.00',
	'0.00',
	'0.00',
	'$xidusuario',
	'$xfechaapertura',
	'1900-01-01',
	'$xestadoturno',
	'0.00',
	'0.00',
	'$xturno',
	'0.00'
	
	)";
	
	if($mysqli->query($consulta) == 1){
			$_SESSION['estadomenu'] = 1;
			$_SESSION['idturno'] = $xidturno;
			
			//Actualizar Saldos en productos
			$consultapro="update producto set
				inicialturno = cantidad,
				vendidoturno = 0,
				compradoturno = 0
				";
			if($mysqli->query($consultapro)){}		
			//$Men = "Los datos fueron guardados satisfactoriamente.";
	}else{
		printf("Errormessage: %s\n", $mysqli->error); exit;
	}
	
$TblMax->free();	
$mysqli->close();
$_SESSION['msgerror'] = $Men;
header("Location: ../../control-habitaciones.php"); exit;
//************************************************************
?>