<?

if (!defined('_IN_MAIN_INDEX')) {
    die("No puedes acceder directamente a este archivo...");
}

global $db, $user, $uid;

$sql = "SELECT gid, super FROM users WHERE uid='$uid'";
$result = $db->sql_query($sql) or die("Error");
list($gid, $super) = $db->sql_fetchrow($result);
$gid = sprintf("%04d", $gid);
if ($super > 6) {
    $_html = "<h1>Usted no es un Gerente</h1>";
} else {

    if ($user) {
        $user = $gid . $user; //agregarlo la concesionaria y hacer 0 fill
        $sql = "SELECT user FROM users WHERE user='$user'";
        $r = $db->sql_query($sql) or die($sql);
        if ($db->sql_numrows($r)) {
            $error .= "Este nombre de usuario no está disponible, porfavor introduzca otro.";
        } else {
            $fecha = date("Y-m-d H:i:s");
            $sql = "INSERT INTO users (user,name,gid,password,super)VALUES('" . strtoupper($user) . "','" . strtoupper($user) . "','$gid',PASSWORD('" . strtoupper($user) . "'),'8')";
            $r = $db->sql_query($sql) or die($sql);
            $xuid= $db->sql_nextid();
            $db->sql_query("INSERT INTO users_registry (uid,gid,date_created) VALUES ('$xuid','$gid','$fecha');");
            header("location:index.php?_module=$_module&msg=Usuario $user creado");
        }
    }
}
?>