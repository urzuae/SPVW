<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


/*
 * ************** METODOS  PARA SEGUMIENTO  *****************/
/*
 * Identifica si un contacto se encuentra en seguimiento o no. Si el contacto
 * esta en seguimiento, le calcula el numero de horas de retraso
 * @param $contacto_id -> el identificador del contacto
 * @return Array -> [0][0]: El contacto no se encuentra en seguimiento. Total de horas retraso=0
 *                  [1][n]: El contacto se encuentra en seguimiento. n es el numero de horas de retraso,
 */
/*
 *
 * Obtiene las horas de retraso de atencion del contacto
 * @param $contact_id -> El id del contacto
 * @param $uid -> el id del vendedor
 * @return -> void
 */
function getHoursDelayAttention($db, $contact_id, $uid,$contador,$tupla)
{
    $sqlEventos = "select eventos.evento_id from  crm_campanas_llamadas as llamadas,
    crm_campanas_llamadas_eventos as eventos  where  llamadas.id=eventos.llamada_id
    and llamadas.contacto_id='$contact_id'  and eventos.uid='$uid'";
    
    $sqlAsignacion = "select asignacion.timestamp from  `crm_contactos_asignacion_log`
    as asignacion  where  asignacion.contacto_id='$contact_id'  and asignacion.to_uid='$uid' order by asignacion.timestamp desc limit 1";
    
    $totalHorasAsginacion = 0;
    $resultSqlAsignacion = $db->sql_query($sqlAsignacion) or die("Error al obtener la fecha de asignacion");
    //si existe fecha de asignacion para este contacto
    if($db->sql_numrows($resultSqlAsignacion) > 0)
    {
        // comprobar si tiene eventos
        $resultSqlEventos = $db->sql_query($sqlEventos) or die("Erro al obtener los eventos del contactos->".$sqlEventos);
        if($db->sql_numrows($resultSqlEventos) == 0)// si tiene eventos entonces no tiene de retardo de atencion
        {
            //el timestamp de la fecha en que se asigno el contacto al vendedor
            list($fechaContactoAsignado) =  $db->sql_fetchrow($db->sql_query($sqlAsignacion));
            $now = date('Y-m-d H:i:s');
            $sql = "SELECT TIMESTAMPDIFF(HOUR,'$fechaContactoAsignado','$now')";//se calcula si existe un retraso
            $resultDiff = $db->sql_query($sql) or die("Error al obtener la difencia en fechas->".$sql);
            list($diffDate) = $db->sql_fetchrow($resultDiff);
            if($diffDate < 0)
            $diffDate = 0;
            $totalHorasAsginacion =  $diffDate;
        }
    }

    $dateNow = date("Y-m-d H:i:s");
    $sqlInsert = " update reporte_contactos_asignados set horas_retraso_atencion='$totalHorasAsginacion',
    timestamp_seguimiento='$dateNow' where contacto_id='$contact_id' and uid='$uid'";
    $db->sql_query($sqlInsert) or die("Error al actualizar las horas de retraso de atencion ->".$sqlInsert);
    
    echo "\n".$contador." / ".$tupla."     Contacto-> $contact_id uid->$uid. Horas de retraso en atencion->".$totalHorasAsginacion."\n";
}

function setFollowAndHoursDelayContact($db, $contactId, $uid,$tupla,$contador2)
{
    $status = getStatusFollowing($db, $contactId, $uid);
    $isFollow = $status[0];
    $totalHours = $status[1];
    $dateNow = date("Y-m-d H:i:s");
    $sql = "update  `reporte_contactos_asignados` as asignados set en_seguimiento='$isFollow',
            horas_retraso='$totalHours', timestamp_seguimiento='$dateNow' where asignados.contacto_id='$contactId'";
    $db->sql_query($sql) or die("Error al actualizar las horas de retraso en compromiso->".$sql);
    echo "\nContador:  ".$contador2." / ".$tupla."          Contacto ->".$contactId." en_Seguimiento->".$isFollow." Horas retraso en Compromiso->".$totalHours;
    return;
}

function getStatusFollowing($db, $contactId, $uid)
{
    //comprobar si el contactos esta en seguimiento o no
    $isSeguimiento = 0;
    $horasRetraso = 0;

    $sql = "select llamada.id as llamada_id, evento.evento_id,evento.fecha_cita,
            evento.timestamp from crm_campanas_llamadas as llamada,
            crm_campanas_llamadas_eventos as evento where llamada.id = evento.llamada_id
            and llamada.contacto_id='$contactId' and evento.uid='$uid' order by evento.fecha_cita desc limit 1";
    
    $result = $db->sql_query($sql) or die("Error al obtener los eventos del contactos->".$sql);
    if($db->sql_numrows($result))//tiene eventos, entonces esta en seguimiento
    {
        $isSeguimiento = 1;
        list($llamadaId, $eventoId, $fechaCita,$timeStamp) = $db->sql_fetchrow($result);
        $sql = "select cierre.cierre_id from `crm_campanas_llamadas_eventos_cierres`
                as cierre, `crm_campanas_llamadas_eventos` as evento where
                cierre.evento_id= evento.evento_id and evento.evento_id='$eventoId'";
        $resultEventClose = $db->sql_query($sql) or die("Error al obtener el cierre_id del contacto->".$sql);
        if($db->sql_numrows($resultEventClose) == 0)//si el evento no esta cerrado
        {
            $horasRetraso = getDelayHours($db, $fechaCita);// obtener las horas de retaso del evento
        }
    }
    
    return array(0 => $isSeguimiento, 1 => $horasRetraso);
}

