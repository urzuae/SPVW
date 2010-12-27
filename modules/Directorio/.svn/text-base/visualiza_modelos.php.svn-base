<?php
$sql="SELECT modelo,version,ano,paquete,tipo_pintura,color_exterior,color_interior,modelo_id,version_id,transmision_id,timestamp FROM crm_prospectos_unidades WHERE contacto_id='".$contacto_id."' order by timestamp;";
$res=$db->sql_query($sql);
if($db->sql_numrows($res) > 0)
{
    $buffer="<table width='80%' align='center' border='0' bordercolor='dcdcdc'>
        <tr>
        <td colspan='8' align='justify'>
        Pasos para actualizar la informaci&oacute;n de un modelo:<br>
        1.- Dar un clic en el nombre del modelo que aparece en el listado.<br>
        2.- Actualizar los datos del modelo.<br>
        3.- Dar clic en el boton Actualizar Inf Modelo.<br>
        4.- El cambio se visualizar&aacute; en el listado de modelos.
        </td>
        </tr>
        <tr bgcolor='#cdcdcd'>
        <td style='color:#fff;text-align:center;font-weight:bold;'>Modelo</td>
        <td style='color:#fff;text-align:center;font-weight:bold;'>Versi&oacute;n</td>
        <td style='color:#fff;text-align:center;font-weight:bold;'>Transmisi&oacute;n</td>
        <td style='color:#fff;text-align:center;font-weight:bold;'>A&ntilde;o</td>
        <td style='color:#fff;text-align:center;font-weight:bold;'>Color Exterior</td>
        <td style='color:#fff;text-align:center;font-weight:bold;'>Color Interior</td>
        <td style='color:#fff;text-align:center;font-weight:bold;'>Tipo de pintura</td>
        <td style='color:#fff;text-align:center;font-weight:bold;'>Borrar</td></tr>";
    $cont=0;
    while(list($modelo,$version,$ano,$paquete,$tipo_pintura,$color_exterior,$color_interior,$modelo_id,$version_id,$transmision_id,$timestamp) = $db->sql_fetchrow($res))
    {
        $buffer.="<tr onMouseDown=\"actualiza_modelo('$modelo','$contacto_id','$modelo_id','$version_id','$transmision_id','$ano','$tipo_pintura','$color_exterior','$color_interior','$timestamp');\">
        <td>".$modelo."</td>
        <td>".$version."</td>
        <td>".regresa_transmision($db,$transmision_id)."</td>
        <td>".$ano."</td>
        <td>".$color_exterior."</td>
        <td>".$color_interior."</td>
        <td>".$tipo_pintura."</td>
        <td align='center'><input type='button' name='del$tmp' style='background-color:#ffffff;color:#3e4f88;border:0px;' onclick=\"elimina_modelo('$modelo','$contacto_id','$modelo_id','$version_id','$transmision_id','$timestamp');\"   value='Borrar' ></td>
        </tr>";
        if($cont == 0)
        {
            $sql="UPDATE reporte_contactos_asignados set modelo = '$modelo',modelo_id='$modelo_id',version_id = '$version_id',transmision_id = '$transmision_id' WHERE contacto_id='$contacto_id' LIMIT 1;";
            $resul=$db->sql_query($sql) or die("\n$sql\n2-Error la tabla de reportes asignados".print_r($db->sql_error()));

        }
        $cont++;
    }
    $buffer.="</table>";
}

function regresa_transmision($db,$transmision_id)
{
    $dato='';
    $sql="SELECT nombre FROM crm_transmisiones WHERE transmision_id=".$transmision_id.";";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        $dato=$db->sql_fetchfield(0,0,$res);
    }
    return $dato;

}
?>