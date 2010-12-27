<?php
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db,$gid,$uid;
$buffer='';
if(($gid > 0) && ($uid >0 ))
{
    $sql="SELECT gid,uid,name FROM users WHERE gid=".$gid." AND uid=".$uid." ORDER BY uid;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($gid,$id,$nombre) = $db->sql_fetchrow($res))
        {
            $buffer.="<option value='".$id."' id='".$id."' ".$tmp." >".$gid."  -  ".$id."  -  ".$nombre."</option>";
        }
    }
}
echo $buffer;
die();
?>
