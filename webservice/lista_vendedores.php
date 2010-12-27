<?php

function vendedores($x,$gid)
{
    header ("content-type: text/xml");
    include_once("../config.php");
    $salida_xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $linkTodb = mysql_connect($_dbhost,$_dbuname,$_dbpass) or die ("error");
    $conn     = mysql_select_db($_dbname,$linkTodb) or die("Error de Base de Datos");
    if($conn)
    {
        $sql="SELECT gid,uid,name,user,'Vendedor' AS tipo_acceso,email FROM users WHERE gid in (".$gid.") ORDER BY gid,uid;";
        $salida_xml .= "\t<informacion>\n";
        $result = mysql_query($sql,$linkTodb) or die("Error en el procedure de vendedores");
        if(mysql_num_rows($result) > 0)
        {
            while ($row= mysql_fetch_array($result,MYSQL_ASSOC))
            {
                $salida_xml .="\t\t<registro>\n";
                $salida_xml .="\t\t\t<gid>".$row['gid']."</gid>\n";
                $salida_xml .="\t\t\t<uid>".$row['uid']."</uid>\n";
                $salida_xml .="\t\t\t<name>".$row['name']."</name>\n";
                $salida_xml .="\t\t\t<user>".$row['user']."</user>\n";
                $salida_xml .="\t\t\t<tipo_acceso>".$row['tipo_acceso']."</tipo_acceso>\n";
                $salida_xml .="\t\t\t<email>".$row['email']."</email>\n";
                $salida_xml .="\t\t</registro>\n";
            }
        }
        $salida_xml .= "\t</informacion>\n";
    }
    return $salida_xml;
}
?>