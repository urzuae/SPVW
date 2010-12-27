<?php
$array_fuentes=Catalogos('crm_fuentes','fuente_id','nombre');
$array_modelos=Catalogos('crm_unidades','unidad_id','nombre');

echo"
    <table width='100%' align='center' border='0'>
    <tr>
    <td valign='top' align='33%'>".Reporte_Fuente('crm_contactos',$fecha_i,$fecha_c)."</td>
    <td valign='top' align='34%'>".Reporte_Fuente('crm_contactos_finalizados',$fecha_i,$fecha_c)."</td>
    <td valign='top' align='33%'>".Reporte_Modelos($fecha_i,$fecha_c)."</td>
    </tr></table>
    <br>
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    <br>
    <table width='100%' align='center' border='0' bordercolor='#000000' >
    <tr>
    <td colspan='3'>".Reporte_Call_Center($fecha_i,$fecha_c)."</td>
    </tr></table>";
/**
 *Funcion que realiza el desglose de modelos vendidos
 * @param date $fecha_i
 * @param date $fecha_c
 * @return String datos;
 *
 */
function Reporte_Modelos($fecha_i,$fecha_c)
{
    // primeros sacamos los prospectos con venta de la concesionaria 2016, lo hacemos de la tabla de contactos y contactos_finalizados
    $array_c2016=array();
    $sql_1="SELECT a.contacto_id FROM crm_prospectos_ventas AS a LEFT JOIN crm_contactos_finalizados AS b
            ON a.contacto_id = b.contacto_id WHERE a.timestamp
            BETWEEN '".$fecha_i."' AND '".$fecha_c."' AND b.gid =2016";
    $res_1=mysql_query($sql_1);
    if(mysql_num_rows($res_1)>0)
    {
        while(list($contacto_id)=mysql_fetch_array($res_1))
        {
            $array_c2016[]=$contacto_id;
        }
    }
    $sql_2="SELECT a.contacto_id FROM crm_prospectos_ventas AS a LEFT JOIN crm_contactos AS b
            ON a.contacto_id = b.contacto_id WHERE a.timestamp
            BETWEEN '".$fecha_i."' AND '".$fecha_c."' AND b.gid =2016";
    $res_2=mysql_query($sql_2);
    if(mysql_num_rows($res_2)>0)
    {
        while(list($contacto_id)=mysql_fetch_array($res_2))
        {
            $array_c2016[]=$contacto_id;
        }
    }

    $excluir='';
    if(count($array_c2016)>0)
        $excluir=" AND v.contacto_id not in (".implode(',',$array_c2016).")";
   
    $tabla_temporal="m".str_replace('-','',substr($fecha_i,0,10));
    $buffer="No hay registros que reportar";
    $sql="CREATE TABLE  ".$tabla_temporal." AS SELECT v.modelo_id as modelo_id, count(v.modelo_id) as total
          FROM crm_prospectos_ventas AS v
          WHERE v.timestamp BETWEEN '".$fecha_i."' AND '".$fecha_c."' ".$excluir." GROUP BY (v.modelo_id)";
    if(mysql_query($sql))
    {
        $sql="SELECT a.modelo_id,b.nombre,a.total FROM ".$tabla_temporal." as a LEFT JOIN crm_unidades as b ON a.modelo_id=b.unidad_id ORDER BY b.nombre;";
        $res=mysql_query($sql);
        if(mysql_num_rows($res)>0)
        {
            $sum_total=0;
            $buffer="<table width='100% align='center' border='1'>
                     <tr><td colspan='3' align='center'><b>Modelos</b></td></tr>
                     <tr><td>Id</td><td>Nombre</td><td>Total</td></tr>";
            while(list($modelo_id,$nombre,$total)=mysql_fetch_array($res))
            {
                if($nombre=='')
                    $nombre='MODELO ELIMINADO';
                $buffer.="<tr><td>".$modelo_id."</td><td>".$nombre."</td><td>".$total."</td></tr>";
                $sum_total=$sum_total+$total;
            }
            $buffer.="<tr><td colspan='2'>Total</td><td>".$sum_total."</td></tr></table>";
        }
    }
    mysql_query("DROP TABLE IF EXISTS ".$tabla_temporal.";");
    return $buffer;
}
/**
 *Funcion que realiza el desglose de fuentes vendidos
 * @param date $fecha_i
 * @param date $fecha_c
 * @return String datos;
 *
 */

