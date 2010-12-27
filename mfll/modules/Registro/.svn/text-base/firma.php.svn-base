<?  
global $db,$contacto_id, $uid,$_theme,$_css, $_themedir, $guardar,$unidad_id, $tipo_pago_id,$auto, $pago;
$_theme = "sinmenu";
$_themedir = "themes/$_theme";

include_once("funciones.php");
$nombre_prospecto=registro($db,$contacto_id);
$vehiculos=Regresa_Pruebas_Manejo($db,$contacto_id);
$select_autos=muestra_autos($db,$unidad);
$select_pago =regresa_pago($db,$tipo_pago_nombre);
$oculto="<input type='hidden' id='contacto_id' name='contacto_id' value='".$contacto_id."'>";

if ($guardar)
{
    $sql_="SELECT * FROM mfll_contactos_firma WHERE contacto_id='".$contacto_id."' AND unidad_id='".$unidad_id."' AND tipo_pago_id='".$pago."';";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) == 0)
    {
        $sql = "INSERT INTO mfll_contactos_firma (contacto_id, unidad_id, tipo_pago_id)
                VALUES ('$contacto_id', '$unidad_id', '$pago')";
        $db->sql_query($sql) or die("$sql<br>Error al insertar contacto".print_r($db->sql_error()));

        // actualizamos la fase
        $sql_fases="SELECT contacto_id,fase_id FROM mfll_contactos_fases WHERE contacto_id=".$contacto_id.";";
        $res_fases=$db->sql_query($sql_fases);
        if($db->sql_numrows($res_fases)>0)
            $sql_c="update mfll_contactos_fases SET fase_id=3 WHERE contacto_id=".$contacto_id.";";
        else
            $sql_c="INSERT INTO mfll_contactos_fases (contacto_id,fase_id) values (".$contacto_id.",'3');";
        $db->sql_query($sql_c) or die("$sql<br>Error al insertar contacto".print_r($db->sql_error()));
    }
    header("Location: index.php?_module=Bienvenida&_op=mfll");
}
?>