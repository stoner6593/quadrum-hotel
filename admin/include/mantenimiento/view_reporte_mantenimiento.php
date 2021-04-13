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



/*if($finicio && $ffin){
    $concatena=' and DATE(a.fecharegistro) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
}else{
    $date = new DateTime();
    $finicio= $date->format('d/m/Y');
    $ffin= $date->format('d/m/Y');

    $f1=explode("/",$finicio);
    $newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];

    $f2=explode("/",$ffin);
    $newfecha2=$f2[2].'-'.$f2[1].'-'.$f2[0];

    $concatena=' and DATE(a.fecharegistro) between "'.$newfecha1.'" and "'.$newfecha2.'" ';
}*/

$sqlmantenimiento = $mysqli->prepare("
  SELECT
    m.hman_id,
    m.fecha_registro,
    (SELECT numero FROM hab_venta v WHERE v.idhabitacion = m.idhabitacion) as idhabitacion,
    (SELECT CONCAT(e.emp_nombres,' ',e.emp_apaterno,' ',e.emp_amaterno)
      FROM empleado e WHERE e.idempleado = m.idempleado) as idempleado,
    m.fecha_inicio,
    m.fecha_fin,
    (SELECT tipo FROM hab_mantenimiento_tipo t WHERE  t.id = m.idtipo) as idtipo,
    m.observacion
   FROM hab_mantenimiento m
  WHERE DATE(m.fecha_registro) between '".$newfecha1."' and '".$newfecha2."'
  ORDER BY m.hman_id");

$sqlmantenimiento->execute(); // Execute the statement.
$result = $sqlmantenimiento->get_result();

$results = $result->fetch_all(MYSQLI_ASSOC);
$arreglo["data"] = $results;
echo json_encode($arreglo);

$mysqli->close();

//************************************************************
?>
