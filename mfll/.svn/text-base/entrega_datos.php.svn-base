<?php
header("Content-type: text/html; charset=iso-8859-1");
$tipo=$_GET['opc'];
$archivo=$_GET['archivo'];
include_once("../config.php");
include_once("clases/Carga_Eventos.class.php");
include_once("clases/Carga_Prospectos.class.php");
include_once("clases/Carga_Prospectos_SPVW_BD.class.php");

// Seleccion de la tabla que se va a utilizar

$_dbname = 'mfll';
//if($tipo == 3)    $_dbname = 'crm_prospectos';

//  ARCHIVO A buscar
$filename ="/home/mfll/".$archivo;
//$filename ="/home/lciencias/Escritorio/".$archivo;
if(!file_exists($filename))
{
    die("El archivo de carga [$filename] no existe\n");
}
else
{
    $fh = fopen($filename, "r");
    if (!$fh)
    {
    	  die("Error, no se puede leer el archivo (tal vez sea demasiado grande)".$filename);
    	  return;
    }
    else
    {
        $conn = mysql_connect($_dbhost,$_dbuname,$_dbpass) or die ("error");
        mysql_select_db($_dbname,$conn) or die("Error de Base de Datos");
        switch($tipo)
        {
            case 1:
                $obj_eve = new Carga_Eventos($fh,$conn);
                echo $obj_eve->Obten_Resultados();
                break;
            case 2:
                $obj_pro = new Carga_Prospectos($fh,$conn);
                echo $obj_pro->Obten_Resultados();
                break;
            case 3:
                $obj_spvw = new Carga_Prospectos_SPVW_BD($fh,$conn);
                echo $obj_spvw->Obten_Resultados();
                break;
        }
    }
    fclose($fh);
}
?>