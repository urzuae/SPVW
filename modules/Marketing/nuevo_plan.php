<?

    if (!defined('_IN_MAIN_INDEX')) die("No puedes acceder directamente a este archivo...");

    global $db, $uid, $year, $month, $pid;

    $sql = "SELECT gid, super FROM users WHERE uid='$uid'";
    $result = $db->sql_query($sql) or die("Error");
    list($gid, $super) = $db->sql_fetchrow($result);
    if ($super > 6)
    {
        $_html = "<h1>Usted no es un Gerente</h1>";
        header("location:index.php?_module=$_module&msg=$month creado");    
    }
    else
    {
            $title =  "Plan de acciones de Marketing del mes de ".$month." ".$year;
            $sql = "SELECT title FROM crm_plans WHERE title='$title'";
            $r = $db->sql_query($sql) or die($sql);
            if ($db->sql_numrows($r))
            {
                $error .= "El plan de marketing para este mas ya ha sido creado.";
            }
            else
            {
                $fecha = date("Y-m-d");
                $sql = "INSERT INTO crm_plans (title, sent_date, gid) VALUES ('$title', '$fecha', '$gid')";
                $r = $db->sql_query($sql) or die($sql);
                $sql = "SELECT plan_id FROM crm_plans WHERE title='$title'";
                $r2 = $db->sql_query($sql) or die($sql);
                list($pid) = $db->sql_fetchrow($r2);
                header("location:index.php?_module=$_module&_op=plan&pid=$pid&msg=$title creado");
            }
    }
?>