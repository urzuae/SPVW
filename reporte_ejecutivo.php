<?php

include_once("config.php");
$db= mysql_connect($_dbhost,$_dbuname,$_dbpass);
$base_selection = mysql_select_db($_dbname,$db);

$fecha_i=$_GET['fechai'];
$fecha_c=$_GET['fechac'];
if ( (!empty($fecha_i)) && (!empty($fecha_c)) )
{
    $fecha_i=$fecha_i.' 00:01:01';
    $fecha_c=$fecha_c.' 23:59:59';
}
else
{
    $fecha_i=date('Y-m-d').' 00:01:01';
    $fecha_c=date('Y-m-d').' 23:59:59';
}

echo"<br><center><h3>Periodo de fechas: Del ".$fecha_i." al ".$fecha_c."</h3></center><br>";
$concesionarias=Regresa_Groups($db,$fecha_i,$fecha_c);
$total_vendedores=Calcula_total_vendedores($db,$fecha_i,$fecha_c);
$total_prospectos=Calcula_total_prospectos($db,$fecha_i,$fecha_c);
$total_ventas_con_ven=Calcula_total_ventas_con_vendedores($db,$fecha_i,$fecha_c);
$total_ventas_sin_ven=Calcula_total_ventas_sin_vendedores($db,$fecha_i,$fecha_c);

if(count($concesionarias) > 0)
{
    $buffer="<Table width='100%' align='center' border='1'>
            <tr>
            <td>#</td>
            <td>Concesionaria</td>
            <td>vendedores con actividad en el mes</td>
            <td>%</td>
            <td>Prospectos Registrados</td>
            <td>%</td>
            <td>ventas concretadas</td>
            <td>%</td>
            </tr>";
    $prom_vend=0;
    $prom_pros=0;
    $prom_vent=0;
    $total_prom_vend=0;
    $total_prom_pros=0;
    $total_prom_vent=0;
    $prom_total_vend=0;
    $prom_total_pros=0;
    $prom_total_vent=0;
    foreach($concesionarias as $clave => $valor)
    {
        if($clave!='0')
        {
            $no_vendedores=Calcula_Vendedores($db,$clave,$fecha_i,$fecha_c);
            $no_prospectos=Calcula_Prospectos($db,$clave,$fecha_i,$fecha_c);
            $no_ventas=Calcula_Ventas($db,$clave,$fecha_i,$fecha_c);

            $total_prom_vend= $total_prom_vend + $no_vendedores;
            $total_prom_pros= $total_prom_pros + $no_prospectos;
            $total_prom_vent= $total_prom_vent + $no_ventas;

            if($total_vendedores == 0)
                $prom_vend=0;
            else
                $prom_vend=($no_vendedores *100) / $total_vendedores;

            if($total_prospectos == 0)
                $prom_pros=0;
            else
                $prom_pros=($no_prospectos *100) / $total_prospectos;

            if($total_ventas_con_ven == 0)
                $prom_vent=0;
            else
                $prom_vent=($no_ventas *100) / $total_ventas_con_ven;

            $prom_total_vend= $prom_total_vend + $prom_vend;
            $prom_total_pros= $prom_total_pros + $prom_pros;
            $prom_total_vent= $prom_total_vent + $prom_vent;

            $buffer.="
            <tr>
            <td>$clave</td>
            <td>$valor</td>
            <td>$no_vendedores</td>
            <td>".number_format($prom_vend,2,'.','')."</td>
            <td>$no_prospectos</td>
            <td>".number_format($prom_pros,2,'.','')."</td>
            <td>$no_ventas</td>
            <td>".number_format($prom_vent,2,'.','')."</td>
            </tr>";
        }
    }
    $buffer.="
            <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>$total_prom_vend</td>
            <td>".number_format($prom_total_vend,2,'.','')."</td>
            <td>$total_prom_pros</td>
            <td>".number_format($prom_total_pros,2,'.','')."</td>
            <td>$total_prom_vent</td>
            <td>".number_format($prom_total_vent,2,'.','')."</td>
            </tr></table>";


}

