<?php
require_once('nusoap/lib/nusoap.php');
$ns="http://10.0.0.98/vw/webservice";
$server = new soap_server();
$server->configureWSDL('VW',$ns);
$server->wsdl->schemaTargetNamespace=$ns;
$server->register('conectar',array('x' => 'xsd:string'),array('return' => 'xsd:string'),$ns);



function conectar($opc,$gid)
{
    header ("content-type: text/xml");
    $registros="";
    include_once("config.php");
    $linkTodb = mysqli_connect($_dbhost,$_dbuname,$_dbpass) or die ("error");
    $conn     = mysqli_select_db ($linkTodb,$_dbname) or die("Error de Base de Datos");
    $call     = Inicia_Proceso_Envio_Datos($opc,$gid);
    if( ($opc>=1) && ($opc<=2))
        $registros= Recupera_Informacion($linkTodb,$call);
    else
        $registros= Recupera_Informacion_Vendedores($linkTodb,$call);

    return $registros;
}

/**
 * Funcion que se encarga de recuperar la información mediante un procedimiento almacenado
 * @param <int> $linkTodb, conexion a la bd
 * @param <string> $call, llamada al Store Procedure
 * @return <string> $salida, cadena que contiene los registros en formato XML
 */
function Recupera_Informacion($linkTodb,$call)
{
    $salida_xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $salida_xml .= "\t<informacion>\n";
    $result = mysqli_query($linkTodb,$call) or die("Error en el Store Procedure");
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
    return $salida_xml;
}

/**
 * Función que se encarga de llamar al Store Procedure correcto
 * @param <int> $opc, opción de los Store procedures
 * @param <int> $gid, id de la concesionaria, en caso de que se deseen saber los vendedores de x concesionaria
 * @return <string> cadena con el store correcto
 */
function Inicia_Proceso_Envio_Datos($opc,$gid)
{
    switch($opc)
    {
        case 1:
            $call="call Concesionarias();";
            break;
        case 2:
            $call="call Modelos();";
            break;
        case 3:
            $call="call Vendedores('".$gid."');";
            break;
        default:
            $call="call Concesionarias();";
            break;
    }
    return $call;
}


function Recupera_Informacion_Vendedores($linkTodb,$call)
{
    $salida_xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $salida_xml .= "\t<informacion>\n";
    $result = mysqli_query($linkTodb,$call) or die("Error en el procedure de vendedores");
    if(mysqli_num_rows($result) > 0)
    {
        while ($row= mysqli_fetch_array($result,MYSQLI_NUM))
        {
            $salida_xml .="\t\t<registro>\n";
            $salida_xml .="\t\t\t<gid>".$row[0]."</gid>\n";
            $salida_xml .="\t\t\t<uid>".$row[1]."</uid>\n";
            $salida_xml .="\t\t\t<name>".$row[2]."</name>\n";
            $salida_xml .="\t\t\t<user>".$row[3]."</user>\n";
            $salida_xml .="\t\t\t<tipo_acceso>".$row[4]."</tipo_acceso>\n";
            $salida_xml .="\t\t\t<email>".$row[5]."</email>\n";
            $salida_xml .="\t\t\t<prospectos>".$row[6]."</prospectos>\n";
            $salida_xml .="\t\t</registro>\n";
        }
    }
    $salida_xml .= "\t</informacion>\n";
    return $salida_xml;
}


$server->service($HTTP_RAW_POST_DATA);
?>