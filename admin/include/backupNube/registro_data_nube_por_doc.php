<?php
session_start();
include "../../config.php";
include "../functions.php";
include "../../configFTP.php";

set_error_handler("myFunctionErrorHandler", E_WARNING);
include "../../configBackupNube.php";

//Obtener id de alquiler
$idAlquiler = $_POST['idalquiler'];
$tipoVenta = $_POST['tipoVenta'];
$prefijo_file_CDR = "R-";

if($tipoVenta == "01") {
    $sqlalquiler = $mysqli->query("select
          a.idalquiler,      a.nrohabitacion,           
          b.nombre,      b.ciudad,      b.tipo_documento,
          b.documento,            a.comentarios,
          a.nroorden,      a.fecharegistro,
          a.documento,      a.codigo_respuesta,
          a.mensaje_respuesta,      a.nombrezip,
          a.nombre_archivo,      a.total,
          a.descuento,
          IFNULL((SELECT sum(det.total) 
          FROM al_venta_detalle det 
          WHERE det.idalquiler=a.idalquiler and det.estadopago<>2),0)+
          IFNULL((SELECT sum(IFNULL(ven.total,0)) from venta ven where ven.idalquiler=a.idalquiler ),0) as tot,
          a.iddocumento, a.idhabitacion,
          a.anulado,
          a.anulado_motivo,
          CASE 
            WHEN a.anulado = 1 THEN 'ANULADO'
          ELSE ''
          END AS anulado_desc,
          'Alquiler' as tipoVentaDes
          
          from al_venta a
          inner join cliente b on b.idhuesped = a.idhuesped
          where a.estadoalquiler is not null 
          and a.iddocumento is not null and a.enviado = 2 and a.anulado = 0 
          and a.idalquiler=". $idAlquiler . "
    order by fecharegistro DESC");

}else{
    $sqlalquiler = $mysqli->query("    
          select
          venta.idventa,  
          '-',           
          case when cliente.nombre is null then venta.cliente else cliente.nombre end nombre,
          cliente.ciudad,
          case when cliente.tipo_documento is null then '0' else cliente.tipo_documento end tipo_documento,
          case when cliente.documento is null then '0000000' else cliente.documento end documento,
          
          venta.anotaciones,
          venta.numero,
          venta.fecha as fecharegistro,
    
          venta.documento,
          venta.codigo_respuesta,
          venta.mensaje_respuesta,
          venta.nombrezip,
          venta.nombre_archivo,
          venta.total,
          venta.descuento,
          (SELECT sum(det.importe) FROM ventadetalle det 
          WHERE det.idventa=venta.idventa) as tot,
          venta.iddocumento, 0,
          venta.anulado,
          venta.anulado_motivo,
          CASE 
            WHEN venta.anulado = 1 THEN 'ANULADO'
          ELSE ''
          END AS anulado_desc,
          'Venta' as tipoVentaDes
          
          from venta 
          left join cliente on cliente.idhuesped = venta.idcliente
          where venta.codigo_respuesta = 0 
          and venta.iddocumento is not null and venta.enviado = 2 and venta.anulado = 0 and venta.idventa=" . $idAlquiler . "
    
    order by fecharegistro DESC");

}

$ArrayMessage = array('success'=> 0,'errors'=>0);
$subirFTP = $habilitar_envio_automatico_archivo_factura;

//set_error_handler("myFunctionErrorHandler", E_NOTICE);

if($subirFTP == 1){
    // conexión
    $conn_id = ftp_connect($ftp_server);

    // logeo
    $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
}

restore_error_handler();

while ($xhFila = $sqlalquiler->fetch_row())
{
    $voucher_number=$xhFila['9'];
    $voucher_date=$xhFila['8'];
    $client_name=$xhFila['2'];
    $client_document=$xhFila['5'];
    $client_type_document=$xhFila['4'];
    $total=$xhFila['16'] - $xhFila['15'];
    $sunat_code_response=$xhFila['10'];
    $sunat_message_response=$xhFila['11'];
    $sunat_id_voucher=$xhFila['17'];
    $file_name_zip=$xhFila['12'];
    $file_name=$xhFila['13'];
    $nombreXml=substr($xhFila['12'], 0,-4).'.xml';
    $nombreCdr=$prefijo_file_CDR.$xhFila['12'];
    $voucher_anulado=0;

    $consulta="call insertComprobantes('".$voucher_number."','".$voucher_date."','".$client_name."',
        '".$client_document."','".$client_type_document."',".$total.",
        '".$sunat_code_response."','".$sunat_message_response."','".$sunat_id_voucher."',
        '".$file_name_zip."','".$file_name."','".$nombreXml."','".$nombreCdr."','".$voucher_anulado."');";

    $return = $dbremoto->query($consulta);
	if($return){
	    $Men = "Los datos fueron guardados satisfactoriamente.";
        $ArrayMessage = array('success'=> array('ReferenceID' => '123','codRespuesta' =>'EXITO01',
            'Description' => 'Se registró con éxito los comprobante'),'errors'=>0);

        if($subirFTP == 1){

            // conexión
            if ((!$conn_id) || (!$login_result)) {
                //echo "Conexión al FTP con errores!";
                $Men = "Conexión al FTP con errores!. Intentando conectar a $ftp_server for user $ftp_user_name";
                //$ArrayMessage = array('success'=>0,'errors'=>array('getMessage' =>'Conexión al FTP con errores!. Intentando conectar a $ftp_server for user $ftp_user_name' ,'getCode'=>0));
                break;
            }
//            else {
//                echo "Conectado a $ftp_server, for user $ftp_user_name \n";
//            }

            // Cambiamos a modo pasivo, esto es importante porque, de esta manera le decimos al
            //servidor que seremos nosotros quienes comenzaremos la transmisión de datos.
            ftp_pasv ($conn_id, true) ;


            //Subida de archivo PDF
            $file = $file_name.".pdf";
            $source_file = $ftp_carpeta_local_pdf.$file;
            $destination_file = $ftp_carpeta_remota.$file;

            // archivo a copiar/subir
            $upload = ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY);

            // estado de subida/copiado
            if (!$upload) {
                //echo "Error al subir el archivo PDF: $source_file \n";
                $Men = "Error al subir el archivo PDF: $source_file";
                $ArrayMessage = array('success'=>0,'errors'=>array('getMessage' =>'Error al subir el archivo PDF: $source_file' ,'getCode'=>0));
                break;
            } else {
                //echo "Archivo PDF $source_file se ha subido exitosamente a $ftp_server en $destination_file \n";
                $ArrayMessage = array('success'=> array('ReferenceID' => '123','codRespuesta' =>'EXITOUP01',
                    'Description' => 'Archivo PDF $source_file se ha subido exitosamente a $ftp_server en $destination_file'),'errors'=>0);
            }

            //echo "\n";

            //Subida de archivo XML
            $source_file = $ftp_carpeta_local_xml.$nombreXml;
            $destination_file = $ftp_carpeta_remota.$nombreXml;
            // archivo a copiar/subir
            $upload = ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY);

            // estado de subida/copiado
            if (!$upload) {
                //echo "Error al subir el archivo XML: $source_file \n";
                $Men = "Error al subir el archivo XML: $source_file";
                $ArrayMessage = array('success'=>0,'errors'=>array('getMessage' =>'Error al subir el archivo PDF: $source_file' ,'getCode'=>0));
                break;
            } else {
                //echo "Archivo XML $source_file se ha subido exitosamente a $ftp_server en $destination_file \n";
                $ArrayMessage = array('success'=> array('ReferenceID' => '123','codRespuesta' =>'EXITOUP02',
                    'Description' => 'Archivo PDF $source_file se ha subido exitosamente a $ftp_server en $destination_file'),'errors'=>0);
            }

            //Subida archivo CDR
            $source_file = $ftp_carpeta_local_cdr.$nombreCdr;
            $destination_file = $ftp_carpeta_remota.$nombreCdr;
            // archivo a copiar/subir
            $upload = ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY);

            // estado de subida/copiado
            if (!$upload) {
                //echo "Error al subir el archivo XML: $source_file \n";
                $Men = "Error al subir el archivo CDR: $source_file";
                $ArrayMessage = array('success'=>0,'errors'=>array('getMessage' =>'Error al subir el archivo PDF: $source_file' ,'getCode'=>0));
                break;
            } else {
                //echo "Archivo XML $source_file se ha subido exitosamente a $ftp_server en $destination_file \n";
                $ArrayMessage = array('success'=> array('ReferenceID' => '123','codRespuesta' =>'EXITOUP02',
                    'Description' => 'Archivo PDF $source_file se ha subido exitosamente a $ftp_server en $destination_file'),'errors'=>0);
            }

            //echo "\n\n";
        }

	}else{
		$Men = "Ha fallado, los datos no han sido registrados.";
        $ArrayMessage = array('success'=>0,'errors'=>array('getMessage' =>'Error al registrar comprobantes en la nube.' ,'getCode'=>0));
        break;
	}
    //echo $return;
}
$sqlalquiler->free();

