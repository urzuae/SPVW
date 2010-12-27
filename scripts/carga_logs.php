<?php
global $db;
$arg = $argv[0];
$fechai=$argv[2];
$fechac=$argv[3];
if( (empty($fechai)) && (empty($fechac)) )
{
    $fechai=date('Y-m-d');
    $fechac=date('Y-m-d');
}
if ( empty($fechac) )
{
    $fechac=$fechai;
}
if ( empty($fechai) )
{
    $fechai=$fechac;
}
$sql_asignacion="select contacto_id,timestamp FROM crm_contactos_asignacion_log WHERE substr(timestamp,1,10) BETWEEN '".$fechai."' AND '".$fechac."' ORDER BY contacto_id,timestamp;";
$res_asignacion=$db->sql_query($sql_asignacion);
$num_asignacion=$db->sql_numrows($res_asignacion);
echo"\nNumero de Registros:   ".$num_asignacion;
sleep(2);
if( $num_asignacion > 0 )
{
    $contador=1;
    while(list($contacto_id,$timestamp) =$db->sql_fetchrow($res_asignacion))
    {
        $opcion=Busca_en_contactos($db,$contacto_id) + 0;
        Registra_contacto($db,$contacto_id,$timestamp,$opcion,$contador);
        $contador++;
    }
}
echo"\n Eliminando registros que han sido eliminados de la tabla de crm_contactos";
$db->sql_query("delete from unicos_log where contacto_id not in (select contacto_id from crm_contactos);");

echo"\n Actualizando gid, de los registros de la tabla de crm_contactos";
$db->sql_query("UPDATE unicos_log AS a SET a.gid=(select b.gid FROM crm_contactos as b WHERE b.contacto_id=a.contacto_id);");

echo"\n Actualizando uid, de los registros de la tabla de crm_contactos";
$db->sql_query("UPDATE unicos_log AS a SET a.uid=(select b.uid FROM crm_contactos as b WHERE b.contacto_id=a.contacto_id);");


  // Funcion Auxiliar para saber si ya esta el id del contacto registrado
function Registra_contacto($db,$contacto_id,$timestamp,$opcion,$contador)
{
    $fecha_actual=date('Y-m-d H:i:s');
    if($opcion == 0)
    {
            $upd="INSERT INTO  unicos_log (contacto_id,timestamp,fecha_act) VALUES ('".$contacto_id."','".$timestamp."','".$fecha_actual."');";
    }
    if($opcion == 1)
    {
            $upd="UPDATE unicos_log SET timestamp='".$timestamp."' ,fecha_act='".$fecha_actual."' WHERE contacto_id=".$contacto_id.";";
    }
    if($db->sql_query($upd))
    {
        echo"\n".$contador."  ".$contacto_id."   ".$upd;
    }
}
function Busca_en_contactos($db,$contacto_id)
{
    $contenido=0;
    $sql_con="SELECT contacto_id FROM unicos_log WHERE contacto_id=".$contacto_id." LIMIT 1;";
    $res_con=$db->sql_query($sql_con);
    if($db->sql_numrows($res_con)>0)
    {
        $contenido=1;
    }
    return $contenido;
}
/*

$sql="delete from unicos_logs where contacto_id not in (select contacto_id from crm_contactos);";
$db->sql_query($sql);
echo"\n Eliminando registros que han sido eliminados de la tabla de crm_contactos";

$sql="SELECT contacto_id FROM unicos_log WHERE timestamp='0000-00-00 00:00:00' ORDER BY contacto_id DESC;";
$res=$db->sql_query($sql);
$num=$db->sql_numrows($res);
echo"\n Numero de Registros:  ".$num;
$contador=0;
while(list($contacto_id)=$db->sql_fetchrow($res))
{
    $fecha_actual=date('Y-m-d H:i:s');
    echo"\n Contacto:  ".$contacto_id;
    $xtimestamp=Regresa_ultima_movimiento($db,$contacto_id);
    $upd="UPDATE unicos_log SET timestamp='".$xtimestamp."' ,fecha_act='".$fecha_actual."' WHERE contacto_id=".$contacto_id.";";
    if($db->sql_query($upd))
    {
        $contador++;
        echo"\n:  ".$contador."  ".$contacto_id."   ".$upd;
    }
}

function Regresa_ultima_movimiento($db,$contacto_id)
{
    $ult_fecha='0000-00-00 00:00:00';
    $sql_asi="SELECT timestamp FROM crm_contactos_asignacion_log where contacto_id=".$contacto_id." order by timestamp DESC  LIMIT 1;";
    $res_asi=$db->sql_query($sql_asi);
    if($db->sql_numrows($res_asi)>0)
    {
        $ult_fecha=$db->sql_fetchfield(0,0,$res_asi);
    }
    return $ult_fecha;
}*/
?>
