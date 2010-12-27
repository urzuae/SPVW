<?

global $db, $campana_id, $gid, $uid, $fecha_ini, $fecha_fin, $origen_id;

$fecha_ini = $argv[2];
$fecha_fin = $argv[3];

if($fecha_ini =="" || $fecha_fin == "")
{
	echo "\n Proporcione el periodo del reporte \n\n";
	die();
}	
/*Abrir archivos para almacenar reportes*/
$filename1="reporte1-$fecha_ini-$fecha_fin.csv";
$freporte = fopen($filename1,"w");

if($fecha_ini)
{
    $l_excel .= "&fecha_ini=$fecha_ini";
    $titulo .= " desde $fecha_ini";
    $fecha_ini_o = $fecha_ini;
    $fecha_ini = date_reverse($fecha_ini);
    $where_fecha .= " AND cl.fecha_importado>'$fecha_ini 00:00:00'";
}
if($fecha_fin)
{
    $l_excel .= "&fecha_fin=$fecha_fin";
    $titulo .= " hasta $fecha_fin";
    $fecha_fin_o = $fecha_fin;
    $fecha_fin = date_reverse($fecha_fin);
    $where_fecha .= " AND cl.fecha_importado<'$fecha_fin 23:59:59'";
}


$sql = "SELECT nombre FROM crm_campanas where campana_id = campana_id < 9 LIMIT 8";
$result = $db->sql_query($sql) or die($sql);
while(list($nombre) = $db->sql_fetchrow($result))
{
    $campanas[$i] = $nombre;
    $i++;
}

$sql = "SELECT zona_id, gid FROM `groups_zonas`";
$result = $db->sql_query($sql) or die($sql);
while(list($zona_id, $gid) = $db->sql_fetchrow($result))
{
    $zonas[$gid] = $zona_id;
}
//obtenemos todas las campa�as posibles para evitar consultas posteriores
$origenes = array();
$sql = "SELECT origen_id, nombre FROM crm_contactos_origenes ";
$r = $db->sql_query($sql) or die($sql);
while(list($c_id, $c_nombre) = $db->sql_fetchrow($r))
{
    $origenes[$c_id] = $c_nombre;
}

$nombre_etapas = array(1 => "PROSPECCION", 2 => "CALIFICACION", 3 => "PRESENTACION", 4=> "DEMOSTRACION", 
					   5 => "NEGOCIACION", 6 => "CIERRE", 7 => "ENTREGA", 8 => "SEGUIMIENTO");
$etapas = array();
$sql = "SELECT etapa_ciclo_id, campana_id FROM crm_campanas ";
$r = $db->sql_query($sql) or die($sql);
while(list($c_id, $campana_id) = $db->sql_fetchrow($r))
{
    $etapas[$campana_id] = $nombre_etapas[$c_id];
}

$nombre_zonas = array();
$sql = "SELECT zona_id, nombre FROM crm_zonas ";
$r = $db->sql_query($sql) or die($sql);
while(list($zid, $n) = $db->sql_fetchrow($r))
{
    $nombre_zonas[$zid] = $n;
}



$contacto_ids = array();
$origen_ids = array();
$fecha_importados = array();
$gids = array();

$sql = "SELECT contacto_id, origen_id, fecha_importado, gid
	        FROM crm_contactos_finalizados AS cl 
	        where 1 $where_fecha";
$r = $db->sql_query($sql) or die($sql);	        	        
//while(list($contacto_id, $origen_id, $fecha_importado, $gid) = $db->sql_fetchrow($result))
while(list($contacto_id, $origen_id, $fecha_importado, $gid) = $db->sql_fetchrow($r))
{
    //$contacto_ids[] = $contacto_id;
    //echo "-$contacto_id,";
    $reporte = "-$contacto_id,";
    //$origen_ids[$contacto_id] = $origen_id;
    //echo $origenes[$origen_id].",";
    $reporte = $reporte.$origenes[$origen_id].",";
    //$fecha_importados[$contacto_id] = $fecha_importado;
    //echo "$fecha_importado,";
    $reporte = $reporte."$fecha_importado,";
    //$gids[$contacto_id] = $gid;
    //echo "$gid,";
    //echo $nombre_zonas[$zonas[$gid]].",";
    $reporte = $reporte."$gid,".$nombre_zonas[$zonas[$gid]].",";
    //coche
    $sql = "select modelo from crm_prospectos_unidades where contacto_id='$contacto_id'";
    $result2 = $db->sql_query($sql) or die($sql);
    list($modelo) = $db->sql_fetchrow($result2);
    //echo "$modelo,";
    $reporte = $reporte."$modelo,";
    //$modelos[$contacto_id] = $modelo;
    //ciclo de venta
    $sql = "select campana_id, id from crm_campanas_llamadas_finalizadas where contacto_id='$contacto_id'";
    $result2 = $db->sql_query($sql) or die($sql);
    list($campana_id, $llamada_id) = $db->sql_fetchrow($result2);
    //echo $etapas[$campana_id].",";
    $reporte = $reporte.$etapas[$campana_id].",";
    //seguimiento
    $sql = "select evento_id from crm_campanas_llamadas_eventos where llamada_id='$llamada_id'";
    $result2 = $db->sql_query($sql) or die($sql);
    $seguimiento = $db->sql_numrows($result2);
    //echo ($seguimiento?"SEG":"").",";
    $reporte = $reporte.($seguimiento?"SEG":"").",";
    $sql = "select chasis from crm_prospectos_ventas where contacto_id='$contacto_id'";
    $result2 = $db->sql_query($sql) or die($sql);
    list($chasis) = $db->sql_fetchrow($result2);
    //echo "$chasis";
    $reporte = $reporte."$chasis \n";
    $count++;    
    //echo "\n";
    //$etapa_ciclo_ids[$contacto_id] = $etapas[$campana_id];
    /* Escribir linea a archivo en formato csv*/
    fputcsv($freporte, split(',', $reporte));    
    echo $count."->$reporte\n";
}

