<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $encuesta_id, $pregunta, $pregunta_id, $submit, $posicion;

if (!$pregunta_id) //nuevo
{
  header("location:index.php?_module=$_module&_op=preguntas&encuesta_id=$encuesta_id'");
}
$msg = "Editando pregunta: $pregunta";
    
    //obtener a que encuesta regresar
$sql = "SELECT encuesta_id FROM crm_encuestas_preguntas WHERE pregunta_id='$pregunta_id' LIMIT 1";
$result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
list($encuesta_id) = htmlize($db->sql_fetchrow($result));

if ($submit && $pregunta_id && ($posicion!=$pregunta_id)) //guardar
{

  //buscar a donde queremos ponerla
  $sql = "SELECT orden FROM crm_encuestas_preguntas WHERE pregunta_id='$posicion'";
  $r = $db->sql_query($sql) or die("Error al buscar posicion nueva");
  list($orden_nuevo) = $db->sql_fetchrow($r);
  $sql = "SELECT orden FROM crm_encuestas_preguntas WHERE pregunta_id='$pregunta_id'";
  $r = $db->sql_query($sql) or die("Error al buscar posicion actual");
  list($orden_actual)  = $db->sql_fetchrow($r);
  
  //ver si es antes del actual o despues, antes es lo más normal
  if ($orden_nuevo < $orden_actual)
  {
    //subir la pregunta actual
    //el orden de pregunta_id es mayor a los ordenes anteriores
    $sql = "select pregunta_id, orden, pregunta FROM crm_encuestas_preguntas where encuesta_id='$encuesta_id' AND orden<'$orden_actual' AND orden>='$orden_nuevo' ORDER BY orden DESC";
    $r = $db->sql_query($sql) or die("Error al buscar preguntas".$sql);
    while (list($pregunta_id_, $orden_, $p) = $db->sql_fetchrow($r))
    {
      //tenemos la lista de los ordees anteriores, ir switcheando de uno en uno
      //hacer el switch
      $sql = "UPDATE crm_encuestas_preguntas SET orden='$orden_actual' WHERE pregunta_id='$pregunta_id_'";
      $db->sql_query($sql) or die($sql);
      $orden_actual = $orden_;
    }
    $sql = "UPDATE crm_encuestas_preguntas SET orden='$orden_nuevo' WHERE pregunta_id='$pregunta_id'";
    $db->sql_query($sql) or die($sql);
  }
  else
  {
    //bajar la pregunta actual
    //el orden de pregunta_id es mayor a los ordenes anteriores
    $sql = "select pregunta_id, orden, pregunta FROM crm_encuestas_preguntas where encuesta_id='$encuesta_id' AND orden>'$orden_actual' AND orden<='$orden_nuevo' ORDER BY orden ASC";
    $r = $db->sql_query($sql) or die("Error al buscar preguntas".$sql);
    while (list($pregunta_id_, $orden_, $p) = $db->sql_fetchrow($r))
    {
      //tenemos la lista de los ordees anteriores, ir switcheando de uno en uno
      //hacer el switch
      $sql = "UPDATE crm_encuestas_preguntas SET orden='$orden_actual' WHERE pregunta_id='$pregunta_id_'";
      $db->sql_query($sql) or die($sql);
      $orden_actual = $orden_;
    }
    $sql = "UPDATE crm_encuestas_preguntas SET orden='$orden_nuevo' WHERE pregunta_id='$pregunta_id'";
    $db->sql_query($sql) or die($sql);
  }
  header("location:index.php?_module=$_module&_op=preguntas&encuesta_id=$encuesta_id");
}


//aquí vamos a poner las preguntas
$sql = "SELECT pregunta_id, pregunta, orden FROM crm_encuestas_preguntas WHERE encuesta_id='$encuesta_id' ORDER BY orden"; //las que son del tipo secciones son padres
$result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
$select_posicion .= "<select name=\"posicion\">\n";


while (list($posicion, $nombre) = htmlize($db->sql_fetchrow($result)))
{
  if (strlen($nombre) > 50) $nombre = substr($nombre, 0, 50);
  
  if ($posicion == $pregunta_id) $select_posicion .= "<option value=\"$posicion\" SELECTED>Selecciona una nueva posición</option>\n";
  else $select_posicion .= "<option value=\"$posicion\">$nombre</option>\n";
}
$select_posicion .= "</select>\n";



?> 
