<?php
  
  define('_IN_MAIN_INDEX', '1');
  @chdir('/home2/phpnuke/html/vw');

  require_once("config.php");
  require_once("$_includesdir/main.php");
  
  global $db;
  $cambiados = 0;
  $sql = "SELECT `contacto_id` , `fecha_importado`, `timestamp`
          FROM `crm_contactos`
          WHERE `fecha_importado` IS NULL ORDER BY contacto_id;";
  $result = $db->sql_query($sql) or die($sql);
  while(list($contacto, $fecha_importado,$timestamp_o) = $db->sql_fetchrow($result)){
     $sqlLog = sprintf("SELECT timestamp FROM `crm_contactos_asignacion_log`
             WHERE `contacto_id` = %s LIMIT 1;",$contacto);
     $resultLog = $db->sql_query($sqlLog) or die($sqlLog);
     list($timestamp) = $db->sql_fetchrow($resultLog);
     if($timestamp == NULL)
         $sqlUpdate = sprintf("UPDATE crm_contactos SET fecha_importado = '%s' WHERE contacto_id = %s",$timestamp_o,$contacto);
     else
         $sqlUpdate = sprintf("UPDATE crm_contactos SET fecha_importado = '%s' WHERE contacto_id = %s",$timestamp,$contacto);
     $resultUpdate = $db->sql_query($sqlUpdate) or die($sqlUpdate."\n");
     if(!$resultUpdate){
       echo "Error Al modificar el contacto con el id ".$contacto;
       exit;
     } 
     //echo sprintf("Registro %s cambiado \n",$contacto);
     $cambiados++;
  }
  echo sprintf("\n%s Registros cambiados \n",$cambiados);
?>

