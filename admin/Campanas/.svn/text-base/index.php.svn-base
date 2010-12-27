<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $how_many, $from, $orderby, $del, $copyc, $gid;

if ($del)
{
    $sql = "DELETE FROM crm_campanas_objeciones WHERE campana_id='$del'";
    $db->sql_query($sql) or die("Error al borrar campaña");
    $sql = "DELETE FROM crm_campanas_groups WHERE campana_id='$del' LIMIT 1";
    $db->sql_query($sql) or die("Error al borrar campaña");
    $sql = "DELETE FROM crm_campanas_targets WHERE campana_id='$del'";
    $db->sql_query($sql) or die("Error al borrar campaña");
    $sql = "DELETE FROM crm_campanas_iniciativas_comunicacion WHERE campana_id='$del'";
    $db->sql_query($sql) or die("Error al borrar campaña");
    $sql = "DELETE FROM crm_campanas_promociones WHERE campana_id='$del'";
    $db->sql_query($sql) or die("Error al borrar campaña");
    $sql = "DELETE FROM crm_campanas_aprendizaje WHERE campana_id='$del'";
    $db->sql_query($sql) or die("Error al borrar campaña");
    //depende del cliente si se borran el log de llamadas
    $sql = "DELETE FROM crm_campanas WHERE campana_id='$del' LIMIT 1";
    $db->sql_query($sql) or die("Error al borrar campaña");
}
else if ($copyc)
{
    $sql = "INSERT INTO crm_campanas (nombre, objetivos, objetivos_especificos, descripcion, concepto, comentarios, fecha_ini, fecha_fin, lugar, saludo, presupuesto, beneficios) SELECT nombre, objetivos, objetivos_especificos, descripcion, concepto, comentarios, fecha_ini, fecha_fin, lugar, saludo, presupuesto, beneficios FROM crm_campanas WHERE campana_id='$copyc'";
    $db->sql_query($sql) or die("Error al copiar campaña");
    $campana_id = $db->sql_nextid();
    $sql = "SELECT gid FROM crm_campanas_groups WHERE campana_id='$copyc'";
    $result = $db->sql_query($sql) or die("Error al copiar campaña");
    while (list($gid) = $db->sql_fetchrow($result))
    {
      $sql = "INSERT INTO crm_campanas_groups (campana_id, gid)VALUES('$campana_id', '$gid')";
      $db->sql_query($sql) or die("Error al copiar campaña");
    }
    
    $sql = "SELECT target, valor FROM crm_campanas_targets WHERE campana_id='$copyc'";
    $result = $db->sql_query($sql) or die("Error al copiar campaña");
    while (list($target, $valor) = $db->sql_fetchrow($result))
    {
      $sql = "INSERT INTO crm_campanas_targets (campana_id, target, valor)VALUES('$campana_id', '$target', '$valor')";
      $db->sql_query($sql) or die("Error al copiar campaña");
    }
    $sql = "INSERT INTO crm_campanas_iniciativas_comunicacion (campana_id, actividades_publicidad, medios, eventos, cambios_pagina_web, patrocinios, actividades_rp, marketing_directo, medio_contacto) SELECT '$campana_id', actividades_publicidad, medios, eventos, cambios_pagina_web, patrocinios, actividades_rp, marketing_directo, medio_contacto FROM crm_campanas_iniciativas_comunicacion WHERE campana_id='$copyc'";
    
    $db->sql_query($sql) or die("Error al copiar campaña");
    $sql = "INSERT INTO crm_campanas_promociones (campana_id, tipo, productos, fecha_ini, fecha_fin, objetivo, mecanica, proceso) SELECT '$campana_id', tipo, productos, fecha_ini, fecha_fin, objetivo, mecanica, proceso FROM crm_campanas_promociones WHERE campana_id='$copyc'";
    
    $db->sql_query($sql) or die("Error al copiar campaña");
    
    $sql = "SELECT actividad, solucion FROM crm_campanas_aprendizaje WHERE campana_id='$copyc'";
    $result = $db->sql_query($sql) or die("Error al copiar campaña");
    while (list($actividad, $solucion) = $db->sql_fetchrow($result))
    {
      $sql = "INSERT INTO crm_campanas_aprendizaje (campana_id, actividad, solucion)VALUES('$campana_id', '$actividad', '$solucion')";
      $db->sql_query($sql) or die("Error al copiar campaña");
    }
}
$how_many = 25;
if ($from < 1 || !$from) $from = 0;
if (!$orderby) $orderby = "nombre";

