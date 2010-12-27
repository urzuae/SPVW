<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit_form, $new_target, $del_target,
        $nombre, $objetivos, $objetivos_especificos, $target, $descripcion, $concepto, $comentarios, $fecha_ini, $fecha_fin, $lugar, $saludo, $campana_id, $groups, $presupuesto, $beneficios, $next_campana_id;
$presupuesto = remove_money_format2($presupuesto);

$fecha_ini = date_reverse($fecha_ini);
$fecha_fin = date_reverse($fecha_fin);

if ($del_target)
{
  $sql = "DELETE FROM crm_campanas_targets WHERE target_id='$del_target'";
  $db->sql_query($sql) or die("Error al borrar target".print_r($db->sql_error()));
}
if (!$campana_id)
{
	//crear
	if ($submit_form || $new_target)
	{
		//crear campaña
		$sql = "INSERT INTO crm_campanas (nombre, objetivos, objetivos_especificos, descripcion, concepto, comentarios, fecha_ini, fecha_fin, lugar, presupuesto, beneficios)"
			."VALUES('$nombre', '$objetivos', '$objetivos_especificos', '$descripcion', '$concepto', '$comentarios', '$fecha_ini', '$fecha_fin', '$lugar', '$presupuesto', '$beneficios')";
      
		$db->sql_query($sql) or die("Error al crear campaña".print_r($db->sql_error()));
		$campana_id = $db->sql_nextid();
    if (is_array($groups))
		  foreach ($groups as $group)
		  {
			  $sql = "INSERT INTO crm_campanas_groups (campana_id, gid)VALUES('$campana_id','$group')";
			  $db->sql_query($sql) or die("Error al agregar grupos".print_r($db->sql_error()));
		  }
// 		header("location:index.php?_module=$_module&_op=iniciativas_comunicacion&campana_id=$campana_id");
	}
}
else
{
	if ($submit_form || $new_target)
	{
		$sql = "UPDATE crm_campanas SET nombre='$nombre', objetivos='$objetivos', objetivos_especificos='$objetivos_especificos', descripcion='$descripcion', concepto='$concepto', comentarios='$comentarios', fecha_ini='$fecha_ini', fecha_fin='$fecha_fin', lugar='$lugar', presupuesto='$presupuesto', beneficios='$beneficios', next_campana_id='$next_campana_id' WHERE campana_id='$campana_id'";
		$db->sql_query($sql) or die("Error al guardar campaña".print_r($db->sql_error()));
		//vaciar grupos
		$db->sql_query("DELETE FROM crm_campanas_groups WHERE campana_id='$campana_id'") or die("Error al borrar");
		if (is_array($groups))
      foreach ($groups as $group)
		  {
			  $sql = "INSERT INTO crm_campanas_groups (campana_id, gid)VALUES('$campana_id','$group')";
			  $db->sql_query($sql) or die("Error al agregar grupos".print_r($db->sql_error()));
		  }
//  		header("location:index.php?_module=$_module&_op=iniciativas_comunicacion&campana_id=$campana_id");
	}
	$sql = "SELECT nombre, objetivos, objetivos_especificos, descripcion, concepto, comentarios, fecha_ini, fecha_fin, lugar, saludo, presupuesto, beneficios, next_campana_id FROM crm_campanas WHERE campana_id='$campana_id'";
	$result = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));

	list($nombre, $objetivos, $objetivos_especificos, $descripcion, $concepto, $comentarios, $fecha_ini, $fecha_fin, $lugar, $saludo, $presupuesto, $beneficios, $next_campana_id) = $db->sql_fetchrow($result);

}

if ($new_target) 
 header("location: index.php?_module=$_module&_op=target&campana_id=$campana_id");

//select de grupos
$array_grupos = array();
$sql = "SELECT gid FROM crm_campanas_groups WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error en groups ".print_r($db->sql_error()));
while (list($group_id) = htmlize($db->sql_fetchrow($result)))
	array_push($array_grupos, $group_id);
$select_grupos = "<select id=\"groups\" name=\"groups[]\" multiple>\n";
$sql = "SELECT gid, name FROM groups WHERE 1 ORDER BY name";
$result = $db->sql_query($sql) or die("Error en groups ".print_r($db->sql_error()));
while (list($group_id, $name) = htmlize($db->sql_fetchrow($result)))
{ 
	if (in_array($group_id, $array_grupos)) 
  {
    $selected = " SELECTED";

  }
	else $selected = "";
	$select_grupos .= "<option value=\"$group_id\"$selected>$name</option>\n";
}

