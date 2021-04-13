<?php
session_start();
include "../../config.php";
include "../functions.php";
include "../Configuraciones.php";
date_default_timezone_set('America/Lima');

$consulta = "
	UPDATE hab_venta 
	SET ".$_POST["campo"]."='".$_POST["valor"]."' 
	WHERE idhabitacion='".$_POST["idHab"]."'";
		
if($mysqli->query($consulta) == 1){
	echo json_encode(array("success" => true));	
}else{
	echo json_encode(array("success" => false));
}
