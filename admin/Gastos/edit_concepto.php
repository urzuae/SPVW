<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

function recursive_padre_select($padre_id, $selected, $level)
{
  global $db;
  if (!$padre_id) $select .= "<option value=\"0\">Ninguno</option>\n";
  $tabs = str_repeat("&nbsp;&nbsp;", $level);
  $sql = "SELECT concepto_id, nombre FROM crm_gastos_conceptos WHERE padre_id='$padre_id' ORDER BY nombre";
  $result = $db->sql_query($sql) or die("Error en padre");
  while (list($concepto_id, $nombre) = $db->sql_fetchrow($result))
  {
    if ($concepto_id == $selected) $s = " SELECTED";
    else $s = "";
    $select .= " <option value=\"$concepto_id\"$s>$tabs$nombre</option>\n";
    $select .= recursive_padre_select($concepto_id, $selected, $level + 1);
  }
  
  return $select;
}
global $db, $concepto_id, $padre_id, $descripcion, $nombre, $submit;
if ($padre_id == "") $padre_id = 0;
if (!$concepto_id) //nuevo
{
    if ($submit) //guardar
    {
        $sql = "INSERT INTO crm_gastos_conceptos (concepto_id, nombre, descripcion, padre_id)VALUES('',  '$nombre', '$descripcion', '$padre_id')";
        $db->sql_query($sql) or die("Error al insertar. ".print_r($db->sql_error()));
        header("location:index.php?_module=$_module");
    }
    $msg = "Concepto nuevo";
    $padre_select = "<select name=\"padre_id\">\n";
    $padre_select .= recursive_padre_select(0, 0, 0);
    $padre_select .= "</select>\n";
}
else
{
    if ($submit) //guardar
    {
        $sql = "UPDATE crm_gastos_conceptos SET nombre='$nombre', descripcion='$descripcion', padre_id='$padre_id' WHERE concepto_id='$concepto_id'";
        $db->sql_query($sql) or die("Error al actualizar. ".print_r($db->sql_error()));

        header("location:index.php?_module=$_module");
    }
    //leer valores
    $sql = "SELECT nombre, descripcion, padre_id FROM crm_gastos_conceptos WHERE concepto_id='$concepto_id'";
    $result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
    list($nombre, $descripcion, $padre_id) = htmlize($db->sql_fetchrow($result));
    $msg = "Editando concepto de: $nombre";
    $padre_select = "<select name=\"padre_id\">\n";
    $padre_select .= recursive_padre_select(0, $padre_id, 1);
    $padre_select .= "</select>\n";
}

?> 
