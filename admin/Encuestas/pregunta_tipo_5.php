<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $pregunta_id, $valor, $submit, $groups;

if (!$pregunta_id) header("index.php?_module=$_module");
//leer valores
//de que encuesta
$sql = "SELECT encuesta_id, pregunta FROM crm_encuestas_preguntas WHERE pregunta_id='$pregunta_id'"; //checar si existe o si no insertar
$result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
list($encuesta_id, $pregunta) = htmlize($db->sql_fetchrow($result));
//checar valor y ver si ya existe
$sql = "SELECT valor FROM crm_encuestas_preguntas_tipo_1 WHERE pregunta_id='$pregunta_id'"; //checar si existe o si no insertar
$result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));

if ($submit)
{
  
  $db->sql_query("DELETE FROM crm_encuestas_preguntas_tipo_5 WHERE pregunta_id='$pregunta_id'") or die("Error al borrar");
  if (is_array($groups))
  {
      foreach ($groups as $group)
      {
        $sql = "INSERT INTO crm_encuestas_preguntas_tipo_5 (pregunta_id, valor)VALUES('$pregunta_id','$group')";
        $db->sql_query($sql) or die("Error al agregar grupos".print_r($db->sql_error()));
      }
  }
  header("location:index.php?_module=$_module&_op=preguntas&encuesta_id=$encuesta_id");
}

// if ($db->sql_numrows($result) > 0) //ya habia algo en la tabla relacionado con esta pregunta
// {
//     if ($submit && isset($valor)) //modificar
//     {
//         $sql = "UPDATE crm_encuestas_preguntas_tipo_1 SET valor='$valor' WHERE pregunta_id='$pregunta_id'";
//         $db->sql_query($sql) or die("Error al insertar. ".print_r($db->sql_error()));
//         header("location:index.php?_module=$_module&_op=preguntas&encuesta_id=$encuesta_id");
//     }
//     else list($valor) = htmlize($db->sql_fetchrow($result));
// }
// else //nuevo
// {
//     if ($submit && isset($valor)) //guardar
//     {
//         $sql = "INSERT INTO crm_encuestas_preguntas_tipo_1 (pregunta_id, valor)VALUES('$pregunta_id', '$valor')";
//         $db->sql_query($sql) or die("Error al actualizar. ".print_r($db->sql_error()));
//         header("location:index.php?_module=$_module&_op=preguntas&encuesta_id=$encuesta_id");
//     }
// }


//select de grupos
$array_grupos = array();
$sql = "SELECT valor FROM crm_encuestas_preguntas_tipo_5 WHERE pregunta_id='$pregunta_id'";

$result = $db->sql_query($sql) or die("Error en groups ".print_r($db->sql_error()));
while (list($group_id) = htmlize($db->sql_fetchrow($result)))
  array_push($array_grupos, $group_id);
$select_grupos = "<select name='groups[]' multiple>\n";
$sql = "SELECT gid, name FROM groups WHERE 1 ORDER BY name";
$result = $db->sql_query($sql) or die("Error en groups ".print_r($db->sql_error()));
while (list($group_id, $name) = htmlize($db->sql_fetchrow($result)))
{
  if (in_array($group_id, $array_grupos)) $selected = " SELECTED";
  else $selected = "";
  $select_grupos .= "<option value=\"$group_id\"$selected>$name</option>\n";
}
$select_grupos .= "</select> (Presionar Control para seleccionar varios)";
?> 
