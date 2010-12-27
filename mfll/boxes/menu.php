<?
if (!defined('_IN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $_module, $_op, $_do_login,$_modulesdir;
$uid = $_COOKIE[_uid];
if ($uid)
    list($gid) = htmlize($db->sql_fetchrow($db->sql_query("SELECT gid FROM users WHERE uid='$uid' LIMIT 1")))
    or die ("Error al buscar el grupo");
else
    $gid = 0;

$_menu .= "<br>\n";

$result = $db->sql_query("SELECT user, name, super, gid FROM users WHERE uid='$uid'")
               or die("Error al buscar nombre de usuario");

list($user, $name, $super, $gid) = htmlize($db->sql_fetchrow($result));

  if ($super == 14 || $super == 12 || $super == 10 || $super == 6 || $super == 4 || $super == 3 || $super == 2 ||$gid == 1)
  {
    $files["Autorizar Carga"] = "Gerente&_op=filtros";
    $files["Reportes"] = "#";
    $files["&nbsp;&nbsp;&nbsp;&nbsp;Por fase"] = "Gerente&_op=graficas&tipo=1";
    $files["&nbsp;&nbsp;&nbsp;&nbsp;Concesionaria"] = "Gerente&_op=graficas&tipo=2";
    $files["&nbsp;&nbsp;&nbsp;&nbsp;(Maneja - Firma)"] = "Gerente&_op=graficas_f&tipo=1";
    $files["&nbsp;&nbsp;&nbsp;&nbsp;(Compran - Manejan)"] = "Gerente&_op=graficas_p&tipo=1";
    $files["Busqueda de Prospectos"] = "Gerente&_op=consultas";    
  }
if(count($files)>0)
{
    foreach ($files AS $key=>$file)
    {
        if ($file)
            $_menu .= "<a href=\"$PHP_SELF?_module=$file\">".$key."</a><br>\n";
    }
}
if ($user)
    {
        $_content .= $_menu;
        $_content .= "<br>Bienvenido <br><i>$name</i><br>";
        if ($gid == 1 && $super == 2 )
            $_content .= ($super?"Coordinador de Gerentes de Zona":"Gerente de Zona")."<br>\n";
        elseif ($gid == 1 && ($super == 10 ||$super == 9 ))
            $_content .= "Callcenter Nacional<br>\n";
        elseif ($super == 10)
            $_content .= "Callcenter. Concesionaria $gid<br>\n";
        elseif ($super == 12)
            $_content .= "Hostess. Concesionaria $gid<br>\n";
        elseif($super == 6 || $super == 4)
            $_content .= ($super?"Gerente de la ":"")."Concesionaria $gid<br>\n";
        $_content .= "<br><br><a href=\"index.php?_do_logout=1\" class=\"box_content\">Salir</a><br>\n";
    }
else if (!$_do_login)
    {
        $_content .= "\n"//<br>Entre a tu cuenta para poder ver más opciones:<br>
            . "<form action=\"index.php\" method=post>\n"
            . "<table style=\"width:100%;\">\n"
            . "<tr><td><input type=\"hidden\" name=\"_module\" value=\"$_module\"></td></tr>"
            . "<tr><td><input type=\"hidden\" name=\"_op\" value=\"$_op\"></td></tr>"
            . "<tr><td>Usuario</td></tr>\n"
            . "<tr><td><input type=\"text\" name=\"_user\" style=\"width:100%;color:black;\"></td></tr>\n"
            . "<tr><td>Password</td></tr>\n"
            . "<tr><td><input type=\"password\" name=\"_password\" style=\"width:100%\"></td></tr>\n"
            . "<tr><td><center><input type=\"submit\" name=\"_do_login\" value=\"Login\"></center></td></tr>"
            . "</table></form>";
    }
else
    {
        $_content .= "<br><b>Usuario o password incorrecto</b><br>\n";
        $_content .= "<center><a href=\"javascript: history.go(-1);\" class=\"box_content\">Regresar</a></center><br>\n";
    }
?>
