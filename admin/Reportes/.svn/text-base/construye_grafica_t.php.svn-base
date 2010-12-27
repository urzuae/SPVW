<?php
include_once("Monitoreo/genera_excel.php");
global $db;
$grafica=$_REQUEST['grafica'];
$tabla_sin_vendedores='';
if(empty($grafica))
{
    $sql="SELECT unidad_id,nombre FROM crm_unidades ORDER BY unidad_id;";
    $res=$db->sql_query($sql);
    while($rs=$db->sql_fetchrow($res))
    {
        $array_titulos[$rs['unidad_id']]=$rs['nombre'];
    }
    $sql_count="SELECT count(*) as total_global
                  FROM reporte_contactos_asignados a
                  WHERE a.gid > 0 ".$filtro.";";

    $sql="SELECT a.modelo_id as ids,count(a.modelo_id) as total
                  FROM reporte_contactos_asignados a
                  WHERE a.gid > 0 ".$filtro."
                  GROUP BY a.modelo_id; ";

    $titulo="Autos Prospectados";
    $tmp_celda="Modelo";
    $res_count=$db->sql_query($sql_count);
    $res=$db->sql_query($sql);

}
else
{
    switch($grafica)
    {
        case 0:
        case 1:
            $sql="SELECT unidad_id,nombre FROM crm_unidades ORDER BY unidad_id;";
            $res=$db->sql_query($sql);
            while($rs=$db->sql_fetchrow($res))
            {
                $array_titulos[$rs['unidad_id']]=$rs['nombre'];
            }

            $sql_count="SELECT count(*) as total_global
                  FROM reporte_contactos_asignados a
                  WHERE a.gid > 0 ".$filtro.";";

            $sql="SELECT a.modelo_id as ids,count(a.modelo_id) as total
                  FROM reporte_contactos_asignados a
                  WHERE a.gid > 0 ".$filtro."
                  GROUP BY a.modelo_id ; ";

            $titulo="Autos Prospectados";
            $titulo_izq="Modelos";
            $tmp_celda="Modelo";
            $res_count=$db->sql_query($sql_count);
            $res=$db->sql_query($sql);
            break;
        case 2:
            $sql="SELECT zona_id,nombre FROM crm_zonas ORDER BY zona_id;";
            $res=$db->sql_query($sql);
            while($rs=$db->sql_fetchrow($res))
            {
                $array_titulos[$rs['zona_id']]=$rs['nombre'];
            }
            $sql_count="SELECT count( a.zona_id ) AS total_global
                        FROM reporte_contactos_asignados a
                        WHERE a.gid > 0 ".$filtro.";";

            $sql="SELECT a.zona_id AS ids,count( a.zona_id ) AS total
                  FROM reporte_contactos_asignados a
                  WHERE a.gid > 0  ".$filtro."
                  GROUP BY a.zona_id";

            $titulo="Reporte de Zonas";
            $titulo_izq="Zonas";
            $tmp_celda="Zona";
            $res_count=$db->sql_query($sql_count);
            $res=$db->sql_query($sql);

            break;
        case 3:
            $sql="SELECT fuente_id,nombre FROM crm_fuentes ORDER BY fuente_id;";
            $res=$db->sql_query($sql);
            while($rs=$db->sql_fetchrow($res))
            {
                $array_titulos[$rs['fuente_id']]=$rs['nombre'];
            }
            $sql_count="SELECT count( * ) AS total_global
                  FROM reporte_contactos_asignados a
                  WHERE a.gid > 0 ".$filtro.";";

            $sql="SELECT a.origen_id as ids,count( a.origen_id) as total
                  FROM reporte_contactos_asignados a
                  WHERE a.gid > 0 ".$filtro."
                  GROUP BY a.origen_id;";

            $titulo="Reporte de Origenes";
            $tmp_celda="Orig&eacute;n";
            $titulo_izq="Origenes";
            $res_count=$db->sql_query($sql_count);
            $res=$db->sql_query($sql);
            break;
        case 6:
            $sql_create = "CREATE TABLE tmp_vtas_t AS SELECT DISTINCT c.contacto_id,c.modelo_id,c.version_id,c.transmision_id,c.uid,u.name as vendedor,u.gid,u.email as name,1 as region_id,10 as zona_id,10 as entidad_id,100 as plaza_id,1 as nivel_id,100 as grupo_empresarial_id
            FROM crm_prospectos_ventas c LEFT JOIN users u ON c.uid = u.uid
            WHERE c.contacto_id>0 ".$filtro_fecha." ORDER BY u.gid,c.contacto_id";
            $res_create=$db->sql_query($sql_create) or die("Error al CREAR la tabla:  ".$sql_create);
            $udp_vtas="update tmp_vtas_t as a SET a.name=(SELECT b.name FROM groups as b WHERE b.gid=a.gid);";
            $res_udp_vtas=$db->sql_query($udp_vtas) or die("Error al Actualizar la tabla:  ".$udp_vtas);
            $res_upd_r=$db->sql_query("update tmp_vtas_t as a SET a.region_id=(SELECT b.region_id FROM groups_ubications as b WHERE b.gid=a.gid);");
            $res_upd_r=$db->sql_query("update tmp_vtas_t as a SET a.zona_id=(SELECT b.zona_id FROM groups_ubications as b WHERE b.gid=a.gid);");
            $res_upd_r=$db->sql_query("update tmp_vtas_t as a SET a.entidad_id=(SELECT b.entidad_id FROM groups_ubications as b WHERE b.gid=a.gid);");
            $res_upd_r=$db->sql_query("update tmp_vtas_t as a SET a.plaza_id=(SELECT b.plaza_id FROM groups_ubications as b WHERE b.gid=a.gid);");
            $res_upd_r=$db->sql_query("update tmp_vtas_t as a SET a.nivel_id=(SELECT b.nivel_id FROM groups_ubications as b WHERE b.gid=a.gid);");
            $res_upd_r=$db->sql_query("update tmp_vtas_t as a SET a.grupo_empresarial_id=(SELECT b.grupo_empresarial_id FROM groups_ubications as b WHERE b.gid=a.gid);");
            if($filtro != '')
            {
                $tablas.= ", reporte_contactos_asignados a";
                $filtro_ventas.= " AND v.contacto_id=a.contacto_id".$filtro." ";
            }
            $sql_count="select count(*) AS total_global FROM tmp_vtas_t v ".$tablas." WHERE v.name is not null ".$filtro_ventas.";";
            $sql="select v.gid as ids,v.name as leyenda,count(v.gid) AS total FROM tmp_vtas_t v ".$tablas." WHERE v.name is not null ".$filtro_ventas." GROUP BY v.gid ORDER BY v.gid;";
            $res_count=$db->sql_query($sql_count);
            $res=$db->sql_query($sql);
            $tabla_sin_vendedores=Muestra_ventas_sin_vendedores($db);
            $sql_drop="DROP TABLE tmp_vtas_t;";
            $res_drop=$db->sql_query($sql_drop) or die("Error al ELIMINAR la tabla:  ".$sql_drop);
            $titulo="Reporte de Ventas por concesionaria";
            $tmp_celda="Concesionaria";
            break;

        case 7:
            $sql_count="SELECT count( a.contacto_id ) AS total_global
                  FROM reporte_contactos_asignados a
                  WHERE a.gid >  0 ".$filtro.";";

            $sql="SELECT a.gid as ids,a.name as leyenda,count(a.gid) as total
                  FROM reporte_contactos_asignados a
                  WHERE a.gid > 0 ".$filtro."
                  GROUP BY a.gid;";

            $titulo="Reporte de Prospectos por concesionaria";
            $tmp_celda="Concesionaria";
            $res_count=$db->sql_query($sql_count);
            $res=$db->sql_query($sql);
            break;

        case 8:
            $sql_create="CREATE TABLE tmp_procesados_t AS  SELECT DISTINCT a.contacto_id,a.uid,a.gid,a.origen_id,
                         a.modelo_id,a.version_id,a.transmision_id,a.name,a.region_id,a.zona_id,a.entidad_id,a.plaza_id,
                         a.grupo_empresarial_id,a.nivel_id
                         FROM reporte_contactos_asignados a, crm_campanas_llamadas l, crm_campanas_llamadas_eventos e
                         WHERE a.gid > 0 AND  a.contacto_id = l.contacto_id
                         AND l.id = e.llamada_id ".$filtro.";";
            $res_create=$db->sql_query($sql_create) or die("Error al CREAR la tabla:  ".$sql_create);
            $sql_count="SELECT COUNT(*) AS total_global
                  FROM tmp_procesados_t;";
            $res_count=$db->sql_query($sql_count);
            $sql="SELECT a.gid as ids,a.name as leyenda,count(a.gid) as total
                  FROM tmp_procesados_t a 
                  GROUP BY a.gid;";
            $res=$db->sql_query($sql);
            $sql_drop="DROP TABLE tmp_procesados_t;";
            $res_drop=$db->sql_query($sql_drop) or die("Error al ELIMINAR la tabla:  ".$sql_drop);
            $titulo="Reporte de Procesados por concesionaria";
            $tmp_celda="Concesionaria";
            break;
    }
}
if(file_exists($archivo))
    unlink($archivo);
