<?php
/**
 * Script que genera tabla con los siguientes datos de contactos reasignados
 * Contacto_id, uid, gid, nombre del prospecto, fecha de alta, nombre del vendedor, nombre del vendedor anterior,
 * penultimo movimiento en log, ultimo movimiento en log, fechas de penultimo y ultimo movimientos en logs
 */

global $db;
$arg = $argv[1]; 
$fechai=$argv[2];
$fechac=$argv[3];

if(empty($fechac))
	$fechac=$fechai;
	

	$tablename_conn="reporte_contactos_reasignados";
	$fecha_inicio=date("Y-m-d H:i:s");
    $a="crm_contactos";
    $b="crm_prospectos_unidades";
    $c="groups";
    $d="users";
    $e="groups_zonas";
    $f="crm_plazas_concesionarias";
    $g="crm_grupos_concesionarias";
    $h="crm_niveles_concesionarias";
    //$sql4="SELECT {$a}.contacto_id,{$a}.uid,{$a}.gid,{$a}.origen_id,{$a}.origen2_id,concat({$a}.nombre,' ',{$a}.apellido_paterno,' ',{$a}.apellido_materno) as prospecto,{$a}.entidad_id,{$a}.fecha_importado,{$b}.modelo,{$c}.name,{$d}.name as vendedor,{$e}.zona_id as zona_id,{$f}.plaza_id as plaza_gid,{$g}.grupo_empresarial_id,{$h}.nivel_id FROM ((((((({$a} LEFT JOIN {$b} ON {$a}.contacto_id = {$b}.contacto_id ) LEFT JOIN {$c} ON {$a}.gid={$c}.gid) LEFT JOIN {$d} ON {$a}.uid={$d}.uid AND {$d}.super=8) LEFT JOIN {$e} ON {$a}.gid={$e}.gid) LEFT JOIN {$f} ON {$a}.gid={$f}.gid) LEFT JOIN {$g} ON {$a}.gid={$g}.gid) LEFT JOIN {$h} ON {$a}.gid={$h}.gid) WHERE  {$a}.contacto_id=155617;";
    $sql4="SELECT {$a}.contacto_id,{$a}.uid,{$a}.gid,{$a}.origen_id,{$a}.origen2_id,concat({$a}.nombre,' ',{$a}.apellido_paterno,' ',{$a}.apellido_materno) as prospecto,{$a}.entidad_id,{$a}.fecha_importado,{$b}.modelo,{$c}.name,{$d}.name as vendedor,{$e}.zona_id as zona_id,{$f}.plaza_id as plaza_gid,{$g}.grupo_empresarial_id,{$h}.nivel_id FROM ((((((({$a} LEFT JOIN {$b} ON {$a}.contacto_id = {$b}.contacto_id ) LEFT JOIN {$c} ON {$a}.gid={$c}.gid) LEFT JOIN {$d} ON {$a}.uid={$d}.uid AND {$d}.super=8) LEFT JOIN {$e} ON {$a}.gid={$e}.gid) LEFT JOIN {$f} ON {$a}.gid={$f}.gid) LEFT JOIN {$g} ON {$a}.gid={$g}.gid) LEFT JOIN {$h} ON {$a}.gid={$h}.gid) WHERE (substr({$a}.fecha_importado,1,10)>='".$fechai."' AND substr({$a}.fecha_importado,1,10)<='".$fechac."') ORDER BY {$a}.fecha_importado;";
    echo"\n".$sql4."\n";
    $res_sql4=$db->sql_query($sql4) or die($sql4);
	$num_sql4=$db->sql_numrows($res_sql4);
	if($num_sql4 > 0)
	{
//	    echo"\n Numero de registros del dia ".substr($fechai,0,10).": ".$num_sql4."\n";
	    $contador=1;
	    while ($fila=$db->sql_fetchrow($res_sql4))    
	    {   
            echo"\n contacto_id:  ".$fila['contacto_id'];
            $contador++;
	        $array_log=revisa_logs($db,$fila['contacto_id']);
    	    $array_log['region_id']=asigna_region($db,$fila['gid']);
            $array_log['entidad_gid']=asigna_entidad($db,$fila['plaza_gid']);
    		if(revisa_en_tabla($db,$tablename_conn,$fila['contacto_id']) > 0)
            	$accion="UPDATE";
        	else
	            $accion="INSERT";
    	    actualiza($db,$tablename_conn,$fila,$array_log,$accion);
    	}
    	echo"\n\nTotal de registros almacenados: ".$num_sql4."\n";   	
	//    revisa_origen_padre($db,$tablename_conn,$fechai,$fechac);
	}
	else 
	{
		echo"\n\nNo hay registros en la fecha se&ntilde;alada:  ".$fechai."\n\n";
	}
