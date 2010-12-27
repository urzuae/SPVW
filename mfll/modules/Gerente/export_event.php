<?php
if (!defined('_IN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db,$_includesdir,$evento_id,$submit;
$titulo="Exportar Evento a Excel";
include_once("funciones.php");
$combo_eventos=Genera_Combo_Eventos($db,$evento_id);
if(isset($submit))
{
    $filtro="";
    if($evento_id>0)    $filtro=" AND a.evento_id='".$evento_id."' ";
    $array_url=array();
    $total_bd=0;
    $buffer="<input type='hidden' name='cevento_id' id='cevento_id' value='".$evento_id."'>";
    $sql="SELECT
         a.contacto_id,a.gid,a.uid,concat(a.nombre,' ',a.apellido_paterno,' ',a.apellido_materno) as nombre,a.tel_casa,a.tel_oficina,a.tel_movil,a.tel_otro,a.email,a.evento_id,b.fase_id,a.medio_id,a.fecha_registro
         FROM mfll_contactos_registro as a ,mfll_contactos_fases as b 
         WHERE a.contacto_id=b.contacto_id AND a.evento_id='$evento_id' 
         ORDER BY a.evento_id,b.fase_id,a.nombre,a.apellido_paterno,a.apellido_materno;";
    $res=$db->sql_query($sql);
    $tuplas=$db->sql_numrows($res);
    if( $tuplas > 0)
    {
        include_once("genera_excel.php");
        $array_eventos=Regresa_Eventos($db);
        $array_fases=Regresa_Fases($db);
        $array_unidades=Regresa_Unidades($db);
        $array_tipo_pago=Regresa_Tipos_Pagos($db);
        $array_gids=Regresa_Concesionarias($db);
        $array_uids=Regresa_Vendedores($db);
        $array_medios=Regresa_Medio($db);
        $encabezado="Exporta Prospectos del Evento: ".$array_eventos[$evento_id];
        $buffer.="<table align='center' class='tablesorter' width='100%'>
                 <thead><tr>
                <th>Evento</th>
                <th>Fase</th>
                <th>Concesionaria</th>
                <th>vendedor</th>
                <th>Nombre</th>
                <th>Tel Casa</th>
                <th>Tel Oficina</th>
                <th>Tel Movil</th>
                <th>Tel Otro</th>
                <th>Email</th>
                <th>Medio</th>
                <th>Unidad(es) Manejados</th>
                <th>Unidad(es) Firmado</th>
                <th>Tipo Pago</th>
                <th>Ingreso</th>
                </tr></thead><tbody>";
        while(list($contacto_id,$gid,$uid,$nombre,$tel_casa,$tel_oficina,$tel_movil,$tel_otro,$email,$evento_id,$fase_id,$medio_id,$fecha_ingreso) = $db->sql_fetchrow($res))
        {
            switch($fase_id)
            {
                case 1:
                    $manejados='';
                    $firmado='';
                    break;
                case 2:
                    $manejados=Revisa_Autos_Manejados($db,$contacto_id,$array_unidades);
                    $firmado='';
                    break;
                case 3:
                    $manejados=Revisa_Autos_Manejados($db,$contacto_id,$array_unidades);
                    $firmado=Revisa_Auto_Firmado($db,$contacto_id,$array_unidades,$array_tipo_pago);
                    break;

            }
            //$array_tmp=Regresa_Datos_Firmado($db,$contacto_id,$array_unidades,$array_tipo_pago);
            $buffer.="<tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;height:30px;\" >
                        <td align='left'>".$array_eventos[$evento_id]."</td>
                        <td align='left'>".$array_fases[$fase_id]."</td>
                        <td align='left'>".$array_gids[$gid]."</td>
                        <td align='left'>".$array_uids[$uid]."</td>
                        <td align='left'>".$nombre."</td>
                        <td align='left'>".$tel_casa."</td>
                        <td align='left'>".$tel_oficina."</td>
                        <td align='left'>".$tel_movil."</td>
                        <td align='left'>".$tel_otro."</td>
                        <td align='left'>".$email."</td>
                        <td align='left'>".$array_medios[$medio_id]."</td>
                        <td align='left'>".$manejados."</td>
                        <td align='left'>".$firmado[0]."</td>
                        <td align='left'>".$firmado[1]."</td>
                        <td align='left'>".substr($fecha_ingreso,0,10)."</td>
                    </tr>";
        }
        $buffer.="</tbody><thead><tr><td colspan='15'>Total de Prospectos: ".$tuplas."</td></tr></thead></table>";
        $objeto = new Genera_Excel($buffer,'MFL');
        $boton_excel=$objeto->Obten_href();
    }
    else
    {
        $encabezado="No se encontraron registros con el filtro seleccionado";
    }
}


function Revisa_Autos_Manejados($db,$contacto_id,$array_unidades)
{
    $regreso='';
    $sql_m="SELECT unidad_id FROM mfll_contactos_registro_unidades WHERE contacto_id='".$contacto_id."';";
    $res_m=$db->sql_query($sql_m);
    if($db->sql_numrows($res_m) > 0)
    {
        while(list($unidad) = $db->sql_fetchrow($res_m))
        {
            $regreso.=$array_unidades[$unidad]."<br>";
        }
    }
    return $regreso;
}

function Revisa_Auto_Firmado($db,$contacto_id,$array_unidades,$array_tipo_pago)
{
    $regreso=array();
    $sql_m="SELECT unidad_id,tipo_pago_id FROM mfll_contactos_firma WHERE contacto_id='".$contacto_id."';";
    $res_m=$db->sql_query($sql_m);
    if($db->sql_numrows($res_m) > 0)
    {
        while(list($unidad,$tipo_pago_id) = $db->sql_fetchrow($res_m))
        {
            $reg1.=$array_unidades[$unidad]."<br>";
            $reg2.=$array_tipo_pago[$tipo_pago_id]."<br>";

        }
    }
    $regreso[0]=$reg1;
    $regreso[1]=$reg2;
    return $regreso;
}
?>
