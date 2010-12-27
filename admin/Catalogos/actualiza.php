<?php
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die("No puedes acceder directamente a este archivo...");
}
global $_admin_menu2,$db,$id;

include_once("menu_derecho.php");
if($id != 0)
{
    $sql="SELECT nombre,fecha_inicial,fecha_final FROM crm_fuentes WHERE fuente_id='".$id."' limit 1;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        list($nombre,$fecha_inicio,$fecha_final) = $db->sql_fetchrow($res);
        $fecha_ini=substr($fecha_inicio,8,2).'-'.substr($fecha_inicio,5,2).'-'.substr($fecha_inicio,0,4).' '.substr($fecha_inicio,11,8);
        $fecha_fin =substr($fecha_final,8,2).'-'.substr($fecha_final,5,2).'-'.substr($fecha_final,0,4).' '.substr($fecha_final,11,8);
    }
}
?>