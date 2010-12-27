<?php
require_once('nusoap/lib/nusoap.php');
require_once('listado_vendedores.php');
$ns="http://10.0.0.98/vw/webservice";
#$ns="http://localhost/p_vw/webservice";
$server = new nusoap_server();
$server->configureWSDL('vwvwsdl2', 'urn:vwvwsdl2');
$server->wsdl->addComplexType(
    'ListaVendedores',
    'complexType',
    'array',
    'all',
    '',
    array(
        'gid'    => array('gid'  => 'gid',  'type' => 'xsd:int'),
        'uid'    => array('uid'  => 'uid',  'type' => 'xsd:int'),
        'nombre' => array('name' => 'name', 'type' => 'xsd:string'),
        'user'   => array('user' => 'user', 'type' => 'xsd:string'),
        'tipo_acceso'   => array('tipo_acceso' => 'tipo_acceso', 'type' => 'xsd:string'),
        'email'   => array('email' => 'email', 'type' => 'xsd:string')
    )
);
$server->register('vendedores',                    // method name
    array('x'      => 'xsd:string'),          // input parameters
    array('return' => 'tns:ListaVendedores'),    // output parameters
    'urn:vwvwsdl2',                         // namespace
    'urn:vwvwsdl2#vw',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Envia el listado de vendedores'        // documentation
);
$server->wsdl->schemaTargetNamespace=$ns;
// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>