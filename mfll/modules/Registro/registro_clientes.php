<?
  if (!defined('_IN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global  $db, $guardar, $last_module, $last_op, $close_after, $uid,$_theme,$_themedirjs,$contacto_id,$nombre,
        $apellido_paterno, $apellido_materno,$tel_casa, $tel_movil, $tel_otro,$lada1,$lada3,$lada4,
        $email,$medio, $tmp_gid,$_css,$_themedir,$_theme,$concesionaria,$radiobutton,$select_concesionaria;
        $_theme = "sinmenu";
        $_themedir = "themes/$_theme";

include_once("funciones.php");
$nombre_prospecto=registro($db,$contacto_id);
$oculto="<input type='hidden' id='contacto_id' name='contacto_id' value='".$contacto_id."'>";
// pintamos catalogos de concesionarias y medios
$ultimo_evento=Regresa_Ultimo_Evento($db);
$select_medio =medio_entero($db,$medio);
$select_concesionaria=regresa_concesionaria_eventos($db,$ultimo_evento);
$mensaje="";
if ($guardar)
{
    $correcto=true;
    if( ($lada1 == '') && ($lada3 == '') && ($lada4 == '') ){
        $mensaje="Favor de teclear al menos una lada";
        $correcto=false;
    }

    if( ($tel_casa == '') && ($tel_movil == '') && ($tel_otro == '') && ($email == '') ){
        $mensaje="Favor de teclear al menos un telefono o correo electr&oacute;nico";
        $correcto=false;
    }

    if ($medio == 0){
        $mensaje="Favor de seleccionar un medio de contacto";
        $correcto=false;
    }

    if ($concesionaria == 0){
        $mensaje="Favor de seleccionar una concesionaria";
        $correcto=false;
    }

    if ($radiobutton == ''){
        $mensaje="Favor de seleccionar un vendedor";
        $correcto=false;
    }
    if($correcto)
    {
        if ($lada1) $tel_casa = "($lada1) $tel_casa";
        if ($lada3) $tel_movil = "($lada3) $tel_movil";
        if ($lada4) $tel_otro = "($lada4) $tel_otro";
        $fecha_hoy=date('Y-m-d H:i:s');
        $sql="update  mfll_contactos_registro SET
              tel_casa='$tel_casa', tel_movil='$tel_movil',
              tel_otro='$tel_otro',email='$email',medio_id='$medio',gid='$concesionaria',uid='$radiobutton',fecha_registro='$fecha_hoy',evento_id='$ultimo_evento'
              where contacto_id=$contacto_id;";
        $db->sql_query($sql) or die("$sql<br>Error al insertar contacto".print_r($db->sql_error()));

        $sql_fases="SELECT contacto_id,fase_id FROM mfll_contactos_fases WHERE contacto_id=".$contacto_id.";";
        $res_fases=$db->sql_query($sql_fases);
        if($db->sql_numrows($res_fases)>0)
            $sql_c="update mfll_contactos_fases SET fase_id=1 WHERE contacto_id=".$contacto_id.";";
        else
            $sql_c="INSERT INTO mfll_contactos_fases (contacto_id,fase_id) values (".$contacto_id.",'1');";
        $db->sql_query($sql_c) or die("$sql<br>Error al insertar contacto".print_r($db->sql_error()));

        header("Location: index.php?_module=Bienvenida&_op=mfll");
    }
}
?>