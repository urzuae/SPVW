<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $fecha_ini,$fecha_fin,$gid,$submit;
$buffer='';
$select_groups=Regresa_Concesionarias($db,$gid);

if ($submit) 
{
    if($gid)        $filtro.= " AND a.gid='".$gid."' ";
    if($fecha_ini)
    {
        $fecha_i=substr($fecha_ini,6,4).'-'.substr($fecha_ini,3,2).'-'.substr($fecha_ini,0,2);
        $filtro.=" AND a.date_created >='".$fecha_i." 00:01:01'";
    }
    if($fecha_fin)
    {
        $fecha_c=substr($fecha_fin,6,4).'-'.substr($fecha_fin,3,2).'-'.substr($fecha_fin,0,2);
        $filtro.=" AND date_created <='".$fecha_c." 23:59:59'";
    }

    $array_concesionarias=Catalogo_Concesionarias($db);
    $array_niveles       =Catalogo_Niveles($db);

    $sql="SELECT a.date_created,a.uid,a.gid,b.super,b.user,b.name
          FROM users_registry as a LEFT JOIN users AS b ON a.uid=b.uid
          WHERE 1 ".$filtro." ORDER BY a.gid,b.name;";
    $res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
    $num=$db->sql_numrows($res);
    if($num > 0)
    {
        $buffer="
            <table class='tablesorter' align='center'>
                 <thead>
                    <tr>
                    <th>uid</th><th>Usuario</th><th>Nombre de Usuario</th><th>Fecha C</th><th>Perfil</th><th>Id Con</th><th>Concesionaria</th>
                    </tr>
                 </thead><tbody>";
        while(list($fecha,$uid,$_gid,$super,$user,$name) = $db->sql_fetchrow($res))
        {
            $_gid=str_pad($_gid,4,'0',STR_PAD_LEFT);
            $nm_gid=$array_concesionarias[$_gid];
            if($user == '') $user="Eliminado";

            if($nm_gid == '')  $nm_gid='Eliminado';
            $buffer.="<tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;\">
                        <td>".$uid."</td>
                        <td>".$user."</td>
                        <td>".$name."</td>
                        <td>".$fecha."</td>
                        <td>".$array_niveles[$super]."</td>
                        <td>".$_gid."</td>
                        <td>".$nm_gid."</td>
                       </tr>";
        }
        $buffer.="</tbody>
                    <thead><tr><td colspan='7' align='left'>Total de Usuarios:  ".$num."</td></tr></thead></table>";
    }
    else
    {
        $buffer.="<p align='center'>No existen usuarios registrado en el perido seleccionado</p>";
    }
}

function Regresa_Concesionarias($db,$gid)
{
    $select_groups = "<select name='gid' id='gid'>";
    $result = $db->sql_query("SELECT gid,name FROM groups WHERE 1 ORDER BY gid") or die("Error al cargar grupos");
    $select_groups .= "<option value=\"\">Selecciona una concesionaria</option>";
    while(list($_gid,$name) = $db->sql_fetchrow($result))
    {
        $selected = "";
        if ($_gid == $gid)
            $selected = " SELECTED";
        $select_groups .= "<option value=\"$_gid\"$selected>$_gid - $name</option>";
    }
    $select_groups.= "</select>";
    return $select_groups;
}

function Catalogo_Concesionarias($db)
{
    $array=array();
    $result = $db->sql_query("SELECT gid,name FROM groups WHERE 1 ORDER BY gid") or die("Error al cargar grupos");
    while(list($_gid,$name) = $db->sql_fetchrow($result))
    {
        $array[$_gid]=$name;
    }
    return $array;
}
 function Catalogo_Niveles($db)
 {
    $array=array();
    $result = $db->sql_query("SELECT tipo_id,nombre FROM users_types WHERE 1 ORDER BY tipo_id") or die("Error al cargar grupos");
    while(list($_gid,$name) = $db->sql_fetchrow($result))
    {
        $array[$_gid]=$name;
    }
    return $array;    
 }

?>