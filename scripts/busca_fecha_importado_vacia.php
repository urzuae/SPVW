<?php
global $db;

//obtener la lista de vehículos
$sql = "SELECT contacto_id FROM crm_contactos WHERE fecha_importado = '0000-00-00'";
$r = $db->sql_query($sql) or die($sql);
$i = $j = 0;
while (list($contacto_id) = $db->sql_fetchrow($r))
{
    
    $i++;
    $sql = "SELECT timestamp FROM `crm_contactos_asignacion_log` WHERE `contacto_id` ='$contacto_id' ORDER BY timestamp ASC";
    $r2 = $db->sql_query($sql) or die($sql);
    list($timestamp) = $db->sql_fetchrow($r2);
    if ($timestamp != "") $j++;
    $sql2 = "UPDATE crm_contactos set fecha_importado = '$timestamp' where contacto_id='$contacto_id'";
    $r3 = $db->sql_query($sql2) or die($sql2);
    echo "$contacto_id - $timestamp => $sql2\n";
}
echo "$i sin timestamp, $j con asignacion log<<<<\n";  


//obtener la lista de vehículos
$sql = "SELECT contacto_id, UNIX_TIMESTAMP(fecha_importado), fecha_importado FROM crm_contactos WHERE 1";//fecha_importado >= '2009-1-1'
$r = $db->sql_query($sql) or die($sql);
$total = $db->sql_numrows($r);
$i = $j = 0;
while (list($contacto_id, $ts_fecha_importado, $fecha_importado) = $db->sql_fetchrow($r))
{
    
    $i++;
    $sql = "SELECT timestamp, UNIX_TIMESTAMP(timestamp) FROM `crm_contactos_asignacion_log` WHERE `contacto_id` ='$contacto_id' ORDER BY timestamp ASC";
    $r2 = $db->sql_query($sql) or die($sql);
    list($timestamp, $ts_timestamp) = $db->sql_fetchrow($r2);
    if ($ts_timestamp < $ts_fecha_importado && $timestamp != "") 
    {
        $j++;
        $sql2 = "UPDATE crm_contactos set fecha_importado = '$timestamp' where contacto_id='$contacto_id'";
        $r3 = $db->sql_query($sql2) or die($sql2);
        echo "$i/$total) $contacto_id - $fecha_importado -> $timestamp => $sql2\n";
    }
}
echo "$i sin timestamp, $j con asignacion log<<<<\n";  

?>
