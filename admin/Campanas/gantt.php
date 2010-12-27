<?
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
  die ("No puedes acceder directamente a este archivo...");
}
global $db, $fecha_ini, $fecha_fin;
include("$_includesdir/jpgraph/jpgraph.php");
include("$_includesdir/jpgraph/jpgraph_gantt.php");
if ($fecha_ini)
{
  $titulo .= " desde $fecha_ini";
  $fecha_ini = date_reverse($fecha_ini);
  $and_fecha .= " AND fecha_ini>='$fecha_ini'";
}
if ($fecha_fin)
{
  $titulo .= " hasta $fecha_fin";
  $fecha_fin = date_reverse($fecha_fin);
  $and_fecha .= " AND fecha_fin<='$fecha_fin'";
}

$colores = array("black", "blue", "yellow", "green", "red", "brown1", "cyan", "darkmagenta", "orange", "pink", "lightblue","aquamarine", "aqua", "blueviolet", "chartreuse");

$sql = "SELECT campana_id, nombre, fecha_ini, fecha_fin FROM crm_campanas WHERE 1$and_fecha ORDER BY fecha_ini";
$result = $db->sql_query($sql) or die("Error");
if ($db->sql_numrows($result) > 0)
{

  // A new graph with automatic size
  $graph  = new GanttGraph (0,0, "auto");
  $graph->SetShadow();
  $graph ->scale->SetDateLocale( "es_MX");
  $graph->scale->actinfo ->SetColTitles (
      array('Campaña','Inicio', 'Fin', /*'Targets',*/ 'Concesionarias'));
  $graph->title-> Set( "Cronograma de campañas"); 
  if ($titulo) $graph->subtitle-> Set( "Campañas$titulo");
  $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH);  //GANTT_HDAY |  GANTT_HWEEK |  
  // $graph->scale-> month-> SetStyle( MONTHSTYLE_SHORTNAMEYEAR2);

  while (list($campana_id, $nombre, $fecha_ini, $fecha_fin) = $db->sql_fetchrow($result))
  {
    if ($fecha_ini == "0000-00-00" || $fecha_fin == "000-00-00") continue;
    //targets
    $sql = "SELECT target, valor FROM crm_campanas_targets AS t WHERE  t.campana_id='$campana_id' ORDER BY target_id";
    $result2 = $db->sql_query($sql) or die("Error".print_r($db->sql_error()));
    $targets = "";
    $targets_index = 0;
    while (list($target, $valor) = $db->sql_fetchrow($result2))
    {
      if ($targets_index++)
        $targets .= " ";
      $targets .= "$target: $valor";
    }
    //target
    $groups = "";
    $groups_index = 0;
    $sql = "SELECT g.name, g.gid FROM crm_campanas_groups AS c, groups AS g WHERE c.gid=g.gid AND c.campana_id='$campana_id' ORDER BY g.gid";
    $result2 = $db->sql_query($sql) or die("Error".print_r($db->sql_error()));
    while (list($nombre_grupo, $gid) = $db->sql_fetchrow($result2))
    {
      if ($groups_index++)
        $groups .= ", ";
      else 
        $first_gid = $gid;
      $groups .= "$nombre_grupo";
    }
    //  A new activity on row i++
    $activity  = new GanttBar ($graph_index++,array("$nombre", date_reverse($fecha_ini), date_reverse($fecha_fin), /*$targets, */$groups), 
                                "$fecha_ini", "$fecha_fin");
    if ($groups_index > 1) $activity ->SetPattern(BAND_RDIAG, "yellow");
    else $activity ->SetPattern(BAND_RDIAG, "yellow");
    $activity ->SetFillColor ($colores[$first_gid]);
    $graph->Add($activity);
  }
  // Display the Gantt chart
  $graph->Stroke();
}
else
{
  die("<html><head><script>alert('No existen campañas en este rango de tiempo'); window.close();</script></head><body></body></html>");
}
?>