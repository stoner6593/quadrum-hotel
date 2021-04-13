<?php
session_start();
include "../../config.php";

$consulta="update cliente set	
	
	tipo_documento='".$_POST["tipo_documento"]."'

	where idhuesped = '".$_POST["idcliente"]."'";

if($mysqli->query($consulta)){
		echo "Los datos fueron guardados satisfactoriamente.";
}else{
		echo "Ha fallado, los datos no han sido registrados.";
}

$mysqli->close();	