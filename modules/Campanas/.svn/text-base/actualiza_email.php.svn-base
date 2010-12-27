<?php
if (!defined('_IN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db,$contacto_id,$email;
echo"<br>esntra al archivo:    ".$contacto_id."   ".$email;
if( ($email != '') && ($contacto_id > 0) )
{
    $upd="UPDATE crm_contactos SET email='".$email."' WHERE contacto_id='".$contacto_id."';";
    $db->sql_query($upd) or die ("Error en la actualización de correo");
}
echo"<br>upd:  ".$upd;
die();
?>
