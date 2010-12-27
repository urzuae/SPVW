<?
/**
 * Funcion que sirve para sacar el total de asignados por gid
 */

include_once("genera_excel.php");
global $db, $tabla, $gid, $from, $campana_id, $uid, $orderby,$tmp_filtros,$order,$url;
if (!defined('_IN_ADMIN_MAIN_INDEX'))
{
	die ("No puedes acceder directamente a este archivo...");
}
	include_once("regresa_filtros.php");
	$filtro=implode(" AND ",$tmp_filtros);
    include_once ("crea_filtro_vehiculo.php");
    if(count($filtroPorVehiculo) > 0)
    {
        $filtro.=" AND ".implode (" AND ",$filtroPorVehiculo);
    }

    $array_totales=genera_total($db,$filtro);
    $array_reasignados=genera_reasignados($db,$filtro);

    $contador_totales=0;
	$sql="SELECT DISTINCT a.gid,a.uid,a.vendedor,sum(a.no_reasignaciones) as total FROM reporte_contactos_asignados a where a.uid >0  AND ".$filtro." group by a.uid order by a.vendedor ASC;";
    $res = $db->sql_query($sql)  or die("Error".$sql);
	if($db->sql_numrows($res) > 0)
	{
		$tabla_vendedores.= '
			<table align="center" class="tablesorter">
				<thead>
			    	<tr>
			        	<th width="28%">Nombre del Vendedor</th>
						<th width="12%">Total Prospectos</th>
			            <th width="12%">Total Prospectos Reasignados</th>
			            <th width="12%">No. de Reasignaciones</th>
			            <th width="12%">No. Prospectos Recibidos</th>
                        <th width="12%">No. Prospectos Quitados</th>
                        <th width="12%">Max de Reasignaciones</th>
			        </tr>
				</thead>';
			    $tmp_totales=0;
			    $tmp_reasig=0;
			    $tmp_total_reasig=0;

			    $total_tmp_totales=0;
			    $total_tmp_reasig=0;
			    $total_tmp_total_reasig=0;
                $t_maximo=0;
			   while($fila = $db->sql_fetchrow($res))
				{
					$uid=$fila['uid'];
			    	$tmp_totales=0 + $array_totales[$uid];
					$tmp_reasig=0 + $array_reasignados[$uid];
					$tmp_total_reasig=0 + $fila['total'];
                    $tmp_recibidos= 0 + movimientos_recibidos($db,$uid);
                    $tmp_quitados=0 + movimientos_quitados($db,$uid);
                    $max_reasig=0 + $array_reasignados[$uid]['maximo'];
                    if($max_reasig > $t_maximo)
                        $t_maximo = $max_reasig;
					/*if( ($tmp_totales) > 0)
			        {*/
			    		$total_tmp_totales = $total_tmp_totales + $tmp_totales;
						$total_tmp_reasig  = $total_tmp_reasig + $tmp_reasig;
						$total_tmp_total_reasig = $total_tmp_total_reasig + $tmp_total_reasig;
                        $total_recibidos = $total_recibidos + $tmp_recibidos;
                        $total_quitados = $total_quitados + $tmp_quitados;
			        	$tabla_vendedores.= "<tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;\">
			        	<td align='left'><a href='index.php?_module=Monitoreo_T&_op=monitoreo_prospecto_vendedor_reasignado&gid=$gid&uid=$uid$url'>".$fila['uid']."  ".$fila['vendedor']."</a></td>
			        	<td>".$tmp_totales."</td>
			        	<td>".$tmp_reasig."</td>
                        <td>".$tmp_total_reasig."</td>
                        <td>".$tmp_recibidos."</td>
                        <td>".$tmp_quitados."</td>
			        	<td>".$max_reasig."</td>
			        	</tr>";
			        //}
				}
		$tabla_vendedores.= "
			<thead><tr>
			<td>Total</td>
			<td>".$total_tmp_totales."</td>
			<td>".$total_tmp_reasig."</td>
			<td>".$total_tmp_total_reasig."</td>
            <td>".$total_recibidos."</td><td>".$total_quitados."</td><td>".$t_maximo."</td>
			</tr></thead></table>";
            $objeto = new Genera_Excel($tabla_vendedores,'Reasignacion-Vendedores');
            $boton_excel=$objeto->Obten_href();
	}
	else
	{
		$tabla_vendedores= "<br>Los prospectos no estan asignados a ning&uacute;n Vendedor<br>";
	}
include_once("templateVehiculo.php");


/*********** FUNCIONES AUXILIARES  ********************/
 /**
 * Funcion que sirve para sacar el total de prospectos por gid
 *
 * @param int conexion a Base de datos $db
 * @param string nombre de la tabla de donde se recuperan los datos $tabla
 * @param string filtro para consultar la BD  $filtro
 * @return array con los totales de prospectos por grupo
 */
function genera_total($db,$filtro)
{
	if(! empty($filtro))
		$filtro=" AND ".$filtro;

	$sql_totales = "SELECT a.gid,a.uid,count(a.uid) as totales FROM  reporte_contactos_asignados a WHERE a.gid > 0 ".$filtro." GROUP BY a.uid ORDER BY a.uid;";
	$res_totales = $db->sql_query($sql_totales) or die("Error".$sql_totales);
	if($db->sql_numrows($res_totales) > 0)
	{
		while($fila=$db->sql_fetchrow($res_totales))
		{
			$array_totales[$fila['uid']]=$fila['totales'];
		}
	}
	return $array_totales;
}


function movimientos_recibidos($db,$uid)
{
    $recibidos=0;
    $sql_recibidos="SELECT count(*) FROM crm_contactos_asignacion_log WHERE to_uid=".$uid." AND from_uid >0;";
    $res_recibidos=$db->sql_query($sql_recibidos);
    if( $db->sql_numrows($res_recibidos) > 0)
        $recibidos=$db->sql_fetchfield(0,0,$res_recibidos);
    return $recibidos;
}

function movimientos_quitados($db,$uid)
{
    $quitados=0;
    $sql_quitados="SELECT count( * ) FROM crm_contactos_asignacion_log WHERE from_uid=".$uid." AND to_uid >0";
    $res_quitados=$db->sql_query($sql_quitados);
    if( $db->sql_numrows($res_quitados) > 0)
        $quitados=$db->sql_fetchfield(0,0,$res_quitados);
    return $quitados;
}
/**
 * Funcion que sirve para sacar el total de reasignaciones
 *
 * @param int conexion a Base de datos $db
 * @param string nombre de la tabla de donde se recuperan los datos $tabla
 * @param string filtro para consultar la BD  $filtro
 * @return array con los totales de prospectos con resignacion por vendedor
 */
function genera_reasignados($db,$filtro)
{
	if(!empty($filtro))
		$filtro=" AND ".$filtro;
    $sql_reasignados="SELECT a.gid,a.uid,count(a.uid) as totales FROM  reporte_contactos_asignados a WHERE a.uid > 0 and a.no_reasignaciones > 0 ".$filtro." GROUP BY a.uid ORDER BY a.uid;";
	$res_reasignados = $db->sql_query($sql_reasignados) or die("Error".$sql_reasignados);
	if($db->sql_numrows($res_reasignados) > 0)
	{
        $tmp_uid=0;
        while($fila=$db->sql_fetchrow($res_asignados))
		{
            $array_reasignados[$fila['uid']]=$fila['totales'];
		}
	}
	return $array_reasignados;
}
?>
