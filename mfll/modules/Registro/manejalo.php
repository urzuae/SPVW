<?php
global $db,$contacto_id,$unidad_id,$_themedir,$agregar,$_theme,$_op,$_module;
$_theme = "sinmenu";
$_themedir = "themes/".$_theme;
include_once("funciones.php");
$nombre_prospecto=registro($db,$contacto_id);
$select_autos=muestra_autos($db);
$buffer=Genera_tabla($db,$contacto_id);
$boton='';
if(Total_vehiculos($db,$contacto_id)< 3)
    $boton='<input type="submit" name="agregar" value="Registrar" id="agregar">';

$contador=0;
if($agregar)
{
    if($unidad_id > 0)
    {
        $sql="SELECT contacto_id,unidad_id FROM mfll_contactos_registro_unidades
          WHERE contacto_id=".$contacto_id." AND unidad_id=".$unidad_id.";";
        $res=$db->sql_query($sql) or die("Error en la consulta  ".$sql);
        if($db->sql_numrows($res) == 0)
        {
            $ins="INSERT INTO  mfll_contactos_registro_unidades (contacto_id,unidad_id) VALUES (".$contacto_id.",".$unidad_id.");";
            if($db->sql_query($ins))
            {
                $sql_fases="SELECT contacto_id,fase_id FROM mfll_contactos_fases WHERE contacto_id=".$contacto_id.";";
                $res_fases=$db->sql_query($sql_fases);
                if($db->sql_numrows($res_fases)>0)
                    $sql_c="update mfll_contactos_fases SET fase_id=2 WHERE contacto_id=".$contacto_id.";";
                else
                    $sql_c="INSERT INTO mfll_contactos_fases (contacto_id,fase_id) values (".$contacto_id.",'2');";
                $db->sql_query($sql_c) or die("$sql<br>Error al insertar contacto".print_r($db->sql_error()));
                
                $buffer=Genera_tabla($db,$contacto_id);
                $boton='';
                if(Total_vehiculos($db,$contacto_id)< 3)
                    $boton='<input type="submit" name="agregar" value="Registrar" id="agregar" class="boton_l">';
            }

        }   
    }    
}
?>