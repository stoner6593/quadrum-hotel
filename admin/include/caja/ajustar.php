<?php
session_start();
include "../../config.php";
include "../functions.php";
include "../Configuraciones.php";
date_default_timezone_set('America/Lima');

$consulta = "
	insert ingresoturno_ajuste (
		id_ingresoturno,
		fecha_hora,
		id_usuario,
		totalhabitacion,
		totalproducto,
		totalefectivo,
		totalvisa,
		totalhabitacion_new,
		totalproducto_new,
		totalefectivo_new,
		totalvisa_new,
		totalmastercard,
		totalmastercard_new
	)values(
		'".$_POST["id-turno"]."',
		'".date("Y-m-d H:i:s")."',
		'".$_POST["id-usuario"]."',
		'".$_POST["totalhabitacion-h"]."',
		'".$_POST["totalproducto-h"]."',
		'".$_POST["totalefectivo-h"]."',
		'".$_POST["totalvisa-h"]."',
		'".$_POST["totalhabitacion"]."',
		'".$_POST["totalproducto"]."',
		'".$_POST["totalefectivo"]."',
		'".$_POST["totalvisa"]."',
		0,
		0
	)";
		
if($mysqli->query($consulta) == 1){

	$consultaact="update ingresosturno set
			totalhabitacion = '".$_POST["totalhabitacion"]."',
			totalproducto = '".$_POST["totalproducto"]."',
			totalefectivo = '".$_POST["totalefectivo"]."',
			totalvisa = '".$_POST["totalvisa"]."'
			where idturno = '".$_POST["id-turno"]."'";
			
	if($mysqli->query($consultaact) == 1){
		header("Location: ../../ajustarCaja.php?exito=1"); 	
	}else{
		printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
	}	
}else{
	printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
}