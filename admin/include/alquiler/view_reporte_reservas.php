<?php
session_start();
include "../../config.php";
include "../functions.php";

//--------------------------------------------------------------
$concatena='';

$finicio=($_POST['finicio']);
$ffin=($_POST['ffin']);

$f1=explode("/",$finicio);
$newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];

$f2=explode("/",$ffin);
$newfecha2=$f2[2].'-'.$f2[1].'-'.$f2[0];



$sqlcuentas = $mysqli->prepare("
  SELECT 
  CASE d.tiporeserva
	WHEN 1 THEN 'Booking'
	WHEN 2 THEN 'Reserva Web'
	WHEN 3 THEN 'Expedia'
	WHEN 4 THEN 'Facebook'
	WHEN 5 THEN 'Crédito'
	WHEN 6 THEN 'Depósito'
	WHEN 7 THEN 'Pago en linea'
	ELSE 'Sin definir' END
  	 as tipo,
  	count(d.tiporeserva) as total  	
FROM al_venta_detalle d
INNER JOIN al_venta v ON d.idalquiler = v.idalquiler
  WHERE DATE(v.fecharegistro) between '".$newfecha1."' and '".$newfecha2."'     
  GROUP BY tipo ORDER BY d.tiporeserva
");

$sqlcuentas->execute(); // Execute the statement.
$result = $sqlcuentas->get_result();

$results = $result->fetch_all(MYSQLI_ASSOC);
$arreglo["data"] = $results;
echo json_encode($arreglo);

$mysqli->close();

//************************************************************
?>
