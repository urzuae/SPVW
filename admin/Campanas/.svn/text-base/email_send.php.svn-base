<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit, $campana_id, $body;
if (!$campana_id) header("location: index.php?_module=$_module");


$sql = "SELECT subject, body FROM crm_campanas_emails WHERE campana_id='$campana_id'";
$result1 = $db->sql_query($sql) or die("Error al cargar email $sql".print_r($db->sql_error()));

list($subject, $body) = ($db->sql_fetchrow($result1));

$body = str_replace("/crm/UserFiles/Image/", "/crm/UserFiles/Image/", $body);
$_html .= "<h1>Enviados</h1>";
$sql = "SELECT email, nombre, apellido_paterno, apellido_materno FROM crm_campanas_emails_contactos AS cc, crm_contactos AS c WHERE cc.contacto_id=c.contacto_id AND cc.campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error al cargar email $sql".print_r($db->sql_error()));
while (list($email, $nombre, $apellido_paterno, $apellido_materno) = $db->sql_fetchrow($result))
{
  $body2 = str_replace("[NOMBRE]", "$nombre $apellido_paterno $apellido_materno", $body);
  $subject2 = str_replace("[NOMBRE]", "$nombre $apellido_paterno $apellido_materno", $subject);
  mail($email, $subject2, $body2);
  usleep(50);//para evitar que reporten como spam
  
  $_html .= "$email<br>";
}
$_html .= "<br><br><center><input value=\"Regresar\" onclick=\"location.href='index.php?_module=$_module&_op=email_contactos&campana_id=$campana_id'\" type=\"button\"></center>";
?>