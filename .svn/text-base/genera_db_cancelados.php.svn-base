<form name="eventos.php" method="post" action="genera_db_cancelados.php">
<?php
include_once("config.php");
$db= mysql_connect($_dbhost,$_dbuname,$_dbpass);
$base_selection = mysql_select_db($_dbname,$db);
$submit=$_POST['submit'];
$fecha_inicio=$_POST['fecha_inicio'];
$fecha_final=$_POST['fecha_final'];

if($submit)
{    
    $buf= Genera_BD($db,$fecha_inicio,$fecha_final);
    $file="bd_excel_cancelados".date("Y-m-d").".xls";
    $f1=fopen($file,"w+");
    fwrite($f1,$buf);
    fclose($f1);
    $buf.="<br><br><a href='".$file."'>Descargar Archivo</a><br><a href='genera_db_cancelados.php'>Otra Base de datos</a></br>";


}
else
{
    $buf="<table width='30%' align='center' style='border:1px solid #cdcdcd'>
       <thead>
            <tr bgcolor='#cdcdcd'><th colspan='2' align='center'>Favor de seleccionar la fuente</th></tr>
        </thead>
        <tbody>
            <tr><td>Fecha Inicial:</td><td><input type='text' name='fecha_inicio' id='fecha_inicio' value=\"".date('Y-m-d')."\" style='width:150px;border:1px solid #cdcdcd;'>&nbsp;&nbsp;&nbsp;aaaa-mm-dd</td></tr>
            <tr><td>Fecha Final:  </td><td><input type='text' name='fecha_final'  id='fecha_final' value=\"".date('Y-m-d')."\" style='width:150px;border:1px solid #cdcdcd;'>&nbsp;&nbsp;&nbsp;aaaa-mm-dd</td></tr>
        </tbody>
        <thead>
            <tr><th colspan='2' align='center'><br><input type='submit' name='submit' value='Generar Excel'></th></tr>
        </thead></table>";
}
echo $buf;



/********************* FUNCIONES AUXILIARES  *****************************/
function Genera_BD($db,$fecha_inicio,$fecha_final)
{
    $style1=" style='border:1px solid #cdcdcd';text-align:center;font-size:10px;background-color:#3e4f88;color:#ffffff;";
    $style=" style='border:1px solid #cdcdcd';text-align:left;font-size:10px;";
    $fecha_inicio=$fecha_inicio." 00:01:01";
    $fecha_final=$fecha_final." 23:59:59";
    $array_fuentes=Regresa_Catalogo($db,'crm_fuentes','fuente_id','nombre');
    $array_concesionarias=Regresa_Catalogo($db,'groups','gid','name');
    $array_vendedores=Regresa_Catalogo($db,'users','uid','name');
    $array_motivos=Regresa_Catalogo($db,'crm_prospectos_cancelaciones_motivos','motivo_id','motivo');
    $total=0;

    $sql="SELECT contacto_id,uid,motivo_id,motivo,timestamp FROM crm_prospectos_cancelaciones WHERE timestamp BETWEEN '".$fecha_inicio."' AND '".$fecha_final."';";
    $res=mysql_query($sql,$db) or die("Error en la consulta de cancelaciones:  ".$sql);
    if(mysql_num_rows($res)>0)
    {
        $buffer.="<table width='100%' ".$style.">
              <thead>
                <tr bgcolor='#cdcdcd'><td colspan='8' align='center'>Base de datos de prospectos cancelados del periodo: <b>".substr($fecha_inicio,0,10)." al ".substr($fecha_final,0,10)."</b></td></tr>
                <tr ".$style1.">
                    <td ".$style1.">Concesionaria</td>
                    <td ".$style1.">Vendedor</td>
                    <td ".$style1.">Fuente</td>
                    <td ".$style1.">Nombre del prospecto</td>
                    <td ".$style1.">Fecha de Ingreso</td>
                    <td ".$style1.">Fecha de Cancelaci&oacute;n</td>
                    <td ".$style1.">Motivo de Cancelacion</td>
                    <td ".$style1.">Observaciones</td>
                    <td ".$style1.">Tel_casa</td>
                    <td ".$style1.">Tel_oficina</td>
                    <td ".$style1.">Tel_movil</td>
                    <td ".$style1.">Tel_otro</td>
                    <td ".$style1.">Email</td>
                    <td ".$style1.">Poblacion</td>
                    <td ".$style1.">Ciudad</td>
                </tr>
                </thead
                <tbody>";

        while(list($contacto_id,$uid,$motivo_id,$motivo,$timestamp)=mysql_fetch_array($res))
        {
            $array_datos=Regresa_Datos($db,$contacto_id);
            $buffer.="<tr>
                        <td ".$style.">".$array_datos['gid']." ".$array_concesionarias[$array_datos['gid']]."</td>
                        <td ".$style.">".$array_vendedores[$uid]."</td>
                        <td ".$style.">".$array_fuentes[$array_datos['origen_id']]."</td>
                        <td ".$style.">".$array_datos['nombre']."</td>
                        <td ".$style.">".substr($array_datos['fecha_importado'],0,10)."</td>
                        <td ".$style.">".substr($timestamp,0,10)."</td>
                        <td ".$style.">".$array_motivos[$motivo_id]."</td>
                        <td ".$style.">&nbsp;".$motivo."</td>
                        <td ".$style.">".$array_datos['tel_casa']."</td>
                        <td ".$style.">".$array_datos['tel_oficina']."</td>
                        <td ".$style.">".$array_datos['tel_movil']."</td>
                        <td ".$style.">".$array_datos['tel_otro']."</td>
                        <td ".$style.">".$array_datos['email']."</td>
                        <td ".$style.">".$array_datos['poblacion']."</td>
                        <td ".$style.">".$array_datos['ciudad']."</td>
                     </tr>";
            $total++;
        }
    }
    $buffer.="<thead><tr><th colspan='15' align='justify'>Total de Registros:  ".$total."</th></tr></thead></table>";
    return $buffer;
}



function Regresa_Datos($db,$contacto_id)
{
    $array=array();
    $sql="SELECT contacto_id,concat(nombre,' ',apellido_paterno,' ',apellido_materno) as nombre,gid,uid,origen_id,fecha_importado,tel_casa,tel_oficina,tel_movil,tel_otro,email,poblacion,ciudad FROM crm_contactos_finalizados WHERE contacto_id=".$contacto_id." LIMIT 1;";
    $res=mysql_query($sql,$db) or die ("Error en la consulta:  ".$sql);
    if(mysql_num_rows($res)>0)
    {
        $array=mysql_fetch_array($res);
    }
    return $array;
}

function Regresa_Catalogo($db,$table,$field1,$field2)
{
    $array=array();
    $sql="SELECT ".$field1.",".$field2." FROM  ".$table." ORDER BY ".$field1.";";
    $res=mysql_query($sql,$db) or die ("Error en el catalogo:  ".$sql);
    if(mysql_num_rows($res)>0)
    {
        while(list($id,$name) = mysql_fetch_array($res))
        {
            $array[$id]=$name;
        }
    }
    return $array;
}

?></form>