<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $pregunta_id, $opcion, $valor, $submit, $opcion_id;


if ($opcion_id) //ya habia algo en la tabla relacionado con esta opcion
{
    if ($submit && isset($valor) && $opcion) //modificar
    {
        $sql = "UPDATE crm_encuestas_preguntas_tipo_6 SET valor='$valor', opcion='$opcion' WHERE opcion_id='$opcion_id'";
        $db->sql_query($sql) or die("Error al insertar. ".print_r($db->sql_error()));
        header("location:index.php?_module=$_module&_op=pregunta_tipo_6&pregunta_id=$pregunta_id");
    }
    else 
    {
    //ahora las opciones que ya estan en la DB
      $sql = "SELECT opcion_id, opcion, valor, pregunta_id FROM crm_encuestas_preguntas_tipo_6 WHERE opcion_id='$opcion_id'";
      $result = $db->sql_query($sql) or die("Error al consultar opción");
      if ($db->sql_numrows($result) > 0)
        list($opcion_id, $opcion, $valor, $pregunta_id) = htmlize($db->sql_fetchrow($result));
      else
        $opcion_id = $opcion = $valor = "";
    }
}
else //nuevo
{
    if ($submit && isset($valor)  && $opcion) //guardar
    {
        $sql = "INSERT INTO crm_encuestas_preguntas_tipo_6 (pregunta_id, valor, opcion)VALUES('$pregunta_id', '$valor', '$opcion')";
        $db->sql_query($sql) or die("Error al actualizar. ".print_r($db->sql_error()));
        header("location:index.php?_module=$_module&_op=pregunta_tipo_6&pregunta_id=$pregunta_id");
    }
}

      //leer valores
      //de que encuesta
      $sql = "SELECT encuesta_id, pregunta FROM crm_encuestas_preguntas WHERE pregunta_id='$pregunta_id'"; //checar si existe o si no insertar
      $result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
      list($encuesta_id, $pregunta) = htmlize($db->sql_fetchrow($result));

?>
