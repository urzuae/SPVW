<?php
require_once('nusoap/lib/nusoap.php');
require_once('listado_modelos.php');
$ns="http://10.0.0.98/vw/webservice";
#$ns="http://localhost/p_vw/webservice";
$server = new nusoap_server();
$server->configureWSDL('vwmwsdl2', 'urn:vwmwsdl2');
$server->wsdl->addComplexType(
    'ListaModelos',
    'complexType',
    'array',
    'all',
    '',
    array(
        'id'     => array('unidad_id'  => 'unidad_id',  'type' => 'xsd:int'),
        'modelo' => array('nombre' => 'nombre', 'type' => 'xsd:string')
    )
);
$server->register('modelos',                    // method name
    array('x'      => 'xsd:int'),          // input parameters
    array('return' => 'tns:ListaModelos'),    // output parameters
    'urn:vwmwsdl2',                         // namespace
    'urn:vwmwsdl2#vw',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Envia el listado de modelos'        // documentation
);
$server->wsdl->schemaTargetNamespace=$ns;
// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>