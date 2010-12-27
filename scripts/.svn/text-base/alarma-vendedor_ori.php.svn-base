<?php
require_once("$_includesdir/mail.php");
global $db;

$debugMsg = "";
//si la fecha en la que se importo, es mayor a 24 horas
$hace_8_horas = time() - (8 * 60 * 60);
$hace_24_horas = time() - (24 * 60 * 60);
$hace_48_horas = time() - (48 * 60 * 60);

//si es viernes o sábado, ampliar los rangos de tiempo
$dia_de_la_semana = date('w');
if ($dia_de_la_semana == '5' || $dia_de_la_semana == '6')
{
    $hace_24_horas = time() - (48 * 60 * 60);
    $hace_48_horas = time() - (72 * 60 * 60);
}
$uids = array();
//este anexo indica que solo se notifique a los vendedores de la fase piloto
//$sql_extra_gid_constraints = " AND gid IN ('0203','0810','1109','1201','1324','1327','1416','1805','1815','2015','2106','2302','2404','2607','3001','3102','3305','3603','3724','3728','1403','1411','1326','1330','3720')";

//buscar a todos los vendedores
//$sql = "SELECT uid, gid, name, email FROM users WHERE super='8'";
$sql = "SELECT uid, gid, name, email FROM users WHERE super='8' and uid='70119' and gid=2924";
$sql .= $sql_extra_gid_constraints;
$r = $db->sql_query($sql) or die("Error");
$cuantos = $db->sql_numrows($r);
echo"\nTotal de Vendedores :   ".$cuantos;
sleep(3);

