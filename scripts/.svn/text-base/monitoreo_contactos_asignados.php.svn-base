<?php
/**
 * Script que genera tabla con los siguientes datos de contactos asignados y no asignados
 * Contacto_id, uid, gid, nombre del prospecto, fecha de alta, nombre del vendedor, nombre del vendedor anterior,
 * penultimo movimiento en log, ultimo movimiento en log, fechas de penultimo y ultimo movimientos en logs
 *
 */
define('_IN_MAIN_INDEX', '1');
//chdir('/var/www/vw');
////////////////////////////// INIT ALL ////////////////////////////////////////
require_once("config.php");
require_once("$_includesdir/main.php");

global $db;
$arg = $argv[0];
$fechai=$argv[1];
$fechac=$argv[2];
if(empty($fechac))
$fechac=$fechai;

$tablename_conn="reporte_contactos_asignados";  // Nombre de la tabla temporal
$fecha_inicio=date("Y-m-d H:i:s");

$array_origenes = origenes($db);   
$array_concesionarias = concesionarias($db);
$array_vendedores = vendedores($db);

$a="crm_contactos";
$b="crm_prospectos_unidades";
$c="groups_ubications";
$d="users";
$sql4="SELECT {$a}.contacto_id,{$a}.uid,{$a}.gid,{$a}.origen_id,{$a}.origen2_id,concat({$a}.nombre,' ',{$a}.apellido_paterno,' ',{$a}.apellido_materno) as prospecto,{$a}.fecha_importado,{$b}.modelo,{$b}.modelo_id,{$b}.version_id,{$b}.transmision_id,{$c}.name,{$d}.name as vendedor,{$c}.region_id,{$c}.zona_id,{$c}.entidad_id,{$c}.plaza_id,{$c}.grupo_empresarial_id,{$c}.nivel_id FROM ((({$a} LEFT JOIN {$b} ON {$a}.contacto_id = {$b}.contacto_id ) LEFT JOIN {$c} ON {$a}.gid={$c}.gid) LEFT JOIN {$d} ON {$a}.uid={$d}.uid AND {$d}.super<=8)  WHERE (substr({$a}.fecha_importado,1,10)>='".$fechai."' AND substr({$a}.fecha_importado,1,10)<='".$fechac."');";
#sql4="SELECT {$a}.contacto_id,{$a}.uid,{$a}.gid,{$a}.origen_id,{$a}.origen2_id,concat({$a}.nombre,' ',{$a}.apellido_paterno,' ',{$a}.apellido_materno) as prospecto,{$a}.fecha_importado,{$b}.modelo,{$b}.modelo_id,{$b}.version_id,{$b}.transmision_id,{$c}.name,{$d}.name as vendedor,{$c}.region_id,{$c}.zona_id,{$c}.entidad_id,{$c}.plaza_id,{$c}.grupo_empresarial_id,{$c}.nivel_id FROM ((({$a} LEFT JOIN {$b} ON {$a}.contacto_id = {$b}.contacto_id ) LEFT JOIN {$c} ON {$a}.gid={$c}.gid) LEFT JOIN {$d} ON {$a}.uid={$d}.uid AND {$d}.super=8)  WHERE {$a}.contacto_id=196356;";
#echo"\n".$sql4."\n";
$res_sql4=$db->sql_query($sql4) or die($sql4);
$num_sql4=$db->sql_numrows($res_sql4);
if($num_sql4 > 0)
{
    $contador=1;
    while ($fila=$db->sql_fetchrow($res_sql4))
    {
        $contador++;
        $array_log=revisa_logs($db,$fila['contacto_id'],$fila['uid'],$fila['gid'],$array_vendedores,$array_concesionarias);
        if(revisa_en_tabla($db,$tablename_conn,$fila['contacto_id'],$fila['fecha_importado']) > 0)
        $accion="UPDATE";
        else
        $accion="INSERT";
        actualiza($db,$tablename_conn,$fila,$array_log,$accion,$array_origenes);
        /*
        if($fila["uid"] > 0)//se calcula el tiempo de retraso y si esta en seguimiento solo si se ha asignado a un vendedor
        {
            setFollowAndHoursDelayContact($db, $fila["contacto_id"],$fila["uid"]);
            getHoursDelayAttention($db, $fila["contacto_id"], $fila["uid"]);
        }*/
    }
    echo"\n\nNumero de registros del dia ".substr($fechai,0,10).": ".$num_sql4;
    echo"\nTotal de regitros almacenados: ".$num_sql4."\n";
}
else 
{
    echo"\n\nNo hay registros en la fecha se&ntilde;alada:  ".$fecha."\n\n";
}
echo"\nFecha de inicio del proceso:  ".$fecha_inicio;
$fecha_termino=date("Y-m-d H:i:s");
echo"\nFecha de termino del proceso:  ".$fecha_termino;
echo"\n\n";


