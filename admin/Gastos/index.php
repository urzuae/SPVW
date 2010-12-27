<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $limit, $del_gasto, $del_concepto, $fecha_ini, $fecha_fin, $campana_id, $proveedor_id, $show_all;

if (!$limit) $limit = 50;

$where = " AND 1";
if ($fecha_ini)
{
  $fecha_ini = date_reverse($fecha_ini);
  $where .= " AND fecha>'$fecha_ini'";
}
if ($fecha_fin)
{
  $fecha_fin = date_reverse($fecha_fin);
  $where .= " AND fecha<'$fecha_fin'";
}
if ($campana_id)
  $where .= " AND campana_id='$campana_id'";
if ($proveedor_id)
  $where .= " AND proveedor_id='$proveedor_id'";
  
if($del_gasto)
{
      $db->sql_query("DELETE FROM crm_gastos WHERE gasto_id='$del_gasto' LIMIT 1") or die("Error al borrar.");
}
if($del_concepto)
{
  //agregar recursivo
      $db->sql_query("DELETE FROM crm_gastos WHERE concepto_id='$del_concepto' LIMIT 1") or die("Error al borrar. 1");
      $db->sql_query("DELETE FROM crm_gastos_conceptos WHERE concepto_id='$del_concepto' LIMIT 1") or die("Error al borrar.");
}
//lista de usuarios

function suma_total($concepto_id, $where)
{
  global $db;
  $total = 0;
  $sql = "SELECT cantidad FROM crm_gastos WHERE concepto_id='$concepto_id'$where";
  $result = $db->sql_query($sql) OR die("Error en suma total");
  while (list($cantidad) = $db->sql_fetchrow($result))
  {
    $total += $cantidad;
  }  
  
  $sql = "SELECT concepto_id FROM crm_gastos_conceptos WHERE padre_id='$concepto_id'";
  $result = $db->sql_query($sql) OR die("Error en suma total 2");
  while (list($concepto_id2) = $db->sql_fetchrow($result))
  {
    $total += suma_total($concepto_id2, $where);
  }
  return $total;
}
function suma_total_estimado($concepto_id, $where)
{
  global $db;
  $total = 0;
  $sql = "SELECT estimado FROM crm_gastos WHERE concepto_id='$concepto_id'$where";
  $result = $db->sql_query($sql) OR die("Error en suma total");
  while (list($cantidad) = $db->sql_fetchrow($result))
  {
    $total += $cantidad;
  }  
  
  $sql = "SELECT concepto_id FROM crm_gastos_conceptos WHERE padre_id='$concepto_id'";
  $result = $db->sql_query($sql) OR die("Error en suma total 2");
  while (list($concepto_id2) = $db->sql_fetchrow($result))
  {
    $total += suma_total($concepto_id2, $where);
  }
  return $total;
}

