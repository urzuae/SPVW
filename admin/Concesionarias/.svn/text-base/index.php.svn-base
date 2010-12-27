<?
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $del, $new,$_module,$_id,$desbloquea;

if($desbloquea)
{
   	$db->sql_query("UPDATE groups SET active=true WHERE gid=".$desbloquea);
    $db->sql_query("UPDATE users  SET active=1 WHERE gid=".$desbloquea);
    $db->sql_query("UPDATE groups_ubications SET active=true WHERE gid=".$desbloquea);
    $error="Se ha desbloqueado la concesionaria con id:  ".$desbloquea;

}
if($del)
{
    $sql="SELECT gid,name FROM delete_groups WHERE gid=".$del.";";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) == 0)
        $db->sql_query("INSERT INTO delete_groups ( SELECT gid,name FROM groups WHERE gid=".$del.");");
    else
        $db->sql_query("UPDATE delete_groups SET name='".$db->sql_fetchfield(1,0,$res)."' WHERE gid='".$del."';");

    $db->sql_query("UPDATE crm_contactos SET gid='0' WHERE gid='$del'") or die("No se pudo actualizar los registros");
    $db->sql_query("DELETE FROM groups_accesses WHERE gid='$del'") or die("Error al borrar permisos");
    $db->sql_query("DELETE FROM groups_ubications WHERE gid='$del' LIMIT 1") or die("No se pudo borrar");
    $db->sql_query("DELETE FROM crm_plazas_concesionarias WHERE gid='$del' LIMIT 1") or die("No se pudo borrar en la tabla de plazas");
    $db->sql_query("DELETE FROM crm_niveles_concesionarias WHERE gid='$del' LIMIT 1") or die("No se pudo borrar en niveles");
    $db->sql_query("DELETE FROM crm_grupos_concesionarias WHERE gid='$del' LIMIT 1") or die("No se pudo borrar en grupos empresariales");
    $db->sql_query("DELETE FROM groups_zonas WHERE gid='$del' LIMIT 1") or die("No se pudo borrar en zonas");
    $db->sql_query("DELETE FROM groups WHERE gid='$del' LIMIT 1") or die("No se pudo borrar groups");
    $db->sql_query("DELETE FROM crm_groups_fuentes WHERE gid='$del'") or die("No se pudo borrar GROUPS - Fuentes");

    // Eliminamos vendedores de la concesionaria eliminada
    $db->sql_query("INSERT INTO delete_users ( SELECT uid,gid,super,name FROM users WHERE gid=".$del.");");
    $db->sql_query("DELETE FROM users WHERE gid=".$del.";");
}

if(isset($new) && $new != "")
{
    $n =$db->sql_numrows($db->sql_query("SELECT name FROM groups WHERE name='$new'"));
    if ($n != 0)
        $error = "<b>No se pudo crear la concesionaria por que ya existe otro con el nombre \"$new\"</b><br>\n";
    else
    {
        $db->sql_query("INSERT INTO groups (gid, name)VALUES('','$new')") or die("No se pudo crear");
        $gid_sig=$db->sql_nextid();
        $db->sql_query("INSERT INTO groups_ubications (gid, name,nivel_id,nombre_nivel)VALUES('$gid_sig','$new','1','Básico')") or die("No se pudo crear la concesionaria");
        $db->sql_query("INSERT INTO crm_niveles_concesionarias (gid, nombre,nivel_id)VALUES('$gid_sig','Básico','1')") or die("No se pudo crear la concesionaria");
        $sql_fuentes="SELECT fuente_id FROM crm_fuentes ORDER BY fuente_id;";
        $res=$db->sql_query($sql_fuentes) or die("Error en la consulta de fuentes  ".$sql);
        if($db->sql_numrows($res) > 0)
        {
            while(list($fuente_id) = $db->sql_fetchrow($res))
            {
                $db->sql_query("INSERT INTO crm_groups_fuentes (gid,fuente_id) VALUES ('".$gid_sig."','".$fuente_id."')");
            }
        }
    }

}
//lista de usuarios
$array_gids=array();
$sql_count="select gid,count(gid) as total FROM crm_contactos WHERE gid>0 GROUP BY gid ORDER BY gid;";
$res_count=$db->sql_query($sql_count);
if($db->sql_numrows($res_count) > 0)
{
    while(list($gid,$total)=$db->sql_fetchrow($res_count))
    {
        $gid=str_pad($gid,4,"0",STR_PAD_LEFT); 
        $array_gids[$gid]=$total;
    }
}
$_html = "<script>
            function del(id,name,no_prospectos)
            {
                if(no_prospectos > 0)
                {
                    alert('No se puede eliminar la concesionaria, por que tiene prospectos asignados');
                }
                else
                {
                    if (confirm('Esta seguro que desea eliminar a la concesionaria '+name))
                        location.href=('index.php?_module=$_module&del='+id);
                }
            }
          </script>";
