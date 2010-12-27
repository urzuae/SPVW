<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $gasto_id, $parent_id, $descripcion, $fecha, $proveedor_id, $cantidad, $estimado, $submit, $campana_id;
$fecha = date_reverse($fecha);
setlocale(LC_MONETARY, 'es_MX');

$cantidad = remove_money_format2($cantidad);
$estimado = remove_money_format2($estimado);
if ($parent_id == "") $parent_id = 0;
if (!$gasto_id) //nuevo
{
    if ($submit) //guardar
    {
        $sql = "INSERT INTO crm_gastos (gasto_id, concepto_id, cantidad, estimado, descripcion, fecha, proveedor_id, campana_id)VALUES('', '$parent_id', '$cantidad', '$estimado', '$descripcion', '$fecha', '$proveedor_id', '$campana_id')";
        $db->sql_query($sql) or die("Error al insertar. ".print_r($db->sql_error()));
        header("location:index.php?_module=$_module");
    }
    $msg = "Gasto nuevo";
}
else
{
    if ($submit) //guardar
    {
        $sql = "UPDATE crm_gastos SET cantidad='$cantidad', estimado='$estimado', descripcion='$descripcion', fecha='$fecha', proveedor_id='$proveedor_id', campana_id='$campana_id' WHERE gasto_id='$gasto_id'";
        $db->sql_query($sql) or die("Error al actualizar. ".print_r($db->sql_error()));

        header("location:index.php?_module=$_module");
    }
    //leer valores
    $sql = "SELECT g.descripcion, g.cantidad, g.estimado, g.fecha, g.proveedor_id, c.nombre, g.campana_id FROM crm_gastos AS g, crm_gastos_conceptos AS c WHERE c.concepto_id=g.concepto_id AND g.gasto_id='$gasto_id'";
    $result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
    list($descripcion, $cantidad, $estimado, $fecha, $proveedor_id, $nombre, $campana_id) = htmlize($db->sql_fetchrow($result));
    $fecha = date_reverse($fecha);
    $msg = "Editando gasto de: $nombre";
}
$select_proveedor = "<select name=proveedor_id>\n";
$select_proveedor .= "<option value=\"0\">Ninguno</option>\n";
$sql = "SELECT proveedor_id, nombre FROM crm_gastos_proveedores WHERE 1 ORDER BY nombre";
$result = $db->sql_query($sql) or die("Error en select proveedor");
while (list($proveedor_id2, $nombre) = $db->sql_fetchrow($result))
{
  if ($proveedor_id2 == $proveedor_id) $s = " SELECTED";
  else $s = "";
  $select_proveedor .= "<option value=\"$proveedor_id2\"$s>$nombre</option>\n";
}
$select_proveedor .= "</select>\n";

$select_campana = "<select name=campana_id>\n";
$select_campana .= "<option value=\"0\">Ninguna</option>\n";
$sql = "SELECT campana_id, nombre FROM crm_campanas WHERE 1 ORDER BY nombre";
$result = $db->sql_query($sql) or die("Error en select campana");
while (list($campana_id2, $nombre) = $db->sql_fetchrow($result))
{
  if ($campana_id2 == $campana_id) $s = " SELECTED";
  else $s = "";
  $select_campana .= "<option value=\"$campana_id2\"$s>$nombre</option>\n";
}
$select_campana .= "</select>\n";
$cantidad = money_format2("%d", $cantidad);
$estimado = money_format2("%d", $estimado);
?> 
