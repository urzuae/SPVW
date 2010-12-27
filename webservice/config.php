<?php
if (eregi("config.php",$_SERVER['PHP_SELF'])) {
    Header("Location: index.php");
    die();
}
//valores para la libreria db
$_dbtype = 'MySQL';
$_dbhost = 'localhost';
$_dbuname = 'root';
$_dbpass = 'mysql_pwd!';
$_dbname = 'crm_prospectos';

//valores para el cms (directorios)
$_includesdir = 'includes';
$_modulesdir = 'modules';
$_htmldir = 'html'; //lo convertiremos en esto -> "$modulesdir/$_module/html"
//opciones
$_defaultmodule = 'Bienvenida';
$_theme = 'grayblue';
$_site_title = 'CRM';

$_csv_delimiter = ",";
?>
