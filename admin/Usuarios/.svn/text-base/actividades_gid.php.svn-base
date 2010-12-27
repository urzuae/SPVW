<?
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die("No puedes acceder directamente a este archivo...");
}

global $db, $fecha_ini, $fecha_fin, $gid, $submit;
include_once($_includesdir . "/fusion/FusionCharts.php");
include_once("funciones.php");
$select_groups = Regresa_Concesionarias($db, $gid);
$select_zonas = Regresa_Zonas($db, $id_zona);
if ($submit) {
    if ($fecha_ini) {
        $fecha_i = substr($fecha_ini, 6, 4) . '-' . substr($fecha_ini, 3, 2) . '-' . substr($fecha_ini, 0, 2);
        $filtro.=" AND c.start >='" . $fecha_i . " 00:01:01'";
    }
    if ($fecha_fin) {
        $fecha_c = substr($fecha_fin, 6, 4) . '-' . substr($fecha_fin, 3, 2) . '-' . substr($fecha_fin, 0, 2);
        $filtro.=" AND c.start <='" . $fecha_c . " 23:59:59'";
    }

    $array_concesionarias = Catalogo_Concesionarias($db);
    $array_niveles = Catalogo_Niveles($db);
    $array_zonas  = Catalogo_Zonas($db);

    $url='index.php?_module=Usuarios&_op=grafic';
    $buffer = "<table width='80%' align='center' border='0'>
                <tr>
                <td align='left'>Listado de uso del sistema por zona regional y concesionaria</td>
                <td align='right'><a href='".$url."'>Regresar</a></td>
                </tr></table><br>";

    $sql="SELECT b.zona_id,a.gid,count(c.uid) as total FROM `users` as a left join `load` as c
          ON a.uid=c.uid LEFT JOIN groups_zonas as b ON a.gid=b.gid WHERE a.gid>0 AND b.zona_id<13 AND b.zona_id IS NOT NULL
          ". $filtro." GROUP BY b.zona_id,a.gid ORDER BY b.zona_id,a.gid;";

    $res = $db->sql_query($sql) or die("Error en la consulta:  " . $sql);
    $num = $db->sql_numrows($res);
    $strXML = '';
    if ($num > 0)
    {
        $strXML .= "<chart caption='Accesos por concesionarias' yaxisName='No de accesos' showValues='1' formatNumber='0' formatNumberScale='0' bgColor='BAB9BF,FFFFFF' bgAlpha='50' baseFontSize='8'  rotateValues='1' plotSpacePercent='30' setAdaptiveYMin='0' scrollToEnd='350'>"."\n";
        $cat = "<categories>"."\n";
        $x_idzona=0;
        $conta_data=0;
        while (list($id_zona,$t_gid, $total) = $db->sql_fetchrow($res))
        {
            $tmp=$t_gid;
            $_gid=str_pad($t_gid, 4, '0', STR_PAD_LEFT);
            $name=Quita_Caracteres($array_concesionarias[$_gid]);
            $url = 'index.php?_module=Usuarios%26_op=actividades_uid%26 gid='.$tmp.'%26fecha_ini='.$fecha_ini.'%26fecha_fin='.$fecha_fin.'%26submit=1';
            if($x_idzona != $id_zona)
            {
                if($conta_data > 0)
                {
                    $val.="</dataset>"."\n";
                }
                $cat.="<category label='' />"."\n";
                $val.= "<dataset seriesName=''>"."\n";
                $conta_data++;
            }
            $x_idzona = $id_zona;
            $val.="<set value='". $total."' showValues='0' FormatNumber='0' formatNumberScale='1'   baseFontSize='8' color='".random_color()."' link='".$url."' toolText='".$tmp."  ".$name." - ".$total."' />"."\n";
        }
        $cat.="</categories>"."\n";
        $strXML.= $cat;
        $strXML.= $val;
        $strXML.= "</dataset></chart>";
    }

    $grafico = renderChartHTML($_includesdir."/fusion/ScrollColumn2D.swf", "", $strXML, "grafico", '95%', 450, false);
}
?>