<?php
global $db, $fuente_id;
$sql="SELECT nombre,active,fecha_inicial,fecha_final FROM crm_fuentes WHERE fuente_id=".$fuente_id.";";
$res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
if($db->sql_numrows($res) > 0)
{
    list($nombre,$active,$fecha_ini,$fecha_fin) = $db->sql_fetchrow($res);
}
?>