echo"\nFecha de inicio del proceso:  ".$fecha_inicio;
$fecha_termino=date("Y-m-d H:i:s");
echo"\nFecha de termino del proceso:  ".$fecha_termino;
echo"\n\n";


/***************** FUNCIONES AUXILIARES   **********************/

/**
 * Esta funcion actualiza la tabla generada con los datos de los logs y de la region
 *
 * @param conexion bd $db
 * @param nombre de la tabla $tablename_conn
 * @param tupla de la tabla que afecta $fila
 * @param arreglo de los datos del log $array_datos
 */
function actualiza($db,$tablename_conn,$fila,$array_datos,$accion)
{
	$campos="";
	$valores="";
	$con=0;
	foreach($fila as $clave => $valor)
	{
		$valor=str_replace("'","",$valor);
		if(($con%2)==1)
		{
			$campos.=$clave.",";
			$valores.="'".$valor."',";
			$cadena.=$clave."='".$valor."',";
		}
		$con++;
	}
	foreach($array_datos as $clave => $valor)
	{
			$valor=str_replace("'","",$valor);		
            $campos.=$clave.",";
            $valores.="'".$valor."',";
            $cadena.=$clave."='".$valor."',";
	}
    if($accion=="INSERT")
    {
    	$tmp_sql="INSERT INTO ".$tablename_conn." (".substr($campos,0,(strlen($campos) - 1)).") VALUES (".substr($valores,0,(strlen($valores) - 1)).");";
    }
    else
    {
    	$tmp_sql="UPDATE ".$tablename_conn." SET ".substr($cadena,0,(strlen($cadena) - 1))." WHERE contacto_id=".$fila['contacto_id']  .";";
	}
	if($db->sql_query($tmp_sql))
	{
		echo"\n\n".$tmp_sql;
	}
}
/**
 * Funcion que busca el contacto en la tabla de reporte para decidir si hace un INSERT 	o UPDATE
 *
 * @param int conexion a la $db
 * @param string nombre de la tabla $tablename_conn
 * @param int id del contacto $contacto_id
 * @return boolean $reg
 */
function revisa_en_tabla($db,$tablename_conn,$contacto_id)
{
	$reg=0;
	$sql_tmp="SELECT contacto_id,fecha_importado FROM ".$tablename_conn." WHERE contacto_id=".$contacto_id.";";   
    $res_tmp=$db->sql_query($sql_tmp);
    $num_tmp=$db->sql_numrows($res_tmp);
    if ($num_tmp > 0)
        $reg=1;
    return $reg;
}

/**
 * Funcion que sirve para traer la entidad correspondiente de la concesionaria
 *
 * @param int conexion a la $db
 * @param int id de la concesionaria $gid
 * @return int numero de entidad correspondiente a la concesionaria
 */
function asigna_entidad($db,$plaza_gid)
{   
    $array_tmp[0]=0;
    if($plaza_gid>0)
    {
        $sql_tmp="select entidad_id FROM  crm_plazas WHERE plaza_id=".$plaza_gid.";";
        $res_tmp=$db->sql_query($sql_tmp) or die($sql_tmp);
        if($db->sql_numrows($res_tmp)>0)
        $array_tmp=$db->sql_fetchrow($res_tmp);
        else
            $array_tmp[0]=0;
    }
    return $array_tmp[0];

}


