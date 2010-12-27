<?php
header ("content-type: text/xml");
require_once('nusoap/lib/nusoap.php');
$cliente = new nusoap_client('http://10.0.0.98/vw/webservice/servicio_vendedores.php');
$resultado = $cliente->call('vendedores', array('x' => '1','y' => '2016,0203'));
echo $resultado;
?>
