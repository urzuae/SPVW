<?php

function modelos()
{
    header ("content-type: text/xml");
    $registros="";
    include_once("../config.php");
    $linkTodb = mysqli_connect($_dbhost,$_dbuname,$_dbpass) or die ("error");
    $conn     = mysqli_select_db ($linkTodb,$_dbname) or die("Error de Base de Datos");
    if($conn)
    {
        $salida_xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $salida_xml .= "\t<informacion>\n";
        $result = mysqli_query($linkTodb,'call Modelos()') or die("Error en el Store Procedure");
        $rows=mysqli_num_rows($result);
        if(mysqli_num_rows($result) > 0)
        {
            while ($row= mysqli_fetch_array($result,MYSQLI_NUM))
            {
                $row[1]=str_replace("&",'',$row[1]);
                $salida_xml .="\t\t<registros>\n";
                $salida_xml .="\t\t\t<id>".$row[0]."</id>\n";
                $salida_xml .="\t\t\t<name>".utf8_encode($row[1])."</name>\n";
                $salida_xml .="\t\t</registros>\n";
            }
        }
        $salida_xml .= "\t</informacion>";
    }
    return $salida_xml;
}
?>