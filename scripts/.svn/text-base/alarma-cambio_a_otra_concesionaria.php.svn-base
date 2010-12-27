<?php

require_once("$_includesdir/mail.php");
global $db;

//si la fecha en la que se importo, es mayor a 24 horas
$hace_8_horas = time() - (8 * 60 * 60);
$hace_24_horas = time() - (24 * 60 * 60);
$hace_48_horas = time() - (48 * 60 * 60);


//buscar a todos los contactos que ya se desasignaron 2 veces, es muy importante que esto corra unos minutos después del que quita a los vendedores por tener más de 48 horas de retraso

$sql = "SELECT";

$sql = "SELECT uid, gid, name, email FROM users WHERE super='0'";
$r = $db->sql_query($sql) or die("Error");
$cuantos = $db->sql_numrows($r);
while(list($uid, $gid, $name, $email) = $db->sql_fetchrow($r))
{
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
//         echo "tratado\n";
      }
      else //no está siendo tratado
      {
        //checar cuanto tiempo lleva sin ser tratado (ver a que hora se asigno)
        $sql  = "SELECT UNIX_TIMESTAMP(timestamp), timestamp FROM `crm_contactos_asignacion_log` WHERE contacto_id='$contacto_id' AND to_uid='$uid' ORDER BY timestamp DESC LIMIT 1"; //checar la última vez que se asigno
        $r4 = $db->sql_query($sql) or die("Error\n$sql");
        list($ts_asignacion, $ts) = $db->sql_fetchrow($r4);
        if ($ts_asignacion < $hace_48_horas)
        {
          $contactos_48_mas[$uid][] = "$contacto_id"; //echo "hace mas de 48 $ts\n";
        }
        elseif ($ts_asignacion < $hace_24_horas)
        {
          $contactos_24_mas[$uid][] = "$contacto_id"; //echo "hace mas de 24 $ts\n";
        }
        elseif ($ts_asignacion < $hace_8_horas)
        {
          $contactos_8_mas[$uid][] = "$contacto_id";//echo "hace mas de 8 $ts\n";
        }
        elseif ($ts_asignacion > $hace_8_horas)
        {
          $contactos_menos_de_8[$uid][] = "$contacto_id";//echo "hace menos de 8 $ts\n";
        }


        $sin_tocar++;
      }
      
    }
  }
}

//obtener emails de los gerentes CRM
$sql = "SELECT uid, gid, name, email FROM users WHERE super='1'";
$r = $db->sql_query($sql) or die("Error");
$cuantos = $db->sql_numrows($r);
while(list($uid, $gid, $name, $email) = $db->sql_fetchrow($r))
{
  $email_gte_crms[$gid] = $email;
}
//obtener emails de los gerentes de ventas
$sql = "SELECT uid, gid, name, email FROM users WHERE super='2'";
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
  if (count($contactos_menos_de_8[$uid]))
  {
    $msg = "$name, usted tiene ".count($contactos_menos_de_8[$uid])." prospectos asignados y sin tratar.";
    //enviar correo informativo
    mail("$email", "$msg", "$msg.", $_email_headers);
    echo $msg."\n";
  }
  if (count($contactos_8_mas))
  {
    $msg = "$name, usted tiene ".count($contactos_8_mas[$uid])." prospectos asignados y no les ha dado seguimiento desde hace más de 8 horas.";
    //enviar correo preventivo
    mail("$email", "$msg", "$msg.", $_email_headers);
    echo $msg."\n";
  }
  if (count($contactos_24_mas))
  {
    $msg = "$name, usted tiene ".count($contactos_24_mas[$uid])." prospectos asignados y no les ha dado seguimiento desde hace más de 24 horas. Es posible que se le retire";
    $msg_gte = "El vendedor $name tiene ".count($contactos_24_mas[$uid])." prospectos asignados y no los ha tratado desde hace más de 24 horas.";
    //enviar correo correctivo
    mail("$email", "$msg", "$msg.", $_email_headers);
    //obtener email del gerente
    mail("$email_gte_crm", "e-mail correctivo del vendedor $name", "$msg_gte.", $_email_headers);
    mail("$email_gte_vtas", "e-mail correctivo del vendedor $name", "$msg_gte.", $_email_headers);
    echo $msg."\n";
  }
  if (count($contactos_48_mas[$uid]))
  {
    $msg = "$name, usted tenía ".count($contactos_48_mas[$uid])." prospectos asignados y no les ha dado seguimiento desde hace más de 48 horas. Le han sido retirados y fue penalizado por esto con ".count($contactos_48_mas[$uid])." puntos.";
    $msg_gte = "Se le retiraron ".count($contactos_48_mas[$uid])." prospectos asignados al vendedor $name por no darles seguimiento por más de 48 horas y fue penalizado con ".count($contactos_48_mas[$uid])." puntos";
    $msg_gral .= "Se le retiraron ".count($contactos_48_mas[$uid])." prospectos asignados al vendedor $name de la concesionaria $gid por no darles seguimiento por más de 48 horas y fue penalizado con ".count($contactos_48_mas[$uid])." puntos";
    //enviar correo correctivo
    mail("$email", "$msg", "$msg.", $_email_headers);
    mail("$email_gte_crm", "e-mail de retiro de prospectos del vendedor $name", "$msg_gte.", $_email_headers);
    mail("$email_gte_vtas", "e-mail de retiro de prospectos del vendedor $name", "$msg_gte.", $_email_headers);
    
    echo $msg."\n";
    //ahora quitarselo y desasignar y penalizar
    foreach ($contactos_48_mas[$uid]  AS $contacto_id)
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
}
if ($msg_gral)
{
  mail("$_email_gerente_gral", "e-mail de retiro de prospectos de vendedores de concesionarias", "$msg_gte.", $_email_headers);
}

/**/
?>