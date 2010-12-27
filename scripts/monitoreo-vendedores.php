<?php
require_once ("$_includesdir/mail.php");
global $db;

$arg = $argv[2]; //php.exe script nombre_de_script 2

$sql = "SELECT uid FROM users WHERE super='8' ORDER BY gid"; //vendedores  //and gid='2302'
$r = $db->sql_query($sql) or die($sql);
while(list($uid) = $db->sql_fetchrow($r))
{
    //obtener todos los prospectos de este usuario
    echo "$uid\n";

    $retrasos = 0;
    $cuantos_retrasos = 0;
    $max_t_asign_ini = 0;
    $max_t_ini_cierre = 0;
    $t_asign_ini_s = 0;
    $t_ini_cierre_s = 0; //sumatoria del tiempo que tarda en dar seguimiento hasta la hora de la venta
    $cuantos_t_asign_ini_s = 0;
    $cuantos_t_ini_cierre_s = 0;

    //primero calcularemos los tiempos de ini a cierre,
    //para esto se toma solo los que tengan cierre (las ventas, las cancelaciones están omitidas)
    $sql2 = "SELECT DISTINCT(c.contacto_id), UNIX_TIMESTAMP(timestamp), timestamp FROM crm_prospectos_ventas AS c WHERE c.uid='$uid' ORDER BY timestamp";
    $r2 = $db->sql_query($sql2) or die("Error al consultar campañas " . print_r($db->sql_error()));

    while(list($c, $ts_ultima_modificacion, $dt_ultima_modificacion) = htmlize($db->sql_fetchrow($r2)))
    {
    	//obtenemos el contacto_id y sobre ese vamos a buscar si la finalización fue venta y ya no tiene más seguimiento

    	//ahora queremos obtener el promedio del tiempo de que tardó en iniciar a darle seguimiento
    	//para eso buscamos el momento en el que se le asigno y el momento en el que se le dio seguimiento (el primer evento despues de la fecha de asignación)
    	$sql = "SELECT id FROM crm_campanas_llamadas_finalizadas WHERE contacto_id='$c'";
    	$r3 = $db->sql_query($sql) or die($sql);
    	//revisamos que este contacto esté en las llamadas finalizadas
    	if ($db->sql_numrows($r3) < 1)
    	{
    		//si no, buscamos en las no finalizadas
	    	$sql = "SELECT id FROM crm_campanas_llamadas WHERE contacto_id='$c'";
	    	$r3 = $db->sql_query($sql) or die($sql);
    	}
    	list($llamada_id) = $db->sql_fetchrow($r3);


    	$sql = "SELECT UNIX_TIMESTAMP(timestamp) FROM `crm_campanas_llamadas_eventos` WHERE `llamada_id` ='$llamada_id' ORDER BY timestamp ASC LIMIT 1";
        $r3 = $db->sql_query($sql) or die($sql);//AND timestamp >= '$dt_ultima_asignacion'
    	//lo siguiente sucede cuando una persona reporta una venta antes de generar algún compromiso
        if ($db->sql_numrows($r3) < 1)
        	$ts_primer_evento = $ts_ultima_modificacion;
        else
        	list($ts_primer_evento) = $db->sql_fetchrow($r3);

        //lo siguiente sucede cuando el navegador tarda en enviar los datos, puede haber una diferencia de segundos o hasta minutos si presionaron f5
        if ($ts_ultima_modificacion < $ts_primer_evento)
    		$segundos_ini_cierre= 0;
    	else
        	$segundos_ini_cierre = $ts_ultima_modificacion - $ts_primer_evento;


    	echo "$c#####$segundos_ini_cierre = $ts_ultima_modificacion - $ts_primer_evento\n";

    	//obtener el máximo
    	if ($segundos_ini_cierre > $max_t_ini_cierre)
    		$max_t_ini_cierre = $segundos_ini_cierre;

    	$cuantos_t_ini_cierre_s++;//aumentar el numero de ventas que contaremos
    	$t_ini_cierre_s += $segundos_ini_cierre;
        //se necesitan datos para el promedio desde la asignación al inicio, desde el inicio al cierre
        //máximos de asg al inicio, y del inicio al cierre
        //los retrasos acumulados
        //obtener el primer y último contacto
        //si tiene algún contacto, no calcula el tiempo desde la asignación
    }



    //calcular los retrasos acumulados
    $sql = "SELECT SUM(TIME_TO_SEC(TIMEDIFF(NOW(), l.fecha_cita))), COUNT(l.fecha_cita) FROM crm_campanas_llamadas AS l, crm_contactos AS c "
    		."WHERE c.uid='$uid' AND status_id = '-2' AND l.fecha_cita < NOW() AND l.contacto_id=c.contacto_id";
    $r4 = $db->sql_query($sql) or die($sql);
    list($retrasos_acumulados, $retrasos) = $db->sql_fetchrow($r4);


    // 3600s = 1h
    //ahora sacar los promedios de este vendedor (se dividen para que de segundos se pasen a horas)
    $kpi_retrasos = round($retrasos_acumulados / 3600); //sumatoria de horas de retraso
    $kpi_max_t_asign_ini = round($max_t_asign_ini / 3600);
    $kpi_max_t_ini_cierre = round($max_t_ini_cierre / 3600);
    if($cuantos_t_asign_ini_s)
        $kpi_t_asign_ini_s = round($t_asign_ini_s / $cuantos_t_asign_ini_s / 3600); ////promedio
    else
        $kpi_t_asign_ini_s = "0";
    if($cuantos_t_ini_cierre_s) //hay ventas
    {
        $x = round($t_ini_cierre_s / $cuantos_t_ini_cierre_s / 3600); //promedio

        $kpi_t_ini_cierre_s = $x;
    } else
        $kpi_t_ini_cierre_s = "0";

    //checar si ya tenemos este user en la tabla de monitoreo
    $sql = "SELECT uid FROM crm_monitoreo_vendedores WHERE uid='$uid'";
    $r4 = $db->sql_query($sql) or die($sql);
    if($db->sql_numrows($r4) > 0) //update
    {
        $sql = "UPDATE crm_monitoreo_vendedores SET prom_t_ini_cierre='$kpi_t_ini_cierre_s',max_t_ini_cierre='$kpi_max_t_ini_cierre', retrasos='$retrasos', retrasos_acumulados='$kpi_retrasos', ventas='$cuantos_t_ini_cierre_s' WHERE uid='$uid'";
    } //insert
	else
    {
        $sql = "INSERT INTO crm_monitoreo_vendedores (uid,prom_t_ini_cierre,max_t_ini_cierre,retrasos, retrasos_acumulados, ventas)" . "VALUES('$uid','$kpi_t_ini_cierre_s','$kpi_max_t_ini_cierre','$retrasos', '$kpi_retrasos', '$cuantos_t_ini_cierre_s')";
    }
    echo "$sql\n";
    $db->sql_query($sql) or die($sql);

}
echo $residuo;





