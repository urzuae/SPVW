<?
if($_GET["gid"] != ""){
    require_once '../config.php';
    $db = mysql_connect($_dbhost,$_dbuname,$_dbpass);
    mysql_select_db($_dbname);

    $sql = "select contacto_id from crm_contactos WHERE gid = '$_GET[gid]' and origen_id = -1";
    $cs = mysql_query($sql);
    if(mysql_affected_rows($db) > 0){
        while(list($contacto_id) = mysql_fetch_row($cs)){
            mysql_query("INSERT INTO crm_prospectos_cancelaciones (contacto_id, uid, motivo, motivo_id)VALUES('$contacto_id', '0', 'Prospectos sin atender por el vendedor', '0')");
            mysql_query("INSERT INTO crm_contactos_finalizados Select * from crm_contactos where contacto_id = '$contacto_id'");
            mysql_query("INSERT INTO crm_campanas_llamadas_finalizadas Select * from crm_campanas_llamadas where contacto_id = '$contacto_id'");
            mysql_query("delete from crm_contactos where contacto_id = '$contacto_id'");
            mysql_query("delete from crm_campanas_llamadas where contacto_id = '$contacto_id'");
            echo "$contacto_id OK<br>";
            ++$cont;
            ob_flush();
            flush();
        }
    }
    echo "<br>$cont procesados.";
}
?>
