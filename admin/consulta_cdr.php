<?php
declare(strict_types=1);

include('../init.php');
include "config.php";
use Greenter\Model\Response\StatusCdrResult;
use Greenter\Ws\Services\ConsultCdrService;
use Greenter\Ws\Services\SoapClient;
use Greenter\Ws\Services\SunatEndpoints;
use PhpParser\Node\Stmt\TryCatch;

require __DIR__ . '/../vendor/autoload.php';

$errorMsg = null;
$filename = null;

/**
 * @param array<string, string> $items
 * @return bool
 */
function validateFields(array $items): bool
{
    global $errorMsg;
    $validateFiels = ['rucSol', 'userSol', 'passSol', 'ruc', 'tipo', 'serie', 'numero'];
    foreach ($items as $key => $value) {
        if (in_array($key, $validateFiels) && empty($value)) {
            $errorMsg = 'El campo '.$key.', es requerido';
            return false;
        }
    }

    return true;
}

/**
 * @param string $user
 * @param string $password
 * @return ConsultCdrService
 */
function getCdrStatusService(?string $user, ?string $password): ConsultCdrService
{
    $ws = new SoapClient(SunatEndpoints::FE_CONSULTA_CDR.'?wsdl');
    $ws->setCredentials($user, $password);

    $service = new ConsultCdrService();
    $service->setClient($ws);

    return $service;
}

/**
 * @param string $filename
 * @param string $content
 */
function savedFile(?string $filename, ?string $content): void
{
    $fileDir = __DIR__ . '/../admin/FE/CDR/';

    if (!file_exists($fileDir)) {
        mkdir($fileDir, 0777, true);
    }
    $pathZip = $fileDir.DIRECTORY_SEPARATOR.$filename;
  
    file_put_contents($pathZip, $content);
}

/**
 * @param array<string, string> $fields
 * @return StatusCdrResult|null
 */
function process(array $fields): ?StatusCdrResult
{
    global $filename;

    if (!isset($fields['rucSol'])) {
        return null;
    }

    if (!validateFields($fields)) {
        return null;
    }

    $service = getCdrStatusService($fields['rucSol'].$fields['userSol'], $fields['passSol']);

    $arguments = [
        $fields['ruc'],
        $fields['tipo'],
        $fields['serie'],
        intval($fields['numero'])
    ];
   
    if (isset($fields['cdr'])) {
        $result = $service->getStatusCdr(...$arguments);
        if ($result->getCdrZip()) {
            $filename = 'R-'.implode('-', $arguments).'.zip';
            savedFile($filename, $result->getCdrZip());
        }

        return $result;
    }

    return $service->getStatus(...$arguments);
}


try{
   /* $DOC = $args;
    $params = array(
        'cdr' => true,
        'rucComprobante' => 'xxxxxxxxxxxxxxxX',
        'rucComprobante' => $DOC['DocEmisorDocNumero'],
        'tipoComprobante' => $DOC['DocTipo'],
        'serieComprobante' => $DOC['DocSerie'],
        'numeroComprobante' => $DOC['DocNumero']
    );*/
    $rsSunat = process($_POST);

    if($rsSunat->isSuccess()){
        $json = array();
        $json["success"] = true;
        $json['codigo'] = $rsSunat->getCode();
        $json['message'] = $rsSunat->getMessage();

        $nDocumento= $_POST['serie'].'-'.$_POST['numero'];
        $db = new conexion();
        $link = $db->conexion();
        $link->query("UPDATE al_venta SET codigo_respuesta = 0, mensaje_respuesta ='El comprobante fue aceptado', enviado=2 
        WHERE documento ='$nDocumento' ");

        echo json_encode($json);
    }else{
        $error = $rsSunat->getError();
        throw new Exception("Error " . $error->getCode() . " | " .$error->getMessage());
    }
}catch(Exception $e){
    $json = array();
    $json["sucess"] = false;
    $json['codigo'] = $e->getCode();
    $json['message'] = $e->getMessage();
    echo json_encode($json);
}

?>