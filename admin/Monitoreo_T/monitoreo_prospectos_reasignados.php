<?
    include_once("genera_excel.php");
	if (!defined('_IN_ADMIN_MAIN_INDEX'))
	{
	    die ("No puedes acceder directamente a este archivo...");
	}

	global $db, $campana_id, $nombre, $apellido_paterno, $apellido_materno,
    $submit, $status_id, $ciclo_de_venta_id, $uid,$gid,$rsort, $open,$orderby,$uid_,$_module,
    $nsort,$_op,$url,$filtro,$leyenda_filtros,$tmp_filtros;

	global $gid,$uid;
    $gid=$_GET['gid'];
	$tabla=" reporte_contactos_asignados ";

    include_once("regresa_filtros.php");
    $filtro=implode(" AND ",$tmp_filtros);
    include_once ("crea_filtro_vehiculo.php");
    if(count($filtroPorVehiculo) > 0)
    {
        $filtro.=" AND ".implode (" AND ",$filtroPorVehiculo);
    }
    // saco totales de origenes padre, de vendedores y de campanas por el gid
    $array_origenes   = regresa_origenes($db);
    $array_vendedores = regresa_vendedor($db,$_GET['uid']);
    $array_campanas=regresa_campanas($db,$_GET['gid']);
    $name_vendedor=$array_vendedores[$_GET['uid']];

    $sql_contactos="SELECT a.contacto_id, a.prospecto, a.origen_id, a.fecha_importado, a.uid,a.gid,a.name AS concesionaria,a.modelo_id,a.modelo,a.no_reasignaciones,a.fecha_log_ultima,a.fecha_log_primera,a.vendedor
    FROM ".$tabla." a
    WHERE a.gid > 0  AND ".$filtro.";";

    $res_contactos=$db->sql_query($sql_contactos) or die("Error".$sql_contactos);
    if($db->sql_numrows($res_contactos) > 0)
    {
		$tabla_campanas = "<table align='center' class='tablesorter' width='100%'>"
	                  ."<thead>"
					  ."<tr>"
	                  ."<th width='12%'>Campa&ntilde;a</th>"
					  ."<th width='25%'>Nombre</th>"
                      ."<th width='23%'>Vendedor</th>"
					  ."<th width='11%'>Fecha de &Uacute;ltima Reasig</th>"
					  ."<th width='11%'>Fecha de Asignaci&oacute;n</th>"
					  ."<th width='8%'>Total de Reasignaciones</th>"
					  ."<th width='10%'>Modelo</th>"
	                  ."</tr></thead>";
        $counter=0;
        $total_reasignaciones=0;
        while ($fila = $db->sql_fetchrow($res_contactos))
        {
            if($counter==0)
            {
                $name_group=$fila['concesionaria'];

            }
            $total_reasignaciones=$total_reasignaciones + $fila['no_reasignaciones'];
            $tabla_campanas .= "
                <tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;\">"
				."<td>".$array_origenes[$fila['origen_id']]."</td>"
                ."<td align='left'><a href=\"index.php?_module=$_module&_op=llamada_ro&llamada_id=".$array_campanas[$fila['contacto_id']]['id']."&contacto_id=".$fila['contacto_id']."&campana_id=".$array_campanas[$fila['contacto_id']]['campana_id']."\">
				".$fila['prospecto']."</a></td>"
	            ."<td align='left'>".$fila['vendedor']."</td>"
	            ."<td align='center'>".$fila['fecha_log_ultima']."</td>"
				."<td align='center'>".$fila['fecha_log_primera']."</td>"
				."<td align='right'>".$fila['no_reasignaciones']."</td>"
				."<td align='center'>".$fila['modelo']."</td>"
	            ."</tr>";
            $counter++;
        }
        $tabla_campanas .="</tbody><thead>
				<tr>
                <td>&nbsp;</td>
		 		<td>Total $counter</td>
                <td colspan='3'>&nbsp;</td>
                <td align='right'>".$total_reasignaciones."</td><td>&nbsp;</td>
				</tr></thead>
			</table>";
    }
    else
    {
        $tabla_campanas ="<center>No hay registros de la concesionaria:   ".$name_group."</center>";
    }
include_once("templateVehiculo.php");


function formato_fecha($fecha)
{
    $array_tmp=explode(" ",$fecha);
    return substr($array_tmp[0],8,2).'-'.substr($array_tmp[0],5,2).'-'.substr($array_tmp[0],0,4);
}
function brigeFormatDayToDayHour($totaldays)
{
    $hoursForDay = 24;
    $days = "" +  ($totaldays/$hoursForDay);
    list($day,$decimalDay) = explode(".",$days);
    $decimalDay =  ((float)(".".$decimalDay))*$hoursForDay;
    return "$day d ".(int)$decimalDay." h";
}
/**
 *
 * @param $db conexion a la base de adtos
 * @return array con los origenes, usando como llave el origen_id
 */

function regresa_origenes($db)
{
    $tmp_array=array();
    $sql_con="SELECT fuente_id,nombre FROM crm_fuentes ORDER BY fuente_id;";
    $res_con=$db->sql_query($sql_con);
    if($db->sql_numrows($res_con)>0)
    {
        while($fila=$db->sql_fetchrow($res_con))
        {
            $tmp_array[$fila['fuente_id']]=$fila['nombre'];
        }
    }
    return $tmp_array;
}

/**
 *
 * @param $db conexion a la base de datos
 * @param int $gid id de la concesionaria
 * @return array con los nombres de los vendedores, usando como llave el uid del vendedor y solo se saca los de gid
 */
function regresa_vendedor($db,$uid)
{
    $tmp_array=array();
    $sql_con="SELECT DISTINCT a.uid,a.name FROM users a WHERE a.uid=".$uid.";";
    $res_con=$db->sql_query($sql_con);
    if($db->sql_numrows($res_con)>0)
    {
        while($fila=$db->sql_fetchrow($res_con))
        {
            $tmp_array[$fila['uid']]=$fila['name'];
        }
    }
    return $tmp_array;
}

/**
 *
* @param $db conexion a la base de datos
 * @param int $gid id de la concesionaria
 * @return array con los id de la campanas, usando como llave el contacto_id del contacto y solo se saca los de gid
  */
function regresa_campanas($db,$gid)
{
    $gid=$gid + 0 ;
    $tmp_array=array();
    $sql_con="SELECT id, campana_id,contacto_id FROM crm_campanas_llamadas WHERE campana_id LIKE '".$gid."%'ORDER BY contacto_id";
    $res_con=$db->sql_query($sql_con);
    if($db->sql_numrows($res_con)>0)
    {
        while($fila=$db->sql_fetchrow($res_con))
        {
            $tmp_array[$fila['contacto_id']]['id']=$fila['id'];
            $tmp_array[$fila['contacto_id']]['campana_id']=$fila['campana_id'];
        }
    }
    return $tmp_array;
}


?>