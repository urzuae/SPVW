<?php
if (!defined('_IN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db,$user, $uid,$fuente_id,$submit,$xgid,$fecha_ini,$fecha_fin;
$orden=1;
$sql  = "SELECT gid, super FROM users WHERE uid='$uid'";
$result = $db->sql_query($sql) or die("Error");
list($gid, $super) = $db->sql_fetchrow($result);
$gid = sprintf("%04d", $gid);
if($submit)
{
    if ($super > 6)
    {
        $_html = "<h1>Usted no es un Gerente</h1>";
    }
    else
    {
        $array_buf= Genera_BD($db,$fuente_id,$gid,$fecha_ini,$fecha_fin,$orden);
        $buf_excel=$array_buf[0];
        $buffer=$array_buf[1];
        $file="admin/archivos/bd_excel_".$gid."_".date("Y-m-d").".xls";
        $f1=fopen($file,"w+");
        fwrite($f1,$buf_excel);
        fclose($f1);
        $buffer.="<br><a href='".$file."'>Descargar Archivo</a></br>";
    }
}
$buf="<table width='100%' align='center' style='border:1px solid #cdcdcd'>
        <tbody>
            <tr><td>Fuente:</td><td colspan='4'>".Genera_Combo_Fuentes($db,$gid,$fuente_id)."</td></tr>
            <tr>
                <td>Fecha Inicial:</td>
                <td>
                    <input name=\"fecha_ini\" id=\"fecha_ini\" value=\"$fecha_ini\"><img src=\"img/calendar.gif\" id=\"f_trigger_c\" style=\"border: 1px solid white; cursor: pointer;\" title=\"Fecha\" onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\">
                    <script>
                        Calendar.setup(
                        {
                            inputField :'fecha_ini',
                            ifFormat :'%Y-%m-%d',
                            button : 'f_trigger_c'
                        });
                     </script>
                </td>
                <td>Fecha Final:</td>
                <td>
                        <input name=\"fecha_fin\" id=\"fecha_fin\" value=\"$fecha_fin\"><img src=\"img/calendar.gif\" id=\"f_trigger_d\" style=\"border: 1px solid white; cursor: pointer;\" title=\"Fecha\" onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\">
                        <script>
			Calendar.setup(
			{
			  inputField :'fecha_fin',
			  ifFormat :'%Y-%m-%d',
			  button : 'f_trigger_d'
			}
			);
			</script>
                </td>
            </tr>
            <tr><td colspan='4' align='center'><br><input type='submit' name='submit' value='Generar Excel'></td></tr>
            <tr><td colspan='4' align='center'><br>".$buffer."</td></tr>
        </tbody></table>";



/********************* FUNCIONES AUXILIARES  *****************************/
function Genera_BD($db,$fuente_id,$xgid,$fecha_ini,$fecha_fin,$orden)
{
    $order = " nombre,apellido_paterno,apellido_materno ";
    if($orden == 2) $order = " gid,nombre,apellido_paterno,apellido_materno ";
    $filtro='';
    if($fuente_id != 0)  $filtro.=" AND origen_id='".$fuente_id."' ";
    if($xgid > 0)        $filtro.=" AND gid='".$xgid."' ";
    if( ($fecha_ini) && ($fecha_fin))
    {
        $filtro.=" AND fecha_importado BETWEEN '".$fecha_ini." 00:01:01' AND '".$fecha_fin." 23:59:59' ";
    }
    if( ($fecha_ini) && (!$fecha_fin))
    {
        $filtro.=" AND fecha_importado BETWEEN '".$fecha_ini." 00:01:01' AND '".$fecha_ini." 23:59:59' ";
    }
    if( (!$fecha_ini) && ($fecha_fin))
    {
        $filtro.=" AND fecha_importado BETWEEN '".$fecha_fin." 00:01:01' AND '".$fecha_fin." 23:59:59' ";
    }

    $titulo=Regresa_Nombre_Fuente($db,$fuente_id);
    $titulo.=Regresa_Nombre_Concesionaria($db,$xgid);
    $array_concesionarias=Regresa_Concesionarias($db,$fuente_id,$xgid);
    $arra_vendedores=Regresa_Vendedores($db,$fuente_id,$xgid);
    $total=0;
    $num_total=0;
    $buffer.="<table width='100%'>
              <thead>
                <tr bgcolor='#cdcdcd'>Base de datos correspondiente a los prospectos que pertenecen a la fuente ".$titulo."
                <tr bgcolor='#cdcdcd'>
                    <td>contacto_id</td><td>Concesionaria</td><td>Vendedor</td><td>Fuente</td><td>nombre</td><td>apellido_paterno</td><td>apellido_materno</td><td>tel_casa</td><td>tel_oficina</td><td>tel_movil</td><td>tel_otro</td><td>email</td><td>domicilio</td><td>colonia</td><td>cp</td><td>poblacion</td><td>ciudad</td><td>rfc</td><td>fecha_importado</td><td>tel_casa_2</td><td>tel_oficina_2</td><td>tel_movil_2</td><td>horario_preferido_casa</td><td>horario_preferido_oficina</td><td>                  horario_preferido_movil</td><td>horario_preferido_casa_2</td><td>horario_preferido_oficina_2</td><td>horario_preferido_movil_2</td><td>Modelo</td>
                </tr>
                </thead
                <tbody>";
    $salida="<table width='100%'>
              <thead>
                <tr><th>Base de datos correspondiente a los prospectos que pertenecen a la fuente ".$nm_fuente."</th></tr>";
    for($j=1; $j<=2; $j++)
    {
        $tabla=' crm_contactos ';
        $titulo=' Contactos NO Finalizados';
        if($j==2 )
        {
            $tabla=' crm_contactos_finalizados ';
            $titulo=' Contactos Finalizados';
        }
        $sql="SELECT DISTINCT contacto_id,uid,gid,origen_id,nombre,apellido_paterno,apellido_materno,tel_casa,tel_oficina,
                     tel_movil,tel_otro,email,domicilio,colonia,cp,poblacion,ciudad,rfc,fecha_importado,
                     tel_casa_2,tel_oficina_2,tel_movil_2,horario_preferido_casa,horario_preferido_oficina,
                     horario_preferido_movil,horario_preferido_casa_2,horario_preferido_oficina_2,
                     horario_preferido_movil_2
              FROM ".$tabla." WHERE 1 ".$filtro." ORDER BY ".$order.";";
        $res=$db->sql_query($sql) or die ("Error en la consulta:  ".$sql);
        $num=$db->sql_numrows($res);
        $num_total=$num_total + $num;
        $cam=$db->sql_numfields($res);
        $buffer.="<thead><tr>
                  <th colspan='".$cam."' align='justify'>".$titulo."       No. de prospectos:  ".$num."</th>
                  </tr></thead>";

        $salida.="
                  <tbody><tr>
                  <td align='justify'>".$titulo."       No. de prospectos:  ".$num."</th>
                  </tr></tbody>";
        if( $num > 0)
        {

            while(list($contacto_id,$uid,$gid,$origen_id,$nombre,$apellido_paterno,$apellido_materno,$tel_casa,$tel_oficina,$tel_movil,$tel_otro,$email,$domicilio,$colonia,$cp,$poblacion,$ciudad,$rfc,$fecha_importado,$tel_casa_2,$tel_oficina_2,$tel_movil_2,$horario_preferido_casa,$horario_preferido_oficina,$horario_preferido_movil,$horario_preferido_casa_2,$horario_preferido_oficina_2,$horario_preferido_movil_2) = $db->sql_fetchrow($res))
            {
                $buffer.="<tr>
                            <td>$contacto_id</td><td>$array_concesionarias[$gid]</td><td>$arra_vendedores[$uid]</td><td>$nm_fuente</td><td>$nombre</td>
                            <td>$apellido_paterno</td><td>$apellido_materno</td><td>$tel_casa</td>
                            <td>$tel_oficina</td><td>$tel_movil</td><td>$tel_otro</td><td>$email</td>
                            <td>$domicilio</td><td>$colonia</td><td>$cp</td><td>$poblacion</td><td>$ciudad</td>
                            <td>$rfc</td><td>$fecha_importado</td><td>$tel_casa_2</td><td>$tel_oficina_2</td>
                            <td>$tel_movil_2</td><td>$horario_preferido_casa</td><td>$horario_preferido_oficina</td>
                            <td>$horario_preferido_movil</td><td>$horario_preferido_casa_2</td>
                            <td>$horario_preferido_oficina_2</td><td>$horario_preferido_movil_2</td>
                            <td>".Regresa_modelo($db,$contacto_id)."</td>
                          </tr>";
                $total++;
            }
            $buffer.="</tbody><thead><tr><th colspan='".$cam."' align='justify'>Total de Registros ".$titulo.":  ".$num."</th></tr></thead>";
        }
    }
    $salida.="
        <thead><tr>
        <th align='justify'>No. de prospectos:  ".$num_total."</th>
        </tr></thead></table>";

    $buffer.="<thead><tr><th colspan='".$cam."' align='justify'>Total de Registros:  ".$total."</th></tr></thead></table>";
    $array[0]=$buffer;
    $array[1]=$salida;
    return $array;
}

function Genera_Combo_Fuentes($db,$gid,$fuente_id)
{
    $select='';
    $filtro_gid='';
    // consulta las fuentes disponibles para esta concesionaria
    $res_no_visibles=$db->sql_query("SELECT fuente_id FROM crm_groups_fuentes WHERE gid='".$gid."';");
    if($db->sql_numrows($res_no_visibles) > 0)
    {
        while(list($id_fuente)=$db->sql_fetchrow($res_no_visibles))
        {
            $array[]=$id_fuente;
        }
        $lista_gid_no_visibles=implode(',',$array);
    }    
    if($lista_gid_no_visibles!='')
        $filtro_gid=" AND fuente_id NOT IN (".$lista_gid_no_visibles.") ";


    $sql="select fuente_id,nombre FROM crm_fuentes where fuente_id<0 ".$filtro_gid." ORDER BY nombre;";
    $res=$db->sql_query($sql) or die("Error en la consulta  ".$sql);    
    if($db->sql_numrows($res) > 0)
    {
        $select="<select name='fuente_id' style='border:1px solid #cdcdcd; color:#000000; width:250px;'>
                    <option value='0'>Seleccione</option>";
        while(list($id,$name) = $db->sql_fetchrow($res))
        {
            $tmp='';
            if($id == $fuente_id)
                $tmp=' SELECTED ';
            $select.="<option value='".$id."' ".$tmp.">".strtoupper(strtolower($name))."</option>";
        }
        $select.="</select>";
    }
    return $select;
}

function Genera_Combo_Concesionarias($db,$gid)
{
    $select='';
    $sql="select gid,name FROM groups where gid > 0 ORDER BY gid;";
    $res=$db->sql_query($sql) or die("Error en la consulta  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        $select="<select name='xgid' style='border:1px solid #cdcdcd; color:#000000; width:550px;'>
                    <option value='0'></option>";
        while(list($id,$name) = $db->sql_fetchrow($res))
        {
            $tmp='';
            if($id == $gid)
                $tmp=' SELECTED ';
            $select.="<option value='".$id."' ".$tmp.">".$id."   -   ".strtoupper(strtolower($name))."</option>";
        }
        $select.="</select>";
    }
    return $select;
}

function Regresa_modelo($db,$contacto_id)
{
    $nm_modelo='';
    $sql="SELECT modelo FROM crm_prospectos_unidades WHERE contacto_id=".$contacto_id.";";
    $res=$db->sql_query($sql) or die("Error en la consulta  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($modelo) = $db->sql_fetchrow($res))
        {
            $nm_modelo.=$modelo."<br>";
        }
    }
    return $nm_modelo;
}

function Regresa_Nombre_Fuente($db,$fuente_id)
{
    $nm_fuente='';
    $sql="SELECT nombre FROM crm_fuentes WHERE fuente_id=".$fuente_id.";";
    $res=$db->sql_query($sql) or die("Error en la consulta  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($nombre) = $db->sql_fetchrow($res))
        {
            $nm_fuente.=$nombre."<br>";
        }
    }
    return $nm_fuente;
}
function Regresa_Nombre_Concesionaria($db,$gid)
{
    $nm_con='';
    $sql="SELECT name FROM groups WHERE gid=".$gid.";";
    $res=$db->sql_query($sql) or die("Error en la consulta  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($nombre) = $db->sql_fetchrow($res))
        {
            $nm_con.=$nombre."<br>";
        }
    }
    return $nm_con;
}

function Regresa_Concesionarias($db,$fuente_id,$xgid)
{
    $filtro='';
    if($fuente_id != 0)  $filtro.=" AND a.origen_id='".$fuente_id."' ";
    if($xgid > 0)        $filtro.=" AND a.gid='".$xgid."' ";
    $array=array();
    $sql="SELECT a.gid,b.name FROM crm_contactos as a LEFT JOIN groups b ON a.gid=b.gid WHERE 1 ".$filtro." ORDER BY a.gid;";
    $res=$db->sql_query($sql) or die("Error en la consulta  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($gid,$name) =  $db->sql_fetchrow($res))
        {
            $array[$gid]=$name;
        }
    }
    return $array;
}
function Regresa_Vendedores($db,$fuente_id,$xgid)
{
    $filtro='';
    if($fuente_id != 0)  $filtro.=" AND a.origen_id='".$fuente_id."' ";
    if($xgid > 0)        $filtro.=" AND a.gid='".$xgid."' ";

    $array=array();
    $sql="SELECT a.uid,b.name FROM crm_contactos as a LEFT JOIN users b ON a.uid=b.uid WHERE 1 ".$filtro." ORDER BY a.gid,a.uid;";
    $res=$db->sql_query($sql) or die("Error en la consulta  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($uid,$name) =  $db->sql_fetchrow($res))
        {
            $array[$uid]=$name;
        }
    }
    return $array;
}
?>