$colors = array();
$data_titulos = array();
$data_valores = array();
$archivo='archivos/blanco.png';
$archivom='';

$count_totales=1;
$count_porcen=0;
if($db->sql_numrows($res_count) > 0)
{
    $count_totales=$db->sql_fetchfield(0,0, $res_count);
    $num=$db->sql_numrows($res);
    if( $num > 0)
    {
        $archivo='archivos/salidag.jpg';
        $archivom='archivos/salidam.jpg';
        $tabla_resul="<table width='50%' class='tablesorter' align='center border='0'>
                      <thead><tr><th>Id</th><th>$tmp_celda</th><th>T</th><th>%</th>";
        if($num <= 15)
        {
           $tabla_resul.=" <th>Color</th>";
        }
        $tabla_resul.="</tr></thead><tbody>";

        $totales=0;
        $maximo=0;
        while ($fila=$db->sql_fetchrow($res))
        {
            $totales=$totales  + $fila['total'];
            $porcentaje=number_format((($fila['total'] / $count_totales)*100),2,'.','');
            $randomColor = dechex(rand(100, 100000));
            $randomColor="#".str_pad($randomColor,6,'0',STR_PAD_RIGHT);
            $count_porcen = $count_porcen + $porcentaje;
            if($grafica < 6)
            {
                $tmp=$array_titulos[$fila['ids']];
                $data_titulos[]=$tmp;
            }
            else
            {
                $tmp=$fila['leyenda'];
                $data_titulos[]=$fila['leyenda'];
            }
            if($maximo < $fila['total'])
                $maximo=$fila['total'];
                
            $data_valores[]=$fila['total'];
            $data_ids[]="".$fila['ids'];
            $colors[] = $randomColor;
            $tabla_resul.="<tr class=\"row".($class_row++%2?"2":"1")."\"><td>".$fila['ids']."</td><td>".$tmp."</td><td>".
            $fila['total']."</td ><td>".$porcentaje."</td>";
            if($num <= 15)
            {
                $tabla_resul.="<td bgcolor='$randomColor'></td>";
            }
            $tabla_resul.="</tr>";
        }
        $tabla_resul.="<thead><tr><td></td><td>Totales: ".count($data_valores)."</td><td>".$totales."</td><td>".number_format($count_porcen,0,'.','')."%</td>";
        if($num <= 15)
        {
           $tabla_resul.="<td></td>";
        }
        $tabla_resul.="</tr></thead></table>";
        $tabla_resul.="<br>".$tabla_sin_vendedores;
        if(count($data_valores) > 0)
        {
           include ("$_includesdir/jpgraph/jpgraph.php");
           if(count($data_valores) <= 15)
            {
                include("$_includesdir/jpgraph/jpgraph_pie.php");
                include("$_includesdir/jpgraph/jpgraph_pie3d.php");
                $graph = new PieGraph(600,650,"auto");
                $graph->title->Set($titulo);
                $graph->title->SetFont(FF_FONT1,FS_NORMAL);
                $graphm = new PieGraph(1200,1300,"auto");
                $graphm->title->Set($titulo);
                $graphm->title->SetFont(FF_FONT1,FS_NORMAL);
                $p1 = new PiePlot3D($data_valores);
                $p1->SetSliceColors($colors);
                $p1->SetSize(.35);
                $p1->SetCenter(.45);
                $p1->SetStartAngle(0);
                $p1->SetLegends($data_titulos);
                $p1->SetLabelType(PIE_VALUE_ABS);
                $p1->value->SetFormat('%d');
                $p1->value->Show();
                $p1->ExplodeAll(20);
                $graph->Add($p1);
                $graph->legend->Pos(0.01,0.99,"right", "bottom");
                $graph->Stroke($archivo);
                $graphm->Add($p1);
                $graphm->legend->Pos(0.01,0.99,"right", "bottom");
                $graphm->Stroke($archivom);
            }
            else
            {

                include ("$_includesdir/jpgraph/jpgraph_bar.php");
                include ("$_includesdir/jpgraph/jpgraph_scatter.php");
                switch($grafica)
                {
                    case 0:
                    case 1:
                    case 2:
                    case 3:
                        pinta_grafica_vehiculos($archivo,$data_titulos,$data_valores,$data_ids,$colors,$titulo,$archivom,$titulo_izq,$grafica);
                        break;
                    case 6:
                    case 7:
                    case 8:
                    {
                        if(count($data_valores) <= 35)
                            pinta_grafica_concesionarias($archivo,$data_titulos,$data_valores,$data_ids,$colors,$titulo,$archivom);
                         else
                            pinta_grafica_concesionarias_totales($archivo,$data_titulos,$data_valores,$data_ids,$colors,$titulo,$archivom);
                        break;
                    }
                }
            }
        }
        $titulo=$titulo."_".date('Y-m-d');
        $objeto = new Genera_Excel($tabla_resul,$titulo);
        $boton_excel=$objeto->Obten_href();
        $boton_grafica="<br><a href=".$archivo." target='_blank'>Ver Gráfico m&aacute;s grande</a>";
    }
}

