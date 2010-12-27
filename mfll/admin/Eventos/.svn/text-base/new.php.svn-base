<?
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $nombre,$domicilio,$fecha_ini,$fecha_fin,$horario,$costo,$res_nombre,$res_paterno,$res_materno,$submit,$_theme,$_themedir,$gid,$gids,$uids;
include_once("Reportes/funciones.php");

$concesionarias_existentes=Genera_combo_Concesionarias_Multiple($db,$gid);
$vendedores_existentes=Genera_combo_Vendedores_Multiple($db,$gid);
$concesionarias_participantes=Genera_combo_Concesionarias_Participantes_Multiple($db,$gid);
$vendedores_participantes=Genera_combo_Vendedores_Participantes_Multiple($db,$gid);

if ($submit) //crear el usuario
{
    $nombre=strtoupper(trim(elimina_acentos($nombre)));
    $domicilio=strtoupper(trim(elimina_acentos($domicilio)));
    $responsable=strtoupper(trim(elimina_acentos($responsable)));
    $evento_id=0;
    if( ($nombre != '') && ($fecha_ini != '') && ($fecha_fin != '') && ($res_nombre != '') && ($res_paterno != '') )
    {
        $array_gids=explode(',',$gids);
        $array_uids=explode(',',$uids);
        $contador_eventos=0;
        foreach($array_gids as $valor)
        {
            if($valor > 0)
            {
                if($contador_eventos == 0)
                {
                    if(revisa_evento($db,$nombre) > 0)
                        $leyenda= "<br><font color='#800000'>El evento registrado ya esta registrado en el sistema, intenta otro nombre de evento</FONT>";
                    else
                    {
                        $fecha_actual=date('Y-m-d H:i:s');
                        $ins="INSERT INTO mfll_eventos (evento_nombre,evento_direccion,evento_fecha_inicial,evento_fecha_final,evento_horario,evento_costo,evento_nombre_responsable,evento_apellido_paterno_responsable,evento_apellido_materno_responsable,timestamp)
                        VALUES ('$nombre','$domicilio','$fecha_ini','$fecha_fin','$horario','$costo','$res_nombre','$res_paterno','$res_materno','$fecha_actual');";
                        if($db->sql_query($ins) or die("No se pudo agregar el evento".print_r($db->sql_error())))
                            $evento_id=$db->sql_nextid();
                    }
                }
                if(revisa_concesionarias($db,$evento_id,$valor)== 0)
                {
                    $ins_c="INSERT INTO mfll_eventos_concesionarias (evento_id,gid) values ('".$evento_id."','".$valor."');";
                    $db->sql_query($ins_c) or die("error en el insert into:  ".$ins_c);
                }
            }
            $contador_eventos++;
            foreach($array_uids as $valor_u)
            {
                $gid_user=Regresa_gid_vendedor($db,$valor_u);
                if(Revisa_Tabla_Vendedores($db,$evento_id,$gid_user,$valor_u) == 0)
                {
                    $ins="INSERT INTO mfll_eventos_vendedores (evento_id,gid,uid) VALUES ('".$evento_id."','".$gid_user."','".$valor_u."');";
                    $db->sql_query($ins) or die("Error al insertar los datos ".$ins);
                }
            }
        }
        #Ingresa_Evento_SPVW($nombre,$array_gids);
        $leyenda="<h2><font color='#666666'>Se Grabar&oacute;n los datos con exito</font></h2>";
    }
    else
    {
            $leyenda= "<br><font color='#800000'>Favor de llenar los campos <b>OBLIGATORIOS</b> marcados por <b>*</b>, intenta de nuevo</font>";
    }
}

