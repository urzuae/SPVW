<?php
header ("content-type: text/xml");
require_once('nusoap/lib/nusoap.php');
$cliente = new nusoap_client('http://10.0.0.98/vw/webservice/servicio_modelos.php');
$resultado = $cliente->call('modelos', array('x' => '1'));
echo $resultado;
?>
