<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit, $campana_id, $del,
        $actividad, $solucion;


if (!$campana_id)
{
		header("location:index.php?_module=$_module");
}
else //empezar por que tenemos la campaña
{
  if ($submit)
  {
		$sql = "INSERT INTO crm_campanas_aprendizaje (campana_id, actividad, solucion)VALUES('$campana_id', '$actividad', '$solucion')";
		$db->sql_query($sql) or die("Error al guardar campaña<br>$sql<br>".print_r($db->sql_error()));
//  		header("location:index.php?_module=$_module&_op=campana&campana_id=$campana_id");
	}
  else if ($del)
  {
    $sql = "DELETE FROM crm_campanas_aprendizaje WHERE aprendizaje_id='$del'";
    $db->sql_query($sql) or die("Error al borrar<br>$sql<br>".print_r($db->sql_error()));
  }

  $sql = "SELECT nombre FROM crm_campanas WHERE campana_id='$campana_id'";
  $result = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));
  list($nombre) = $db->sql_fetchrow($result);
  $sql = "SELECT actividad, solucion, aprendizaje_id FROM crm_campanas_aprendizaje WHERE campana_id='$campana_id' ORDER BY aprendizaje_id";
  $result = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));
  if ($db->sql_numrows($result) > 0)
  {
    while (list($actividad, $solucion, $aprendizaje_id) = $db->sql_fetchrow($result))
    {
      $actividades .= "<tr class=\"row".(++$rowclass%2+1)."\"><td>$actividad</td><td>$solucion</td>"
                      ."<td><a href=\"index.php?_module=$_module&_op=$_op&campana_id=$campana_id&del=$aprendizaje_id\">"
                      ."<a href=\"index.php?_module=$_module&_op=$_op&campana_id=$campana_id&del=$aprendizaje_id\"><img border=\"0\" src=\"../img/del.gif\"></a></td></tr>\n";
    }
  }
  $rowclass = (($rowclass+1)%2)+1;
  $rowclass2 = $rowclass + 1;
}


?>