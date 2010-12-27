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
        $password = strtoupper($password);
        $db->sql_query("INSERT INTO users (user, name, password,gid,super) VALUES('$user', '$name', PASSWORD('$password'),'1','6')")
            or die("No se pudo agregar el usuario ".print_r($db->sql_error()));
        $uid = $db->sql_nextid();
        $leyenda="Se Grabar&oacute;n los datos con exito";
    }
}
?>
