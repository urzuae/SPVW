<?php
$cadena=$_GET['peticion'];
include_once("../config.php");
$linkTodb = mysql_connect($_dbhost,$_dbuname,$_dbpass) or die ("error");
$conn     = mysql_select_db($_dbname,$linkTodb) or die("Error de Base de Datos");
$registros=array();
if($conn)
{
    $sql="SELECT uid,name FROM users WHERE super=8 AND gid > 3 AND gid IN (".$cadena.");";
    $result = mysql_query($sql,$linkTodb) or die("Error en la consulta a la tabla de modelos");
    if(mysql_num_rows($result) > 0)
    {
        while (list($id,$nombre) = mysql_fetch_array($result))
        {
            echo $id."|".$nombre."\n";
        }
    }
    else
    {
        echo"Sin Registros|0";
    }
}
?>