function Reporte_Fuente($table,$fecha_i,$fecha_c)
{

    $tabla_temporal="t".str_replace('-','',substr($fecha_i,0,10));
    $buffer="No hay registros que reportar";
    $sql="CREATE TABLE  ".$tabla_temporal." AS SELECT u.origen_id as origen_id, count(v.contacto_id) as total
          FROM crm_prospectos_ventas AS v, ".$table." AS u
          WHERE v.contacto_id = u.contacto_id AND v.timestamp BETWEEN '".$fecha_i."' AND '".$fecha_c."'
          AND u.gid != 2016 GROUP BY (u.origen_id)";
    if(mysql_query($sql))
    {
        $sql="SELECT a.origen_id,b.nombre,a.total FROM ".$tabla_temporal." as a LEFT JOIN crm_fuentes as b ON a.origen_id=b.fuente_id ORDER BY b.nombre;";
        $res=mysql_query($sql);
        if(mysql_num_rows($res)>0)
        {
            $sum_total=0;
            $buffer="<table width='100% align='center' border='1'>
                     <tr><td colspan='3' align='center'><b>".$table."</b></td></tr>
                     <tr><td>Id</td><td>Nombre</td><td>Total</td></tr>";
            while(list($origen_id,$nombre,$total)=mysql_fetch_array($res))
            {
                if($nombre=='')
                    $nombre='FUENTE ELIMINADA';
                $buffer.="<tr><td>".$origen_id."</td><td>".$nombre."</td><td>".$total."</td></tr>";
                $sum_total=$sum_total+$total;
            }
            $buffer.="<tr><td colspan='2'>Total</td><td>".$sum_total."</td></tr></table>";
        }        
    }
    mysql_query("DROP TABLE IF EXISTS ".$tabla_temporal.";");
    return $buffer;
}

/**
 * Funcion que regresa el listado de prospectos que se enviaron a concesionaria desde call center
 * @param date $fecha_i fecha inicio
 * @param date $fecha_c fecha termino
 * @return string tabla de salida 
 */
function Reporte_Call_Center($fecha_i,$fecha_c)
{
    $buffer='';
    $sql="SELECT * FROM crm_contactos_no_asignados_finalizados
          WHERE timestamp BETWEEN '".$fecha_i."' AND '".$fecha_c."'  AND motivo_fin LIKE '%se asigno%'
          ORDER BY timestamp";
    $res=mysql_query($sql);
    $rows=mysql_num_rows($res);
    if($rows > 0)
    {
        $cols=mysql_num_fields($res);

        $buffer.="<table border='1' align='center' bordercolor='#000000'><tr bgcolor='#cccccc'>";
        for($c=0;$c<$cols;$c++)
        {
            $buffer.="<td>&nbsp;".mysql_field_name($res,$c)."</td>";
        }
        $buffer.="</tr>";
        for($r=0;$r<$rows;$r++)
        {
            $buffer.="<tr>";
            for($c=0;$c<$cols;$c++)
            {
                $buffer.="<td>&nbsp;".mysql_result($res,$r,$c)."</td>";
            }
            $buffer.="</tr>";
        }
        $buffer.="<tr><td colspan='".$cols."'>Total de Prospectos:  ".$rows."</td></tr></table>";
    }
    return $buffer;
}

/**
 * Funcion que regresa el catalogo de la tabla pasada como parametro
 * @param string $tabla nombre de la tabla
 * @param string $id campo para ordebar y key
 * @param string $nombre campo que contiene la descripcion
 * @return array
 */
function Catalogos($tabla,$id,$nombre)
{
    $array=array();
    $sql="SELECT ".$id.",".$nombre." FROM ".$tabla." ORDER BY ".$id.";";
    $res=mysql_query($sql);
    if(mysql_num_rows($res) > 0)
    {
        while(list($xid,$xnombre)= mysql_fetch_array($res))
        {
            $array[$xid]=$xnombre;
        }
    }
    return $array;
}


?>