$sql_index = "SELECT c.campana_id, c.nombre, c.fecha_ini, c.fecha_fin FROM crm_campanas AS c, crm_campanas_groups AS g WHERE c.campana_id=g.campana_id AND g.gid='$gid' ORDER BY $orderby, campana_id LIMIT $from, $how_many";
$result = $db->sql_query($sql_index) or die("Error al consultar campañas");
if ($db->sql_numrows($result))
{
  $tabla_campanas .= "<table width=\"100%\" border=0>\n";
  $tabla_campanas .= "<thead><tr>"
                      ."<td><a href=\"index.php?_module=$_module&_op=$_op&orderby=nombre\" style=\"color:#ffffff\">Nombre</a></td>"
  //                     ."<td><a href=\"index.php?_module=$_module&_op=$_op&orderby=fecha_ini\" style=\"color:#ffffff\">Inicio</a></td>"
  //                     ."<td><a href=\"index.php?_module=$_module&_op=$_op&orderby=fecha_fin\" style=\"color:#ffffff\">Fin</a></td>"
                      ."<td>Contactos</td>"
                      ."<td>No utilizados</td>"
                      ."<td colspan=3>Acción</td></tr></thead>\n";
  while (list($campana_id, $name, $fecha_ini, $fecha_fin) =
          htmlize($db->sql_fetchrow($result)))
  {
      //quiero saber cuantas llamadas tiene y cuantas tiene sin tocar
      $sql = "SELECT id FROM crm_campanas_llamadas WHERE campana_id='$campana_id'";
      $result2 = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));
      $campana_llamadas_total = $db->sql_numrows($result2);
      $sql = "SELECT id FROM crm_campanas_llamadas WHERE campana_id='$campana_id' AND status_id='0'";
      $result2 = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));
      $campana_llamadas_nuevos = $db->sql_numrows($result2);
      $semaforo = "";
      if ($campana_llamadas_total)
      {
        if ($campana_llamadas_total == $campana_llamadas_nuevos) //no hay avance
        {
          $semaforo = "red";
        }
        else //ya hay avance
        {
          $semaforo = "green";
        }
      }
      if ($semaforo) $style_semaforo = " style=\"background:$semaforo;color:white;\"";
      else $style_semaforo = "";
      $tabla_campanas .= "<tr class=\"row".(($c++%2)+1)."\"><td>$name</td><td>$campana_llamadas_total</td><td$style_semaforo>$campana_llamadas_nuevos</td>"
                          ."<td style=\"width:1px;\"><a href=\"index.php?_module=$_module&_op=campana&campana_id=$campana_id\"><img src=\"../img/edit.gif\" onmouseover=\"return escape('Editar')\"  border=0></a></td>"
                          .(($campana_id>0)?"<td style=\"width:1px;\"><a href=\"index.php?_module=$_module&_op=$_op&copyc=$campana_id\"><img src=\"../img/copy.gif\" onmouseover=\"return escape('Copiar')\"  border=0></a></td>":"<td> </td>")
                          ."<td style=\"width:1px;\">"
                          .(($campana_id>0)?"<a href=\"javascript: del('$campana_id','$name');\" ><img src=\"../img/del.gif\" onmouseover=\"return escape('Borrar')\" border=0></a>":"")
                          ."</td>"
                          ."</tr>\n";
  }
  $tabla_campanas .= "</table>\n";
}

$select_groups = "<select name=\"gid\" onchange=\"document.forms[0].submit();\">";
$result = $db->sql_query("SELECT gid,name FROM groups WHERE 1 ORDER BY gid") or die("Error al cargar grupos");
$select_groups .= "<option value=\"\">Selecciona una concesionaria</option>";
while(list($_gid,$name) = $db->sql_fetchrow($result))
{
  if ($_gid == $gid)
    $selected = " SELECTED";
  else
    $selected = "";
  $select_groups .= "<option value=\"$_gid\"$selected>$_gid - $name</option>";
}
$select_groups .= "</select>";

    $result = $db->sql_query($sql_index) or die("Error al cargar campañas");
    $num_news = $db->sql_numrows($result);
    if ($num_news > $how_many)
    {
        if ($from > 0) 
            $paginacion_campanas .= "<a href=\"index.php?_module=$_module&orderby=$orderby&from=".($from - $how_many)."\">&lt;</a>&nbsp;";
        for ($i = 0; $i < $num_news; $i += $how_many)
        {
            $j++;
            if (!($i >= $from && $i <= ($from + $how_many - 1)))
            {
                $link1 = "<a href=\"index.php?_module=$_module&orderby=$orderby&from=".($i)."\">";
                $link2 = "</a>";
            }
            else $link1 = $link2 = "";
            $paginacion_campanas .= "&nbsp;$link1$j$link2";
        }
        if (($from + $how_many) < $num_news) 
            $paginacion_campanas .= "&nbsp;<a href=\"index.php?_module=$_module&orderby=$orderby&from=".($from + $how_many)."\">&gt;</a>";
    }

global $_admin_menu2;//<img src=\"../img/new.gif\" border=0>
$_admin_menu2 .= "<table><tr><td></td><td><a href=\"index.php?_module=$_module&_op=campana\"> Crear nueva</a></td></tr>"
                  ."<tr><td></td><td><a href=\"index.php?_module=$_module&_op=progreso\"> Progreso</a></td></tr>"
//                   ."<tr><td></td><td><a href=\"index.php?_module=$_module&_op=gantt\" target=\"gantt\"> Cronograma de campañas</a></td></tr>"
//                   ."<tr><td></td><td><a href=\"index.php?_module=$_module&_op=gantt&fecha_ini=".(date("01-m-Y"))."\" target=\"gantt\"> Cronograma de campañas (desde este mes)</a></td></tr>"
                  ."</table>";

?>