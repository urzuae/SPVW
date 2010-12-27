<?php

function vendedores($gid)
{
    include_once("../config.php");
    $linkTodb = mysql_connect($_dbhost,$_dbuname,$_dbpass) or die ("error");
    $conn     = mysql_select_db($_dbname,$linkTodb) or die("Error de Base de Datos");
    $array=array();
    if($conn)
    {
        $sql="SELECT gid,uid,name,user,'Vendedor' AS tipo_acceso,email FROM users WHERE gid in (".$gid.") AND super=8 ORDER BY gid,uid;";
        $result = mysql_query($sql,$linkTodb) or die("Error en la consulta a la tabla");
        if(mysql_num_rows($result) > 0)
        {
            while ($row= mysql_fetch_array($result,MYSQL_NUM))
            {
                $row[2]=str_replace("&",'',$row[2]);
                $array[]=array('gid' => $row[0],'uid' => $row[1], 'nombre' => $row[2], 'user' => $row[3], 'tipo_acceso' => $row[4],'email' => $row[5]);
            }
        }
    }
    return $array;
}
?>