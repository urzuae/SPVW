<?php
global $db,$_includesdir,$fecha_ini,$fecha_fin,$id,$tipo,$evento_id;
include_once("funciones.php");
include_once("filtro_fechas.php");
$archivo='blanco.png';
$array_fases=Regresa_Fases($db);
$array_unidades=Regresa_Unidades($db);
$array_tipo_pagos=Regresa_Tipos_Pagos($db);
$array_medio=Regresa_Medio($db);
$total_bd=0;
$total_unicos=0;
if($id==1)
{
    $titulos="Gráficas de la Fase ".$array_fases[$id]."";
}
if($id==2)
{
    $titulos="Gráficas de la Fase ".$array_fases[$id]." ";
}
if($id==3)
{
    $titulos="Gráficas de la Fase ".$array_fases[$id]." (Modelos - Forma de Pago)";
}


switch($id)
{
    case 1:
    {
        $sql_count="SELECT COUNT(a.contacto_id) as total_f FROM mfll_contactos_registro  as a ,mfll_contactos_fases as b WHERE a.contacto_id=b.contacto_id  ".$rango_fechas." AND b.fase_id >=1;";
        $sql="SELECT a.medio_id as ids ,count(a.medio_id) as total FROM mfll_contactos_registro as a,mfll_contactos_fases as b WHERE a.contacto_id=b.contacto_id ".$rango_fechas." AND b.fase_id>=1 GROUP BY a.medio_id ORDER BY a.medio_id;";
        $tmp_celda="Total de prospectos por medio de contacto";
        $titulo=" Grafica por No. de Prospectos x medio de contacto";
        break;
    }
    case 2:
    {
        $sql_delete="DROP TABLE IF EXISTS paso;";
        $db->sql_query($sql_delete);
        $create="CREATE TABLE paso AS SELECT a.contacto_id,a.evento_id,b.fase_id FROM mfll_contactos_registro  as a ,mfll_contactos_fases as b WHERE a.contacto_id=b.contacto_id  ".$rango_fechas." AND fase_id>1;";
        $db->sql_query($create);
        $sql_count="SELECT count(b.unidad_id) as total_f FROM paso as a,mfll_contactos_registro_unidades as b WHERE a.contacto_id=b.contacto_id ".$rango_fechas." AND fase_id>1;";
        $sql="SELECT b.unidad_id as ids ,count(b.unidad_id) as total FROM paso as a,mfll_contactos_registro_unidades as b WHERE a.contacto_id=b.contacto_id ".$rango_fechas." AND fase_id>1 GROUP BY unidad_id ORDER BY unidad_id;";
        $tmp_celda="Modelo";
        $titulo=" Grafica por No. de Prospectos x unidades";
        break;

    }
    case 3:
    {
        #$sql_count="SELECT COUNT(*) as total_f FROM mfll_contactos_registro  as a ,mfll_contactos_fases as b WHERE a.contacto_id=b.contacto_id  ".$rango_fechas." AND fase_id=3;";
        $sql_delete="DROP TABLE IF EXISTS paso_3;";
        $db->sql_query($sql_delete);
        $create="CREATE TABLE paso_3 AS SELECT a.contacto_id,a.evento_id,b.fase_id FROM mfll_contactos_registro  as a ,mfll_contactos_fases as b WHERE a.contacto_id=b.contacto_id  ".$rango_fechas." AND fase_id=3;";
        $db->sql_query($create);
        $sql_count="SELECT count(b.unidad_id) as total_f FROM paso_3 as a,mfll_contactos_firma as b WHERE a.contacto_id=b.contacto_id ".$rango_fechas." AND fase_id=3;";
        $sql_count_unicos="SELECT COUNT(DISTINCT(a.contacto_id)) FROM paso_3 as a,mfll_contactos_firma as b WHERE a.contacto_id=b.contacto_id ".$rango_fechas." AND fase_id=3;";
        $sql="SELECT b.unidad_id as ids ,count(b.unidad_id) as total FROM paso_3 as a,mfll_contactos_firma as b WHERE a.contacto_id=b.contacto_id ".$rango_fechas." AND fase_id=3 GROUP BY unidad_id ORDER BY unidad_id;";
        $tmp_celda="Modelo";
        $titulo=" Grafica por No. de Prospectos x Modelos";
        break;
    }
}
$res_count=$db->sql_query($sql_count);
if($db->sql_numrows($res_count) > 0)
    $total_bd=$db->sql_fetchfield(0,0,$res_count);
