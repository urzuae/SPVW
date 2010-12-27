<?php
global $db;
$arg = $argv[1];


//$array_unidades=regresa_unidades($db);
$sql="SELECT contacto_id,timestamp FROM crm_prospectos_ventas WHERE eliminar=0 AND modelo_id = 0 ORDER BY timestamp,contacto_id;";
echo"\n".$sql;
$res_sql=$db->sql_query($sql) or die($sql);
$num_sql=$db->sql_numrows($res_sql);
echo"\n\nTotal de Registros:  ".$num_sql."\n\n";
sleep(5);
if($num_sql > 0)
{
    while ($fila=$db->sql_fetchrow($res_sql))
    {        
        $array=regresa_id_modelo($db,$fila['contacto_id']);
        
		actualiza_id_modelo($db,$fila['contacto_id'],$array,$fila['timestamp']);
    }
}

function regresa_id_modelo($db,$contacto_id)
{
    $array=array();
    $sql_mod="SELECT modelo,modelo_id,version_id,transmision_id FROM crm_prospectos_unidades WHERE contacto_id='".$contacto_id."' limit 1;";
    //echo"\n".$sql_mod;
    $res_mod=$db->sql_query($sql_mod) or die($sql_mod);
    $num_mod=$db->sql_numrows($res_mod);
    if($num_mod > 0)
    {
        while ($fila=$db->sql_fetchrow($res_mod))
        {
	        $array=$fila;
        }
    }
    return $array;
}

function actualiza_id_modelo($db,$contacto_id,$array,$timestamp)
{
    $upda="UPDATE crm_prospectos_ventas SET timestamp='".$timestamp."', modelo_id=".$array['modelo_id'].",version_id=".$array['version_id'].",transmision_id=".$array['transmision_id']." WHERE contacto_id='".$contacto_id."' AND timestamp='".$timestamp."';";
    if($db->sql_query($upda))
    {
        echo"\nContacto:   ".$contacto_id."  TIMESTAMP:  ".$timestamp."\n".$upda."\n\n";
    }
}
function regresa_unidades($db)
{
	$array=array();
	$sql="SELECT * FROM crm_unidades ORDER BY unidad_id;";
	$res_sql=$db->sql_query($sql) or die($sql);
	$num_sql=$db->sql_numrows($res_sql);
	if($num_sql > 0)
	{
	    while ($fila=$db->sql_fetchrow($res_sql))
    	{
	    	$id=$fila['unidad_id'];
    	    $array[$id]=$fila['nombre'];
	    }    
	}
	return $array;
}
?>