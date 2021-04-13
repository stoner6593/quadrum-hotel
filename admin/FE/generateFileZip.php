<?php
//include("../includes/controller.php");
include "../validar.php";
include "../config.php";
include "../include/functions.php";
//if(!isset($_SESSION['username'])) {
//    header("Location: login.php");
//    exit;
//}else{
date_default_timezone_set('America/Lima');

    $finicio = $_POST['finiciodown'];
    $ffin = $_POST['ffindown'];
    $concatena='';
    $typeFile = $_POST['typeFile'];
    $estadoCP = $_POST['estadoCP'];

    $f1=explode("/",$finicio);
    $newfecha1=$f1[2].'-'.$f1[1].'-'.$f1[0];

    $f2=explode("/",$ffin);
    $newfecha2=$f2[2].'-'.$f2[1].'-'.$f2[0];

    $concatena=" and DATE(a.fecharegistro) between '".$newfecha1."' and '".$newfecha2."' ";

    $concatenaVenta=' and DATE(venta.fecha) between "'.$newfecha1.'" and "'.$newfecha2.'" ';

    $concatenaEstado="";
    $concatenaEstadoVenta="";
    if($estadoCP=="ES"){
        $concatenaEstado=" and a.enviado = 2 ";
        $concatenaEstadoVenta=' and venta.enviado = 2 ';
    }

    $fechaActual = date("d-m-Y");

    $rutaFolder = "";

    if($typeFile=="PDF") {
        $sql = "select concat(a.nombre_archivo,'.pdf')            
      from al_venta a
      inner join cliente b on b.idhuesped = a.idhuesped
      where a.estadoalquiler is not null 
      and a.iddocumento is not null and a.enviado is not null and a.nombre_archivo is not null ".$concatenaEstado."
      ".$concatena."
      
      union
      
    select concat(a.nombre_archivo,'.pdf')      
      from al_venta a
      inner join cliente d on d.idhuesped = a.idhuesped
      where a.codigo_respuesta = -1 and a.estadoalquiler is not null
      and a.iddocumento is null and a.nombre_archivo is not null ".$concatenaEstado."
      ".$concatena."      
  
      union

      select concat(venta.nombre_archivo,'.pdf')
      
      from venta 
      left join cliente on cliente.idhuesped = venta.idcliente
      where venta.codigo_respuesta = 0 
      and venta.iddocumento is not null and venta.nombre_archivo is not null ".$concatenaEstadoVenta."
      " . $concatenaVenta;

        $rutaFolder = "PDF/";
    }
    if($typeFile=="XML") {
        $sql = "select concat(SUBSTRING_INDEX(a.nombrezip,'.',1) ,'.xml')            
      from al_venta a
      inner join cliente b on b.idhuesped = a.idhuesped
      where a.estadoalquiler is not null 
      and a.iddocumento is not null and a.enviado is not null and a.nombrezip is not null ".$concatenaEstado."
      ".$concatena."
      
      union
      
    select concat(SUBSTRING_INDEX(a.nombrezip,'.',1) ,'.xml')      
      from al_venta a
      inner join cliente d on d.idhuesped = a.idhuesped
      where a.codigo_respuesta = -1 and a.estadoalquiler is not null
      and a.iddocumento is null and a.nombrezip is not null ".$concatenaEstado."
      ".$concatena."      
  
      union

      select concat(SUBSTRING_INDEX(venta.nombrezip,'.',1) ,'.xml')
      
      from venta 
      left join cliente on cliente.idhuesped = venta.idcliente
      where venta.codigo_respuesta = 0 
      and venta.iddocumento is not null and venta.nombrezip is not null ".$concatenaEstadoVenta."
      " . $concatenaVenta;

        $rutaFolder = "XMLFIRMADOS/";
    }
    if($typeFile=="CDR") {

        $sql = "select concat('R-',a.nombrezip)            
      from al_venta a
      inner join cliente b on b.idhuesped = a.idhuesped
      where a.estadoalquiler is not null 
      and a.iddocumento is not null and a.enviado is not null and a.nombrezip is not null ".$concatenaEstado."
      ".$concatena."
      
      union
      
    select concat('R-',a.nombrezip)      
      from al_venta a
      inner join cliente d on d.idhuesped = a.idhuesped
      where a.codigo_respuesta = -1 and a.estadoalquiler is not null
      and a.iddocumento is null and a.nombrezip is not null ".$concatenaEstado."
      ".$concatena."      
  
      union

      select concat('R-',venta.nombrezip)
      
      from venta 
      left join cliente on cliente.idhuesped = venta.idcliente
      where venta.codigo_respuesta = 0 
      and venta.iddocumento is not null and venta.nombrezip is not null ".$concatenaEstadoVenta."
      " . $concatenaVenta;

        $rutaFolder = "CDR/";
    }
        //echo $sql;
//    $result = $db->prepare($sql);
//    $result->execute();

    $result = $mysqli->query($sql);

    $zip = new ZipArchive();
    $filename = "fileDoc_".rand().".zip";

    while ($row = $result->fetch_row()) {
        $nombre_fichero = $rutaFolder.$row[0];
        if (file_exists($nombre_fichero)) {
            //echo "El fichero $nombre_fichero existe \n";

            if($zip->open($filename,ZIPARCHIVE::CREATE)===true) {
                $zip->addFile($nombre_fichero,$row[0]);
                $zip->close();
                //echo 'Creado '.$filename."\n";
            }
            else {
                //echo 'Error creando '.$filename."\n";
            }

        } else {
            //echo "El fichero $nombre_fichero no existe \n";
        }
    }

    if(file_exists($filename)){
        // Define headers
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        // Creamos las cabeceras que forzaran la descarga del archivo como archivo zip.
        header('Content-type: "application/zip"');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header("Content-Transfer-Encoding: binary");

        readfile($filename);// leemos el archivo creado

        unlink($filename);//Destruye el archivo temporal
    }else{
        $_SESSION['msgerror'] = "No se pudo generar el archivo comprimido. No se encontraron archivos para las fechas ".$finicio." hasta ".$ffin;
        //echo "No se pudo generar el archivo comprimido. No se encontraron archivos para las fechas ".$finicio." hasta ".$ffin;
    }
    header("Location: ../resumendiariohistorico.php");
    exit;
?>