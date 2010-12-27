<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $encuesta_id, $descripcion, $nombre, $submit;

if ($encuesta_id)
{
  $fp = fopen("encuesta$encuesta_id.csv", 'w');
  $sql = "SELECT resultado_id, contacto_id, fecha FROM crm_encuestas_resultados WHERE encuesta_id='$encuesta_id' ORDER BY fecha";
  $result = $db->sql_query($sql) or die("Error al consultar preguntas 1<br>$sql");
  while(list($resultado_id, $contacto_id, $fecha) = $db->sql_fetchrow($result))
  {
    
    $sql = "SELECT nombre, apellido_paterno, apellido_materno, tel_casa, tel_oficina, tel_movil, tel_otro FROM crm_contactos WHERE contacto_id='$contacto_id'";
    $result1 = $db->sql_query($sql) or die("Error al consultar contacto");
    list( $nombre, $apellido_paterno, $apellido_materno, $tel_casa, $tel_oficina, $tel_movil, $tel_otro ) = $db->sql_fetchrow($result1);
    $array_preguntas = array("#", "ID", "Nombre", "Teléfono");
    if ($tel_casa) $telefono = $tel_casa;
    else if ($tel_oficina) $telefono = $tel_oficina;
    else if ($tel_movil) $telefono = $tel_movil;
    else $telefono = $tel_otro;
    $array_respuestas = array(++$indice, $resultado_id, "$nombre $apellido_paterno $apellido_materno", "$telefono");
    $sql = "SELECT pregunta_id, pregunta, tipo_id FROM crm_encuestas_preguntas WHERE encuesta_id='$encuesta_id' ORDER BY orden";
    $result1 = $db->sql_query($sql) or die("Error al consultar preguntas 2");
    while(list($pregunta_id, $pregunta, $tipo_id) = $db->sql_fetchrow($result1))
    {

      if (!($index_header_preguntas)) $array_preguntas[] = "$pregunta";
      switch($tipo_id)
      {
        case "1": //si no
                  $sql = "SELECT respuesta FROM crm_encuestas_respuestas_tipo_1 AS t WHERE t.pregunta_id='$pregunta_id' AND resultado_id='$resultado_id'";
                  $result2 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                  list($respuesta) = $db->sql_fetchrow($result2);
                    if ($respuesta)
                      $r = "VERDADERO";
                    else
                      $r = "FALSO";
                    $array_respuestas[] = "$r";
                  break;
        case "2": //opcion múltiple
                  $sql = "SELECT respuesta FROM crm_encuestas_respuestas_tipo_2 AS t WHERE t.pregunta_id='$pregunta_id' AND resultado_id='$resultado_id'";
                  $result2 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                  list($respuesta) = $db->sql_fetchrow($result2);

                  $sql = "SELECT opcion FROM crm_encuestas_preguntas_tipo_2 WHERE opcion_id='$respuesta' LIMIT 1";
                  $result3 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                  list($respuesta) = $db->sql_fetchrow($result3);
                  $array_respuestas[] = "$respuesta";

                  break;
        case "3": //abierta
                  $sql = "SELECT respuesta FROM crm_encuestas_respuestas_tipo_3 AS t WHERE t.pregunta_id='$pregunta_id' AND resultado_id='$resultado_id'";
                  $result2 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                  list($respuesta) = $db->sql_fetchrow($result2);

                  $array_respuestas[] = "$respuesta";

                  break;
        case "4": //selección múltiple
                  $sql = "SELECT respuesta FROM crm_encuestas_respuestas_tipo_4 AS t WHERE t.pregunta_id='$pregunta_id' AND resultado_id='$resultado_id'";
                  $result2 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                  $index_resp = 0;
                  $respuestas = "";
                  while (list($respuesta) = $db->sql_fetchrow($result2))
                  {
                    $sql = "SELECT opcion FROM crm_encuestas_preguntas_tipo_4 WHERE opcion_id='$respuesta' LIMIT 1";
                    $result3 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                    list($respuesta) = $db->sql_fetchrow($result3);
                    if ($index_resp++) $respuestas .= ", ";
                    $respuestas .= "$respuesta";
                  }
                  $array_respuestas[] = "$respuestas";
                  break;
        case "5": //usuario
                  $sql = "SELECT respuesta FROM crm_encuestas_respuestas_tipo_5 AS t WHERE t.pregunta_id='$pregunta_id' AND resultado_id='$resultado_id'";
                  $result2 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                  list($respuesta) = $db->sql_fetchrow($result2);

                  $sql = "SELECT name FROM users WHERE uid='$respuesta' LIMIT 1";
                  $result3 = $db->sql_query($sql) or die("Error al consultar preguntas tipo 5<br>".print_r($db->sql_error()));
                  list($respuesta) = $db->sql_fetchrow($result3);
                  $array_respuestas[] = "$respuesta";

                  break;
        case "6": //opcion múltiple
                  $sql = "SELECT respuesta FROM crm_encuestas_respuestas_tipo_6 AS t WHERE t.pregunta_id='$pregunta_id' AND resultado_id='$resultado_id'";
                  $result2 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                  list($respuesta) = $db->sql_fetchrow($result2);

                  $sql = "SELECT opcion FROM crm_encuestas_preguntas_tipo_6 WHERE opcion_id='$respuesta' LIMIT 1";
                  $result3 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                  list($respuesta) = $db->sql_fetchrow($result3);
                  $array_respuestas[] = "$respuesta";

                  break;
      }//switch


    }//while contactos
    if (!($index_header_preguntas++)) fputcsv($fp, $array_preguntas);
    fputcsv($fp, $array_respuestas);
  }
  /*
  $sql = "SELECT pregunta_id, pregunta, tipo_id FROM crm_encuestas_preguntas WHERE encuesta_id='$encuesta_id'  ORDER BY orden";
  $result = $db->sql_query($sql) or die("Error al consultar preguntas");
  while(list($pregunta_id, $pregunta, $tipo_id) = $db->sql_fetchrow($result))
  {
    $array_respuestas = array(++$indice, $pregunta);
    switch($tipo_id)
    {
      case "1": //si no
                $sql = "SELECT contacto_id, respuesta FROM crm_encuestas_respuestas_tipo_1 AS t, crm_encuestas_resultados AS r WHERE t.resultado_id=r.resultado_id AND t.pregunta_id='$pregunta_id'";
                $result2 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                while (list($contacto_id, $respuesta) = $db->sql_fetchrow($result2))
                {
                  if ($respuesta)
                    $r = "VERDADERO";
                  else
                    $r = "FALSO";
                  $array_respuestas[] = "$r";
                }
                fputcsv($fp, $array_respuestas);
                break;
      case "2": //opcion múltiple
                $sql = "SELECT contacto_id, respuesta FROM crm_encuestas_respuestas_tipo_2 AS t, crm_encuestas_resultados AS r WHERE t.resultado_id=r.resultado_id AND t.pregunta_id='$pregunta_id'";
                $result2 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                while (list($contacto_id, $respuesta) = $db->sql_fetchrow($result2))
                {
                  $sql = "SELECT opcion FROM crm_encuestas_preguntas_tipo_2 WHERE opcion_id='$respuesta' LIMIT 1";
                  $result3 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                  list($respuesta) = $db->sql_fetchrow($result3);
                  $array_respuestas[] = "$respuesta";
                }
                fputcsv($fp, $array_respuestas);
                break;
      case "3": //abierta
                $sql = "SELECT contacto_id, respuesta FROM crm_encuestas_respuestas_tipo_3 AS t, crm_encuestas_resultados AS r WHERE t.resultado_id=r.resultado_id AND t.pregunta_id='$pregunta_id'";
                $result2 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                while (list($contacto_id, $respuesta) = $db->sql_fetchrow($result2))
                {
                  $array_respuestas[] = "$respuesta";
                }
                fputcsv($fp, $array_respuestas);
                break;
      case "4": //selección múltiple
                $sql = "SELECT contacto_id, respuesta FROM crm_encuestas_respuestas_tipo_4 AS t, crm_encuestas_resultados AS r WHERE t.resultado_id=r.resultado_id AND t.pregunta_id='$pregunta_id'";
                $result2 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                while (list($contacto_id, $respuesta) = $db->sql_fetchrow($result2))
                {
                  $sql = "SELECT opcion FROM crm_encuestas_preguntas_tipo_4 WHERE opcion_id='$respuesta' LIMIT 1";
                  $result3 = $db->sql_query($sql) or die("Error al consultar preguntas tipo<br>".print_r($db->sql_error()));
                  list($respuesta) = $db->sql_fetchrow($result3);
                  $array_respuestas[] = "$respuesta";
                }
                fputcsv($fp, $array_respuestas);
                break;
    }
  }*/
  fclose($fp);
  header("location:encuesta$encuesta_id.csv");
}
else
{

}

?> 