function list_recursive($parent, $level, $prefix, $where)
{
  global $db, $_html, $_module, $showall;
  $tabs = str_repeat("&nbsp;&nbsp;&nbsp;", $level);
  $tabs2 = str_repeat("&nbsp;&nbsp;&nbsp;", $level+1);
  $sql = "SELECT concepto_id, nombre, descripcion FROM crm_gastos_conceptos WHERE padre_id='$parent' ORDER BY nombre";
  $result = $db->sql_query($sql) OR die("Error al consultar db*: ".print_r($db->sql_error()));
  //voy a usar row2 para los que tienen hijos y row1 para los que no
  while (list($concepto_id, $nombre, $descripcion) = htmlize($db->sql_fetchrow($result))) 
  {
      //vamos a hacer la sumatoria de todos los de adentro antes
      $cantidad = suma_total($concepto_id, $where);
      $cantidad_f = money_format2("%n", $cantidad);
      $estimado = suma_total_estimado($concepto_id, $where);
      $estimado_f = money_format2("%n", $estimado);

      if ($cantidad < 0) $cantidad_f = "<span style=\"color:red;\">$cantidad_f</span>";
      if ($parent && !$showall) 
      {
        $toggle_style = "display: none;";
      }
      else 
      {
        $toggle_style = "";
      }

      if (strlen($descripcion) > 35) $descripcion_f = substr($descripcion, 0, 32)."...";
      $_html .=  "<tr style=\"$toggle_style\" id=\"$prefix-$concepto_id\" class=\"row2\">"
                ." <td>$tabs<a href=\"javascript: toggle_list('$prefix-$concepto_id');\"><b>$nombre<b><a></td>"
                ."<td align=\"left\" onmouseover=\"return escape('$descripcion')\"><span style=\"font-size:xx-small;\">$descripcion</span></td>"
                ."<td align=\"right\">$cantidad_f</td>"
                ."<td align=\"right\">$estimado_f</td>"
                ."<td align=\"right\">&nbsp;</td>"
                ."<td align=\"right\">&nbsp;</td>"
                ."<td><a href=\"index.php?_module=$_module&_op=edit_concepto&concepto_id=$concepto_id\"><img src=\"../img/edit.gif\" onmouseover=\"return escape('Editar')\"  border=0></a></td>"
                ."<td><a href=\"index.php?_module=$_module&_op=edit_gasto&parent_id=$concepto_id\"><img src=\"../img/more.gif\" onmouseover=\"return escape('Agregar')\"  border=0></a></td>"
                ."<td><a href=\"#\" onclick=\"del_concepto('$concepto_id','$nombre')\"><img src=\"../img/del.gif\" onmouseover=\"return escape('Borrar')\"  border=0></a></td></tr>\n";
      //ahora los gastos

      $sql2 = "SELECT gasto_id, cantidad, estimado, descripcion, fecha, proveedor_id, campana_id FROM crm_gastos WHERE concepto_id='$concepto_id' $where ORDER BY fecha ASC";
//       echo $sql2."<br>";
      $result2 = $db->sql_query($sql2) OR die("Error en gastos*");
      while (list($gasto_id, $cantidad, $estimado, $descripcion, $fecha, $proveedor_id, $campana_id) = htmlize($db->sql_fetchrow($result2)))
      {
        list($y, $m, $d) = explode("-", $fecha);
        $fecha = "$d-$m-$y";
        $total += $cantidad;
        if ($proveedor_id) list($proveedor) = htmlize($db->sql_fetchrow($db->sql_query("SELECT nombre FROM crm_gastos_proveedores WHERE proveedor_id='$proveedor_id' LIMIT 1")));
        else $proveedor = "";
        if (strlen($proveedor) > 25) $proveedor_f = substr($proveedor, 0, 22)."...";
        else $proveedor_f = $proveedor;
        if ($proveedor_f) $proveedor_f = "<a href=\"index.php?_module=$_module&_op=edit_proveedor&proveedor_id=$proveedor_id\"><span style=\"font-size:xx-small;\">$proveedor_f</span></a>";
        $cantidad_f = money_format2("%n", $cantidad);
        if ($cantidad < 0) $cantidad_f = "<span style=\"color:red;\">$cantidad_f</span>";
        $estimado_f = money_format2("%n", $estimado);
        if ($estimado < 0) $estimado_f = "<span style=\"color:red;\">$estimado_f</span>";
        else if ($estimado == 0) $estimado_f = "";
        $res = $db->sql_query("SELECT nombre FROM crm_campanas WHERE campana_id='$campana_id'");
        list($compania) = $db->sql_fetchrow($res);
        if ($compania)
          if (strlen($compania) > 15)
            $compania_f = "<span style=\"font-size:xx-small;\">".substr($compania, 0, 12)."...</span>";
          else
            $compania_f = "<span style=\"font-size:xx-small;\">$compania</span>";
//         if ($parent) 
        $toggle_style = "display: none;";//no mostrarlo (colapsado)
//         else $toggle_style = "display: ;";
        $_html .=  "<tr style=\"$toggle_style\" id=\"$prefix-$concepto_id+$gasto_id\" class=\"row1\"><td>$tabs2$fecha</td>"
                  ."<td align=\"left\"  onmouseover=\"return escape('$descripcion')\"><span style=\"font-size:xx-small;\">$descripcion</span></td>"
                  ."<td align=\"right\">$cantidad_f</td>"
                  ."<td align=\"right\">$estimado_f</td>"
                  ."<td align=\"left\" onmouseover=\"return escape('$proveedor')\">$proveedor_f</td>"
                  ."<td align=\"left\" onmouseover=\"return escape('$compania')\">$compania_f</td>"
                  ."<td><a href=\"index.php?_module=$_module&_op=edit_gasto&gasto_id=$gasto_id\"><img src=\"../img/edit.gif\" onmouseover=\"return escape('Editar')\"  border=0></a></td>"
                  ."<td><a href=\"index.php?_module=$_module&_op=edit_gasto&parent_id=$concepto_id\"><img src=\"../img/more.gif\" onmouseover=\"return escape('Agregar')\"  border=0></a></td>"
                  ."<td><a href=\"#\" onclick=\"del_gasto('$gasto_id','$nombre')\"><img src=\"../img/del.gif\" onmouseover=\"return escape('Borrar')\"  border=0></a></td></tr>\n";      
      }
      
      $total += list_recursive($concepto_id, $level+1, "$prefix-$concepto_id", $where);

  }
  return $total;
}

