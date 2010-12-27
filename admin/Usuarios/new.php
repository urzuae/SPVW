<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $user, $name, $gid, $sgid, $password, $config, $submit, $super_val;
if ($submit) //crear el usuario
{
    if ($db->sql_numrows($db->sql_query("SELECT name FROM users WHERE user='$user'")) > 0)
        $error = "<br>Ese usuario ya esta registrado en el sistema, intenta otro nombre de usuario";
    else 
    {
        //agregar el usuario
        //agregar el usuario
        /*if ($super) $super = 1;
        else $super = 0; */
        $fecha=date("Y-m-d H:i:s");
        $password = strtoupper($password);
        $db->sql_query("INSERT INTO users (`user`, `name`, `gid`, `super`, `password`) VALUES('$user', '$name', '$gid', '$super_val', PASSWORD('$password'))")
        or die("No se pudo agregar el usuario ".print_r($db->sql_error()));
        $uid = $db->sql_nextid();

        $db->sql_query("INSERT INTO users_registry (uid,gid,date_created) VALUES ('$uid','$gid','$fecha');");
        //ahora seteamos la configuración por default (copiando la de uid=0 en la db)
        $result = $db->sql_query("SELECT * FROM users_configs WHERE uid='0' LIMIT 1")
            or die("Error al cargar la configuracion por default");
        $row = $db->sql_fetchrow($result);
        //el primer valor sera el uid, así ke lo kambiamos por el nuevo
        $values = "'$uid'";
        $i = 1;
        while (isset($row[$i]))
        {
            $values .= ", '".$row[$i++]."'";
        }
        $sql = "INSERT INTO users_configs VALUES($values)";
        $db->sql_query($sql) or die("Error al crear configuración por default");
        //si se selexiono la kasilla de configurar entonces redirigir a la de configuración personal
        if ($config) $configurarusuario = "&_op=config&id=$uid";
        else $configurarusuario = "";
        header("location: index.php?_module=$_module$configurarusuario");
    }
}
$gselect = "<select name=gid onchange=\"return check_gid();\">";
$result = $db->sql_query("SELECT gid, name FROM groups WHERE 1 order by gid");
while (list($id, $name) = $db->sql_fetchrow($result))
{
    if (!$i++) $selected = "SELECTED"; //seleccionar el primero
    else $selected = "";
    $gselect .= "<option value=\"$id\" $selected>$id - $name</option>";
}
$gselect .= "</select>";
/*TIPOS DE USUARIOS*/
$select_super = "<select name=super_val>";
$result = $db->sql_query("SELECT tipo_id, nombre FROM users_types WHERE 1 order by tipo_id");
while (list($id, $name) = $db->sql_fetchrow($result))
{
    if (!$i++) $selected = "SELECTED"; //seleccionar el primero
    else $selected = "";
    $select_super .= "<option value=\"$id\" $selected>$name</option>";
}
$select_super .= "</select>";

//
//$sql = "SELECT user, name, gid FROM users WHERE uid='$id' LIMIT 1";
//list($user, $name, $gid) = htmlize($db->sql_fetchrow($db->sql_query($sql)));
//$gselect2 = "<select name=sgid>";
//$result = $db->sql_query("SELECT gid, name FROM groups WHERE order by gid");
//while (list($g_id, $g_name) = $db->sql_fetchrow($result))
//{
//    if ($g_id == 1) continue; //no se puede ser gerente de gerentes
//    if ($g_id == $sgid) $selected = "SELECTED"; //seleccionar el primero
//    else $selected = "";
//    $gselect2 .= "<option value=\"$g_id\" $selected>$g_id - $g_name</option>";
//}
//$gselect2 .= "</select>";
?>