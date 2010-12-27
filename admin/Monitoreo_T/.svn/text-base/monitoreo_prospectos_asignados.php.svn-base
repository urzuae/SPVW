<?
include_once("genera_excel.php");
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
	global $db,$gid,$uid;
	include_once("regresa_filtros.php");
    $filtro=implode(" AND ",$tmp_filtros);
    include_once ("crea_filtro_vehiculo.php");
    if(count($filtroPorVehiculo) > 0)
    {
        $filtro.=" AND ".implode (" AND ",$filtroPorVehiculo);
    }
    // saco totales de origenes padre, de vendedores y de campanas por el gid
    $array_origenes   = regresa_origenes($db);
    $array_campanas=regresa_campanas($db,$_GET['gid']);
   
    $sql_contactos="SELECT a.contacto_id,a.prospecto,a.origen_id, a.fecha_importado, a.vendedor,a.uid,
    a.name AS concesionaria,a.modelo,a.horas_retraso_asignacion,a.fecha_log_primera,a.fecha_retraso_asig 
    FROM reporte_contactos_asignados a
    WHERE a.gid > 0 AND ".$filtro.";";
    #echo"<br>".$sql_contactos;
    $res_contactos=$db->sql_query($sql_contactos) or die("Error".$sql_contactos);
    if($db->sql_numrows($res_contactos) > 0)
    {
		$tabla_campanas = "<table align='center' class='tablesorter' width='100%'>"
	                  ."<thead>"
					  ."<tr>"
	                  ."<th width='7%'>Campa&ntilde;a</th>"
					  ."<th width='26%'>Nombre</th>"
					  ."<th width='23%'>Vendedor</th>"
					  ."<th>Fecha de importaci&oacute;n</th>"
					  ."<th>Fecha de asignacion</th>"
					  ."<th>Hrs de Retraso</th>"
                      ."<th>Modelo</th>"
	                  ."</tr></thead>";
        $counter=0;
        while ($fila = $db->sql_fetchrow($res_contactos))
        {
            if($counter==0) $name_group=$fila['concesionaria'];
            $retraso=0;
            $tmp_contacto=$fila['contacto_id'];
            $tmp_campana_id=$array_campanas[$tmp_contacto]['campana_id'];
            $tmp_id=$array_campanas[$tmp_contacto]['id'];

            // Revisamos cuando algun prospecto no tenga vendedor asignado y checamos el retrazo
            $tabla_campanas .= "
                <tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;\">"
				."<td>".$array_origenes[$fila['origen_id']]."</td>"
	            ."<td align='left'><a href=\"index.php?_module=Monitoreo_T&_op=llamada_ro&llamada_id=".$tmp_id."&contacto_id=".$tmp_contacto."&campana_id=".$tmp_campana_id."\">
				".$fila['prospecto']."</a></td>"
				."<td align='left'>".$fila['uid']."  ".$fila['vendedor']."</td>"
	            ."<td align='center'>".$fila['fecha_importado']."</td>"
				."<td align='center'>".$fila['fecha_log_primera']."</td>"
                ."<td align='right'>".brigeFormatDayToDayHour ($fila['horas_retraso_asignacion'])."</td>"
                ."<td align='center'>".$fila['modelo']."</td>"
	            ."</tr>";
                $counter++;
        }
        $tabla_campanas .="</tbody><thead>
				<tr>
                <td>&nbsp;</td>
		 		<td>Total $counter</td>
                <td colspan='5'>&nbsp;</td>
				</tr></thead>
			</table>";
    }
    else
    {
        $tabla_campanas ="<center>No hay registros de la concesionaria:   ".$name_group."</center>";
    }
    include_once("templateVehiculo.php");
/*****************  EMPIEZAN FUNCIONES AUXILIARES PARA GENERAR EL REPORTE  ***********************/
/**
 * Funcion que calcula las horas de retraso, esto se calcula, cuando el prospecto no tiene vendedor asignado
 * @param int $db conexion a la base de datos
 * @param int $contacto_id es el id del contacto
 * @return int numero de horas
 */
function calcula_horas_retraso($db,$contacto_id)
{
        $date=date("Y-m-d H:i:s");
		$hrs=0;
		$sql_hrs="select TIMESTAMPDIFF( HOUR , timestamp, '".$date."' ) as num_horas from crm_contactos_asignacion_log where to_uid=0 and from_uid=0 and contacto_id=".$contacto_id.";";
		$res_hrs=$db->sql_query($sql_hrs);
		if($db->sql_numrows($res_hrs) > 0)
		{
			$hrs=$db->sql_fetchfield('num_horas',0,$res_hrs);
		}
		return $hrs;
	}


/**
 * FUNCION QUE CAMBIA EL FORMATO DE LA FECHA, YA QUE VIENE EN FECHA - HORA
 * @param string $fecha formato a�o - mes - dia   hora - min - seg
 * @return string formato dia - mes -a�o
 */
function formato_fecha($fecha)
{
    $array_tmp=explode(" ",$fecha);
    return substr($array_tmp[0],8,2).'-'.substr($array_tmp[0],5,2).'-'.substr($array_tmp[0],0,4);
}

/**
 * Metodo que cambia el numero de horas en formato dias - horas
 * @param int $totalHours numero de horas
 * @return string  con la cadena de dias y horas
 */
function brigeFormatDayToDayHour($totalHours)
{
    $hoursForDay = 24;
    $days = "" +  ($totalHours/$hoursForDay);
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
 * @return array con los id de la campanas, usando como llave el contacto_id del contacto y solo se saca los de gid
  */
function regresa_campanas($db,$gid)
{
    $gid= 0 +$gid;
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