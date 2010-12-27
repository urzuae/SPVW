<?
    include_once("genera_excel.php");
	if (!defined('_IN_ADMIN_MAIN_INDEX')) 
	{
	    die ("No puedes acceder directamente a este archivo...");
	}

	global $db, $campana_id, $nombre, $apellido_paterno, $apellido_materno, 
    $submit, $status_id, $ciclo_de_venta_id, $uid,$gid,$rsort, $open,$orderby,$uid_,$_module,
    $nsort,$_op,$url,$filtro,$leyenda_filtros,$tmp_filtros;       
	global $gid,$uid,$_dbhost,$_dbuname,$_dbpass,$_dbname;
    $fecha_i='';
    $fecha_c='';
    $tmp_origen=0;
	$tabla=" crm_contactos ";
    include_once("regresa_filtros.php");
    include_once ("crea_filtro_vehiculo.php");
    $filtro='';
    $filtro_contacto='';
    $filtro_vehiculo='';
    if( count($tmp_filtros) > 0)
    {
        $filtro=" AND ".implode(" AND ",$tmp_filtros);
    }
    if( count($tmp_filtros_contactos) > 0)
    {
        $filtro_contacto=" AND ".implode(" AND ",$tmp_filtros_contactos);
    }
    if( count($filtroPorVehiculo) > 0)
    {
        $filtro_vehiculo=" AND ".implode(" AND ",$filtroPorVehiculo)." ";
    }

    // saco totales de origenes padre, de vendedores y de campanas por el gid
    $array_origenes   = regresa_origenes($db);
    $array_vendedores = regresa_vendedor($db,$gid,$uid);
    $array_campanas=regresa_campanas($db,$gid);
    $name_vendedor=$array_vendedores[$uid];


    /*** prepara la conexion para mandar llamar al store procedure ***/
    $hoy=date('Y-m-d H:i:s');
    $linkTodb = mysqli_connect($_dbhost,$_dbuname,$_dbpass);
    if (mysqli_connect_errno())
    {
        echo "Error de Conexion";
        exit();
    }
    $conn = mysqli_select_db ($linkTodb,$_dbname);
    if (! $conn)
    {
        echo "Error de Base de Datos";
    }
    $result = mysqli_query($linkTodb,"CALL reasignaciones_vendedores_prospectos('$gid','$uid','$tmp_origen','$fecha_i','$fecha_c')");
    if (! $result)
    {
        echo "error de procedure";
        exit;
    }
    $i=1;
    if(mysqli_num_rows($result) > 0)
    {
        $array_reasignaciones=array();
        while (list($id_contacto,$no_reasignaciones,$maximo,$minimo) = mysqli_fetch_array($result,MYSQLI_NUM))
        {
            $gid=str_pad($gid, 4, "0", STR_PAD_LEFT);
            $array_reasignaciones[$id_contacto]['maximo']=$maximo;
            $array_reasignaciones[$id_contacto]['minimo']=$minimo;
            $array_reasignaciones[$id_contacto]['reasig']=$no_reasignaciones;
        }
    }
    
    $sql_contactos="SELECT b.contacto_id, concat(b.nombre, ' ', b.apellido_paterno, ' ', b.apellido_materno ) AS prospecto, 
                    b.origen_id, b.fecha_importado,m.modelo_id,m.modelo
                    FROM crm_contactos b,groups_ubications a, crm_prospectos_unidades m
                    WHERE b.gid=a.gid AND b.uid=".$uid." AND b.contacto_id=m.contacto_id ".$filtro." ".$filtro_contacto." ".$filtro_vehiculo.";";
    $res_contactos=$db->sql_query($sql_contactos) or die("Error".$sql_contactos);
    if($db->sql_numrows($res_contactos) > 0)
    {
		$tabla_campanas = "<table align='center' class='tablesorter' width='100%'>"
	                  ."<thead>"
					  ."<tr>"
	                  ."<th width='15%'>Campa&ntilde;a</th>"
					  ."<th width='35%'>Nombre</th>"
					  ."<th width='15%'>Fecha de &Uacute;ltima Reasig</th>"
					  ."<th width='15%'>Fecha de Asignaci&oacute;n</th>"
					  ."<th width='10%'>Total de Reasignaciones</th>"
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
            $contacto_id=$fila['contacto_id'];
            $num_reasig=$array_reasignaciones[$contacto_id]['reasig'];
            if($num_reasig < 0)
                $num_reasig=0;

            $total_reasignaciones= $total_reasignaciones + $num_reasig;
            $tabla_campanas .= "
                <tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;\">"
				."<td>".$array_origenes[$fila['origen_id']]."</td>"
                ."<td align='left'><a href=\"index.php?_module=$_module&_op=llamada_ro&llamada_id=".$array_campanas[$fila['contacto_id']]['id']."&contacto_id=".$fila['contacto_id']."&campana_id=".$array_campanas[$fila['contacto_id']]['campana_id']."\">
				".$fila['prospecto']."</a></td>"
	            ."<td align='center'>".$array_reasignaciones[$contacto_id]['maximo']."</td>"
				."<td align='center'>".$array_reasignaciones[$contacto_id]['minimo']."</td>"
				."<td align='right '>".$num_reasig."</td>"
				."<td align='center'>".$fila['modelo']."</td>"
	            ."</tr>";
                $counter++;
        }
        $tabla_campanas .="</tbody><thead>
				<tr>
                <td>&nbsp;</td>
		 		<td>Total de prospectos asignados $counter</td>
                <td colspan='2'>&nbsp;</td>
                <td>".$total_reasignaciones."</td><td>&nbsp;</td>
				</tr></thead>
			</table>";
    }
    else
    {
        $tabla_campanas ="<center>No hay registros de la concesionaria:   ".$name_group."</center>";
    }
include_once("templateVehiculo.php");

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
function regresa_vendedor($db,$gid,$uid)
{
    $tmp_array=array();
    $sql_con="SELECT uid,name FROM users  WHERE gid=".$gid." and uid=".$uid.";";
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
    $gid= 0 + $gid;
    $tmp_array=array();
    $sql_con="SELECT id, campana_id,contacto_id FROM crm_campanas_llamadas WHERE campana_id LIKE '".$gid."%'ORDER BY contacto_id";
    //$sql_con="SELECT id,campana_id FROM crm_campanas_llamadas WHERE contacto_id=".$contacto_id." limit 1;";
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