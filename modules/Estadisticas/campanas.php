<?
  if (!defined('_IN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
    global $db, $campana_id, $gid, $uid, $fecha_ini, $fecha_fin, $origen_id;
global $_admin_menu2, $_admin_menu;
// $_admin_menu = " ";
$result = $db->sql_query("SELECT gid FROM users WHERE uid='$uid' LIMIT 1") or die("Error en grupo ".print_r($db->sql_error()));
list($gid) = $db->sql_fetchrow($result);
if ($gid != 1) $where_gid = " AND gid='$gid'";
$sql = "SELECT nombre, campana_id FROM crm_campanas WHERE 1";
$sql = "SELECT nombre, c.campana_id FROM crm_campanas AS c, crm_campanas_groups  AS g WHERE c.campana_id=g.campana_id $where_gid ORDER BY c.campana_id";
$result = $db->sql_query($sql) or die("Error  ".$sql);
$select_campanas .= "<select name=campana_id>\n";
while (list($nombre, $campana_id2) = $db->sql_fetchrow($result))
{
    $result2 = $db->sql_query("SELECT campana_id FROM crm_campanas_groups where campana_id='$campana_id2'") or die("Error 2");
    if ($db->sql_numrows($result2) > 0)
    {
        $result3 = $db->sql_query("SELECT contacto_id FROM crm_campanas_llamadas where campana_id='$campana_id2'") or die("Error 3");
        if ($db->sql_numrows($result2) < 1) continue; //campaña sin call center
        if ($campana_id2 == $campana_id) $sel = " SELECTED";
        else $sel = "";
        $select_campanas .= "<option value=\"$campana_id2\" $sel>$nombre</option>";
    }
}
$select_campanas .= "</select>";
$lista_gid_no_visibles='';
$array=array();
$res_no_visibles=$db->sql_query("SELECT fuente_id FROM crm_groups_fuentes WHERE gid='".$gid."' ORDER BY fuente_id;");
if($db->sql_numrows($res_no_visibles) > 0)
{
    while(list($fuente_id)=$db->sql_fetchrow($res_no_visibles))
    {
        $array[]=$fuente_id;
    }
    $lista_gid_no_visibles=implode(',',$array);
}
if($lista_gid_no_visibles!='')
    $filtro_gid=" WHERE fuente_id NOT IN (".$lista_gid_no_visibles.") ";

$sql = "SELECT nombre, fuente_id FROM  crm_fuentes  ".$filtro_gid." ORDER BY nombre";

$result = $db->sql_query($sql) or die("Error  ".$sql);
$select_origenes .= "<select name=origen_id>\n";
$select_origenes .= "<option value=\"\">Todas</option>";
while (list($nombre, $origen_id2) = $db->sql_fetchrow($result))
{
        if ($origen_id2 == $origen_id) $sel = " SELECTED";
        else $sel = "";
        $select_origenes .= "<option value=\"$origen_id2\" $sel>$nombre</option>";
}
$select_origenes .= "</select>";

if ($campana_id) 
  $graph = "<img src=\"?_module=$_module&_op=graph&campana_id=$campana_id&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&origen_id=$origen_id\">";
$_html .= "<center>$graph<h1>Selecciona una campaña</h1><form><input type=hidden name=_module value=\"$_module\"><input type=hidden name=_op value=\"$_op\">$select_campanas<input type=submit value=Aceptar></form></center>";

 ?>