$reporte="";
$sql = "SELECT contacto_id, origen_id, fecha_importado, gid
	        FROM crm_contactos AS cl 
	        where 1 $where_fecha";
$result = $db->sql_query($sql) or die($sql);
while(list($contacto_id, $origen_id, $fecha_importado, $gid) = $db->sql_fetchrow($result))
{
    //$contacto_ids[] = $contacto_id;
    //echo "$contacto_id,";
    $reporte="$contacto_id,";
    //$origen_ids[$contacto_id] = $origen_id;
    //echo $origenes[$origen_id].",";
    $reporte=$reporte.$origenes[$origen_id].",";
    //$fecha_importados[$contacto_id] = $fecha_importado;
    //echo "$fecha_importado,";
    $reporte=$reporte."$fecha_importado,";
    //$gids[$contacto_id] = $gid;
    //echo "$gid,";
    //echo $nombre_zonas[$zonas[$gid]].",";
    $reporte=$reporte."$gid,".$nombre_zonas[$zonas[$gid]].",";
    //coche
    $sql = "select modelo from crm_prospectos_unidades where contacto_id='$contacto_id'";
    $result2 = $db->sql_query($sql) or die($sql);
    list($modelo) = $db->sql_fetchrow($result2);
    //echo "$modelo,";
    $reporte=$reporte."$modelo,";
    //$modelos[$contacto_id] = $modelo;
    //ciclo de venta
    $sql = "select campana_id, id from crm_campanas_llamadas where contacto_id='$contacto_id'";
    $result2 = $db->sql_query($sql) or die($sql);
    list($campana_id, $llamada_id) = $db->sql_fetchrow($result2);
    //echo $etapas[$campana_id].",";
    $reporte=$reporte.$etapas[$campana_id].",";
    //seguimiento
    $sql = "select evento_id from crm_campanas_llamadas_eventos where llamada_id='$llamada_id'";
    $result2 = $db->sql_query($sql) or die($sql);
    $seguimiento = $db->sql_numrows($result2);
    //echo ($seguimiento?"SEG":"").",";
    $reporte=$reporte.($seguimiento?"SEG":"").",";
    $sql = "select chasis from crm_prospectos_ventas where contacto_id='$contacto_id'";
    $result2 = $db->sql_query($sql) or die($sql);
    list($chasis) = $db->sql_fetchrow($result2);
    //echo "$chasis";
    $reporte=$reporte."$chasis";
    $count++;
    //echo "\n";
    //$etapa_ciclo_ids[$contacto_id] = $etapas[$campana_id];
    /* Escribir linea a archivo en formato csv*/
    fputcsv($freporte, split(',', $reporte));
    echo $count." registros procesados\n";    
}

/*Cerrando archivo*/
fclose($freporte);

/*Enviar correo*/
$mime_boundary = "<<<--==+X[".md5(time())."]";
$headers .= "From: noreply@pcsmexico-pymes.com";
//$headers .= "Cc: yrokecelis@yahoo.com.mx";
$cuerpo="Por favor descargue el reporte adjunto ";
$asunto="Reporte";
$destinatario="orangel@pcsmexico.com,lahernandez@pcsmexico.com";
$cuerpo .= "Content-Type: application/octet-stream;\r\n";
$cuerpo .= " name=\"$filename1\"\r\n";
$cuerpo .= "Content-Transfer-Encoding: quoted-printable\r\n";
$cuerpo .= "Content-Disposition: attachment;\r\n";
$cuerpo .= " filename=\"$filename1\"\r\n";
$cuerpo .= "\r\n";
//$cuerpo .= $fileContent;
$cuerpo .= "\r\n";
$cuerpo .= "--".$mime_boundary."\r\n";

mail($destinatario,$asunto,$cuerpo,$headers);
/*
$tabla = "";
foreach($contacto_ids as $c)
{
    echo  "{$c},{$origenes[$origen_ids[$c]]},{$fecha_importados[$c]},{$gids[$c]},{$modelos[$c]},{$etapa_ciclo_ids[$c]}\n";
}
*/
?>
