<?php
/******************************************************************************
 *                                                                            *
 * index.php@z2 en esto está basado la funcionalidad del sistema              *
 *                                                                            *
 * Esto funciona de la siguiente manera:                                      *
 *  - Leer la configuracion para obtener unas variables (p.ej. base de datos) *
 *  - Incluir funciones comunes del archivo main.php                          *
 *  - Ejecutar la funcion do_module() o mostrar los modulos                   *
 *   - Tratamos de entrar al directorio del modulo que se pidio ($_module)    *
 *   - Ejecutar $_op.php                                                      *
 *   - Leer, interpretar e imprimir el html/$_op.html                         *
 *    - Si no existia ese html imprimir la variable: $_html (del $_op.php)    *
 *                                                                            * 
 ******************************************************************************/

define('_IN_ADMIN_MAIN_INDEX', '1');


require_once("../config.php");
//definimos algunos directorios para su uso dentro de los htmls
$_includesdir = "../".$_includesdir;
require_once("$_includesdir/main.php");
$_htmldir = "$_module/html";
$_themedir = "../themes/$_theme";
require_once("$_includesdir/main.php");


if ($login == "Login")
{
	$sql = "SELECT admin_id, admin_name, password FROM admins WHERE admin_name='$_POST[admin_name]' AND password=PASSWORD('$_POST[password]')";
	$result = $db->sql_query($sql);
	if ($db->sql_numrows($result) > 0) //si se encontro en la base de datos
	{
		list ($admin_id, $admin_name, $password) = $db->sql_fetchrow($result);
 		setcookie("_admin_id", $admin_id);
 		setcookie("_admin_name", md5($admin_name));
 		setcookie("_admin_password", md5($password));
 		header("location:index.php"); //volver a abrir la pagina
	}
	else 
  
  {
      global $_module, $_op, $_themedir, $_theme;
      if ($_theme != "") {
          include_once("$_themedir/theme.php");
          _theme_before_content();
      }
      //mostramos login fields
      print("<script>alert('Login incorrecto');location.href='index.php';</script>");
      if ($_theme != "")
      _theme_after_content();
  }
}

//en este lugar hay ke usar identificacion del admin
$admin_id = $_COOKIE[_admin_id];
$sql = "SELECT admin_name, password FROM admins WHERE admin_id='$admin_id'";
$result = $db->sql_query($sql);
list($admin_name, $admin_password) = $db->sql_fetchrow($result);

//si no es korrekto pedir ke loggee
if (!($_COOKIE[_admin_name] == md5($admin_name) && $_COOKIE[_admin_password] == md5($admin_password)))
{
    if ($_theme != "") {
        require_once("$_themedir/theme.php");
        global $_admin_menu;
        $_admin_menu = "Por favor ingrese su usuario y contraseña para acceder a esta sección";
        _theme_before_content();
    }
	//mostramos login fields
	print ("<div class=title>Admin login</div><br><table><form action=\"index.php\" method=post>\n"
		. "<tr><td>Admin</td><td><input type=\"text\" name=\"admin_name\"></td></tr>\n"
		. "<tr><td>Password</td><td><input type=\"password\" name=\"password\"></td></tr>\n"
		. "<tr><td colspan=2 align=center><input type=\"submit\" name=\"login\" value=\"Login\"></td></tr></form></table>");
    if ($_theme != "") {
        _theme_after_content();
    }
    die();
}

//todas las variables deklaradas aki 
//inician con underscore ("_") para evitar ke se usen por error
//esta funcion se manda llamar kada ke se kiere hacer algo (siempre)
function do_module($_module) {
	//variables ke estan disponibles en los modulos
	global $PHP_SELF, $_includesdir, $_htmldir, $_themedir, $_op, $_theme, $_html, $_COOKIE;
	
	//si existe el modulo ke estamos buskando empezar
	if (file_exists("$_module")) {
		//si se manda una _op entonces tratar de usar el archivo del 
		//modulo de nombre _op
		//si no hay una definida por default poner index para ke trate 
		//de jalar index.php, .html, etc.
		if (!isset($_op)) $_op = "index";
		//si existe el html o el php entonces existe esta op del modulo 
		//(si no pues no se hace nada)
		if (file_exists("$_module/$_op.php") || file_exists("$_htmldir/$_op.html"))
                {
			//si existe poner el php kon el nombre op
			if (file_exists("$_module/$_op.php"))
				include("$_module/$_op.php");

            if ($_theme != "") {
                include("$_themedir/theme.php");
                global $_admin_menu, $_admin_menu2;
                $_admin_menu = _do_admin_menu();
                _theme_before_content();
            }
			//si existe imprimir el html
			//(e interpretar sus variables) de nombre op
			if (file_exists("$_htmldir/$_op.html")) {
				$_tmpl_file = "$_htmldir/$_op.html";
				$_thefile = implode("", file($_tmpl_file));
                if ($_theme != "") $_thefile = _html_get_body($_thefile); //aseguramos ke no tenga <head>
				//kitar los \' de las '
				for($_i = 0; $_i < strlen($_thefile); $_i++)
				{
				  if($_thefile[$_i] == '"')
				  {
				    $_thefile = substr($_thefile, 0, $_i)."\\\"".substr($_thefile, $_i+1, strlen($_thefile)+1);
				    $_i++;
				  }
				}				
				$_thefile = "\$_r_file=\"".$_thefile."\";";
				eval($_thefile);
				print $_r_file;
			}
			else
            {
                print $_html; //esta variable se imprime si no hay 
                       //un .html, en teoría el .php debe de 
                       //poner su salida aquí
            }
			
            if ($_theme != "")
                _theme_after_content();
		}
		else
			die ("No existe esa operación en el módulo...");

	}else
		die ("No existe ese módulo...");
}


