<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define('_IN_MAIN_INDEX', '1');
//chdir('/var/www/vw');
////////////////////////////// INIT ALL ////////////////////////////////////////
require_once("config.php");
require_once("$_includesdir/main.php");
require_once ("libForHRAC.php");

global $db;
$startDate = $argv[1];
$lastDate = $argv[2];
if(empty ($startDate) && empty ($lastDate))
die("Por favor proporcione al menos una fecha para el reporte");
if(empty ($lastDate))
$lastDate = $startDate;

$sql = "select contacto_id, uid from reporte_contactos_asignados where fecha_importado >= '$startDate' and fecha_importado <= '$lastDate'";
$result = $db->sql_query($sql) or die("Error al obtener la lista de contactos".$sql);
while(list($contacto_id,$uid) = $db->sql_fetchrow($result))
{
    if($uid > 0)
    {
        setFollowAndHoursDelayContact($db, $contacto_id,$uid);
        getHoursDelayAttention($db, $contacto_id, $uid);
    }
}

?>