$suma = 0;
$suma_uid = 0;
$contador_registros = 0;
$tiempo_mayor = 0;
$promedio = 0;

$timeini = mktime(date(H), date(i), date(s), date(m), date(d), date(Y));

//LISTAR VENDEDORES CON CONTACTOS ASIGNADOS
$sql = 'select to_uid, contacto_id, timestamp from users u inner join crm_contactos_asignacion_log l on u.uid = l.to_uid
where u.super = \'8\' order by to_uid ,contacto_id, timestamp';
$cs = $db->sql_query($sql);
while (list($to_uid, $contacto_id, $timestamp_asignacion) = $db->sql_fetchrow($cs))
{
    $segundos_asignacion = timestamp2seconds($timestamp_asignacion);

    //LISTAR SI LOS CONTACTOS ASIGNADOS HAN TENIDO SEGUIMIENTO O EVENTOS
    $sql2 = 'select e.timestamp from crm_campanas_llamadas c inner join crm_campanas_llamadas_eventos e
    on c.id = e.llamada_id where contacto_id = \''.$contacto_id.'\' and uid = \''.$to_uid.'\'';
    $cs2 = $db->sql_query($sql2);
    $result = $db->sql_affectedrows($cs2);
    
    //COMPROBAR SI TUVO O NO EVENTOS
    if($result > 0)
    {
        while(list($timestamp_eventos) = $db->sql_fetchrow($cs2))
        {
            if(!isset($uid))
                $uid = $to_uid;
                
            $segundos_eventos = timestamp2seconds($timestamp_eventos);
            if($segundos_asignacion < $segundos_eventos)
            {     
                if($uid == $to_uid)
                {
                    $suma = abs($segundos_asignacion - $segundos_eventos);
                    $suma_uid = $suma_uid + $suma;

                    //BUSCAR EL TIEMPO MAYOR
                    if($tiempo_mayor < $suma)
                        $tiempo_mayor = $suma;

                    //CONTADOR DE REGISTROS
                    ++$contador_registros;
                }
                else
                {
                    //INSERTAR O ACTUALIZAR REGISTRO EN LA BASE DE DATOS
                    $promedio = (($suma_uid/$contador_registros)/60)/60;
                    $tiempo_mayor = ($tiempo_mayor/60)/60;

                    $sql3 = 'select count(uid) from crm_monitoreo_vendedores where uid=\''.$uid.'\'';
                    $cs3 = $db->sql_query($sql3);
                    $rs3 = $db->sql_fetchrow($cs3);
                    if($rs3[0]>0)
                    {
                        $sql4 = 'update crm_monitoreo_vendedores set prom_t_asig_ini = \''.$promedio.'\', max_t_asig_ini = \''.$tiempo_mayor.'\' where uid = \''.$uid.'\'';
                        $db->sql_query($sql4);
                    }
                    else
                    {
                        $sql4 = 'insert into crm_monitoreo_vendedores(prom_t_asig_ini,max_t_asig_ini) values(\''.$promedio.'\',\''.$tiempo_mayor.'\')';
                        $db->sql_query($sql4);
                    }

                    echo "$uid     $suma_uid     $tiempo_mayor     $contador_registros     $promedio\n";

                    //CALCULAR NUEVO USER E INICIALIZAR VARIABLES
                    unset($suma);
                    $suma = abs($segundos_asignacion - $segundos_eventos);
                    unset($suma_uid);
                    $suma_uid = $suma_uid + $suma;
                    unset($uid);
                    $uid = $to_uid;
                    unset($contador_registros);
                    $contador_registros = 0;
                    ++$contador_registros;
                    unset($tiempo_mayor);
                    $tiempo_mayor = $suma;                    
                }  
                break;
            }         
        }
    }
}

$timefin = mktime(date(H), date(i), date(s), date(m), date(d), date(Y));
$tiempo = abs($timeini - $timefin);

function timestamp2seconds($timestamp)
{
    $timestamp = explode(' ',$timestamp);
    list($year, $month, $day) = explode('-',$timestamp[0]);
    list($hour, $minute, $second) = explode(':',$timestamp[1]);
    $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
    return $timestamp;
}
?>