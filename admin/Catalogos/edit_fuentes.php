<?php
if (!defined('_IN_ADMIN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db,$opc,$padre_id,$fuente,$fecha_ini,$fecha_fin;

switch($opc)
{
    case 1:
        $regreso = Actualiza_Fuente($db,$padre_id,$fuente,$fecha_ini,$fecha_fin);
        break;

    case 2:
        $regreso = Bloquea_Fuente($db,$padre_id);
        break;
    case 3:
        $regreso = Desbloquea_Fuente($db,$padre_id);
        break;
    case 4:
        $regreso = Elimina_Fuente($db,$padre_id);
        break;


}
echo $regreso;
die();


function Actualiza_Fuente($db,$padre_id,$fuente,$fecha_ini,$fecha_fin)
{
    $reg="No actualizado";
    $fecha_inicio=substr($fecha_ini,6,4).'-'.substr($fecha_ini,3,2).'-'.substr($fecha_ini,0,2).' '.substr($fecha_ini,11,8);
    $fecha_final =substr($fecha_fin,6,4).'-'.substr($fecha_fin,3,2).'-'.substr($fecha_fin,0,2).' '.substr($fecha_fin,11,8);
    $fuente=ucwords($fuente);
    $update="UPDATE crm_fuentes SET nombre='".$fuente."', fecha_inicial='".$fecha_inicio."',fecha_final='".$fecha_final."' WHERE fuente_id='".$padre_id."';";
    if($db->sql_query($update) or die ("Error en el Update:  ".$update))
    {
        $reg="Actualizado";
    }
    return $reg;
}

function Bloquea_Fuente($db,$padre_id)
{
    $regreso='';
    if($db->sql_query("UPDATE crm_fuentes SET active=0 WHERE fuente_id='".$padre_id."';"))
            $regreso="Se ha bloqueado la fuente ";
    return $regreso;
}

function Desbloquea_Fuente($db,$padre_id)
{
    $regreso='';
    if($db->sql_query("UPDATE crm_fuentes SET active=1 WHERE fuente_id='".$padre_id."';"))
            $regreso="Se ha Desbloqueado la fuente ";
    return $regreso;
}

function Elimina_Fuente($db,$padre_id)
{
    $regreso='';
    if($db->sql_query("UPDATE crm_fuentes SET active=2 WHERE fuente_id='".$padre_id."';"))
            $regreso="Se ha Eliminado la fuente ";
    return $regreso;
}
?>