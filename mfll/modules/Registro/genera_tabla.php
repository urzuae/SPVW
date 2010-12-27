<?php
if (!defined('_IN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $gid;
$buffer='No existen vendedores en la Concesionaria seleccionada';
#$sql="SELECT uid,name FROM users WHERE gid=".$gid." AND super=8 ORDER BY name;";
$sql="SELECT a.uid,b.name FROM mfll_eventos_vendedores as a LEFT JOIN users as b ON a.uid=b.uid WHERE a.gid='".$gid."';";
$res=$db->sql_query($sql);
if($db->sql_numrows($res)>0)
{
    $buffer="<select name='radiobutton' id='radiobutton'>";
    while(list($id,$name) = $db->sql_fetchrow($res))
    {
        $buffer.="<option value='".$id."'>".$name."</option>";
    }
    $buffer.="</select>";
}
echo $buffer;
die();
?>
