<?php
$array_filtros=array();
$filtro='';
$tablas='';
$campos='';
if($nombre != '')
    $array_filtros[]=" a.nombre LIKE '%".strtoupper($nombre)."%'";
if($apaterno != '')
    $array_filtros[]=" a.apellido_paterno LIKE '%".strtoupper($apaterno)."%'";
if($amaterno != '')
    $array_filtros[]=" a.apellido_materno LIKE '%".strtoupper($amaterno)."%'";

if($evento_id > 0)
    $array_filtros[]=" a.evento_id ='".$evento_id."'";

if($fase_id > 0)
    $array_filtros[]=" c.fase_id ='".$fase_id."'";

if($tmp_gid > 0)
    $array_filtros[]=" a.gid ='".$tmp_gid."'";

if($tmp_uid > 0)
    $array_filtros[]=" a.uid ='".$tmp_uid."'";

if($fecha_ini != "" && $fecha_fin != "")
    $array_filtros[]=" a.fecha_registro between '".$fecha_ini."' and '".$fecha_fin."'";
elseif($fecha_ini != "")
    $array_filtros[]=" a.fecha_registro >= '".$fecha_ini."'";
elseif($fecha_fin != "")
    $array_filtros[]=" a.fecha_registro <= '".$fecha_fin."'";

if(count($array_filtros) > 0)
{
    $filtro  = implode (" AND ",$array_filtros);
}
?>