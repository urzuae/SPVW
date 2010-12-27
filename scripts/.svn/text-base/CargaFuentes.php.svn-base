<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
global $db;

$sql = "drop table crm_fuentes";
$sqlCreateTable = "CREATE TABLE IF NOT EXISTS `crm_fuentes` (
  `fuente_id` int(11) NOT NULL auto_increment,
  `nombre` varchar(300) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`fuente_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT = 1;
";
$db->sql_query($sql) or die("Error al borrar la tabla crm_fuentes->".$sql);
$db->sql_query($sqlCreateTable) or die("Error al crear la tabla crm_fuentes->".$sqlCreateTable);

$sql = "select origenes.nombre as nombre from crm_contactos_origenes as origenes";
$result = $db->sql_query($sql);

while(list($nombre) = $db->sql_fetchrow($result))
{
    $currentDay = date("Y-m-d H:i:s");
    $db->sql_query("insert into crm_fuentes values('','$nombre','$currentDay')");
}

$sql = "select padre.origen_padre as nombre  from crm_contactos_origenes_padre as padre";
$result = $db->sql_query($sql);
while(list($nombre) = $db->sql_fetchrow($result))
{
    $currentDay = date("Y-m-d H:i:s");
    $db->sql_query("insert into crm_fuentes values('','$nombre','$currentDay')");
}
?>
