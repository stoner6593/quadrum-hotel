<?php
require_once "../../init.php";

session_start();
//include "generales/convertir.php";
include "../config.php";
include "../include/functions.php";
include "Pdf.php";

use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;

require __DIR__ . '/../../vendor/autoload.php';

$util = Util::getInstance();

$dato=new GeneraxmlCB($_POST['idVenta'],$_POST['tipo_documento'],$_POST['finicio'],$_POST['ffin'],strtoupper($_POST['Motivo']));


    if($_POST['tipoVenta']=='1') {
        $dato->generar_xml();
    }
    if($_POST['tipoVenta']=='2') {
        $dato->generar_xml_venta();
    }

class GeneraxmlCB
{

    //private $sqlalquiler=null;
    public $idalquiler;
    public $tipo_documento;
    public $finicio;
    public $ffin;
    public $motivo;

    //Empresa Emisora
    public $RUCEmisor ;
    public $NombreComercial ;
    public $RazonSocial;

    //Address
    public $Ubigueo ;
    public $Distrito ;
    public $Provincia ;
    public $Departamento ;
    public $Urbanizacion ;
    public $CodLocal ;
    public $Direccion ;

    function __construct($idalquiler, $tipo_documento, $finicio = null, $ffin,$motivo)
    {

        $this->idalquiler = $idalquiler;
        $this->tipo_documento = $tipo_documento;
        $this->finicio = $finicio;
        $this->ffin = $ffin;
        $this->motivo = $motivo;
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
			al_venta.iddocumento,
			al_venta.fechaemision,
			al_venta.documento
			from al_venta inner join cliente on cliente.idhuesped = al_venta.idhuesped
			where al_venta.idalquiler = '$this->idalquiler' 
			and al_venta.anulado = 0
			");
        $xaFila = $sqlalquiler->fetch_row();

        $company = new Company();

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

        $documento = $xaFila[20];
        $fechaEmision = $xaFila[19];

        $dateGene = new DateTime($xaFila[19]);
        $dateFecComunicacion = new \DateTime();

        $now = (new \DateTime())->format('Y-m-d');

        $arrDoc = split ("-", $documento);

        $correlativoCB = 1;

        $correlativo=$link->query("SELECT codsunat FROM series WHERE serie='$arrDoc[0]' and estado=1")->fetch_row();

        $CCB=$link->query("select correlativo from comunicacion_baja where fecha = '".$now."'")->fetch_row();

        if(!is_null($CCB[0])){
            $correlativoCB = $CCB[0] + 1;
        }

        $detial1 = new VoidedDetail();
        $detial1->setTipoDoc($correlativo[0])
            ->setSerie($arrDoc[0])
            ->setCorrelativo($arrDoc[1])
            ->setDesMotivoBaja($this->motivo);

        $voided = new Voided();
        $voided->setCorrelativo($correlativoCB)
            ->setCompany($company)
            ->setDetails([$detial1]);

        $voided->setFecGeneracion($dateGene);
        $voided->setFecComunicacion($dateFecComunicacion);

        $see = require __DIR__ . '/ConfigDatosSunatEmpresa.php';

        // Envio a SUNAT.
        $res = $see->send($voided);
        $this->util->writeXml($voided, $see->getFactory()->getLastXml());

        if ($res->isSuccess()) {
            /**@var $res \Greenter\Model\Response\SummaryResult*/
            $ticket = $res->getTicket();

            $_SESSION['msgsuccess'] = 'Ticket :<strong>' . $ticket .'</strong><br/>';

            $result = $see->getStatus($ticket);
            if ($result->isSuccess()) {
                $cdr = $result->getCdrResponse();
                $this->util->writeCdr($voided, $result->getCdrZip());

                $codigoRespuesta = $cdr->getId();
                $mensajeSunat = $cdr->getDescription();
                $nombreZip = 'R-'.$voided->getName().'.zip';

                $_SESSION['msgsuccess'] = $_SESSION['msgsuccess'].$this->util->getResponseFromCdr($cdr).
                    '<br/><b><a href="FE/CDR/'.$nombreZip.'" target="_blank">Descargar CDR</a></b>';

                $id_cb = $this->InsertarComunicacionBaja($correlativoCB,0,$cdr->getDescription(),$nombreZip,'R-'.$voided->getName(),$voided->getName(),$now);

                $this->ActualizaVenta($this->motivo,$id_cb);

            } else {
                $_SESSION['msgerror'] = var_dump($res->getError());

                $id_cb = $this->InsertarComunicacionBaja($correlativoCB,-1,$_SESSION['msgerror'],'','',$voided->getName(),$now);

            }
        } else {
            $_SESSION['msgerror'] = var_dump($res->getError()).'. Error, Intentelo denuevo...';

            $id_cb = $this->InsertarComunicacionBaja($correlativoCB,-1,$_SESSION['msgerror'],'','',$voided->getName(),$now);
        }

        header("Location: ../comunicacionBaja.php");
        exit;
    }

