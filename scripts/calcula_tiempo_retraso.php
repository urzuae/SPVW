<?php
/***
 * Script que sirve para recuperar por fechas los movimientos registrados en el log de contactos
 * Con esto se calcula la hrs de retraso de asignacion, no de reasignaciones y vendedor anterior
 */

global $db;

echo"\n Consultando tabla de logs del periodo: ".$fechai." al ".$fechac." para actualizar registros\n\n";
$sql="SELECT distinct(contacto_id),uid,from_uid,to_uid,from_gid,to_gid,timestamp as fecha_log_ultima FROM crm_contactos_asignacion_log WHERE (substr(timestamp,1,10)>='".$fechai."' AND substr(timestamp,1,10)<='".$fechac."') ORDER BY timestamp DESC;";
$res=$db->sql_query($sql) or die($sql);
$num=$db->sql_numrows($res);
if($num > 0)
{
    while ($fila=$db->sql_fetchrow($res))
    {
        // inicializo posiciones en el arreglo
        $fila['timestamp']=date("Y-m-d H:i:s");
        $fila['retraso_asignacion']=0;
        $fila['vendedor_anterior']='';
        $fila['concesionaria_anterior']='';
        $fila['vendedor']='';
        $fila['name']='';
        echo"\n timestamp:  ".$fila['timestamp'];
        echo" ultimo log:  ".$fila['fecha_log_ultima'];

        // hagos consultas para variar las posiciones en el array
        // calculo las horas de retraso
        $fila['horas_retraso']=calcula_horas_retraso($db,$fila['to_uid'],$fila['timestamp'],$fila['fecha_log_ultima']);
        if( $fila['horas_retraso'] > 0 )   $fila['retraso_asignacion']=1;

        // calculo el numero de reasignaciones
        $fila['no_reasignaciones']=calcula_no_reasignaciones($db,$fila['contacto_id']);
        
        // saco vendedores y concesionarias anteriores
        if($fila['from_uid'] > 0)   $fila['vendedor_anterior']=$array_vendedores[$fila['from_uid']];
        if($fila['from_gid'] > 0)   $fila['concesionaria_anterior']=$array_concesionarias[$fila['from_gid']];
        
        // saco vendedores y concesionarias actuales
        $fila['uid']=$fila['to_uid'] + 0;
        $fila['gid']=$fila['to_gid'] + 0;
        if($fila['to_uid'] > 0)     $fila['vendedor']=$array_vendedores[$fila['to_uid']];
        if($fila['to_gid'] > 0)     $fila['name']=$array_concesionarias[$fila['to_gid']];

       actualiza_contactos($db,$fila);
    }
    echo"\n Numero de prospectos actualizados por movimientos en el log:  ".$num."\n";
    // en los contactos que no toco y que no tienen vendedor asignados le recalculo sus horas,
    // y actualizo su timestamp con la hora actual
    actualiza_horas_en_contactos($db);

}
else
{
    echo"\n En el logs no hay registros del periodo ".$fechai." al ".$fechac.", por favor corriga su fecha.";
}

function actualiza_horas_en_contactos($db)
{
    $date_actual=date('Y-m-d H:i:s');
    $dia=date('Y-m-d');
    echo"\n\n\n\n * * * * * * *   Recalculando contactos no tocados y que no han sido asignados a un vendedor* * * * * * *   \n ";
    $sql="update reporte_contactos_asignados  as a
          SET horas_retraso_asignacion = (select TIMESTAMPDIFF(HOUR,a.fecha_retraso_asig,'".$date_actual."')), a.timestamp='".$date_actual."'  WHERE a.uid = 0 AND substr(a.timestamp,1,10)!='".$dia."' AND a.retraso_asignacion = 1;";
    if($db->sql_query($sql))
    {
        echo"\nsql:  ".$sql;
        echo"\n se ha actualizado la hora de retraso para el proceso de asignacion y para los contactos no procesados";
    }
}

function actualiza_contactos($db,$fila)
{
    $fecha_ultima_logs=regresa_fecha($db,$fila['contacto_id']);
    $condiciones[]=" timestamp='".$fila['timestamp']."',retraso_asignacion=".$fila['retraso_asignacion'].",
                     horas_retraso_asignacion=".$fila['horas_retraso'].",fecha_retraso_asig='".$fila['fecha_log_ultima']."',no_reasignaciones=".$fila['no_reasignaciones'].",
                     fecha_log_anterior='".$fecha_ultima_logs."', fecha_log_ultima='".$fila['fecha_log_ultima']."'";
    if($fila['from_uid'] > 0)
    {
        $condiciones[]=" from_uid=".$fila['from_uid'].", vendedor_anterior='".$fila['vendedor_anterior']."'";
    }
    if($fila['from_gid'] > 0)
    {
        $condiciones[]=" from_gid=".$fila['from_gid'].", concesionaria_anterior='".$fila['concesionaria_anterior']."'";
    }
    $cadena=implode(',',$condiciones);
    $upd="UPDATE reporte_contactos_asignados SET ".$cadena." WHERE contacto_id=".$fila['contacto_id'].";";
    if($db->sql_query($upd))
        echo"\n".$upd;
}


function regresa_fecha($db,$contacto_id)
{
    $regreso="0000-00-00 00:00:00";
    $sql_tmp="select fecha_log_ultima FROM reporte_contactos_asignados WHERE contacto_id=".$contacto_id.";";
    $res_tmp=$db->sql_query($sql_tmp);
    if($db->sql_numrows($res_tmp) > 0)
    {
        $regreso=$db->sql_fetchfield(0, 0,'fecha_log_ultima');
    }
    return $regreso;
}


function calcula_horas_retraso($db,$uid,$date_actual,$fecha_ultimo_log)
{
    $horas_retraso=0;
    if($uid == 0)
    {
        $sql_tiempo="select TIMESTAMPDIFF(HOUR,'".$fecha_ultimo_log."','".$date_actual."') as retrasos;";
        $res_tiempo=$db->sql_query($sql_tiempo);
        $horas_retraso=( ($db->sql_fetchfield(0,0,$res_tiempo)) + 0);
    }
    return $horas_retraso;
}

/**
 * Funcion que calcula el numero de reasignaciones de un contacto
 */
function calcula_no_reasignaciones($db,$contacto_id)
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
?>