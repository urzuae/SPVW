<?
include_once("genera_excel.php");
if (!defined('_IN_ADMIN_MAIN_INDEX')) 
{
    die ("No puedes acceder directamente a este archivo...");
}
	global $db, $how_many, $from, $rsort, $orderby, $sql, $result;
	global $cant,$_module,$_op,$notassignedsdeassigneds,$notassignedsnews;
	global $tmp_filtros,$filtro;
    include_once("crea_filtro.php");
    $filtro='';
	if( count($tmp_filtros) > 0)
	{
		$filtro=" AND ".implode(" AND ",$tmp_filtros);
    }
	$array_totales=genera_totales($db,$filtro);
	$array_asignados=genera_asignados($db,$filtro);

    $date=date("Y-m-d H:i:s");
    $sql="SELECT a.gid,a.name,sum(a.horas_retraso_asignacion) as retrasos, sum(a.retraso_asignacion) as p_retrasados,max(a.horas_retraso_asignacion) as maximo FROM reporte_contactos_asignados a where  a.gid > 0 ".$filtro." GROUP BY a.gid;";
    $result = $db->sql_query($sql) or die("Error".print_r($sql));
    if($db->sql_numrows($result)> 0)
    {
			$tabla_campanas .= "
			<table align='center' class='tablesorter'>
			<thead><tr>
			<th width='5%'>#</th>
			<th width='32%'>Concesionaria</th>
			<th width='8%'>No Prospectos</th>
			<th width='10%'>P Asignados</th>
			<th width='8%'>%  Asignaci&oacute;n</th>
			<th width='8%'>Hrs Retraso</th>
			<th width='8%'>P Retraso</th>
			<th width='10%'>Prom de Hrs</th>
			<th width='8%'>Max hrs retraso</th>
			<th width='5%'>&nbsp;</th>
			</tr>
			</thead>";
			$total_fin=0;
			$asignado_fin=0;
            $hrs_total=0;
            $prosp_total=0;
            $hrs_max_total=0;
            while (list($gid, $name, $retrasos,$p_retrasados,$maximo) = $db->sql_fetchrow($result))
            {
                $tmp_gid=0 + $gid;
                // procentaje de asignados
                $porcentaje_asignados=0 + ( ($array_asignados[$gid] * 100) /$array_totales[$gid]);
                $porcentaje_asignados= number_format($porcentaje_asignados, 2, '.', ' ');

                // procentaje de horas retraso
                $no_de_prospectos_retrasados=$p_retrasados;
                if ($no_de_prospectos_retrasados <= 0)
                    $no_de_prospectos_retrasados=1;

                $porcentaje_horas=$retrasos / $no_de_prospectos_retrasados;
                $porcentaje_horas=number_format($porcentaje_horas,2,',','');

                // pintamos los valores en la tabla
				$tabla_campanas .= "<tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer; height:35px;\">"
									."<td>$gid</td>"
                                    ."<td align='left'>$name</td>"
									."<td align='right'>$array_totales[$gid]</td>"
                                    ."<td align='right'>".($array_asignados[$gid] + 0)."</td>"
									."<td align='right'>$porcentaje_asignados</td>"
                                    ."<td align='right'>".brigeFormatDayToDayHour($retrasos + 0)."</td>"
                                    ."<td align='right'>".$p_retrasados."</td>"
									."<td align='right'>".brigeFormatDayToDayHour($porcentaje_horas + 0)."</td>"
                                    ."<td align='right'>".brigeFormatDayToDayHour($maximo + 0)."</td>"
									."<td align='center'><a href='index.php?_module=Monitoreo_T&_op=monitoreo_prospectos_asignados&gid=$gid$url'>Prospectos</a></td>"
									."</tr>";

				$total_fin     = 0 + $total_fin + $array_totales[$gid];
				$asignado_fin  = 0 +$asignado_fin + $array_asignados[$gid];
                $hrs_total     = 0 + $hrs_total + $retrasos;
                $prosp_total   = 0 + $prosp_total + $p_retrasados;

                if($maximo > $hrs_max_total )
                    $hrs_max_total = $maximo;
            }
            if($prosp_total==0)
                $prosp_total=1;

            $porcentaje_total_retraso=number_format( ($hrs_total/$prosp_total),2,'.','');
            $porcentaje_total=(($asignado_fin/$total_fin) *100);
			$tabla_campanas .= "<thead><tr>
							<td>&nbsp;</td>
							<td align='right'>Totales:</td>
							<td align='right'>$total_fin</td>
							<td align='right'>$asignado_fin</td>
							<td align='right'>".number_format ($porcentaje_total, 2, '.', ' ')."</td>
							<td align='right'>".$hrs_total."</td>
							<td align='right'>".$prosp_total."</td>
							<td align='right'>".$porcentaje_total_retraso."</td>
							<td align='right'>".$hrs_max_total."</td>
							<td align='center'>&nbsp;</td>
							</tr></thead></table>";
          $objeto = new Genera_Excel($tabla_campanas,'Asignacion-Concesionarias');
          $boton_excel=$objeto->Obten_href();

    }
	else
	{
		$tabla_campanas= "<br><center>La consulta no arroja resultados</center><br>";
	}
    include_once("templateFiltros.php");

/*** funciones auxiliares  *****/
/**
 * Funcion que sirve para sacar el total de asignados por gid
 */
function genera_totales($db,$filtro)
{
	$sql_totales = "SELECT a.gid,count(a.gid) as totales FROM reporte_contactos_asignados a where a.gid>0 ".$filtro." GROUP BY gid ORDER BY gid;";
	$res_totales = $db->sql_query($sql_totales) or die("Error".print_r($db->sql_error()));
	if($db->sql_numrows($res_totales) > 0)
	{
		while($fila=$db->sql_fetchrow($res_totales))
		{
			$array_totales[$fila['gid']]=$fila['totales'];
		}
	}
	return $array_totales;
}

/**
 * Funcion que sirve para sacar el total de asignados y con uid distinto de cero
 */
function genera_asignados($db,$filtro)
{
	$sql_asignados = "SELECT a.gid,count(a.gid) as totales FROM reporte_contactos_asignados a WHERE a.uid!=0 ".$filtro." GROUP BY gid ORDER BY gid;";
	$res_asignados = $db->sql_query($sql_asignados) or die("Error".print_r($db->sql_error()));
	if($db->sql_numrows($res_asignados) > 0)
	{
		while($fila=$db->sql_fetchrow($res_asignados))
		{
			$array_asignados[$fila['gid']]=$fila['totales'];
		}
	}
	return $array_asignados;
}

function brigeFormatDayToDayHour($totaldays)
{
    $hoursForDay = 24;
    $days = "" +  ($totaldays/$hoursForDay);
    list($day,$decimalDay) = explode(".",$days);
    $decimalDay =  ((float)(".".$decimalDay))*$hoursForDay;
    return "$day d ".(int)$decimalDay." h";
}

?>