    //Insertar Comunicacion baja
    function InsertarComunicacionBaja($correlativo,$cod_estado_envio_sunat,$mensaje_envio_sunat,$nombre_archivo_zip,$nombre_archivo,$nombre_documento,$fecha){
        $db = new conexion();
        $link = $db->conexion();
        $idCB = -1;
        $resul = $link->query("INSERT INTO comunicacion_baja(correlativo,cod_estado_envio_sunat,mensaje_envio_sunat,nombre_archivo_zip,
                              nombre_archivo,nombre_documento,fecha)
                              VALUES ('$correlativo','$cod_estado_envio_sunat','$mensaje_envio_sunat','$nombre_archivo_zip',
                              '$nombre_archivo','$nombre_documento','$fecha')");

        if($resul){
            $rowCB = $link->query("select * from comunicacion_baja
                  order by id_cb desc limit 1");

            $cFila = $rowCB->fetch_row();
            $idCB = $cFila[0];
        }

        return $idCB;

    }

    //Actualizar Correlativo
    function ActualizaCorrelativo($numeracion=array()){
        $db = new conexion();
        $link = $db->conexion();
        return $link->query("UPDATE series SET numeracion = numeracion + 1 WHERE codsunat='$numeracion[1]' and iddocumento='$numeracion[0]'");

    }
    //Actualiza datos de venta
    function ActualizaVenta($motivo,$id_cb){
        $db = new conexion();
        $link = $db->conexion();
        return $link->query("UPDATE al_venta SET anulado_motivo ='$motivo',anulaporusuario=1,id_cb='$id_cb' 
			WHERE idalquiler ='$this->idalquiler'");
    }

    //Actualiza datos de venta
    function ActualizaVentaProductos($motivo,$id_cb){
        $db = new conexion();
        $link = $db->conexion();
        return $link->query("UPDATE venta SET anulado_motivo ='$motivo',anulado=1,id_cb='$id_cb' 
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
			0,
			a.documento			
			
			from venta a 
            left join cliente b on b.idhuesped = a.idcliente
			where a.idventa = '$this->idalquiler' 
			");
        $xaFila = $sqlalquiler->fetch_row();

        $company = new Company();

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

        $documento = $xaFila[17];
        $fechaEmision = $xaFila[13];

        $dateGene = new DateTime($xaFila[13]);
        $dateFecComunicacion = new \DateTime();

        $now = (new \DateTime())->format('Y-m-d');

        $arrDoc = split ("-", $documento);

        $correlativoCB = 1;

        $correlativo=$link->query("SELECT codsunat FROM series WHERE serie='$arrDoc[0]' and estado=1")->fetch_row();

        $CCB=$link->query("select correlativo from comunicacion_baja where fecha = '".$now."'")->fetch_row();

        if(!is_null($CCB[0])){
            $correlativoCB = $CCB[0] + 1;
        }

        $detial1 = new VoidedDetail();
        $detial1->setTipoDoc($correlativo[0])
            ->setSerie($arrDoc[0])
            ->setCorrelativo($arrDoc[1])
            ->setDesMotivoBaja($this->motivo);

        $voided = new Voided();
        $voided->setCorrelativo($correlativoCB)
            ->setCompany($company)
            ->setDetails([$detial1]);

        $voided->setFecGeneracion($dateGene);
        $voided->setFecComunicacion($dateFecComunicacion);

        $see = require __DIR__ . '/ConfigDatosSunatEmpresa.php';

        // Envio a SUNAT.
        $res = $see->send($voided);
        $this->util->writeXml($voided, $see->getFactory()->getLastXml());

        if ($res->isSuccess()) {
            /**@var $res \Greenter\Model\Response\SummaryResult*/
            $ticket = $res->getTicket();

            $_SESSION['msgsuccess'] = 'Ticket :<strong>' . $ticket .'</strong><br/>';

            $result = $see->getStatus($ticket);
            if ($result->isSuccess()) {
                $cdr = $result->getCdrResponse();
                $this->util->writeCdr($voided, $result->getCdrZip());

                $codigoRespuesta = $cdr->getId();
                $mensajeSunat = $cdr->getDescription();
                $nombreZip = 'R-'.$voided->getName().'.zip';

                $_SESSION['msgsuccess'] = $_SESSION['msgsuccess'].$this->util->getResponseFromCdr($cdr).
                    '<br/><b><a href="FE/CDR/'.$nombreZip.'" target="_blank">Descargar CDR</a></b>';

                $id_cb = $this->InsertarComunicacionBaja($correlativoCB,0,$cdr->getDescription(),$nombreZip,'R-'.$voided->getName(),$voided->getName(),$now);

                $this->ActualizaVentaProductos($this->motivo,$id_cb);

            } else {
                $_SESSION['msgerror'] = var_dump($res->getError());

                $id_cb = $this->InsertarComunicacionBaja($correlativoCB,-1,$_SESSION['msgerror'],'','',$voided->getName(),$now);

            }
        } else {
            $_SESSION['msgerror'] = var_dump($res->getError()).'. Error, Intentelo denuevo...';

            $id_cb = $this->InsertarComunicacionBaja($correlativoCB,-1,$_SESSION['msgerror'],'','',$voided->getName(),$now);
        }

        header("Location: ../comunicacionBaja.php");
        exit;

    }

}



