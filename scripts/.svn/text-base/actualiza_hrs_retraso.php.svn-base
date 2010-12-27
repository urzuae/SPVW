<?php
/* 
 * Este Script actualiza el numero de horas en asignacion de la tabla temporal
 *
 */

global $db;

echo"\n Actualizando lash horas de retraso en asignación de la tabla temporal\n\n";

$date_actual=date('Y-m-d H:i:s');
$dia=date('Y-m-d');
$sql="update reporte_contactos_asignados  as a
      SET horas_retraso_asignacion = (select TIMESTAMPDIFF(HOUR,a.fecha_retraso_asig,'".$date_actual."')), a.timestamp='".$date_actual."'  WHERE a.uid = 0  AND  a.retraso_asignacion = 1;";
    if($db->sql_query($sql))
    {
        echo"\nsql:  ".$sql;
        echo"\n se ha actualizado la hora de retraso para el proceso de asignacion.";
    }
    else
    {
        echo"\nE R R O R:  No se actualizaron las horas de retraso a este dia";
    }
?>
