<?
global $db, $tabla, $gid, $from, $campana_id, $uid, $orderby,$tmp_filtros,$order,$url, $totalGlobal, $filtroVehiculo;

$tabla=" reporte_contactos_asignados ";
if (!defined('_IN_ADMIN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
include_once("regresa_filtros.php");
include_once ("crea_filtro_vehiculo.php");
include_once 'genera_excel.php';
include_once("templateFiltros.php");
$filtro='';
if(count($filtroPorVehiculo))
{
	$filtro.=" AND ".implode(" AND ",$filtroPorVehiculo);
}

if(count($tmp_filtros))
{
    $filtro.=" AND ".implode(" AND ",$tmp_filtros);
}

$tabla_vendedores.= '
            <table border="0" class="tablesorter">
                <thead>
                    <tr>
                        <th>Vendedor</th>
                        <th>HRA</th>
                        <th>Prom. HRA</th>
                        <th>Max. HRA</th>
                        <th>Asignados</th>
                        <th>% Asignados</th>
                        <th>Prospectos en seguimiento</th>
                        <th>HRC</th>
                        <th>Prom. HRC</th>
                        <th>Max. HRC</th>
                    </tr>
                </thead>
                <tbody>';

$class_row = 0;
$sqlDataSales = "SELECT DISTINCT a.uid, a.vendedor,  sum( a.horas_retraso_atencion), avg(horas_retraso_atencion),
        max(horas_retraso_atencion), count( a.contacto_id), sum(en_seguimiento),
        sum(a.horas_retraso), avg(a.horas_retraso), max(a.horas_retraso) FROM reporte_contactos_asignados
        as a where  a.gid='$gid' and a.uid <> 0  $filtro group by a.uid  order by vendedor ASC";
$sqlGetTotalAsignados = "select count(a.contacto_id) from reporte_contactos_asignados as a where
        a.gid='203' and a.uid <> 0 and $gid limit 1";
$resultAssignedContact = $db->sql_query($sqlGetTotalAsignados);
list($totalAssignedContact) = $db->sql_fetchrow($resultAssignedContact);

$resultDataSales = $db->sql_query($sqlDataSales) or die("Error al obtener los datos por vendedor->".$sqlDataSales);
while(list($uid, $salesName, $totalHRA,$avgHRA, $maxHRA, $totalAssigned, $followingContact,
        $totalHRC,$avgHRC, $maxHRC)= $db->sql_fetchrow($resultDataSales))
{
    $assignedPorcent = getPorcentAsignedContact($totalAssignedContact, $totalAssigned);
    $tabla_vendedores.= "<tr class=\"row".($class_row++%2?"2":"1")."\">
                        <td><a href='index.php?_module=Monitoreo_T&_op=seguimiento_prospectos_por_vendedor&uid=$uid$url'>".$salesName."</a></td>"
    ."<td>".brigeFormatDayToDayHour($totalHRA)."</td>"
    ."<td>".brigeFormatDayToDayHour($avgHRA)."</td>"
    ."<td>".brigeFormatDayToDayHour($maxHRA)."</td>"
    ."<td>".$totalAssigned."</td>"
    ."<td>".$assignedPorcent."</td>"
    ."<td>".$followingContact."</td>"
    ."<td>".brigeFormatDayToDayHour($totalHRC)."</td>"
    ."<td>".brigeFormatDayToDayHour($avgHRC)."</td>"
    ."<td>".brigeFormatDayToDayHour($maxHRC)."</td>"
    ."</tr>";
    $totales["HRA"] = $totales["HRA"] + $totalHRA;
    $totales["HRAVG"] = $totales["HRAVG"] + $avgHRA;
    $totales["HRAMAX"] = $totales["HRAMAX"] + $maxHRA;
    $totales["assigned"] = $totales["assigned"] + $totalAssigned;
    $totales["assignedPorcent"] = $totales["assignedPorcent"] + $assignedPorcent;
    $totales["follow"] = $totales["follow"] + $followingContact;
    $totales["delay"] = $totales["delay"] + $totalHRC;
    $totales["avg"] = $totales["avg"] + $avgHRC;
    $totales["max"] = $totales["max"] + $maxHRC;
}

    $tabla_vendedores.= "
            <thead>
            <tr>
            <td>Totales</td>
            <td>".brigeFormatDayToDayHour($totales["HRA"])."</td>
            <td>".brigeFormatDayToDayHour($totales["HRAVG"])."</td>
            <td>".brigeFormatDayToDayHour($totales["HRAMAX"])."</td>
            <td>".$totales["assigned"]."</td>
            <td>".$totales["assignedPorcent"]."</td>
            <td>".$totales["follow"]."</td>
            <td>".brigeFormatDayToDayHour($totales["delay"])."</td>
            <td>".brigeFormatDayToDayHour($totales["avg"])."</td>
            <td>".brigeFormatDayToDayHour($totales["max"])."</td>
            </tr>
            </thead>
            </tr></table>";
    $report = new Genera_Excel($tabla_vendedores,"reporteSeguimientoVendedoresProspectos".date("d-m-Y"));
    $reporteExcel = $report->Obten_href();

if(!$db->sql_numrows($resultDataSales))
    $tabla_vendedores= "<br>Los prospectos no estan asignados a ning&uacute;n Vendedor<br>";

require_once("templateVehiculo.php");

function brigeFormatDayToDayHour($totalHours)
{

    $hoursForDay = 24;
    $days = "" +  ($totalHours/$hoursForDay);
    list($day,$decimalDay) = explode(".",$days);
    $decimalDay =  ((float)(".".$decimalDay))*$hoursForDay;
    return "$day d ".(int)$decimalDay." h";
}
/*
 * Obtiene el pocentaje respectoa aun total
 * @param int $totalContact -> total
 * @param int $asignedContact -> muestra
 * @return float
 */
function getPorcentAsignedContact($totalContact, $asignedContact)
{
    if($totalContact == 0)
    return 0;
    else
    return number_format((($asignedContact * 100)/$totalContact),2,".","");
}
?>
