<?
    if(!defined('_IN_MAIN_INDEX')) die ("No puedes acceder directamente a este archivo...");

    global $db, $uid, $msg, $pid;
    
    if ($msg) $_html .= "<script>alert('$msg');</script>";
    
    //Select plan attributes
    $sql = "SELECT title, sent_date, gid, target, investment FROM crm_plans WHERE plan_id='$pid'";
    $result = $db->sql_query($sql) or die($sql);
    list($title, $date, $gid, $targ, $invmt) = $db->sql_fetchrow($result);
    $invmt = "$".substr((string)$invmt, 0 , -3).",".substr((string)$invmt, -3);
    
    //Select Gerente de CRM
    $sql = "SELECT name FROM users WHERE gid='$gid' AND super='6'";
    $r2 = $db->sql_query($sql) or die($sql);
    list($gerente_gral) = $db->sql_fetchrow($r2);
    
    //Select gerente de ventas
    $sql = "SELECT name FROM users WHERE gid='$gid' AND super='4'";
    $r2 = $db->sql_query($sql) or die($sql);
    list($gerente_vtas) = $db->sql_fetchrow($r2);
    
    //Select information concesionaria
    $sql = "SELECT name FROM groups WHERE gid='$gid'";
    $r = $db->sql_query($sql) or die($sql);
    list($razon_social) = $db->sql_fetchrow($r);
    
    //Select actions within plan
    $sql = "SELECT name, fuente_id, initial_date, final_date, place, target, media, budget, prospects, benefits
        FROM crm_plan_actions WHERE plan_id='$pid'";
    $result1 = $db->sql_query($sql) or die($sql);
    $action_list = "";
    while(list($name, $fuente_id, $initial_date, $final_date, $place, $target, $media, $budget, $prospects, $benefits) = $db->sql_fetchrow($result1))
    {
        $sql = "SELECT nombre FROM crm_fuentes WHERE fuente_id='$fuente_id'";
        $result2 = $db->sql_query($sql) or die($sql);
        $fuente_nombre = $db->sql_fetchrow($result2);
        $action_list .=
            "<tr>
                <td>$name</td>
                <td>$fuente_nombre</td>
                <td>$initial_date</td>
                <td>$place</td>
                <td>$target</td>
                <td>$media</td>
                <td>$budget</td>
                <td>$prospects</td>
                <td>$benefits</td>
            </tr>";
    }
    if($action_list->strlen == 0)
        $action_list = "<td style=\"text-align:center;\">No hay acciones actualmente para este plan.</td>";
?>