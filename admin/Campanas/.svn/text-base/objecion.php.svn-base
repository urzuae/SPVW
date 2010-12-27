<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit, $campana_id, $objecion_id, $objecion, $titulo;

$sql = "SELECT nombre FROM crm_campanas WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die(print_r($db->sql_error()));
list($campana) = $db->sql_fetchrow($result);

if ($submit)
{
	$sql = "UPDATE crm_campanas_objeciones SET objecion='$objecion' WHERE objecion_id='$objecion_id'";
	$db->sql_query($sql) or die("Error al guardar objecion".print_r($db->sql_error()));
	header("location:index.php?_module=$_module&_op=objeciones&campana_id=$campana_id");
}


$sql = "SELECT objecion_id, titulo, objecion FROM crm_campanas_objeciones WHERE objecion_id='$objecion_id'";
$result = $db->sql_query($sql) or die("Error al cargar objeciones $sql".print_r($db->sql_error()));
list($objecion_id, $titulo, $objecion) = htmlize($db->sql_fetchrow($result));

?>