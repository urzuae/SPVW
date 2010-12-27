<?
//   if (!defined('_IN_ADMIN_MAIN_INDEX')) {
//     die ("No puedes acceder directamente a este archivo...");
// }
    global $db, $file, $submit, $del;
global $_admin_menu2, $_admin_menu;
// $_admin_menu = " ";
/*$_admin_menu2 = "
<h2>Casos Clientes</h2>
<a href=\"index.php?_module=$_module&_op=casos_no_resueltos\" >Casos clientes no resueltos</a><br>
<a href=\"index.php?_module=$_module&_op=casos_no_resueltos_usuario\" >Casos clientes no resueltos por usuario</a><br>
<a href=\"index.php?_module=$_module&_op=casos_resueltos\" >Casos clientes resueltos</a><br>
<a href=\"index.php?_module=$_module&_op=casos_no_procede\" >Casos clientes no procedentes</a><br>

<h2>Encuestas</h2>
<a href=\"index.php?_module=$_module&_op=encuestas\" >Encuesta para gráfica</a><br>
";*/
/*
 * <a href=\"index.php?_module=$_module&_op=autos\">Autos</a><br>
<a href=\"index.php?_module=$_module&_op=zonas\">Zonas</a><br>
<a href=\"index.php?_module=$_module&_op=origenes\">Origenes</a><br>
<a href=\"index.php?_module=$_module&_op=ciclo_venta\">Ciclos de Venta</a><br>
<a href=\"index.php?_module=$_module&_op=ciclo_venta_gestionados\">Ciclos de Venta Gestionados</a><br>

<a href=\"index.php?_module=$_module&_op=total_ventas_por_concesionaria\">Total ventas por concesionaria </a><br>

<a href=\"index.php?_module=$_module&_op=total_prospectos_por_concesionaria\">Total prospectos por concesionaria </a><br>
<a href=\"index.php?_module=$_module&_op=procesados_concesionaria\">Total procesados por concesionaria</a><br>

 */
$_admin_menu2 .= "<h2>Campañas</h2>
<a href=\"index.php?_module=$_module&_op=campanas_avance\" >Reporte de avance</a><br>
<a href=\"index.php?_module=$_module&_op=penalizacion_usuarios\" >Reporte de penalizaciones por usuario</a><br>
<br>
<h2>Estadísticas</h2>
<a href=\"index.php?_module=$_module&_op=autos\">Graficas</a><br>
<a href=\"index.php?_module=$_module&_op=graficas\">Graficas_T</a><br>
<a href=\"index.php?_module=$_module&_op=status_concesionaria\">Status concesionaria</a><br>
<a href=\"index.php?_module=$_module&_op=prospectos_no_asignados\">Prospectos no asignados a concesionarias</a><br>
<br>
<h2>Reportes</h2>
<a href=\"index.php?_module=$_module&_op=pdf_cantidad_ventas_concretadas_por_vendedor\">Cantidad de Ventas Concretadas por Vendedor</a><br>
<a href=\"index.php?_module=$_module&_op=pdf_cancelaciones_ventas\">Cantidad de Cancelaciones de Ventas</a><br>
<a href=\"index.php?_module=$_module&_op=pdf_cancelaciones_ventas_motivos\">Motivos de Cancelaciones de Ventas</a><br>
";
 ?>
