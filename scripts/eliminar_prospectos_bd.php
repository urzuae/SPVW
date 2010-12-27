<?
if($_GET["gid"] != ""){
    require_once '../config.php';
    $db = mysql_connect($_dbhost,$_dbuname,$_dbpass);
    mysql_select_db($_dbname);

    $sql = "select contacto_id from crm_contactos_finalizados WHERE gid = '".$_GET['gid']."'";
    $cs = mysql_query($sql);
    $cont_ventas=0;
    $cont_unidades=0;
    $cont_cancelaciones=0;
    $cont_campanas=0;
    $cont=0;
    if(mysql_affected_rows($db) > 0){
        while(list($contacto_id) = mysql_fetch_row($cs))
        {
            if(Elimina_Contacto_Tablas($db,$contacto_id,'crm_prospectos_ventas') == 1)
                $cont_ventas++;
            if(Elimina_Contacto_Tablas($db,$contacto_id,'crm_prospectos_cancelaciones') == 1)
                $cont_cancelaciones++;
            if(Elimina_Contacto_Tablas($db,$contacto_id,'crm_prospectos_unidades') == 1)
                $cont_unidades++;
            if(Elimina_Contacto_Tablas($db,$contacto_id,'crm_campanas_llamadas_finalizadas') == 1)
                $cont_campanas++;
            if(Elimina_Contacto_Tablas($db,$contacto_id,'crm_contactos_finalizados') == 1)
                $cont++;

            echo "$contacto_id OK<br>";

            ob_flush();
            flush();
        }
        echo"<br>Total de ventas eliminadas:  ".$cont_ventas;
        echo"<br>Total de unidades:  ".$cont_unidades;
        echo"<br>Total de cancelaciones:  ".$cont_cancelaciones;
        echo"<br>Total de campa&ntilde;as:  ".$cont_campanas;
        echo"<br>Total de contactos_finalizados:  ".$cont;

    }
    echo "<br>$cont procesados.";
}


function Elimina_Contacto_Tablas($db,$contacto_id,$tabla)
{
    $reg=0;
    $sql="SELECT * FROM ".$tabla." WHERE contacto_id= ".$contacto_id.";";
    $res = mysql_query($sql);
    if(mysql_num_rows($res)>0)
    {
        $dl="DELETE FROM ".$tabla." WHERE contacto_id= ".$contacto_id.";";
        if(mysql_query($dl,$db) or die("Error en la eliminacion en la tabla:  ".$tabla."  en el contacto:  ".$contacto_id))
        {
                $reg=1;
        }
    }
    return $reg;
}
?>
