<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit, $campana_id, $target, $valor;

if (!$campana_id) header("location: index.php?_module=$_module");
$sql = "SELECT nombre FROM crm_campanas WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));
list($campana) = $db->sql_fetchrow($result);


if ($submit && $target && $valor)
{
	$sql = "INSERT INTO crm_campanas_targets (campana_id, target, valor)VALUES('$campana_id', '$target', '$valor')";
	$db->sql_query($sql) or die("Error al guardar".print_r($db->sql_error()));
	header("location:index.php?_module=$_module&_op=campana&campana_id=$campana_id");
}


?>