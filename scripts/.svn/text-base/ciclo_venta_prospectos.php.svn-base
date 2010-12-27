<?php
/* 
 * Script que consulta la etapa en la que se encuentran los prospectos de x gerente.
 */

include_once("../config.php");

// Realizo la conexion
$prospectos=0;
$conn=mysql_connect($_dbhost,$_dbuname,$_dbpass) or die("Error en la conexión a la Base de Datos");
$red_db=mysql_select_db($_dbname,$conn);
$sql="SELECT gid,uid FROM users WHERE user='".$_REQUEST['user']."' AND password like '%".$_REQUEST['password']."%';";
$res=mysql_query($sql,$conn) or die("Error al consultar:  ".$sql);
if(mysql_num_rows($res) > 0)
{
    list($gid,$uid)=mysql_fetch_array($res);
    $array_campanas=Regresa_Campanas($conn,$gid);
    $tmp=Regresa_Lista_Prospectos($conn,$gid,$array_campanas);
    $prospectos=$tmp[0];
    $buffer=$tmp[1];
}
die($prospectos."\n\n".$buffer);


function Regresa_Lista_Prospectos($conn,$gid,$array_campanas)
{
    $buf='';
    $no_pros=0;
    $sql="SELECT a.gid, b.campana_id, count( b.campana_id ) AS total
          FROM crm_contactos AS a LEFT JOIN crm_campanas_llamadas AS b ON a.contacto_id = b.contacto_id
          WHERE a.gid = ".$gid." AND a.uid >0 AND b.campana_id IS NOT NULL GROUP BY b.campana_id;";
    $res=mysql_query($sql,$conn);
    if(mysql_num_rows($res) > 0)
    {
        while(list($_gid,$id,$total) = mysql_fetch_array($res))
        {
            $buf.=$array_campanas[$id].' - '.$total."\n";
            $no_pros=$no_pros + $total;
        }
    }
    return array($no_pros,$buf);
}
function Regresa_Campanas($conn,$gid)
{
    $array=array();
    $sql="SELECT a.campana_id, b.nombre FROM crm_campanas_groups AS a LEFT JOIN crm_campanas AS b
          ON a.campana_id = b.campana_id WHERE a.gid =2016 order by a.campana_id;";
    $res=mysql_query($sql,$conn);
    if(mysql_num_rows($res) > 0)
    {
        while(list($id,$name) = mysql_fetch_array($res))
        {
            $array[$id]=$name;
        }
    }
    return $array;
}
?>