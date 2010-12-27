<?
if (!defined('_IN_ADMIN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $del, $new,$_module,$_id;
$error="";
if($del)
{
    $db->sql_query("DELETE FROM crm_vehiculo_versiones WHERE vehiculo_id='$del'") or die("Error al borrar permisos");
    $db->sql_query("DELETE FROM crm_unidades WHERE unidad_id='$del' LIMIT 1") or die("No se pudo borrar el vehiculo");
    $error="<font color='#666'>Se ha eliminado el modelo seleccionado.</font>";
}

//lista de usuarios
if(isset($new) && $new != "")
{
    $n =$db->sql_numrows($db->sql_query("SELECT nombre FROM crm_unidades WHERE nombre='$new'"));
    if ($n != 0)
        $error = "<b>No se pudo crear el modelo \"$new\", por que ya existe en la tabla</b><br>\n";
    else
    {
        $db->sql_query("INSERT INTO crm_unidades (unidad_id, nombre)VALUES('','$new')") or die("No se pudo crear el modelo");
        $unidad_id_sig=$db->sql_nextid();
        $error="<font color='#3e4f88'>Se ha creado el nuevo vehiculo, por favor actualiza la versi&oacute;n y la transmisi&oacute;n de tu veh&iacute;culo creado.</font>";
    }
}

//lista de usuarios
$_html ="<script type='text/javascript' src='".$_themedir."/jquery/crm_monitoreo_asignacion_main.js'></script>";
$_html .= "<script>function del(id,name){if (confirm('Esta seguro que desea borrar el modelo '+name)) location.href=('index.php?_module=$_module&del='+id);}</script>";
$_html .= "<script>function nuevoVehiculo(){var model = prompt('Ingrese el nombre del modelo nuevo');if (model) location.href=('index.php?_module=$_module&new='+model);}</script>\n";
$_html .= "<div class=title>Lista de Modelos</div><br>\n";
$_html .= "Aquí se muestra la lista de los modelos registrados en el sistema.<br>\n";
$_html .= $error;
$_html .= "<table width='60%' border='0' align='center' class='tablesorter'>";
$_html .= "<thead><tr><th>Nombre de Veh&iacute;culo</th><th colspan='2'>Acciones</th></tr></thead><tbody>";
$result = $db->sql_query("SELECT unidad_id, nombre FROM crm_unidades WHERE 1 ORDER BY nombre") OR die("Error al consultar db: ".print_r($db->sql_error()));
while (list($unidad_id, $nombre) = htmlize($db->sql_fetchrow($result)))
{
	$_html .=  "<tr class=\"row".(($c++%2)+1)."\">
                <td><a href=\"index.php?_module=$_module&_op=edit&unidad_id=$unidad_id\">$nombre<a></td>
                <td><a href=\"index.php?_module=$_module&_op=edit&unidad_id=$unidad_id\"><img src=\"../img/edit.gif\" onmouseover=\"return escape('Actualizar')\"  border=0></a></td>
                <td><a href=\"#\" onclick=\"del('$unidad_id','$nombre')\"><img src=\"../img/del.gif\" onmouseover=\"return escape('Borrar')\"  border=0></a></td>"

              ."</tr>\n";
}
$_html .= "</tbody></table>";
global $_admin_menu2;
$_admin_menu2 .= "<table><tr><td></td><td><a href=\"#\" onclick=\"nuevoVehiculo()\"> Crear un veh&iacute;culo nuevo </a></td></tr></table>";
?>