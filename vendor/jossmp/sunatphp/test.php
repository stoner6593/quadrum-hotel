<?php
require_once('./src/autoload.php');


$sunat = new \Sunat\ruc( );



$documento = $_POST['documento'];
$tipo_documento = $_POST['tipo_documento'];

if($tipo_documento ==1 ){
    
    $search2=($sunat->consulta($documento));

    if ($search2->success == true) {
		
		echo $search2->json(NULL, true);

	}else{
        echo json_encode (['success' => false,'message' => 'No hay registros para estos filtros']);
    }
}

//$search1 = $sunat->consulta($ruc);


?>