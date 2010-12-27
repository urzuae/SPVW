<?
if (!defined('_IN_ADMIN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db,$unidad_id,$version_id,$nombre_modelo,$new,$guardat;

// Crear transmision
if(isset($new) && $new != "")
{
    $n =$db->sql_numrows($db->sql_query("SELECT nombre FROM crm_transmisiones WHERE nombre='$new'"));
    if ($n != 0)
        $error = "<b>No se pudo crear la transmisi&oacute;n \"$new\", por que ya existe en la tabla</b><br>\n";
    else
    {
        $db->sql_query("INSERT INTO crm_transmisiones (transmision_id, nombre) VALUES ('','$new')") or die("No se pudo crear la transmision");
        $version_id_sig=$db->sql_nextid();
        $error="<font color='#AA2000'>Se ha creado la transmisi&oacute;n.</font>";
    }
}

if($guardat)
{
    //SACAMOS LAS VERSIONES LIGADOS AL VEHICULO
    $version_id=$_POST['version_id'];
    $del="DELETE FROM crm_version_transmisiones WHERE version_id=".$version_id.";";
    $db->sql_query($del);
    foreach($_POST['transmisiones_ids'] as $id)
    {
        $inser="INSERT INTO crm_version_transmisiones (version_id,transmision_id) VALUES ('".$version_id."','".$id."');";
        $db->sql_query($inser);
        $error="<font color='#3e4f88'>Se han almacenado las trasnmisiones seleccionadas</font>  ";
    }
}


$nombre_modelo=regresa_nombre($db,$unidad_id);
$nombre_version=regresa_nombre_version($db,$version_id);
$array_transmisiones=listado_transmisiones($db);
$array_seleccionadas=regresa_transmisiones_seleccionadas($db,$version_id);

$buffer="<script>function nuevatransmision(){var vers= prompt('Ingrese el nombre de la transmisión');if (vers) location.href=('index.php?_module=$_module&_op=editt&unidad_id=$unidad_id&version_id=$version_id&new='+vers);}</script>\n";
$buffer.=$error;
$buffer.='<input type="hidden" name="version_id" value="'.$version_id.'"><table width="80%" align="0" align="center" >
            <tbody><tr class="row1">
               <td width=25%>Nombre del Veh&iacute;culo:</td><td width=35%>'.$nombre_modelo.'</td><td width=40%>&nbsp;</td>
            </tr>
            <tr class="row2">
               <td width=25%>Nombre de la Versi&oacute;:</td><td width=35%><input type="text" name="version" value="'.$nombre_version.'" size="35"></td><td width=40%>&nbsp;</td>
            </tr>
            <tr class="row1">
                <td>Transmisi&oacute;n</td><td><br>';
                    $buffer.=regresa_transmisiones_asignadas($db,$array_transmisiones,$array_seleccionadas);
                $buffer.="</td><td valign='top'><a href=\"#\" onclick=\"nuevatransmision()\"> Ingresar nueva transmisi&oacute;n</a><hr color='#ffffff'><font color='#333333'>Versiones de Transmisiones</font><br><ul>";
                foreach($array_seleccionadas as $var)
                {
                    $buffer.="<li>".$array_transmisiones[$var]."</li>";
                }
                $buffer.="</ul><br><hr color='#ffffff'>Nota: Para seleccionar las caracter&iacute;sticas de la transmisi&oacute;n, presiona la tecla <font color='#0f00ff'>Ctrl+ clic</font> en opci&oacute;n.<br>Para desmarcar alguna de las opciones, realiza el mismo proceso.<br>Al finalizar dar clic en el bot&oacute;n Guardar Transmisiones</td>
                </tr>
                <tr class='row1'>
                <td colspan='2' align='center'>
                    <input type='submit' name='guardat' value='Guardar Transmisiones'>
                    &nbsp;&nbsp;
                    <input type='button' name='regresa' value='Regresar a Versiones' onclick=\"location='index.php?_module=Modelos&_op=edit&unidad_id=$unidad_id'\">
                </td></tr>
            </tbody>
        </table>";



/* **************** FUNCIONES AUXILIARES ***************/

/**
 * Funcion que regresa el nombre de la unidad
 * @param $db conector de la base de datos
 * @param int $unidad_id, id del unidad
 * @return string regresa el nombre de la unidad
 */
function regresa_nombre($db,$unidad_id)
{
    $name='';
    $sql="SELECT nombre FROM crm_unidades WHERE unidad_id=".$unidad_id.";";
    $res=$db->sql_query($sql);
    if ( $db->sql_numrows($res) > 0 )
    {
        $name=$db->sql_fetchfield(0,0,$res);
    }
    return $name;
}
function regresa_nombre_version($db,$version_id)
{
    $name='';
    $sql="SELECT nombre FROM crm_versiones WHERE version_id=".$version_id.";";
    $res=$db->sql_query($sql);
    if ( $db->sql_numrows($res) > 0 )
    {
        $name=$db->sql_fetchfield(0,0,$res);
    }
    return $name;

}
function regresa_transmisiones_seleccionadas($db,$version_id)
{
    $array=array();
    $sql="SELECT a.version_id,a.transmision_id FROM crm_version_transmisiones a WHERE a.version_id=".$version_id." ORDER BY a.version_id;";
    $res=$db->sql_query($sql);
    if ( $db->sql_numrows($res) > 0 )
    {
        while($fila = $db->sql_fetchrow($res))
        {
            $array[]=$fila['transmision_id'];
        }
    }
    return $array;
}

function regresa_transmisiones_asignadas($db,$array_transmisiones,$array)
{
    $select='';
    if ( count($array_transmisiones) > 0 )
    {
        $select.="<select multiple name='transmisiones_ids[]' id='transmision' style='width:260px;height:160px;background-color:#fff;color:#666;'>";
        foreach($array_transmisiones as $key => $value)
        {
            $tmp="";
            if(in_array($key,$array))
               $tmp=" selected ";
            $select.="<option value=".$key." ".$tmp.">".$value."</option>";
        }
        $select.="</select>";
    }
    return $select;
}

function listado_transmisiones($db)
{
    $array=array();
    $sql="SELECT transmision_id,nombre FROM crm_transmisiones ORDER BY transmision_id;";
    $res=$db->sql_query($sql);
    if ( $db->sql_numrows($res) > 0 )
    {
        while($fila = $db->sql_fetchrow($res))
        {
            $array[$fila['transmision_id']]=$fila['nombre'];

        }
    }
    return $array;
}
?>