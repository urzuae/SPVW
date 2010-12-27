<?php
include_once("config.php");
$db= mysql_connect($_dbhost,$_dbuname,$_dbpass);
$base_selection = mysql_select_db($_dbname,$db);

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
            $msg_alerta.="\nConcesionaria:   ".$gid."  No. Contactos :   ".$total_contactos."   No. de Campanas llamadas:   ".$total_campanas;
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
        mail("lahernandez@pcsmexico.com", "Problemas con contactos y campanas ".date("Y-m-d"), $msg_alerta, $_email_headers);
    }
}

function Count_Contactos($db,$gid)
{
    $sql="SELECT COUNT(*) FROM crm_contactos WHERE gid=".$gid.";";
    $res=mysql_query($sql,$db);
    list($total) = mysql_fetch_array($res);
    return $total;
}

function Count_Campanas($db,$gid)
{
    $sql="SELECT count(c.contacto_id)
    FROM crm_campanas_llamadas AS c, crm_contactos AS d
    WHERE d.contacto_id=c.contacto_id AND d.gid='$gid'";
    $res=mysql_query($sql,$db);
    list($total) = mysql_fetch_array($res);
    return $total;
}
?>
