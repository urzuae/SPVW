<?
if (!defined('_IN_MAIN_INDEX')) {
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
if ($gid == "1") //gerente de zona
{
    if($super == 14)
    {
        $files["Carga de Prospectos"] = "CallcenterNacional&_op=carga_prospectos";
        $files["Monitoreo de Validadores"] = "CallcenterNacional&_op=monitoreo_validadores";
        $files["Compromisos"] = "CallcenterNacional&_op=compromisos";
        $files["Total llamadas realizadas y N&uacute;mero de prospectos trabajados"] = "CallcenterNacional&_op=total_llamadas";
        $files["Gr&aacute;fica de Status"] = "CallcenterNacional&_op=grafica_status";
    }
	elseif ($super == 10 || $super == 9) //menos opciones para este rol
	{
		$files["Outbound"] = "CallcenterNacional&_op=filtrar";
		$files["Inbound"] = "Directorio&_op=seleccionar_concesionaria";
	}
	elseif($super == 2 || $super == 3 )
	{
	    $files["Monitoreo"] = "";
        $files["&nbsp;&nbsp;&nbsp;&nbsp;Tráfico de piso"] = "Zona&_op=monitoreo_trafico_piso";
	    $files["&nbsp;&nbsp;&nbsp;&nbsp;Concesionarias"] = "Zona&_op=monitoreo_concesionarias";
	    $files["&nbsp;&nbsp;&nbsp;&nbsp;Prospectos"] = "Zona&_op=monitoreo_concesionarias_prospectos";
	    $files["Reportes"] = "Zona&_op=zonas";
	}
	else{
		$files["Capturar Prospecto"] = "Directorio&_op=seleccionar_concesionaria";
	}
}
else
{
  if ($super == 4 || $super == 6) 
  {
    $files["Administración de usuarios"] = "Gerente";
    $files["Administración de vendedores"] = "Gerente&_op=administracion_vendedores";
    $files["Regresar a primera etapa"] = "Gerente&_op=regresa_etapa";
    $files["Asignación de prospectos"] = "Gerente&_op=contactos";
    //Changes made by me
    $files["Marketing"] = "Marketing";
    //
    $files["Monitoreo"] = "";
    $files["&nbsp;&nbsp;&nbsp;&nbsp;Vendedores"] = "Gerente&_op=monitoreo_vendedores";
    $files["&nbsp;&nbsp;&nbsp;&nbsp;Alta de prospectos"] = "Gerente&_op=monitoreo_alta_prospectos";
    $files["&nbsp;&nbsp;&nbsp;&nbsp;Prospectos"] = "Gerente&_op=monitoreo_prospectos";
    $files["&nbsp;&nbsp;&nbsp;&nbsp;Tasa de conversión"] = "Gerente&_op=monitoreo_tasa_de_conversion";
    $files["&nbsp;&nbsp;&nbsp;&nbsp;Tasa de conversión x Vendedor"] = "Gerente&_op=monitoreo_tasa_de_conversion_vendedor";
    $files["&nbsp;&nbsp;&nbsp;&nbsp;Envia prospectos a Excel"] = "Gerente&_op=genera_db_fuentes";
    $files["Prospectos finalizados"] = "Gerente&_op=reasignar";
    $files["Carga de prospectos"] = "Gerente&_op=carga_contactos";
    $files["Reportes"] = "Estadisticas";
    //$files["Noticias"] = "Noticias";
  }
  else
  {		//si tiene callcenter, pero no es la primer letra de la cadena (por el ===FALSE)
  	  if ($super == 10 || $super == 12) //menos opciones para este rol
	  	$files = array(
                    	"Capturar prospecto" => "Directorio&_op=contacto"
	                  );  	  
  	  else 
	  	$files = array(
	                  "Prospectos" => "Campanas",
                    "Compromisos" => "Campanas&_op=compromisos",
                    "Capturar prospecto" => "Directorio&_op=contacto"
                    //,"Noticias" => "Noticias"
	                  );
  }
}
foreach ($files AS $key=>$file)
{
  if ($file)
    $_menu .= "<a href=\"$PHP_SELF?_module=$file\">".$key."</a><br>\n";
  else
    $_menu .= "".$key."<br>\n";
  if (($super == 10 || $super == 9) && $key == "Inbound")
  	$_menu .= "<A target=\"virtualshowroom\" href=\"http://www.inhand.carspecs.jato.com/clientsites/inhand/mx.volkswagen.login/default.asp?Dealer=$gid</A><br>\n";//&gid=$gid&uid=$user
  if(($super >=4 && $super <= 6) && $key == "Reportes"){
    $_menu .= "<A target=\"virtualshowroom\" href=\"http://www.inhand.carspecs.jato.com/clientsites/inhand/mx.volkswagen.login/default.asp?Dealer=$gid\">Virtual Show Room</A><br>\n";//&gid=$gid&uid=$user
  }
}
if($super == 8)
{
    $_menu .= "<a href=\"PCS-Manual-Vendedor.pdf\">Manual Vendedor</a><br>\n";
}
if (($super >= 2 && $super <= 6 ) && $gid != 1) //pegar pdfs
{
  $_menu .= "<a href=\"PCS-Manual-Gerente-CRM.pdf\">Manual Gerente CRM</a><br>\n";
  $_menu .= "<a href=\"PCS-Manual-Gerente-Ventas.pdf\">Manual Gerente Ventas</a><br>\n";
  $_menu .= "<a href=\"PCS-Manual-Vendedor.pdf\">Manual Vendedor</a><br>\n";
  $_menu .= "<a href=\"PCS-Manual-Callcenter.pdf\">Manual Call Center</a><br>\n";
  $_menu .= "<a href=\"PCS-Manual-Hostess.pdf\">Manual Hostess</a><br>\n";
}
if ($gid == 1 && $super == 2 )
{
  $_menu .= "<a href=\"PCS-Manual-Gerente-Zona.pdf\">Manual Gerente de Zona</a><br>\n";
}
if($super == 10)
{
  $_menu .= "<a href=\"PCS-Manual-Callcenter.pdf\">Manual Call Center</a><br>\n";
}
if($super == 12)
{
    $_menu .= "<a href=\"PCS-Manual-Hostess.pdf\">Manual Hostess</a><br>\n";
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
//     $_content .= $_menu;
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
//     $_content .= $_menu;
    $_content .= "<br><b>Usuario o password incorrecto</b><br>\n";
    $_content .= "<center><a href=\"javascript: history.go(-1);\" class=\"box_content\">Regresar</a></center><br>\n";
}

?>