while(list($uid, $gid, $name, $email) = $db->sql_fetchrow($r))
{
    $date=date("Y-m-d H:i:s");
    echo "=$uid\n";
    $no_segumiento=0;
    $no_asignacion=0;
    //primero checar si el vendedor tiene prospectos asignados
    $sql = "SELECT contacto_id FROM crm_contactos WHERE uid='$uid'";
    $r2 = $db->sql_query($sql) or die("Error");
    $cuantos_contactos_asignados = $db->sql_numrows($r2);
    if ($cuantos_contactos_asignados) //si no tiene nada no vale la pena hacer nada
    {
        $uids[] = $uid;
        $gids[$uid] = $gid;
        $names[$uid] = $name;
        $emails[$uid] = $email;
        while(list($contacto_id) = $db->sql_fetchrow($r2))
        {
            //checar si el contacto ya fue atendido (está en el log con esta uid)
            $sql = "SELECT contacto_id FROM `crm_campanas_llamadas_log` WHERE contacto_id='$contacto_id' AND uid='$uid'";
            $r3 = $db->sql_query($sql) or die("Error");
            if ($db->sql_numrows($r3) > 0) //ya está siendo tratado
            {
                //etapa de seguimiento
                $no_segumiento++;
                $sql = "SELECT status_id, UNIX_TIMESTAMP(fecha_cita),fecha_cita FROM `crm_campanas_llamadas` WHERE contacto_id='$contacto_id'";
                $r4 = $db->sql_query($sql) or die("Error");
                list($status_id, $ts_fecha_cita,$fecha_cita) = $db->sql_fetchrow($r4);
                $sql_tmp="select TIMESTAMPDIFF(HOUR,'".$fecha_cita."','".$date."') as retrasos;";
                $res_tmp=$db->sql_query($sql_tmp);
                $tmp_fecha=$db->sql_fetchfield(0,0, $res_tmp);
                //si está en 1, está en proceso, checar que no pase tiempo desde aquí
                //si está en -2, está pospuesta y se debe de considerar para esto la fecha de la cita para considerar el retraso
                if ($status_id == -2)
                {
                    if ($ts_fecha_cita < $hace_48_horas)
                    {
                        $contactos_seguimiento_mas_de_48[$uid][] = $contacto_id."  -  ".$fecha_cita."  -  ".$tmp_fecha;
                    }
                    elseif ($ts_fecha_cita < $hace_24_horas)
                    {
                        $contactos_seguimiento_mas_de_24[$uid][] = $contacto_id."  -  ".$fecha_cita."  -  ".$tmp_fecha;
                    }
                    elseif ($ts_fecha_cita < $hace_8_horas)
                    {
                        $contactos_seguimiento_mas_de_8[$uid][] = $contacto_id."  -  ".$fecha_cita."  -  ".$tmp_fecha;
                    }
                    elseif ($ts_fecha_cita > $hace_8_horas)
                    {
                        $contactos_seguimiento_menos_de_8[$uid][] = $contacto_id."  -  ".$fecha_cita."  -  ".$tmp_fecha;
                    }
                }
                elseif ($status_id == 1) //en proceso
                {
                    //checar cuando fue la última que se tocó
                    $sql  = "SELECT UNIX_TIMESTAMP(timestamp),timestamp FROM `crm_campanas_llamadas_log` WHERE contacto_id='$contacto_id' ORDER BY timestamp DESC LIMIT 1"; //checar la última vez que se asigno
                    $r4 = $db->sql_query($sql) or die("Error\n$sql");
                    list($ts_ultimo_contacto,$timestamp) = $db->sql_fetchrow($r4);
                    $sql_tmp="select TIMESTAMPDIFF(HOUR,'".$timestamp."','".$date."') as retrasos;";
                    $res_tmp=$db->sql_query($sql_tmp);
                    $tmp_fecha=$db->sql_fetchfield(0,0, $res_tmp);

                    if ($ts_ultimo_contacto < $hace_48_horas)
                    {
                        $contactos_seguimiento_mas_de_48[$uid][] = $contacto_id."  -  ".$timestamp."  -  ".$tmp_fecha;
                    }
                    elseif ($ts_ultimo_contacto < $hace_24_horas)
                    {
                        $contactos_seguimiento_mas_de_24[$uid][] = $contacto_id."  -  ".$timestamp."  -  ".$tmp_fecha;
                    }
                    elseif ($ts_ultimo_contacto < $hace_8_horas)
                    {
                        $contactos_seguimiento_mas_de_8[$uid][] = $contacto_id."  -  ".$timestamp."  -  ".$tmp_fecha;
                    }
                    elseif ($ts_ultimo_contacto > $hace_8_horas)
                    {
                        $contactos_seguimiento_menos_de_8[$uid][] = $contacto_id."  -  ".$timestamp."  -  ".$tmp_fecha;
                    }
                }
            }
            else //no está siendo tratado
            {
                $no_asignacion++;
                //checar cuanto tiempo lleva sin ser tratado (ver a que hora se asigno)
                $sql  = "SELECT UNIX_TIMESTAMP(timestamp), timestamp FROM `crm_contactos_asignacion_log` WHERE contacto_id='$contacto_id' AND to_uid='$uid' ORDER BY timestamp DESC LIMIT 1"; //checar la última vez que se asigno
                $r4 = $db->sql_query($sql) or die("Error\n$sql");
                list($ts_asignacion, $ts) = $db->sql_fetchrow($r4);
                $sql_tmp="select TIMESTAMPDIFF(HOUR,'".$ts."','".$date."') as retrasos;";
                $res_tmp=$db->sql_query($sql_tmp);
                $tmp_fecha=$db->sql_fetchfield(0,0, $res_tmp);

                if ($ts_asignacion < $hace_48_horas)
                {
                    $contactos_mas_de_48[$uid][] = $contacto_id."  -  ".$ts."  -  ".$tmp_fecha;
                }
                elseif ($ts_asignacion < $hace_24_horas)
                {
                    $contactos_mas_de_24[$uid][] = $contacto_id."  -  ".$ts."  -  ".$tmp_fecha;
                }
                elseif ($ts_asignacion < $hace_8_horas)
                {
                    $contactos_mas_de_8[$uid][] = $contacto_id."  -  ".$ts."  -  ".$tmp_fecha;
                }
                elseif ($ts_asignacion > $hace_8_horas)
                {
                    $contactos_menos_de_8[$uid][] = $contacto_id."  -  ".$ts."  -  ".$tmp_fecha;
                }
            }//tratado o no tratado
        }//while
    }
}

//obtener emails de los gerentes CRM
$sql = "SELECT uid, gid, name, email FROM users WHERE super='4'";
$sql .= $sql_extra_gid_constraints;
$r = $db->sql_query($sql) or die("Error");
$cuantos = $db->sql_numrows($r);
while(list($uid, $gid, $name, $email) = $db->sql_fetchrow($r))
{
    $email_gte_crms[$gid] = $email;
}
//obtener emails de los gerentes de ventas
$sql = "SELECT uid, gid, name, email FROM users WHERE super='6'";
$sql .= $sql_extra_gid_constraints;
$r = $db->sql_query($sql) or die("Error");
$cuantos = $db->sql_numrows($r);
while(list($uid, $gid, $name, $email) = $db->sql_fetchrow($r))
{
    $email_gte_vtass[$gid] = $email;
}


