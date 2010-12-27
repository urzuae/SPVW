<?php
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db,$gid;
$buffer='';
if($gid > 0)
{
    $sql="SELECT gid,name FROM groups WHERE gid=".$gid." ORDER BY gid;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $buffer.="<option value='".$id."' id='".$id."' ".$tmp." >".$id."  -  ".$nombre."</option>";
        }
    }
}
echo $buffer;
die();
?>
