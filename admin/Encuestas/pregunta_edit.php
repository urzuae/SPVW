<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $encuesta_id, $pregunta, $pregunta_id, $tipo_id, $observacion, $padre_id, $submit, $posicion;
if ($observacion) $observacion = "1";

if (!$pregunta_id) //nuevo
{
    if ($submit && $pregunta && $tipo_id) //guardar
    {
        //obtener el "orden" anterior
        $sql = "SELECT orden FROM crm_encuestas_preguntas WHERE 1 ORDER BY orden DESC LIMIT 1";
        $result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
        list($orden) = htmlize($db->sql_fetchrow($result));
        $sql = "INSERT INTO crm_encuestas_preguntas (pregunta, tipo_id, observacion, encuesta_id, orden, padre_id)VALUES('$pregunta', '$tipo_id', '$observacion', '$encuesta_id', '".($orden+1)."', '$padre_id')";
        $db->sql_query($sql) or die("Error al insertar. ".print_r($db->sql_error()));
        header("location:index.php?_module=$_module&_op=preguntas&encuesta_id=$encuesta_id");
    }
    $msg = "Pregunta nueva";
}
else
{
    //obtener a que encuesta regresar
    $sql = "SELECT encuesta_id FROM crm_encuestas_preguntas WHERE pregunta_id='$pregunta_id' LIMIT 1";
    $result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
    list($encuesta_id) = htmlize($db->sql_fetchrow($result));

    if ($submit && $pregunta && $tipo_id) //guardar
    {
        $sql = "UPDATE crm_encuestas_preguntas SET pregunta='$pregunta', tipo_id='$tipo_id', observacion='$observacion', padre_id='$padre_id' WHERE pregunta_id='$pregunta_id'";
        $db->sql_query($sql) or die("Error al actualizar. ".print_r($db->sql_error()));
        header("location:index.php?_module=$_module&_op=preguntas&encuesta_id=$encuesta_id");
    }
    //leer valores
    $sql = "SELECT pregunta, tipo_id, observacion, padre_id FROM crm_encuestas_preguntas WHERE pregunta_id='$pregunta_id'";
    $result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
    list($pregunta, $tipo_id, $observacion, $padre_id) = htmlize($db->sql_fetchrow($result));
    if ($observacion) $obs_check = " CHECKED";
    $msg = "Editando pregunta: $pregunta";
    
    
    $boton_posición = "<input value=\"Cambiar posición\" onclick=\"location.href='index.php?_module=$_module&_op=pregunta_posicion&pregunta_id=$pregunta_id&encuesta_id=$encuesta_id';\" type=\"button\">";
}

$sql = "SELECT tipo_id, nombre FROM crm_encuestas_preguntas_tipos WHERE 1 ORDER BY nombre";
$result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
$select_tipo .= "<select name=\"tipo_id\">\n";
while (list($tipo_id2, $nombre) = htmlize($db->sql_fetchrow($result)))
{
  if ($tipo_id == $tipo_id2) $selected = " SELECTED";
  else $selected = "";
  $select_tipo .= "<option value=\"$tipo_id2\"$selected>$nombre</option>\n";
}
$select_tipo .= "</select>\n";


//aquí vamos a poner los padres

$sql = "SELECT pregunta_id, pregunta FROM crm_encuestas_preguntas WHERE tipo_id='101' AND encuesta_id='$encuesta_id' ORDER BY orden"; //las que son del tipo secciones son padres
$result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
$select_padre .= "<select name=\"padre_id\">\n";

$select_padre .= "<option".($padre_id?"":" SELECTED").">Ninguna sección padre</option>\n";

while (list($padre_id2, $nombre) = htmlize($db->sql_fetchrow($result)))
{
  if ($padre_id == $padre_id2 || !$pregunta_id) $selected = " SELECTED";
  else $selected = "";
  $select_padre .= "<option value=\"$padre_id2\"$selected>$nombre</option>\n";
}
$select_padre .= "</select>\n";



?> 
