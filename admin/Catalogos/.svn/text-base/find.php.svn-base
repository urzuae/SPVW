<?php
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die("No puedes acceder directamente a este archivo...");
}
global $_admin_menu2,$db,$buscar_fuentes,$_module,$_op;
include_once("menu_derecho.php");
$select_origenPadre=genera_origen($db,$origen_padre_id);
$array_fuentes=array();
$id_fuente=0;
if($buscar_fuentes)
{
    $array_fuentes=Regresa_Fuentes($db);
    foreach ($_GET as $clave => $value)
    {
        if( ($clave != '_module') && ($clave != '_op') && ($clave != 'buscar_fuentes'))
        if($value != 0)
            $id_fuente = $value;
    }
    $buffer=Lista_Fuente($db, $array_fuentes, $id_fuente);
}



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
function Lista_Fuente($db,$array_fuentes,$padre_id)
{
    // VERIFICO SIA LA FUENTE TIENE RAMAS

    $buffer='';
    $sql="SELECT padre_id,hijo_id FROM crm_fuentes_arbol WHERE padre_id='".$padre_id."' ORDER BY padre_id,hijo_id;";
    $res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
    $num=$db->sql_numrows($res);
    if( $num > 0)
    {
        $buffer.=Genera_Salida($db,$num,$res,$array_fuentes);
    }
    else
    {
        $sql="SELECT 0 as padre_id,hijo_id FROM crm_fuentes_arbol WHERE hijo_id='".$padre_id."' ORDER BY padre_id,hijo_id;";
        $res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
        $num=$db->sql_numrows($res);
        if( $num > 0)
        {
            $buffer.=Genera_Salida($db,$num,$res,$array_fuentes);
        }
    }
    return $buffer;
}

function Genera_Salida($db,$num,$res,$array_fuentes)
{
    if( $num > 0)
    {
        $buffer.="
        <table width='100%' align='center' class='tablesorter'>
        <thead><tr>
            <th>Id</th>
            <th>Fuente</th>
            <th>Fechas de Validez</th>
            <th>Bloquear</th>
            <th>Desbloquear</th>
        </tr></thead>";
        $i=0;
        while(list($id_padre,$id_hijo)= $db->sql_fetchrow($res))
        {
            $i++;
            $buffer.="
            <tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;\">
            <td>".$i."</td>
            <td>".$array_fuentes[$id_hijo]['name']."</td>
            <td align='center'><img src='../img/date.png' width='16' height='16' border='0' alt='Actualiza Fuente' onclick=\"location='index.php?_module=Catalogos&_op=actualiza&id=$id_hijo'\"></td>";
            if($array_fuentes[$id_hijo]['active'] == 1)
            {
                $buffer.="<td align='center'><img src='../img/lock.gif' width='16' height='16' border='0' alt='Bloquear Fuente' onclick=\"Bloquea_Fuentes('".$id_hijo."');\"></td>
                          <td>&nbsp;</td>";
            }
            else
            {
                $buffer.="<td>&nbsp;</td>
                    <td align='center'><img src='../img/desbloquea.jpg' width='16' height='16' border='0' alt='Desbloquear Fuente'  onclick=\"Desbloquea_Fuentes('".$id_hijo."');\"></td>";
            }
            $buffer.="</tr>";
        }
        $buffer.="</tbody><thead><tr><td colspan='5'>Total de fuentes: ".$num."</td></tr></thead></table>";
    }
    return $buffer;
}

function Regresa_Fuentes($db)
{
    $array=array();
    $sql="SELECT fuente_id,nombre,active FROM crm_fuentes WHERE active != 2 ORDER BY fuente_id;";
    $res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$name,$active) = $db->sql_fetchrow($res))
        {
            $array[$id]['id']=$id;
            $array[$id]['name']=$name;
            $array[$id]['active']=$active;
        }
    }
    return $array;
}

?>