set_error_handler("myFunctionErrorHandler", E_WARNING);
//set_error_handler("myFunctionErrorHandler", E_NOTICE);

if($subirFTP == 1){
    // cerramos
    ftp_close($conn_id);
}
restore_error_handler();

$mysqli->close();
$dbremoto->close();
$_SESSION['msgerror'] = $Men;

echo json_encode($ArrayMessage);

//$_SESSION['msgerror'] = $Men;
//header("Location: ../../cargaDataNube.php"); exit;
//************************************************************

// función de gestión de errores
function myFunctionErrorHandler($errno, $errstr, $errfile, $errline)
{
    /* Según el típo de error, lo procesamos */
    switch ($errno) {
        case E_WARNING:
//            echo "Hay un WARNING.<br />\n";
//            echo "El warning es: ". $errstr ."<br />\n";
//            echo "El fichero donde se ha producido el warning es: ". $errfile ."<br />\n";
//            echo "La línea donde se ha producido el warning es: ". $errline ."<br />\n";
            $Men = "WARNING: ".$errstr;
            $ArrayMessage = array('success'=>0,'errors'=>array('getMessage' =>'WARNING: '.$errstr ,'getCode'=>0));
            echo json_encode($ArrayMessage);
            /* No ejecutar el gestor de errores interno de PHP, hacemos que lo pueda procesar un try catch */
            //return true;
            break;

        case E_NOTICE:
            //echo "Hay un NOTICE:<br />\n";
            /* No ejecutar el gestor de errores interno de PHP, hacemos que lo pueda procesar un try catch */
            //return true;
            break;

        default:
            /* Ejecuta el gestor de errores interno de PHP */
            return false;
            break;
    }
    exit();
}

?>