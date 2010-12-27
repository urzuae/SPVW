<?
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
    global $db, $file, $submit, $del;
global $_admin_menu2, $_admin_menu;
// $_admin_menu = " ";
$_admin_menu2 .= "<br>
<a href=\"index.php?_module=$_module&_op=contactos_modificados\" >Contactos modificados</a><br>
<a href=\"index.php?_module=$_module&_op=encuestas\" >Encuestas</a><br>
";
 ?>