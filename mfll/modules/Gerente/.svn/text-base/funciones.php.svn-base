<?php
/*********** FUNCIONES AUXILIARES *********/

/**
 * Funcion que regresa un array con el id y nombre de las concesionarias
 * @param <type> $db conexion a la base de datos
 * @return <type> $array, array de consecionarias
 */
function Regresa_Concesionarias($db)
{
    $array=array();
    $sql="SELECT gid,name FROM groups ORDER BY gid;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $array[$id]=$nombre;
        }
    }
    return $array;
}

/**
 * Funcion que regresa un array con el id y nombre de las fases
 * @param <type> $db conexion a la base de datos
 * @return <type> $array, array de fases
 */
function Regresa_Fases($db)
{
    $array=array();
    $sql="SELECT fase_id,fase_nombre FROM mfll_fases ORDER BY fase_id;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $array[$id]=$nombre;
        }
    }
    return $array;
}

function Regresa_Unidades($db)
{
    $array=array();
    $sql="SELECT unidad_id,nombre FROM crm_unidades ORDER BY nombre;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $array[$id]=$nombre;
        }
    }
    return $array;
}
function Regresa_Vendedores($db)
{
    $array=array();
    $sql="SELECT uid,name FROM users ORDER BY name;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $array[$id]=$nombre;
        }
    }
    return $array;
}

function Regresa_Eventos($db)
{
    $array=array();
    $sql="SELECT evento_id,evento_nombre FROM mfll_eventos ORDER BY evento_id;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $array[$id]=$nombre;
        }
    }
    return $array;
}

function Regresa_Tipos_Pagos($db)
{
    $array=array();
    $sql="SELECT tipo_pago_id,tipo_pago_nombre FROM mfll_tipo_pagos ORDER BY tipo_pago_id;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $array[$id]=$nombre;
        }
    }
    return $array;
}
function Genera_Combo_Eventos($db,$evento_id)
{
    $buffer='';
    $sql="SELECT evento_id,evento_nombre FROM mfll_eventos ORDER BY evento_nombre;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        $buffer.="<select name='evento_id' id='evento_id' style='width:180px; border:1px solid #cdcdcd;color:#000000;'>
                  <option value='0'></option>";
        while(list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $tmp='';
            if($id == $evento_id)   $tmp=' SELECTED ';
            $buffer.="<option value='".$id."' ".$tmp." >".$nombre."</option>";
        }
        $buffer.="</select>";
    }
    return $buffer;
}
function Genera_Combo_Fases($db,$id_fase)
{
    $buffer='';
    $sql="SELECT fase_id,fase_nombre FROM mfll_fases ORDER BY fase_id;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        $buffer.="<select name='fase_id' id='fase_id' style='width:180px; border:1px solid #cdcdcd;color:#000000;'>
                  <option value='0'></option>";
        while(list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $tmp='';
            if($id == $id_fase)   $tmp=' SELECTED ';
            $buffer.="<option value='".$id."' ".$tmp." >".$nombre."</option>";
        }
        $buffer.="</select>";
    }
    return $buffer;
}
function Genera_Combo_Unidades($db,$id_unidad)
{
    $buffer='';
    $sql="SELECT unidad_id,nombre FROM crm_unidades ORDER BY nombre;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        $buffer.="<select name='unidad_id' id='unidad_id' style='width:180px; border:1px solid #cdcdcd;color:#000000;'>
                  <option value='0'></option>";
        while(list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $tmp='';
            if($id == $id_unidad)   $tmp=' SELECTED ';
            $buffer.="<option value='".$id."' ".$tmp." >".$nombre."</option>";
        }
        $buffer.="</select>";
    }
    return $buffer;
}
function Genera_Combo_Tipo_Pago($db,$id_tipo_pago)
{
    $buffer='';
    /*$sql="SELECT tipo_pago_id,tipo_pago_nombre FROM mfll_tipo_pagos ORDER BY tipo_pago_id;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {*/
        $buffer.="<select name='tipo_pago_id' id='tipo_pago_id' style='width:180px; border:1px solid #cdcdcd;color:#000000;'>
                  <option value='0'></option>";
       /* while(list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $tmp='';
            if($id == $id_tipo_pago)   $tmp=' SELECTED ';
            $buffer.="<option value='".$id."' ".$tmp." >".$nombre."</option>";
        }*/
        $buffer.="</select>";
    //}
    return $buffer;
}
function Genera_combo_Concesionarias($db,$gid)
{
    $buffer='';
    $sql="SELECT gid,name FROM groups WHERE gid> 3 ORDER BY gid;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        $buffer.="<select name='tmp_gid' id='tmp_gid' style='width:320px; border:1px solid #cdcdcd;color:#000000;'>
                  <option value='0'></option>";
        while(list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $tmp='';
            if($id == $gid)   $tmp=' SELECTED ';
            $buffer.="<option value='".$id."' ".$tmp." >".$id."  -  ".$nombre."</option>";
        }
        $buffer.="</select>";
    }
    return $buffer;
}
function Genera_combo_Vendedores($db,$uid)
{
    $buffer.="<select name='tmp_uid' id='tmp_uid' style='width:260px; border:1px solid #cdcdcd;color:#000000;'>
              <option value='0'></option></select>";
    return $buffer;
}
function Regresa_Medio($db)
{
    $array=array();
    $sql="SELECT medio_id,medio FROM mfll_medios ORDER BY medio_id;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$nombre) = $db->sql_fetchrow($res))
        {
            $array[$id]=$nombre;
        }
    }
    return $array;
}

