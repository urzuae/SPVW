<?
include_once 'genera_excel.php';
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $how_many, $from, $campana_id, $nombre, $apellido_paterno, $apellido_materno,
    $submit, $status_id, $ciclo_de_venta_id, $uid,$gid,$rsort, $open,$orderby,$uid_,$_module,
    $_op,$url,$filtro,$leyenda_filtros,$tmp_filtros;

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

$sql = "select a.contacto_id, llamadas.id, campanas.campana_id,campanas.nombre,
        a.prospecto, a.vendedor, a.horas_retraso_atencion, a.horas_retraso from
        reporte_contactos_asignados as a left join crm_campanas_llamadas as llamadas
        on a.contacto_id=llamadas.contacto_id left join crm_campanas as  campanas on
        llamadas.campana_id=campanas.campana_id where  a.gid='$gid' and a.uid='$uid'  $filtro";

$tabla_campanas .= "<table class=\"tablesorter\">"
."<thead>"
."<tr>"
."<th>Campaña</th>"
."<th>Nombre</th>"

."<th>HRA</th>"
."<th>HRC</th>"
."<th>Primer contacto</th>"
."<th>Ultimo contacto</th>"
."</tr></thead>"
."<tbody>";

$vendedor = "";
$result = $db->sql_query($sql) or die("Error al obtener los prospectos->".$sql);
$total = array();
while(list($contactId, $calledId, $campaignId,$campaignName,$contactName, $salesName,$hoursDelayAttention, $hoursDelayCompromise) = $db->sql_fetchrow($result))
{
    $tabla_campanas .= "<tr class=\"row".(($c++%2)+1)."\">"
    ."<td>$campaignName</td>"
    ."<td><a href=\"index.php?_module=$_module&_op=llamada_ro&llamada_id=$calledId&contacto_id=$contactId&campana_id=$campaignId\">$contactName</a></td>"   
    ."<td>".brigeFormatDayToDayHour($hoursDelayAttention)."</td>"
    ."<td>".brigeFormatDayToDayHour($hoursDelayCompromise)."</td>"
    ."<td>".getFirstDateContact($db, $contactId)."</td>"
    ."<td>".getLastDateContact($db, $contactId)."</td>"
    ."</tr>";
    $vendedor = $salesName;
    $total["contacts"] = $total["contacts"] + 1;
    $total["hoursDelayCompromise"] = $total["hoursDelayCompromise"] + $hoursDelayCompromise;
    $total["hoursDelayAttention"] = $total["hoursDelayAttention"] + $hoursDelayAttention;
}
$tabla_campanas .= "</tbody><thead><tr>
                <td>Total prospectos:</td>
                <td>".$total["contacts"]."</td>                
                <td>".brigeFormatDayToDayHour($total["hoursDelayAttention"])."</td>
                <td>".brigeFormatDayToDayHour($total["hoursDelayCompromise"])."</td>
                <td></td>
                <td></td>
                </tr></thead></tr></table>";
$report = new Genera_Excel($tabla_campanas,"reporteSeguimientProspectosPorConcesionaria".date("d-m-Y"));
$reporteExcel = $report->Obten_href();
require_once("templateVehiculo.php");
    /*
     * Obtiene la fecha del  primer contacto de un prospecto
     * @param $db -> conexion a la bd
     * @parma $idContact -> id del contacto
     * @return -> fecha del primer contacto
     */
function getFirstDateContact($db, $idContact)
{
    $sql = "select eventos.fecha_cita from crm_campanas_llamadas as llamadas,
        crm_campanas_llamadas_eventos as eventos where llamadas.id=eventos.llamada_id and
        llamadas.contacto_id='$idContact' order by eventos.fecha_cita asc limit 1";
    $result = $db->sql_query($sql);
    list($timestamp) = $db->sql_fetchrow($result);
    list($fecha, $hora) = explode(" ",$timestamp);
    return date_reverse($fecha);
}
function getLastDateContact($db, $idContact)
{
    $sql = "select eventos.fecha_cita from crm_campanas_llamadas as llamadas,
        crm_campanas_llamadas_eventos as eventos where llamadas.id=eventos.llamada_id and
        llamadas.contacto_id='$idContact' order by eventos.fecha_cita desc limit 1";
    $result = $db->sql_query($sql);
    list($timestamp) = $db->sql_fetchrow($result);
    list($fecha, $hora) = explode(" ",$timestamp);
    return date_reverse($fecha);
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