<?php
if (!defined('_IN_ADMIN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db,$opc,$padre_id,$fuente,$fecha_ini,$fecha_fin,$zona_id,$id_gid,$nm_gid,$gids;
$regreso='';
switch($opc)
{
    case 1:
        $regreso=Inserta_Fuente($db,$padre_id,$fuente,$fecha_ini,$fecha_fin);
        break;
    case 2:
        $regreso=Regresa_Concesionarias($db,$zona_id);
        break;
    case 3:
        $regreso=Buscar_Concesionaria($db,$id_gid,$nm_gid);
        break;
    case 4:
        $regreso=Activa_Concesionarias($db,$gids,$fuente);
        break;
}
echo $regreso;
die();

/******************* FUNCTIONES AUXILIARES *******************/

function Regresa_Id_Fuente($db,$fuente)
{
    $id=0;
    $sql="SELECT fuente_id FROM crm_fuentes WHERE nombre='".$fuente."';";
    $res=$db->sql_query($sql) or die ($sql);
    if($db->sql_numrows($res)>0)
        list($id)= $db->sql_fetchrow($res);
    return $id;    
}
function Activa_Concesionarias($db,$gids,$fuente)
{
    $regreso="No se activo la concesionaria";
    $array_gids=array();
    if($gids != '')
    {
        $id_fuente=Regresa_Id_Fuente($db,$fuente);
        if($id_fuente != 0)
        {
            $db->sql_query("DELETE FROM crm_groups_fuentes WHERE fuente_id='".$id_fuente."';");
            Bloquea_Fuente_en_Concesinarias($db,$id_fuente);
            $gids=substr($gids,0,(strlen($gids) - 1 ));
            $array_gids=explode('|',$gids);
            foreach($array_gids as $gid)
            {
                $db->sql_query("DELETE FROM crm_groups_fuentes WHERE gid='".$gid."' and fuente_id='".$id_fuente."';");
            }
            $regreso="Se ha activado la fuente: ".$fuente." a la concesionarias seleccionadas";
        }
    }
    return $regreso;
}

function Buscar_Concesionaria($db,$id_gid,$nm_gid)
{
    $minimo=Regresa_Minimo($db);
    $fltro='';
    if($id_gid > 0)        $filtro.=" AND gid=".$id_gid." ";
    if($nm_gid != '')        $filtro.=" AND name like '".$nm_gid."%' ";
    // RECUPERAMOS EL ID
    $buffer="No se tiene registrada una concesionaria con este parametro de busquedad";
    $sql="SELECT gid,name FROM groups WHERE 1 ".$filtro.";";
    $res=$db->sql_query($sql) or die ($sql);
    $num=$db->sql_numrows($res);
    if( $num > 0)
    {
        $buffer="
        <table width='100%' align='center' class='tablesorter'>
        <thead><tr><th width='15%'>Estatus</th>
                    <th width='10%'>Gid</th>
                    <th width='60%'>Concesionaria</th>
        </tr></thead>";
        while(list($_xgid,$name) = $db->sql_fetchrow($res))
        {
            $bloqueado = Regresa_Concesionaria_Bloqueadas($db,$_xgid,$minimo);
            $tit="<font color='#DF0101'>Bloqueada</font>";
            $tmp="";
            if($bloqueado==0)
            {
                $tit="<font color='#04B45F'>Activa</font>";
                $tmp=" checked ";
            }
            $buffer.="
            <tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;\">
            <td><input type='checkbox' name='activa' id='activa' ".$tmp." value='".$_xgid."'>&nbsp;".$tit."</td>
            <td>".$_xgid."</td>
            <td>".htmlentities($name)."</td>
             </tr>";
            $i++;
        }
        $buffer.="</tbody><thead><tr><td colspan='3'>Total de concesionarias: ".$num."</td></tr></thead></table>";
    }
    return $buffer;
}

function Regresa_Concesionaria_Bloqueadas($db,$_gid,$minimo)
{
    $reg=0;
    $sql="SELECT b.gid,b.fuente_id FROM crm_groups_fuentes as b
          WHERE b.gid=".$_gid." AND b.fuente_id=".$minimo." ORDER BY b.gid,b.fuente_id;";
    $res=$db->sql_query($sql) or die ($sql);
    if($db->sql_numrows($res) > 0)
    {
        $reg=1;
    }
    return $reg;
}

function Regresa_Fuentes_Bloqueadas($db,$zona_id,$minimo)
{
    $array=array();
    # saco las fuentes donde no tiene acceso la concesionaria
    $sql="SELECT a.zona_id,a.gid,b.fuente_id FROM groups_zonas as a
          LEFT JOIN crm_groups_fuentes as b ON a.gid=b.gid
          WHERE a.zona_id=".$zona_id." AND b.fuente_id=".$minimo." ORDER BY a.zona_id,a.gid,b.fuente_id;";
    $res=$db->sql_query($sql) or die ($sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($zona_id,$gid,$fuente_id)= $db->sql_fetchrow($res))
        {
            $array[]=$gid;
        }
    }
    return $array;
}

function Regresa_Concesionarias($db,$zona_id)
{    
    $minimo=Regresa_Minimo($db);
    $array_fuentes_desactivadas=Regresa_Fuentes_Bloqueadas($db,$zona_id,$minimo);

    $buffer='';
    $sql="SELECT a.zona_id,a.gid,b.nombre,c.name FROM groups_zonas as a
          LEFT JOIN crm_zonas as b ON a.zona_id=b.zona_id
          LEFT JOIN groups as c ON a.gid=c.gid WHERE a.zona_id=".$zona_id." 
          AND c.name IS NOT NULL ORDER BY a.zona_id,a.gid;";
    $res=$db->sql_query($sql) or die ($sql);
    $num=$db->sql_numrows($res);
    if( $num > 0)
    {
        $buffer.="
        <table width='100%' align='center' class='tablesorter'>
        <thead><tr><th width='15%'>Estatus</th>
                    <th width='15%'>Zona</th>
                    <th width='10%'>Gid</th>
                    <th width='60%'>Concesionaria</th>
        </tr></thead>";
        $i=0;
        while(list($zona_id,$gid,$zona,$name)= $db->sql_fetchrow($res))
        {
            $tit="<font color='#DF0101'>Bloqueada</font>";
            $tmp="";
            if(!in_array($gid,$array_fuentes_desactivadas))
            {
                $tit="<font color='#04B45F'>Activa</font>";
                $tmp=" checked ";
            }
            $buffer.="
            <tr class=\"row".($class_row++%2?"2":"1")."\" style=\"cursor:pointer;\">
            <td><input type='checkbox' name='activa' id='activa' ".$tmp." value='".$gid."'>&nbsp;".$tit."</td>
            <td>".$zona_id." - ".$zona."</td>
            <td>".$gid."</td>
            <td>".htmlentities($name)."</td>
             </tr>";
            $i++;
        }
        $buffer.="</tbody><thead><tr><td colspan='4'>Total de concesionarias: ".$num."</td></tr></thead></table>";
    }
    return $buffer;

}
function Regresa_Minimo($db)
{
    $sql="SELECT min(fuente_id) FROM crm_fuentes;";
    $res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
    list($minimo) = $db->sql_fetchrow($res);
    $minimo=$minimo+ 0;
    return $minimo;
}

function Inserta_Fuente($db,$padre_id,$fuente,$fecha_ini,$fecha_fin)
{
    $reg="No guardado";
    #Buscamos que la fuente no este registrada en la tabla
    $sql="SELECT nombre FROM crm_fuentes WHERE nombre='".$fuente."';";
    $res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
    if($db->sql_numrows($res) == 0)
    {
         #saco el id de fuente
        $minimo=Regresa_Minimo($db);
        $minimo=$minimo-1;
        #manipulo las fechas,
        $fecha_inicio=substr($fecha_ini,6,4).'-'.substr($fecha_ini,3,2).'-'.substr($fecha_ini,0,2).' '.substr($fecha_ini,11,8);
        $fecha_final =substr($fecha_fin,6,4).'-'.substr($fecha_fin,3,2).'-'.substr($fecha_fin,0,2).' '.substr($fecha_fin,11,8);
        $fuente=ucwords($fuente);
        #inserto en la tabla
        $ins="INSERT INTO crm_fuentes (fuente_id,nombre,timestamp,active,fecha_inicial,fecha_final) VALUES ('".$minimo."','".$fuente."','".date("Y-m-d H:i:s")."','1','".$fecha_inicio."','".$fecha_final."');";
        if($db->sql_query($ins) or die ("Error en el Inser:  ".$ins))
        {
            $ins="INSERT INTO crm_fuentes_arbol (padre_id,hijo_id) VALUES ('".$padre_id."','".$minimo."');";
            $db->sql_query($ins) or die ("Error en el Inser:  ".$ins);
            $reg="Guardado";
            Bloquea_Fuente_en_Concesinarias($db,$minimo);
        }
    }
    return $reg;
}
function Bloquea_Fuente_en_Concesinarias($db,$minimo)
{
    $sql="SELECT gid FROM groups ORDER BY gid;";
    $res=$db->sql_query($sql) or die ($sql);
    if( $db->sql_numrows($res) > 0)
    {
        while(list($gid)= $db->sql_fetchrow($res))
        {
            $db->sql_query("INSERT INTO crm_groups_fuentes (gid,fuente_id) VALUES ('".$gid."','".$minimo."');");
        }
    }

}
?>