$_html .= "<script>function desbloquea(id,name){if (confirm('Esta seguro que desea desbloquear la concesionaria '+name)) location.href=('index.php?_module=$_module&desbloquea='+id);}</script>";
$_html .= "<script>function newgroup(){var conc = prompt('Ingrese el nombre de la nueva concesionaria');if (conc) location.href=('index.php?_module=$_module&new='+conc);}</script>\n";          
$_html .= "<div class=title>Lista de concesionariass</div><br>\n";
$_html .= "Aquí se muestra la lista de los grupos de usuarios.<br>\n";
$_html .= "Cada grupo tiene acceso a ciertos módulos, para editar a cuales tendrá acceso de click a modificar.<br>\n";
// $_html .= "Para ver la lista de miembros del grupo dé click al nombre.<br>\n";
$_html .= $error;
$_html .= "<table style=\"border-spacing:0\" cellspacing=3  cellpadding=3>\n";
$_html .= "<thead><tr style=\"font-weight:bold;\"><td>Gid</td><td>Nombre</td><td colspan=\"5\" align=\"center\">Acci&oacute;n</td><td></td></tr></thead><tbody>\n";


$result = $db->sql_query("SELECT g.gid, g.name,g.active FROM groups AS g WHERE 1 ORDER BY g.gid;") OR die("Error al consultar db: ".print_r($db->sql_error()));
while (list($id, $name, $active) = htmlize($db->sql_fetchrow($result)))
{

    $id=str_pad($id,4,"0",STR_PAD_LEFT);
    $gid_total=( $array_gids[$id] + 0);
	$_html .=  "<tr class=\"row".(($c++%2)+1)."\">"
              ."<td>$id</td>"
              ."<td><a href=\"index.php?_module=$_module&_op=edit&gid=$id\">$name<a></td>"
              ."<td><a href=\"index.php?_module=$_module&_op=edit&gid=$id\"><img src=\"../img/edit.gif\" onmouseover=\"return escape('Editar')\"  border=0></a></td>"
              ."<td><a href=\"index.php?_module=$_module&_op=bloquea&gid=$id\"><img src=\"../img/cross.gif\" onmouseover=\"return escape('Bloquear')\"  border=0></a></td>"              
              ."<td><a href=\"index.php?_module=$_module&_op=reubicar&gid=$id\"><img src=\"../img/mexico.gif\" onmouseover=\"return escape('Reubicar')\"  border=0></a></td>"
              ."<td><a href=\"index.php?_module=$_module&_op=categoria&gid=$id\"><img src=\"../img/categorias.png\" onmouseover=\"return escape('Categoria')\"  border=0></a></td>"
              ."<td>
                <a href=\"#\" onclick=\"del('$id','$id','$gid_total')\"><img src=\"../img/del.gif\" onmouseover=\"return escape('Borrar')\"  border=\"0\"></a></td><td>";
              if($active == false)
              {
                $_html .="<a href=\"#\" onclick=\"desbloquea('$id','$id')\"><img src=\"../img/lock.gif\" width='18' height='18' onmouseover=\"return escape('Desbloquear Concesionaria')\"  border=0></a>";
              }
              $_html .="</td></tr>\n";
}

//ahora el ultimo es para usuarios sin registrar
$_html .=  "<tr class=\"row".(($c++%2)+1)."\"><td>&nbsp;</td><td><i>Usuarios anónimos</i></td>"
            ."<td><a href=\"index.php?_module=$_module&_op=edit&gid=0\"><img src=\"../img/edit.gif\" onmouseover=\"return escape('Editar')\"  border=0></a></td>"
            ."<td>&nbsp;</td></tr>\n";
//cerrar tabla
$_html .= "</tbody></table>";


global $_admin_menu2;//<img src=\"../img/new.gif\" border=0>
$_admin_menu2 .= "<table><tr><td></td><td><a href=\"#\" onclick=\"newgroup()\"> Crear una nueva concesionaria</a></td></tr></table>";


?>