function pinta_grafica_concesionarias($archivo,$data_titulos,$data_valores,$data_ids,$colors,$titulo,$archivom)
{
    $width=750;
    $height=900;
    $top = 120;
    $bottom = 30;
    $left = 100;
    $right = 30;

    $graph = new Graph($width,$height,'auto');
    $graph->SetScale("textlin");
    $graph->Set90AndMargin($left,$right,$top,$bottom);
    $graph->title->Set($titulo);
     $graph->title->SetColor("#ba8c30");
    $graph->title->SetFont(FF_FONT2,FS_BOLD);
    
    $graph->xaxis->SetTickLabels($data_ids);
    $graph->xaxis->SetLabelAlign('right','center','right');
    $graph->yaxis->SetLabelAlign('center','bottom');

    $txt = new Text();
    $txt->SetFont(FF_FONT1,FS_BOLD);
    $txt->SetColor("#3e4f88");
    $txt->Set("Id's   de   Concesionarias");
    $txt->SetPos(200,800,'center','center');
    $txt->SetOrientation(90);

    $txtx = new Text("Total de prospectos");
    $txtx->SetPos(0,480,'left','top');
    $txtx->SetFont(FF_FONT1,FS_BOLD);
    $txtx->SetColor("#3e4f88");

    $graph->AddText($txt);
    $graph->AddText($txtx);

    $bplot = new BarPlot($data_valores);
    $bplot->SetFillColor("#E6E6EB");
    $bplot->SetWidth(0.6);
    $bplot->value->SetFormat('%d');
    $bplot->value->Show();
    $bplot->value->SetAlign('center','center');
    $graph->Add($bplot);
    $graph->Stroke($archivo);
}