$select_grupos .= "</select> (Mayúsculas para seleccionar varios)";
$presupuesto = money_format2("%d", $presupuesto);
if ($campana_id)
{
  $sql = "SELECT SUM(cantidad) FROM crm_gastos WHERE campana_id='$campana_id'";
  $result = $db->sql_query($sql) or die("Error en gastos");
  list($costo_real) = $db->sql_fetchrow($result);
  $costo_real = money_format2("%d", $costo_real);
  $sql = "SELECT SUM(estimado) FROM crm_gastos WHERE campana_id='$campana_id'";
  $result = $db->sql_query($sql) or die("Error en gastos");
  list($costo_estimado) = $db->sql_fetchrow($result);
  $costo_estimado = money_format2("%d", $costo_estimado);
  //lista de los target de la campaña
  $target_list = "<input type=\"hidden\" name='del_target' id='del_target' value=''>";
  $target_list .= "<table style=\"width:100%\" border=1 cellspacing=0>";
  $target_list .= "<thead><td style=\"width:20%;\">Target</td><td colspan=2 style=\"width:80%;\">Valor</td></thead>";
  $sql = "SELECT target_id, target, valor FROM crm_campanas_targets WHERE campana_id='$campana_id' ORDER BY target_id";
  $result = $db->sql_query($sql) or die("Error en gastos");
  while (list($target_id, $target, $valor) = $db->sql_fetchrow($result))
  {
    $target_list .= "<tr>";
    $target_list .= "<td>$target</td><td>$valor</td><td><a href=\"javascript: void(0);\" onclick=\"document.getElementById('del_target').value=$target_id;if (validate(document.forms[0])) return document.forms[0].submit()\">"
                      ."<img border=\"0\" src=\"../img/del.gif\" onmouseover=\"return escape('Borrar target: $target')\" onmouseout=\"return escape();\"></a></td>";
    $target_list .= "</tr>";
  }
  $target_list .= "<tr><td colspan=\"3\" align=\"center\"><input type=\"hidden\" name='new_target' id='new_target' value=''>"
                  ."<a href=\"#\" onclick=\"document.getElementById('new_target').value=1;if (validate(document.forms[0])) document.forms[0].submit()\">Agregar target</a>"
                  .($campana_id?"&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php?_module=$_module&_op=targets_default&campana_id=$campana_id\" onclick=\"\">Agregar varios targets</a>":"Guarde la campaña primero para agregar varios targets")
                  ."</td></tr></table>";
  //ver cuantos archivos tiene adjuntos
  $dir = "$_module/files/$campana_id";
  if (!file_exists($dir)) mkdir("$dir");
  $dir_handle = @opendir("$dir") or die("No se puede leer el directorio $dir");
  while ($file = readdir($dir_handle))
  { 
    if ((strpos($file, ".") === 0)) //si enkontramos archivo que empieze por . no mostrar
           continue;
    $files_num++;
  }
  
  //la parte que encadena a otra campana
  $select_campanas = "<select name=\"next_campana_id\">";
  $select_campanas .= "<option>Ninguna</option>";

  $sql = "SELECT campana_id, nombre FROM crm_campanas WHERE 1 ORDER BY nombre";
  $result = $db->sql_query($sql) or die("Error en siguiente campana ".print_r($db->sql_error()));
  while (list($campana_id_, $nombre_) = htmlize($db->sql_fetchrow($result)))
  {
    //checar si es de este grupo
    $sql = "SELECT gid FROM crm_campanas_groups WHERE campana_id='$campana_id_'";
    $result2 = $db->sql_query($sql) or die("Error en siguiente campana ".print_r($db->sql_error()));
    $in_array_gid = false;
    while (list($gid_) = ($db->sql_fetchrow($result2)))
    {
      if (in_array($gid_, $array_grupos))
        $in_array_gid = true;
    }
    if (!$in_array_gid) continue;
    
    if ($campana_id == $campana_id_) continue; //no se puede ciclar sobre la misma
    if ($campana_id_ == $next_campana_id) $selected = " SELECTED";
    else $selected = "";
    $select_campanas .= "<option value=\"$campana_id_\"$selected>$nombre_</option>\n";
  }
 
  $select_campanas .= "</select>";
  $semi_tabla_ciclo = "<tr class=\"row1\"><td style=\"text-align: right;\">Ciclo siguiente al finalizar</td><td>$select_campanas</td></tr>";
  
  if ($files_num) $files_num = "($files_num)";
  global $_admin_menu2;
  $_admin_menu2 .= "<table>"
                  ."<tr><td></td><td><a href=\"index.php?_module=$_module&campana_id=$campana_id&_op=iniciativas_comunicacion\">Iniciativas de comunicación</a></td></tr>"
                  ."<tr><td></td><td><a href=\"index.php?_module=$_module&campana_id=$campana_id&_op=aprendizaje\">Aspectos de aprendizaje</a></td></tr>"
                  ."<tr><td></td><td><a href=\"index.php?_module=$_module&campana_id=$campana_id&_op=promocion\">Promoción de ventas</a></td></tr>"
                  ."<tr><td></td><td><a href=\"index.php?_module=$_module&campana_id=$campana_id&_op=print\" target=\"print\">Plan general de acciones</a></td></tr>"
                  ."<tr><td></td><td><a href=\"index.php?_module=$_module&campana_id=$campana_id&_op=status\">Call center</a></td></tr>"
                  ."<tr><td></td><td><a href=\"index.php?_module=$_module&campana_id=$campana_id&_op=email_editor\">Email</a></td></tr>"
                  ."<tr><td></td><td><a href=\"index.php?_module=$_module&campana_id=$campana_id&_op=carta_editor\">Carta</a></td></tr>"
                  ."<tr><td></td><td><a href=\"index.php?_module=$_module&campana_id=$campana_id&_op=files\">Archivos Adjuntos $files_num</a></td></tr>"
                  ."</table>";
}
else
{
  $costo_real = money_format2("%d", 0);
  $costo_estimado = money_format2("%d", 0);
  $target_list .= "<center><input type=\"hidden\" name='new_target' id='new_target' value=''>"
                  ."<a href=\"javascript: void(0);\" onclick=\"document.getElementById('new_target').value=1;if (validate(document.forms[0])) return document.forms[0].submit()\">Agregar Target</a>"
                  ."</center>";
}
$fecha_ini = date_reverse($fecha_ini);
$fecha_fin = date_reverse($fecha_fin);

?>