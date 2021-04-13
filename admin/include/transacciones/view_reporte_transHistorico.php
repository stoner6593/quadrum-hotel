<?php
session_start();
include "../../config.php";
include "../functions.php";

//--------------------------------------------------------------
$concatena='';

$finicio=($_POST['fechaDesde']);
$ffin=($_POST['fechaHasta']);

$f1=explode("/",$finicio);
$newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];

$f2=explode("/",$ffin);
$newfecha2=$f2[2].'-'.$f2[1].'-'.$f2[0];


$sql = $mysqli->prepare("
  SELECT 
	it.idturno,
	(SELECT u.user_nombre FROM usuario u WHERE u.user_id = it.idusuario) as idusuario,
	turno,
	fechaapertura,
	fechacierre,
	totalhabitacion,
	totalproducto,
	totaldescuento
  FROM ingresosturno it
  WHERE DATE(it.fechaapertura) between '".$newfecha1."' and '".$newfecha2."' 
");

$sql->execute(); // Execute the statement.
$result = $sql->get_result();

$results = $result->fetch_all(MYSQLI_ASSOC);
$arreglo["data"] = $results;
$arreglo["success"] = true;
$arreglo["msg"] = "ok";
echo json_encode($arreglo);

$mysqli->close();
