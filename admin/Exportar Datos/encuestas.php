<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db;


$tabla_encuestas = "<table style=\"width:100%;\">\n";
$tabla_encuestas .= "<thead><tr><td>Nombre</td><td  style=\"width:50%;\">Descripción</td><td>Respuestas</td><td>Calificación</td></tr></thead>\n";
$sql = "SELECT encuesta_id, nombre, descripcion FROM crm_encuestas WHERE 1 ORDER BY nombre";
$result = $db->sql_query($sql) or die("Error al mostrar encuentras");
while (list($encuesta_id, $nombre, $descripcion) = $db->sql_fetchrow($result))
{
  $tabla_encuestas .= "<tr class=\"row".(++$row_class%2?"1":"2")."\"><td>$nombre</td><td>$descripcion</td>"
                      ."<td>"
                      ." <a href=\"index.php?_module=$_module&_op=reporte_respuestas_encuestas&encuesta_id=$encuesta_id\" target=\"reporte\" onclick=\"\" onmouseover=\"return escape('Respuestas');\" onmouseout=\"return escape();\"><img src=\"../img/list.png\" border=0></a>"
                      ."</td>"
                      ."<td>"
                      ." <a href=\"index.php?_module=$_module&_op=calificar&encuesta_id=$encuesta_id\" target=\"reporte\" onclick=\"\" onmouseover=\"return escape('Calificación');\" onmouseout=\"return escape();\"><img src=\"../img/list.png\" border=0></a>"
                      ."</td>"
                      ."</tr>\n";
}
$tabla_encuestas .= "</table>\n";

// global $_admin_menu2;
// $_admin_menu2 .= "<table><tr><td></td><td><a href=\"index.php?_module=$_module&_op=edit\">Nueva</a></td></tr></table>";


?> 
