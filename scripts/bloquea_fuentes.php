<?php
/**
 * Script que sirve pra bloquear concesionarias
 *
 */
include_once("../config.php");

$_email_from = "noreply@pcsmexico-pymes.com";
#$_email_gerente_gral = "gerardo.garcia@vw.com.mx";
$_email_gerente_gral = "lahernandez@pcsmexico.com";
$_email_headers = "from:$_email_from\r\n";
$conn=mysql_connect($_dbhost,$_dbuname,$_dbpass);
mysql_select_db($_dbname,$conn);
$hora_sistema=date('Y-m-d H:i:s');
$fuentes_bloquedas='';
$total_fuentes_desactivadas=0;
$total_fuentes_activadas=0;
$array_fuentes_desactivadas=array();
$array_fuentes_activadas=array();

$sql="SELECT fuente_id,nombre,fecha_inicial,fecha_final,active FROM crm_fuentes WHERE fuente_id!= 0 AND  fecha_final!= '0000-00-00 00:00:00';";
$res = mysql_query($sql,$conn) or die ("Error en la consulta  ".$sql );
if(mysql_num_rows($res) > 0)
{
    while(list($fuente_id,$nombre,$fecha_inicial,$fecha_final,$active) = mysql_fetch_array($res))
    {
        $sql_f="SELECT IF ('".$hora_sistema."' BETWEEN '".$fecha_inicial."' AND '".$fecha_final."', '1','0') AS respuesta FROM crm_fuentes WHERE fuente_id='".$fuente_id."';";
        $res_f=mysql_query($sql_f,$conn) or die("Error en el calculo:  ".$sql_f);
        list($respuesta) =mysql_fetch_array($res_f);
        $active=0;
        if($respuesta == 0)
        {
            $active = 0;
            $array_fuentes_desactivadas[$fuente_id]=$nombre."|".$fecha_inicial."|".$fecha_final."|".$active;
            $total_fuentes_desactivadas++;
        }

        if($respuesta == 1)
        {
            $active = 1;
            $array_fuentes_activadas[$fuente_id]=$nombre."|".$fecha_inicial."|".$fecha_final."|".$active;
            $total_fuentes_activadas++;
        }
        Actualiza_Bloqueo($conn,$fuente_id,$active);
    }
    $buf="Listado de Fuentes\n";
    if(count($array_fuentes_activadas) > 0)
    {
        $buf.="Se han Activado ".$total_fuentes_activadas." fuentes.\n\n
        Listado de fuentes Activadas:\n".Genera_Salida($array_fuentes_activadas)."\n\n\n\n";
    }
    if(count($array_fuentes_desactivadas) > 0)
    {
        $buf.="Se han bloquedo ".$total_fuentes_desactivadas." fuentes.\n\n
               Listado de fuentes bloqueadas:\n".Genera_Salida($array_fuentes_desactivadas)."";
    }
    die("Termino de proceso\n");
/*    $_email_headers  = 'MIME-Version: 1.0' . "\r\n";
    $_email_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $_email_from = "noreply@pcsmexico.com";
    $_email_headers .= "from:$_email_from\r\n";
    mail("orangel@pcsmexico.com", "Fuentes Bloqueadas con fecha".date("Y-m-d H:i:s"), $buf, $_email_headers);
    mail("lahernandez@pcsmexico.com", "Fuentes Bloqueadas con fecha ".date("Y-m-d H:i:s"), $buf, $_email_headers);
    mail($_email_gerente_gral, "Totales de Carga de prospectos con fecha  ".date("Y-m-d H:i:s"), $buf, $_email_headers);
    die($buf);*/
}

function Actualiza_Bloqueo($db,$fuente_id,$active)
{
    $upd="UPDATE crm_fuentes SET active=".$active." WHERE fuente_id='".$fuente_id."';";
    mysql_query($upd,$db) or die("Error:  ".$upd);
}

function Genera_Salida($array)
{
    $msg_a="<table width='80%' align='center' border='0'>
            <tr bgcolor='#cdcdcd'>
                <td>Fuente</td><td>Fecha Inicial</td><td Fecha Termino</td>
            </tr>";
    foreach($array as $key => $value)
    {
        $tmp=explode('|',$value);
        $msg_a.="<tr>
                    <td>".$tmp[0]."</td><td>".$tmp[1]."</td><td>".$tmp[2]."</td>
                </tr>";
    }
    $msg_a.="</table>";
    return $msg_a;
}


