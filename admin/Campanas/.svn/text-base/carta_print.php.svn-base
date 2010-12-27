<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit, $campana_id, $body;
$_theme = "";
if (!$campana_id) header("location: index.php?_module=$_module");


$sql = "SELECT body FROM crm_campanas_cartas WHERE campana_id='$campana_id'";
$result1 = $db->sql_query($sql) or die("Error al cargar carta $sql".print_r($db->sql_error()));

list($body) = ($db->sql_fetchrow($result1));

$body = str_replace("/crm/UserFiles/Image/", "", $body);

$sql = "SELECT nombre, apellido_paterno, apellido_materno FROM crm_campanas_cartas_contactos AS cc, crm_contactos AS c WHERE cc.contacto_id=c.contacto_id AND cc.campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error al cargar carta $sql".print_r($db->sql_error()));
while (list($nombre, $apellido_paterno, $apellido_materno) = $db->sql_fetchrow($result))
{
  $body2 = str_replace("[NOMBRE]", "$nombre $apellido_paterno $apellido_materno", $body);
  $body_all .= "<div style=\"page-break-after:always;\">$body2</div>";
}
$html  = "<html><head></head>$body_all<body></body><html>";

require_once("$_includesdir/dompdf/dompdf_config.inc.php");
$dompdf = new DOMPDF();
$dompdf->set_base_path("../UserFiles/Image");
// $dompdf->set_base_path("/");
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream("carta_$campana_id.pdf");
?>