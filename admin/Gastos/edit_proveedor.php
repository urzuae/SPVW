<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $proveedor_id, $descripcion, $direccion, $rfc, $nombre, $submit;

if (!$proveedor_id) //nuevo
{
    if ($submit) //guardar
    {
        $sql = "INSERT INTO crm_gastos_proveedores (proveedor_id, nombre, rfc, direccion, descripcion)VALUES('',  '$nombre', '$rfc', '$direccion', '$descripcion')";
        $db->sql_query($sql) or die("Error al insertar. ".print_r($db->sql_error()));
        header("location:index.php?_module=$_module");
    }
    $msg = "Proveedor nueva";
}
else
{
    if ($submit) //guardar
    {
        $sql = "UPDATE crm_gastos_proveedores SET nombre='$nombre', rfc='$rfc', direccion='$direccion', descripcion='$descripcion' WHERE proveedor_id='$proveedor_id'";
        $db->sql_query($sql) or die("Error al actualizar. ".print_r($db->sql_error()));

        header("location:index.php?_module=$_module");
    }
    //leer valores
    $sql = "SELECT nombre, rfc, direccion, descripcion FROM crm_gastos_proveedores WHERE proveedor_id='$proveedor_id'";
    $result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
    list($nombre, $rfc, $direccion, $descripcion) = htmlize($db->sql_fetchrow($result));
    $msg = "Editando Proveedor: $nombre";
}

?> 
