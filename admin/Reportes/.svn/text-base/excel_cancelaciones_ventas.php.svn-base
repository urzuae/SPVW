<?php
    $table="No existen ventas registradas";
    include_once("Reportes/genera_excel.php");
    $total=0;
    $sql = "SELECT u.gid, u.name, count(v.uid) FROM `crm_prospectos_cancelaciones` AS v, users AS u where v.uid=u.uid $and_fecha group by (v.uid)";

    $result = $db->sql_query($sql) or die("Error: ".$sql);
    if ($db->sql_numrows($result) > 0)
    {
        $table="
            <table width='90%' align='center' border='0'>
            <tbody>
                <tr><td colspan='3' align='center'>Cantidad de Cancelaciones de Ventas por Vendedor/<td></tr>
            </tbody>
            <thead>
            <tr bgcolor='#cdcdcd'>
                <th align='center'>Id Concesionaria</th>
                <th align='center'>Nombre del Vendedor</th>
                <th align='center'>Total de cancelaciones</th>
            </tr>
            </thead><tbody>";
        while(list($id_concesionaria, $nombre_vendedor, $ventas_concretadas) = $db->sql_fetchrow($result))
        {
            $table.="
                <tr>
                    <td align='left'>".$id_concesionaria."</td>
                    <td align='left'>".$nombre_vendedor."</td>
                    <td align='rigth'>".$ventas_concretadas."</td>
                </tr>";
            $total=$total + $ventas_concretadas;
        }
        $table.="</tbody>
            <thead><tr bgcolor='#cdcdcd'><th colspan='2' align='right'>Total de Venta Canceladas:</th><th align='rigth'>".$total."</th></tr></thead></table>";
        $objeto = new Genera_Excel($table,'Ventas_Canceladas_x_vendedor');
        $url=$objeto->Obten_href();
        header('location: '.$url);
    }
?>
