<form name="eventos.php" method="post" action="genera_db_gid.php">
<?php
include_once("config.php");
$db= mysql_connect($_dbhost,$_dbuname,$_dbpass);
$base_selection = mysql_select_db($_dbname,$db);
$submit=$_POST['submit'];
$xgid=$_POST['xgid'];
if($submit)
{
    $buf= Genera_BD($db,$xgid);
    $file="bd_excel_concesionaria-".$xgid."_".date("Y-m-d").".xls";
    $f1=fopen($file,"w+");
    fwrite($f1,$buf);
    fclose($f1);
    $buf.="<br><br><a href='".$file."'>Descargar Archivo</a><br><a href='genera_db_gid.php'>Otra Concesionaria</a></br>";


}
else
{
    $buf="<table width='30%' align='center' style='border:1px solid #cdcdcd'>
       <thead>
            <tr bgcolor='#cdcdcd'><th colspan='2' align='center'>Favor de seleccionar la concesionaria</th></tr>
        </thead>
        <tbody>
            <tr><td>Seleccione:</td><td>".Genera_Combo_Concesionarias($db,$xgid)."</td></tr>
        </tbody>
        <thead>
            <tr><th colspan='2' align='center'><br><input type='submit' name='submit' value='Generar Excel'></th></tr>
        </thead></table>";
}
echo $buf;



