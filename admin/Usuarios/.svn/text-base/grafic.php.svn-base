<?

if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die("No puedes acceder directamente a este archivo...");
}
global $db, $fecha_ini, $fecha_fin, $gid, $table,$submit;
include_once($_includesdir . "/fusion/FusionCharts.php");
include_once("funciones.php");
$buffer = '';
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
    $sql="SELECT count(c.uid) as total from users as a LEFT JOIN `load` as c on a.uid=c.uid WHERE a.gid>0 AND c.uid > 0 ".$filtro.";";
    $res = $db->sql_query($sql) or die("Error en la consulta:  " . $sql);
    $num = $db->sql_numrows($res);   
    if ($num > 0)
    {
        $strXML .= "<chart caption='Actividades en el sistema del periodo del ".$fecha_ini."  al  ".$fecha_fin." ' baseFontSize='12' palette='4' decimals='0' enableSmartLabels='1' enableRotation='0' bgColor='99CCFF,FFFFFF' bgAlpha='40,100' bgRatio='0,100' bgAngle='360' showBorder='1' startingAngle='70'>"."\n";
        $cat = "<categories>"."\n";
        $x_idzona=0;
        $conta_data=0;
        while (list($total) = $db->sql_fetchrow($res))
        {
            $url = 'index.php?_module=Usuarios%26_op=actividades_gid%26fecha_ini='.$fecha_ini.'%26fecha_fin='.$fecha_fin.'%26submit=1';
            $strXML.="<set label='Total' value='".$total."' link='".$url."' toolText='Total de Actividades' showValues='0' FormatNumber='0' formatNumberScale='1'/>";
        }
        $strXML.= "</chart>";
    }
    $grafico = renderChartHTML($_includesdir."/fusion/Pie3D.swf", "", $strXML, "grafico", '600', 250, false);
}
?>