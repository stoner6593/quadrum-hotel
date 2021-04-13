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
    v.nrohabitacion as habitacion,
    (SELECT nombre FROM cliente c WHERE c.idhuesped = v.idhuesped) as huesped,
    d.total as monto,
    IF(d.cobrado = 1, 'Pagado', CONCAT('<button type=button onclick=formPagar(',d.idalquilerdetalle,')>Pagar</button>')) as cobrado,
    d.comentarioscredito as comentarios
  FROM al_venta_detalle d
  INNER JOIN al_venta v ON d.idalquiler = v.idalquiler 
  WHERE DATE(v.fecharegistro) between '".$newfecha1."' and '".$newfecha2."' 
    AND d.tiporeserva = 5
  ORDER BY v.idalquiler
");

$sqlcuentas->execute(); // Execute the statement.
$result = $sqlcuentas->get_result();

$results = $result->fetch_all(MYSQLI_ASSOC);
$arreglo["data"] = $results;
echo json_encode($arreglo);

$mysqli->close();

//************************************************************
?>
