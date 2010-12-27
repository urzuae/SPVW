<?php
if (!defined('_IN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $contactos;
$array_contactos=explode('|',$contactos);
$elemento = array_pop($array_contactos);
$eliminados=0;

foreach($array_contactos as $valor)
{
    $del="UPDATE mfll_contactos_registro set cargado=2 WHERE contacto_id=".$valor.";";
    if($db->sql_query($del))
    {
        $eliminados++;
    }
}
echo $eliminados;
die();
?>