<?php

require_once("$_includesdir/mail.php");
global $db;
$fn = "finalizados.csv";
$arg = $argv[2]; //php.exe script nombre_de_script
$num_2_3_meses = array();
$num_4_5_meses = array();
$num_5_mas_meses = array();
$gids = array();
$contacto_ids = array();
$where_2_meses = " AND fecha_importado < '2008-11-1 00:00:00'";
$where_2_3_meses = "AND fecha_importado BETWEEN '2008-09-1 00:00:00' AND '2008-11-1 00:00:00'";
$where_4_5_meses = "AND fecha_importado BETWEEN '2008-07-1 00:00:00' AND '2008-09-1 00:00:00'";
$where_5_mas_meses = "AND fecha_importado < '2008-07-1 00:00:00'";
//seleccionar los prospectos que fueron importados hace 3 meses y no tienen un vendedor asignado
$sql = "SELECT contacto_id, gid FROM crm_contactos WHERE uid='0' AND gid!='0' $where_2_3_meses";
$r = $db->sql_query($sql) or die($sql);
while (list($contacto_id, $gid) = $db->sql_fetchrow($r))
{
	$num_2_3_meses[$gid]++;
	if (!in_array($gid, $gids))
		$gids[] = $gid;
	$contacto_ids[] = $contacto_id;
}

$sql = "SELECT contacto_id, gid FROM crm_contactos WHERE uid='0' AND gid!='0' $where_4_5_meses";
$r = $db->sql_query($sql) or die($sql);
while (list($contacto_id, $gid) = $db->sql_fetchrow($r))
{
	$num_4_5_meses[$gid]++;
	if (!in_array($gid, $gids))
		$gids[] = $gid;
	$contacto_ids[] = $contacto_id;	
}

$sql = "SELECT contacto_id, gid FROM crm_contactos WHERE uid='0' AND gid!='0' $where_5_mas_meses";
$r = $db->sql_query($sql) or die($sql);
while (list($contacto_id, $gid) = $db->sql_fetchrow($r))
{
	$num_5_mas_meses[$gid]++;
	if (!in_array($gid, $gids))
		$gids[] = $gid;
	$contacto_ids[] = $contacto_id;
}
sort($gids);
$fp = fopen($fn, "w");
if (!$fp) die("No se pudo abrir el archivo $fn");
fputcsv($fp, "Concesionaria", "2-3 meses", "4-5 meses", "5+ meses");
foreach ($gids AS $gid)
{
	fputcsv($fp, array($gid,$num_2_3_meses[$gid],$num_4_5_meses[$gid],$num_5_mas_meses[$gid]));
	echo "$gid,{$num_2_3_meses[$gid]},{$num_4_5_meses[$gid]},{$num_5_mas_meses[$gid]}\n";
}
fclose($fp);
$motivo = "Prospectos sin atender por el vendedor";
$motivo_id = 0;
foreach($contacto_ids AS $contacto_id)
{

  $sql = "INSERT INTO crm_prospectos_cancelaciones (contacto_id, uid, motivo, motivo_id)VALUES('$contacto_id', '0', '$motivo', '$motivo_id')";
  $db->sql_query($sql) or die($sql.print_r($db->sql_error()));
  //actualizar el status como finalizad
  $sql = "INSERT INTO crm_contactos_finalizados Select * from crm_contactos where contacto_id = '$contacto_id'";
  $db->sql_query($sql) or die($sql.print_r($db->sql_error()));
  $sql = "INSERT INTO crm_campanas_llamadas_finalizadas Select * from crm_campanas_llamadas where contacto_id = '$contacto_id'";
  $db->sql_query($sql) or die($sql.print_r($db->sql_error()));
  $sql = "delete from crm_contactos where contacto_id = '$contacto_id'";
  echo $sql."\n";
  $db->sql_query($sql) or die($sql.print_r($db->sql_error()));
  $sql = "delete from crm_campanas_llamadas where contacto_id = '$contacto_id'";
  $db->sql_query($sql) or die($sql.print_r($db->sql_error()));
}
/**/
?>