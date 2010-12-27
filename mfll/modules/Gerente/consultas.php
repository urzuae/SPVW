<?php
if (!defined('_IN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db,$_includesdir,$nombre,$apaterno,$amaterno,$evento_id,$fase_id,$unidad_id,$tipo_pago_id,$tmp_gid,$tmp_uid,$fecha_ini,$fecha_fin,$submit;
$titulo="Busqueda de Prospectos";
$encabezado="No se encontraron registros con el filtro seleccionado";
include_once("funciones.php");
$combo_eventos=Genera_Combo_Eventos($db,$evento_id);
$combo_fases=Genera_Combo_Fases($db,$fase_id);
$combo_modelos=Genera_Combo_Unidades($db,$unidad_id);
$combo_pagos=Genera_Combo_Tipo_Pago($db,$tipo_pago_id);
$combo_concesionarias=Genera_combo_Concesionarias($db,$tmp_gid);
$combo_vendedores=Genera_combo_Vendedores($db,$tmp_uid);
$evento_nom=Genera_Evento($db);
if(isset($submit))
{
    include_once("genera_excel.php");
    include_once("filtro_busquedas.php");
    if($filtro != '')   $filtro= "AND ".$filtro;

    $sql="SELECT a.contacto_id,concat(a.nombre,' ',a.apellido_paterno,' ',apellido_materno) AS nombre,
          c.fase_id,a.gid,a.uid,DATE_FORMAT(a.fecha_registro,'%d/%m/%Y %H:%i:%s'),a.tel_casa,a.tel_oficina,a.tel_movil,a.tel_otro,a.email,a.evento_id ".$campos."
          FROM mfll_contactos_registro AS a LEFT JOIN mfll_contactos_fases as c ON a.contacto_id = c.contacto_id
            WHERE 1 ".$filtro." ORDER BY c.fase_id,a.nombre,a.apellido_paterno,a.apellido_materno;";
    $res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
    $tuplas=$db->sql_numrows($res);
    if($tuplas > 0)
    {
        $array_eventos=Regresa_Eventos($db);
        $array_fases=Regresa_Fases($db);
        $array_unidades=Regresa_Unidades($db);
        $array_tipo_pago=Regresa_Tipos_Pagos($db);
        $array_gids=Regresa_Concesionarias($db);        
        $array_uids=Regresa_Vendedores($db);        
        $encabezado="Listado de prospectos por filtro seleccionado";
        $buffer="<table align='center' class='tablesorter' width='100%'>
                 <thead><tr>
                <th width='12%'>Evento</th>
                <th width='8%'>Fase</th>
                <th width='20%'>Nombre</th>
                <th width='15%'>Concesionaria</th>
                <th width='13%'>Vendedor</th>
                <th width='9%'>M. Manejados</th>
                <th width='9%'>M. Firmado</th>
                <th width='6%'>Tipo Pago</th>
                <th width='9%'>Ingreso</th></tr></thead><tbody>";
        $buffer_excel="<table align='center' class='tablesorter' width='100%'>
                 <thead><tr>
                <th>Evento</th>
                <th>Fase</th>
                <th>Nombre</th>
                <th>Concesionaria</th>
                <th>Vendedor</th>
                <th>Unidad manejadas</th>
                <th>Unidad Firmadas</th>
                <th>Tipo Pago</th>
                <th>Tel casa</th>
                <th>Tel Oficina</th>
                <th>Tel movil</th>
                <th>Tel Otro</th>
                <th>Email</th>
                <th>Ingreso</th></tr></thead><tbody>";
        $total_registros=0;
        while(list($contacto_id,$nombre,$fase_id,$gid,$uid,$timestamp,$tel_casa,$tel_oficina,$tel_movil,$tel_otro,$email,$evento_id) = $db->sql_fetchrow($res))
        {
            $unidades_manejadas='';
            $unidades_firmadas='';
            $tipos_pagos='';
            if($fase_id == 1)
            {
                if( ($unidad_id == 0) && ($tipo_pago_id == 0) )
                {
                    $unidades_manejadas='No selecciono';
                    $unidades_firmadas='No selecciono';
                    $tipos_pagos='Ninguna';
                }
            }

            if($fase_id == 2)
            {
                if($tipo_pago_id == 0)
                {
                    $unidades_manejadas=Regresa_Unidades_Manejadas($db,$contacto_id,$array_unidades,$unidad_id);
                }
            }
            if($fase_id == 3)
            {
                if($tipo_pago_id == 0)
                {
                    $unidades_manejadas=Regresa_Unidades_Manejadas($db,$contacto_id,$array_unidades,$unidad_id);                 
                }
                else
                {
                    $tmp_unidades_manejadas=Regresa_Unidades_Manejadas($db,$contacto_id,$array_unidades,$unidad_id);
                }
                $unidades_firmadas=Regresa_Unidades_Firmadas($db,$contacto_id,$array_unidades,$unidad_id,$tipo_pago_id);
                $tipos_pagos=Regresa_Tipos_Pagos_Firmado($db,$contacto_id,$array_tipo_pago,$unidad_id,$tipo_pago_id);
            }
            if( (strlen($unidades_manejadas) > 0) || (strlen($unidades_firmadas) > 0) || (strlen($tipos_pagos) > 0))
            {
                $total_registros++;
                $buffer.="<tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;height:30px;\" >
                        <td>".$array_eventos[$evento_id]."</td>
                        <td>".$array_fases[$fase_id]."</td>
                        <td>".$nombre."</td>
                        <td>".$array_gids[$gid]."</td>
                        <td>".$array_uids[$uid]."</td>
                        <td>".$unidades_manejadas.''.$tmp_unidades_manejadas."</td>
                        <td>".$unidades_firmadas."</td>
                        <td>".$tipos_pagos."</td>
                        <td>".$timestamp."</td>
                    </tr>";

                $buffer_excel.="<tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;height:30px;\" >
                        <td align='left'>".$array_eventos[$evento_id]."</td>
                        <td align='left'>".$array_fases[$fase_id]."</td>
                        <td align='left'>".$nombre."</td>
                        <td align='left'>".$array_gids[$gid]."</td>
                        <td align='left'>".$array_uids[$uid]."</td>
                        <td>".$unidades_manejadas."</td>
                        <td>".$unidades_firmadas."</td>
                        <td>".$tipos_pagos."</td>
                        <td>".$tel_casa."</td>
                        <td>".$tel_oficina."</td>
                        <td>".$tel_movil."</td>
                        <td>".$tel_otro."</td>
                        <td>".$email."</td>
                        <td>".$timestamp."</td>
                        </tr>";
            }
        }
        $buffer.="</tbody><thead><tr><td colspan='9'>Total de Prospectos: ".$total_registros."</td></tr></thead></table>";
        $buffer_excel.="</tbody><thead><tr><td colspan='13'>Total de Prospectos: ".$total_registros."</td></tr></thead></table>";
        $objeto = new Genera_Excel($buffer_excel,'MFL');
        $boton_excel=$objeto->Obten_href();
    }
}
function Genera_Evento($db)
{
    $nom='';
    $sql="select evento_nombre FROM mfll_eventos  order by timestamp desc limit 1;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res)>0)
        $nom=$db->sql_fetchfield(0,0,$res);
    return $nom;

}
function Regresa_Unidades_Manejadas($db,$contacto_id,$array_unidades,$unidad_id)
{
    $filtro='';
    $buffer='';
    if($unidad_id > 0)  $filtro=" AND unidad_id='".$unidad_id."' ";

    $sql="SELECT unidad_id FROM mfll_contactos_registro_unidades WHERE contacto_id=".$contacto_id." ".$filtro.";";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res)>0)
    {
        while(list($unidad_id) = $db->sql_fetchrow($res))
        {
            $buffer.=$array_unidades[$unidad_id]."<br>";
        }
    }
    return $buffer;
}

