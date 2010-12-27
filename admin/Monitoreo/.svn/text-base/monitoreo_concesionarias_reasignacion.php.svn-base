<?
include_once("genera_excel.php");
if (!defined('_IN_ADMIN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $how_many, $from, $rsort, $orderby, $sql, $result;
global $cant,$_module,$_op,$notassignedsdeassigneds,$notassignedsnews;
global $tmp_filtros,$filtro,$_dbhost,$_dbuname,$_dbpass,$_dbname;
$fecha_i='';
$fecha_c='';
$tmp_origen=0;

include_once("crea_filtro.php");
$filtro='';
$filtro_contacto='';
if( count($tmp_filtros) > 0)
{
    $filtro=" AND ".implode(" AND ",$tmp_filtros);
}
if( count($tmp_filtros_contactos) > 0)
{
    $filtro_contacto=" AND ".implode(" AND ",$tmp_filtros_contactos);
}

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

$result = mysqli_query($linkTodb,"CALL reasignaciones('$tmp_origen','$fecha_i','$fecha_c')");
if (! $result)
{
    echo "error de procedure";
    exit;
}
$i=1;
if(mysqli_num_rows($result) > 0)
{
    $array_reasignaciones=array();
    while (list($gid,$no_contactos,$no_reasignaciones) = mysqli_fetch_array($result,MYSQLI_NUM))
    {
        $gid=str_pad($gid, 4, "0", STR_PAD_LEFT);
        $array_reasignaciones[$gid]['no_cont']=$no_contactos;
        $array_reasignaciones[$gid]['no_reas']=$no_reasignaciones;
    }
}
$array_asignados=genera_asignados($db,$filtro,$filtro_contacto);
$sql="SELECT b.gid,a.name,count(b.gid) as total FROM crm_contactos b LEFT JOIN groups_ubications a ON b.gid=a.gid  WHERE b.gid > 0 ".$filtro." ".$filtro_contacto." GROUP by b.gid ORDER BY b.gid;";
$result = $db->sql_query($sql) or die("Error".print_r($db->sql_error()));
if($db->sql_numrows($result)> 0)
{
    $tabla_campanas .= "
        <table align='center' class='tablesorter'>
		<thead><tr>
		<th width='5%'>#</th>
		<th width='42%'>Concesionaria</th>
		<th width='8%'>No Prospectos</th>
		<th width='8%'>Prospectos Asignados</th>
        <th width='8%'>Porcentaje Asignaci&oacute;n</th>
		<th>No. de Prospectos Reasignados</th>
		<th>No. de Reasignaciones</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		</tr>
		</thead>";
		$total_fin=0;
		$asignado_fin=0;
        $prosp_total=0;
        $no_contactos=0;
        $no_rasignaciones=0;
        $total_contactos=0;
        $total_reasignaciones=0;
        while (list($gid, $name, $total) = $db->sql_fetchrow($result))
        {
            $gid=str_pad($gid, 4, "0", STR_PAD_LEFT);
            if($total==0)
                $porcentaje_asignados=0 + ( ($array_asignados[$gid] * 100) /1);
            else
                $porcentaje_asignados=0 + ( ($array_asignados[$gid] * 100) /$total);

            $porcentaje_asignados= number_format($porcentaje_asignados, 2, '.', ' ');
            $no_contactos=$array_reasignaciones[$gid]['no_cont'] + 0;
            $no_reasignaciones=$array_reasignaciones[$gid]['no_reas'] + 0;
            $tabla_campanas .= "<tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;\">"
                            ."<td>$gid</td>"
                            ."<td align='left'>$name</td>"
							."<td align='right'>$total</td>"
                            ."<td align='right'>".($array_asignados[$gid] + 0)."</td>"
							."<td align='right'>$porcentaje_asignados</td>"
                            ."<td align='right'>".$no_contactos."</td>"
							."<td align='right'>".$no_reasignaciones."</td>"
                            ."<td align='center'><a href='index.php?_module=Monitoreo&_op=monitoreo_vendedor_concesionaria_reasignados&gid=$gid$url'>Vendedores</a></td>"
							."<td align='center'><a href='index.php?_module=Monitoreo&_op=monitoreo_prospectos_reasignados&gid=$gid$url'>Prospectos</a></td>"
							."</tr>";
            $total_fin      = 0 + $total_fin + $total;
            $total_contactos= $total_contactos + $no_contactos;
            $total_reasignaciones= $total_reasignaciones +$no_reasignaciones;
			$asignado_fin  = 0 +$asignado_fin + $array_asignados[$gid];
        }
        if($total_fin == 0)
            $porcentaje_total=(($asignado_fin/1) *100);
        else
            $porcentaje_total=(($asignado_fin/$total_fin) *100);
		$tabla_campanas .= "<thead><tr>
							<td>&nbsp;</td>
							<td align='right'>Totales:</td>
							<td align='right'>$total_fin</td>
							<td align='right'>$asignado_fin</td>
							<td align='right'>".number_format ($porcentaje_total, 2, '.', ' ')."</td>
							<td align='right'>$total_contactos</td>
							<td align='right'>$total_reasignaciones</td>
							<td align='right'></td>
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

/**|
 * Funcion que sirve para sacar el total de asignados y con uid distinto de cero
 */
function genera_asignados($db,$filtro,$filtro_contacto)
{
    $sql_asignados = "SELECT a.gid,count(b.gid) as totales FROM groups_ubications a, crm_contactos b WHERE a.gid>0 and a.gid=b.gid AND b.uid != 0 ".$filtro." ".$filtro_contacto." GROUP by a.gid ORDER BY a.gid";
	$res_asignados = $db->sql_query($sql_asignados) or die("Error".print_r($db->sql_error()));
	if($db->sql_numrows($res_asignados) > 0)
	{
		while($fila=$db->sql_fetchrow($res_asignados))
		{
            $tmp_gid=str_pad($fila['gid'], 4, "0", STR_PAD_LEFT);
			$array_asignados[$tmp_gid]=$fila['totales'];
		}
	}
	return $array_asignados;
}
?>