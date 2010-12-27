<?
include_once("genera_excel.php");
if (!defined('_IN_MAIN_INDEX'))
{
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $how_many, $from, $campana_id, $nombre, $apellido_paterno, $apellido_materno,
       $submit, $status_id, $ciclo_de_venta_id, $uid, $orderby, $rsort, $open,$_dbhost,
       $_dbuname,$_dbpass,$_dbname,$submit_mp,$fecha_ini,$fecha_fin;
include_once("class_autorizado.php");
$sql  = "SELECT gid, super FROM users WHERE uid='".$_COOKIE['_uid']."'";
$result = $db->sql_query($sql) or die("Error");
list($gid, $super) = $db->sql_fetchrow($result);
if ($super > 6)
{
    die("<h1>Usted no es un Gerente</h1>");
}
else
{
    if(($submit_mp) || ($submit))
    {
        $fecha_inicial="2008-01-01 00:01:01";
        $fecha_final=date('Y-m-d H:i:s');
        if($fecha_ini!='')
            $fecha_inicial=substr($fecha_ini,6,4).'-'.substr($fecha_ini,3,2).'-'.substr($fecha_ini,0,2).' 00:01:01';
        if($fecha_fin!='')
            $fecha_final  =substr($fecha_fin,6,4).'-'.substr($fecha_fin,3,2).'-'.substr($fecha_fin,0,2).' 23:59:59';

        $sql = "SELECT c.campana_id, nombre FROM crm_campanas AS c, crm_campanas_groups  AS g WHERE c.campana_id=g.campana_id AND g.gid='$gid' ORDER BY campana_id LIMIT 0,25";
        $result = $db->sql_query($sql) or die("Error al consultar campañas ".print_r($db->sql_error()));
        if($db->sql_numrows($result)> 0)
        {
            while (list($campana_id, $name) = htmlize($db->sql_fetchrow($result)))
            {
                $campanasNombre[]=array(
                'campana' 	=> $name,
                'campanaId'	=> $campana_id);
            }
        }
        $array_versiones=Regresa_Versiones($db);
        $array_transmisiones=Regresa_Transmisiones($db);
        $hoy=date('Y-m-d H:i:s');
        $linkTodb = mysqli_connect($_dbhost,$_dbuname,$_dbpass);
        if (mysqli_connect_errno())
        {
            echo "Error de Conexion";
            exit();
        }
        $conn = mysqli_select_db ($linkTodb,$_dbname);
        if (! $conn)
        {
            echo "Error de Base de Datos";
        }        
        $result = mysqli_query($linkTodb,"CALL reporte_prospectos('$gid','$fecha_inicial','$fecha_final')");
        if (! $result)
        {
            echo "error de procedure";
            exit;
        }
        $i=1;
        if(mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result,MYSQLI_NUM))
            {
                #echo "<pre>".print_r($row)."</pre><br>";
                $counter=$i++;
                $retraso=$row[10];
                $campanas[]=array('campaña' =>$row[2]);
                $registros[]=array(
                'campana'	=> $row[2],
                'idllamada'	=> $row[12],
                'idcampana'	=> $row[0],
                'idcontacto'	=> $row[11],
                'origen'	=> $row[3],
                'nombre'	=> $row[4],
                'vendedor'	=> $row[5],
                'espera'	=> $row[6],
                'prim_contacto'	=> $row[7],
                'ulti_contacto'	=> $row[8],
                'compromiso'	=> $row[9],
                'retraso'	=> $retraso,
                'total_campana'	=> $row[13],
                'fecha_importado'=> $row[14],
                'tel_casa'=> $row[15],
                'tel_oficina'=> $row[16],
                'email'=> $row[17],
                'domicilio'=> $row[18],
                'colonia'=> $row[19],
                'cp'=> $row[20],
                'poblacion'=> $row[21],
                'fecha_autorizado'=> $row[22],
                'fecha_firmado'=> $row[23],
                'tel_movil'=> $row[24],
                'codigo_campana'=> $row[25],
                'medio_entero'=> $row[26]
                );
            }
        }
        $contador_tablas=0;
        $modelos_seleccionados='';
        $array_medios=array(0 => '',1 => 'Radio',2 =>  'TV',3 =>  'Periódico',4 =>  'Volantes',5 =>  'Revistas',6 =>  'Mantas',7 =>  'Invitación de vendedor');
        if ( (count($campanasNombre) > 0) )
        {
            foreach($campanasNombre as $valor)
            {
                $total_bloque=0;
                $display_bloque = "none";
                $icono_bloque = "more";
                $contador_tablas++;
                $tabla_campanas .="<form onchange='capsall(this);' method='post' action='index.php' name='seleccionar' onsubmit='return validate(this);'>";
                $tabla_campanas .='<input name="_module" value="'.$_module.'" type="hidden"><input name="_op" value="monitoreo_vendedor_reasignar" type="hidden">
                <input name="uid" value="$uid" type="hidden">
                <input name="open" id="open" value="$open" type="hidden">';
                $tabla_campanas .="<table style=\"text-align: left; width: 100%;\" border=\"0\" cellpadding=\"2\" cellspacing=\"2\"> <tbody>
                <tr style=\"cursor:pointer\" onclick=\"var v=document.getElementById('bloque_$uid_$valor[campanaId]');	var i=document.getElementById('img_$uid_$valor[campanaId]'); var o=document.getElementById('open');	if(v.style.display=='none'){v.style.display='block';i.src='img/less.gif';o.value = o.value+'$valor[campanaId]'+'-';}else{ v.style.display='none';i.src='img/more.gif';o.value = o.value.replace('$valor[campanaId] ','')}\">
                <th><img src=\"img/pixel.gif\" width=\"15px\"><img src=\"img/$icono_bloque.gif\" id=\"img_$uid_$valor[campanaId]\"> $valor[campana]</th>
                </tr>
                </table>
                <div id=\"bloque_$uid_$valor[campanaId]\" style=\"display:$display_bloque;\">
                <table id=\"tabla_contactos$contador_tablas\" class=\"tablesorter\" >
                    <thead>
                      <tr>
                      <th style=\"width:150px; cursor:pointer;\">Campaña</td>
                      <th style=\"width:360px; cursor:pointer;\">Nombre</th>
                      <th style=\"width:340px; cursor:pointer;\">Vendedor</th>
                      <th style=\"width:150px; cursor:pointer;\">Registro</th>
                      <th style=\"width:170px; cursor:pointer;\">Primer contacto</th>
                      <th style=\"width:170px; cursor:pointer;\">Último contacto</th>
                      <th style=\"width:170px; cursor:pointer;\">Compromiso</th>
                      <th style=\"width:150px; cursor:pointer;\">Retraso (hrs)</th>
                      <th style=\"width:32px; cursor:pointer;\">Sel.</th></tr>
                    </thead>
                <tbody>";
                $tabla_excel.=
                "<table style=\"text-align: left; width: 100%;\" border=\"0\" cellpadding=\"2\" cellspacing=\"2\"> <tbody>
                <tr style=\"cursor:pointer\" >
                    <th><img src=\"img/$icono_bloque.gif\" id=\"img_$uid_$valor[campanaId]\">$valor[campana]</th>
                </tr>
                </table>
                <div id=\"bloque_$uid_$valor[campanaId]\" style=\"display:$display_bloque;\">
                <table>
                 <thead>
                      <tr>
                      <th>Campaña</td>
                      <th>Nombre</th>
                      <th>Vendedor</th>
                      <th>Registro</th>
                      <th>Primer contacto</th>
                      <th>Último contacto</th>
                      <th>Compromiso</th>
                      <th>Retraso (hrs)</th>
                      <th>Tel Casa</th>
                      <th>Tel Oficina</th>
                      <th>Tel Movil</th>
                      <th>Email</th>
                      <th>Domicilio</th>
                      <th>Colonia</th>
                      <th>Cp</th>
                      <th>Poblacion</th>
                      <th>Código de Campaña</th>
                      <th>Medio Entero</th>
                      <th>Unidades de Interes</th>
                    </thead>
                    <tbody>";
                    if(count($registros)> 0)
                    {
                        $modelos_seleccionados='';
                        foreach($registros as $valores)
                        {
                            $campanaOriginal=$valor['campanaId'];
                            $campanaDeRegistro=$valores['idcampana'];
                            $i=1;
                            if($campanaOriginal == $campanaDeRegistro)
                            {
                                if($valores[retraso]<= 0)
                                    $valores[retraso]=0;
                                $modelos_seleccionados=Regresa_Modelos($db,$valores['idcontacto'],$array_versiones,$array_transmisiones);
                                $total_bloque++;
                                $conunt=$i+$i;
                                $objeto= new Fecha_autorizado ($db,$valores['fecha_autorizado'],$valores['fecha_firmado']);
                                $color_semaforo=$objeto->Obten_Semaforo();
                                $tabla_campanas .= "<tr class=\"row".(($c++%2)+1)."\">"
                                ."<td>$valores[origen]</td>"
                                ."<td><a class='background-color:#fff;' target=\"llamada\" href=\"index.php?_module=Campanas&_op=llamada_ro&llamada_id=$valores[idllamada]&contacto_id=$valores[idcontacto]&campana_id={$valores[idcampana]}\">
                                $valores[nombre]</a>&nbsp;&nbsp;<span style='background-color:{$color_semaforo}'>&nbsp;&nbsp;&nbsp;</span></td>"
                                ."<td align='left'>$valores[vendedor]</td>"
                                ."<td align='left'>$valores[fecha_importado]</td>"
                                ."<td align='left'>$valores[prim_contacto]</td>"
                                ."<td align='left'>$valores[ulti_contacto]</td>"
                                ."<td align='left'>$valores[compromiso]</td>"
                                ."<td align='left'>$valores[retraso]</td>"
                                ."<td><input type=\"checkbox\" name=\"chbx_$valores[idcontacto]\" style=\"height:12;width:16;\"></td>"
                                ."</tr>";
                                $total_campana=$valores['total_campana'];
                                $tabla_excel.= "<tr class=\"row".(($c++%2)+1)."\">"
                                ."<td align='left'>$valores[origen]</td>"
                                ."<td align='left'>$valores[nombre]</a></td>"
                                ."<td align='left'>$valores[vendedor]</td>"
                                ."<td align='left'>$valores[fecha_importado]</td>"
                                ."<td align='left'>$valores[prim_contacto]</td>"
                                ."<td align='left'>$valores[ulti_contacto]</td>"
                                ."<td align='left'>$valores[compromiso]</td>"
                                ."<td align='left'>$valores[retraso]</td>"
                                ."<td align='left'>$valores[tel_casa]</td>"
                                ."<td align='left'>$valores[tel_oficina]</td>"
                                ."<td align='left'>$valores[tel_movil]</td>"
                                ."<td align='left'>$valores[email]</td>"
                                ."<td align='left'>$valores[direccion]</td>"
                                ."<td align='left'>$valores[colonia]</td>"
                                ."<td align='left'>$valores[cp]</td>"
                                ."<td align='left'>$valores[poblacion]</td>"
                                ."<td align='left'>$valores[codigo_campana]</td>"
                                ."<td align='left'>".$array_medios[$valores['medio_entero']]."</td>"
                                ."<td>$modelos_seleccionados</td>"
                                ."</tr>";
                            }
                        }
                    }
                    $tabla_campanas .= "</tbody>";
                    $tabla_campanas .= "<tr class=\"row".(($c++%2)+1)."\"><td align=\"right\"><b>Total</b></td><td><b>$total_bloque</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
                    $tabla_campanas .= "</table>";
                    $tabla_campanas .= "</div>";
                    $tabla_excel.= "</tbody>
                        <tr class=\"row".(($c++%2)+1)."\">
                    <td align=\"right\"><b>Total</b></td><td><b>$total_bloque</b></td><td colspan='18'>&nbsp;</td>
                    </tr>
                    </table>";
            }
            $tabla_campanas .= "<table class=\"width100\">";
            $tabla_campanas .= "<thead>";
            $tabla_campanas .= "<tr class=\"row".(($c++%2)+1)."\"><th></th><th align=\"left\" colspan=\"6\"><b>Total</b><b> $counter</b></th></tr>";
            $tabla_campanas .= "</thead></table>";
            $tabla_excel.="<table>
                    <thead>
                       <tr class=\"row".(($c++%2)+1)."\"><th></th><th align=\"left\" colspan=\"7\"><b>Total</b><b> $counter</b></th></tr>
                    </thead></table>";
            $tabla_campanas .= "<table class=\"width100\"><tr class=\"row".(++$row_class%2+1)."\" style=\"text-align:center;\">"
            ."<td colspan=7>"
            ."<input name=\"all\" type=\"button\" onclick=\"allon();\" value=\"Todos\">&nbsp;"
            ."<input name=\"none\" type=\"button\" onclick=\"alloff();\" value=\"Ninguno\"></td></tr>"
            ."<tr class=\"row".(++$row_class%2+1)."\" style=\"text-align:center;\">"
            ."<td colspan=7>"
            ."<input type=\"submit\" onclick='return validate();' name=\"seleccionar\" value=\"Reasignar\"></td></tr>"
            ."<tr class=\"row".(++$row_class%2+1)."\" style=\"text-align:center;\"></table></form>";

            $archivo='Monitoreo_de_Prospectos'.$gid;
            if(file_exists("admin/archivos/".$archivo.".xls"))
                unlink("admin/archivos/".$archivo.".xls");

            $objeto = new Genera_Excel($tabla_excel,$archivo);
            $boton_excel=$objeto->Obten_href();
        }
    }
    else
    {
        $fecha_ini='';
        $fecha_fin='';
    }
}
function Regresa_Modelos($db,$idcontacto,$array_versiones,$array_transmisiones)
{
    $lista_modelos='';
    $sql="SELECT modelo,modelo_id,version_id,transmision_id,ano,paquete,tipo_pintura,accesorios,color_exterior,color_interior FROM crm_prospectos_unidades WHERE contacto_id=".$idcontacto.";";
    $res=$db->sql_query($sql) or die("Error en la consulta de vehiculos");
    if($db->sql_numrows($res)>0)
    {
        while(list($modelo,$modelo_id,$version_id,$transmision_id,$ano,$paquete,$tipo_pintura,$accesorios,$color_exterior,$color_interior) = $db->sql_fetchrow($res))
        {
            $tmp='';
            if(strlen($array_versiones[$version_id]) > 0)   $tmp.=', Version:  '.$array_versiones[$version_id];
            if(strlen($array_transmisiones[$transmision_id]) > 0)   $tmp.=', Transmision:  '.$array_transmisiones[$transmision_id];
            if((strlen($ano)>0) && ($ano > 0)) $tmp.=", A&ntilde;o: ".$ano;
            if(strlen($tipo_pintura)>0 )    $tmp.=", tipo pintura: ".$tipo_pintura;
            if(strlen($color_exterior)>0)   $tmp.=", Color Ext:   ".$color_exterior;
            if(strlen($color_interior)>0)   $tmp.=", Color Int:   ".$color_interior;
            $lista_modelos.=$modelo." ".$tmp."<br>";
        }
    }
    return $lista_modelos;
}
function Regresa_Versiones($db)
{
    $array=array();
    $sql="SELECT version_id,nombre FROM crm_versiones ORDER BY version_id;";
    $res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$name) = $db->sql_fetchrow($res))
        {
            $array[$id]=$name;
        }
    }
    return $array;
}
function Regresa_Transmisiones($db)
{
    $array=array();
    $sql="SELECT transmision_id,nombre FROM crm_transmisiones ORDER BY transmision_id;";
    $res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        while(list($id,$name) = $db->sql_fetchrow($res))
        {
            $array[$id]=$name;
        }
    }
    return $array;

}

?>