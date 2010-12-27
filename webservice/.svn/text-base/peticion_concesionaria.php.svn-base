<?php
$cadena=$_GET['peticion'];
include_once("../config.php");
$linkTodb = mysql_connect($_dbhost,$_dbuname,$_dbpass) or die ("error");
$conn     = mysql_select_db($_dbname,$linkTodb) or die("Error de Base de Datos");
$registros=array();
if($conn)
{
    $cadena_upper=strtoupper($cadena);
    $sql_con="SELECT nombre FROM crm_fuentes WHERE nombre='".$cadena_upper."';";
    $res_con=mysql_query($sql_con,$linkTodb);
    if(mysql_num_rows($res_con) == 0)
    {
        $sql_min="SELECT MIN(fuente_id) FROM crm_fuentes;";
        $res_min=mysql_query($sql_min,$linkTodb);
        if(mysql_num_rows($res_min)>0)
        {
            $min= mysql_result($res_min,0,0);
        }
        $min--;
        $sql_inser="INSERT INTO crm_fuentes (fuente_id,nombre,timestamp,active) VALUES ('".$min."','".$cadena."','".date("Y-m-d H:i:s")."','1');";
        if(mysql_query($sql_inser,$linkTodb))
        {
            mysql_query("INSERT INTO crm_fuentes_arbol (padre_id,hijo_id) VALUES ('1','".$min."');");
        }
    }
    $sql="SELECT gid,name FROM groups WHERE gid>3 ORDER BY gid;";
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
