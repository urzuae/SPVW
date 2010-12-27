<?php
/**** Este script sirve para calcular las horas de retraso en atencion (asignacion),
 *    y el numero de reasignaciones de un prospecto, cada que calcula deja el dato de
 *    la fecha, en que fue calculado.
 */

global $db;
$arg = $argv[0];
$fechai=$argv[2];
$fechac=$argv[3];

if(empty($fechac))
$fechac=$fechai;

// cargo los catalogos en array's
$array_origenes = origenes($db);
$array_concesionarias = concesionarias($db);
$array_vendedores = vendedores($db);


// Barro la tabla de reportes
$sql="SELECT gid,uid,contacto_id,fecha_retraso_asig,horas_retraso_asignacion,retraso_asignacion,no_reasignaciones,fecha_log_ultima,fecha_log_anterior
      FROM reporte_contactos_asignados WHERE (substr(fecha_importado,1,10)>='".$fechai."' AND substr(fecha_importado,1,10)<='".$fechac."') ORDER BY fecha_importado;";
echo"\nsql:  ".$sql."\n";
$res=$db->sql_query($sql);
$num=$db->sql_numrows($res);
if( $num > 0)
{
    $contador_retraso=1;
    while($fila = $db->sql_fetchrow($res))
    {
        $array_contactos= revisa_contacto($db,$fila['contacto_id']);
        $no_reasig      = calculo_no_reasignaciones($db,$fila['contacto_id']);
        $array_logs     = calculo_retraso_asignacion($db,$fila['contacto_id'],$array_contactos['uid']);
        if($array_logs['retraso_asignacion'] == 1)
            $contador_retraso++;        
        $result = array_merge((array)$array_contactos, (array)$array_logs);
        actualiza_valores($db,$fila['contacto_id'],$result,$no_reasig);
    }
}
echo"\nTotal de prospectos procesados:  ".$num;
echo"\nTotal con retraso en asignacion:  ".$contador_retraso;



/********************* FUNCIONES AUXILIARES *****************/
function actualiza_valores($db,$contacto_id,$result,$no_reasig)
{
    $upd="UPDATE reporte_contactos_asignados SET no_reasignaciones=".$no_reasig.", fecha_retraso_asig='".$result['fecha_retraso_asig']."', horas_retraso_asignacion=".$result['horas_retraso'].",retraso_asignacion=".$result['retraso_asignacion'].",timestamp='".$result['timestamp']."' WHERE contacto_id=".$contacto_id.";" ;
    echo"\n*******\nContacto:   ".$contacto_id."    Uid Vendedor:  ".$result['uid']." hrs asigna:   ".$result['horas_retraso']."   Reasign:   ".$no_reasig;
    if($db->sql_query($upd))
        echo"\n\nupdate:   ".$upd;
}

function calculo_no_reasignaciones($db,$contacto_id)
{
    $no_reasignaciones=0;
    $sql_tmp="select ( count(contacto_id) - 2 ) as total from crm_contactos_asignacion_log where contacto_id=".$contacto_id.";";
    $res_tmp=$db->sql_query($sql_tmp);
    if($db->sql_numrows($res_tmp) > 0)
    {
        $no_reasignaciones=$db->sql_fetchfield(0,0, $res_tmp);
    }
    if ($no_reasignaciones <= 0 )
        $no_reasignaciones=0;
    return $no_reasignaciones;

}
function calculo_retraso_asignacion($db,$contacto_id,$uid)
{
    $date=date("Y-m-d H:i:s");
    $array['timestamp']=$date;
    if($uid == 0)
    {
        $sql_tmp="select contacto_id,from_uid,to_uid,from_gid,to_gid,timestamp from crm_contactos_asignacion_log where contacto_id=".$contacto_id." order by timestamp desc limit 1;";
        $res_tmp=$db->sql_query($sql_tmp);
        if($db->sql_numrows($res_tmp) > 0)
        {
            while($fila= $db->sql_fetchrow($res_tmp))
            {
                $array['from_uid']= $fila['from_uid'];
                $array['to_uid']= $fila['to_uid'];
                $array['from_gid']= $fila['from_gid'];
                $array['to_gid']= $fila['to_gid'];
                $array['fecha_retraso_asig']= $fila['timestamp'];
            }
        }
        $sql_tiempo="select TIMESTAMPDIFF(HOUR,'".$array['fecha_retraso_asig']."','".$date."') as retrasos;";
        $res_tiempo=$db->sql_query($sql_tiempo);
        $array['horas_retraso']=( ($db->sql_fetchfield(0,0,$res_tiempo)) + 0);
        $array['retraso_asignacion']=1;

    }
    else
    {
        $array['from_uid']= 0;
        $array['to_uid']= 0;
        $array['from_gid']= 0;
        $array['to_gid']=0;
        $array['fecha_retraso_asig']='0000-00-00 00:00:00';
        $array['horas_retraso']=0;
        $array['retraso_asignacion']=0;
    }
    return $array;
}


function revisa_contacto($db,$contacto_id)
{
    $sql_tmp="SELECT uid,gid,origen_id FROM crm_contactos WHERE contacto_id=".$contacto_id.";";
    $res_tmp=$db->sql_query($sql_tmp);
    if($db->sql_numrows($res_tmp) > 0)
    {
        while($fila= $db->sql_fetchrow($res_tmp))
        {
            $array['uid']= $fila['uid'];
            $array['gid']= $fila['gid'];
            $array['origen_id']= $fila['origen_id'];
        }
    }
    return $array;
}

function origenes($db)
{
    $sql_tmp="SELECT padre_id,hijo_id from crm_fuentes_arbol WHERE padre_id > 0 ORDER BY hijo_id;";
    $res_tmp=$db->sql_query($sql_tmp) or die($sql_tmp);
    if($db->sql_numrows($res_tmp) > 0)
    {
        while($rs=$db->sql_fetchrow($res_tmp))
        {
            $array_tmp[$rs['hijo_id']]=$rs['padre_id'];
        }
    }
    return $array_tmp;
}

function vendedores($db)
{
    $sql_tmp="SELECT uid,name from users WHERE super < 10 ORDER BY uid;";
    $res_tmp=$db->sql_query($sql_tmp) or die($sql_tmp);
    if($db->sql_numrows($res_tmp) > 0)
    {
        while($rs=$db->sql_fetchrow($res_tmp))
        $array_tmp[$rs['uid']]=$rs['name'];
    }
    return $array_tmp;
}
function concesionarias($db)
{
    $sql_tmp="SELECT gid,name from groups WHERE gid>0 ORDER BY gid;";
    $res_tmp=$db->sql_query($sql_tmp) or die($sql_tmp);
    if($db->sql_numrows($res_tmp) > 0)
    {
        while($rs=$db->sql_fetchrow($res_tmp))
        {
            $array_tmp[$rs['gid']]=$rs['name'];
        }
    }
    return $array_tmp;
}
?>