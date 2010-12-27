<?php
if($fecha_ini != "" && $fecha_fin != "")
    $rango_fechas = " and a.fecha_registro between '".$fecha_ini."' and '".$fecha_fin."'";
elseif($fecha_ini != "")
    $rango_fechas = " and a.fecha_registro >= '".$fecha_ini."'";
elseif($fecha_fin != "")
    $rango_fechas = " and a.fecha_registro <= '".$fecha_fin."'";

if($evento_id > 0)
    $rango_fechas .= " and a.evento_id='".$evento_id."' ";

 if($fase_id > 0 )
    $rango_fechas .= " and b.fase_id = '".$fase_id."' ";
?>
