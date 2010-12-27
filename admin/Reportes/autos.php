<?
//include_once("Monitoreo/genera_excel.php");
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $campana_id, $gid, $uid, $fecha_ini, $fecha_fin, $origen_id ;
global $_admin_menu2, $_admin_menu,$_excel;


//include_once("Monitoreo/regresa_filtros.php");
include_once("crea_Filtros_Reportes.php");
$filtro='';
if( count($tmp_filtros) > 0)
{
    $filtro=" AND ".implode(" AND ",$tmp_filtros);
}
if(count($tmp_filtros_conc) > 0)
{
    $filtro_conce=" AND ".implode(" AND ",$tmp_filtros_conc);
}
if(count($tmp_filtros_mod) > 0)
{
    $filtro_modelo=" AND ".implode(" AND ",$tmp_filtros_mod);
}
if(count($tmp_filtros_v)>0)
{
   $filtro_fecha=" AND ".implode(" AND ",$tmp_filtros_v);
}

include_once("templateFiltrosReportes.php");
include_once("construye_grafica.php");

/*global $db, $fecha_ini, $fecha_fin,$gid;

if($gid){
	$where_concesionaria .= " AND c.gid = '$gid'";
}

if ($fecha_fin || $fecha_ini) {
    $sql = "SELECT nombre FROM `crm_unidades`";
    $result = $db->sql_query ( $sql ) or die ( $sql );

    if ($fecha_ini) {
		$titulo .= " desde $fecha_ini";
		$fecha_ini = date_reverse ( $fecha_ini );
		$where_fecha .= " AND c.fecha_importado>'$fecha_ini 00:00:00'";
	}
	if ($fecha_fin) {
		$titulo .= " hasta $fecha_fin";
		$fecha_fin = date_reverse ( $fecha_fin );
		$where_fecha .= " AND c.fecha_importado<'$fecha_fin 23:59:59'";
	}

    while ( list ( $modelo_nombre) = $db->sql_fetchrow ( $result ) ) {
        $sql_con[] = "SELECT u.modelo, COUNT( u.contacto_id ) FROM crm_prospectos_unidades AS u, crm_contactos AS c
    	 WHERE u.modelo = '$modelo_nombre' AND c.contacto_id = u.contacto_id ".$where_fecha." $where_concesionaria GROUP BY u.modelo ";
    }
}
else{
	$sql_con[] = "select u.modelo,
	                     count(u.modelo)
	              from crm_prospectos_unidades as u,
	                   crm_contactos as c
	              WHERE  u.contacto_id = c.contacto_id
	                     $where_concesionaria
	              group by u.modelo";
}



$graph = "<br><iframe style=\"width:650px;height:500px;\" border=\"2\" frameBorder=\"NO\"  SCROLLING=\"NO\" name=\"graph\" src=\"?_module=$_module&_op=graph_autos&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&gid=$gid\"></iframe>";
$_html_b .= "<h1>Gráfica de Auto Prospectado</h1><center>$graph</center>";

if($gid){
	$where_concesionaria .= " AND c.gid = '$gid'";
	$l_excel .= "&gid=$gid";
}

if ($fecha_fin || $fecha_ini) {
    $sql = "SELECT nombre FROM `crm_unidades`";
    $result = $db->sql_query ( $sql ) or die ( $sql );
    
    if ($fecha_ini) {
    	$l_excel .= "&fecha_ini=$fecha_ini";
		$titulo .= " desde $fecha_ini";
		$fecha_ini_o = $fecha_ini;
		$fecha_ini = date_reverse ( $fecha_ini );
		$where_fecha .= " AND c.fecha_importado>'$fecha_ini 00:00:00'";
	}
	if ($fecha_fin) {
		$l_excel .= "&fecha_fin=$fecha_fin";
		$titulo .= " hasta $fecha_fin";
		$fecha_fin_o = $fecha_fin;
		$fecha_fin = date_reverse ( $fecha_fin );
		$where_fecha .= " AND c.fecha_importado<'$fecha_fin 23:59:59'";
	}
    
    while ( list ( $modelo_nombre) = $db->sql_fetchrow ( $result ) ) {
        $sql_con[] = "SELECT u.modelo, COUNT( u.contacto_id ) FROM crm_prospectos_unidades AS u, crm_contactos AS c
    	 WHERE u.modelo = '$modelo_nombre' AND c.contacto_id = u.contacto_id ".$where_fecha." $where_concesionaria GROUP BY u.modelo "; 	
    }
}
else{
	$sql_con[] = "select u.modelo, 
	                     count(u.modelo) 
	              from crm_prospectos_unidades as u,
	                   crm_contactos as c
	              WHERE  u.contacto_id = c.contacto_id
	                     $where_concesionaria
	              group by u.modelo";
}

    foreach ($sql_con as $consultas => $consulta){
	$result = $db->sql_query ( $consulta ) or die ( $consulta );
    while ( list ( $modelo, $cuenta ) = $db->sql_fetchrow ( $result ) ) {
       $tabla_contenido_autos .= "<tr class=\"row".(++$rowclass%2?"2":"1")."\" >
                    <td>$modelo</td>
                    <td>$cuenta</td>
                    </tr>";
	}
	$titulo = "Reporte Por Autos";
	$link_excel = "autos_excel".$l_excel;
  }
  $contenido_autos = "<table class='tablesorter' >
         <thead>
           <tr>
	         <td rowspan=\"1\">Modelo</td>
	         <td rowspan=\"1\">Total</td>
	      </tr>
	     </thead>
	     $tabla_contenido_autos
	   </table>";
  $fecha_fin = $fecha_fin_o;
  $fecha_ini = $fecha_ini_o;
   
  $sql = "SELECT gid, name FROM groups";
  $r = $db->sql_query($sql) or die($sql);
  $groups = array();
  while (list($id, $n) = $db->sql_fetchrow($r)){
  	if($gid == $id){
		$concesionaria_origen = $n;
		break; 
	} 
}
  $select_groups = "<select name=\"gid\">";
  $result = $db->sql_query("SELECT gid,name FROM groups WHERE 1 ORDER BY gid") or die("Error al cargar grupos");
  $select_groups .= "<option value=\"\">Selecciona una concesionaria</option>\n";
  while(list($_gid,$name) = $db->sql_fetchrow($result)){
  	if ($_gid == $gid)
  	  $selected = " SELECTED";
  	else
  	  $selected = "";
  	$select_groups .= "<option value=\"$_gid\"$selected>$_gid - $name</option>";
  }
  $select_groups .= "</select>";*/
 ?>
