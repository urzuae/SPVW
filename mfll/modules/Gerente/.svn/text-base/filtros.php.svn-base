<?php
if (!defined('_IN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db,$_includesdir,$fecha_ini,$fecha_fin,$evento_id,$submit,$fase_id,$cargar;
$titulo="Autorizaci&oacute;n de carga al SPVW";
include_once("funciones.php");
$combo_eventos=Genera_Combo_Eventos($db,$evento_id);
$combo_fases=Genera_Combo_Fases($db,$fase_id);

if($cargar)
{
    include_once("cargar_registros.php");
    
}
//if(isset($submit) or (isset($cargar)))
if(isset($submit))
{
    include_once("filtro_fechas.php");
    $array_fases=Regresa_Fases($db);
    $array_concesionarias=Regresa_Concesionarias($db);
    $array_url=array();
    $total_bd=0;
    $buffer="<input type='hidden' name='cfecha_ini' id='cfecha_ini' value='".$fecha_ini."'>
             <input type='hidden' name='cfecha_fin' id='cfecha_fin' value='".$fecha_fin."'>
             <input type='hidden' name='cevento_id' id='cevento_id' value='".$evento_id."'>
             <input type='hidden' name='cfase_id' id='cfase_id' value='".$fase_id."'>";

    $sql="SELECT distinct a.contacto_id,a.gid,a.uid,concat(a.nombre,' ',a.apellido_paterno,' ',a.apellido_materno) as nombre,
                 a.tel_casa,a.tel_oficina,a.tel_movil,a.tel_otro,a.email,a.evento_id,b.fase_id,a.medio_id,a.fecha_registro
          FROM mfll_contactos_registro as a ,mfll_contactos_fases as b 
          WHERE a.contacto_id=b.contacto_id AND a.cargado = 0 ".$rango_fechas." ORDER BY a.evento_id,b.fase_id,a.nombre,a.apellido_paterno,a.apellido_materno;";
    $res=$db->sql_query($sql);
    $tuplas=$db->sql_numrows($res);
    if( $tuplas > 0)
    {
        $array_eventos=Regresa_Eventos($db);
        $array_fases=Regresa_Fases($db);
        $array_unidades=Regresa_Unidades($db);
        $array_tipo_pago=Regresa_Tipos_Pagos($db);
        $array_gids=Regresa_Concesionarias($db);
        $array_uids=Regresa_Vendedores($db);
        $encabezado="Listado de prospectos para cargar en el Sistema de Prospecci&oacute;n de VW";
        $buffer.="<table align='center' width='100%' border='0'>
                    <tr>
                        <td align='right'>
                        <input type='button' name='marcar' id='marcar' value='Marcar Todos' style='width:140px;background-color:#ffffff;border:1px solid #cdcdcd;color:#333333;font-weight:bold;'>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type='button' name='desmarcar' id='desmarcar' value='Desmarcar Todos' style='width:140px;background-color:#ffffff;border:1px solid #cdcdcd;color:#333333;font-weight:bold;'>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type='button' name='boton_checks' id='boton_checks' value='Eliminar' style='width:140px;background-color:#ffffff;border:1px solid #cdcdcd;color:#333333;font-weight:bold;'>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type='submit' name='cargar' id='cargar' value='Cargar al SPVW' style='width:140px;background-color:#ffffff;border:1px solid #cdcdcd;color:#333333;font-weight:bold;'></td>
                    </tr></table>";
        $buffer.="<table align='center' class='tablesorter' width='100%'>
                 <thead><tr>
                <th width='12%'>Evento</th>
                <th width='8%'>Fase</th>
                <th width='20%'>Nombre</th>
                <th width='15%'>Telef&oacute;nos</th>
                <th width='15%'>Email</th>
                <th width='10%'>Unidad Firmado</th>
                <th width='10%'>Tipo Pago</th>
                <th width='10%'>Ingreso</th>
                <th width='5%'>E</th>
                </tr></thead><tbody>";
        while(list($contacto_id,$gid,$uid,$nombre,$tel_casa,$tel_oficina,$tel_movil,$tel_otro,$email,$evento_id,$fase_id,$medio_id,$fecha_ingreso) = $db->sql_fetchrow($res))
        {
            $tels="Tel casa: ".$tel_casa."<br>
                   Tel Oficina: ".$tel_oficina."<br>
                   Tel Movil: ".$tel_movil."<br>
                   Tel Otro: ".$tel_otro;

            $array_tmp=Regresa_Datos_Firmado($db,$contacto_id,$array_unidades,$array_tipo_pago);
            $buffer.="<tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;height:30px;\" >
                        <td>".$array_eventos[$evento_id]."</td>
                        <td>".$array_fases[$fase_id]."</td>
                        <td>".$nombre."</td>
                        <td>".$tels."</td>
                        <td>".$email."</td>
                        <td>".$array_tmp[0]."</td>
                        <td>".$array_tmp[1]."</td>
                        <td>".substr($fecha_ingreso,0,10)."</td>
                        <td><input type='checkbox' name='c$contacto_id' id='c$contacto_id' value='$contacto_id'></td>
                    </tr>";
        }
        $buffer.="</tbody><thead><tr><td colspan='9'>Total de Prospectos: ".$tuplas."</td></tr></thead></table>";
    }
    else
    {
        $encabezado="No se encontraron registros con el filtro seleccionado";
    }
}
?>
