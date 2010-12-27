<?php

    // incluyo el archivo con las claves para la conexion a la BD
    include_once("../config.php");

    // Realizo la conexion
    $conn=mysql_connect($_dbhost,$_dbuname,$_dbpass);
    $red_db=mysql_select_db($_dbname,$conn);
    $url='';
    $regresa='';
    $mensaje='';
    $url='';
    $sql="SELECT gid,uid,super FROM users WHERE user='".$_REQUEST['user']."' AND password like '%".$_REQUEST['password']."%';";
    $res=mysql_query($sql,$conn) or die("Error al consultar:  ".$sql);
    if(mysql_num_rows($res) > 0)
    {
        list($gid,$uid,$super)=mysql_fetch_array($res);
        if($super == 6)
        {
            if( ($gid > 0) && ($uid > 0))
            {
                $regresa=Regresa_Prospectos_Sin_Asignacion($conn,$gid,0);
            }
            if($regresa == 0)
            {
                $mensaje='Sin prospectos para asignar';
            }
            if($regresa > 0)
            {
                $s='';
                if($regresa>1)
                $s='s';
                $mensaje=$regresa." prospecto".$s." sin asignación.\nRequiere atencion.";
            }
        }
        if($super == 8)
        {
            if( ($gid > 0) && ($uid > 0))
            {
                $pros_seguimiento=Regresa_Prospectos_En_Seguimiento($conn,$gid,$uid);
                $pros_seguimiento_no_cumplio_compromiso=Regresa_Prospectos_En_Seguimiento_Sin_Cumplir_C($conn,$gid,$uid);
                $pros_sin_asignar=Regresa_Prospectos_Nuevos($conn,$gid,$uid);
                $suma= $pros_seguimiento_no_cumplio_compromiso + $pros_sin_asignar;
            }
            if($suma == 0)
            {
                $regresa=0;
                $mensaje="Sin prospectos nuevos\n".$pros_seguimiento_no_cumplio_compromiso." compromisos que requieren atencion\n".$pros_seguimiento." prospectos en seguimiento";
            }
            if($suma > 0)
            {
                $s='';
                if($pros_sin_asignar > 1)
                    $s="s";

                $regresa=$pros_sin_asignar;
                $mensaje=$pros_sin_asignar." prospecto".$s." nuevo".$s."\n".$pros_seguimiento_no_cumplio_compromiso." compromisos que requieren atencion\n".$pros_seguimiento." prospectos en seguimiento";
            }
        }
        if($super == 10)
        {
            $regresa=Regresa_Call_Center($conn,$uid);
            $mensaje=$regresa." prospectos en Call Center";
            if($regresa == 0)
            {
                $mensaje="Sin prospectos en Call Center";
            }
        }
    }
    die($regresa."\n".$mensaje);

/**
 * Funcion que verifica cuantos prospectos hay en el call center
 * @param <int> $conn conexion a la base de datos
 * @param <int> $uid   id del vendedor
 */
   function Regresa_Call_Center($conn,$uid)
   {
        $reg=0;
        $sql="SELECT COUNT(*) as total FROM crm_contactos_no_asignados;";
        $res=mysql_query($sql,$conn);
        $reg=mysql_result($res,0,'total');
        return $reg;

   }
/**
 * Funcion que verifica cuantos prospectos tiene sin asignar una concesionaria
 * @param <int> $conn conexion a la base de datos
 * @param <int> $gid   id de concesionaria
 * @return <int>  numero de prospectos sin asignar
 */
    function Regresa_Prospectos_Sin_Asignacion($conn,$gid,$uid)
    {
        $filtro='';
        if($uid>0)
            $filtro=' AND uid='.$uid;
        $reg=0;
        $sql="SELECT COUNT(*) as total FROM crm_contactos WHERE gid='".$gid."' AND uid=0 ".$filtro.";";
        $res=mysql_query($sql,$conn);
        $reg=mysql_result($res,0,'total');
        return $reg;
    }

/**
 * Funcion que verifica cuantos prospectos tiene sin asignar una concesionaria
 * @param <int> $conn conexion a la base de datos
 * @param <int> $gid   id de concesionaria
 * @param <int> $uid   id del vendedor
 * @return <int>  numero de prospectos sin asignar
 */

    function Regresa_Prospectos_En_Seguimiento($conn,$gid,$uid)
    {
        $prospectos_en_segumiento=0;
        $sql="SELECT a.contacto_id,b.id,b.status_id,b.fecha_cita FROM crm_contactos as a LEFT JOIN crm_campanas_llamadas as b
              ON a.contacto_id=b.contacto_id WHERE a.gid='".$gid."' AND a.uid=".$uid." AND b.fecha_cita IS NOT NULL;";
        $res=mysql_query($sql,$conn);
        $prospectos_en_segumiento=mysql_num_rows($res);
        return $prospectos_en_segumiento;
    }

    function Regresa_Prospectos_En_Seguimiento_Sin_Cumplir_C($conn,$gid,$uid)
    {
        $compromisos_sin_cumplir=0;
        $sql="SELECT a.contacto_id,b.id,b.status_id,b.fecha_cita FROM crm_contactos as a LEFT JOIN crm_campanas_llamadas as b
              ON a.contacto_id=b.contacto_id WHERE a.gid='".$gid."' AND a.uid=".$uid." AND b.fecha_cita IS NOT NULL;";
        $res=mysql_query($sql,$conn);
        if(mysql_num_rows($res) > 0)
        {
            $date_actual=date('Y-m-d H:i:s');
            while(list($contacto_id,$id,$status_id,$fecha_cita) = mysql_fetch_array($res))
            {
                if($status_id == '-2')
                {
                    $sql_r="select TIMESTAMPDIFF(MINUTE,'".$fecha_cita."','".$date_actual."') as retraso;";
                    $res_r=mysql_query($sql_r,$conn);
                    list($retraso) = mysql_fetch_array($res_r);
                    if($retraso > 0)
                        $compromisos_sin_cumplir++;
                }
            }
        }
         return $compromisos_sin_cumplir;
    }

    function Regresa_Prospectos_Nuevos($conn,$gid,$uid)
    {
        $prospectos_en_segumiento=0;
        $compromisos_sin_cumplir=0;
        $sql="SELECT a.contacto_id,b.id,b.status_id,b.fecha_cita FROM crm_contactos as a LEFT JOIN crm_campanas_llamadas as b
              ON a.contacto_id=b.contacto_id WHERE a.gid='".$gid."' AND a.uid=".$uid." AND b.status_id=0 AND b.fecha_cita IS NULL;";
        $res=mysql_query($sql,$conn);
        $prospectos_en_segumiento=mysql_num_rows($res);
        return $prospectos_en_segumiento;
    }
?>