function Regresa_Datos_Firmado($db,$contacto_id,$array_unidades,$array_tipo_pago)
{
    $array=array();
    $tmp_u='';
    $tmp_t='';
    $sql="SELECT unidad_id,tipo_pago_id FROM  mfll_contactos_firma WHERE contacto_id=".$contacto_id.";";
    $res=$db->sql_query($sql) or die("Eror en el query");
    if($db->sql_numrows($res)>0)
    {
        while(list($unidad_id,$tipo_pago_id) = $db->sql_fetchrow($res))
        {
            $tmp_u.=$array_unidades[$unidad_id]."<br>";
            $tmp_t.=$array_tipo_pago[$tipo_pago_id]."<br>";
        }
    }
    $array[0]=$tmp_u;
    $array[1]=$tmp_t;
    return $array;
}
function Regresa_Id_Fase($db,$contacto_id)
{
    $id_fase=0;
    $sql="SELECT fase_id FROM  mfll_contactos_fases WHERE contacto_id=".$contacto_id.";";
    $res=$db->sql_query($sql) or die("Eror en el query");
    if($db->sql_numrows($res)>0)
    {
        $id_fase=$db->sql_fetchfield(0,0,$res);
    }
    return $id_fase;
}
function Regresa_Id_Unidades_Firma($db,$contacto_id)
{
    $array_unidades=array();
    $array_unidades=Regresa_Id_Unidades_Maneja($db,$contacto_id);
    $sql="SELECT unidad_id FROM  mfll_contactos_firma WHERE contacto_id=".$contacto_id." AND unidad_id>0;";
    $res=$db->sql_query($sql) or die("Eror en el query");
    if($db->sql_numrows($res)>0)
    {
        while(list($unidad_id) = $db->sql_fetchrow($res))
        {
            if(!in_array($unidad_id,$array_unidades))
            {
            $array_unidades[]=$unidad_id;
            }
            
        }
    }
    return $array_unidades;
}
function Regresa_Id_TPagos_Firma($db,$contacto_id)
{
    $array_t_pago=array();
    $sql="SELECT tipo_pago_id FROM  mfll_contactos_firma WHERE contacto_id=".$contacto_id." AND tipo_pago_id > 0;";
    $res=$db->sql_query($sql) or die("Eror en el query");
    if($db->sql_numrows($res)>0)
    {
        while(list($tpago_id) = $db->sql_fetchrow($res))
        {
            $array_t_pago[]=$tpago_id;
        }
    }
    return $array_t_pago;
}
function Regresa_Id_Unidades_Maneja($db,$contacto_id)
{
    $array_unidades=array();
    $sql="SELECT unidad_id FROM  mfll_contactos_registro_unidades WHERE contacto_id=".$contacto_id." AND unidad_id>0;";
    $res=$db->sql_query($sql) or die("Eror en el query");
    if($db->sql_numrows($res)>0)
    {
        while(list($unidad_id) = $db->sql_fetchrow($res))
        {
            $array_unidades[]=$unidad_id;
        }
    }
    return $array_unidades;
}

?>