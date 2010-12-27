<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit, $add, $del,
        $nombre, $campana_id, $groups;

if (!$campana_id)
{
    die("Necesitas seleccionar una campaña antes.");
}
else
{
    if ($add)
    {
        list($last_order) = $db->sql_fetchrow($db->sql_query("SELECT orden FROM crm_campanas_llamadas_status WHERE campana_id='$campana_id' ORDER BY orden DESC LIMIT 1"));
        $nombre = strtolower($nombre);
        $db->sql_query("INSERT INTO crm_campanas_llamadas_status (campana_id, nombre,orden)VALUES('$campana_id', '$nombre', '$last_order'+1)") or die("No se puede agregar".print_r($db->sql_error($db)));
        $done .= " - Status $nombre agregado";
    }
    if ($del)
    {
        $db->sql_query("DELETE FROM crm_campanas_llamadas_status WHERE campana_id='$campana_id' AND status_id='$del'") or die("No se puede borrar".print_r($db->sql_error($db)));
        $done .= " - Status borrado";
    }
	if ($submit)
	{
        header("location:index.php?_module=$_module&_op=saludo&campana_id=$campana_id");
	}
    $table_status .= "<center><table><thead><tr><td>Nombre</td><td>Acción</td></tr></thead>\n";   
    //los de todas
    $sql = "SELECT status_id, nombre FROM crm_campanas_llamadas_status WHERE campana_id='0' ORDER BY orden";
    $result = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));
    while (list($status_id, $nombre) = $db->sql_fetchrow($result))
    {
        $table_status .= "<tr class=\"row".(($j++%2)+1)."\"><td>$nombre</td><td>&nbsp;</td></tr>\n";
    }
    //los de esta
	$sql = "SELECT status_id, nombre FROM crm_campanas_llamadas_status WHERE campana_id='$campana_id' ORDER BY orden";
	$result = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));
 	while (list($status_id, $nombre) = $db->sql_fetchrow($result))
    {
        $table_status .= "<tr class=\"row".(($j++%2)+1)."\"><td>$nombre</td><td align=center><a href=\"javascript:del('$status_id','$nombre');\"><img src=\"../img/del.gif\" onmouseover=\"return escape('Borrar')\"  border=0><a></td></tr>\n";
    }
    $table_status .= "</table></center>\n";
}

?>