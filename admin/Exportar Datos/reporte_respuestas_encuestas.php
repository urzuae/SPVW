<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $encuesta_id, $descripcion, $nombre, $submit, $_modulesdir, $_csv_delimiter;
$delimiter = "$_csv_delimiter";
if ($encuesta_id)
{
  $file = "$_module/files/respuestas_encuesta$encuesta_id.csv";
  $fp = fopen("$file", 'w');
  $sql = "SELECT pregunta_id, pregunta, tipo_id FROM crm_encuestas_preguntas WHERE encuesta_id='$encuesta_id'  ORDER BY orden";
  $result2 = $db->sql_query($sql) or die("Error al consultar preguntas");
  while(list($pregunta_id, $pregunta, $tipo_id) = $db->sql_fetchrow($result2))
  {
    $preguntas[] = $pregunta;
    $preguntas_id[] = $pregunta_id;
    $tipos_id[] = $tipo_id;

  }

  $columnas = array();
//   $array_respuestas = array();
  $j = 0;
  $sql = "SELECT resultado_id, contacto_id FROM crm_encuestas_resultados WHERE encuesta_id='$encuesta_id'";
  $result = $db->sql_query($sql) or die("Error al consultar preguntas");
  while(list($resultado_id, $contacto_id) = $db->sql_fetchrow($result))
  {
    //poner el nombre como titulo de esta columna
    $sql = "SELECT nombre, apellido_paterno, apellido_materno FROM crm_contactos WHERE contacto_id='$contacto_id'";
    $result_c = $db->sql_query($sql) or die("Error al consultar contactos");
    list($a, $b, $c) = $db->sql_fetchrow($result_c);
    $array_respuestas  = array("$a $b $c");
    $noempty = 0;
    for ($i = 1; $i < count($preguntas)+1; $i++)
    {
        $tipo_id = $tipos_id[$i-1];
        $pregunta_id=$preguntas_id[$i-1];
        switch($tipo_id)
        {
          case "1": //si no
                    $sql = "SELECT respuesta FROM crm_encuestas_respuestas_tipo_1 WHERE resultado_id='$resultado_id' AND pregunta_id='$pregunta_id'";
                    $result2 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                    if ($db->sql_numrows($result2) < 1) //esta vacio
                    {
                      $array_respuestas[$i] = "";
                      break;
                    }
                    else
                    {
                      $noempty++;
                      list($respuesta) = $db->sql_fetchrow($result2);
                      if ($respuesta == 1)
                          $r = "Sí";
                      else
                          $r = "No";
                      $array_respuestas[$i] = "$r";
                      break;
                    }
          case "2": //opcion múltiple
                    $sql = "SELECT respuesta FROM crm_encuestas_respuestas_tipo_2 WHERE resultado_id='$resultado_id' AND pregunta_id='$pregunta_id'";
                    $result2 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                    if ($db->sql_numrows($result2) < 1) //esta vacio
                    {
                      $array_respuestas[$i] = "";
                      break;
                    }
                    else
                    {
                      $noempty++;
                      list($respuesta) = $db->sql_fetchrow($result2);
                      $sql = "SELECT opcion FROM crm_encuestas_preguntas_tipo_2 WHERE opcion_id='$respuesta' LIMIT 1";
                      $result3 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                      list($respuesta) = $db->sql_fetchrow($result3);
                      $array_respuestas[$i] = "$respuesta";
                      break;
                    }
          case "3": //abierta
                    $sql = "SELECT respuesta FROM crm_encuestas_respuestas_tipo_3 WHERE resultado_id='$resultado_id' AND pregunta_id='$pregunta_id'";
                    $result2 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                    if ($db->sql_numrows($result2) < 1) //esta vacio
                    {
                      $array_respuestas[$i] = "";
                      break;
                    }
                    else
                    {
                      $noempty++;
                      list($respuesta) = $db->sql_fetchrow($result2);
                      $array_respuestas[$i] = "$respuesta";
                      break;
                    }
        }
    }
    
    if ($noempty)
    {
      $columnas[$j++] = $array_respuestas;
//       print_r($array_respuestas);echo "\n";
    }
  }
  for ($i = 0; $i < count($preguntas); $i++)
  {
    if ($i) $row = array($preguntas[$i-1]); //a1
    else  $row = array("Pregunta\Nombre");
    for ($j = 0; $j < count($columnas); $j++)
        $row[] = $columnas[$j][$i]; //b1, c1, d1, ...
    fputcsv($fp, $row, $delimiter);
  }

  fclose($fp);
  header("location:$file");
}


?> 