function pinta_grafica_vehiculos($archivo,$data_titulos,$data_valores,$data_ids,$colors,$titulo,$archivom,$titulo_izq,$grafica)
{
    $width=750;
    $height=2000;
    $top = 120;
    $bottom = 30;
    $left = 120;
    $right = 30;
    $graph = new Graph($width,$height,'auto');
    $graph->SetScale("textlin");

    $graph->Set90AndMargin($left,$right,$top,$bottom);
    $graph->title->Set($titulo);
    $graph->title->SetColor("#ba8c30");
    $graph->title->SetFont(FF_FONT2,FS_BOLD);
    $graph->xaxis->SetColor("#000000");
    $graph->xaxis->SetTickLabels($data_titulos);
    $graph->xaxis->SetLabelAlign('right','center','right');
    $graph->yaxis->SetLabelAlign('center','bottom');

    $txt = new Text();
    $txt->SetFont(FF_FONT1,FS_BOLD);
    $txt->SetColor("#3e4f88");
    $txt->Set($titulo_izq);
    $txt->SetPos(-58,1350,'left','top');
    $txt->SetOrientation(90);   
    

    $txtx = new Text("Total de prospectos");
    $txtx->SetPos(-550,1000,'left','top');
    $txtx->SetFont(FF_FONT1,FS_BOLD);
    $txtx->SetColor("#3e4f88");

    $graph->AddText($txt);
    $graph->AddText($txtx);

    $bplot = new BarPlot($data_valores);
    $bplot->SetFillColor("#E6E6EB");
    $bplot->SetWidth(0.5);
    $bplot->value->SetFormat('%d');
    $bplot->value->Show();
    $bplot->value->SetAlign('center','center');
    $graph->Add($bplot);
    $graph->Stroke($archivo);
}
function pinta_grafica_concesionarias_totales($archivo,$data_titulos,$data_valores,$data_ids,$colors,$titulo,$archivom)
{
    $width=750;
    $height=3900;
    $top = 120;
    $bottom = 30;
    $left = 100;
    $right = 30;

    $graph = new Graph($width,$height,'auto');
    $graph->SetScale("textlin");
    $graph->Set90AndMargin($left,$right,$top,$bottom);
    $graph->title->Set($titulo);
    $graph->title->SetColor("#ba8c30");
    $graph->title->SetFont(FF_FONT2,FS_BOLD);

    $graph->xaxis->SetColor("#000000");
    $graph->xaxis->SetTickLabels($data_ids);
    $graph->xaxis->SetLabelAlign('right','center','right');
    $graph->yaxis->SetLabelAlign('center','bottom');

    $txt = new Text();
    $txt->SetFont(FF_FONT1,FS_BOLD);
    $txt->SetColor("#3e4f88");
    $txt->Set("Id's   de   Concesionarias");
    $txt->SetPos(-1300,2300,'center','center');
    $txt->SetOrientation(90);

    $txtx = new Text("Total de prospectos");
    $txtx->SetPos(-1500,2000,'left','top');
    $txtx->SetFont(FF_FONT1,FS_BOLD);
    $txtx->SetColor("#3e4f88");

    $graph->AddText($txt);
    $graph->AddText($txtx);

    $bplot = new BarPlot($data_valores);
    $bplot->SetFillColor("#E6E6EB");
    $bplot->SetWidth(0.6);
    $bplot->value->SetFormat('%d');
    $bplot->value->Show();
    $bplot->value->SetAlign('center','center');
    $graph->Add($bplot);
    $graph->Stroke($archivo);
}

