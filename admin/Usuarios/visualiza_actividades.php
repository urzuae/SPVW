<?
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die("No puedes acceder directamente a este archivo...");
}

global $db, $fecha_ini, $fecha_fin, $gid, $submit,$uid;
include_once("funciones.php");
if ($submit) {
    if($gid){
        $filtro.=" AND a.gid ='".$gid."'";
    }
    if($gid){
        $filtro.=" AND a.uid ='".$uid."'";
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
    $url='index.php?_module=Usuarios&_op=actividades_uid&gid='.$gid.'&fecha_ini='.$fecha_ini.'&fecha_fin='.$fecha_fin.'&submit=1&table='.$table;
    $buffer = "<table width='80%' align='center' border='0'>
                <tr>
                <td align='left'>Listado de uso del sistema del vendedor:   ".$array_vendedores[$uid]."</td>
                <td align='right'><a href='".$url."'>Regresar</a></td>
                </tr></table><br>";
    $sql="SELECT a.gid,c.uid,c.module,c.op,c.start,c.stop FROM `users` as a left join `load` as c
          ON a.uid=c.uid WHERE 1 ". $filtro." ORDER BY a.gid,c.uid;";
    $res = $db->sql_query($sql) or die("Error en la consulta:  " . $sql);
    $num = $db->sql_numrows($res);
    if ($num > 0)
    {
        $buffer.="<table width='100%' align='center' border='0' class='tablesorter'>
            <thead>
            <tr>
                <th>uid</th><th>Vendedor</th><th>Modulo</th><th>Op</th><th>Fecha/ Hora</th>
            </tr>
            </thead><tbody>";
        $i=0;
        while (list($gid, $uid, $t_module,$t_op,$t_start,$t_stop) = $db->sql_fetchrow($res))
        {
            $gid=str_pad($gid, 4, '0', STR_PAD_LEFT);
            $buffer.="<tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;\">
                <td>".$uid."</td><td>".$array_vendedores[$uid]."</td><td>".$t_module."</td><td>".$t_op."</td>
                <td>".$t_start."</td></tr>";
        }
        $buffer.= "</tbody><thead><tr><th colspan='5'>Total de movimientos:  ".$num."</th></tr></thead></table>";
    }
    else
    {
        $buffer.= "No existe uso del sistema del vendedor con uid: ".$uid."  ";
    }
}
?>