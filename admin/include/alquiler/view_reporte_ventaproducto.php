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
  	v.numero,
  	v.cliente,
    vd.nombre,
  	v.fecha,
  	v.hora,
    vd.cantidad,
    vd.precio,
  	(vd.importe) as total,
    IF(vd.idproducto > 0 ,'PRODUCTO','SERVICIO') as tipo
   
FROM venta v
INNER JOIN ventadetalle vd
ON v.idventa = vd.idventa
  WHERE DATE(v.fecha) between '".$newfecha1."' and '".$newfecha2."'     
  ORDER BY v.idventa
");

$sqlcuentas->execute(); // Execute the statement.
$result = $sqlcuentas->get_result();

$results = $result->fetch_all(MYSQLI_ASSOC);
$arreglo["data"] = $results;
echo json_encode($arreglo);

$mysqli->close();

//************************************************************
?>
