<?php
session_start();
include "../../config.php";
include "../functions.php";

set_time_limit(1200);
//--------------------------------------------------------------
$concatena='';

$finicio=($_POST['finicio']);
$ffin=($_POST['ffin']);

$f1=explode("/",$finicio);
$newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];

$f2=explode("/",$ffin);
$newfecha2=$f2[2].'-'.$f2[1].'-'.$f2[0];



if($finicio && $ffin){
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
}
//echo $concatena; exit;
$sqllibrohuespedes = $mysqli->prepare("
    select 
  a.idalquiler,
    a.nrohabitacion,
  DATE_FORMAT(a.fecharegistro, '%d/%m/%Y' ) as dia_ingreso,
  DATE_FORMAT(a.fecharegistro, '%H:%i' ) as hora_ingreso,
  b.nombre,
  b.sexo, 
    
    '' as clase_huesped,
    tid.identificacion as tipo_documento,
  b.documento, 
    
    b.ocupacion,
  
    b.pais, 
    
    
  
  DATE_FORMAT(a.fecharealsalidahuesped, '%H:%i' ) as hora_retiro,
  EXTRACT(day FROM a.fecharealsalidahuesped) as dia_retiro,
  EXTRACT(month FROM a.fecharealsalidahuesped) as mes_retiro,
  EXTRACT(year FROM a.fecharealsalidahuesped) as anio_retiro,
  a.comentarios,
    a.nroorden,
    a.fecharegistro, 
    a.fecharealsalidahuesped,
  if(a.documento is null,CONCAT('TK-','',a.nroorden),a.documento) as documento_cp,
    a.codigo_respuesta,
  a.mensaje_respuesta,
    a.nombrezip,
  a.nombre_archivo,
    a.total,
  a.descuento,
  (SELECT sum(det.total + IFNULL(ven.total,0))
  FROM al_venta_detalle det 
    LEFT JOIN venta ven ON det.idalquiler=ven.idalquiler
  WHERE det.idalquiler=a.idalquiler AND det.estadopago = 1) as tot,
  a.iddocumento, 
    a.idhabitacion,
    (SELECT CONCAT(
    avp.nombre,'-',
    ti.identificacion,'-',
        avp.sexo,'-',
        avp.nacionalidad,'-',
        avp.dni)
  FROM al_venta_personaadicional avp
  INNER JOIN tipo_identificacion ti ON avp.tipo_doc = ti.id
  WHERE avp.idalquiler = a.idalquiler ORDER BY avp.idpersona LIMIT 1) as personaadicional
    
  from al_venta a
    inner join cliente b on b.idhuesped = a.idhuesped
    
    left join tipo_identificacion tid on b.tipo_documento = tid.id 
  where a.estadoalquiler is not null 
  and a.anulado=0
    ".$concatena."
    order by fecharegistro DESC
  ");

//if($sqllibrohuespedes == 1){
  $sqllibrohuespedes->execute(); // Execute the statement.
  $result = $sqllibrohuespedes->get_result();

  $results = $result->fetch_all(MYSQLI_ASSOC);
  
  foreach ($results as $key => $reg) {
    $arr = isset($reg["personaadicional"]) ? explode("-", $reg["personaadicional"]) : [0] ;
    //print_r( $arr);
    if(count($arr) > 0){
      $results[$key]["acompaniante"] = $arr[0];
      $results[$key]["tipo_doc"] = $arr[1];
      $results[$key]["sexoAc"] = $arr[2];
      $results[$key]["nacionalidadAc"] = $arr[3];
      $results[$key]["dni_acom"] = $arr[4];
    }else{
      $results[$key]["acompaniante"] = "";
      $results[$key]["tipo_doc"] = "";
      $results[$key]["sexoAc"] = "";
      $results[$key]["nacionalidadAc"] = "";
      $results[$key]["dni_acom"] = "";
    }
  }

  $arreglo["data"] = $results;
  echo json_encode($arreglo);

  $mysqli->close();
/*}else{
  printf("line ".__LINE__." - Errormessage: %s\n", $mysqli->error); exit;
}*/

//************************************************************
?>
