<?php
require_once('nusoap/lib/nusoap.php');
#$cliente = new nusoap_client('http://localhost/p_vw/webservice/webservicevendedores.php');
$cliente = new nusoap_client('http://10.0.0.98/vw/webservice/webservicevendedores.php');
$resultado = $cliente->call('vendedores', array('x' => '2016,0203'));
if ($cliente->fault)
{
    echo '<h2>Fallo en el arreglo</h2><pre>';
    print_r($resultado);
    echo '</pre>';
}
else
{
    $err = $cliente->getError();
    if ($err)
    {
        echo '<h2>Error</h2><pre>' . $err . '</pre>';
    }
    else
    {
        echo"<br><pre>";
        print_r($resultado);
        echo"</pre><br>";
    }
}
?>