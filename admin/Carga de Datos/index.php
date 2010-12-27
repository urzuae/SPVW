<?
//   if (!defined('_IN_MAIN_INDEX')) {
//     die ("No puedes acceder directamente a este archivo...");
// }
    global $db, $file, $submit, $del;
global $_admin_menu2, $_admin_menu;
// $_admin_menu = " ";
$_admin_menu2 .= "<br>
<a href=\"index.php?_module=$_module&_op=grupos\">Concesionarias</a><br>
<a href=\"index.php?_module=$_module&_op=prospectos_no_asignados_asignar\">Calificaci&oacute;n de prospectos</a><br>
<a href=\"index.php?_module=$_module&_op=usuarios\">Usuarios</a><br>
<a href=\"index.php?_module=$_module&_op=prospectos\">Prospectos</a><br>
<a href=\"index.php?_module=$_module&_op=prospectos_no_asignados\">Prospectos no asignados</a><br>
<a href=\"index.php?_module=$_module&_op=modelos\">Modelos</a><br>";
/*

<a href=\"index.php?_module=$_module&_op=usuarios\">Usuarios</a><br>
 
 */
 ?>
