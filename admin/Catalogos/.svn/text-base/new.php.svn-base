<?php
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die("No puedes acceder directamente a este archivo...");
}
global $_admin_menu2,$db;
include_once("menu_derecho.php");
$select_origenPadre=genera_origen($db,$origen_padre_id);
$select_zonas=genera_zonas($db,$zona_id);

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

?>