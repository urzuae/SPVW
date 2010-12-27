<?php
include_once("config.php");
$opc=$_GET['opc'];
if($opc == 2)
    $tabla="_finalizados";
$entidad_id=21;
$cadena_gids='';
$contador_total=0;
$contador_ccn=0;

$db= mysql_connect($_dbhost,$_dbuname,$_dbpass);
$base_selection = mysql_select_db($_dbname,$db);

// saco las concesionarias que se ubican en la entidad 21 puebla
$array_concesionarias=Regresa_Concesionarias($db,$entidad_id);
if(count($array_concesionarias) > 0)
    $cadena_gids=implode(',',$array_concesionarias);

$array_nombre_concesionarias=Regresa_Nombre_Concesionarias($db,$cadena_gids);
$array_nombre_vendedores=Regresa_Nombre_Vendedores($db,$cadena_gids);
$array_nombre_origenes=Regresa_Nombre_Fuentes($db);

//saco a los prospectos que esten en esas concesionarias con todo y modelo
$sql="SELECT a.contacto_id,a.gid,a.uid,a.origen_id,a.nombre,a.apellido_paterno,a.apellido_materno,a.domicilio,a.colonia,a.cp,a.poblacion,a.ciudad,a.fecha_importado,a.tel_casa,a.tel_oficina,a.tel_movil,a.tel_otro,a.email,a.tel_casa_2,a.tel_oficina_2,a.tel_movil_2,a.horario_preferido_casa,a.horario_preferido_oficina,a.horario_preferido_movil,a.horario_preferido_casa_2,a.horario_preferido_oficina_2,a.horario_preferido_movil_2
      FROM crm_contactos".$tabla." AS a WHERE a.gid IN (".$cadena_gids.") ORDER BY a.gid,a.nombre,a.apellido_paterno,a.apellido_materno;";
$res=mysql_query($sql,$db);
$num=mysql_num_rows($res);
if($num > 0)
{
    $file="prospectos_puebla".$tabla.".csv";
    $f1=fopen($file,"w+");
    $cabecera='Id,Concesionaria,Vendedor,Origen o Fuente,Nombre,Apellido Paterno,Apellido_Materno,Domicilio,Colonia, Cp, Poblacion,Ciudad,Fecha Importado,Tel Casa, Tel Oficina, Tel Movil, Tel otro,Email, Tel Casa 2,Tel Oficina 2,Tel Movil 2, Horario Casa, Horario Oficina, Horario Movil, Horario Casa 2, Horario Oficina 2, Horario Movil 2,Modelo';
    fwrite($f1,$cabecera."\n");
    while(list($contacto_id,$gid,$uid,$origen_id,$nombre,$apellido_paterno,$apellido_materno,$domicilio,$colonia,$cp,$poblacion,$ciudad,$fecha_importado,$tel_casa,$tel_oficina,$tel_movil,$tel_otro,$email,$tel_casa_2,$tel_oficina_2,$tel_movil_2,$horario_preferido_casa,$horario_preferido_oficina,$horario_preferido_movil,$horario_preferido_casa_2,$horario_preferido_oficina_2,$horario_preferido_movil_2) = mysql_fetch_array($res))
    {        
        if(Checa_En_Concesionrias($db,$nombre,$apellido_paterno,$apellido_materno)==1)
        {
            echo"<br>contacto:_  ".$contacto_id." ---  ".$nombre." ".$apellido_paterno." ".$apellido_materno;
            $modelo=Regresa_Modelos($db,$contacto_id);
            $cadena=$contacto_id.','.$array_nombre_concesionarias[$gid].','.$array_nombre_vendedores[$uid].','.$array_nombre_origenes[$origen_id].','.$nombre.','.$apellido_paterno.','.$apellido_materno.','.$domicilio.','.$colonia.','.$cp.','.$poblacion.','.$ciudad.','.$fecha_importado.','.$tel_casa.','.$tel_oficina.','.$tel_movil.','.$tel_otro.','.$email.','.$tel_casa_2.','.$tel_oficina_2.','.$tel_movil_2.','.$horario_preferido_casa.','.$horario_preferido_oficina.','.$horario_preferido_movil.','.$horario_preferido_casa_2.','.$horario_preferido_oficina_2.','.$horario_preferido_movil_2.','.$modelo;
            fwrite($f1,$cadena."\n");
            $contador_ccn++;
        }
        $contador_total++;
    }
    fwrite($f1,"total de prospectos que llegan por CCN:  .".$contador_ccn."\n");
    fclose($f1);
    echo"<br>Total de prospectos en tabla de crm-contactos:   ".$contador_total."\n";
    echo"<br>Total de prospectos llegados desde call center:  ".$contador_ccn."\n";
    echo"<br><a href='".$file."'>Ver archivo</a>";
}

function Regresa_Modelos($db,$contacto_id)
{
    $cad_modelo='';
    $sql="SELECT modelo FROM crm_prospectos_unidades WHERE contacto_id=".$contacto_id.";";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res)>0)
    {
        while(list($modelo)= mysql_fetch_array($res))
        {
            $array[]=$modelo;
        }
        $cad_modelo=implode(',',$array);
    }
    return $cad_modelo;
}

function Checa_En_Concesionrias($db,$nombre,$apellido_paterno,$apellido_materno)
{
    $opc=0;
    $sql="SELECT * FROM crm_historial_contactos WHERE nombre='".$nombre."' AND primer_apellido='".$apellido_paterno."' AND segundo_apellido='".$apellido_materno."';";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res)>0)
    {
        $opc=1;
    }
    return $opc;
}

function Regresa_Concesionarias($db,$entidad_id)
{
    $array=array();
    $sql="SELECT DISTINCT gid FROM groups_municipios WHERE entidad_id = ".$entidad_id." ORDER BY gid;";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        while(list($gid)=mysql_fetch_array($res))
        {
          $array[]=$gid;  
        }
    }
    return $array;
}
function Regresa_Nombre_Concesionarias($db,$cadena_gids)
{
    $array=array();
    $sql="SELECT gid,name FROM groups WHERE gid IN (".$cadena_gids.") ORDER BY gid;";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        while(list($gid,$name)=mysql_fetch_array($res))
        {
          $array[$gid]=$name;
        }
    }
    return $array;
}
function Regresa_Nombre_Vendedores($db,$cadena_gids)
{
    $array=array();
    $sql="SELECT uid,name FROM users WHERE gid IN (".$cadena_gids.") ORDER BY gid,uid;";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        while(list($uid,$name)=mysql_fetch_array($res))
        {
          $array[$uid]=$name;
        }
    }
    return $array;
}
function Regresa_Nombre_Fuentes($db)
{
    $array=array();
    $sql="SELECT fuente_id,nombre FROM crm_fuentes ORDER BY fuente_id;";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        while(list($fuente_id,$nombre)=mysql_fetch_array($res))
        {
          $array[$fuente_id]=$nombre;
        }
    }
    return $array;
}
?>