if($id== 3)
{
    $res_count_unicos=$db->sql_query($sql_count_unicos);
    if($db->sql_numrows($res_count_unicos)>0)
    {
        $total_unicos=$db->sql_fetchfield(0,0,$res_count_unicos);
    }
}

$res=$db->sql_query($sql);
if($db->sql_numrows($res) > 0)
{
    $tabla_resul="<table width='100%'>
                    <thead><tr><th>$tmp_celda</th><th>Totales</th><th>Porcentaje</th></tr></thead><tbody>";
    while(list($ids,$total) = $db->sql_fetchrow($res))
    {
        $data_valores[]=$total;
        $data_ids[]=$ids;
        $totales=$totales + $total;
        if($id == 1)
        {
            $data_titulos[$ids]=" ".$array_medio[$ids]." (".$total.")";
            $tmp=$array_medio[$ids];
        }
        if($id >= 2)
        {
            $data_titulos[$ids]=" ".$array_unidades[$ids]." (".$total.")";
            $tmp=$array_unidades[$ids];
        }
        $promedio=0;
        if($total_bd != 0)  $promedio=(($total/$total_bd)*100);
        $promedio_total= $promedio_total + $promedio;
        $tabla_resul.="<tr><td align='left'>".$tmp."</td><td align='center'>".$total."</td><td align='right'>".number_format($promedio,2,'.','')."&nbsp;%</td></tr>";
    }
    $tabla_resul.="</tbody><thead><tr><td align='left'>Total:</td><td align='center'>".$totales."</td><td align='right'>".number_format($promedio_total,2,'.','')."&nbsp;%</td></tr></thead></thead>
                    <thead><tr><td colspan='4'>Total de Prospectos Unicos:  ".$total_unicos."</th></tr></thead></table>";
    include_once("construye_grafica.php");
    if($id == 3)
    {
        $array_clase_pagos=array(1 => 'Banco',2 => 'Banco',3 => 'Contado',4 => 'VW Bank',5 => 'VW Bank',6 => 'VW Bank');
        $titulo=" Grafica por No. de Prospectos x Forma de Pago";
        $data_valores_t=array();
        $data_ids_t=array();
        $data_titulos_t=array();
        $totales=0;
        $promedio_total=0;
        $tmp='';

        $sql="SELECT b.tipo_pago_id as ids ,count(b.tipo_pago_id) as total FROM paso_3 as a,mfll_contactos_firma as b WHERE a.contacto_id=b.contacto_id ".$rango_fechas." AND fase_id=3 GROUP BY tipo_pago_id ORDER BY tipo_pago_id;";
                    $tmp_celda="Tipo Pago";
        $res=$db->sql_query($sql);
        if($db->sql_numrows($res) > 0)
        {
            $tabla_resul_t="<table width='100%'>
                    <thead><tr><th>Categoria</th><th>$tmp_celda</th><th>Totales</th><th>Porcentaje</th></tr></thead><tbody>";
            while(list($ids,$total) = $db->sql_fetchrow($res))
            {
                $data_valores_t[]=$total;
                $data_ids_t[]=$ids;
                $totales=$totales + $total;

                $data_titulos_t[$ids]=" ".$array_tipo_pagos[$ids]." (".$total.")";
                $tmp=$array_tipo_pagos[$ids];
                
                $promedio=0;
                if($total_bd != 0)  $promedio=(($total/$total_bd)*100);

                $promedio_total= $promedio_total + $promedio;
                $tabla_resul_t.="<tr><td align='left'>".$array_clase_pagos[$ids]."</td><td align='left'>".$tmp."</td><td align='center'>".$total."</td><td align='right'>".number_format($promedio,2,'.','')."&nbsp;%</td></tr>";
            }
            $tabla_resul_t.="</tbody><thead><tr><td align='left'>Total:</td><td align='center'>".$totales."</td><td align='right'>".number_format($promedio_total,2,'.','')."&nbsp;%</td><td></td></tr></thead></thead>
                    <thead><tr><td colspan='4'>Total de Prospectos Unicos:  ".$total_unicos."</th></tr></thead></table>";

            include_once("construye_grafica_c.php");
        }
    }
}
else
{
    $_grafico =  "No hay registros en ese rango de fechas";
}
$url_reg="index.php?_module=Reportes&_op=graficas&tipo=".$tipo."&fecha_ini=".$fecha_ini."&fecha_fin=".$fecha_fin."&evento_id=".$evento_id."&submit=sumbit";
$boton_regreso="<a href='".$url_reg."'>Regresar</a>";
?>