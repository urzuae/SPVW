<?php
include_once("../config.php");
$conn=mysql_connect($_dbhost,$_dbuname,$_dbpass);
$red_db=mysql_select_db($_dbname,$conn);
$regresa=0;
$buffer='';
$contador=0;
$mensajes='';
// Sacamos la informacion de los remainder que existen en la tabla
$sql="SELECT id,contacto_id,gid,uid,campana_id,fecha_cita,remainder,remainder_cumplido FROM crm_contactos_remainder
      WHERE remainder_cumplido=0 ORDER BY fecha_cita ASC;";
$res=mysql_query($sql,$conn) or die("Error en la consulta:  ".$sql);
if(mysql_num_rows($res) > 0)
{
    $fecha_actual=date('Y-m-d H:i:s');

    while(list($id,$contacto_id,$gid,$uid,$campana_id,$fecha_cita,$remainder,$remainder_cumplido) = mysql_fetch_array($res))
    {

        $fecha_letrero=" el día ".substr($fecha_cita,8,2)."-".substr($fecha_cita,5,2)."-".substr($fecha_cita,0,4)." a las ".substr($fecha_cita,11,5);
        //Saco la fecha en que se deberia de mostrar la alarma, esto lo hago resdtando en remainder a la fecha de cita
        $sql_alarma="SELECT DATE_SUB('".$fecha_cita."', INTERVAL '".$remainder."' MINUTE) as fecha_alarma
              FROM crm_contactos_remainder;";
        $res_alarma=mysql_query($sql_alarma,$conn) or die ("Error en la consulta de remainder:  ".$sql_alarma);
        list($fecha_alarma)=mysql_fetch_array($res_alarma);

        //verifico que la fecha de la alarma este entre la fecha de alarma y la fecha actual, si es asi mando el compromiso
        $sql_f="SELECT IF ('".$fecha_actual."' BETWEEN '".$fecha_alarma."' AND '".$fecha_cita."', '1','0') AS respuesta;";
        $res_f=mysql_query($sql_f,$conn) or die("Error en el calculo:  ".$sql_f);
        list($respuesta) =mysql_fetch_array($res_f);
        if($respuesta == 1)
        {
            $regresa=1;
            if($contador == 0)
            {
                $buffer.="Recordatorio de compromiso";
                $buffer.="\n".Regresa_Datos($conn,$gid,3)."\nVendedor:  ".Regresa_Datos($conn,$uid,2);
            }
            $buffer.="\nUsted tiene un compromiso ".$fecha_letrero.",\ncon el prospecto ".Regresa_Datos($conn,$contacto_id,1)."
                  que se encuentra en la etapa de ".Regresa_Datos($conn,$campana_id,4);
            $contador++;
        }
    }
    die($regresa."\n".$buffer);

}


function Regresa_Datos($conn,$valor,$opc)
{
    $nombre='';
    switch($opc)
    {
        case 1:
            $sql_con="SELECT concat(nombre,' ',apellido_paterno,' ',apellido_materno) as prospecto FROM crm_contactos
            WHERE contacto_id=".$valor." LIMIT 1;";
            break;
        case 2:
            $sql_con="SELECT name FROM users WHERE uid=".$valor." LIMIT 1;";
            break;
        case 3:
            $sql_con="SELECT name FROM groups WHERE gid=".$valor." LIMIT 1;";
            break;
        case 4:
            $sql_con="SELECT nombre FROM crm_campanas WHERE campana_id=".$valor." LIMIT 1;";
            break;
    }
    $res_con=mysql_query($sql_con,$conn) or die ("Error:  ".$sql_con);
    if(mysql_num_rows($res_con) > 0)
    {
        $nombre=mysql_result($res_con,0,0);
    }
    return $nombre;
}
?>