function getDelayHours($db, $dateEvent)
{
    $now = date('Y-m-d H:i:s');
    $sql = "SELECT TIMESTAMPDIFF(HOUR,'$dateEvent','$now')";//se calcula si existe un retraso
    $result  = $db->sql_query($sql) or die ("Error al obtener las difencia");
    list($diffDate) = $db->sql_fetchrow($db->sql_query($sql));
    if($diffDate < 0)
    $diffDate = 0;
    return $diffDate;
}
/*
 * Se actualiza la tabla reporte_contactos_asignado por dia, periodo. La idea es revisar los logs, obtener los registros del dia
 * y solo calcular las HRA's y HRC's  para ese contacto. Y para el resto solo sumar a partir del timestamp las HRA y HRC
 * segun el caso
 */

function updateReportAssignedContactForDay($db, $startDate, $lastDate)
{
    // Se  obtienen los contactos del dia a los que se han de obtener las horas de retraso en atencion
    $sqlGetContactsHRA = "select distinct(contacto_id), to_uid from crm_contactos_asignacion_log where date(substr(timestamp,1,10))
                          between '$startDate' and '$lastDate' ORDER BY timestamp;";

    $resultGetContactsHRA = $db->sql_query($sqlGetContactsHRA) or die("Error al obtener los contactos para HRA->".$sqlGetContactsHRA);
    $tupla=$db->sql_numrows($resultGetContactsHRA);
    $contador=1;
    while(list($contacto_id, $uid) = $db->sql_fetchrow($resultGetContactsHRA))
    {
        if($uid > 0)
        {
            getHoursDelayAttention($db, $contacto_id, $uid,$contador,$tupla);
            $contador++;
        }
    }
    // se obtenienen los contactos del dia a los que se ha de obtener las horas de retraso en compromiso
    $sqlGetContactsHRC = "select distinct(llamadas.contacto_id), uid from crm_campanas_llamadas as llamadas,
                        crm_campanas_llamadas_eventos as eventos where eventos.llamada_id=llamadas.id and
                        date(substr(eventos.timestamp,1,10)) between '$startDate' and '$lastDate' order by llamadas.contacto_id;";

    $resultGetContactHRC = $db->sql_query($sqlGetContactsHRC) or die("Error al obner los contactos para HRC->".$sqlGetContactsHRC);
    $tupla=$db->sql_numrows($resultGetContactHRC);
    $contador2=0;
    while(list($contactoId, $uid) = $db->sql_fetchrow($resultGetContactHRC))
    {
        if($uid > 0)
        {
            setFollowAndHoursDelayContact($db, $contactoId, $uid,$tupla,$contador2);
            $contador2++;
        }
    }
    
    // Ahora tomar el resto de los contactos, los que no se modificaron ese dia
    //Primero, actualizar aquellos que puedan tener horas de retraso en compromiso
    $sqlUpdateContactIntoHRC = "update reporte_contactos_asignados as reporte set
    reporte.horas_retraso=(select sum(reporte.horas_retraso +
    (select TIMESTAMPDIFF(HOUR,reporte.timestamp_seguimiento,NOW())))),
    timestamp_seguimiento=(select now()) where date(substr(reporte.timestamp_seguimiento,1,10))
    not between '$startDate' and '$lastDate' and reporte.en_seguimiento='1'
    and reporte.horas_retraso > 0  and (select TIMESTAMPDIFF(HOUR,reporte.timestamp_seguimiento,NOW())) > 0";
    

    $db->sql_query($sqlUpdateContactIntoHRC) or
    die("Error al actualizar los contactos con horas de retraso en compromiso->\n".$sqlUpdateContactIntoHRC);
    echo "\nSe han actualizado las horas de retraso en compromiso satisfactoriamente\n";
    
    // Finalmente actualizar las horas de retraso en atencion
    $sqlUpdateContactIntoHRA = " update reporte_contactos_asignados as reporte set
    reporte.horas_retraso_atencion=(select sum(reporte.horas_retraso_atencion + 
    (select TIMESTAMPDIFF(HOUR,reporte.timestamp_seguimiento,NOW())))),
    timestamp_seguimiento=(select now()) where date(substr(reporte.timestamp_seguimiento,1,10))
    not between '$startDate' and '$lastDate' and reporte.en_seguimiento='0'  and
    reporte.horas_retraso_atencion > 0 and (select TIMESTAMPDIFF(HOUR,reporte.timestamp_seguimiento,NOW())) > 0";
    

    $db->sql_query($sqlUpdateContactIntoHRA) or
    die ("Error al actualizar los contactos con horas de retraso en atencion->\n".$sqlUpdateContactIntoHRA);
    echo "\nSe han actualizado las horas de retraso en atencion satisfactoriamente\n\n";
}

?>
