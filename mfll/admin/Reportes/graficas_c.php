<?php
global $db,$_includesdir,$fecha_ini,$fecha_fin,$submit,$evento_id,$evento_id,$id,$tipo;
$titulo="Gr&aacute;fica de Fases (Firma y Llet&aacute;culo  -  Maneja)";
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

    $sql="SELECT contacto_id FROM mfll_contactos_registro WHERE 1 ".$rango_fechas.";";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        //$array_unidades_manejadas=Regresa
    }
    /*$titulo="Gráfica por Fases de Maneja - Firma Llevátelo por modelo";
    $tmp_celda="Modelo";
    $total_maneja=0;
    $sql_count_m="SELECT COUNT(*) AS total_f
                FROM mfll_contactos_registro as a ,mfll_contactos_registro_unidades as b 
                WHERE a.contacto_id=b.contacto_id ".$rango_fechas.";";
    $res_count_m=$db->sql_query($sql_count_m)  or die("Error en la consulta:  ".$sql_count_m);
    if($db->sql_numrows($res_count_m) > 0)
        $total_maneja=$db->sql_fetchfield(0,0,$res_count_m);


    $sql_m="SELECT b.unidad_id as ids ,count(b.unidad_id) as total
            FROM mfll_contactos_registro as a ,mfll_contactos_registro_unidades as b 
            WHERE a.contacto_id=b.contacto_id ".$rango_fechas."
            GROUP BY b.unidad_id ORDER BY b.unidad_id;";    
    $res_m=$db->sql_query($sql_m) or die("Error en la consulta:  ".$sql_m);
    if($db->sql_numrows($res_m) > 0)
    {
        while(list($id,$total) = $db->sql_fetchrow($res_m))
        {
            $array_totales_m[$id]=$total;
            $array_titulos_m[$id]=$array_unidades[$id];
        }
    }
    $sql_count_f="SELECT COUNT(*) AS total_f
                FROM mfll_contactos_registro as a ,mfll_contactos_firma as b
                WHERE a.contacto_id=b.contacto_id ".$rango_fechas.";";
    $res_count_f=$db->sql_query($sql_count_f)  or die("Error en la consulta:  ".$sql_count_f);
    if($db->sql_numrows($res_count_f) > 0)
        $total_firma=$db->sql_fetchfield(0,0,$res_count_f);


    $sql_f="SELECT b.unidad_id as ids ,count(b.unidad_id) as total
            FROM mfll_contactos_registro as a ,mfll_contactos_firma as b 
            WHERE a.contacto_id=b.contacto_id ".$rango_fechas."
            GROUP BY b.unidad_id ORDER BY b.unidad_id;";
    $res_f=$db->sql_query($sql_f) or die("Error en la consulta:  ".$sql_f);
    if($db->sql_numrows($res_f) > 0)
    {
        while(list($id,$total) = $db->sql_fetchrow($res_f))
        {
            $array_totales_f[$id]=$total;
            $array_titulos_f[$id]=$array_unidades[$id];
        }
    }
    if( (count($array_totales_m)>0) || (count($array_totales_f)>0))
    {
        $data_titulos=array();
        foreach($array_unidades as $clave => $valor)
        {
            $promedio='0.0';
            if( (array_key_exists($clave,$array_totales_m)) or (array_key_exists($clave,$array_totales_f)) )
            {
                $data1y[]=(0 + $array_totales_m[$clave]);
                $data2y[]=(0 + $array_totales_f[$clave]);

                if((0 + $array_totales_m[$clave])>0)
                    $promedio=number_format( (($array_totales_f[$clave]/$array_totales_m[$clave])*100),1,'.','');
                
                $data_titulos[]=$valor."  (".$promedio.")";
            }
        }
        #include_once("construye_grafica_f.php");
    }
    else
    {
        $_grafico =  "<span style=\"color:#3e4f88;font-weight:bold;\">No se recuperaron registros con el filtro seleccionado</span>";
    }*/
}
?>