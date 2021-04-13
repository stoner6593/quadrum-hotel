<?php 

$params = parse_ini_file('params.ini', TRUE);

$environment = $params['app']['environment'];

define('APP_ENVIRONMENT',			$environment);
define('APP_EMISOR_RUC',			$params[APP_ENVIRONMENT]['emisor_ruc']);
define('APP_EMISOR_NOMBRE',			$params[APP_ENVIRONMENT]['emisor_nombre']);
define('APP_EMISOR_RAZONSOCIAL',	$params[APP_ENVIRONMENT]['emisor_razonsocial']);
define('APP_EMISOR_UBIGUEO',		$params[APP_ENVIRONMENT]['emisor_ubigueo']);
define('APP_EMISOR_DISTRITO',		$params[APP_ENVIRONMENT]['emisor_distrito']);
define('APP_EMISOR_PROVINCIA',		$params[APP_ENVIRONMENT]['emisor_provincia']);
define('APP_EMISOR_DEPARTAMENTO',	$params[APP_ENVIRONMENT]['emisor_departamento']);
define('APP_EMISOR_URBANIZACION',	$params[APP_ENVIRONMENT]['emisor_urbanizacion']);
define('APP_EMISOR_CODLOCAL',		$params[APP_ENVIRONMENT]['emisor_codlocal']);
define('APP_EMISOR_DIRECCION',		$params[APP_ENVIRONMENT]['emisor_direccion']);
define('APP_SUNAT_ENDPOINT',		$params[APP_ENVIRONMENT]['sunat_endpoint']);
define('APP_SUNAT_WSDL',			$params[APP_ENVIRONMENT]['sunat_wsdl']);
define('APP_SUNAT_USER',			$params[APP_ENVIRONMENT]['sunat_user']);
define('APP_SUNAT_PASS',			$params[APP_ENVIRONMENT]['sunat_pass']);