//informativo son menos de 8 horas
//preventivo es 8 horas
//correctivo son 24 horas
//retiro es a las 48 horas
//ahora ya tenemos los contactos
foreach ($uids AS $uid) //solo los que tienen contactos
{

    $email = $emails[$uid];
    if (!$email) continue;
    $name = $names[$uid];
    $gid = $gids[$uid];
    $email_gte_crm = $email_gte_crms[$gid];
    $email_gte_vtas = $email_gte_vtass[$gid];
    $email="lciencias@gmail.com";
    $email_gte_crm = "lciencias@gmail.com";
    $email_gte_vtas = "lciencias@gmail.com";
    $_email_gerente_gral= "lciencias@gmail.com";

    $cuantos_contactos_menos_de_8 = count($contactos_menos_de_8[$uid]);
    $cuantos_contactos_mas_de_8 = count($contactos_mas_de_8[$uid]);
    $cuantos_contactos_mas_de_24 = count($contactos_mas_de_24[$uid]);
    $cuantos_contactos_mas_de_48 = count($contactos_mas_de_48[$uid]);
    $cuantos_contactos_seguimiento_menos_de_8 = count($contactos_seguimiento_menos_de_8[$uid]);
    $cuantos_contactos_seguimiento_mas_de_8 = count($contactos_seguimiento_mas_de_8[$uid]);
    $cuantos_contactos_seguimiento_mas_de_24 = count($contactos_seguimiento_mas_de_24[$uid]);
    $cuantos_contactos_seguimiento_mas_de_48 = count($contactos_seguimiento_mas_de_48[$uid]);

    echo"\nProspectos en asignacion:   ".$no_asignacion;
    echo"\ncuantos_contactos_menos_de_8     ".$cuantos_contactos_menos_de_8;
    echo"\ncuantos_contactos_mas_de_8       ".$cuantos_contactos_mas_de_8;
    echo"\ncuantos_contactos_mas_de_24      ".$cuantos_contactos_mas_de_24;
    echo"\ncuantos_contactos_mas_de_48      ".$cuantos_contactos_mas_de_48;
    echo"\n\n\nProspectos en seguimiento:  ".$no_segumiento;
    echo"\ncuantos_contactos_seguimiento_menos_de_8   ".$cuantos_contactos_seguimiento_menos_de_8;
    echo"\ncuantos_contactos_seguimiento_mas_de_8     ".$cuantos_contactos_seguimiento_mas_de_8;
    echo"\ncuantos_contactos_seguimiento_mas_de_24    ".$cuantos_contactos_seguimiento_mas_de_24;
    echo"\ncuantos_contactos_seguimiento_mas_de_48    ".$cuantos_contactos_seguimiento_mas_de_48;
    echo"\n\n";
    echo"\ncontactos_seguimiento_menos_de_8:    ".print_r($contactos_seguimiento_menos_de_8[$uid])."\n\n";
    echo"\ncontactos_seguimiento_mas_de_8:      ".print_r($contactos_seguimiento_mas_de_8[$uid])."\n\n";
    echo"\ncontactos_seguimiento_mas_de_24:     ".print_r($contactos_seguimiento_mas_de_24[$uid])."\n\n";
    echo"\ncontactos_seguimiento_mas_de_48:     ".print_r($contactos_seguimiento_mas_de_48[$uid])."\n\n";

    sleep(3);

    //aquí son los que no están en seguimiento (los recién asignados)
    if ($cuantos_contactos_menos_de_8)
    {
        $msg = "$name, usted tiene $cuantos_contactos_menos_de_8 prospectos asignados y sin tratar.";
        //enviar correo informativo
        @mail("$email", "$msg", "$msg.", $_email_headers);
        echo $msg."\n";
    }
    if ($cuantos_contactos_mas_de_8)
    {
        $msg = "$name, usted tiene $cuantos_contactos_mas_de_8 prospectos asignados y no les ha dado seguimiento desde hace mas de 8 horas.";
        //enviar correo preventivo
        @mail("$email", "$msg", "$msg.", $_email_headers);
        echo $msg."\n";
    }
    if ($cuantos_contactos_mas_de_24)
    {
        $msg = "$name, usted tiene $cuantos_contactos_mas_de_24 prospectos asignados y no les ha dado seguimiento desde hace mas de 24 horas. Es posible que se le retire";
        $msg_gte = "El vendedor $name tiene $cuantos_contactos_mas_de_24 prospectos asignados y no los ha tratado desde hace mas de 24 horas";
        //enviar correo correctivo
        @mail("$email", "$msg", "$msg.", $_email_headers);
        //obtener email del gerente
        @mail("$email_gte_crm", "e-mail correctivo del vendedor $name", "$msg_gte.", $_email_headers);
        @mail("$email_gte_vtas", "e-mail correctivo del vendedor $name", "$msg_gte.", $_email_headers);
        echo $msg."\n";
    }
    if ($cuantos_contactos_mas_de_48)
    {
        $contactos_desasignar = array();
        $contactos_no_desasignar = array();
        $cuantos_no_desasignar = $cuantos_desasignar = 0;
        $msg_no_desasignar = $msg_desasignar = "";
        foreach ($contactos_mas_de_48[$uid]  AS $contacto_id)
        {
            //reviso que la fuente el valor de la fuente padre
            $sql = "SELECT a.origen_id,b.padre_id FROM crm_contactos a,crm_fuentes_arbol b WHERE a.contacto_id='$contacto_id' AND a.origen_id=b.hijo_id";
            $r2 = $db->sql_query($sql) or die($sql);
            list($origen_id,$padre_id) = $db->sql_fetchrow($r2);
            if ($padre_id == 1)
            {
                $contactos_no_desasignar[]= $contacto_id;
            }
            else
            {
                $contactos_desasignar[] = $contacto_id;
            }
        }

        $cuantos_desasignar = count($contactos_desasignar);
        $cuantos_no_desasignar = count($contactos_no_desasignar);
        if ($cuantos_desasignar)
        {
            $msg_desasignar = " $cuantos_desasignar prospectos le fueron retirados y fue penalizado con $cuantos_desasignar puntos.";
            foreach ($contactos_desasignar AS $contacto_id)    //ahora quitarselo y desasignar y penalizar
            {
                $sql = "UPDATE crm_contactos SET uid='0' WHERE contacto_id='$contacto_id'";
                $db->sql_query($sql) or die($sql);
                $sql = "UPDATE users SET score=score-1 WHERE uid='$uid'";
                $db->sql_query($sql) or die($sql);
                $sql = "INSERT INTO users_penalties (uid,uid_super,score) VALUES('$uid','0','-1')";
                $db->sql_query($sql) or die($sql);
                $sql = "INSERT INTO `crm_contactos_asignacion_log` (contacto_id,uid,from_uid,to_uid)VALUES('$contacto_id','0','$uid','0')";
                $db->sql_query($sql) or die($sql);
            }
        }
        if ($cuantos_no_desasignar)
        {
            $msg_no_desasignar = " $cuantos_no_desasignar prospectos se preservaron por ser personales.";
        }

        $msg = "$name $uid, usted tenía $cuantos_contactos_mas_de_48 prospectos sin tratar y no les había dado seguimiento desde hace mas de 48 horas.$msg_desasignar$msg_no_desasignar\n";
        $msg_gte = "El vendedor $name tenía $cuantos_contactos_mas_de_48 prospectos sin tratar y no les había dado seguimiento desde hace mas de 48 horas.$msg_desasignar$msg_no_desasignar\n";
        $msg_gral .= "El vendedor $name de la concesionaria tenía $cuantos_contactos_mas_de_48 prospectos sin tratar y no les había dado seguimiento desde hace mas de 48 horas.$msg_desasignar$msg_no_desasignar\n";
        //enviar correo correctivo
        @mail("$email", "$msg", "$msg.", $_email_headers);
        @mail("$email_gte_crm", "e-mail de retiro de prospectos en seguimiento del vendedor $name", "$msg_gte.", $_email_headers);
        @mail("$email_gte_vtas", "e-mail de retiro de prospectos en seguimiento del vendedor $name", "$msg_gte.", $_email_headers);

        echo $msg."\n";

    }
    //hasta aquí los que no están en seguimiento
    //los que están en seguimiento desde aquí
    if ($cuantos_contactos_seguimiento_menos_de_8)
    {
        $msg = "$name, usted tiene $cuantos_contactos_seguimiento_menos_de_8 prospectos para tratar.";
        //enviar correo informativo
        @mail("$email", "$msg", "$msg.", $_email_headers);
        echo $msg."\n";
    }
    if ($cuantos_contactos_seguimiento_mas_de_8)
    {
        $msg = "$name, usted tiene $cuantos_contactos_seguimiento_mas_de_8 prospectos en seguimiento y no les ha dado seguimiento desde hace mas de 8 horas.";
        //enviar correo preventivo
        @mail("$email", "$msg", "$msg.", $_email_headers);
        echo $msg."\n";
    }
    if ($cuantos_contactos_seguimiento_mas_de_24)
    {
        $msg = "$name, usted tiene $cuantos_contactos_seguimiento_mas_de_24 prospectos en seguimiento y no les ha dado seguimiento desde hace mas de 24 horas. Es posible que se le retire";
        $msg_gte = "El vendedor $name tiene $cuantos_contactos_seguimiento_mas_de_24 prospectos en seguimiento y no los ha tratado desde hace mas de 24 horas.";
        //enviar correo correctivo
        @mail("$email", "$msg", "$msg.", $_email_headers);
        //obtener email del gerente
        @mail("$email_gte_crm", "e-mail correctivo del vendedor $name", "$msg_gte.", $_email_headers);
        @mail("$email_gte_vtas", "e-mail correctivo del vendedor $name", "$msg_gte.", $_email_headers);
        echo $msg."\n";
    }
    if ($cuantos_contactos_seguimiento_mas_de_48)
    {
        $contactos_desasignar = array();
        $contactos_no_desasignar = array();
        $cuantos_no_desasignar = $cuantos_desasignar = 0;
        $msg_no_desasignar = $msg_desasignar = "";
        foreach ($contactos_seguimiento_mas_de_48[$uid]  AS $contacto_id)
        {
            $sql = "SELECT a.origen_id,b.padre_id FROM crm_contactos a,crm_fuentes_arbol b WHERE a.contacto_id='$contacto_id' AND a.origen_id=b.hijo_id";
            $r2 = $db->sql_query($sql) or die($sql);
            list($origen_id,$padre_id) = $db->sql_fetchrow($r2);
            if ($padre_id == 1)
            {
                $contactos_no_desasignar[]= $contacto_id;
            }
            else
            {
                $contactos_desasignar[] = $contacto_id;
            }
        }
        $cuantos_desasignar = count($contactos_desasignar);
        $cuantos_no_desasignar = count($contactos_no_desasignar);
        if ($cuantos_desasignar)
        {
            $msg_desasignar = " $cuantos_desasignar prospectos le fueron retirados y fue penalizado con $cuantos_desasignar puntos.";
            foreach ($contactos_desasignar AS $contacto_id)    //ahora quitarselo y desasignar y penalizar
            {
                $sql = "UPDATE crm_contactos SET uid='0' WHERE contacto_id='$contacto_id'";
                $db->sql_query($sql) or die($sql);
                $sql = "UPDATE users SET score=score-1 WHERE uid='$uid'";
                $db->sql_query($sql) or die($sql);
                $sql = "INSERT INTO users_penalties (uid,uid_super,score) VALUES('$uid','0','-1')";
                $db->sql_query($sql) or die($sql);
                $sql = "INSERT INTO `crm_contactos_asignacion_log` (contacto_id,uid,from_uid,to_uid)VALUES('$contacto_id','0','$uid','0')";
                $db->sql_query($sql) or die($sql);
            }
        }
        if ($contactos_no_desasignar)
        {
            $msg_no_desasignar = " $cuantos_no_desasignar prospectos se preservaron por ser personales.";
        }

        $msg = "$name $uid, usted tenía $cuantos_contactos_seguimiento_mas_de_48 prospectos en seguimiento y no les había dado seguimiento desde hace mas de 48 horas.$msg_desasignar$msg_no_desasignar\n";
        $msg_gte = "El vendedor $name tenía $cuantos_contactos_seguimiento_mas_de_48 prospectos en seguimiento y no les había dado seguimiento desde hace mas de 48 horas.$msg_desasignar$msg_no_desasignar\n";
        $msg_gral .= "El vendedor $name de la concesionaria tenía $cuantos_contactos_seguimiento_mas_de_48 prospectos en seguimiento y no les había dado seguimiento desde hace mas de 48 horas.$msg_desasignar$msg_no_desasignar\n";
        //enviar correo correctivo
        @mail("$email", "$msg", "$msg.", $_email_headers);
        @mail("$email_gte_crm", "e-mail de retiro de prospectos en seguimiento del vendedor $name", "$msg_gte.", $_email_headers);
        @mail("$email_gte_vtas", "e-mail de retiro de prospectos en seguimiento del vendedor $name", "$msg_gte.", $_email_headers);

        echo $msg."\n";

    }
}
if ($msg_gral)
{
    @mail("$_email_gerente_gral", "e-mail de retiro de prospectos de vendedores de concesionarias", "$msg_gral.", $_email_headers);
    @mail("orangel@pcsmexico.com", "e-mail de retiro de prospectos de vendedores de concesionarias", "$msg_gral.", $_email_headers);
}
else
{
    @mail("orangel@pcsmexico.com", "No hubo retiro de prospectos de vendedores", "", $_email_headers);
}
/**/
?>
