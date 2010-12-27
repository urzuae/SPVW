<?php
global $db;

echo"\n Almacenando los contactos del periodo: ".$fechai." al ".$fechac." a la tabla temporal\n\n";

$a="crm_contactos";
$b="crm_prospectos_unidades";
$c="groups_ubications";
$d="users";
$sql4="SELECT {$a}.contacto_id,{$a}.uid,{$a}.gid,{$a}.origen_id,{$a}.origen2_id,concat({$a}.nombre,' ',{$a}.apellido_paterno,' ',{$a}.apellido_materno) as prospecto,{$a}.fecha_importado,{$b}.modelo,{$b}.modelo_id,{$b}.version_id,{$b}.transmision_id,{$c}.name,{$d}.name as vendedor,{$c}.region_id,{$c}.zona_id,{$c}.entidad_id,{$c}.plaza_id,{$c}.grupo_empresarial_id,{$c}.nivel_id FROM ((({$a} LEFT JOIN {$b} ON {$a}.contacto_id = {$b}.contacto_id ) LEFT JOIN {$c} ON {$a}.gid={$c}.gid) LEFT JOIN {$d} ON {$a}.uid={$d}.uid AND {$d}.super<=8)  WHERE (substr({$a}.fecha_importado,1,10)>='".$fechai."' AND substr({$a}.fecha_importado,1,10)<='".$fechac."');";

$res_sql4=$db->sql_query($sql4) or die($sql4);
$num_sql4=$db->sql_numrows($res_sql4);
if($num_sql4 > 0)
{
    $contador=1;
    while ($fila=$db->sql_fetchrow($res_sql4))
    {
        $contador++;
        if(revisa_en_tabla($db,$tablename_conn,$fila['contacto_id'],$fila['modelo_id'],$fila['version_id'],$fila['transmision_id']) > 0)
            $accion="UPDATE";
        else
            $accion="INSERT";
        actualiza($db,$tablename_conn,$fila,$accion,$array_origenes);
    }
    echo"\n\nNumero de registros del dia ".substr($fechai,0,10).": ".$num_sql4;
    echo"\nTotal de registros almacenados: ".$num_sql4."\n";
}
else
{
    echo"\n\nNo hay registros en la fecha se&ntilde;alada:  ".$fecha."\n\n";
}


/********************  FUNCIONES AUXILIARES  ******************/
/**
 * Funcion que se encarga de inserta o actualizar el registro en la tabla generado
 * @param conexion bd $db
 * @param string $tablename_conn,  nombre de la tabla
 * @param int $contacto_id, numero de contacto
 * @return int 1 si es actualizaco, 0 si es creado
 */
function revisa_en_tabla($db,$tablename_conn,$contacto_id,$modelo_id,$version_id,$transmision_id)
{
    $reg=0;
    $sql_tmp="SELECT contacto_id,fecha_importado FROM ".$tablename_conn." WHERE contacto_id=".$contacto_id." AND modelo_id=".$modelo_id." AND version_id=".$version_id." AND transmision_id=".$transmision_id.";";
    //$sql_tmp="SELECT contacto_id,fecha_importado FROM ".$tablename_conn." WHERE contacto_id=".$contacto_id.";";
    $res_tmp=$db->sql_query($sql_tmp);
    $num_tmp=$db->sql_numrows($res_tmp);
    if ($num_tmp > 0)   $reg=1;
    return $reg;
}

/**
 * Esta funcion actualiza la tabla generada con los datos de los logs y de la region
 *
 * @param conexion bd $db
 * @param nombre de la tabla $tablename_conn
 * @param tupla de la tabla que afecta $fila
 * @param arreglo de los datos del log $array_datos
 */
function actualiza($db,$tablename_conn,$fila,$accion,$array_origenes)
{
    $campos="";
    $valores="";
    $cadena = "";
    $con=0;
    $valor_tmp=$fila['origen_id'];
    $valor_fuente=$array_origenes[$valor_tmp];
    foreach($fila as $clave => $valor)
    {
        $valor=str_replace("'","",$valor);

        if(($con%2)==1)
        {
            $campos.=$clave.",";
            if($clave=='origen2_id')
            {
                $valor=$valor_fuente;
            }
            $valores.="'".$valor."',";
            $cadena.=$clave."='".$valor."',";
        }
        $con++;
    }
    if($accion=="INSERT")
    {
        $tmp_sql="INSERT INTO ".$tablename_conn." (".substr($campos,0,(strlen($campos) - 1)).") VALUES (".substr($valores,0,(strlen($valores) - 1)).");";
    }
    else
    {
        $tmp_sql="UPDATE ".$tablename_conn." SET ".substr($cadena,0,(strlen($cadena) - 1))." WHERE contacto_id=".$fila['contacto_id']  .";";
    }
    if($db->sql_query($tmp_sql) or die ("Error en la consulta->".$tmp_sql))
    {
        echo"\n\n".$tmp_sql;
    }
}
?>
