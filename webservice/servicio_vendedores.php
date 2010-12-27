<?php
require_once('nusoap/lib/nusoap.php');
require_once('lista_vendedores.php');
$ns="http://10.0.0.98/vw/webservice";
$server = new nusoap_server();
$server->configureWSDL('VW',$ns);

$server->wsdl->schemaTargetNamespace=$ns;
$server->register('vendedores',array('x' => 'xsd:string'), array('return' => 'xsd:string'),$ns);
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>