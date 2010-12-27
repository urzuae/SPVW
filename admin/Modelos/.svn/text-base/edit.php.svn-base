<? 
if (!defined('_IN_ADMIN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db,$unidad_id,$nombre_modelo,$new,$guarda;

// Crear version
if(isset($new) && $new != "")
{
    $n =$db->sql_numrows($db->sql_query("SELECT nombre FROM crm_versiones WHERE nombre='$new'"));
    if ($n != 0)
        $error = "<b>No se pudo crear la versi&oacute;n \"$new\", por que ya existe en la tabla</b><br>\n";
    else
    {
        $db->sql_query("INSERT INTO crm_versiones (version_id, nombre) VALUES ('','$new')") or die("No se pudo crear la version");
        $version_id_sig=$db->sql_nextid();
        $error="<font color='#AA2000'>Se ha creado la versi&oacute;n, por favor actualiza la transmisi&oacute;n de tu veh&iacute;culo creado.</font>";
    }
}

if($guarda)
{
    //SACAMOS LAS VERSIONES LIGADOS AL VEHICULO
    $del="DELETE FROM crm_vehiculo_versiones WHERE vehiculo_id=".$unidad_id.";";
    $db->sql_query($del);
    foreach($_POST['versiones_ids'] as $id)
    {
        $inser="INSERT INTO crm_vehiculo_versiones (vehiculo_id,version_id) VALUES ('".$unidad_id."','".$id."');";
        $db->sql_query($inser);
        $error="<font color='#3e4f88'>Se han almacenado las versiones seleccionadas</font>  ";
    }
}


$nombre_modelo=regresa_nombre($db,$unidad_id);
$array_versiones=listado_versiones($db);
$array_seleccionadas=regresa_versiones_seleccionadas($db,$unidad_id);

$buffer="<script>function nuevaversion(){var vers= prompt('Ingrese el nombre de la version');if (vers) location.href=('index.php?_module=$_module&_op=edit&unidad_id=$unidad_id&new='+vers);}</script>\n";
$buffer.=$error;
$buffer.='<table width="80%" align="0" align="center" >
            <tbody><tr class="row1">
                <td width=25%>Nombre del Veh&iacute;culo:</td><td width=35%><input type="text" name="nombre" value="'.$nombre_modelo.'" size="35"></td><td width=40%>&nbsp;</td>
            </tr>
            <tr class="row2">
                <td><font color= blue >Versi&oacute;n</font><br</td><td><br>';
                $buffer.=regresa_versiones($db,$array_versiones,$array_seleccionadas);
                $buffer.="</td><td valign='top'><a href=\"#\" onclick=\"nuevaversion()\"> Ingresar nueva versi&oacute;n</a><hr color='#ffffff'><font color='#333333'>Agregar una transmisi&oacute;n a las versiones del Modelo</font><br><ul>";
                foreach($array_seleccionadas as $var)
                {
                    $buffer.="<li><a href='index.php?_module=$_module&_op=editt&unidad_id=$unidad_id&version_id=$var'>".$array_versiones[$var]."</a></li>";
                }

                $buffer.="</ul><br><hr color='#ffffff'>Nota: Para seleccionar las caracter&iacute;sticas de la versi&oacute;n, presiona la tecla <font color='#0f00ff'>Ctrl+ clic</font> en opci&oacute;n.<br>Para desmarcar alguna de las opciones, realiza el mismo proceso.<br>Al finalizar dar clic en el bot&oacute;n Guardar Versiones</td>
                </tr>
                <tr class='row1'>
                <td colspan='2' align='center'>
                    <input type='submit' name='guarda' value='Guardar Versiones'>
                    &nbsp;&nbsp;
                <input type='button' name='regresa' value='Regresar a Modelos' onclick=\"location='index.php?_module=Modelos'\">
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
function regresa_versiones_seleccionadas($db,$unidad_id)
{
    $array=array();
    $sql="SELECT a.vehiculo_id,a.version_id FROM crm_vehiculo_versiones a WHERE a.vehiculo_id=".$unidad_id." ORDER BY a.version_id;";
    $res=$db->sql_query($sql);
    if ( $db->sql_numrows($res) > 0 )
    {
        while($fila = $db->sql_fetchrow($res))
        {
            $array[]=$fila['version_id'];
        }
    }
    return $array;
}

function regresa_versiones($db,$array_versiones,$array)
{
    $select='';
    if ( count($array_versiones) > 0 )
    {
        $select.="<select multiple name='versiones_ids[]' id='version' style='width:260px;height:160px;background-color:#fff;color:#666;'>";
        foreach($array_versiones as $key => $value)
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

function listado_versiones($db)
{
    $array=array();
    $sql="SELECT version_id,nombre FROM crm_versiones ORDER BY version_id;";
    $res=$db->sql_query($sql);
    if ( $db->sql_numrows($res) > 0 )
    {
        while($fila = $db->sql_fetchrow($res))
        {
            $array[$fila['version_id']]=$fila['nombre'];

        }
    }
    return $array;
}
?>