/**
 * Funcion que regresa el id de la region
 *
 * @param conexion bd $db
 * @param id del grupo o concesionaria $gid
 */
function asigna_region($db,$gid)
{
    $sql_tmp="select b.region_id from groups_zonas a, crm_zonas b WHERE a.gid=".$gid." AND a.zona_id=b.zona_id;";
    $res_tmp=$db->sql_query($sql_tmp) or die($sql_tmp);
    if($db->sql_numrows($res_tmp)>0)
        $array_tmp=$db->sql_fetchrow($res_tmp);
    else
    $array_tmp[0]=0;
    return $array_tmp[0];
    
}

/**
 * Funcion que regresa el nombre de un vendedor
 *
 * @param conexion bd $db
 * @param id del vendedor $uid
 * @return arreglo con los datos del vendedor
 */

function asigna_vendedor($db,$uid)
{
    $sql_tmp="SELECT uid,gid,super,name,user from users WHERE uid=".$uid." AND super=8;";
    $res_tmp=$db->sql_query($sql_tmp) or die($sql_tmp);
    $array_tmp=$db->sql_fetchrow($res_tmp);
    return $array_tmp['name'];
}

/**
 * Funcion que regresa el nombre de una concesionaria
 *
 * @param conexion bd $db
 * @param id de la concesionaria $gid
 * @return arreglo con los datos de la concesionaria
 */
function asigna_concesionaria($db,$gid)
{
    $sql_tmp="SELECT gid,name from groups WHERE gid=".$gid.";";
    $res_tmp=$db->sql_query($sql_tmp) or die($sql_tmp);
    $array_tmp=$db->sql_fetchrow($res_tmp);
    return $array_tmp['name'];
}

/**
 * Funcion que consulta los logs de un contacto, para sacar el vendedor anterior, primer fecha de asignacion, ultima fecha de reasignacion y numero de reasignaciones
 *
 * @param conexion bd $db
 * @param id de contacto $contacto_id
 * @return arreglo con los datos de los logs
 */
function revisa_logs($db,$contacto_id)
{
    $sql_tmp="SELECT contacto_id,from_uid,to_uid,from_gid,to_gid,timestamp from crm_contactos_asignacion_log WHERE contacto_id=".$contacto_id." ORDER BY timestamp DESC ;";
    $res_tmp=$db->sql_query($sql_tmp) or die($sql_tmp);
    while($tmp=$db->sql_fetchrow($res_tmp))
    {
        $array_tuplas[]=$tmp;
    }
    return genera_registro($db,$array_tuplas);
}

/**
 * Funcion que crea el arreglo con los datos correspondientes de los logs
 *
 * @param array  con el numero de logs de un contacto $array_tuplas
 * @return array con los datos especificos de los logs del contacto
 */
function genera_registro($db,$array_tuplas)
{
	$array_datos['no_reasignaciones']=(count($array_tuplas) - 1);
	for($contador=0;$contador < count($array_tuplas); $contador++)
	{
		if($contador==0)
			$array_datos['ultima_reasignacion']=$array_tuplas[$contador]['timestamp'];	

		if( ($array_tuplas[$contador]['to_uid'] > 0)  )
			$array_datos['to_uid']=$array_tuplas[$contador]['to_uid'];
		
		
		if( ($array_tuplas[$contador]['to_gid'] > 0)  )
			$array_datos['to_gid']=$array_tuplas[$contador]['to_gid'];
		
		if( $contador == (count($array_tuplas)- 1) )
			$array_datos['primera_reasignacion']=$array_tuplas[$contador]['timestamp'];

         if($contador < (count($array_tuplas) -1 ))
         {
             asigna_reasignaciones_vendedor($db,$array_tuplas[$contador]['to_uid'],'recibidos');
             asigna_reasignaciones_vendedor($db,$array_tuplas[$contador]['from_uid'],'quitados');

         }
	}
	$array_datos['vendedor_anterior']='';
	if($array_datos['to_uid']>0)
		$array_datos['vendedor_anterior']=asigna_vendedor($db,$array_datos['to_uid']);
		
	$array_datos['concesionaria_anterior']='';
	if($array_datos['to_gid']>0)
	$array_datos['concesionaria_anterior']=asigna_concesionaria($db,$array_datos['to_gid']);	
	
	return $array_datos;
}

