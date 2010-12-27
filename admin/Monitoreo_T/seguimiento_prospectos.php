<?

if (!defined('_IN_ADMIN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $how_many, $from, $rsort, $orderby, $sql, $result,$cant,$_module,$_op,$notassignedsdeassigneds,$notassignedsnews;
include_once("crea_filtro.php");
include_once("templateFiltros.php");
include_once 'genera_excel.php';
$filtro='';
if( count($tmp_filtros) > 0)
{
    $filtro= " AND ".implode(" AND ",$tmp_filtros);
}
$sql="SELECT a.gid,a.name FROM reporte_contactos_asignados a where a.gid>0 and ".$filtro." order by a.gid";
$tabla_campanas .= "
            <table align='center' class='tablesorter'>
            <thead>
            <tr>
            <th rowspan=\"1\">#</th>
            <th rowspan=\"1\">Concesionaria</th>
            <th rowspan=\"1\"width='7%'>Total</th>
            <th rowspan=\"1\" width='16%' onmouseover=\"return escape('Prospectos sin asignar')\">Sin asignar</th>
            <th rowspan=\"1\" onmouseover=\"return escape('Horas de retraso en atencion')\">HRA</th>
            <th colspan=\"1\" width='9%' onmouseover=\"return escape('Promedio de Hora de Retraso en Atencion')\"  border=0>Prom. HRA</th>
            <th colspan=\"1\" width='9%' onmouseover=\"return escape('Maximo en Horas de Retraso en Atencion')\" >Max. HRA</th>
            <th colspan=\"1\" width='10%' onmouseover=\"return escape('Prospectos asignados')\">Asignados</td>
            <th colspan=\"1\" width='14%' onmouseover=\"return escape('Porcentaje de prospectos asignados')\">% Asignados</th>
            <th colspan=\"1\" width='10%' onmouseover=\"return escape('Prospectos en seguimiento')\">P. Seguimiento</th>
            <th colspan=\"1\" width='6%' onmouseover=\"return escape('Horas de retraso en compromiso')\">HRC</th>
            <th colspan=\"1\" width='8%' onmouseover=\"return escape('Promedio de Hora de Retraso en Compromiso')\">Prom. HRC</th>
            <th colspan=\"1\" width='8%' onmouseover=\"return escape('Maximo de Hora de Retraso en Compromiso')\" >Max. HRC</th>
            <th colspan=\"1\"width='11s%'>Vendedor</th>
            <th colspan=\"1\"width='14%'>Prospectos</th>
            </tr>
            </thead>";

$class_row = 0;
$sql = "select distinct a.gid, a.name, count(a.contacto_id), sum(a.horas_retraso_atencion),
        avg(a.horas_retraso_atencion), max(a.horas_retraso_atencion), sum(a.en_seguimiento),
        sum(a.horas_retraso), avg(a.horas_retraso), max(a.horas_retraso) from reporte_contactos_asignados
        as a where a.gid >0  ".$filtro." group by a.gid order by a.gid,a.name asc";

$resultGetDataGroup = $db->sql_query($sql) or die("Error al obtener los datos por grupo->".$sql);
while(list($gid, $groupName, $contactsByGroup, $totalHRA, $avgHRA, $maxHRA, $followingContact,
        $totalHRC, $avgHRC, $maxHRC) = $db->sql_fetchrow($resultGetDataGroup))
{

    $assignedContacts = getAssignedContacts($db, $gid,$filtro);
    $assignedPorcent = getPorcentAsignedContact($contactsByGroup, $assignedContacts);
    $notassigned =0;
    if($contactsByGroup > 0)
        $notassigned = $contactsByGroup - $assignedContacts;

    //asignar valor de 0 en caso de que sean nulos
    setDafultValue($contactsByGroup, 0);
    setDafultValue($totalHRA, 0);
    setDafultValue($avgHRA, 0);
    setDafultValue($maxHRA, 0);
    setDafultValue($totalHRC, 0);
    setDafultValue($avgHRC, 0);
    setDafultValue($maxHRC, 0);
    setDafultValue($assignedContacts, 0);
    setDafultValue($assignedPorcent, 0);
    setDafultValue($notassigned, 0);


    $tabla_campanas .=  "<tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;\">"
    ."<td>$gid</td>"
    ."<td>$groupName</td>"
    ."<td>$contactsByGroup</td>"
    ."<td>$notassigned</td>"
    ."<td>". brigeFormatDayToDayHour($totalHRA)." </td>"
    ."<td>". brigeFormatDayToDayHour($avgHRA)." </td>"
    ."<td>". brigeFormatDayToDayHour($maxHRA)." </td>"
    ."<td>$assignedContacts</td>"
    ."<td>".getPorcentAsignedContact($contactsByGroup, $assignedContacts)."</td>"
    ."<td>".$followingContact." </td>"
    ."<td>".brigeFormatDayToDayHour($totalHRC)." </td>"
    ."<td>".brigeFormatDayToDayHour($avgHRC)." </td>"
    ."<td>".brigeFormatDayToDayHour($maxHRC)." </td>"
    ."<td align='center'><a href='index.php?_module=Monitoreo_T&_op=seguimiento_prospectos_vendedor&totalGlobal=$assignedContacts&gid=$gid$url'>Vendedores</a></td>"
    ."<td align='center'><a href='index.php?_module=Monitoreo_T&_op=seguimiento_concesionaria_prospectos&gid=$gid$url'>Prospectos</a></td>"
    ."</tr>";
    $totales["totalProspectos"] = $totales["totalProspectos"] + $contactsByGroup;
    $totales["totalNoAsignados"] = $totales["totalNoAsignados"] + $notassigned;
    $totales["HRA"] = $totales["HRA"] + $totalHRA;
    $totales["HRAVG"] = $totales["HRAVG"] + $avgHRA;
    $totales["HRAMAX"] = $totales["HRAMAX"] + $maxHRA;
    $totales["totalAsignados"] = $totales["totalAsignados"] + $assignedContacts;
    $totales["HRC"] = $totales["HRC"] + $totalHRC;
    $totales["HRCAVG"] = $totales["HRCAVG"] + $avgHRC;
    $totales["HRCMAX"] = $totales["HRCMAX"] + $maxHRC;
    $totales["following"] = $totales["following"] + $followingContact;
}
$tabla_campanas .= "<thead><tr>
                            <td></td>
                            <td>Totales:</td>
                            <td>".$totales["totalProspectos"]."</td>
                            <td>".$totales["totalNoAsignados"]."</td>
                            <td>".brigeFormatDayToDayHour($totales["HRA"])."</td>
                            <td>".brigeFormatDayToDayHour($totales["HRAVG"])."</td>
                            <td>".brigeFormatDayToDayHour($totales["HRAMAX"])."</td>
                            <td>".$totales["totalAsignados"]."</td>
                            <td></td>
                            <td>".$totales["following"]."</td>
                            <td>".brigeFormatDayToDayHour($totales["HRC"])."</td>
                            <td>".brigeFormatDayToDayHour($totales["HRCAVG"])."</td>
                            <td>".brigeFormatDayToDayHour($totales["HRCMAX"])."</td>
                            <td></td>
                            <td></td>
                            </tr></thead></table>";
$report = new Genera_Excel($tabla_campanas,"reporteSeguimiento".date("d-m-Y"));
$reporteExcel = $report->Obten_href();

if(!$class_row)
$tabla_campanas= "<br><center>La consulta no arroja resultados</center><br>";

include_once("templateFiltros.php");


/*
 * Asigna un valor 0 a una variable en caso de que se nula
 * @param int name -> la variable a la que se asignara una valor por default
 * @param int val -> el valor que se ha de asignar
 * @return void
 */

function setDafultValue(&$name, $val)
{
    if($name == "" || $name == null)
    $name = $val;
    return;
}
/**
 * Obtiene el numero de horas de retraso de los contactos en base a una concesionaria
 * @param $gid -> id de la concesionaria
 * @return $horas_retraso -> el total de horas de retraso de la concesionaria
 */
function getHoursDelayByGid($db, $gid)
{
    $isnext = 1;
    $delay = 0;
    $inFollowing = 0;
    $sql = "select sum(horas_retraso),max(horas_retraso),avg(horas_retraso),
            count(contacto_id) from reporte_contactos_asignados where gid='$gid'
            and en_seguimiento='$isnext' and uid > 0";
    $result = $db->sql_query($sql);
    list($sum, $max, $avg ,$inFollowing) = $db->sql_fetchrow($result);
    if($inFollowing== "") $inFollowing=0;
    return array("sum" => $sum, "max" => $max, "avg" =>$avg, "follow" => $inFollowing);
}
/*
 * Obtiene el total de contactos asignados por grupo
 * @param Object $db -> base de datos
 * @param int $gid -> grupo al cual se han de obtener los contactos asignados
 * @return int numero de contactos asignados
 */
function getAssignedContacts($db, $gid,$filtro)
{    
    $sql = " select count(a.contacto_id) from reporte_contactos_asignados as a where
             uid > 0 and a.gid='$gid' ".$filtro." order by a.name asc;";
    $result = $db->sql_query($sql);
    list($assignedContact) = $db->sql_fetchrow($result);
    return $assignedContact;
}

/*
 * Obtiene el porcentaje de contactos asignados
 * @param $totalContact -> Total de contactos
 * @param $asginedContact -> Contactos asignados a un vendedor
 * @return -> porcentaje que representa los contactos asignados con respecto al total
 */
function getPorcentAsignedContact($totalContact, $asignedContact)
{
    if($totalContact == 0)
    return 0;
    else
    return number_format((($asignedContact * 100)/$totalContact),2,".","");
}

/*
 * Obtiene las horas de retraso de los contactos de una concesionaria
 * @param $filtros -> los filtros que se han de poner al quiery
 * @return -> total de horas de atencion
 */
function getHoursDelayAttention($db, $filtro, $gid)
{
    $sql = "select sum(horas_retraso_atencion), max(horas_retraso_atencion),
            avg(horas_retraso_atencion) from reporte_contactos_asignados where gid='$gid' and uid > 0";
    //die($sql);
    $result = $db->sql_query($sql) or die("Erro al obtener el sum, max, avg de las horas de retraso en atencion->".$sql);
    list($total,$max,$avg) = $db->sql_fetchrow($result);
    return array("total" =>$total, "max" => $max, "avg" => $avg);
}
function brigeFormatDayToDayHour($totalHours)
{

    $hoursForDay = 24;
    $days = "" +  ($totalHours/$hoursForDay);
    list($day,$decimalDay) = explode(".",$days);
    $decimalDay =  ((float)(".".$decimalDay))*$hoursForDay;
    return "$day d ".(int)$decimalDay." h";
}
?>