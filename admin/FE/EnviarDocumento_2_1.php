<?php
require_once "../../init.php";
require_once "../../src/Util.php";

include "generales/convertir.php";
include "../config.php";
include "../include/functions.php";
include "Pdf.php";

use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;

require __DIR__ . '/../../vendor/autoload.php';

$util = Util::getInstance();

$dato=new Generaxml($_POST['idalquiler'],$_POST['tipo_documento'],$_POST['finicio'],$_POST['ffin']);

if($_POST['idalquiler']==0 && $_POST['tipo_documento']==0){

    $dato->_xmlResumen();
}else{
    if($_POST['tipo_servicio']=='AL') {
        $dato->generar_xml();
    }
    if($_POST['tipo_servicio']=='VE') {
        $dato->generar_xml_venta();
    }
}

class Generaxml
{

    //private $sqlalquiler=null;
    public $idalquiler;
    public $tipo_documento;
    public $finicio;
    public $ffin;

    //Empresa Emisora
    public $RUCEmisor ;
    public $NombreComercial ;
    public $RazonSocial ;

    //Address
    public $Ubigueo ;
    public $Distrito ;
    public $Provincia ;
    public $Departamento ;
    public $Urbanizacion ;
    public $CodLocal ;
    public $Direccion ;

    function __construct($idalquiler, $tipo_documento, $finicio = null, $ffin)
    {
        $this->idalquiler = $idalquiler;
        $this->tipo_documento = $tipo_documento;
        $this->finicio = $finicio;
        $this->ffin = $ffin;
        $this->crear_directorio();
        $this->util = Util::getInstance();

        $this->RUCEmisor        = APP_EMISOR_RUC;
        $this->NombreComercial  = APP_EMISOR_NOMBRE;
        $this->RazonSocial      = APP_EMISOR_RAZONSOCIAL;

        //Address
        $this->Ubigueo          = APP_EMISOR_UBIGUEO;
        $this->Distrito         = APP_EMISOR_DISTRITO;
        $this->Provincia        = APP_EMISOR_PROVINCIA;
        $this->Departamento     = APP_EMISOR_DEPARTAMENTO;
        $this->Urbanizacion     = APP_EMISOR_URBANIZACION;
        $this->CodLocal         = APP_EMISOR_CODLOCAL;
        $this->Direccion        = APP_EMISOR_DIRECCION;

/*
        $this->RUCEmisor = '20545756022';
        $this->NombreComercial = '';
        $this->RazonSocial = 'INVERSIONES INKA´S PALACE S.A.C.';

        //Address
        $this->Ubigueo = '150101';
        $this->Distrito = 'LIMA';
        $this->Provincia = 'LIMA';
        $this->Departamento = 'LIMA';
        $this->Urbanizacion = '-';
        $this->CodLocal = '0000';
        $this->Direccion = 'CAL.MANUEL DEL PINO NRO. 116 URB. SANTA BEATRIZ (ALT.CDRA.16 AV.ARENALES) LIMA - LIMA - LIMA';
        */
    }

    public function crear_directorio(){

        if(!is_dir('XMLFIRMADOS/')){
            @mkdir('XMLFIRMADOS/', 0700);
        }

        if(!is_dir('XML/')){
            @mkdir('XML/', 0700);
        }
        if(!is_dir('CDR/')){
            @mkdir('CDR/', 0700);
        }

        if(!is_dir('XMLENVIAR/')){
            @mkdir('XMLENVIAR/', 0700);
        }
        if(!is_dir('PDF/')){
            @mkdir('PDF/', 0700);
        }
    }

