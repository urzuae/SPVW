<?php
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die("No puedes acceder directamente a este archivo...");
}
$fecha_mes_i="2010-10-01";
$fecha_mes_f="2010-10-30";
global $_admin_menu2,$db;
include_once("menu_derecho.php");
/*$sql="SELECT fuente_id,nombre,timestamp,active,fecha_inicial,fecha_final FROM crm_fuentes WHERE timestamp between '".$fecha_mes_i."' AND '".$fecha_mes_f."';";
$res=$db->sql_query($sql) or die ("Error en la consulta:   ".$sql);
if($db->sql_numrows($res)>0)
{
    $crm_fuentes="
        <table width='100%' align='center' class='tablesorter'>
        <thead><tr><th>Nombre de la Fuente</th>
                    <th>Creada por la concesionaria</th>
                    <th>Fecha de Creación</th>
                    <th>Periodo de Validez</th>
                    <th>Validar</th>
        </tr></thead>";
    while(list($fuente_id,$nombre,$timestamp,$active,$fecha_inicial,$fecha_final)= $db->sql_fetchrow($res))
    {
        $crm_fuentes.="
            <tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;\">
            <td>".$nombre."</td>
            <td>2016 - Concesionaria de Prueba</td>
            <td>".$timestamp."</td>
            <td>".$fecha_inicial."&nbsp;&nbsp;&nbsp;&nbsp;<b>al</b>&nbsp;&nbsp;&nbsp;&nbsp;".$fecha_final."</td>
            <td><input type='checkbox' name='validar'>Validar</td>
             </tr>";
    }
    $crm_fuentes.="</tbody><thead><tr><td colspan='5'>Total de concesionarias: ".$db->sql_numrows($res)."</td></tr></thead></table>";

}*/
?>