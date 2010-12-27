<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $del;

if ($del)
{
  $sql = "SELECT pregunta_id FROM crm_encuestas_preguntas WHERE encuesta_id='$del'";
  $result = $db->sql_query($sql) or die("Error al mostrar encuentras");
  while (list($pregunta_id) = $db->sql_fetchrow($result))
  {
    $sql = "DELETE FROM crm_encuestas_preguntas_tipo_1 WHERE pregunta_id='$pregunta_id'";
    $result = $db->sql_query($sql) or die("Error al borrar");
    $sql = "DELETE FROM crm_encuestas_preguntas_tipo_2 WHERE pregunta_id='$pregunta_id'";
    $result = $db->sql_query($sql) or die("Error al borrar");
    $sql = "DELETE FROM crm_encuestas_preguntas_tipo_3 WHERE pregunta_id='$pregunta_id'";
    $result = $db->sql_query($sql) or die("Error al borrar");
    $sql = "DELETE FROM crm_encuestas_preguntas WHERE pregunta_id='$pregunta_id'";
    $result = $db->sql_query($sql) or die("Error al borrar");
  }
  $sql = "DELETE FROM crm_encuestas WHERE encuesta_id='$del'";
  $result = $db->sql_query($sql) or die("Error al borrar");
}
$tabla_encuestas = "<table style=\"width:100%;\">\n";
$tabla_encuestas .= "<thead><tr><td>Nombre</td><td  style=\"width:50%;\">Descripción</td><td>Acción</td></tr></thead>\n";
$sql = "SELECT encuesta_id, nombre, descripcion FROM crm_encuestas WHERE 1 ORDER BY nombre";
$result = $db->sql_query($sql) or die("Error al mostrar encuentras");
while (list($encuesta_id, $nombre, $descripcion) = $db->sql_fetchrow($result))
{
  $tabla_encuestas .= "<tr class=\"row".(++$row_class%2?"1":"2")."\"><td>$nombre</td><td>$descripcion</td>"
                      ."<td>"
                      ."<a href=\"index.php?_module=$_module&_op=edit&encuesta_id=$encuesta_id\" onmouseover=\"return escape('Editar');\" onmouseout=\"return escape();\"><img src=\"../img/edit.gif\" border=0></a>"
                      ." <a href=\"index.php?_module=$_module&_op=reporte&encuesta_id=$encuesta_id\" target=\"reporte\" onclick=\"\" onmouseover=\"return escape('Reporte');\" onmouseout=\"return escape();\"><img src=\"../img/excel.gif\" border=0></a>"
                      .(($encuesta_id>0)?" <a href=\"javascript: void(0);\" onclick=\"return delEncuesta('$encuesta_id', '$nombre');\" onmouseover=\"return escape('Borrar');\" onmouseout=\"return escape();\"><img src=\"../img/del.gif\" border=0></a>":"")
                      ."</td></tr>\n";
}
$tabla_encuestas .= "</table>\n";

global $_admin_menu2;

$_admin_menu2 .= "<table><tr><td></td><td><a href=\"index.php?_module=$_module&_op=edit\">Nueva</a></td></tr></table>";


?> 