function asigna_reasignaciones_vendedor($db,$uid,$campo)
{
    $tabla=" reporte_contactos_reasignados_vendedores ";
    if($uid>0)
    {
        $sql_ven="SELECT ".$campo." FROM ".$tabla." WHERE uid=".$uid.";";
        $res_ven=$db->sql_query($sql_ven);
        if($db->sql_numrows($res_ven) > 0)
        {
            $total=$db->sql_fetchfield($campo, 0,$res_ven);
            echo"\ntotal:  ".$total;
            $total=$total + 1;
            echo"\ntotal:  ".$total;
            $query="UPDATE ".$tabla." SET ".$campo."=".$total." WHERE uid=".$uid.";";
        }
        else
        {
            $rec=0;
            $qui=0;
            if($campo=='recibidos')
                $rec=1;
            if($campo=='quitados')
                $qui=1;
            $query="INSERT INTO ".$tabla." VALUES (".$uid.",".$rec.",".$qui.");";
        }
        if($db->sql_query($query))
        {
            echo"\n".$query;
        }
    }
}
/**
 * funcion que saca los distintis origenes hijos
 *
 * @param conexion BD$db
 * @param String nombre de la tabla $tablename_conn
 */
function revisa_origen_padre($db,$tablename_conn,$fechai,$fechac)
{
    $sql="SELECT DISTINCT origen_id FROM ".$tablename_conn." WHERE (substr({$a}.fecha_importado,1,10)>='".$fechai."' AND substr({$a}.fecha_importado,1,10)<='".$fechac."') ORDER BY origen_id;";
    $res_sql=$db->sql_query($sql) or die($sql);
    while($fila=$db->sql_fetchrow($res_sql))
    {
        $tmp_entidad=asigna_origen($db,$fila['origen_id']);
        actualiza_origen($db,$fila['origen_id'],$tmp_entidad,$tablename_conn);
    }
}

/**
 * Funcion que actualiza el origen del padre 
 *
 * @param conexion $db
 * @param int origen del hijo $origen_hijo
 * @param int origen padre $origen_padre
 * @param string tabla $tablename_conn
 */
function actualiza_origen($db,$origen_hijo,$origen_padre,$tablename_conn)
{
	$upd="UPDATE ".$tablename_conn." SET origen2_id=".$origen_padre." WHERE origen_id=".$origen_hijo.";";
	#echo"\n exito:  ".$upd;
	if($db->sql_query($upd))
		echo"\n exito:  ".$upd;
}

/**
 * Funcion que regresa el origen padre dependiendo del origen hijo
 *
 * @param conexion $db
 * @param int id del origen hijo  $origen
 * @return int id del padre
 */
function asigna_origen($db,$origen)
{
    $sql_tmp="select origen_padre_id FROM crm_contactos_origenes WHERE origen_id=".$origen.";";
    $res_tmp=$db->sql_query($sql_tmp) or die($sql_tmp);
    if($db->sql_numrows($res_tmp)>0)
        $array_tmp=$db->sql_fetchrow($res_tmp);
    else
    	$array_tmp['origen_padre_id']=0;
    return $array_tmp['origen_padre_id'];    
}


function count_totales($db,$tablename_conn)
{
    $sql1="SELECT COUNT(*) FROM ".$tablename_conn." WHERE uid=0;";
    $res_sql1=$db->sql_query($sql1) or die($sql1);
    $total_no_asignados=$db->sql_fetchrow($res_sql1);
    echo"Numero de contactos no asignados:  $total_no_asignados[0]\n";
    
    $sql2="SELECT COUNT(*) FROM ".$tablename_conn." WHERE uid>0;";
    $res_sql2=$db->sql_query($sql2) or die($sql2);
    $total_asignados=$db->sql_fetchrow($res_sql2);
    echo"Numero de contactos Asignados:  $total_asignados[0]\n";
}
?>