function Regresa_Unidades_Firmadas($db,$contacto_id,$array_unidades,$unidad_id,$tipo_pago_id)
{
    $filtro='';
    $buffer='';
    if($unidad_id > 0)  $filtro=" AND unidad_id='".$unidad_id."' ";
    if($tipo_pago_id > 0)   $filtro.=" AND 	tipo_pago_id='".$tipo_pago_id."' ";
    
    $sql="SELECT unidad_id FROM mfll_contactos_firma WHERE contacto_id=".$contacto_id." ".$filtro.";";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res)>0)
    {
        while(list($unidad_id) = $db->sql_fetchrow($res))
        {
            $buffer.=$array_unidades[$unidad_id]."<br>";
        }
    }
    return $buffer;
}
function Regresa_Tipos_Pagos_Firmado($db,$contacto_id,$array_tipo_pago,$unidad_id,$tipo_pago_id)
{
    $filtro='';
    $buffer='';
    if($unidad_id > 0)  $filtro=" AND unidad_id='".$unidad_id."' ";
    if($tipo_pago_id > 0)   $filtro.=" AND 	tipo_pago_id='".$tipo_pago_id."' ";
    
    $sql="SELECT tipo_pago_id  FROM mfll_contactos_firma WHERE contacto_id=".$contacto_id." ".$filtro.";";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res)>0)
    {
        while(list($tipo_pago_id) = $db->sql_fetchrow($res))
        {
            $buffer.=$array_tipo_pago[$tipo_pago_id]."<br>";
        }
    }
    return $buffer;
}

?>