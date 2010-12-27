<?
  if (!defined('_IN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $uid,$fecha_ini,$fecha_fin,$id_conce,$id_fuente,$submit;
//obtener gerente de que zona es
$filtro='';
$sql = "SELECT zona_id FROM crm_zonas_gerentes WHERE uid='$uid'";
$r = $db->sql_query($sql) or die("<br>Error".$db->sql_error());
list($zona_id) = $db->sql_fetchrow($r);
$select_fuentes=Regresa_Fuentes($db,$id_fuente);
$select_concesionarias=Regresa_Concesionarias($db,$zona_id,$id_conce);
if($zona_id > 0)
    $filtro=" AND a.zona_id='$zona_id' ";

//obtener concesionarias que administra
if($submit)
{
    $contador=0;
    $total_pros=0;
    $total_asig=0;
    $filtro_zona=' ';
    $filtro=' AND a.zona_id<13';
    $array_zonas=Regresa_Zonas($db);
    if($id_conce)
    {
        $filtro.=" AND a.gid='".$id_conce."' ";
        $filtro_zona.=" AND c.gid='".$id_conce."' ";
    }
    if($id_fuente)  $filtro_zona.=" AND c.origen_id='".$id_fuente."' ";
    if(($fecha_ini) && ($fecha_fin)) $filtro_zona.=" AND c.fecha_importado BETWEEN '".$fecha_ini." 00:01:01'  AND  '".$fecha_fin." 23:59:59' ";

    $array_name_groups=Regresa_Grupos($db);
    $array_contactos=Regresa_no_contactos($db,1,0,$filtro_zona);
    $array_contactos_asignados=Regresa_no_contactos($db,1,1,$filtro_zona);
    $array_contactos_finalizados=Regresa_no_contactos($db,2,0,$filtro_zona);
    $array_mostrados=$array_contactos;    //array_keys($array_contactos);

    $sql = "SELECT a.zona_id,a.gid,b.name FROM groups_zonas as a LEFT JOIN groups AS b ON a.gid=b.gid
            WHERE b.name IS NOT NULL ".$filtro." ORDER BY a.zona_id,a.gid,b.name;";
   
    $r = $db->sql_query($sql) or die("Error: en la consulta:  ".$sql);
    while (list($zona_id,$gid,$nombre) = $db->sql_fetchrow($r))    
    {
        $gid=str_pad($gid,4,'0',STR_PAD_LEFT);
        if($gid != '2016')
        {
            $total_pros = $total_pros + $array_contactos[$gid];
            $total_asig = $total_asig + $array_contactos_asignados[$gid];
            $total_fin  = $total_fin  + $array_contactos_finalizados[$gid];
            $total      = $total      + $array_contactos[$gid] + $array_contactos_finalizados[$gid];
            $tabla_campanas .= "
            <tr class=\"row".(++$rowclass%2?"2":"1")."\" style=\"cursor:pointer;\" onclick=\"location.href='index.php?_module=$_module&_op=monitoreo_vendedores&gid=$gid';\">"
            ."<td>".$array_zonas[$zona_id]."</td>"
            ."<td>".$gid."</td>"
            ."<td>".$nombre."</td>"
            ."<td align='right'>".($array_contactos_asignados[$gid] + 0)."</td>"
            ."<td align='right'>".($array_contactos[$gid] + 0)."</td>"
            ."<td align='right'>".($array_contactos_finalizados[$gid] + 0)."</td>"
            ."<td align='right'>".($array_contactos[$gid] + $array_contactos_finalizados[$gid] + 0)."</td>"
            ."</tr>";
            $contador++;
            if(array_key_exists($gid,$array_mostrados))
            {
                $array_mostrados[$gid]=-1;
            }
        }
    }
    $tabla_campanas = "<table class='tablesorter' border=\"0\" width=\"100%\">
                    <thead><tr>
                        <th width='11%'>Zona</th>
                        <th colspan='2' width='55%'>Concesionaria</th>
                        <th width='9%'>Asignados</th>
                        <th width='9%'>Prospectos</th>
                        <th width='9%'>Finalizados</th>
                        <th width='9%'>Total de Prospectos</th>
                    </tr></thead>
                    <tbody>" .$tabla_campanas."</tbody>
                    <thead><tr>
                    <td colspan='3'>Total de Concesionarias: ".$contador."</td>
                    <td align='right'>".$total_asig."</td>
                    <td align='right'>".$total_pros."</td>
                    <td align='right'>".$total_fin."</td>
                    <td align='right'>".$total."</td>
                    </tr></thead></table>";

    if(($zona_id == 0) && (count($array_mostrados) > 0) && ($id_conce == 0))
    {
        $tabla_campanas.= "
        <p><h1>Listado de concesionarias que no estan asignadas a ninguna zona, pero que cuentan con prospectos</h1></p><br>
        <table class='tablesorter' border=\"0\" width=\"100%\">
                    <thead><tr>
                        <th width='11%'>Zona</th>
                        <th colspan='2' width='55%'>Concesionaria</th>
                        <th width='9%'>Asignados</th>
                        <th width='9%'>Prospectos</th>
                        <th width='9%'>Finalizados</th>
                        <th width='9%'>Total de Prospectos</th>
                    </tr></thead>
                    <tbody>";
        $i=0;
        $total_pros_sz=0;
        $total_asig_sz=0;
        $total_fin_sz=0;
        $total_sz=0;
        foreach($array_mostrados as $xgid => $no_prospectos)
        {
            $xgid=str_pad($xgid,4,'0',STR_PAD_LEFT);
            if($xgid != 2016)
            {
                if($no_prospectos>=0)
                {
                    $total_pros_sz = $total_pros_sz + $array_contactos[$xgid];
                    $total_asig_sz = $total_asig_sz + $array_contactos_asignados[$xgid];
                    $total_fin_sz  = $total_fin_sz  + $array_contactos_finalizados[$xgid];
                    $total_sz      = $total_sz      + $array_contactos[$xgid] + $array_contactos_finalizados[$xgid];
                    $tabla_campanas.= "<tr class=\"row".(++$rowclass%2?"2":"1")."\" style=\"cursor:pointer;\" onclick=\"location.href='index.php?_module=$_module&_op=monitoreo_vendedores&gid=$gid';\">"
                    ."<td>Sin Zona</td>"
                    ."<td>".$xgid."</td>"
                    ."<td>".$array_name_groups[$xgid]."</td>"
                    ."<td align='right'>".($array_contactos_asignados[$xgid] + 0)."</td>"
                    ."<td align='right'>".($array_contactos[$xgid] + 0)."</td>"
                    ."<td align='right'>".($array_contactos_finalizados[$xgid] + 0)."</td>"
                    ."<td align='right'>".($array_contactos[$xgid] + $array_contactos_finalizados[$xgid] + 0)."</td>"
                    ."</tr>";
                    $i++;
                }
            }
        }
        $tabla_campanas.="</tbody>
                    <thead><tr>
                    <td colspan='3'>Total de Concesionarias: ".$i."</td>
                    <td align='right'>".$total_asig_sz."</td>
                    <td align='right'>".$total_pros_sz."</td>
                    <td align='right'>".$total_fin_sz."</td>
                    <td align='right'>".$total_sz."</td>
                    </tr></thead></table><br>";

        $tabla_campanas.="<table border=\"0\" width=\"100%\">
                    <thead><tr>
                        <th width='11%'>&nbsp;</th>
                        <th colspan='2' width='55%'>N&uacute;mero de Concesionarias (total): ".($contador + $i)."</th>
                        <th align='right' width='9%'>".($total_asig + $total_asig_sz)."</th>
                        <th align='right' width='9%'>".($total_pros + $total_pros_sz)."</th>
                        <th align='right' width='9%'>".($total_fin + $total_fin_sz)."</th>
                        <th align='right' width='9%'>".($total + $total_sz)."</th>
                    </tr></thead>
                    </table>";


    }
}
/******* FUNCIONES AUXILIARES   *******/

function Regresa_no_contactos($db,$opc,$asignados,$filtro_zona)
{
    $array=array();
    $filtro='';
    $tabla=' crm_contactos ';
    if($opc == 2)
        $tabla=' crm_contactos_finalizados ';
    if($asignados > 0)
        $filtro=' AND uid > 0 ';

    $sql="SELECT c.gid,count(distinct(c.contacto_id)) FROM ".$tabla." as c WHERE c.gid>0 ".$filtro_zona." ".$filtro." GROUP BY c.gid ORDER BY c.gid;";
    $res=$db->sql_query($sql) or die ("Error:  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$total) = $db->sql_fetchrow($res))
        {
            $id=str_pad($id,4,'0',STR_PAD_LEFT);
            $array[$id]=($total + 0);
        }
    }
    return $array;
}
function Regresa_Fuentes($db,$id_fuente)
{
    $select='';
    $sql="SELECT fuente_id,nombre FROM crm_fuentes WHERE fuente_id != 0 ORDER BY fuente_id DESC;";
    $res=$db->sql_query($sql) or die ("Error:  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        $select="<select name='id_fuente' id='id_fuente'>
                    <option value='0'>Todas las fuentes</option>";
        while(list($id,$name) = $db->sql_fetchrow($res))
        {
            $tmp='';
            if($id == $id_fuente)
                $tmp=' SELECTED ';
            $select.="<option value='".$id."' ".$tmp.">".$name."</option>";
        }
        $select.="</select>";
    }
    return $select;
}
function Regresa_Concesionarias($db,$zona_id,$id_conce)
{
    $filtro='';
    if($zona_id > 0)
        $filtro=" AND a.zona_id=".$zona_id." ";
    $select='';
    $sql="SELECT a.zona_id,a.gid,b.name FROM groups_zonas as a LEFT JOIN groups AS b ON a.gid=b.gid
          WHERE b.name IS NOT NULL ".$filtro." ORDER BY a.zona_id,a.gid;";
    $res=$db->sql_query($sql) or die ("Error:  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        $select="<select name='id_conce' id='id_conce'>
                    <option value='0'>Todas las concesionarias</option>";
        while(list($id_zona,$id,$name) = $db->sql_fetchrow($res))
        {
            $tmp='';
            if($id == $id_conce)    $tmp=' SELECTED ';
            if($zona_id == 0)
                $select.="<option value='".$id."' ".$tmp.">".$id_zona." - ".$id." - ".$name."</option>";
            else
                $select.="<option value='".$id."' ".$tmp.">".$id." - ".$name."</option>";
        }
        $select.="</select>";
    }
    return $select;
}

function Regresa_Zonas($db)
{
    $array=array();
    $sql="SELECT  zona_id,nombre FROM crm_zonas ORDER BY zona_id;";
    $res=$db->sql_query($sql) or die ("Error:  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$name) = $db->sql_fetchrow($res))
        {
            $array[$id]=$name;
        }
    }
    return $array;
}
function Regresa_Grupos($db)
{
    $array=array();
    $sql="SELECT  gid,name FROM groups ORDER BY gid;";
    $res=$db->sql_query($sql) or die ("Error:  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$name) = $db->sql_fetchrow($res))
        {
            $array[$id]=$name;
        }
    }
    return $array;

}
?>