<?
  if (!defined('_IN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $uid, $msg;
$sql  = "SELECT gid, super FROM users WHERE uid='$uid'";
$result = $db->sql_query($sql) or die("Error");
list($gid, $super) = $db->sql_fetchrow($result);
if ($super > 6)
{
  $_html = "<h1>Usted no es un Gerente</h1>";
}
else
{ 
  $_html = '<div class=title>Bienvenido al Sistema MFLL Reportes</div><br><br><br>
<table>
    <tr>
        <td class="content" style="width:100%;">
            Para operar el sistema, favor de seleccionar una opción del menú de la izquierda.<br><br>
        </td>
        <td>
            <img src="modules/Bienvenida/html/Pv01_r2_c3.jpg" name="imagen1">
        </td>
    </tr>
</table>
<br><br>';
}
 ?>