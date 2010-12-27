<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit, $campana_id, $saludo, $titulo;

if (!$campana_id) header("location: index.php?_module=$_module");

if ($submit)
{
	$sql = "UPDATE crm_campanas SET saludo='$saludo' WHERE campana_id='$campana_id'";
	$db->sql_query($sql) or die("Error al guardar saludo".print_r($db->sql_error()));
 	header("location:index.php?_module=$_module&_op=objeciones&campana_id=$campana_id");
}


$sql = "SELECT nombre, saludo FROM crm_campanas WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error al cargar saludos $sql".print_r($db->sql_error()));
list($campana, $saludo) = htmlize($db->sql_fetchrow($result));

?>