/********************* FUNCIONES AUXILIARES  *****************************/
function Genera_BD($db,$xgid)
{
    $nm_concesionaria=Regresa_Nombre_Concesionaria($db,$xgid);
    $array_concesionarias=Regresa_Concesionarias($db,$xgid);
    $arra_vendedores=Regresa_Vendedores($db,$xgid);
    $array_fuentes=Regresa_Fuentes($db);
    $total=0;
    $buffer.="<table width='100%'>
              <thead>
                <tr  bgcolor='#cdcdcd'>Base de datos correspondiente a los prospectos que pertenecen a la concesionaria ".$xgid."
                    <td colspanb='29' align='left'>
                <tr  bgcolor='#cdcdcd'>
                    <td>contacto_id</td><td>Concesionaria</td><td>Vendedor</td><td>Fuente</td><td>nombre</td><td>apellido_paterno</td><td>apellido_materno</td><td>tel_casa</td><td>tel_oficina</td><td>tel_movil</td><td>tel_otro</td><td>email</td><td>domicilio</td><td>colonia</td><td>cp</td><td>poblacion</td><td>ciudad</td><td>rfc</td><td>fecha_importado</td><td>tel_casa_2</td><td>tel_oficina_2</td><td>tel_movil_2</td><td>horario_preferido_casa</td><td>horario_preferido_oficina</td><td>                  horario_preferido_movil</td><td>horario_preferido_casa_2</td><td>horario_preferido_oficina_2</td><td>horario_preferido_movil_2</td><td>Modelo</td>
                </tr>
                </thead
                <tbody>";
    for($j=1; $j<=2; $j++)
    {
        $tabla=' crm_contactos ';
        $titulo=' Contactos NO Finalizados';
        if($j==2 )
        {
            $tabla=' crm_contactos_finalizados ';
            $titulo=' Contactos Finalizados';
        }
        $sql="SELECT contacto_id,uid,gid,origen_id,nombre,apellido_paterno,apellido_materno,tel_casa,tel_oficina,
                     tel_movil,tel_otro,email,domicilio,colonia,cp,poblacion,ciudad,rfc,fecha_importado,
                     tel_casa_2,tel_oficina_2,tel_movil_2,horario_preferido_casa,horario_preferido_oficina,
                     horario_preferido_movil,horario_preferido_casa_2,horario_preferido_oficina_2,
                     horario_preferido_movil_2
              FROM ".$tabla." WHERE gid='".$xgid."' ORDER BY contacto_id;";
        $res=mysql_query($sql,$db);
        $num=mysql_num_rows($res);
        $cam=mysql_num_fields($res);
        $buffer.="<thead><tr>
                  <th colspan='".$cam."' align='justify'>".$titulo."       No. de prospectos:  ".$num."</th>
                  </tr></thead>";
        if( $num > 0)
        {

            while(list($contacto_id,$uid,$gid,$origen_id,$nombre,$apellido_paterno,$apellido_materno,$tel_casa,$tel_oficina,$tel_movil,$tel_otro,$email,$domicilio,$colonia,$cp,$poblacion,$ciudad,$rfc,$fecha_importado,$tel_casa_2,$tel_oficina_2,$tel_movil_2,$horario_preferido_casa,$horario_preferido_oficina,$horario_preferido_movil,$horario_preferido_casa_2,$horario_preferido_oficina_2,$horario_preferido_movil_2) = mysql_fetch_array($res))
            {
                $buffer.="<tr>
                            <td>$contacto_id</td><td>$nm_concesionaria</td><td>$arra_vendedores[$uid]</td><td>$array_fuentes[$origen_id]</td><td>$nombre</td>
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
    $buffer.="<thead><tr><th colspan='".$cam."' align='justify'>Total de Registros:  ".$total."</th></tr></thead></table>";
    return $buffer;
}

function Genera_Combo_Concesionarias($db,$gid)
{
    $select='';
    $sql="select gid,name FROM groups where gid > 0 ORDER BY gid;";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        $select="<select name='xgid' style='border:1px solid #cdcdcdc; color:#000000';>
                    <option value='0'>Seleccione</option>";
        while($fila = mysql_fetch_assoc($res))
        {
            $tmp='';
            if($fila['gid'] == $fuente_id)
                $tmp=' SELECTED ';
            $select.="<option value='".$fila['gid']."' ".$tmp.">".$fila['gid']."  -  ".$fila['name']."</option>";
        }
        $select.="</select>";
    }
    return $select;
}
function Regresa_modelo($db,$contacto_id)
{
    $nm_modelo='';
    $sql="SELECT modelo FROM crm_prospectos_unidades WHERE contacto_id=".$contacto_id.";";

    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        while(list($modelo) = mysql_fetch_array($res))
        {
            $nm_modelo.=$modelo."<br>";
        }
    }
    return $nm_modelo;
}

function Regresa_Nombre_Concesionaria($db,$gid)
{
    $nm_con='';
    $sql="SELECT name FROM groups WHERE gid=".$gid.";";
    $res = mysql_query($sql,$db) or die ("Error en la conexion ".$sql);
    if(mysql_num_rows($res) > 0)
    {
        while(list($nombre) = mysql_fetch_array($res))
        {
            $nm_con.=$nombre."<br>";
        }
    }
    return $nm_con;
}

function Regresa_Concesionarias($db,$gid)
{
    $array=array();
    $sql="SELECT b.gid,b.name FROM groups b WHERE b.gid='".$gid."' ORDER BY b.gid;";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        while(list($gid,$name) = mysql_fetch_array($res))
        {
            $array[$gid]=$name;
        }
    }
    return $array;
}
function Regresa_Vendedores($db,$gid)
{
    $array=array();
    $sql="SELECT b.uid,b.name FROM users b WHERE b.gid='".$gid."' ORDER BY b.gid,b.uid;";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        while(list($uid,$name) = mysql_fetch_array($res))
        {
            $array[$uid]=$name;
        }
    }
    return $array;
}
function Regresa_Fuentes($db)
{
    $array=array();
    $sql="SELECT fuente_id,nombre FROM crm_fuentes WHERE fuente_id!= 0 ORDER BY fuente_id;";
    $res=mysql_query($sql,$db);
    if(mysql_num_rows($res) > 0)
    {
        while(list($id,$name) = mysql_fetch_array($res))
        {
            $array[$id]=$name;
        }
    }
    return $array;
    
}
?></form>