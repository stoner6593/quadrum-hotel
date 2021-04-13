<?php
session_start();
//include "validar.php";
include "config.php";

 $xidalquiler=$_GET['idalquiler'];
 $xidhuesped=$_GET['id'];

	$sqlhuesped = $mysqli->query("UPDATE al_venta SET idhuesped=$xidhuesped WHERE idalquiler=$xidalquiler ");

	if($sqlhuesped){

		$ArrayMessage=array('success'=>"Cliente Actualizado",'errors'=>0);
							

	}else{

		$ArrayMessage=array('success'=>0,'errors'=>"Error al actualizar cliente");
	}

	echo  json_encode($ArrayMessage);

?>