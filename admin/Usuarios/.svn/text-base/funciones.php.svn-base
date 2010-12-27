<?php
/* 
 * Funciones auxiliares que sirven para el modulo de uso del sistema
 *
 */
function Regresa_Concesionarias($db, $gid) {
    $select_groups = "<select name='gid' id='gid'>";
    $result = $db->sql_query("SELECT gid,name FROM groups WHERE 1 ORDER BY gid") or die("Error al cargar grupos");
    $select_groups .= "<option value=\"\">Selecciona una concesionaria</option>";
    while (list($_gid, $name) = $db->sql_fetchrow($result))
    {
        $selected = "";
        if ($_gid == $gid)
            $selected = " SELECTED";
        $select_groups .= "<option value=\"$_gid\"$selected>$_gid - $name</option>";
    }
    $select_groups.= "</select>";
    return $select_groups;
}

function Regresa_Zonas($db, $id_zona)
{
    $select_groups = "<select name='id_zona' id='id_zona'>";
    $result = $db->sql_query("SELECT zona_id,nombre FROM crm_zonas WHERE 1 ORDER BY zona_id") or die("Error al cargar grupos");
    $select_groups .= "<option value=\"\">Selecciona una zona</option>";
    while (list($_id, $name) = $db->sql_fetchrow($result))
    {
        $selected = "";
        if ($_id == $id_zona)
            $selected = " SELECTED";
        $select_groups .= "<option value=\"$_id\"$selected>$name</option>";
    }
    $select_groups.= "</select>";
    return $select_groups;
}

function Catalogo_Zonas($db)
{
    $array = array();
    $array[0] = "Sin zona asignada";
    $result = $db->sql_query("SELECT zona_id,nombre FROM crm_zonas WHERE 1 ORDER BY zona_id") or die("Error al cargar zonas");
    while (list($id, $name) = $db->sql_fetchrow($result))
    {
        $array[$id] = $name;
    }
    return $array;

}

function Catalogo_Concesionarias($db)
{
    $array = array();
    $result = $db->sql_query("SELECT gid,name FROM groups WHERE 1 ORDER BY gid") or die("Error al cargar grupos");
    while (list($_gid, $name) = $db->sql_fetchrow($result))
    {
        $array[$_gid] = $name;
    }
    return $array;
}

function Catalogo_Niveles($db)
{
    $array = array();
    $result = $db->sql_query("SELECT tipo_id,nombre FROM users_types WHERE 1 ORDER BY tipo_id") or die("Error al cargar grupos");
    while (list($_gid, $name) = $db->sql_fetchrow($result))
    {
        $array[$_gid] = $name;
    }
    return $array;
}

function Catalogo_Vendedores($db,$gid)
{
    $array = array();
    $array[0] = "Sin vendedor";
    $result = $db->sql_query("SELECT uid,name FROM users WHERE gid=".$gid." ORDER BY uid") or die("Error al cargar vendedores");
    while (list($id, $name) = $db->sql_fetchrow($result))
    {
        $array[$id] = $name;
    }
    return $array;

}

function random_color()
{
    mt_srand((double) microtime() * 1000000);
    $color = '';
    while (strlen($color) < 6)
    {
        $color .= sprintf("%02X", mt_rand(0, 255));
    }
    return $color;
}

function Quita_Caracteres($name)
{
    $name1=str_replace("'",'',$name);
    $name1=str_replace('*','',$name1);
    $name1=str_replace('&','-',$name1);

    return $name1;
}
?>