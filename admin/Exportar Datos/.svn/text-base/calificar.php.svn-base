<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
$_css = $_themedir."/style.css";
$_theme = "";
global $db, $contacto_id, $encuesta_id, $resultado_id, $submit, $_csv_delimiter;
$delimiter = "$_csv_delimiter";

$result = $db->sql_query("SELECT nombre FROM crm_encuestas WHERE encuesta_id='$encuesta_id'") or die("Error1".print_r($db->sql_error()));
if ($db->sql_numrows($result) < 1)
  die("Esa encuesta no existe");
list($nombre_encuesta) = ($db->sql_fetchrow($result));

$file = "$_module/files/respuestas_encuesta$encuesta_id.csv";
$fp = fopen("$file", 'w');

$sql = "SELECT resultado_id, contacto_id, fecha FROM crm_encuestas_resultados WHERE encuesta_id='$encuesta_id'";
$result0 = $db->sql_query($sql) or die("Error al buscar contacto.<br>$sql");
$array_header = array("contacto_id", "Nombre", "Fecha", "Hora");
while(list($resultado_id, $contacto_id, $fecha) = $db->sql_fetchrow($result0))
{
    list($fecha, $hora) = explode(" ", $fecha);
    $fecha = date_reverse($fecha);
    $result = $db->sql_query("SELECT nombre, apellido_paterno, apellido_materno FROM crm_contactos WHERE contacto_id='$contacto_id'") or die("Error2");
    if ($db->sql_numrows($result) < 1)
      die("Ese contacto no existe");
    list($nombre, $apellido_paterno, $apellido_materno) = ($db->sql_fetchrow($result));

    $array_fila = array("$contacto_id", "$nombre $apellido_paterno $apellido_materno", "$fecha", "$hora");

    $valor_total = 0;//aqui se acumula el valor de esta encuesta
    //obtener que preguntas hay
    $sql = "SELECT pregunta_id, pregunta, tipo_id, observacion FROM crm_encuestas_preguntas WHERE encuesta_id='$encuesta_id' ORDER BY orden";
    $result = $db->sql_query($sql) or die("Error3".print_r($db->sql_error()));
    if ($db->sql_numrows($result) < 1)
      die("Encuesta vacia");
    while (list($pregunta_id, $pregunta, $tipo_id, $observacion) = ($db->sql_fetchrow($result)))
    {
      if (!$mas_de_la_primera_vuelta)
      {
        $array_header[] = $pregunta;
      }
      //tenemos la pregunta, buscar su respuesta dependiendo el tipo
      switch ($tipo_id)
      {
        case 1: //si es SI (1) multiplicarlo por el valor
                $sql = "SELECT respuesta FROM crm_encuestas_respuestas_tipo_1 WHERE resultado_id='$resultado_id' AND pregunta_id='$pregunta_id'";// ORDER BY timestamp DESC LIMIT 1 ";
                $result2 = $db->sql_query($sql) or die("Error al abrir tipo 1".print_r($db->sql_error()));
                list($respuesta) = $db->sql_fetchrow($result2);
  //               if ($respuesta)
  //               {
                  $sql = "SELECT valor FROM crm_encuestas_preguntas_tipo_1 WHERE pregunta_id='$pregunta_id' LIMIT 1";
                  $result2 = $db->sql_query($sql) or die("Error al abrir tipo 1".print_r($db->sql_error()));
                  list($valor) = $db->sql_fetchrow($result2);
  
                  $valor_total += $valor;
//                   echo "$tipo_id/$pregunta_id) $valor_total += $valor;<br>";
  //               }
                break;
        case 2: //buscar respuesta y evaluarla
                $sql = "SELECT respuesta FROM crm_encuestas_respuestas_tipo_2 WHERE resultado_id='$resultado_id' AND pregunta_id='$pregunta_id'";// ORDER BY timestamp DESC LIMIT 1";
                $result2 = $db->sql_query($sql) or die("Error al abrir tipo 2<br>$sql<br>".print_r($db->sql_error()));
                list($respuesta) = $db->sql_fetchrow($result2);
                if ($respuesta)
                {
                  $result2 = $db->sql_query($sql) or die("Error al abrir tipo 2".print_r($db->sql_error()));
                  list($valor) = $db->sql_fetchrow($result2);
                  $valor_total += $valor;
//                   echo "$tipo_id/$pregunta_id) $valor_total += $valor;<br>";
                }
        case 3: 
                $sql = "SELECT respuesta FROM crm_encuestas_respuestas_tipo_3 WHERE resultado_id='$resultado_id' AND pregunta_id='$pregunta_id'";// ORDER BY timestamp DESC LIMIT 1";
                $result2 = $db->sql_query($sql) or die("Error al abrir tipo 3".print_r($db->sql_error()));
                list($respuesta) = $db->sql_fetchrow($result2);
                if ($respuesta)
                {
                  $sql = "SELECT valor FROM crm_encuestas_preguntas_tipo_3 WHERE pregunta_id='$pregunta_id' LIMIT 1";
                  $result2 = $db->sql_query($sql) or die("Error al abrir tipo 3".print_r($db->sql_error()));
                  list($valor) = $db->sql_fetchrow($result2);
                  $valor_total += $valor;
//                   echo "$tipo_id/$pregunta_id) $valor_total += $valor;<br>";
                }
      }//switch
      $array_fila[] = $valor;
    }//while preguntas
    $array_fila[] = $valor_total;//al final de las preguntas poner el total
    if (!$mas_de_la_primera_vuelta)
    {
      $array_header[] = "Total";
      $array_columnas[] = $array_header;
      fputcsv($fp, $array_header, $delimiter);
      $mas_de_la_primera_vuelta++;
    }
    fputcsv($fp, $array_fila, $delimiter);
    $array_columnas[] = $array_fila;
}//while contactos
fclose($fp);
header("location:$file");
?>