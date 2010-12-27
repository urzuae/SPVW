<?php
if (!defined('_IN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $id,$fecha_ini,$fecha_fin,$evento_id,$fase_id;
include_once("filtro_fechas.php");
include_once("funciones.php");
$array_eventos=Regresa_Eventos($db);
$array_contactos_cargados=array();
$sql="SELECT a.contacto_id,a.gid,a.uid,a.nombre,a.apellido_paterno,a.apellido_materno,a.tel_casa,a.tel_oficina,a.tel_movil,a.tel_otro,a.email,a.medio_id,a.evento_id
      FROM mfll_contactos_registro as a LEFT JOIN mfll_contactos_fases AS b ON a.contacto_id=b.contacto_id WHERE a.cargado=0 and a.contacto_id>0 AND a.gid>0 AND a.uid>0 ".$rango_fechas." ORDER BY a.contacto_id;";

$res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
if($db->sql_numrows($res)>0)
{
    while(list($contacto_id,$gid,$uid,$nombre,$apellido_paterno,$apellido_materno,$tel_casa,$tel_oficina,$tel_movil,$tel_otro,$email,$medio_id,$evento_id)= $db->sql_fetchrow($res))
    {
        $array_contactos_cargados[]=$contacto_id;
        $array_unidades[$contacto_id]=array();
        $array_tpagos[$contacto_id]=array();
        $fase_id=Regresa_Id_Fase($db,$contacto_id);
        if( ($fase_id>0) && ($fase_id<4) )
        {
            if($fase_id == 1)
            {
                $array_unidades[$contacto_id]=array(4);
            }
            if($fase_id == 2)
            {
                $array_unidades[$contacto_id]=Regresa_Id_Unidades_Maneja($db,$contacto_id);
            }
            if($fase_id == 3)
            {
                $array_unidades[$contacto_id]=Regresa_Id_Unidades_Firma($db,$contacto_id);
                $array_tpagos[$contacto_id]=Regresa_Id_TPagos_Firma($db,$contacto_id);
            }
            $tipo_pago_id=0;
            $evento_nombre=$array_eventos[$evento_id];
            $tmp=$gid.'|'.$uid.'|'.$fase_id.'|'.$nombre.'|'.$apellido_paterno.'|'.$apellido_materno.'|'.$tel_casa.'|'.$tel_oficina.'|'.$tel_movil.'|'.$tel_otro.'|'.$email.'|'.$medio_id.'|'.$evento_nombre;
            $array[$contacto_id]=explode('|',$tmp);
        }
    }
    if(count($array_contactos_cargados) > 0)
    {
        $lista_contactos=implode(',',$array_contactos_cargados);
        $db->sql_query("UPDATE mfll_contactos_registro set cargado=1 WHERE cargado=0 AND contacto_id IN (".$lista_contactos.");");
    }
    include_once("clases/Envia_Prospectos_SPVW.class.php");
    $obj = new Envia_Prospectos_SPVW($array,$array_unidades,$array_tpagos);
    $message=$obj->Obten_Resultados();
}
else
{
    $message="<br>No hay registros para cargar al SPVW<br>";
}
?>