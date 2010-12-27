<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $how_many, $from, $orderby, $gid;



$how_many = 25;
if ($from < 1 || !$from) $from = 0;
// if (!$orderby) $orderby = "nombre";




//buscar todas las concesionarias
$groups = array();
$sql  = "SELECT name, gid FROM groups WHERE 1";
$result = $db->sql_query($sql) or die("Error");
while ( list($name, $gid) = $db->sql_fetchrow($result))
{
  $groups[$gid] = $name;
}

//escoger todas las campañas
$campanas_id = array();
$campanas_id_gid = array();
$campanas_id_nombres = array();
$sql  = "SELECT campana_id, nombre FROM crm_campanas WHERE 1";
$result = $db->sql_query($sql) or die("Error2".print_r($db->sql_error()));
while ( list($campana_id, $nombre) = $db->sql_fetchrow($result))
{
  $campanas_id[] = $campana_id;
  $campanas_id_nombre[$campana_id] = $nombre;
  //ver a quien le pertenece
  $sql  = "SELECT gid FROM crm_campanas_groups WHERE campana_id='$campana_id' LIMIT 1";
  $result2 = $db->sql_query($sql) or die("Error3".print_r($db->sql_error()));
  list($gid) = $db->sql_fetchrow($result2);
  $campanas_id_gid[$campana_id] = $gid;
}
//ya tenemos los datos

//ahora vamos a obtener los valores del progreso
$llamadas_total = array();
$llamadas_nuevos = array();
$porcentajes = array();
//para eso necesitamos las llamadas que están como no realizadas
foreach ($campanas_id AS $campana_id)
{
      $sql = "SELECT id FROM crm_campanas_llamadas WHERE campana_id='$campana_id'";
      $result2 = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));
      $llamadas_total[$campana_id] = $db->sql_numrows($result2);
      
      $sql = "SELECT id FROM crm_campanas_llamadas WHERE campana_id='$campana_id' AND status_id='0'";
      $result2 = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));
      $llamadas_nuevos[$campana_id] = $db->sql_numrows($result2);
      
      if ($llamadas_total[$campana_id]) $porcentajes[$campana_id] = ($llamadas_total[$campana_id] - $llamadas_nuevos[$campana_id])/$llamadas_total[$campana_id] * 100;
      else $porcentajes[$campana_id] = 0;
}

//ordenar por porcentaje
asort($porcentajes);

//desplegar
foreach ($porcentajes AS $campana_id => $porcentaje)
{
//   if ($iii++ > 20) break;
  $nombre = $campanas_id_nombre[$campana_id];
  $gid = $campanas_id_gid[$campana_id];
  $grupo = $groups[sprintf("%04u",$gid)];
  if ($porcentaje == 0) //no hay porcenta
  {
    if (!$llamadas_total[$campana_id]) //no hay a quienes llamar
    {
      $semaforo = "orange";
    }
    else //hay a quienes llamar pero no les han llamado
    {
      $semaforo = "red";
    }
  }
  elseif ($porcentaje == 100) $semaforo = "black";
  else $semaforo = "green";
  
  if ($semaforo) $style_semaforo = " style=\"background:$semaforo;color:white;\"";

  $tabla_campanas .= "<tr class=\"row".(($c++%2)+1)."\"><td>$nombre</td><td>$grupo</td>"
                    ."<td$style_semaforo>$porcentaje</td>"
                    ."<td>{$llamadas_total[$campana_id]}</td>"
                    ."<td$style_semaforo>{$llamadas_nuevos[$campana_id]}</td>"
                      ."</tr>\n";

}


  $tabla_campanas =  "<table width=\"100%\" border=0>\n"
                      ."<thead><tr>"
                      ."<td><a href=\"index.php?_module=$_module&_op=$_op&orderby=nombre\" style=\"color:#ffffff\">Nombre</a></td>"
                      ."<td>Concesionaria</td>"
                      ."<td>Progreso</td>"
                      ."<td>Contactos</td>"
                      ."<td>No utilizados</td>"
                      ."</tr></thead>\n<tbody>"
                      .$tabla_campanas
                      ."</tbody></table>";
                      
/*
global $_admin_menu2;
$_admin_menu2 .= "<table><tr><td></td><td><a href=\"index.php?_module=$_module&_op=campana\"> Crear nueva</a></td></tr>"
                  ."<tr><td></td><td><a href=\"index.php?_module=$_module&_op=gantt\" target=\"gantt\"> Cronograma de campañas</a></td></tr>"
                  ."<tr><td></td><td><a href=\"index.php?_module=$_module&_op=gantt&fecha_ini=".(date("01-m-Y"))."\" target=\"gantt\"> Cronograma de campañas (desde este mes)</a></td></tr>"
                  ."</table>";
*/
?>