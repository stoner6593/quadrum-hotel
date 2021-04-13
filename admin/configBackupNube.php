<?php

	global $dbremoto;
    $dbremoto = new mysqli("electronperu.com:3306", "electron_factec", "usermysqlfe741", "electron_gestor_comprobantes");
	if ($dbremoto->connect_errno) {
		echo "Fallo al conectar a MySQL: (" . $dbremoto->connect_errno.") " . $dbremoto->connect_error;
	}

	$acentos = $dbremoto->query("SET NAMES 'utf8'");

?>