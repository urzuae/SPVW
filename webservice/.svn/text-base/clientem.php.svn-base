<?php
require_once('nusoap/lib/nusoap.php');
#$cliente = new nusoap_client('http://localhost/p_vw/webservice/webservicemodelos.php');
$cliente = new nusoap_client('http://10.0.0.98/vw/webservice/webservicemodelos.php');
$resultado = $cliente->call('modelos', array('x' => '1'));
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