function _do_admin_menu()
{
    $_content .= "<br>";
    //crear el menu de administrador para el tema
    // Loop through the files
    $files = array(
                    "Concesionarias" => "Concesionarias",
                    "Usuarios" => "Usuarios",
                    "Administradores" => "Administradores",
                    "Ciclo de venta" => "Campanas",
                    "Carga de Datos" => "Carga de Datos",
                    "Crear BD Excel" => "Catalogos&_op=genera_db_fuentes",
                    "Catálogo de Fuentes" => "Catalogos&_op=mostrarArbol",
                    "Reasignar Contactos" => "Contactos&_op=contactos",
                    "Cancelar Contactos" => "Contactos&_op=cancelar",
                    "Veh&iacute;culos" => "Modelos",
                    "Monitorear" => "",                        
                    "&nbsp;&nbsp;Asignación" => "Monitoreo&_op=monitoreo_concesionarias_asignacion",
                    "&nbsp;&nbsp;Reasignación" => "Monitoreo&_op=monitoreo_concesionarias_reasignacion",
                    "&nbsp;&nbsp;Seguimiento" => "Monitoreo&_op=seguimiento_prospectos",
                    "&nbsp;&nbsp;Concesionarias" => "Monitoreo&_op=monitoreo_concesionarias",
                  //"&nbsp;&nbsp;Prospectos no asignados a concesionarias" => "Monitoreo&_op=monitoreo_prospectos_no_asignados",
                  //"&nbsp;&nbsp;Concesionarias" => "Monitoreo&_op=monitoreo_concesionarias",
                    "&nbsp;&nbsp;Tasa de conversion" => "Monitoreo&_op=monitoreo_tasa_de_conversion",
                    "Reportes" => "Reportes"
                    );

	foreach ($files AS $key=>$file)
	{
	  if ($file)
	    $_menu .= "<a href=\"$PHP_SELF?_module=$file\">".$key."</a><br>\n";
	  else
	    $_menu .= "".$key."<br>\n";
	}
	$_content .= $_menu;
    $_content .= "<h2>Panel de Control</h2>";
    $files = array(/*"Menus",*/
                    "Documentos de Apoyo",
                    //"Noticias",
                    "Respaldar"
                    );
    foreach ($files as $file)
        $_content .= "<a href=\"$PHP_SELF?_module=$file\">$file</a><br>\n";

    $_content .= "<br><a href=\"$PHP_SELF?_logout=1\">Salir</a>";
    return $_content;

}

//esta todo dentro de una funcion para evitar que se puedan acceder variables 
//no definidas

if (isset($_module)) 
{ 
    if (isset($_COOKIE[_admin_id])) 
        $db->sql_query("UPDATE admins SET last_activity=NOW() WHERE admin_id='".$_COOKIE[_admin_id]."' LIMIT 1")
            or die("Error al actualizar tabla admins");
	do_module($_module);
}
else //mostrar el menu de modulos ke se pueden administrar
{
    if (isset($_logout))
    {
        setcookie ("_admin_password", "", $time-3200);
        setcookie ("_admin_name", "", $time-3200);
        setcookie ("_admin_id", "", $time-3200);
        header("location: $PHP_SELF");
    }
    else
    {
        if ($_theme != "") {
            require_once("$_themedir/theme.php");
            global $_admin_menu;
            $_admin_menu = _do_admin_menu();
            _theme_before_content();
        }
        //mostramos bienvenida
        $sql = "SELECT admin_name, last_login, last_activity, logged_from FROM admins WHERE admin_id='$_admin_id'";
        list($admin, $lastlogin, $lastactivity, $logged_from) = $db->sql_fetchrow($db->sql_query($sql)) or die("Error");
        print("<div class=title>Bienvenido <i>$admin</i></div>Seleccione una opción del menú de la izquierda.");
        print("<br><br><table cellpadding=3><thead><tr><td colspan=2>Registro de actividad de <i>$admin</i></td></tr></thead>");
        print("<tr class=row1><td>Último registro: </td><td><i>$lastlogin</i></td></tr>");
        print("<tr class=row2><td>Última actividad: </td><td><i>$lastactivity</i></td></tr>");
        print("<tr class=row1><td>Se conectó desde: </td><td><i>$logged_from</i></td></tr></table>");
        //actualizar el ultimo login
        $from = $_SERVER['REMOTE_ADDR'];
        if ($_SERVER['REMOTE_HOST'] != "") $from .= "->".$_SERVER['REMOTE_HOST'];
        $db->sql_query("UPDATE admins SET last_login=NOW(), logged_from='$from' WHERE admin_id='$admin_id' LIMIT 1");
        if ($_theme != "") {
            _theme_after_content();
        }
    }
}


?>