/*global $db;
require_once("$_includesdir/mail.php");
$hora_sistema=date('Y-m-d H:i:s');
$fuentes_bloquedas='';
$total_fuentes_desactivadas=0;
$total_fuentes_activadas=0;
$array_fuentes_desactivadas=array();
$array_fuentes_activadas=array();

$sql="SELECT fuente_id,nombre,fecha_inicial,fecha_final,active FROM crm_fuentes WHERE fuente_id!= 0 AND fecha_inicial!= '0000-00-00 00:00:00' AND  fecha_final!= '0000-00-00 00:00:00';";
$res=$db->sql_query($sql) or die ("Error en la consulta  ".$sql );
if($db->sql_numrows($res) > 0)
{
    while(list($fuente_id,$nombre,$fecha_inicial,$fecha_final,$active) = $db->sql_fetchrow($res))
    {
        $sql_f="SELECT IF ('".$hora_sistema."' BETWEEN '".$fecha_inicial."' AND '".$fecha_final."', '1','0') AS respuesta FROM crm_fuentes WHERE fuente_id='".$fuente_id."';";
        $res_f=$db->sql_query($sql_f) or die("Error en el calculo:  ".$sql_f);
        list($respuesta) =$db->sql_fetchrow($res_f);
        $active=0;
        if($respuesta == 0)
        {
            $active = 0;
            $array_fuentes_desactivadas[$fuente_id]=$nombre."|".$fecha_inicial."|".$fecha_final."|".$active;
            $total_fuentes_desactivadas++;
        }

        if($respuesta == 1)
        {
            $active = 1;
            $array_fuentes_activadas[$fuente_id]=$nombre."|".$fecha_inicial."|".$fecha_final."|".$active;
            $total_fuentes_activadas++;
        }
        Actualiza_Bloqueo($db,$fuente_id,$active);
    }
    $buf="Listado de Fuentes\n";
    if(count($array_fuentes_activadas) > 0)
    {
        $buf.="Se han Activado ".$total_fuentes_activadas." fuentes.\n\n
        Listado de fuentes Activadas:\n".Genera_Salida($array_fuentes_activadas)."\n\n\n\n";
    }
    if(count($array_fuentes_desactivadas) > 0)
    {
        $buf.="Se han bloquedo ".$total_fuentes_desactivadas." fuentes.\n\n
               Listado de fuentes bloqueadas:\n".Genera_Salida($array_fuentes_desactivadas)."";
    }
#    die("Termino de proceso\n");
    $_email_headers  = 'MIME-Version: 1.0' . "\r\n";
    $_email_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $_email_from = "noreply@pcsmexico.com";
    $_email_headers .= "from:$_email_from\r\n";
    mail("orangel@pcsmexico.com", "Fuentes Bloqueadas con fecha".date("Y-m-d H:i:s"), $buf, $_email_headers);
    mail("lahernandez@pcsmexico.com", "Fuentes Bloqueadas con fecha ".date("Y-m-d H:i:s"), $buf, $_email_headers);
    mail($_email_gerente_gral, "Totales de Carga de prospectos con fecha  ".date("Y-m-d H:i:s"), $buf, $_email_headers);
    die($buf);
}
*/

/**
 * Funcion que actualiza la tabla de fuentes para bloquear o desbloquear fuentes
 * @param <int> $db id de conexion a bd
 * @param <int> $fuente_id  id de la fuente
 * @param <int> $active   0 bloqueada, 1 activa
 */
/*function Actualiza_Bloqueo($db,$fuente_id,$active)
{
    $upd="UPDATE crm_fuentes SET active=".$active." WHERE fuente_id='".$fuente_id."';";
    $db->sql_query($upd) or die("Error:  ".$upd);
}*/


/**
 * Funcion que despliega la salida de las fuentes para el mensaje
 * @param <array> $array  Array de fuentes
 * @return <string>   Cadena de mensaje
 */
/*function Genera_Salida($array)
{
    $msg_a="<table width='80%' align='center' border='0'>
            <tr bgcolor='#cdcdcd'>
                <td>Fuente</td><td>Fecha Inicial</td><td Fecha Termino</td>
            </tr>";
    foreach($array as $key => $value)
    {
        $tmp=explode('|',$value);
        $msg_a.="<tr>
                    <td>".$tmp[0]."</td><td>".$tmp[1]."</td><td>".$tmp[2]."</td>
                </tr>";
    }
    $msg_a.="</table>";
    return $msg_a;
}*/
?>