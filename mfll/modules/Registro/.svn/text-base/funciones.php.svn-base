<?php
function registro($db,$contacto_id)
{
    $nombre_contacto='';
    $sql = "SELECT concat(nombre,' ',apellido_paterno,' ',apellido_materno) as nombre_contacto
            FROM mfll_contactos_registro WHERE contacto_id='$contacto_id'";
    $result = $db->sql_query($sql) or die("Error al consultar datos del contacto".$sql);
    if($db->sql_numrows($result)>0)
        $nombre_contacto= $db->sql_fetchfield(0,0,$result);
    return $nombre_contacto;
}

function regresa_concesionaria_eventos($db,$evento_id)
{
    $tmp='';
    $sql="SELECT a.evento_id, a.gid, b.name FROM mfll_eventos_concesionarias as a, groups as b where a.evento_id='$evento_id' AND a.gid=b.gid;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res)>0)
    {
        $tmp.="<select name='concesionaria' id='concesionaria' class='seleccion'>
                <option value='0'>Selecciona Concesionaria</option>";
        while($fila = $db->sql_fetchrow($res))
        {
            $tmp.="<option value='".$fila['gid']."' >".$fila['gid']."  ".$fila['name']."</option>";
        }
        $tmp.="</select>";
    }
    return $tmp;
}

function medio_entero($db,$medio_id)
{
    $tmp='';
    $sql="SELECT medio_id,medio FROM  mfll_medios ORDER BY medio";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res)>0)
    {
        $tmp.="<select name='medio' id='medio' class='seleccion'>
                <option value='0'>Selecciona medio de contacto</option>";

        while( list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $x="";
            if($id == $medio_id)
                $x=" SELECTED ";
            $tmp.="<option value='".$id."' ".$x.">".$nombre."</option>";
        }
        $tmp.="</select>";
    }
    return $tmp;
}

function Regresa_Ultimo_Evento($db)
{
    $evento_id=0;
    $sql = "SELECT evento_id,evento_nombre FROM mfll_eventos ORDER BY timestamp desc limit 1;";
    $result = $db->sql_query($sql) or die("Error al consultar datos del contacto".$sql);
    if($db->sql_numrows($result)>0)
    {
        $evento_id=$db->sql_fetchfield(0,0,$result);
    }
    return $evento_id;
}

function muestra_autos($db)
{
    $tmp='';
    $sql="SELECT unidad_id,nombre FROM crm_unidades ORDER BY nombre";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res)>0)
    {
        $tmp.="<select name='unidad_id' id='unidad_id' class='seleccion'>
                <option value='0'>Selecciona Autom&oacute;vil</option>";
        while(list($unidad_id,$nombre) = $db->sql_fetchrow($res))
        {
            $tmp.="<option value='".$unidad_id."'>".$nombre."</option>";
        }
        $tmp.="</select>";
    }
    return $tmp;
}
function Genera_tabla($db,$contacto_id)
{
    $buf='';
    $sql="SELECT a.contacto_id,a.unidad_id,b.nombre FROM mfll_contactos_registro_unidades as a LEFT JOIN crm_unidades as b
          ON a.unidad_id=b.unidad_id WHERE contacto_id=".$contacto_id.";";
    $res=$db->sql_query($sql) or die("Error en la consulta  ".$sql);
    if($db->sql_numrows($res) >0)
    {
        $conta=1;
        $buf.="<table width='40%' align='center' border='0'>
                <thead><tr><th>No</th><th>Unidad Seleccionada</th></tr></thead><tbody>";
        while(list($contacto_id,$unidad_id,$nombre) = $db->sql_fetchrow($res))
        {
            $buf.="<tr><td>".$conta."</td><td>".$nombre."</td></tr>";
            $conta++;
        }
        $buf.="</tbody></table>";
    }
    return $buf;
}
function Total_vehiculos($db,$contacto_id)
{
    $reg=0;
    $sql="SELECT unidad_id FROM mfll_contactos_registro_unidades WHERE contacto_id=".$contacto_id.";";
    $res=$db->sql_query($sql) or die("Error en la consulta  ".$sql);
    $num=$db->sql_numrows($res);
    if($num >0)
    {
        $reg=$num;
    }
    return $reg;
}
function Regresa_Pruebas_Manejo($db,$contacto_id)
{

    $sql = "SELECT a.nombre, b.contacto_id, b.unidad_id FROM crm_unidades as a, mfll_contactos_registro_unidades as b
            WHERE b.contacto_id ='$contacto_id' AND b.unidad_id= a.unidad_id";

    $result = $db->sql_query($sql) or die("Error al consultar datos del contacto".$sql);
    if($db->sql_numrows($result)>0)
    {
        while(list($nombre) = $db->sql_fetchrow($result))
        {
            $vehiculos.=$nombre."<br> ";
        }
    }
        return $vehiculos;
}

function regresa_pago($db,$tipo_pago_nombre)
{
    $tmp='';
    $sql="SELECT * FROM mfll_tipo_pagos ORDER BY tipo_pago_id DESC";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res)>0)
    {
        $tmp.="<select name='pago' id='pago' class='seleccion'><option value='0'>Selecciona Tipo de pago</option>";

        while($fila = $db->sql_fetchrow($res))
        {
            $x="";
            if($fila['tipo_pago_id'] == $tipo_pago_id)
                $x=" SELECTED ";
            $tmp.="<option value='".$fila['tipo_pago_id']."' ".$x.">".$fila['tipo_pago_nombre']."</option>";
        }
        $tmp.="</select>";
    }
    return $tmp;
}
?>