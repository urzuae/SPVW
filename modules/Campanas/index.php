<?
  if (!defined('_IN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $uid, $how_many, $from, $orderby, $del;
$window_opc = "'llamada','location=no,resizable=yes,scrollbars=yes,navigation=no,titlebar=no,directories=no,width=800,height=750,left=0,top=0,alwaysraised=yes'";
$how_many = 25;
if ($from < 1 || !$from) $from = 0;
if (!$orderby) $orderby = "nombre";

$result = $db->sql_query("SELECT gid FROM users WHERE uid='$uid' LIMIT 1") or die("Error en grupo ".print_r($db->sql_error()));
list($gid) = $db->sql_fetchrow($result);
if ($gid != 1) $where_gid = " AND gid='$gid'";
$sql = "SELECT c.campana_id, nombre, fecha_ini, fecha_fin FROM crm_campanas AS c, crm_campanas_groups  AS g WHERE c.campana_id=g.campana_id $where_gid ORDER BY $orderby LIMIT $from, $how_many";
$result = $db->sql_query($sql) or die("Error al consultar campañas ".print_r($db->sql_error()));

$tabla_campanas .= "<table border=\"0\" style=\"width:360px;\">\n";
$tabla_campanas .= "<thead><tr>"
                    ."<td ><a href=\"index.php?_module=$_module&_op=$_op&orderby=nombre\" style=\"color:#ffffff\">Nombre</a></td>"
//                     ."<td><a href=\"index.php?_module=$_module&_op=$_op&orderby=fecha_ini\" style=\"color:#ffffff\">Inicio</a></td>"
//                     ."<td><a href=\"index.php?_module=$_module&_op=$_op&orderby=fecha_fin\" style=\"color:#ffffff\">Fin</a></td>"
                     ."<td>Prospectos</td>"
                     ."<td colspan=\"3\" width=\"32px\">Acción</td>"
                    ."</tr></thead>\n";
while (list($campana_id, $name, $fecha_ini, $fecha_fin) =
         htmlize($db->sql_fetchrow($result)))
{
	//a cada una calcularle sus contactos
	$sql = "SELECT (id) FROM crm_campanas_llamadas AS l, crm_contactos AS c WHERE c.contacto_id=l.contacto_id AND c.uid='$uid' AND campana_id='$campana_id'";
	$r2 = $db->sql_query($sql) or die($sql);
	$cuantos = $db->sql_numrows($r2);
	$total += $cuantos;
    $tabla_campanas .= "<tr  class=\"row".(($c++%2)+1)."\">"
                       ."<td  style=\"cursor:pointer;\" onclick=\"window.open('index.php?_module=$_module&_op=actividades&campana_id=$campana_id','_self');\">$name</td>"
//                        ."<td>$fecha_ini</td><td>$fecha_fin</td>"
            ."<td>$cuantos</td>"
			."<td align=\"center\"><a href=\"#\" onclick=\"window.open('index.php?_module=$_module&_op=llamada&campana_id=$campana_id',$window_opc);\"><img src=\"img/phone.gif\" border=></a></td>"
      ."<td align=\"center\"><a href=\"#\" onclick=\"window.open('index.php?_module=$_module&_op=buscar&campana_id=$campana_id','_self');\"><img src=\"img/search.gif\" border=></a></td>"
      
      ."<td align=\"center\"><a href=\"#\" onclick=\"window.open('index.php?_module=$_module&_op=actividades&campana_id=$campana_id','_self');\"><img src=\"img/circle.gif\" border=></a></td>"
//                        ."<td align=center><a href=\"index.php?_module=$_module&_op=campana&campana_id=$campana_id\"><img src=\"img/list.png\" border=></a></td>"
                        ."</tr>\n";
}
$tabla_campanas .= "<tr class=\"row".(($c++%2)+1)."\"><td  align=\"right\"><b>Total</b></td><td><b>$total</b></td><td></td></tr>\n";
$tabla_campanas .= "</table>\n";


    $result = $db->sql_query("SELECT c.campana_id FROM crm_campanas AS c, crm_campanas_groups  AS g WHERE c.campana_id=g.campana_id $where_gid") or die("Error al cargar campañas");
    $num_news = $db->sql_numrows($result);
    if ($num_news > $how_many)
    {
        if ($from > 0) 
            $paginacion_campanas .= "<a href=\"index.php?_module=$_module&orderby=$orderby&from=".($from - $how_many)."\">&lt;</a>&nbsp;";
        for ($i = 0; $i < $num_news; $i += $how_many)
        {
            $j++;
            if (!($i >= $from && $i <= ($from + $how_many - 1)))
            {
                $link1 = "<a href=\"index.php?_module=$_module&orderby=$orderby&from=".($i)."\">";
                $link2 = "</a>";
            }
            else $link1 = $link2 = "";
            $paginacion_campanas .= "&nbsp;$link1$j$link2";
        }
        if (($from + $how_many) < $num_news) 
            $paginacion_campanas .= "&nbsp;<a href=\"index.php?_module=$_module&orderby=$orderby&from=".($from + $how_many)."\">&gt;</a>";
    }
?>