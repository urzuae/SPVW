<?php

function modelos($x)
{
    include_once("../config.php");
    $linkTodb = mysql_connect($_dbhost,$_dbuname,$_dbpass) or die ("error");
    $conn     = mysql_select_db($_dbname,$linkTodb) or die("Error de Base de Datos");
    $registros=array();
    if($conn)
    {
        $sql="SELECT unidad_id,nombre FROM crm_unidades WHERE 1 ORDER BY nombre;";
        $result = mysql_query($sql,$linkTodb) or die("Error en la consulta a la tabla de modelos");
        if(mysql_num_rows($result) > 0)
        {
            while ($row= mysql_fetch_array($result,MYSQL_NUM))
            {
                $row[1]=str_replace("&",'',$row[1]);
                $registros[]=array('id' => $row[0], 'modelo' => $row[1]);
            }
        }
    }
    return $registros;
}
?>