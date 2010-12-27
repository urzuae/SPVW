<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 *
 * Se actualiza la tabal reporte_contactos_asignado por dia. La idea es revisar los logs y solo calcular las HRA's y HRC's
 * para ese contacto. Y para el resto solo sumara a partir del timestamp las HRA y HRC segun el caso
 */
include_once ("libForHRAC.php");
global $db;

$startDate = $argv[2];
$lastDate = $argv[3];
if(empty ($startDate) && empty ($lastDate))
die("Por favor proporcione al menos una fecha para el reporte");
if(empty ($lastDate))
$lastDate = $startDate;
//ejecuta el proceso
updateReportAssignedContactForDay($db, $startDate, $lastDate);
?>
