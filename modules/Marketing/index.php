<?
    //File accessability
    if (!defined('_IN_MAIN_INDEX')) die ("No puedes acceder directamente a este archivo...");
    
    global $db, $campana_id, $gid, $uid;
    global $_admin_menu2, $_admin_menu;

    $_html .= "<h1>Selecciona una opcion</h1>";

    $_admin_menu2 .= "<br/>
        <a href=\"index.php?_module=$_module&_op=plan_marketing\">Planes de Acciones</a><br/>
        <br/>";
 ?>
