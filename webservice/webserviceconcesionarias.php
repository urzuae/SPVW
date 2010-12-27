<?php
require_once('nusoap/lib/nusoap.php');
require_once('lista_concesionarias.php');
#$ns="http://localhost/p_vw/webservice";
$ns="http://10.0.0.98/vw/webservice";
$server = new nusoap_server();
$server->configureWSDL('vwwsdl2', 'urn:vwwsdl2');
$server->wsdl->addComplexType(
    'ListaConcesionarias',
    'complexType',
    'array',
    'all',
    '',
    array(
        'id'     => array('gid'  => 'gid',  'type' => 'xsd:int'),
        'nombre' => array('name' => 'name', 'type' => 'xsd:string')
    )
);
$server->register('concesionarias',                    // method name
    array('x'      => 'xsd:int'),          // input parameters
    array('return' => 'tns:ListaConcesionarias'),    // output parameters
    'urn:vwwsdl2',                         // namespace
    'urn:vwwsdl2#vw',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Envia el listado de las concesionarias'        // documentation
);
$server->wsdl->schemaTargetNamespace=$ns;
// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>
