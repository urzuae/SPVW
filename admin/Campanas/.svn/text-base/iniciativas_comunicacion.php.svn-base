<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit, $campana_id,
        $actividades_publicidad, $medios, $eventos, $cambios_pagina_web, $patrocinios, $actividades_rp, $marketing_directo, $medio_contacto;


if (!$campana_id)
{
		header("location:index.php?_module=$_module");
}
else //empezar por que tenemos la campaña
{
  if (!$submit)
  {
    $sql = "SELECT nombre FROM crm_campanas WHERE campana_id='$campana_id'";
    $result = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));
    list($nombre) = $db->sql_fetchrow($result);
    $sql = "SELECT actividades_publicidad, medios, eventos, cambios_pagina_web, patrocinios, actividades_rp, marketing_directo, medio_contacto FROM crm_campanas_iniciativas_comunicacion WHERE campana_id='$campana_id'";
    $result = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));
    if ($db->sql_numrows($result) > 0)
    {
      list($actividades_publicidad, $medios, $eventos, $cambios_pagina_web, $patrocinios, $actividades_rp, $marketing_directo, $medio_contacto) = $db->sql_fetchrow($result);
    }
    else //está vacia, insertar uno en blanco
    {
    $sql = "INSERT INTO crm_campanas_iniciativas_comunicacion (campana_id)VALUES('$campana_id')";
      $db->sql_query($sql) or die("Error al agregar grupos".print_r($db->sql_error()));
    } //
  }
	else
  {
		$sql = "UPDATE crm_campanas_iniciativas_comunicacion SET "
      ."actividades_publicidad='$actividades_publicidad', medios='$medios', eventos='$eventos', cambios_pagina_web='$cambios_pagina_web', patrocinios='$patrocinios', actividades_rp='$actividades_rp', marketing_directo='$marketing_directo', medio_contacto='$medio_contacto'"
      ."WHERE campana_id='$campana_id'";
		$db->sql_query($sql) or die("Error al guardar campaña".print_r($db->sql_error()));
 		header("location:index.php?_module=$_module&_op=campana&campana_id=$campana_id");
	}

}
?>