function Muestra_ventas_sin_vendedores($db)
{
    $concesionarias=Regresa_Groups($db);
    $total_tuplas_fin=0;
    $total_tuplas_con=0;
    $sql="select b.gid,count(b.gid) as total from tmp_vtas_t a, crm_contactos_finalizados b where a.contacto_id=b.contacto_id and a.vendedor IS NULL group by b.gid order by b.gid;";
    $res=$db->sql_query($sql);
    $no_tuplas=$db->sql_numrows($res);
    if($no_tuplas> 0)
    {
        while($fila = $db->sql_fetchrow($res))
        {
            $array[$fila['gid']+ 0]=$fila['total'];
            $total_tuplas_fin = $total_tuplas_fin + $fila['total'];
        }
    }
    $sql="select b.gid,count(b.gid) as total from tmp_vtas_t a, crm_contactos b where a.contacto_id=b.contacto_id and a.vendedor IS NULL group by b.gid order by b.gid;";
    $res=$db->sql_query($sql);
    $no_tuplas_c=$db->sql_numrows($res);

    if($no_tuplas_c> 0)
    {
        while($fila = $db->sql_fetchrow($res))
        {
            $array[$fila['gid']+ 0]=  $array[$fila['gid']] + $fila['total'];
            $total_tuplas_con = $total_tuplas_con  + $fila['total'];
        }
    }
    $total_tuplas=$total_tuplas_fin + $total_tuplas_con;
    $buffer='';
    if(count($array) > 0)
    {
        $total=0;
        $totalprom=0;
        $buffer.="<Table width='50%' align='center' border='0' class='tablesorter'>
            <thead>
            <tr bgcolor='#333333'>
            <td>Id</td>
            <td>Concesionaria</td>
            <td>Ventas sin Vendedor</td>
            <td>%</td>
            </tr></thead><tbody>";
        foreach($array as $clave => $valor)
        {
            $total=$total + $valor;
            $porcen=($valor*100)/$total_tuplas ;
            $totalprom= $totalprom +$porcen;
            $buffer.="
            <tr class=\"row".($class_row++%2?"2":"1")."\">
            <td>$clave</td>
            <td>$concesionarias[$clave]</td>
            <td>$valor</td>
            <td>".number_format($porcen,2,'.','')."</td>
            </tr>";
        }
        $buffer.="</tbody>
            <thead>
            <tr bgcolor='#333333'>
              <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>$total</td>
            <td>".number_format($totalprom,0,'.','')." %</td>
            </tr></thead></table>";
    }
    return $buffer;
}


function Regresa_Groups($db)
{
    $array=array();
    $sql="select gid,name FROM groups where gid>3 ORDER BY gid;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res)> 0)
    {
        while($fila = $db->sql_fetchrow($res))
        {
            $array[$fila['gid']+ 0]=$fila['name'];
        }
    }
    return $array;
}

?>