$buffer.="<br><br>Ventas realizadas por vendedores eliminadoas:<br>";
$array=Muestra_ventas_sin_vendedores($db,$fecha_i,$fecha_c);
if(count($array) > 0)
{
    $total=0;
    $buffer.="<Table width='100%' align='center' border='1'>
            <tr>
            <td>#</td>
            <td>Concesionaria</td>
            <td>Ventas sin Vendedor</td>
            </tr>";
    foreach($array as $clave => $valor)
    {
        $total=$total + $valor;
        $buffer.="
            <tr>
            <td>$clave</td>
            <td>$concesionarias[$clave]</td>
            <td>$valor</td>
            </tr>";
    }
    $buffer.="
            <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>$total</td>
            </tr></table>";

}

echo"<br>".$buffer;
echo"<br>PERIODO DE VENTAS:  ".$_GET['fechai']."  AL  ".$_GET['fechac'];
echo"<br>TOTAL DE VENTAS <B>CON</B> VENDEDOR ASIGNADO:   ".$total_prom_vent;
echo"<br>TOTAL DE VENTAS <B>SIN</B> VENDEDOR ASIGNADO:   ".$total;
echo"<br><BR>TOTAL DE VENTAS:  ".($total_prom_vent + $total + 0);
echo"<BR><BR><BR>* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * <br><BR><br>";
include_once("reporte_ejecutivo1.php");





function Calcula_total_vendedores($db,$fecha_i,$fecha_c)
{
    $total=0;
    $sql="SELECT count( * ) FROM users WHERE `last_activity` BETWEEN '".$fecha_i."' AND '".$fecha_c."' AND SUPER = '8' AND gid != 2016;";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        $total=mysql_result($res,0,0);
    }
    return $total;
}
function Calcula_Vendedores($db,$gid,$fecha_i,$fecha_c)
{ 
    $total=0;
    $sql="SELECT u.`gid` , count( u.`uid` )
            FROM `users` AS u
            WHERE `last_activity`
            BETWEEN '".$fecha_i."' AND '".$fecha_c."' AND SUPER = '8' AND u.gid =".$gid." GROUP BY (u.`gid`);";
    
    //$sql="SELECT count(u.uid) FROM users AS u WHERE u.gid=".$gid." AND last_activity BETWEEN '".$fecha_i."' AND '".$fecha_c."' AND SUPER = '8'; ";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        $total=mysql_result($res,0,1);
    }
    return $total;    
}

function Calcula_total_prospectos($db,$fecha_i,$fecha_c)
{
    $total=0;
    //$sql="SELECT COUNT(c.contacto_id) FROM crm_contactos as c, users as u WHERE c.uid=u.uid AND c.fecha_importado BETWEEN '".$fecha_i."' AND '".$fecha_c."';";
    $sql="SELECT COUNT(c.contacto_id) FROM crm_contactos as c WHERE c.fecha_importado BETWEEN '".$fecha_i."' AND '".$fecha_c."' AND c.gid!=2016;";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        $total=mysql_result($res,0,0);
    }
    return $total;
}
function Calcula_Prospectos($db,$gid,$fecha_i,$fecha_c)
{
    $total=0;
    //$sql="SELECT COUNT(c.contacto_id) FROM crm_contactos as c, users as u WHERE c.gid=".$gid." and c.uid=u.uid AND c.fecha_importado BETWEEN '".$fecha_i."' AND '".$fecha_c."';";
    $sql="SELECT COUNT(c.contacto_id) FROM crm_contactos as c WHERE c.gid=".$gid." AND c.fecha_importado BETWEEN '".$fecha_i."' AND '".$fecha_c."';";    
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        $total=mysql_result($res,0,0);
    }
    return $total;
}

function Calcula_total_ventas_con_vendedores($db,$fecha_i,$fecha_c)
{
    $total=0;
    $sql="SELECT count(a.contacto_id)
            FROM crm_prospectos_ventas a LEFT JOIN users b ON a.uid = b.uid
            WHERE timestamp BETWEEN '".$fecha_i."' AND '".$fecha_c."'
            AND b.name IS NOT NULL AND b.gid != 2016";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        $total=mysql_result($res,0,0);
    }
    return $total;
}

