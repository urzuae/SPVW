<?
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die("No puedes acceder directamente a este archivo...");
}

global $db, $fecha_ini, $fecha_fin, $gid, $submit;
include_once($_includesdir . "/fusion/FusionCharts.php");
include_once("funciones.php");
if ($submit) {
    if($gid){
        $filtro.=" AND a.gid ='".$gid."'";
    }
    if ($fecha_ini) {
        $fecha_i = substr($fecha_ini, 6, 4) . '-' . substr($fecha_ini, 3, 2) . '-' . substr($fecha_ini, 0, 2);
        $filtro.=" AND c.start >='" . $fecha_i . " 00:01:01'";
    }
    if ($fecha_fin) {
        $fecha_c = substr($fecha_fin, 6, 4) . '-' . substr($fecha_fin, 3, 2) . '-' . substr($fecha_fin, 0, 2);
        $filtro.=" AND c.start <='" . $fecha_c . " 23:59:59'";
    }
    $array_concesionarias = Catalogo_Concesionarias($db);
    $array_niveles    = Catalogo_Niveles($db);
    $array_vendedores = Catalogo_Vendedores($db,$gid);

    $url='index.php?_module=Usuarios&_op=actividades_gid&fecha_ini='.$fecha_ini.'&fecha_fin='.$fecha_fin.'&submit=1&table='.$table;
    $buffer = "<table width='80%' align='center' border='0'>
                <tr>
                <td align='left'>Listado de uso del sistema por concesionaria:   ".$array_concesionarias[$gid]."</td>
                <td align='right'><a href='".$url."'>Regresar</a></td>
                </tr></table><br>";

    $sql="SELECT a.gid,a.uid,count(c.uid) as total FROM `users` as a left join `load` as c
          ON a.uid=c.uid WHERE 1 ". $filtro." GROUP BY a.gid,c.uid ORDER BY a.gid,c.uid;";
    $res = $db->sql_query($sql) or die("Error en la consulta:  " . $sql);
    $num = $db->sql_numrows($res);
    $strXML = '';
    if ($num > 0)
    {
        $strXML .= "<chart caption='Accesos por vendedores' yaxisName='No de accesos' showValues='1' formatNumber='0' formatNumberScale='0' bgColor='BAB9BF,FFFFFF' bgAlpha='50' baseFontSize='8'  rotateValues='1' plotSpacePercent='30' setAdaptiveYMin='0' scrollToEnd='350'>"."\n";
        while (list($_gid, $_uid, $total) = $db->sql_fetchrow($res))
        {
            $_gid=str_pad($_gid, 4, '0', STR_PAD_LEFT);
            $name_ven=Quita_Caracteres($array_vendedores[$_uid]);
            $url = 'index.php?_module=Usuarios%26_op=visualiza_actividades%26gid='.$_gid.'%26uid='.$_uid.'%26fecha_ini='.$fecha_ini.'%26fecha_fin='.$fecha_fin.'%26submit=1';
            $strXML.="<set label='".$_uid."' value='".$total."' showValues='0' FormatNumber='0' formatNumberScale='1'   baseFontSize='8' color='".random_color()."' link='".$url."' toolText='".$name_ven." - ".$total."' />";
        }
        $strXML.= "</chart>";
    }
    $grafico = renderChartHTML($_includesdir."/fusion/Column3D.swf", "", $strXML, "grafico", '95%', 450, false);
}
?>