setlocale(LC_MONETARY, 'es_MX');
$sql = "SELECT concepto_id, nombre, descripcion FROM crm_gastos_conceptos WHERE padre_id='0' ORDER BY nombre";
$result = $db->sql_query($sql) OR die("Error al consultar db: ".print_r($db->sql_error()));
$_html .= "<table style=\"width:100%;\">\n";
if ($db->sql_numrows($result) > 0)
{
    $_html .= "<thead><tr style=\"font-weight:bold;\"><td width=\"150\">Nombre</td><td>Descripción</td><td width=\"90\">Cantidad Real</td><td width=\"90\">Estimado</td><td width=\"90\">Proveedor</td><td width=\"90\">Campaña</td><td colspan=3>Acción</td></tr></thead><tbody id=\"table_main\">\n";
    list_recursive(0, 0, "tr", $where);
}
$cantidad = suma_total(0, $where);
$cantidad_f = money_format2("%n", $cantidad);
if ($cantidad < 0) $cantidad_f = "<span style=\"color:red;\">$cantidad_f</span>";
$_html .= "<tbody><thead><tr style=\"font-weight:bold;\"><td width=\"150\"> </td><td align=\"right\">Total</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td width=\"90\">$cantidad_f</td><td colspan=3></td></tr></thead>\n";

//cerrar tabla
$_html .= "</table>";

$select_campana = "<select name=campana_id>\n";
$select_campana .= "<option>Todas</option>\n";
$sql = "SELECT campana_id, nombre FROM crm_campanas WHERE 1 ORDER BY nombre";
$result = $db->sql_query($sql) or die("Error en select campana");
while (list($campana_id2, $nombre) = $db->sql_fetchrow($result))
{
  if ($campana_id2 == $campana_id) $s = " SELECTED";
  else $s = "";
  $select_campana .= "<option value=\"$campana_id2\"$s>$nombre</option>\n";
}
$select_campana .= "</select>\n";

$select_proveedor = "<select name=proveedor_id>\n";
$select_proveedor .= "<option>Todos</option>\n";
$sql = "SELECT proveedor_id, nombre FROM crm_gastos_proveedores WHERE 1 ORDER BY nombre";
$result = $db->sql_query($sql) or die("Error en select proveedor");
while (list($proveedor_id2, $nombre) = $db->sql_fetchrow($result))
{
  if ($proveedor_id2 == $proveedor_id) $s = " SELECTED";
  else $s = "";
  $select_proveedor .= "<option value=\"$proveedor_id2\"$s>$nombre</option>\n";
}
$select_proveedor .= "</select>\n";

global $_admin_menu2;//<img src=\"../img/new.gif\" border=0>
$_admin_menu2 .= "<table><tr><td></td><td><a href=\"index.php?_module=$_module&_op=edit_concepto\">Nuevo Concepto</a></td></tr><tr><td></td><td><a href=\"index.php?_module=$_module&_op=edit_proveedor\">Nueva Proveedor</a></td></tr></table>";

$fecha_ini = date_reverse($fecha_ini);
$fecha_fin = date_reverse($fecha_fin);

?> 