/***************** FUNCIONES AUXILIARES   **********************/

/**
 * Funcion que regresa los nombres de los origenes y los guarda en un array, donde la key es el id del origen
 * @param conexion bd $db
  * @return arreglo con los datos del vendedor
 */
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

/**
 * Funcion que regresa los nombres de los vendedores y los guarda en un array, donde la key es el uid
 * @param conexion bd $db
  * @return arreglo con los datos del vendedor
 */
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

/**
 * Funcion que regresa los nombres de la concesionaria
 * @param conexion bd $db
  * @return arreglo con los datos de las concesionarias
 */
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

/**
 * Funcion que se encarga de inserta o actualizar el registro en la tabla generado
 * @param conexion bd $db
 * @param string $tablename_conn,  nombre de la tabla
 * @param int $contacto_id, numero de contacto
 * @return int 1 si es actualizaco, 0 si es creado
 */
function revisa_en_tabla($db,$tablename_conn,$contacto_id)
{
    $reg=0;
    $sql_tmp="SELECT contacto_id,fecha_importado FROM ".$tablename_conn." WHERE contacto_id=".$contacto_id.";";
    $res_tmp=$db->sql_query($sql_tmp);
    $num_tmp=$db->sql_numrows($res_tmp);
    if ($num_tmp > 0)
    $reg=1;
    return $reg;
}

/**
 * Esta funcion actualiza la tabla generada con los datos de los logs y de la region
 *
 * @param conexion bd $db
 * @param nombre de la tabla $tablename_conn
 * @param tupla de la tabla que afecta $fila
 * @param arreglo de los datos del log $array_datos
 */
function actualiza($db,$tablename_conn,$fila,$array_datos,$accion,$array_origenes)
{
    $campos="";
    $valores="";
    $cadena = "";
    $con=0;
    $valor_tmp=$fila['origen_id'];
    $valor_fuente=$array_origenes[$valor_tmp];
    foreach($fila as $clave => $valor)
    {
        $valor=str_replace("'","",$valor);

        if(($con%2)==1)
        {
            $campos.=$clave.",";
            if($clave=='origen2_id')
            {
                $valor=$valor_fuente;
            }
            $valores.="'".$valor."',";
            $cadena.=$clave."='".$valor."',";
        }
        $con++;
    }
    foreach($array_datos as $clave => $valor)
    {
        $valor=str_replace("'","",$valor);
        $campos.=$clave.",";
        $valores.="'".$valor."',";
        $cadena.=$clave."='".$valor."',";
    }
    if($accion=="INSERT")
    {
        $tmp_sql="INSERT INTO ".$tablename_conn." (".substr($campos,0,(strlen($campos) - 1)).") VALUES (".substr($valores,0,(strlen($valores) - 1)).");";
    }
    else
    {
        $tmp_sql="UPDATE ".$tablename_conn." SET ".substr($cadena,0,(strlen($cadena) - 1))." WHERE contacto_id=".$fila['contacto_id']  .";";
    }
    echo"\n".$tmp_sql."\n";
    if($db->sql_query($tmp_sql) or die ("Error en la consulta->".$tmp_sql))
    {
        echo"\n\n".$tmp_sql;
    }
}

/**
 * Funcion que se encarga de sacar los datos del uid y gid de contactos reasignados
 *
 * @param conexion bd $db
 * @param numero de filas afectadas en el log $num_tuplas
 * @param arreglo con los datos del contacto $array_tuplas
 * @return arreglo con los datos del log
 */

