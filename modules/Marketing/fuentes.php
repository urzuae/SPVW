<?
  if (!defined('_IN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $uid;

$_css = $_themedir."/style.css";
$_theme = "";
$sql  = "SELECT gid, super FROM users WHERE uid='$uid'";
$result = $db->sql_query($sql) or die("Error");
list($gid, $super) = $db->sql_fetchrow($result);
if ($super > 6)
{
	die("<html><head><script>window.close();</script></head></html>");
}
else
{
    $result2 = $db->sql_query("SELECT fuente_id, nombre FROM crm_fuentes WHERE active='1'") or die("Error");
    if ($db->sql_numrows($result2) > 0)
    {
        $count = 0;
        while(list($fuente_id,$nombre) = $db->sql_fetchrow($result2))
        {
            $class = ++$count % 2 == 0 ? "row1":"row2";
            $rows .=
                "<tr class='$class'>
                    <td>$fuente_id</td>
                    <td>$prospectos</td>
                </tr>";
        }
    }
    else  die("<html><head><script>window.close();</script></head></html>");
}
?>
