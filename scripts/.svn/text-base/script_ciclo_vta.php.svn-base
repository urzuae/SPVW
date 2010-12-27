<?php

include_once("../config.php");
$db= mysql_connect($_dbhost,$_dbuname,$_dbpass);
$base_selection = mysql_select_db($_dbname,$db);
#$sql="select gid FROM groups WHERE gid !='2016' order by gid;";
$sql="select gid FROM groups WHERE gid > 0 order by gid;";
$res=mysql_query($sql,$db) or die ("Error:  ".$sql);
if(mysql_num_rows($res)>0)
{
    while(list($gid) = mysql_fetch_array($res))
    {        
        $gid=str_pad($gid,4,'0',STR_PAD_LEFT);
        Revisa_no_Etapas($db,$gid);
    }
}


function Revisa_no_Etapas($db,$gid)
{
    $array_nm_campanas[1]='Captación Activa';
    $array_nm_campanas[2]='Primer Contacto';
    $array_nm_campanas[3]='Detección de Necesidades';
    $array_nm_campanas[4]='Presentación de Vehículo';
    $array_nm_campanas[5]='Prueba de Vehículo';
    $array_nm_campanas[6]='Oferta V.N.';
    $array_nm_campanas[7]='Oferta de Financiamiento';
    $array_nm_campanas[8]='Toma del Auto Usado';
    $array_nm_campanas[9]='Segumiento Preventa';
    $array_nm_campanas[10]='Cierre de la Venta';
    $array_nm_campanas[11]='Entrega V.N.';
    $array_nm_campanas[12]='Segumiento Postventa';


    $sql_g=" select campana_id FROM crm_campanas_groups WHERE gid='".$gid."' order by gid,campana_id;";
    $res_g=mysql_query($sql_g,$db);
    $num_g=mysql_num_rows($res_g);
    if( ($num_g > 0) && ($num < 12) )
    {
        for($i = 0; $i < $num_g; $i++)
        {
            $xcampana_id=mysql_result($res_g,$i,0);
            Actualiza_Nombre($db,$gid,$xcampana_id,$array_nm_campanas[$i+1],$i+1,2);

        }
        for($j=$num_g+1; $j <= count($array_nm_campanas);$j++)
        {
            $no_etapa=$gid.str_pad($j,2,0,STR_PAD_LEFT);
            $inser="INSERT INTO crm_campanas_groups (campana_id,gid) VALUES ('".$no_etapa."','".$gid."');";
            if(mysql_query($inser,$db) or die("Error en actualizar groups:  ".$inser)){
                if(Actualiza_Nombre($db,$gid,$no_etapa,$array_nm_campanas[$j],$j,1) == 1)
                {
                    echo "<br>Se actualizo la concesionaria:  ".$gid.", en el ciclo de venta de la concesionaria:  ".$gid."  campana_id ".$no_etapa;
                }
            }
        }
    }
}

    function Actualiza_Nombre($db,$gid,$xcampana_id,$titulo,$indice,$opc)
    {
        $xcampana_num=$xcampana_id+ 0;
        $reg=0;
        $etapa=$indice +1;
        $next=$gid.str_pad($etapa,2,0,STR_PAD_LEFT);
        if($indice == 12)    $next=0;
        $xnom=$xcampana_num.'-'.$indice.' '.$titulo;
        if($opc == 2)
        {
            $sql="UPDATE crm_campanas SET nombre='".$xnom."',next_campana_id=".$next." WHERE campana_id='".$xcampana_id."' AND etapa_ciclo_id=".$indice.";";
        }
        if($opc == 1)
        {
            $sql="INSERT INTO crm_campanas (campana_id,etapa_ciclo_id,nombre,fecha_ini,fecha_fin,timestamp,next_campana_id)
                  VALUES ('".$xcampana_id."','".$indice."','".$xnom."','".date('Y-m-d')."','".date('Y-m-d')."','".date('Y-m-d H:i:s')."','".$next."');";
        }
        if(mysql_query($sql,$db) or die ("error  AAA".$sql))
            $reg=1;
        return $reg;
    }
?>
