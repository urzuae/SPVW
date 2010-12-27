<?

function _aux_include($file)
{
	global $_content;
	include($file);
}

function _theme_before_content()
{
	  global $_themedir, $_boxdir, $db, $_content, $_site_title, $_admin_menu, $_module, $uid, $_no_boxes, $jquery;
   header("Cache-Control: no-cache");
   /* //comentado todo lo de alertas
    $sql = "SELECT a.gid, a.module FROM users AS u, groups_accesses as a WHERE u.uid='$uid' AND u.gid=a.gid AND module='Alertas'";
    $result = $db->sql_query($sql) or die("Error al consultar accesos de grupos");
    //si estamos en la parte de usuarios checar alarmas
    if (defined('_IN_MAIN_INDEX'))
      if ($db->sql_numrows($result) > 0) //si estamos autorizados a ver alertas entonces mostrarlas
      {
        //checar si tenemos permiso de abrir el módulo
        $sql = "SELECT a.gid FROM groups_accesses AS a, users AS u WHERE u.uid='$uid' AND u.gid=a.gid AND module='Alertas'";
        $result = $db->sql_query($sql) or die("Error");
        
        if ($db->sql_numrows($result) > 0)
          $start_checkForAlerts = "checkForAlerts();";
      }
    */
    if (isset($_admin_menu)) //si es menu administrador solo queremos ese menu
    {
          $_title = "Administración";
          $_content = $_admin_menu;
          if (file_exists("$_themedir/boxleft.html")) {
             $_tmpl_file = "$_themedir/boxleft.html";
             $_thefile = implode("", file($_tmpl_file));
             $_thefile = addslashes($_thefile);
             $_thefile = "\$_r_file=\"".$_thefile."\";";
             eval($_thefile);
             $_boxleft = $_r_file;
          }
          $_boxesleft = $_boxleft;
    }
    else if (!$_no_boxes)//si no crear todas las boxes
    {
	   $_sql = "SELECT `file`, `title` from `boxes` where `status`='1' ORDER BY `order` ASC";
	   $_result = $db->sql_query($_sql);
        //Poner boxes de la izq.
	   while (list($_file, $_title) = $db->sql_fetchrow($_result))
	   {
	      $_content = "";
		  //llenar $_content
		  if (file_exists("$_boxdir/$_file.php")) {
			 //funcion auxiliar para limitar el scope
			 _aux_include("$_boxdir/$_file.php");
		  }
		  else $_content = "Este contenido no ha sido establecido.";
		  //imprimir la box
		  if (file_exists("$_themedir/boxleft.html")) {
			 $_tmpl_file = "$_themedir/boxleft.html";
			 $_thefile = implode("", file($_tmpl_file));
			 $_thefile = addslashes($_thefile);
			 $_thefile = "\$_r_file=\"".$_thefile."\";";
			 eval($_thefile);
			 $_boxleft = $_r_file;
		  }
		  $_boxesleft .= $_boxleft;
	   }
	}
	if (file_exists("$_themedir/header.html")) {
		$_tmpl_file = "$_themedir/header.html";
		$_thefile = implode("", file($_tmpl_file));
		$_thefile = addslashes($_thefile);
		$_thefile = "\$_r_file=\"".$_thefile."\";";
		eval($_thefile);
		print $_r_file;
	}
}

function _theme_after_content()
{	
	global $_themedir, $_boxdir, $db, $_content, $_admin_menu, $_admin_menu2;
	if ((isset($_admin_menu) || defined('_IN_MAIN_INDEX')) && isset($_admin_menu2)){
        if ($_admin_menu2 != "")
        {
          $_title = "Opciones";
          $_content = $_admin_menu2;
          if (file_exists("$_themedir/boxright.html")) {
             $_tmpl_file = "$_themedir/boxright.html";
             $_thefile = implode("", file($_tmpl_file));
             $_thefile = addslashes($_thefile);
             $_thefile = "\$_r_file=\"".$_thefile."\";";
             eval($_thefile);
             $_boxright = $_r_file;
          }
          $_boxesright = $_boxright;
        }
     }
    else if (!$_no_boxes &&defined('_IN_MAIN_INDEX')) //krear boxes der
    {
	   $_sql = "SELECT `file`, `title` from `boxes` where `status`='2' ORDER BY `order` ASC";
	   $_result = $db->sql_query($_sql);
	   while (list($_file, $_title) = $db->sql_fetchrow($_result))
	   {
	           $_content = "";
		  //llenar $_content
		  if (file_exists("$_boxdir/$_file.php")) {
			 _aux_include("$_boxdir/$_file.php");
		  }
		  else $_content = "Este contenido no ha sido establecido.";
		  //imprimir la box
		  if (file_exists("$_themedir/boxright.html")) {
			 $_tmpl_file = "$_themedir/boxright.html";
			 $_thefile = implode("", file($_tmpl_file));
			 $_thefile = addslashes($_thefile);
			 $_thefile = "\$_r_file=\"".$_thefile."\";";
			 eval($_thefile);
			 $_boxright = $_r_file;
		  }
		  $_boxesright .= $_boxright;
	   }
	}
	
	if (file_exists("$_themedir/footer.html")) {
		$_tmpl_file = "$_themedir/footer.html";
		$_thefile = implode("", file($_tmpl_file));
		$_thefile = addslashes($_thefile);
		$_thefile = "\$_r_file=\"".$_thefile."\";";
		eval($_thefile);
		
		print $_r_file;
	}
}

?>