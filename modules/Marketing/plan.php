<?
    if(!defined('_IN_MAIN_INDEX')) die ("No puedes acceder directamente a este archivo...");

    global $db, $uid, $gid, $msg, $pid;
    
    if ($msg) $_html .= "<script>alert('$msg');</script>";
    
    $sql = "SELECT title, sent_date FROM crm_plans WHERE plan_id='$pid'";
    $result = $db->sql_query($sql) or die($sql);
    list($title, $date) = $db->sql_fetchrow($result);
    
    
?>