<?php
include_once("config.php");
$db= mysql_connect($_dbhost,$_dbuname,$_dbpass);
$base_selection = mysql_select_db($_dbname,$db);

$array_gis_problemas=array();
$msg_alerta='';
$sql="SELECT gid FROM groups ORDER BY gid limit 300;";
$res=mysql_query($sql,$db);
if(mysql_num_rows($res) > 0)
{
    $buffer.="<table width='60%' align='center' border='1'>
              <thead><tr><th>Gid</th><th>Crm Contactos</th><th>Crm Campanas</th></tr></thead><tbody>";
    while(list($gid) = mysql_fetch_array($res))
    {
        $total_contactos=Count_Contactos($db,$gid);
        $total_campanas=Count_Campanas($db,$gid);
        $tmp='';
        if($total_contactos > $total_campanas)
        {
            $tmp=" bgcolor='#ff0000' ";
            $msg_alerta.="\nConcesionaria:   ".$gid."  No Contactos Finalizados:   ".$total_contactos."   No de Campanas llamadas Finalizados:   ".$total_campanas;
            $array_gis_problemas[]=$gid;

        }
        if($total_contactos < $total_campanas)
        {
            $tmp=" bgcolor='#ffff99' ";
        }
        $buffer.="<tr><td $tmp>".$gid."</td><td $tmp>".$total_contactos."</td><td $tmp>".$total_campanas."</td></tr>";
    }
    $buffer.="</table>";
    echo $buffer;
    if($msg_alerta != '')
    {
        $_email_headers  = 'MIME-Version: 1.0' . "\r\n";
        $_email_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $_email_from = "noreply@pcsmexico.com";
        $_email_headers .= "from:$_email_from\r\n";
        mail("lahernandez@pcsmexico.com", "Problemas con contactos Finalizados y campanas finalizadas ".date("Y-m-d"), $msg_alerta, $_email_headers);
    }

    // Resolvemos los problemas, incrustando el prospecto en campanas_llamadas_finalizadas
    if(count($array_gis_problemas) > 0)
    {
        foreach($array_gis_problemas as $xgid)
        {
            Revisa_Concesionarias($db,$xgid);
        }
    }
}

function Revisa_Concesionarias($db,$xgid)
{
    $sql="SELECT a.contacto_id,b.id FROM crm_contactos_finalizados as a LEFT JOIN crm_campanas_llamadas_finalizadas as b ON a.contacto_id=b.contacto_id
          WHERE a.gid='".$xgid."' AND b.id IS NULL ORDER BY a.contacto_id;";
    $res=mysql_query($sql,$db) or die ("Error en la consulta:   ".$sql);
    if(mysql_num_rows($res) > 0)
    {
        $campana_id=str_pad($xgid,4,'0',STR_PAD_LEFT)."01";
        while(list($contacto_id,$id) = mysql_fetch_array($res))
        {
            Inserta_Campanas_En_Finalizados($db,$campana_id,$contacto_id);
        }
    }
}

function Inserta_Campanas_En_Finalizados($db,$campana_id,$contacto_id)
{

    $ins = "INSERT INTO crm_campanas_llamadas_finalizadas (campana_id,contacto_id,status_id,fecha_cita,intentos)
            VALUES('$campana_id','$contacto_id','0','0000-00-00 00:00:00','0');";
    if(mysql_query($ins,$db) or die("Error en el inser  ".$ins))
    {
        echo "<br>Se inserto en registro con campana:  ".$campana_id."   contacto:   ".$contacto_id;
    }


}
function Count_Contactos($db,$gid)
{
    $sql="SELECT COUNT(*) FROM crm_contactos_finalizados WHERE gid=".$gid.";";
    $res=mysql_query($sql,$db);
    list($total) = mysql_fetch_array($res);
    return $total;
}

function Count_Campanas($db,$gid)
{
    $sql="SELECT count(c.contacto_id)
    FROM crm_campanas_llamadas_finalizadas AS c, crm_contactos_finalizados AS d
    WHERE d.contacto_id=c.contacto_id AND d.gid='$gid'";
    $res=mysql_query($sql,$db);
    list($total) = mysql_fetch_array($res);
    return $total;
}
?>