function varios_logs($db,$array_tuplas,$num_tuplas,$uid,$array_vendedores,$array_concesionarias)
{
    $fecha=date("Y-m-d H:m:s");
    $no_resig=0;
    $array_datos['from_uid']=0;
    $array_datos['vendedor_anterior']="";
    $array_datos['from_gid']=0;
    $array_datos['concesionaria_anterior']="";
    $array_datos['fecha_retraso_asig']="0000-00-00 00:00:00";
    $array_datos['horas_retraso_asignacion']=0;
    $array_datos['retraso_asignacion']=0;
    $array_datos['fecha_log_ultima']="0000-00-00 00:00:00";
    $array_datos['fecha_log_anterior']="0000-00-00 00:00:00";
    $array_datos['fecha_log_primera'] ="0000-00-00 00:00:00";

    if($num_tuplas > 0)
    {
        $array_datos['fecha_log_ultima']=$array_tuplas[($num_tuplas - 1)]['timestamp'];
        $array_datos['fecha_log_anterior']=$array_tuplas[($num_tuplas - 2)]['timestamp'];
        $array_datos['fecha_log_primera'] =$array_tuplas[0]['timestamp'];
        for($k=0; $k < count($array_tuplas);$k++)
        {
            // Saco el vendedor anterior
            if($array_tuplas[$k]['from_uid'] > 0)
            {
                $array_datos['from_uid']=$array_tuplas[$k]['from_uid'];
                if(array_key_exists($array_tuplas[$k]['from_uid'], $array_vendedores))
                $array_datos['vendedor_anterior']=$array_vendedores[$array_tuplas[$k]['from_uid']];
                else
                echo "\n La clave->".$array_tuplas[$k]['from_uid']." no existe en el arreglo de vendedores \n";
            }
            // Saco la concesionaria anterior
            if($array_tuplas[$k]['to_gid'] > 0)
            {
                $array_datos['from_gid']=$array_tuplas[$k]['to_gid'];
                $array_datos['concesionaria_anterior']=$array_concesionarias[$array_tuplas[$k]['to_gid']];
            }
            // si no tiene vendedor asignado, saco la fecha de retraso
            if($uid==0)
            {
                if($array_tuplas[$k]['to_uid'] == 0)
                $array_datos['fecha_retraso_asig']=$array_tuplas[$k]['timestamp'];
            }
            // calculo las reasignaciones
            if( ($array_tuplas[$k]['from_uid'] > 0) || ($array_tuplas[$k]['to_uid'] > 0))
            {
                $no_resig++;
            }
        }
        // a las reasignaciones le resto 2 por la asignacion de concesionaria y al primer vendedor
        if( ($no_resig - 1) < 1)
        $array_datos['no_reasignaciones']=0;
        else
        $array_datos['no_reasignaciones']=($no_resig - 1);
    }
    // verifico que la fecha de retraso sea valida, si es asi, calculo las horas de diferencia
    if($array_datos['fecha_retraso_asig'] != '0000-00-00 00:00:00')
    {
        $date=date("Y-m-d H:m:s");
        $array_datos['retraso_asignacion']=1;
        $sql_dif="select TIMESTAMPDIFF(HOUR,'".$array_datos['fecha_retraso_asig']."','".$date."') as retrasos;";
        $res_dif=$db->sql_query($sql_dif);
        $diferencia=$db->sql_fetchfield(0,0,$res_dif);
        $diferencia= $diferencia + 0;
        $array_datos['horas_retraso_asignacion']=$diferencia;
    }
    echo"\nfecha de asignaci:    ".$array_datos['fecha_retraso_asig'];
    echo"\nhoras retraso_asig:   ".$array_datos['horas_retraso_asignacion'];
    echo"\nfecha_log_ultima:     ".$array_datos['fecha_log_ultima'];
    echo"\nfecha_log_anterior:   ".$array_datos['fecha_log_anterior'];
    echo"\nfecha_log_primera:    ".$array_datos['fecha_log_primera'];
    return $array_datos;
}



/*
 * Funcion que regresa vacios cuando el contacto no ha sido reasignado
 */

