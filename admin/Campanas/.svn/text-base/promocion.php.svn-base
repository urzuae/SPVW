<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit, $campana_id,
        $tipo, $productos, $fecha_ini, $fecha_fin, $objetivo, $mecanica, $proceso;

$fecha_ini = date_reverse($fecha_ini);
$fecha_fin = date_reverse($fecha_fin);
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
    $sql = "SELECT tipo, productos, fecha_ini, fecha_fin, objetivo, mecanica, proceso FROM crm_campanas_promociones WHERE campana_id='$campana_id'";
    $result = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));
    if ($db->sql_numrows($result) > 0)
    {
      list($tipo, $productos, $fecha_ini, $fecha_fin, $objetivo, $mecanica, $proceso) = $db->sql_fetchrow($result);
    }
    else //está vacia, insertar uno en blanco
    {
    $sql = "INSERT INTO crm_campanas_promociones (campana_id)VALUES('$campana_id')";
      $db->sql_query($sql) or die("Error al agregar grupos".print_r($db->sql_error()));
    } //
  }
	else
  {
		$sql = "UPDATE crm_campanas_promociones SET "
          ."tipo='$tipo', productos='$productos', fecha_ini='$fecha_ini', fecha_fin='$fecha_fin', objetivo='$objetivo', mecanica='$mecanica', proceso='$proceso' "
      ."WHERE campana_id='$campana_id'";
		$db->sql_query($sql) or die("Error al guardar campaña".print_r($db->sql_error()));
 		header("location:index.php?_module=$_module&_op=campana&campana_id=$campana_id");
	}

}

$fecha_ini = date_reverse($fecha_ini);
$fecha_fin = date_reverse($fecha_fin);
?>