<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit, $campana_id, $add, $del, $padre_id;

$sql = "SELECT nombre FROM crm_campanas WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die(print_r($db->sql_error()));
list($campana) = htmlize($db->sql_fetchrow($result));

if ($add)
{
    $sql = "INSERT INTO crm_campanas_objeciones (campana_id, titulo, objecion_padre_id) VALUES('$campana_id', '$add', '$padre_id')";
    $db->sql_query($sql) or die("Error al agregar");
    $id = $db->sql_nextid();
    header("location:index.php?_module=$_module&_op=objecion&campana_id=$campana_id&objecion_id=$id");
}
function recursive_del($id)
{
    global $db;
    $sql = "SELECT objecion_id FROM crm_campanas_objeciones WHERE objecion_padre_id='$id'";
    $result = $db->sql_query($sql) or die("Error al borrar");
    if ($db->sql_numrows($result) < 1) return;
    while (list($id) = $db->sql_fetchrow($result))
    {
        recursive_del($id);
        $sql = "DELETE FROM crm_campanas_objeciones WHERE objecion_id='$id'";
        $db->sql_query($sql) or die("Error al borrar");
    }
}
if ($del)
{
    recursive_del($del);
    //al final borrar al padre
    $sql = "DELETE FROM crm_campanas_objeciones WHERE objecion_id='$del'";
    $db->sql_query($sql) or die("Error al borrar");
}
if ($submit)
{
	header("location:index.php?_module=$_module&_op=contactos&campana_id=$campana_id");
}

//función recursiva
function tabla($padre_id)
{
	global $db, $campana_id, $_module, $_htmldir;
	
	$sql = "SELECT objecion_id, titulo, objecion FROM crm_campanas_objeciones WHERE campana_id='$campana_id' AND objecion_padre_id='$padre_id'";
	$result = $db->sql_query($sql)
		or die("Error al cargar objeciones".print_r($db->sql_error()));
    //si no hay dejar de iterar
    if ($db->sql_numrows($result) < 1)
    {
        return "";
    }
    $obj = "<table border=0 cellspacing=0 cellpadding=0>\n <tr>\n";
	$objs = array();
	//la primer fila tiene los botones
	while (list($objecion_id, $titulo, $objecion) = htmlize($db->sql_fetchrow($result)))
	{
		$obj .= "  <td align=center>"
			."<input type=\"button\" name=\"o$objecion_id\" value=\"$titulo\""
			." onclick=\"location.href='index.php?_module=$_module&_op=objecion&campana_id=$campana_id&objecion_id=$objecion_id';\">"
            //botones extra: agregar y borrar
            ."<a href=\"#\" onclick=\"add($objecion_id, '$titulo')\"><img src=\"../img/more.gif\" onmouseover=\"return escape('Agregar')\"  border=0></a>"
            ."<a href=\"javascript:del($objecion_id, '$titulo');\"><img src=\"../img/del.gif\" onmouseover=\"return escape('Borrar')\"  border=0></a>"
			."&nbsp;</td>\n";
		array_push($objs, $objecion_id);
	}
	$obj .= " </tr>\n";
	//la segunda fila tiene un corchete para mostrar los hijos
	$obj .= " <tr>\n";
    $objs2 = array();
	foreach($objs as $obj_id)
	{
        $hijos = tabla($obj_id);
        array_push($objs2, $hijos);
        //si sí hay hijos dibujar corchete
        if ($hijos != "")
		  $obj .= "  <td><img style=\"width: 100%; height: 32px;\" src=\"../img/corchete.png\"></td>\n";
        else 
            $obj .= "  <td></td>\n";
	}
	$obj .= " </tr>\n";
	$obj .= " <tr>\n";
	//la tercera tiene a los hijos
    foreach($objs2 as $hijos)
	{
		$obj .= "  <td align=center>";
        $obj .= $hijos;
        $obj .= "</td>\n";
	}
	$obj .= " </tr>\n";
	$obj .= "</table>\n";
	return $obj;
}

$obj = tabla(0);
if ($obj) $corchete_inicial = '<img style="width: 100%; height: 32px;" alt="" src="../img/corchete.png">';

//extra objeciones
$sql = "SELECT objecion_id, titulo, objecion FROM crm_campanas_objeciones WHERE campana_id='$campana_id' AND objecion_padre_id='-1'";
$result = $db->sql_query($sql)
        or die("Error al cargar objeciones".print_r($db->sql_error()));
while(list($objecion_id, $titulo, $objeciones) = $db->sql_fetchrow($result))
{
    $extras .=  "<input type=\"button\" name=\"o$objecion_id\" value=\"$titulo\""
                ." onclick=\"location.href='index.php?_module=$_module&_op=objecion&campana_id=$campana_id&objecion_id=$objecion_id';\">"
                ."<a href=\"javascript:del($objecion_id, '$titulo');\"><img src=\"../img/del.gif\" onmouseover=\"return escape('Borrar')\"  border=0></a>"
                ."<br>";
}
?>