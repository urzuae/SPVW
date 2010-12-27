<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $encuesta_id, $descripcion, $nombre, $submit;

if (!$encuesta_id) //nuevo
{
    if ($submit && $descripcion && $nombre) //guardar
    {
        $sql = "INSERT INTO crm_encuestas (nombre, descripcion)VALUES('$nombre', '$descripcion')";
        $r = $db->sql_query($sql) or die("Error al insertar. ".print_r($db->sql_error()));
        $encuesta_id = $db->sql_nextid($r);
        header("location:index.php?_module=$_module&_op=preguntas&encuesta_id=$encuesta_id");
    }
    $msg = "Encuesta nueva";
}
else
{
    if ($submit && $descripcion && $nombre) //guardar
    {
        $sql = "UPDATE crm_encuestas SET nombre='$nombre', descripcion='$descripcion' WHERE encuesta_id='$encuesta_id'";
        $db->sql_query($sql) or die("Error al actualizar. ".print_r($db->sql_error()));
        header("location:index.php?_module=$_module&_op=preguntas&encuesta_id=$encuesta_id");
    }
    //leer valores
    $sql = "SELECT nombre, descripcion FROM crm_encuestas WHERE encuesta_id='$encuesta_id'";
    $result = $db->sql_query($sql) or die("Error al leer.".print_r($db->sql_error()));
    list($nombre, $descripcion) = htmlize($db->sql_fetchrow($result));
    $msg = "Editando encuesta: $nombre";
}

?> 
