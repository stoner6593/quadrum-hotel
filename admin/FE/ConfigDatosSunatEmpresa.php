<?php
require_once "../../init.php";

use Greenter\Ws\Services\SunatEndpoints;

$see = new \Greenter\See();
$see->setService(SunatEndpoints::FE_PRODUCCION);
//$see->setCertificate(file_get_contents(__DIR__.'/../resources/cert.pem'));
$see->setCertificate(file_get_contents(realpath(__DIR__ ).'/certificado/cert.pem'));

$see->setCredentials(APP_SUNAT_USER, APP_SUNAT_PASS);


/*$see = new \Greenter\See();
$see->setService(SunatEndpoints::FE_BETA);
//$see->setCertificate(file_get_contents(__DIR__.'/../resources/cert.pem'));
$see->setCertificate(file_get_contents(realpath(__DIR__ ).'/certificado/cert.pem'));

$see->setCredentials('20545756022MODDATOS', 'moddatos');*/


return $see;