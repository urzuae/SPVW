<?
if (!defined('_IN_MAIN_INDEX')) {
  die ("No puedes acceder directamente a este archivo...");
} 
global $nom, $ap, $am, $mod, $db;
$sql = "SELECT c.contacto_id FROM crm_contactos AS c, crm_prospectos_unidades AS u
        WHERE c.nombre = '$nom' AND
              c.apellido_paterno = '$ap' AND
              c.apellido_materno = '$am' AND
              c.contacto_id = u.contacto_id AND
              u.modelo_id = '$mod'";
$result = $db->sql_query($sql) or die("Error al obtener datos del usuario");
$xml='No existe';
if ($db->sql_numrows($result) > 0)
{
    $xml='existe';
}
die($xml);
?>
