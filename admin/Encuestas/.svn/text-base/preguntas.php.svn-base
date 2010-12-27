<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $encuesta_id, $submit, $del, $padre_id;

if (!$encuesta_id) //error, regresar
  header("location:index.php?_module=$_module");
  
if ($submit) //meter algunos en una sección
{
  
  //buscar todas las preguntas para checar chbox
  $sql = "SELECT pregunta_id FROM crm_encuestas_preguntas WHERE encuesta_id='$encuesta_id' ORDER BY orden";
  $result = $db->sql_query($sql) or die("Error al mostrar encuestas".print_r($db->sql_error()));
  if ($db->sql_numrows($result) > 0)
  {
    while (list($pregunta_id) = htmlize($db->sql_fetchrow($result)))
    {
      $chbx = "chbx_$pregunta_id"; //el nombre del check box
      global $$chbx;
      if (isset($$chbx) && $pregunta_id!=$padre_id)
      {
        $sql = "UPDATE crm_encuestas_preguntas SET padre_id='$padre_id' WHERE pregunta_id='$pregunta_id'";
        $db->sql_query($sql) or die("Error al modificar encuestas".print_r($db->sql_error()));
      }
    }
  }

}


if ($del)
{
  $sql = "DELETE FROM crm_encuestas_preguntas_tipo_1 WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
  $sql = "DELETE FROM crm_encuestas_preguntas_tipo_2 WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
  $sql = "DELETE FROM crm_encuestas_preguntas_tipo_3 WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
  $sql = "DELETE FROM crm_encuestas_preguntas_tipo_4 WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
  $sql = "DELETE FROM crm_encuestas_preguntas_tipo_5 WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
  $sql = "DELETE FROM crm_encuestas_preguntas_tipo_6 WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
  $sql = "DELETE FROM crm_encuestas_preguntas_tipo_7 WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
  $sql = "DELETE FROM crm_encuestas_preguntas_tipo_8 WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
  $sql = "DELETE FROM crm_encuestas_preguntas_tipo_9 WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
  $sql = "DELETE FROM crm_encuestas_preguntas_tipo_101 WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
  $sql = "DELETE FROM crm_encuestas_preguntas_tipo_102 WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
  $sql = "DELETE FROM crm_encuestas_preguntas_tipo_103 WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
  $sql = "DELETE FROM crm_encuestas_preguntas_tipo_104 WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
  $sql = "DELETE FROM crm_encuestas_preguntas WHERE pregunta_id='$del' LIMIT 1";
  $result = $db->sql_query($sql) or die("Error al borrar");
}

