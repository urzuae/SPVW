<?php
global $db;

$arg = $argv[1];

$sql="SELECT DISTINCT modelo FROM crm_prospectos_unidades WHERE modelo_id=0 ORDER BY modelo;";
$res_sql=$db->sql_query($sql) or die($sql);
$num_sql=$db->sql_numrows($res_sql);
if($num_sql > 0)
{
    while ($fila=$db->sql_fetchrow($res_sql))
    {
        echo"\n Nombre del modelo:  ".$fila['modelo']."\n";
        $id_modelo=regresa_id_modelo($db,$fila['modelo']);
        actualiza_id_modelo($db,$fila['modelo'],$id_modelo);
    }
}

function regresa_id_modelo($db,$modelo)
{
    $id_tmp=0;
    $sql_mod="SELECT unidad_id FROM crm_unidades WHERE nombre='".$modelo."';";
    $res_mod=$db->sql_query($sql_mod) or die($sql_mod);
    $num_mod=$db->sql_numrows($res_mod);
    if($num_mod > 0)
    {
        $id_tmp=$db->sql_fetchfield(0,0,$res_mod);
    }
    return $id_tmp;
}

function actualiza_id_modelo($db,$modelo,$id_modelo)
{
    $upda="UPDATE crm_prospectos_unidades SET modelo_id=".$id_modelo." WHERE modelo='".$modelo."';";
    if($db->sql_query($upda))
    {
        echo"\n".$upda;
    }
}

?>
