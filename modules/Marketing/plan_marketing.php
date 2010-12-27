<?
    if (!defined('_IN_MAIN_INDEX')) die ("No puedes acceder directamente a este archivo...");
  
    global $db, $campana_id, $gid, $uid, $fecha_ini, $fecha_fin, $origen_id;
    global $_admin_menu2, $_admin_menu;
    
    $result = $db->sql_query("SELECT gid FROM users WHERE uid='$uid' LIMIT 1")
        or die("Error en grupo ".print_r($db->sql_error()));
    
    list($gid, $super) = $db->sql_fetchrow($result);
    
    if($super > 6)
    {
        $_html = "<h1>No tiene los permisos necesarios</h1>";    
    }
    else
    {
        $sql = "SELECT plan_id, title FROM crm_plans WHERE gid='$gid'";
        $result = $db->sql_query($sql) or die ("Database connection error ".$sql);
        $plans = array();
        while(list($plan_id, $title) = $db->sql_fetchrow($result))
        {
            $plans[$plan_id] = $title;
        }
    }
    $count = 1;
    foreach ($plans as $key=>$value) {
        $class = $count % 2 == 0 ? "odd" : "even";
        $plan_table .=
            "<tr class=\"$class\">
                <td></td>
                <td>$value</td>
                <td>Crear</a></td>
                <td>Crear</td>
            </tr>";
        $count++;
    }
    
    
  
?>