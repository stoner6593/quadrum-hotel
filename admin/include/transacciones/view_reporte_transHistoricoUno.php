<?php
session_start();
include "../../config.php";
include "../functions.php";

$sqlVentasTurno = $mysqli->prepare("
  SELECT 
    v.idalquiler + 1000 as orden,
    (SELECT hv.numero FROM hab_venta hv WHERE hv.idhabitacion = v.idhabitacion) as habitacion,
    (SELECT c.nombre FROM cliente c WHERE c.idhuesped = v.idhuesped) as huesped,
    v.total as total
  FROM al_venta v WHERE v.idturno = '".$_POST["idTurno"]."'
");

$sqlVentasTurno->execute(); // Execute the statement.
$resultVT = $sqlVentasTurno->get_result();
$resultsVT = $resultVT->fetch_all(MYSQLI_ASSOC);

$sqlVentasProdTurno = $mysqli->prepare("
  SELECT 
    v.numero as orden,
    v.cliente as huesped,
    v.total as total
  FROM venta v WHERE v.idturno = '".$_POST["idTurno"]."'
");

$sqlVentasProdTurno->execute(); // Execute the statement.
$resultPT = $sqlVentasProdTurno->get_result();
$resultsPT = $resultPT->fetch_all(MYSQLI_ASSOC);

$sql = $mysqli->prepare("
  SELECT it.*, u.user_nombre FROM ingresosturno it 
  LEFT JOIN usuario u ON it.idusuario = u.user_id
  WHERE it.idturno = '".$_POST["idTurno"]."'
");

$sql->execute(); // Execute the statement.
$result = $sql->get_result();
$results = $result->fetch_all(MYSQLI_ASSOC);


$arreglo["data"] = $results;
$arreglo["dataVT"] = $resultsVT;
$arreglo["dataPT"] = $resultsPT;

$arreglo["success"] = true;
$arreglo["msg"] = "ok";
echo json_encode($arreglo);

$mysqli->close();