function un_log($db,$array_tuplas,$uid)
{
    $fecha=date("Y-m-d H:m:s");
    $array_datos['from_uid']=0;
    $array_datos['vendedor_anterior']="";
    $array_datos['from_gid']=0;
    $array_datos['concesionaria_anterior']="";
    $array_datos['fecha_retraso_asig']="0000-00-00 00:00:00";
    $array_datos['horas_retraso_asignacion']=0;
    $array_datos['no_reasignaciones']=0;
    $array_datos['fecha_log_ultima']  =$array_tuplas[0]['timestamp'];
    $array_datos['fecha_log_anterior']=$array_tuplas[0]['timestamp'];
    $array_datos['fecha_log_primera'] =$array_tuplas[0]['timestamp'];
    $array_datos['fecha_retraso_asig']="0000-00-00 00:00:00";
    $array_datos['retraso_asignacion']=0;

    // si no tiene vendedor asignado tomo la fecha inicial de log como la fecha de asignacion
    if($uid==0)
    {
        $array_datos['fecha_retraso_asig']=$fecha - $array_tuplas[0]['timestamp'];
        $array_datos['retraso_asignacion']=1;
    }

    // verifico que tenga vendedor anterior
    if( $array_tuplas[0]['from_uid']>0)
    {
        $array_datos['from_uid']=$array_tuplas[0]['from_uid'];
        $array_datos['vendedor_anterior']=$array_vendedores[$array_tuplas[0]['from_uid']];
    }

    // verifico que tenga concesionaria anterior
    if($array_tuplas[0]['to_gid'] > 0)
    {
        $array_datos['from_gid']=$array_tuplas[0]['to_gid'];
        $array_datos['concesionaria_anterior']=$array_concesionarias[$array_tuplas[0]['to_gid']];
    }

    // calculo las horas de retraso
    if($array_datos['fecha_retraso_asig'] != '0000-00-00 00:00:00')
    {
        $date=date("Y-m-d H.m.s");
        $array_datos['retraso_asignacion']=1;
        $sql_dif="select TIMESTAMPDIFF(HOUR,'".$array_datos['fecha_retraso_asig']."','".$date."') as retrasos;";
        $res_dif=$db->sql_query($sql_dif);
        $diferencia=$db->sql_fetchfield(0,0,$red_dif);
        $diferencia= $diferencia + 0;
        $array_datos['horas_retraso_asignacion']=$diferencia;
    }
    return $array_datos;
}

/*
 * Funcion que consulta los logs de un contacto, se consulta hasta 2 logs de cada uno
 */
function revisa_logs($db,$contacto_id,$uid,$gid,$array_vendedores,$array_concesionarias)
{
    $sql_tmp="SELECT contacto_id,uid,from_uid,to_uid,from_gid,to_gid,timestamp from crm_contactos_asignacion_log WHERE contacto_id=".$contacto_id." ORDER BY timestamp ASC;";
    $res_tmp=$db->sql_query($sql_tmp) or die($sql_tmp);
    $num_tuplas=$db->sql_numrows($res_tmp);
    while($tmp=$db->sql_fetchrow($res_tmp))
    {
        $array_tuplas[]=$tmp;
    }
    if($num_tuplas < 2)
    $array_datos=un_log($db,$array_tuplas,$uid);
    else
    $array_datos=varios_logs($db,$array_tuplas,$num_tuplas,$uid,$array_vendedores,$array_concesionarias);
    return $array_datos;
}

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
 * @param $uidt -> el id del vendedor
 * @return -> void
 */
function getHoursDelayAttention($db, $contact_id, $uid)
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
        if($db->sql_numrows($resultSqlEventos) > 0)// si tiene eventos entonces no tiene de retardo de atencion
        $totalHorasAsginacion = 0;
        else // si no existen eventos para este contactos entonces se calcula las horas de retraso en atencion
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
    else
    $totalHorasAsginacion = 0;
    $sqlInsert = " update reporte_contactos_asignados set horas_retraso_atencion='$totalHorasAsginacion'
    where contacto_id='$contact_id' and uid='$uid'";
    $db->sql_query($sqlInsert) or die("Error al actualizar las horas de retraso de atencion ->".$sqlInsert);
    echo "\nContacto-> $contact_id uid->$uid. Horas de retraso en atencion->".$totalHorasAsginacion."\n";
    return;
}

function setFollowAndHoursDelayContact($db, $contactId, $uid)
{
    $status = getStatusFollowing($db, $contactId, $uid);
    $isFollow = $status[0];
    $totalHours = $status[1];
    $sql = "update  `reporte_contactos_asignados` as asignados set en_seguimiento='$isFollow',
            horas_retraso='$totalHours' where asignados.contacto_id='$contactId'";
    $db->sql_query($sql) or die("Error al actualizar las horas de retraso en compromiso->".$sql);
    echo "Contacto ->".$contactId." en_Seguimiento->".$isFollow." Horas retraso en Compromiso->".$totalHours;
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
        if(!$db->sql_numrows($resultEventClose))//si el evento no esta cerrado
        $horasRetraso = getDelayHours($db, $fechaCita);// obtener las horas de retaso del evento

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

?>