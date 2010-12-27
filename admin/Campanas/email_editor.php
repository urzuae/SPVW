<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit, $campana_id, $subject, $body;

if (!$campana_id) header("location: index.php?_module=$_module");


$sql = "SELECT subject, body FROM crm_campanas_emails WHERE campana_id='$campana_id'";
$result1 = $db->sql_query($sql) or die("Error al cargar email $sql".print_r($db->sql_error()));

if ($submit)
{
  if ($db->sql_numrows($result1) > 0 ) //ya hay algo en la db
  {
	  $sql = "UPDATE crm_campanas_emails SET subject='$subject', body='$body' WHERE campana_id='$campana_id'";
	  $db->sql_query($sql) or die("Error al guardar email".print_r($db->sql_error()));
  }
  else
  {
    $sql = "INSERT INTO crm_campanas_emails (campana_id, subject, body)VALUES('$campana_id', '$subject', '$body')";
    $db->sql_query($sql) or die("Error al guardar email".print_r($db->sql_error()));
  }
 	header("location:index.php?_module=$_module&_op=email_contactos&campana_id=$campana_id");
}


$sql = "SELECT nombre FROM crm_campanas WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error al cargar saludos $sql".print_r($db->sql_error()));
list($campana) = htmlize($db->sql_fetchrow($result));

list($subject, $body) = ($db->sql_fetchrow($result1));
list($subject) = htmlize(array($subject));
$editor = fckeditor("400", "body", $body);
?>