<?
#/usr/bin/ sh
# LLena la tabla de contactos asignados por periodos el periodo indicado ;)
global $db;

$startDate = $argv[2];
$lastDate = "";
$numDay = 30;

$lastDate = getLastDate($db, $startDate, $numDay);
$execMonitoringAssignedContact = "php scripts/monitoreo_contactos_asignados.php $startDate  $lastDate";
$execMonitoringFollowingContacto = "php scripts/comprueba_seguimiento.php $startDate $lastDate";

while(!empty($lastDate))
{    
    passthru ($execMonitoringAssignedContact);// el orden de los procesos no debe ser alterado
    passthru($execMonitoringFollowingContacto);
    $startDate = $lastDate;
    $lastDate = getLastDate($db, $startDate, $numDay);
    $execMonitoringAssignedContact = "php scripts/monitoreo_contactos_asignados.php $startDate  $lastDate";
    $execMonitoringFollowingContacto = "php scripts/comprueba_seguimiento.php $startDate $lastDate";
    echo "\nSe han ejecutado las tareas en el periodo del $startDate al $lastDate \n";
}

function getLastDate($db, $startDate, $numDay)
{    
    //hacer el calculo del siguiente periodo a partir de la fehca de inici (startDate)
    $sqlAddDate = "SELECT DATE_ADD('$startDate', INTERVAL $numDay DAY)";
    $resultAddDate = $db->sql_query($sqlAddDate) or die("Error al obtener la fecha final del periodo en el reporte de HRA HRC");
    list($lastDate) = $db->sql_fetchrow($resultAddDate);
    //comprobar que existan registros para la fecha especificada :)
    $sqlGetRegister = "select contacto_id from crm_contactos where fecha_importado >= '$startDate' and fecha_importado <= '$lastDate'";
    $resultGetRegister = $db->sql_query($sqlGetRegister) or die("Error al obtener los registros en el periodo->".$sqlGetRegister);
    list($existRegister) = $db->sql_fetchrow($resultGetRegister);
    if($existRegister != null)//si existen registros para el periodo especificado, entonces retornar la fecha limite
        return $lastDate;
    else //si no hay registros regresar vacio, condicion para terminar los procesos
        return "";
}
?>
