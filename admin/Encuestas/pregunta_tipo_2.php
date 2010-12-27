<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $pregunta_id, $valor, $submit;

if (!$pregunta_id) header("index.php?_module=$_module");

//leer valores
//de que encuesta
$sql = "SELECT encuesta_id, pregunta FROM crm_encuestas_preguntas WHERE pregunta_id='$pregunta_id'"; //checar si existe
$result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
list($encuesta_id, $pregunta) = htmlize($db->sql_fetchrow($result));
//checar valor y ver si ya existe
$sql = "SELECT opcion_id, opcion, valor FROM crm_encuestas_preguntas_tipo_2 WHERE pregunta_id='$pregunta_id' ORDER BY opcion_id"; //chekar datos
$result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));


if ($db->sql_numrows($result) > 0) //ya habia algo en la tabla relacionado con esta pregunta
{
    //ahora las opciones que ya estan en la DB
    $tabla_opciones = "<table style=\"width:100%;\">\n";
    $tabla_opciones .= "<thead><tr><td  style=\"width:75%;\">Opcion</td><td style=\"width:1%;\">Valor</td><td style=\"width:1%;\">Acción</td></tr></thead>\n";

    while (list($opcion_id, $opcion, $valor) = htmlize($db->sql_fetchrow($result)))
    {
      $tabla_opciones .= "<tr class=\"row".(++$row_class%2?"1":"2")."\"><td>$opcion</td><td>$valor</td>"
                          ."<td><a href=\"index.php?_module=$_module&_op=pregunta_tipo_2_edit&opcion_id=$opcion_id\" onmouseover=\"return escape('Editar');\" onmouseout=\"return escape();\"><img src=\"../img/edit.gif\" border=0></a></td></tr>\n";
    }
    $tabla_opciones .= "</table>\n";
}
else //nuevo
{
    $tabla_opciones .= "<center>Todavía no es han dado de alta opciones para esta pregunta.<br><a href=\"index.php?_module=$_module&_op=pregunta_tipo_2_edit&pregunta_id=$pregunta_id\">Agregar una opción</a></center>";
}

global $_admin_menu2;//<img src=\"../img/new.gif\" border=0>

$_admin_menu2 .= "<table><tr><td></td><td><a href=\"index.php?_module=$_module&_op=pregunta_tipo_2_edit&pregunta_id=$pregunta_id\">Nueva</a></td></tr></table>";


?>