function preguntas_recursivas($padre_id, $indent_level = 0)
{
  global $db, $encuesta_id, $_module;
  $sql = "SELECT pregunta_id, pregunta, t.tipo_id, t.nombre, observacion, padre_id FROM crm_encuestas_preguntas AS p, crm_encuestas_preguntas_tipos AS t WHERE p.tipo_id=t.tipo_id AND encuesta_id='$encuesta_id' AND padre_id='$padre_id' ORDER BY orden";
  $indent = str_repeat("&nbsp;&nbsp;",$indent_level); 
  $result = $db->sql_query($sql) or die("Error al mostrar encuestas".print_r($db->sql_error()));
  if ($db->sql_numrows($result) > 0)
  {
    
    while (list($pregunta_id, $pregunta, $tipo_id, $tipo, $observacion, $padre_id) = htmlize($db->sql_fetchrow($result)))
    {
      //buscar el nombre de la sección
      $sql = "SELECT pregunta FROM crm_encuestas_preguntas WHERE pregunta_id='$padre_id'";
      $result2 = $db->sql_query($sql) or die("Error al mostrar encuestas".print_r($db->sql_error()));
      list($padre) = $db->sql_fetchrow($result2);
      $padre_len = 25;
      if (strlen($padre) > $padre_len)
        $padre = substr($padre, 0, $padre_len);
      if ($observacion) $obs_si = "<img src=\"../img/ok.gif\"  onmouseover=\"return escape('Sí cuenta con observaciones');\" onmouseout=\"return escape();\"/>";
      else $obs_si = "";
      
      $tabla_preguntas .= "<tr class=\"row".(++$row_class%2?"1":"2")."\"><td>".($tipo_id==101?"<b>":"")."$indent$pregunta".($tipo_id==101?"</b>":"")."</td>"
                          ."<td>$padre</td>"
                          ."<td>$tipo</td>"
                          ."<td>$obs_si</td>"
                          ."<td><a href=\"index.php?_module=$_module&_op=pregunta_edit&pregunta_id=$pregunta_id\" onmouseover=\"return escape('Editar');\" onmouseout=\"return escape();\"><img src=\"../img/edit.gif\" border=0></a>"
                          ."</td><td> <a href=\"index.php?_module=$_module&_op=pregunta_tipo_$tipo_id&pregunta_id=$pregunta_id\" onmouseover=\"return escape('Configurar valores y opciones');\" onmouseout=\"return escape();\"><img src=\"../img/list.png\" border=0></a>"
                          ."</td><td> <a href=\"javascript: void(0);\" onclick=\"return delPregunta('$pregunta_id', '$pregunta');\" onmouseover=\"return escape('Borrar');\" onmouseout=\"return escape();\"><img src=\"../img/del.gif\" border=0></a>"
                          ."</td><td><input type=\"checkbox\" name=\"chbx_$pregunta_id\"></td></tr>\n";
      if ($tipo_id == 101)
        $tabla_preguntas .= preguntas_recursivas($pregunta_id, $indent_level + 1);
    }
    return $tabla_preguntas;
  }
  else
    return "";

}

$tabla = preguntas_recursivas(0);
if ($tabla == "")
{
  $tabla_preguntas = "<center>Esta encuesta aún no tiene preguntas.<br><a href=\"index.php?_module=$_module&_op=pregunta_edit&encuesta_id=$encuesta_id\">Click aquí para agregar una</a></center><br>";
}
else
{
  $tabla_preguntas = "<table style=\"width:100%;\">\n";
  $tabla_preguntas .= "<thead><tr><td  style=\"width:70%;\">Nombre</td><td style=\"width:50px;\">Sección padre</td><td style=\"width:50px;\">Tipo</td><td onmouseover=\"return escape('Cuenta con observaciones');\" onmouseout=\"return escape();\"  style=\"width:12px;\">Obs</td><td  style=\"width:30px;\" colspan=\"4\">Acción</td></tr></thead>\n";
  $tabla_preguntas .= $tabla;
  $tabla_preguntas .= "</table>\n";
}
global $_admin_menu2;//<img src=\"../img/new.gif\" border=0>

$_admin_menu2 .= "<table><tr><td></td><td><a href=\"index.php?_module=$_module&_op=pregunta_edit&encuesta_id=$encuesta_id\" acceskey=\"n\">Nueva</a></td></tr></table>";




//aquí vamos a poner los padres
$sql = "SELECT pregunta_id, pregunta FROM crm_encuestas_preguntas WHERE tipo_id='101' AND encuesta_id='$encuesta_id' ORDER BY orden"; //las que son del tipo secciones son padres
$result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
$select_padre .= "<select name=\"padre_id\">\n";

// $select_padre .= "<option".($padre_id?"":" SELECTED").">Seleccione una sección padre</option>\n";
$select_padre .= "<option".($padre_id?"":" SELECTED")." value=\"0\">Ninguna sección padre</option>\n";

while (list($padre_id2, $nombre) = htmlize($db->sql_fetchrow($result)))
{
  if ($padre_id == $padre_id2) $selected = " SELECTED";
  else $selected = "";
  $select_padre .= "<option value=\"$padre_id2\"$selected>$nombre</option>\n";
}
$select_padre .= "</select>\n";

?> 
