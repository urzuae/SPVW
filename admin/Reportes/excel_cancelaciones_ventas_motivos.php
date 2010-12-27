<?php
    $table="No existen ventas registradas";
    include_once("Reportes/genera_excel.php");
    $array_fuentes=Regresa_Fuentes($db);
    $total=0;
    $sql = "SELECT u.gid, u.name, v.motivo_id,v.contacto_id FROM `crm_prospectos_cancelaciones` AS v, users AS u where v.uid=u.uid $and_fecha $and_motivo ";
    $result = $db->sql_query($sql) or die("Error: ".$sql);
    if ($db->sql_numrows($result) > 0)
    {
        $table="
            <table width='90%' align='center' border='0'>
            <tbody>
                <tr><td colspan='4' align='center'>Motivos de cancelaciones de ventas por vendedor ".$titulo." ".$tit_motivo."</td></tr>
            </tbody>
            <thead>
            <tr bgcolor='#cdcdcd'>
                <th align='center'>Id Concesionaria</th>
                <th align='center'>Nombre del Vendedor</th>
                <th align='center'>Motivos de cancelaciones</th>
                <th align='center'>Fuente u Origen</th>
            </tr>
            </thead><tbody>";
        while(list($id_concesionaria, $nombre_vendedor, $motivo_ventas_canceladas,$contacto_id) = $db->sql_fetchrow($result))
        {
            $id_fuente=Regresa_Fuente($db,$contacto_id);
            $table.="
                <tr>
                    <td align='left'>".$id_concesionaria."</td>
                    <td align='left'>".$nombre_vendedor."</td>
                    <td align='left'>".$array[$motivo_ventas_canceladas]."</td>
                    <td align='left'>".$array_fuentes[$id_fuente]."</td>
                </tr>";
        }
        $table.="</tbody></table>";
        $objeto = new Genera_Excel($table,'Ventas_Canceladas_motivos_vendedor');
        $url=$objeto->Obten_href();
        header('location: '.$url);
    }



    function Regresa_Fuentes($db)
    {
        $array=array();
        $sql="SELECT fuente_id,nombre FROM crm_fuentes WHERE fuente_id != 0 ORDER BY fuente_id;";
        $res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
        if($db->sql_numrows($res) > 0)
        {
            while(list($id,$name)= $db->sql_fetchrow($res))
            {
                $array[$id]=$name;
            }
        }
        return $array;
    }

    function Regresa_Fuente($db,$contacto_id)
    {
        $id_fuente=0;
        $sql="SELECT origen_id FROM crm_contactos WHERE contacto_id = ".$contacto_id." LIMIT 1;";
        $res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
        if($db->sql_numrows($res) > 0)
        {
            $id_fuente=$db->sql_fetchfield(0,0,$res);
        }
        else
        {
            $sql="SELECT origen_id FROM crm_contactos_finalizados WHERE contacto_id = ".$contacto_id." LIMIT 1;";
            $res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
            if($db->sql_numrows($res) > 0)
            {
                $id_fuente=$db->sql_fetchfield(0,0,$res);
            }
        }
        return $id_fuente;

    }
?>