function Calcula_total_ventas_sin_vendedores($db,$fecha_i,$fecha_c)
{
    $total=0;
    $sql="SELECT count(a.contacto_id)
            FROM crm_prospectos_ventas a LEFT JOIN users b ON a.uid = b.uid
            WHERE timestamp BETWEEN '".$fecha_i."' AND '".$fecha_c."'
            AND b.name IS NULL AND b.gid != 2016";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        $total=mysql_result($res,0,0);
    }
    return $total;

}
function Muestra_ventas_sin_vendedores($db,$fecha_i,$fecha_c)
{
    $array1=array();
    $array2=array();
    $array=array();
    $sql="CREATE TABLE vtas AS SELECT a.contacto_id
            FROM crm_prospectos_ventas a LEFT JOIN users b on a.uid = b.uid
            WHERE a.timestamp BETWEEN '".$fecha_i."' AND '".$fecha_c."'
            AND b.name IS NULL  ;";
    if(mysql_query($sql,$db))
    {
        $sql="select b.gid,count(b.gid) as total from vtas a, crm_contactos_finalizados b where a.contacto_id=b.contacto_id group by b.gid order by b.gid;";
        $res=mysql_query($sql,$db);
        if(mysql_numrows($res)> 0)
        {
            while($fila = mysql_fetch_assoc($res))
            {
                $array[$fila['gid']+ 0]=$fila['total'];
            }
        }
        $sql="select b.gid,count(b.gid) as total from vtas a, crm_contactos b where a.contacto_id=b.contacto_id group by b.gid order by b.gid;";
        $res=mysql_query($sql,$db);
        if(mysql_numrows($res)> 0)
            {
            while($fila = mysql_fetch_assoc($res))
            {
                $array[$fila['gid']+ 0]=  $array[$fila['gid']] + $fila['total'];
            }
        }
        $sql="DROP TABLE vtas;";
        if(mysql_query($sql))
            echo"<liminado";
    }
    return $array;
}
function Calcula_Ventas_sin_Vendedor($db,$gid,$fecha_i,$fecha_c)
{
    $total=0;
    $sql="SELECT count(a.contacto_id)
            FROM crm_prospectos_ventas a LEFT JOIN users b on a.uid = b.uid
            WHERE a.timestamp BETWEEN '".$fecha_i."' AND '".$fecha_c."'
            AND b.name IS NULL AND b.gid=".$gid.";";
    echo"<br>".$sql;
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        $total=mysql_result($res,0,0);
    }
    return $total;

}
function Calcula_Ventas($db,$gid,$fecha_i,$fecha_c)
{
    $total=0;
    $sql="SELECT count(a.contacto_id)
            FROM crm_prospectos_ventas a LEFT JOIN users b on a.uid = b.uid
            WHERE a.timestamp BETWEEN '".$fecha_i."' AND '".$fecha_c."'
            AND b.name IS NOT NULL AND b.gid=".$gid.";";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        $total=mysql_result($res,0,0);
    }
    return $total;

}

function Regresa_Groups($db,$fecha_i,$fecha_c)
{
    $array=array();
    $vacio='PROSPECTOS NO ASIGNADOS';
    $eliminada='CONCESIONARIA ELIMINADA';
    $sql_c="SELECT DISTINCT a.gid,b.name FROM crm_contactos a left join groups b ON a.gid=b.gid WHERE a.fecha_importado between '".$fecha_i."' AND '".$fecha_c."' AND b.name IS NULL ORDER BY a.gid;";
    $res_c=mysql_query($sql_c,$db);
    if(mysql_num_rows($res_c) > 0)
    {
        while($fila = mysql_fetch_assoc($res_c))
        {
            if($fila['gid'] == 0)
                $array[$fila['gid']+ 0]=$vacio;
            else
                $array[$fila['gid']+ 0]=$eliminada;
        }
    }
    $sql="select gid,name FROM groups where gid>3 AND gid!=2016 ORDER BY gid;";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {        
        while($fila = mysql_fetch_assoc($res))
        {
            $array[$fila['gid']+ 0]=$fila['name'];
        }
    }
    ksort($array);
    return $array;
}
?>
