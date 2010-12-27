<?php
global $db,$_includesdir,$fecha_ini,$fecha_fin,$submit,$evento_id,$evento_id,$id,$tipo;
$titulo="Gr&aacute;fica de Prospectos que compran y manejan el mismo auto";
include_once("funciones.php");
$combo_eventos=Genera_Combo_Eventos($db,$evento_id);
if(isset($submit))
{
    include_once("filtro_fechas.php");
    $array_unidades=Regresa_Unidades($db);
    $array_url=array();
    $array_totales_m=array();
    $array_titulos_m=array();
    $array_totales_f=array();
    $array_titulos_f=array();
    $vacios=0;
    $titulo="Gráfica por Prospectos que compran y manejan mismo auto";
    $tmp_celda="Modelo";
    $total=0;
    $array_compras=Inicializa_Array_Compras($db);
    $array_manejos=Inicializa_Array_Compras($db);
    $array_unidades_id=Inicializa_Array_Compras($db);
    $sql_count_m="SELECT a.contacto_id,a.evento_id,c.fase_id
                FROM mfll_contactos_registro as a ,mfll_contactos_fases as c
                WHERE a.contacto_id=c.contacto_id  AND c.fase_id=3 ".$rango_fechas." ORDER BY c.fase_id,a.contacto_id;";
    
    $res_count_m=$db->sql_query($sql_count_m)  or die("Error en la consulta:  ".$sql_count_m);
    $total =  $db->sql_numrows($res_count_m);
    if( $total > 0)
    {
        while(list($contacto_id,$evento_id,$fase_id) = $db->sql_fetchrow($res_count_m))
        {
            $id_compra=0;
            $id_compra=Regresa_Vehiculo_Comprado($db,$contacto_id);
            if($id_compra > 0)
            {
                $id_maneja=Regresa_Vehiculo_Manejado($db,$contacto_id,$id_compra);
                if( $id_maneja > 0)
                {
                  $array_manejos[$id_compra]=$array_manejos[$id_compra]+ 1;
                }
                $array_compras[$id_compra]=$array_compras[$id_compra] + 1;
            }
        }
        // saco los modelos que no tengan registrada compra y manejado
        $total_autos_comprados=0;
        foreach($array_unidades_id as $clave => $valor)
        {
            if( ($array_compras[$clave] == 0 ) && ($array_manejos[$clave] == 0 )  )
            {
                $vacios++;
            }
            else
            {
                if((0 + $array_compras[$clave])>0)
                    $promedio=number_format( (($array_manejos[$clave]/$array_compras[$clave])*100),0,'.','');
                $total_autos_comprados= $total_autos_comprados + $array_compras[$clave];
                $data1y[]=$array_compras[$clave];
                $data2y[]=$array_manejos[$clave];
                $data_titulos[]=$array_unidades[$clave]."  (".$promedio." %)";

            }
        }
        $titulog=$titulo."\n Total de Compras:  ".$total_autos_comprados;
        // Genero Grafico
        include_once("construye_grafica_p.php");
    }
}

function Regresa_Vehiculo_Manejado($db,$contacto_id,$id_compra)
{
    $reg=0;
    $sql="SELECT unidad_id FROM mfll_contactos_registro_unidades WHERE contacto_id=".$contacto_id." AND unidad_id=".$id_compra.";";
    $res=$db->sql_query($sql) or die("Error en la consulta ".$sql);
    if($db->sql_numrows($res) > 0)
        $reg=$db->sql_fetchfield(0,0,$res);
    return $reg;

}
function Regresa_Vehiculo_Comprado($db,$contacto_id)
{
    $reg=0;
    $sql="SELECT unidad_id FROM mfll_contactos_firma WHERE contacto_id=".$contacto_id.";";
    $res=$db->sql_query($sql) or die("Error en la consulta ".$sql);
    if($db->sql_numrows($res) > 0)
        $reg=$db->sql_fetchfield(0,0,$res);
    return $reg;
}
function Inicializa_Array_Compras($db)
{
    $array=array();
    $sql="SELECT unidad_id FROM crm_unidades ORDER BY nombre;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id) = $db->sql_fetchrow($res))
        {
            $array[$id]=0;
        }
    }
    return $array;    
}
?>