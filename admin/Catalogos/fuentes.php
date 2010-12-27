<?php
header("Cache-Control: no-cache, must-revalidate");
if (!defined('_IN_ADMIN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db;
$array_padres=regresa_padres($db,$id_padre);
if(count($array_padres) > 0)
{
    $html_origen_padre='<table width="100%" class="tablesorter" align="center" border="0">
            <thead>
            <tr height="30px">
                <th align="center" width="80%">Fuentes Padres</th>
                <td align="center" width="20%">Acciones</td>
            </tr></thead><tbody>';
            foreach($array_padres as $clave => $valor)
            {
                    $html_origen_padre.='
                        <tr class="row'.($class_row++%2?"2":"1").'" style="cursor:pointer;">
                        <td>'.strtoupper($valor).'</td>
                        <td align="center">&nbsp;&nbsp;
                        <a href="index.php?_module=Catalogos&_op=nuevaFuente&padre_id='.$clave.'&nombre='.$valor.'"><img border="0" width="16" height="16" src="../img/backup/new.png" alt="A&ntilde;adir fuentes"></a>
                        &nbsp;&nbsp;
                        <a href=""><img border="0" width="16" height="16" src="../img/backup/edit.png" alt="Modificar fuentes"></a>
                        &nbsp;&nbsp;
                        <a href=""><img border="0" width="16" height="16" src="../img/backup/del.png" alt="Eliminar fuentes Padres"></a>
                        &nbsp;&nbsp;
                        <a href="index.php?_module=Catalogos&_op=mostrarArbol"><img border="0" width="16" height="16" src="../img/backup/list.png" alt="Bloquear Fuentes"></a>
                        &nbsp;&nbsp;
                        </td>
                    </tr>';
            }
            $html_origen_padre.='</tbody><thead><tr><td align="center">N&uacute;mero de Fuentes:</td><td align="center">'.count($array_padres).' </td></tr></thead></table><br>';
}




/**
 * Funcion que sirve para sacar los id de los padres de cualquier nivel del arbol
 * @param int conexion a la base de datos $db
 * @param int id del padre en caso de que estuviese seleccionado $id_padre
 * @return array  con los nombres de de los padres que se meteran en un combo
 */
function regresa_padres($db,$id_padre)
{
    if($id_padre> 0 )
    {
        $filtro=" WHERE padre_id=".$id_padre;
    }
    $sql_padre="SELECT fuente_id,nombre FROM crm_fuentes ".$filtro." ORDER BY fuente_id;";
    $res_padre=$db->sql_query($sql_padre);
    if($db->sql_numrows($res_padre) > 0)
    {
        while($fila = $db->sql_fetchrow($res_padre))
        {
            $array_padre[$fila['fuente_id']]=$fila['nombre'];
        }
    }
return $array_padre;
}




/*unset($html_origen);

if($_REQUEST["action"] == "insert_sub"){
	if($_REQUEST["nueva_fuente"] != ""){
		$sql = "select count(origen_id) from crm_contactos_origenes where nombre = '$_REQUEST[nueva_fuente]'";
		$cs = $db->sql_query($sql);
		list($cont_id) = $db->sql_fetchrow($cs);
		if($cont_id > 0){
			die("false");
		}
		else{
			//primero obtener el numero de la fuente que sigue
			$sql = "select origen_id from crm_contactos_origenes order by origen_id ASC";
			$cs = $db->sql_query($sql);
			list($origen_id) = $db->sql_fetchrow($cs);
			$origen_id--;			
			$sql = "insert into crm_contactos_origenes(origen_id,nombre,origen_padre_id,activo) values('$origen_id','$_REQUEST[nueva_fuente]','$_REQUEST[origen_padre_id]',1)";
			$db->sql_query($sql);
			die("true");
		}
	}
}

if($_REQUEST["action"] == "show_subs"){
	if(is_numeric($_REQUEST["origen_padre_id"])){
		$sql_add = " where origen_padre_id = '$_REQUEST[origen_padre_id]'";
	}
}
if($_REQUEST["action"] == "lock_subs" or $_REQUEST["action"] == "unlock_subs"){
	if($_REQUEST["action"] == "lock_subs") $set_activo = 0; else $set_activo = 1;
	if(is_numeric($_REQUEST["origen_id"])){
		$sql = "update crm_contactos_origenes set activo = $set_activo where origen_id = '$_REQUEST[origen_id]'";
		$db->sql_query($sql);
		$sql = "select origen_padre_id from crm_contactos_origenes where origen_id = '$_REQUEST[origen_id]'";
		$cs = $db->sql_query($sql);
		list($origen_padre_id) = $db->sql_fetchrow($cs);
		$sql_add = " where origen_padre_id = '$origen_padre_id'";
	}
}

$sql = "select origen_padre_id,	origen_padre from crm_contactos_origenes_padre order by origen_padre asc";
$cs = $db->sql_query($sql);
while(list($origen_padre_id, $origen_padre) = $db->sql_fetchrow($cs)){
	if($row == "row2") $row = "row1"; else $row = "row2";
	$html_origen_padre .= "<tr class=\"$row\">";
	$html_origen_padre .= "<td><a href=\"javascript:fuentes_show_subs('$origen_padre_id');\">$origen_padre</a></td>";
	$html_origen_padre .= "<td width=\"30px;\">
		<!--<a style=\"float:left\" href=\"javascript:fuentes_delete_padre('$origen_padre_id');\"><img border=\"0\" src=\"../img/del.png\"/></a>-->
	</td>";
	$html_origen_padre .= "</tr>";
}


if($_REQUEST["action"] == "show_subs" or $_REQUEST["action"] == "lock_subs" or $_REQUEST["action"] == "unlock_subs") {
	$sql = "select origen_id, nombre, activo from crm_contactos_origenes $sql_add order by nombre asc";
	$cs = $db->sql_query($sql);
	while(list($origen_id, $origen, $activo) = $db->sql_fetchrow($cs)){
		if($row == "row2") $row = "row1"; else $row = "row2";
		if($activo == 0){ 
			$style = "color:gray;text-decoration:line-through;";
			$img_lock = "<a style=\"float:left\" href=\"javascript:fuentes_unlock_subs('$origen_id');\"><img alt=\"Click para desbloquear esta fuente\" border=\"0\" src=\"../img/more.gif\"/></a>";
		}
		if($activo == 1) {
			$style = "";
			$img_lock = "<a style=\"float:left\" href=\"javascript:fuentes_lock_subs('$origen_id');\"><img alt=\"Click para bloquear esta fuente\" border=\"0\" src=\"../img/less.gif\"/></a>";
		}
		$html_origen .= "<div class=\"$row\" style=\"$style float:left;width:250px;\">$origen</div>";
		$html_origen .= "<div class=\"$row\" style=\"float:left;\">$img_lock</div><br>";
		unset($activo);
	}
	die($html_origen);
}*/
?>