function Revisa_Tabla_Vendedores($db,$evento_id,$gid_user,$valor_u)
{
    $reg=0;
    $sql_con="SELECT evento_id,gid FROM mfll_eventos_vendedores
    WHERE evento_id=".$evento_id." AND gid=".$gid_user." AND uid=".$valor_u.";";
    if ($db->sql_numrows($db->sql_query($sql_con)) > 0)
        $reg=1;
    return $reg;

}
function Regresa_gid_vendedor($db,$valor_u)
{
    $tmp='';
    $sql="SELECT gid FROM users WHERE uid=".$valor_u.";";
    $res=$db->sql_query($sql);
    if ($db->sql_numrows($res) > 0)
        $tmp=$db->sql_fetchfield(0,0,$res);
    return $tmp;
}
function revisa_concesionarias($db,$evento_id,$gid)
{
    $reg=0;
    $sql_con="SELECT evento_id,gid FROM mfll_eventos_concesionarias
    WHERE evento_id=".$evento_id." AND gid=".$gid.";";
    if ($db->sql_numrows($db->sql_query($sql_con)) > 0)
        $reg=1;
    return $reg;
}
function revisa_evento($db,$nombre)
{
    $reg=0;
    $sql="SELECT evento_nombre FROM mfll_eventos WHERE evento_nombre='".$nombre."';";
    if ($db->sql_numrows($db->sql_query($sql)) > 0)
    {
        $reg=1;
    }
    return $reg;
}
function elimina_acentos($cadena)
{
    $tofind = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
    $replac = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
    return(strtr($cadena,$tofind,$replac));
}
function Ingresa_Evento_SPVW($nombre,$array_gids)
{
    $_dbhost = 'www.prospeccionvw.com';
    $_dbuname = 'root';
    $_dbpass = 'mysql_pwd!';
    #$_dbname = 'crm_prospectos';
    $_dbpass = 'MuchosHuevos';
    #$_dbname = 'crm_prospectos_pruebas';
    $conn = mysql_connect($_dbhost,$_dbuname,$_dbpass) or die ("error");
    mysql_select_db($_dbname,$conn) or die("Error de Base de Datos");
    $sql="SELECT fuente_id,nombre FROM crm_fuentes WHERE nombre='".$nombre."';";
    $res=mysql_query($sql,$conn);
    if(mysql_num_rows($res) == 0)
    {
        $date=date("Y-m-d H:i:s");
        $sql_min="SELECT min(fuente_id) FROM crm_fuentes;";
        $res_min=mysql_query($sql_min);
        $min=mysql_result($res_min,0,0);
        $min= $min - 1;

        $ins="INSERT INTO crm_fuentes (fuente_id,nombre,timestamp,active) VALUES ('".$min."','".$nombre."','".$date."','1');";
        if(mysql_query($ins))
        {
            $ins="INSERT INTO crm_fuentes_arbol (padre_id,hijo_id) VALUES (1,'".$min."');";
        }
    }
    else
    {
        $min=mysql_result($res,0,0);
    }
    // inserta en groups_fuentes
    $array_gids_sistema=Regresa_Concesionarias_SPVW($conn);
    if(count($array_gids)>0)
    {
        foreach($array_gids_sistema as $clave_gid => $valor)
        {
            if(!in_array($clave_gid,$array_gids))
            {
                $sql="SELECT gid,fuente_id FROM crm_groups_fuentes WHERE gid='".$clave_gid."' AND fuente_id='".$min."';";
                $res=mysql_query($sql,$conn);
                if(mysql_num_rows($res) == 0)
                {
                    mysql_query("INSERT INTO crm_groups_fuentes (gid,fuente_id) VALUES ('".$clave_gid."','".$min."');");
                }

            }
        }
    }
}

function Regresa_Concesionarias_SPVW($conn)
{
    $array=array();
    $sql="SELECT gid,name FROM groups ORDER BY gid;";
    $res=mysql_query($sql,$conn);
    if(mysql_num_rows($res) > 0)
    {
        while(list($id,$nombre) = mysql_fetch_array($res))
        {
            $array[$id]=$nombre;
        }
    }
    return $array;

}
?>