    public function generar_xml()
    {

        $db = new conexion();
        $link = $db->conexion();

        //Cabecera alquiler
        $sqlalquiler = $link->query("select
			al_venta.idalquiler,
			al_venta.idhuesped,
			al_venta.idhabitacion,
			al_venta.nrohabitacion,
			al_venta.tipooperacion,
			al_venta.total,
			
			cliente.idhuesped,
			cliente.nombre,
			cliente.ciudad,
			cliente.tipo_documento,
			cliente.documento,
			
			al_venta.comentarios,
			al_venta.nroorden,
			al_venta.fechafin,
			al_venta.totalefectivo,
			al_venta.totalvisa,
			al_venta.descuento,
			al_venta.fecharegistro,

            cliente.RUC,
            cliente.razon_social,
            cliente.direccion

			from al_venta inner join cliente on cliente.idhuesped = al_venta.idhuesped
			where al_venta.idalquiler = '$this->idalquiler' 
			and al_venta.anulado = 0
			");
        $xaFila = $sqlalquiler->fetch_row();

        $invoice = new Invoice();
        $company = new Company();
        $client = new Client();

        $company ->setRuc($this->RUCEmisor)
            ->setNombreComercial( $this->NombreComercial)
            ->setRazonSocial( $this->RazonSocial)
            ->setAddress((new Address())
                ->setUbigueo( $this->Ubigueo)
                ->setDistrito( $this->Distrito)
                ->setProvincia( $this->Provincia)
                ->setDepartamento( $this->Departamento)
                ->setUrbanizacion( $this->Urbanizacion)
                ->setCodLocal( $this->CodLocal)
                ->setDireccion( $this->Direccion));

        if($xaFila[9]==''){
            $client = new Client();
            $client->setTipoDoc('6')
                ->setNumDoc('00000000000')
                ->setRznSocial($this->sanear_string($xaFila[7]))
                ->setAddress((new Address())
                    ->setDireccion($this->sanear_string($xaFila[8])));

            /* Datos para PDF */
            $RUCReceptor='00000000';
            $RznSoc= $xaFila[7];
            $Direccion=$xaFila[8];
            $TipoDocumento='1';

        }else{

            $client = new Client();
            $client->setTipoDoc($xaFila[9])
            /*    ->setNumDoc($xaFila[10])
                ->setRznSocial($xaFila[7])*/
                ->setNumDoc($xaFila[18])
                ->setRznSocial($this->sanear_string($xaFila[19]))
                ->setAddress((new Address())
                    //->setDireccion($xaFila[8]));
                    ->setDireccion($this->sanear_string($xaFila[20])));

            /* Datos para PDF */
            /*$RUCReceptor=$xaFila[10];
            $RznSoc= $xaFila[7];*/
            $RUCReceptor=$xaFila[18];
            $RznSoc= $xaFila[19];
            //$Direccion=$xaFila[8];
            $Direccion=$xaFila[20];
            $TipoDocumento=$xaFila[9];
        }


        /* Datos para PDF */
        $array = [
            'Encabezado' => [

                'Emisor' => [
                    'RUCEmisor' => $this->RUCEmisor,
                    'RznSoc' => $this->RazonSocial,
                    'NomComercial' => $this->NombreComercial,
                    'Ubigeo' => $this->Ubigueo,
                    'Direccion'=> $this->Direccion,
                    'Urbanizacion' => $this->Urbanizacion,
                    'Departamento' => $this->Departamento,
                    'Provincia' => $this->Provincia,
                    'Distrito' => $this->Distrito,
                ],
                'Receptor' => [
                    'RUCReceptor' =>$RUCReceptor,
                    'RznSoc' => $RznSoc,
                    'Direccion'=> $Direccion,
                    'TipoDocumento'=>$TipoDocumento,
                ],
            ],
        ];


        //Detalle Aquiler
        $sqldetalle = $link->query("select
				idalquilerdetalle,
				idalquiler,
				tipoalquiler,	
				fechadesde,	
				fechahasta,	
				nrohoras,	
				nrodias,	
				costohora,	
				costodia,	
				formapago,	
				totalefectivo,	
				totalvisa,	
				estadopago,	
				costoingresoanticipado,	
				horaadicional,
				costohoraadicional,	
				huespedadicional,	
				costohuespedadicional,	
				preciounitario,	
				cantidad,	
				total,	
				idturno,	
				idusuario
				
				from al_venta_detalle 
				where idalquiler = '$this->idalquiler'   and estadopago!=2 order by idalquilerdetalle asc
				");

        $descripcion="";

        $items=array();
        $itemsPDF=array();

        $globalIGV=0;
        $globalTotalVenta=0;
        $globalGrabadas=0;
        $Descuento=0;
        $num=0;
        $IGV_decimal = 0.18;
        $IGV_porcentual = 18;

        while ($tmpFila = $sqldetalle->fetch_row()){
            $num++;
            
            //Validación para que no muestre toda la descripción del alquiler
            //Según reglamento no se debe de alguilar por horas
           if($tmpFila['2']==1){
				    
				    	$descripcion="HOSPEDAJE";
				    
		    }else{
		        
		    
				$descripcion=tipoAlquiler($tmpFila['2']).' ('.$tmpFila['19'].')';
				if($tmpFila['2'] != 4 &&  $tmpFila['2'] != 5){
					$descripcion.= fechadesdehasta($tmpFila['3'],$tmpFila['4']);
				}
				
			}
    			
            $pu1=number_format($tmpFila[20] / (1+$IGV_decimal),2);
            $pu= str_replace(",", "", $pu1);
            $t=(str_replace(",", "", $tmpFila[20]));
            $stt=number_format($t / (1.18 ),2);
            $st=str_replace(",", "", $stt);
            $igvv=number_format($t - $st,2);
            $igv=str_replace(",", "", $igvv);
            $precioUnitario = number_format($t/1);//Total venta detalle entre cantidad de productos

            $item = new SaleDetail();
            $item->setCodProducto('P00'.$num)
                ->setUnidad('NIU')
                ->setDescripcion(str_replace('<br>', '', $descripcion))
                ->setCantidad(1)
                ->setMtoValorUnitario(str_replace(",", "", number_format($st,2)))
                ->setMtoValorVenta(str_replace(",", "", number_format($t,2)))
                ->setMtoBaseIgv(str_replace(",", "", number_format($stt,2)))
                ->setPorcentajeIgv($IGV_porcentual)
                ->setIgv($igv)
                ->setTipAfeIgv('10')//Gravado - Operación Onerosa
                ->setTotalImpuestos($igv)
                ->setMtoPrecioUnitario($precioUnitario)
                ->setIsc(0)
                ->setOtroTributo(0)
            ;


            $item_pdf ["pro_id"]     = $num;
            $item_pdf ["pro_desc"]   =str_replace('<br>', '', $descripcion) ;
            $item_pdf ['pro_cantidad']   = 1;
            $item_pdf ["pro_unimedida"]  = 'NIU';
            $item_pdf ["pro_preunitario"]    =str_replace(",", "", number_format($pu,2));
            $item_pdf ['pro_preref']     =str_replace(",", "", number_format($pu,2));
            $item_pdf ["pro_tipoprecio"]     = "01"; //Precio Incluye IGV
            $item_pdf ["pro_igv"]    = str_replace(',', '', $igv);
            $item_pdf ['pro_tipoimpuesto']   = "10"; //Gravado - Operación Onerosa
            $item_pdf ["pro_isc"]    = number_format(0.00,2);
            $item_pdf ["pro_otroimpuesto"]   = number_format(0.00,2);
            $item_pdf ['pro_subtotal']   =str_replace(',', '', $st);
            $item_pdf ['pro_total']  = str_replace(",", "", number_format($t,2)); //number_format(str_replace(",", "", $t),2);


            $globalIGV+=$igv;
            $globalTotalVenta+= $t;//$tmpFila[18];
            $globalGrabadas+=$st;

            array_push($items,$item);
            array_push($itemsPDF,$item_pdf);

            //Actualiza estado de envío de alquiler a SUNAT / Esto para cuando se agreguen nuevos productos solo se envíen los NO FACTURADOS
            $sqlactualiza = $link->query("UPDATE  al_venta_detalle SET procesado=1 where idalquilerdetalle = '$tmpFila[0]'");
        }

        //INICIO DETALLE VENTAS
        $sqlventa = $link->query("select
			venta.idventa,
			venta.idalquiler,
			ventadetalle.idventadetalle,
			ventadetalle.idventa,
			ventadetalle.nombre,
			ventadetalle.cantidad,
			ventadetalle.precio,
			ventadetalle.importe
			
			from venta left join ventadetalle on ventadetalle.idventa = venta.idventa
			where venta.idalquiler = '$this->idalquiler'  order by ventadetalle.idventadetalle asc");

        $detTotventa=0;
        while($vFila = $sqlventa->fetch_row()){
            $num++;

            $descripcion=$vFila[4];
            $cantidad = $vFila['5'];
            $pu2=number_format($vFila[6] / 1.18,2);
            $vv2=number_format($vFila[6],2);

            $ttt=($vFila[6] * $vFila[5]);
            $t2=str_replace(",", "",$ttt);

            $st2=str_replace(',', '', number_format($t2 / (1.18 ),2)) ;
            $igv2=number_format($t2 - $st2,2);
            $precioUnitario2 = number_format($t2/$cantidad);//Total venta detalle entre cantidad de productos

            $item2 = new SaleDetail();
            $item2->setCodProducto('P00'.$num)
                ->setUnidad('NIU')
                ->setDescripcion(str_replace('<br>', '', $descripcion))
                ->setCantidad($cantidad)
                ->setMtoValorUnitario(str_replace(",", "", number_format($pu2,2)))
                ->setMtoValorVenta(str_replace(",", "", number_format($vv2,2)))
                ->setMtoBaseIgv(str_replace(",", "", number_format($st2,2)))
                ->setPorcentajeIgv($IGV_porcentual)
                ->setIgv($igv2)
                ->setTipAfeIgv('10')//Gravado - Operación Onerosa
                ->setTotalImpuestos($igv2)
                ->setMtoPrecioUnitario($pu2)
                ->setIsc(0)
                ->setOtroTributo(0)
            ;

            $itempdf2 ["pro_id"]     = rand();
            $itempdf2 ["pro_desc"]   =$vFila['4'] ;
            $itempdf2 ['pro_cantidad']   = $vFila['5'];
            $itempdf2 ["pro_unimedida"]  = 'NIU';
            $itempdf2 ["pro_preunitario"]    =$pu2;
            $itempdf2 ['pro_preref']     =str_replace(",", "", $pu2);
            $itempdf2 ["pro_tipoprecio"]     = "01"; //Precio Incluye IGV
            $itempdf2 ["pro_igv"]    = $igv2;
            $itempdf2 ['pro_tipoimpuesto']   = "10"; //Gravado - Operación Onerosa
            $itempdf2 ["pro_isc"]    = number_format(0.00,2);
            $itempdf2 ["pro_otroimpuesto"]   = number_format(0.00,2);
            $itempdf2 ['pro_subtotal']   =str_replace(",", "", $st2) ;
            $itempdf2 ['pro_total']  =str_replace(",", "", number_format($t2,2)) ;

            $globalIGV+= str_replace(",", "", $igv2);
            $globalTotalVenta+=str_replace(',', '', number_format($t2,2));
            $globalGrabadas+= str_replace(",", "", $st2);

            if(count($item2)>0){
                array_push($items,$item2);
                array_push($itemsPDF,$itempdf2);
                //Actualiza estado de envío de producto a SUNAT / Esto para cuando se agreguen nuevos productos solo se envíen los NO FACTURADOS
                $sqlDetalleVenta = $link->query("UPDATE ventadetalle SET procesado=1 where idventadetalle='$vFila[2]'");
            }

        }


        //Descuento Global
        $Descuento= $xaFila[16];

        //CORRELATIVO PARA LOS DOCUMENTOS
        $correlativo=$link->query("SELECT * FROM series WHERE codsunat='$this->tipo_documento' and estado=1")->fetch_row();

        //Validar si es boleta o factura
        $corre=$correlativo[3].'-'.str_pad($correlativo[4], 8, "0", STR_PAD_LEFT);

        $date = new DateTime($xaFila[13]);
        $dateEmi = new DateTime();

        $invoice
            ->setUblVersion('2.1')
            ->setFecVencimiento(new \DateTime())
            ->setTipoOperacion('0101')
            ->setTipoDoc($this->tipo_documento)
            ->setSerie($correlativo[3])
            ->setCorrelativo(str_pad($correlativo[4], 8, "0", STR_PAD_LEFT))//str_pad($correlativo[4], 8, "0", STR_PAD_LEFT)
            ->setFechaEmision($dateEmi)
            ->setTipoMoneda('PEN')
            ->setClient($client)
            ->setMtoOperGravadas($globalGrabadas)
            ->setMtoOperExoneradas(0)
            ->setMtoIGV($globalIGV)
            ->setTotalImpuestos($globalIGV)
            ->setValorVenta($globalGrabadas)
            ->setMtoImpVenta($globalTotalVenta)
            ->setCompany($company)
            ->setMtoDescuentos($Descuento);


        $MontoLetras=num_to_letras(($globalTotalVenta - $Descuento),"PEN");

        $invoice->setDetails($items)
            ->setLegends([
                (new Legend())
                    ->setCode('1000')
                    ->setValue($MontoLetras)
            ]);

        /* Datos para generar PDF */
        $dato['TotIgv']=str_replace(',', '', $globalIGV) ;
        $dato['TotVenta']=str_replace(',', '', $globalTotalVenta) ;
        $dato['TotGravada']= str_replace(',', '', $globalGrabadas) ;
        $dato['TotGratuitas']=0.00;
        $dato['TotInafectas']=0.00;
        $dato['TotExoneradas']=0.00;
        $dato['DescuentoGlobal']=$Descuento;
        $dato['Moneda']='PEN';
        $dato['tipo_documento']=trim($correlativo[1]);
        $dato['fecharegistro']=$dateEmi->format('Y-m-d');

        if($xaFila['14'] > 0){
            $dato['formapago']="Efectivo";
        }elseif ($xaFila['15'] > 0) {
            $dato['formapago']="Visa";
        }

        //Nombre para el archivo PDF ***** mejorar
        $nombre_archivo = utf8_decode($array['Encabezado']['Emisor']['RUCEmisor'].'-'.$dateEmi->format('Y-m-d').'-'.$corre);

        // Envio a SUNAT.
        //$see = $this->util->getSee(SunatEndpoints::FE_BETA);
        $see = require __DIR__ . '/ConfigDatosSunatEmpresa.php';

        /** Si solo desea enviar un XML ya generado utilice esta función**/
        //$res = $see->sendXml(get_class($invoice), $invoice->getName(), file_get_contents($ruta_XML));

        $res = $see->send($invoice);

        //guardar archivo sin firmar
        $this->util->writeXmlSinFirmar($corre, $see->getFactory()->getBuilder()->build($invoice));

        //guardar archivo firmado
        $this->util->writeXml($invoice, $see->getFactory()->getLastXml());

        if ($res->isSuccess()) {
            /**@var $res \Greenter\Model\Response\BillResult*/
            $cdr = $res->getCdrResponse();
            $this->util->writeCdr($invoice, $res->getCdrZip());

            $codigoRespuesta = $cdr->getId();
            $mensajeSunat = $cdr->getDescription();
            $nombreZip = ''.$invoice->getName().'.zip';

            $this->generapdfinvoice($array,$corre,$dato,$itemsPDF,$MontoLetras,$nombre_archivo,'',$xaFila[3]); //Genera PDF
            $this->ActualizaCorrelativo($correlativo);
            $this->ActualizaVenta($correlativo,$codigoRespuesta,$mensajeSunat,$nombreZip,$nombre_archivo,$corre,2);

            $ArrayMessage=array('success'=> array('ReferenceID' => $corre,'codRespuesta' =>$codigoRespuesta,
                'Description' => $mensajeSunat,'nombre_archivo'=>$nombre_archivo.'.pdf' ),'errors'=>0);

            echo json_encode($ArrayMessage);

        } else {
            //var_dump($res->getError());
            //$error= var_dump($res->getError());
            $ArrayMessage=array('success'=>0,'errors'=>array('getMessage' =>'CodeError: '.$res->getError()->getCode().'.Mensaje: '.$res->getError()->getMessage() ,'getCode'=>0));
            echo json_encode($ArrayMessage);
        }

    }

    //Actualizar Correlativo
    function ActualizaCorrelativo($numeracion=array()){
        $db = new conexion();
        $link = $db->conexion();
        return $link->query("UPDATE series SET numeracion = numeracion + 1 WHERE codsunat='$numeracion[1]' and iddocumento='$numeracion[0]'");

    }
    //Actualiza datos de venta
    function ActualizaVenta($numeracion=array(),$codigoRespuesta,$msgRespuesta,$nombreZip,$nombre_archivo,$corre,$enviado){
        $db = new conexion();
        $link = $db->conexion();
        return $link->query("UPDATE al_venta SET iddocumento ='$numeracion[0]',correlativo='$numeracion[4]',codigo_respuesta='$codigoRespuesta',
				mensaje_respuesta='$msgRespuesta',nombrezip='$nombreZip',nombre_archivo='$nombre_archivo',documento='$corre', enviado='$enviado',fechaemision = CURRENT_TIMESTAMP
			WHERE idalquiler ='$this->idalquiler'");
    }

    //Actualiza datos de venta
    function ActualizaVentaProductos($numeracion=array(),$codigoRespuesta,$msgRespuesta,$nombreZip,$nombre_archivo,$corre,$enviado){
        $db = new conexion();
        $link = $db->conexion();
        return $link->query("UPDATE venta SET iddocumento ='$numeracion[0]',correlativo='$numeracion[4]',codigo_respuesta='$codigoRespuesta',
				mensaje_respuesta='$msgRespuesta',nombrezip='$nombreZip',nombre_archivo='$nombre_archivo',documento='$corre', enviado='$enviado'
			WHERE idventa ='$this->idalquiler'");
    }

    public function generar_xml_venta(){

        $db = new conexion();
        $link = $db->conexion();


        $sqlalquiler = $link->query("select
			a.idalquiler,
			a.idcliente,
			0,
			'',
			0,
			a.total,
			
			b.idhuesped,
			case when b.nombre is null then a.cliente else b.nombre end as nombre,
			b.ciudad,
			b.tipo_documento,
			b.documento,
			
			a.anotaciones,
			a.numero,
			a.fecha,
			case when a.formapago=1 then a.formapago else 0 end,
			case when a.formapago=2 then a.formapago else 0 end,
			0
			
			from venta a 
            left join cliente b on b.idhuesped = a.idcliente
			where a.idventa = '$this->idalquiler' 
			and a.anulado = 0");
        $xaFila = $sqlalquiler->fetch_row();


        $invoice = new Invoice();
        $company = new Company();
        $client = new Client();

        $company ->setRuc($this->RUCEmisor)
            ->setNombreComercial( $this->NombreComercial)
            ->setRazonSocial( $this->RazonSocial)
            ->setAddress((new Address())
                ->setUbigueo( $this->Ubigueo)
                ->setDistrito( $this->Distrito)
                ->setProvincia( $this->Provincia)
                ->setDepartamento( $this->Departamento)
                ->setUrbanizacion( $this->Urbanizacion)
                ->setCodLocal( $this->CodLocal)
                ->setDireccion( $this->Direccion));


        if($xaFila[9]==''){
            $client = new Client();
            $client->setTipoDoc('6')
                ->setNumDoc('00000000000')
                ->setRznSocial($this->sanear_string($xaFila[7]))
                ->setAddress((new Address())
                    ->setDireccion($this->sanear_string($xaFila[8])));

            /* Datos para PDF */
            $RUCReceptor='00000000';
            $RznSoc= $xaFila[7];
            $Direccion=$xaFila[8];
            $TipoDocumento='1';

        }else{

            $client = new Client();
            $client->setTipoDoc($xaFila[9])
            /*    ->setNumDoc($xaFila[10])
                ->setRznSocial($xaFila[7])*/
                ->setNumDoc($xaFila[18])
                ->setRznSocial($this->sanear_string($xaFila[19]))
                ->setAddress((new Address())
                    //->setDireccion($xaFila[8]));
                    ->setDireccion($this->sanear_string($xaFila[20])));

            /* Datos para PDF */
            /*$RUCReceptor=$xaFila[10];
            $RznSoc= $xaFila[7];*/
            $RUCReceptor=$xaFila[18];
            $RznSoc= $xaFila[19];
            //$Direccion=$xaFila[8];
            $Direccion=$xaFila[20];
            $TipoDocumento=$xaFila[9];
        }

        /* Datos para PDF */
        $array = [
            'Encabezado' => [

                'Emisor' => [
                    'RUCEmisor' => $this->RUCEmisor,
                    'RznSoc' => $this->RazonSocial,
                    'NomComercial' => $this->NombreComercial,
                    'Ubigeo' => $this->Ubigueo,
                    'Direccion'=> $this->Direccion,
                    'Urbanizacion' => $this->Urbanizacion,
                    'Departamento' => $this->Departamento,
                    'Provincia' => $this->Provincia,
                    'Distrito' => $this->Distrito,
                ],
                'Receptor' => [
                    'RUCReceptor' =>$RUCReceptor,
                    'RznSoc' => $RznSoc,
                    'Direccion'=> $Direccion,
                    'TipoDocumento'=>$TipoDocumento,
                ],
            ],
        ];


        $items=array();
        $itemsPDF=array();
        $globalIGV=0;
        $globalTotalVenta=0;
        $globalGrabadas=0;
        $Descuento=0;
        $descripcion="";
        $num=0;
        $IGV_decimal = 0.18;
        $IGV_porcentual = 18;

        //INICIO DETALLE VENTAS
        $sqlventa = $link->query("select
			venta.idventa,
			venta.idalquiler,
			ventadetalle.idventadetalle,
			ventadetalle.idventa,
			ventadetalle.nombre,
			ventadetalle.cantidad,
			ventadetalle.precio,
			ventadetalle.importe
			
			from venta left join ventadetalle on ventadetalle.idventa = venta.idventa
			where venta.idventa = '$this->idalquiler'  order by ventadetalle.idventadetalle asc");

        $detTotventa=0;
        while($vFila = $sqlventa->fetch_row()){
            $num++;

            $descripcion=$vFila[4];
            $cantidad = $vFila['5'];
            $pu2=number_format($vFila[6] / (1+$IGV_decimal),2);
            $vv2=number_format($vFila[6],2);

            $ttt=($vFila[6] * $vFila[5]);
            $t2=str_replace(",", "",$ttt);

            $st2=str_replace(',', '', number_format($t2 / (1+$IGV_decimal),2)) ;
            $igv2=number_format($t2 - $st2,2);
            $igv=str_replace(",", "", $igv2);
            $precioUnitario2 = number_format($t2/$cantidad);//Total venta detalle entre cantidad de productos

            $item2 = new SaleDetail();
            $item2->setCodProducto('P00'.$num)
                ->setUnidad('NIU')
                ->setDescripcion(str_replace('<br>', '', $descripcion))
                ->setCantidad($cantidad)
                ->setMtoValorUnitario(str_replace(",", "", number_format($pu2,2)))
                ->setMtoValorVenta(str_replace(",", "", number_format($vv2,2)))
                ->setMtoBaseIgv(str_replace(",", "", number_format($st2,2)))
                ->setPorcentajeIgv($IGV_porcentual)
                ->setIgv($igv2)
                ->setTipAfeIgv('10')//Gravado - Operación Onerosa
                ->setTotalImpuestos($igv2)
                ->setMtoPrecioUnitario($pu2)
                ->setIsc(0)
                ->setOtroTributo(0)
            ;

            $itempdf2 ["pro_id"]     = rand();
            $itempdf2 ["pro_desc"]   =$vFila['4'] ;
            $itempdf2 ['pro_cantidad']   = $vFila['5'];
            $itempdf2 ["pro_unimedida"]  = 'NIU';
            $itempdf2 ["pro_preunitario"]    =$pu2;
            $itempdf2 ['pro_preref']     =str_replace(",", "", $pu2);
            $itempdf2 ["pro_tipoprecio"]     = "01"; //Precio Incluye IGV
            $itempdf2 ["pro_igv"]    = $igv2;
            $itempdf2 ['pro_tipoimpuesto']   = "10"; //Gravado - Operación Onerosa
            $itempdf2 ["pro_isc"]    = number_format(0.00,2);
            $itempdf2 ["pro_otroimpuesto"]   = number_format(0.00,2);
            $itempdf2 ['pro_subtotal']   =str_replace(",", "", $st2) ;
            $itempdf2 ['pro_total']  =str_replace(",", "", number_format($t2,2)) ;

            $globalIGV+= str_replace(",", "", $igv2);
            $globalTotalVenta+=str_replace(',', '', number_format($t2,2));
            $globalGrabadas+= str_replace(",", "", $st2);

            if(count($item2)>0){

                array_push($items,$item2);
                array_push($itemsPDF,$itempdf2);

                //Actualiza estado de envío de producto a SUNAT / Esto para cuando se agreguen nuevos productos solo se envíen los NO FACTURADOS
                $sqlDetalleVenta = $link->query("UPDATE ventadetalle SET procesado=1 where idventadetalle='$vFila[2]'");
            }

        }
        //Descuento Global
        $Descuento= $xaFila[16];

        //CORRELATIVO PARA LOS DOCUMENTOS

        $correlativo=$link->query("SELECT * FROM series WHERE codsunat='$this->tipo_documento' and estado=1")->fetch_row();


        $date = new DateTime($xaFila[13]);
        $dateEmi = new DateTime();

        //Validar si es boleta o factura
        $corre=$correlativo[3].'-'.str_pad($correlativo[4], 8, "0", STR_PAD_LEFT);

        $invoice
            ->setUblVersion('2.1')
            ->setFecVencimiento(new \DateTime())
            ->setTipoOperacion('0101')
            ->setTipoDoc($this->tipo_documento)
            ->setSerie($correlativo[3])
            ->setCorrelativo(str_pad($correlativo[4], 8, "0", STR_PAD_LEFT))//str_pad($correlativo[4], 8, "0", STR_PAD_LEFT)
            ->setFechaEmision($dateEmi)
            ->setTipoMoneda('PEN')
            ->setClient($client)
            ->setMtoOperGravadas($globalGrabadas)
            ->setMtoOperExoneradas(0)
            ->setMtoIGV($globalIGV)
            ->setTotalImpuestos($globalIGV)
            ->setValorVenta($globalGrabadas)
            ->setMtoImpVenta($globalTotalVenta)
            ->setCompany($company)
            ->setMtoDescuentos($Descuento);

        $MontoLetras=num_to_letras(($globalTotalVenta - $Descuento),"PEN");

        $invoice->setDetails($items)
            ->setLegends([
                (new Legend())
                    ->setCode('1000')
                    ->setValue($MontoLetras)
            ]);

        /* Datos para generar PDF */
        $dato['TotIgv']=str_replace(',', '', $globalIGV) ;
        $dato['TotVenta']=str_replace(',', '', $globalTotalVenta) ;
        $dato['TotGravada']= str_replace(',', '', $globalGrabadas) ;
        $dato['TotGratuitas']=0.00;
        $dato['TotInafectas']=0.00;
        $dato['TotExoneradas']=0.00;
        $dato['DescuentoGlobal']=$Descuento;
        $dato['Moneda']='PEN';
        $dato['tipo_documento']=trim($correlativo[1]);
        $dato['fecharegistro']=$dateEmi->format('Y-m-d');

        if($xaFila['14'] > 0){
            $dato['formapago']="Efectivo";
        }elseif ($xaFila['15'] > 0) {
            $dato['formapago']="Visa";
        }

        //Nombre para el archivo PDF
        $nombre_archivo = utf8_decode($array['Encabezado']['Emisor']['RUCEmisor'].'-'.$dateEmi->format('Y-m-d').'-'.$corre);

        // Envio a SUNAT.
        //$see = $this->util->getSee(SunatEndpoints::FE_BETA);
        $see = require __DIR__ . '/ConfigDatosSunatEmpresa.php';

        /** Si solo desea enviar un XML ya generado utilice esta función**/
        //$res = $see->sendXml(get_class($invoice), $invoice->getName(), file_get_contents($ruta_XML));

        $res = $see->send($invoice);

        //guardar archivo sin firmar
        $this->util->writeXmlSinFirmar($corre, $see->getFactory()->getBuilder()->build($invoice));

        //guardar archivo firmado
        $this->util->writeXml($invoice, $see->getFactory()->getLastXml());

        if ($res->isSuccess()) {
            /**@var $res \Greenter\Model\Response\BillResult*/
            $cdr = $res->getCdrResponse();
            $this->util->writeCdr($invoice, $res->getCdrZip());

            $codigoRespuesta = $cdr->getId();
            $mensajeSunat = $cdr->getDescription();
            $nombreZip = ''.$invoice->getName().'.zip';

            $this->generapdfinvoice($array,$corre,$dato,$itemsPDF,$MontoLetras,$nombre_archivo,'',$xaFila[3]); //Genera PDF
            $this->ActualizaCorrelativo($correlativo);
            $this->ActualizaVentaProductos($correlativo,$codigoRespuesta,$mensajeSunat,$nombreZip,$nombre_archivo,$corre,2);

            $ArrayMessage=array('success'=> array('ReferenceID' => $corre,'codRespuesta' =>$codigoRespuesta,
                'Description' => $mensajeSunat,'nombre_archivo'=>$nombre_archivo.'.pdf' ),'errors'=>0);

            echo json_encode($ArrayMessage);

        } else {
            //var_dump($res->getError());
            //$error= var_dump($res->getError());
            $ArrayMessage=array('success'=>0,'errors'=>array('getMessage' =>'CodeError: '.$res->getError()->getCode().'.Mensaje: '.$res->getError()->getMessage() ,'getCode'=>0));
            echo json_encode($ArrayMessage);
        }

        //$this->generapdfinvoice($array,$corre,$dato,$items,$MontoLetras,$nombre_archivo,$firmado->firma,$xaFila[3]); //Genera PDF

    }

    public function generapdfinvoice($Emcabezado=array(),$NumDocumento,$Datos,$productos=array(),$MontoLetras,$nombre_archivo,$firma='',$NHabitacion=''){

        $arr=$Emcabezado;
        $medidas = array(90, 350); // Ajustar aqui segun los milimetros necesarios;

        $pdf = new Pdf('P', 'mm', $medidas, true, 'UTF-8', false);
        $pdf->setPageFormat($medidas, $orientation='P');


        $pdf->RUCEmpresa=$arr['Encabezado']['Emisor']['RUCEmisor'];
        $pdf->NumDocumento=$NumDocumento;

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Erwin Torres León');
        $pdf->SetTitle($nombre_archivo);

        $NomDocumentoImp = '';
        switch ($Datos['tipo_documento'])
        {

            case "01":
                $pdf->NomDocumento='FACTURA ELECTRÓNICA';
                $NomDocumentoImp = 'Factura Electrónica';
                break;
            case "03":
                $pdf->NomDocumento='BOLETA DE VENTA ELECTRÓNICA';
                $NomDocumentoImp = 'Boleta de Venta Electrónica';
                break;
            case "07":
                $pdf->NomDocumento='NOTA DE CRÉDITO ELECTRÓNICA';
                $NomDocumentoImp = 'Nota de Crédito Electrónica';
                break;
            case "08":
                $pdf->NomDocumento='NOTA DE DÉDITO ELECTRÓNICA';
                $NomDocumentoImp = 'Nota de Débito Eletrónica';
                break;

        }



        // Establecer el tipo de letra

        //Si tienes que imprimir carácteres ASCII estándar, puede utilizar las fuentes básicas como
        // Helvetica para reducir el tamaño del archivo.
        $pdf->SetFont('Helvetica', '', 12, '', true);

        // Añadir una página
        // Este método tiene varias opciones, consulta la documentación para más información.
        $pdf->Open();
        $pdf->AddPage();
        // ponemos los márgenes
        //$pdf->SetMargins(15,15);

        /* incluimos un rectángulo relleno para contener datos del cliente */
        //$pdf->Rect(15,47,180,22,'FD','',array(255,255,255));
        $pdf->SetX(0);
        $pdf->SetFont('Helvetica','B',8);
        $pdf->SetXY(3,40.5);
        $pdf->Cell(3, 5, "RUC/DNI:",0,0,'L');
        $pdf->SetFont('Helvetica','',8);
        $pdf->SetXY(20,40.5);
        $pdf->Cell(10, 5, $arr['Encabezado']['Receptor']['RUCReceptor'],0,0,'L');
        $pdf->SetFont('Helvetica','B',10);


        $pdf->Ln(4);
        $pdf->SetX(3);
        $pdf->SetFont('Helvetica','B',8);
        $pdf->SetXY(3,45);

        $pdf->Cell(17, 5, "Cliente:".$end_y,0,0,'L');
        $end_y = $pdf->GetY();
        $pdf->SetFont('Helvetica','',8);
        //$pdf->Cell(17, 48, $arr['Encabezado']['Receptor']['RznSoc'],0,0,'L');
        $pdf->MultiCell(68,2,$arr['Encabezado']['Receptor']['RznSoc'],0,'L',FALSE,1,20,$end_y); //51

        //$pdf->Ln();
        $pdf->SetX(3);
        $end_y = $pdf->GetY();
        $pdf->SetXY(3,$end_y);
        $pdf->SetFont('Helvetica','B',8);
        $pdf->Cell(17, 2, "Dirección:",0,0,'L');
        $pdf->SetFont('Helvetica','',7);
        $pdf->MultiCell(68,8,$arr['Encabezado']['Receptor']['Direccion'],0,'L',FALSE,1,20,$end_y+0.5); //54.5


        //$pdf->Ln(4);
        $pdf->SetX(3);
        // get the new Y
        $end_y = $pdf->GetY();
        $pdf->SetXY(3,$end_y);
        $pdf->SetFont('Helvetica','B',8);
        $pdf->Cell(30, 2, "Fecha de Emisión:"); //,FALSE,1,2,$pdf->GetY()

        $pdf->SetXY(30,$end_y);
        $pdf->SetFont('Helvetica','',8);
        $pdf->Cell(30, 2, $Datos['fecharegistro'],0,0,'L');
        //$pdf->SetFont('Helvetica','B',8);
        //$pdf->SetXY(2,44);
        //$pdf->Cell(30, 48, "Moneda:",0,0,'L');
        //$pdf->SetFont('Helvetica','',8);
        //$pdf->SetXY(30,44);
        //$pdf->Cell(30, 48, 'Soles',0,0,'L');

        $pdf->Ln(4);
        $pdf->SetX(3);
        $end_y = $pdf->GetY();
        $pdf->SetXY(3,$end_y);

        $pdf->SetFont('Helvetica','B',8);
        $pdf->Cell(30, 2, "Nº Habitación:",0,0,'L');
        $pdf->SetFont('Helvetica','',8);
        $pdf->SetXY(30,$end_y);
        $pdf->Cell(30, 2, $NHabitacion,0,0,'L');

        //$end_y = $pdf->GetY();
        $pdf->Ln(4);
        $end_y = $pdf->GetY();
        $pdf->SetX(1);
        $pdf->SetXY(1,$end_y);
        $pdf->Cell(100,5,"------------------------------------------------------------------------------------",0,0,'L');
        $pdf->Ln(3);
        // Anchuras de las columnas
        //$w = array(10, 20, 16, 95,18,22);
        $w = array(43,8,11,14);
        //$pdf->Cell(100, 5, "                                        ".$pdf->GetY(),0,0,'L');
        // Títulos de las columnas
        $pdf->SetFont('Helvetica','B',8);
        $header = array('Descripción','Cant.','P. Unit.','Total');
        $end_y = $pdf->GetY();
        $pdf->SetXY(3,$end_y);

        // Cabeceras
        for($i=0;$i<count($header);$i++)
            $pdf->Cell($w[$i],7,$header[$i],0,0,'L',0);



        $pdf->Ln();
        // Datos
        $i=1;
        $pdf->SetFont('Helvetica','',7);


        if($Datos['Moneda']=='PEN'):
            $CodMoneda='S/';
        else:
            $CodMoneda='$';
        endif;

        foreach($productos as $row)
        {
            $pdf->SetX(3);

            $current_y = $pdf->GetY();
            $current_x = $pdf->GetX();

            $acotado = $row['pro_desc'];
            $pdf->MultiCell($w[0],4,$acotado,0,‘L’); //$pdf->SetXY(149,$y);

            $current_x+=$w[0];       //calculate position for next cell
            $pdf->SetXY($current_x, $current_y);    //set position for next cell to print

            $pdf->MultiCell($w[1],4,$row['pro_cantidad'],'0','R');

            $current_x+=$w[1];       //calculate position for next cell
            $pdf->SetXY($current_x, $current_y);    //set position

            $pdf->MultiCell($w[2],4, number_format(($row['pro_total'] / $row['pro_cantidad']),2),'0','R');

            $current_x+=$w[2];       //calculate position for next cell
            $pdf->SetXY($current_x, $current_y);    //set position

            //$pdf->SetFont('Helvetica','B',7);
            $pdf->MultiCell($w[3],4,$CodMoneda.' '.number_format(($row['pro_total']),2),0,'R');
            $current_x+=$w[3];       //calculate position for next cell

            $pdf->Ln();
            $i++;
            $pdf->SetFont('Helvetica','',7);
        }

        /*TOTALES*/
        $pdf->TotGravadas=$CodMoneda." ".number_format($Datos['TotGravada'],2);
        $pdf->TotGratuitas=$CodMoneda." ".number_format($Datos['TotGratuitas'],2);
        $pdf->TotExoneradas=$CodMoneda." ".number_format($Datos['TotExoneradas'],2);
        $pdf->TotInafectas=$CodMoneda." ".number_format($Datos['TotInafectas'],2);


        //$pdf->Ln();

        //VALIDACION PARA EL DESCUENTO GLOBAL

        if($Datos['DescuentoGlobal']>0){
            $Datos['TotVenta']=number_format($Datos['TotVenta'] - $Datos['DescuentoGlobal'],2);
            $Datos['TotGravada']=$Datos['TotVenta'] / 1.18;
            $Datos['TotIgv']=number_format($Datos['TotVenta'] - $Datos['TotGravada'],2);
        }


        $pdf->SetX(0);
        $pdf->Cell(100,5,"------------------------------------------------------------------------------------------------",0,0,'L');
        $pdf->Ln();
        $pdf->SetFont('Helvetica','',7);
        $pdf->Cell(18,5,"OP. GRAVADAS: ".$CodMoneda,0,0,'R');
        $pdf->SetFont('Helvetica','B',7);
        $pdf->Cell(48,5,number_format($Datos['TotGravada'],2),0,0,'R');
        $pdf->Ln();
        $pdf->SetFont('Helvetica','',7);
        $pdf->Cell(21,5,"OP. EXONERADAS: ".$CodMoneda,0,0,'R');
        $pdf->SetFont('Helvetica','B',7);
        $pdf->Cell(45,5,number_format($Datos['TotExoneradas'],2),0,0,'R');
        $pdf->Ln();
        $pdf->SetFont('Helvetica','',7);
        $pdf->Cell(18,5,"OP. INAFECTAS: ".$CodMoneda,0,0,'R');
        $pdf->SetFont('Helvetica','B',7);
        $pdf->Cell(48,5,number_format($Datos['TotInafectas'],2),0,0,'R');
        $pdf->Ln();
        $pdf->SetFont('Helvetica','',7);
        $pdf->Cell(18,5,"OP. GRATUITAS: ".$CodMoneda,0,0,'R');
        $pdf->SetFont('Helvetica','B',7);
        $pdf->Cell(48,5,number_format($Datos['TotGratuitas'],2),0,0,'R');
        $pdf->Ln();
        $pdf->SetFont('Helvetica','',7);
        $pdf->Cell(18,5,"IGV: ".$CodMoneda,0,0,'R');
        $pdf->SetFont('Helvetica','B',7);
        $pdf->Cell(48,5,str_replace(',', '', $Datos['TotIgv']),0,0,'R'); //number_format($Datos['TotIgv'],2)
        $pdf->Ln();
        $pdf->SetFont('Helvetica','',7);
        $pdf->Cell(18,5,"DESCUENTO: ".$CodMoneda,0,0,'R');
        $pdf->SetFont('Helvetica','B',7);
        $pdf->Cell(48,5,number_format($Datos['DescuentoGlobal'],2),0,0,'R');
        $pdf->Ln();
        $pdf->SetFont('Helvetica','',7);
        $pdf->Cell(18,5,"TOTAL: ".$CodMoneda,0,0,'R');
        $pdf->SetFont('Helvetica','B',7);
        $pdf->Cell(48,5,number_format($Datos['TotVenta'],2),0,0,'R');
        $pdf->Ln();
        $pdf->SetX(40);
        $pdf->Cell(40,5,"-----------------------------------------------------------------------------------------------",0,0,'R');
        $pdf->Ln();
        $pdf->SetX(5);

        //$pdf->Cell(50,5,"SON ".$MontoLetras,0,0,'L');
        //$yy1=$pdf->GetY();
        $pdf->MultiCell(70,4,"SON: ".$MontoLetras,0,'L',FALSE,1,6,$pdf->GetY());
        //$pdf->Ln();
        $pdf->SetX(40);
        $pdf->Cell(40,5,"-----------------------------------------------------------------------------------------------",0,0,'R');
        $pdf->Ln();
        $datosAdicionales_CDB=$arr['Encabezado']['Emisor']['RUCEmisor']."|".$Datos['tipo_documento']."|".$NumDocumento."|".$Datos['TotIgv']."|".$Datos['TotVenta']."|".$Datos['fecharegistro']."|".$arr['Encabezado']['Receptor']['TipoDocumento']."|".$arr['Encabezado']['Receptor']['RUCReceptor'];


        // set style for barcode
        $style = array(
            'border' => 2,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        $pdf->CodBarras=$datosAdicionales_CDB;
        $pdf->SetX(2);
        $pdf->Ln();
        $pdf->SetX(10);
        //$pdf->Cell(15,5,$firma,0,'C');

        //$pdf->Cell(2,5,'Representación impresa de la '.ucwords(strtolower($pdf->strtolower_utf8($pdf->NomDocumento))).'',0,'C');
        $pdf->Cell(2,5,'Representación impresa de la '.$NomDocumentoImp,0,'C');
        //$pdf->Cell(2,5,'Representación impresa del comprobante electronico'.'',0,'J');
        $pdf->Ln(2);
        $alto=$pdf->GetY();
        $pdf->SetX(25);
        $pdf->Ln(2);
        $pdf->Cell(2,8,$pdf->write2DBarcode($datosAdicionales_CDB, 'QRCODE', 35, $alto+5, 20, 20, $style, 'N'),0,'J');
        $pdf->Ln(2);
        $yy=$pdf->GetY();
        $pdf->SetX(26);
        $pdf->Cell(2,5,'Forma de Pago: '.$Datos['formapago'],0,'J');

        $pdf->Ln(6);
        $y=$pdf->GetY();
        $pdf->MultiCell(70,8,"Gracias... vuelva pronto!!!",0,'C',FALSE,1,6,$y+5);

        $pdf->Output('PDF/'.$nombre_archivo.'.pdf', 'F');

    }

    private function sanear_string($string)
    {
     
        $string = trim($string);
     
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
     
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );
     
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
     
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );
     
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
     
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );
     
        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array(
                 "·", "$", "%", "&", "/",
                 "(", ")", "?", "'", "¡",
                 "¿", "[", "^", "<code>", "]",
                 "+", "}", "{", "¨", "´",
                 ">", "< ", ";", ",", ":",
                 "."),
            '',
            $string
        );
     
     
        return $string;
    }

}



