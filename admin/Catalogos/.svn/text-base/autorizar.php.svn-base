<?php
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die("No puedes acceder directamente a este archivo...");
}
global $_admin_menu2,$db;
include_once("menu_derecho.php");
$select_origenPadre=genera_origen($db,$origen_padre_id);
$select_zonas=genera_zonas($db,$zona_id);
$buffer_groups=Genera_Tabla_Fuentes($db,$fuente_id);
$select_groups=genera_grupos($db,$id_group);
$select_fuentes=genera_origen($db,$origen_padre_id);

function genera_origen($db,$origen_padre_id)
{
    $sql_padres="SELECT a.padre_id,b.nombre,b.fuente_id FROM crm_fuentes_arbol a,crm_fuentes b WHERE a.padre_id=0 and a.hijo_id=b.fuente_id ORDER BY b.nombre;";
    $res_padres=$db->sql_query($sql_padres);
    if( $db->sql_numrows($res_padres) > 0)
    {
        $select_origenPadre="<select name=\"padre_id\" id=\"padre_id\" class=\"nodo\">
                          <option value='0'>Seleccione</option>";
        while($fila = $db->sql_fetchrow($res_padres))
        {
            $select_origenPadre.= "<option value=\"".$fila['fuente_id']."\">".$fila['nombre']."</otpion>";
        }
        $select_origenPadre.="</select>";
    }
    /** cambios de luis hdez **/
    $select_origenPadre.="&nbsp;&nbsp;
                        <input type='hidden' id='origen' name='origen'>
                        <br><select name='hijo_id_1' id='hijo_id_1' class='nodo'><option value='0'>Seleccionar</option></select>
                        <br><select name='hijo_id_2' id='hijo_id_2' class='nodo'><option value='0'>Seleccionar</option></select>
                        <br><select name='hijo_id_3' id='hijo_id_3' class='nodo'><option value='0'>Seleccionar</option></select>
                        <br><select name='hijo_id_4' id='hijo_id_4' class='nodo'><option value='0'>Seleccionar</option></select>";
    return $select_origenPadre;
}

function genera_zonas($db,$zona_id)
{
    $select_zonas="";
    $sql="select zona_id,nombre from crm_zonas WHERE zona_id<13 ORDER BY zona_id;";
    $res=$db->sql_query($sql);
    if( $db->sql_numrows($res) > 0)
    {
        $select_zonas="<select name=\"zona_id\" id=\"zona_id\" class=\"nodo\">
                   <option value='0'>Concesionarias por zonas</option>";
        while(list($id,$name) = $db->sql_fetchrow($res))
        {
            $tmp='';
            if($id == $zona_id)
                $tmp=' SELECTED ';

            $select_zonas.="<option value='".$id."' ".$tmp.">".$id."  ".$name."</option>";

        }
        $select_zonas.="</SELECT>";
    }
    return $select_zonas;
}

function Genera_Tabla_Fuentes($db,$zona_id)
{
    $buffer='';

    $sql="select fuente_id,nombre,fecha_inicial,fecha_final from crm_fuentes WHERE fuente_id != 0 ORDER BY fuente_id limit 150,15;";
    $res=$db->sql_query($sql) or die ($sql);
    $num=$db->sql_numrows($res);
    if( $num > 0)
    {
        $buffer.="
        <table width='100%' align='center' class='tablesorter'>
        <thead><tr>
            <th>Id</th>
            <th>Fuente</th>
            <th>Periodo de Validez</th>
            <th>Valider Fuente</th>
        </tr></thead>";
        $i=0;
        while(list($id,$name,$fecha_inicio,$fecha_final)= $db->sql_fetchrow($res))
        {
            $buffer.="
            <tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;\">
            <td>".$id."</td>
            <td>".$name."</td>
            <td>".$fecha_inicio."   al   ".$fecha_final."</td>
            <td><input type='checkbox' name='validar'>Validar</td>
             </tr>";
            $i++;
        }
        $buffer.="</tbody><thead><tr><td colspan='5'>Total de fuentes: ".$num."</td></tr></thead></table>";
    }
    return $buffer;
}

function genera_grupos($db,$id_group)
{
    $res = $db->sql_query("SELECT DISTINCT groups.gid,groups.name FROM groups INNER JOIN groups_zonas ON groups.gid=groups_zonas.gid order by groups.gid ASC");
    if($db->sql_numrows($res) > 0)
    {
        $select_concesion='<select id="concesionaria" name="concesionaria"  style="width:250px;">
                            <option value="0" selected="selected">Selecciona Concesionaria</option>';
        while($rs = $db->sql_fetchrow($res))
        {
            $tmp_seleccion="";
            if($rs['gid']==$id_group)
            $tmp_seleccion="SELECTED";
            $select_concesion.= "<option value=".$rs['gid']." ".$tmp_seleccion.">".$rs['gid']."&nbsp;&nbsp;".$rs['name']."</option>";
        }
        $select_concesion.='</select>';
    }
    return $select_concesion;
}

?>