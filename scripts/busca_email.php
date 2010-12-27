<?php
global $db, $submit, $del;
$filename = $argv[2];
if(!file_exists($filename)){
    die("El archivo de carga [$filename] no existe\n");
}
require_once("$_includesdir/mail.php");

$fh = fopen($filename, "r");

if (!$fh){
  die("Error, no se puede leer el archivo (tal vez sea demasiado grande)".$filename);
  return;
}

//obtener la lista de vehículos
$sql = "SELECT unidad_id, nombre FROM crm_unidades";
$r = $db->sql_query($sql) or die($sql);
$unidades = array();
while (list($id, $n) = $db->sql_fetchrow($r))
  $unidades[$id] = $n;
//obtener la lista de concesionarias
$sql = "SELECT gid, name FROM groups";
$r = $db->sql_query($sql) or die($sql);
$groups = array();
while (list($id, $n) = $db->sql_fetchrow($r))
    $groups[$id] = $n;
//obtener la lista de satelites y el gid que les corresponde
$sql = "SELECT gid, name FROM groups_satelites";
$r = $db->sql_query($sql) or die($sql);
$satelites = array();
while (list($id, $n) = $db->sql_fetchrow($r)){
	if (!is_array($satelites[$id]))
	  $satelites[$id] = array();
	if (!in_array($n, $satelites[$id])) //checar que no esté ya
	  $satelites[$id][] = $n;//metemos en el array dentro de este id el nombre
}
//los gids que se encuentran
$gid_founds = array();



while(1)
{
    $linea++;
	//leer normalmente
	$data = fgetcsv($fh, 1000, ",");
	if ($linea == 1) continue;
	if (!$data) break;
	$procesados++;
	$data2 = array();
	foreach ($data as $undato){
		$data2[] = addslashes($undato);
	}
	list($email, 
	     $nombre,
	     $clicks
     )= $data2;
    $sql = "SELECT contacto_id FROM crm_contactos WHERE email = '$email'";
    $r = $db->sql_query($sql) or die($sql);
    list($contacto_id) = $db->sql_fetchrow($r);
    if (!$contacto_id) //buscar en finalizados
    {
        $sql = "SELECT contacto_id FROM crm_contactos_finalizados WHERE email = '$email'";
        $r = $db->sql_query($sql) or die($sql);
        list($contacto_id) = $db->sql_fetchrow($r);
    }
    if (!$contaco_id)
    {
        echo "! $email\n";
    }
    else
    {
	echo "% $email\n";
    }
}
?>
