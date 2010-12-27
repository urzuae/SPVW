<?php

require_once("$_includesdir/mail.php");
global $db;

//este anexo indica que solo se notifique a los vendedores de la fase piloto
//$sql_extra_gid_constraints = " AND gid ='1008' || gid ='1112'";

//si la fecha en la que se importo, es mayor a 24 horas
$hace_24_horas = time() - (24 * 60 * 60);
$retrasos = array();
//primero checar los que tenemos asignados
//buscamos todos los groups
$sql  = "SELECT gid FROM groups WHERE 1$sql_extra_gid_constraints ORDER BY gid";
$r = $db->sql_query($sql) or die("Error");
$retrasos = array();
$sin_asignar = array();
while(list($gid) = $db->sql_fetchrow($r))
{
    $sql  = "SELECT c.contacto_id FROM crm_contactos AS c WHERE c.gid='$gid' AND c.uid='0'";
    $r2 = $db->sql_query($sql) or die("Error");

    while(list($contacto_id) = $db->sql_fetchrow($r2))
    {
        $sql  = "SELECT UNIX_TIMESTAMP(l.timestamp), to_uid FROM crm_contactos_asignacion_log AS l WHERE l.contacto_id='$contacto_id' ORDER BY l.timestamp DESC LIMIT 1"; //obtiene el último log de asignacion
        $r3 = $db->sql_query($sql) or die("Error");
        list($ts, $to_uid) = $db->sql_fetchrow($r3);
        //si no tiene to_uid entonces esta sin asignar
        if ($to_uid == '0')
        {
            $sin_asignar[$gid]++;
            if ($hace_24_horas > $ts)
                $retrasos[$gid]++; //entonces, hay 1 retraso, agregarlo a la concesionaria
        }
    }
}

foreach($sin_asignar AS $gid=>$cuantos)
{
  $msg = "Concesionaria $gid tiene $cuantos prospectos sin asignar.";
  if ($retrasos[$gid])
    $msg .= " {$retrasos[$gid]} de estos, tienen más de 24 horas de retraso.";
  $msg_gral .=  "$msg\n"; //esto se lo enviaré al admin
  //obtener gerentes de la concesionaria
  $sql  = "SELECT email FROM users WHERE gid='$gid' AND super >= '4' AND super <=6 ";
  
  $r = $db->sql_query($sql) or die("Error");
  //ahora mandar el correo de la concesionaria correspondiente
  while(list($email) = $db->sql_fetchrow($r))
  {
    if ($email)
    {
      //@mail("$email", "$msg", "$msg", $_email_headers);
    }
  }
  
  
}

if ($msg_gral)
{

  echo "$msg_gral\n";
  //ahora mandar al gerente general
  @mail("$_email_gerente_gral", "Concesionarias con prospectos sin asignar retrasados", "$msg_gral", $_email_headers);
  @mail("orangel@pcsmexico.com", "Concesionarias con prospectos sin asignar retrasados", "$msg_gral", $_email_headers);
  @mail("gcorzo@pcsmexico.com", "Concesionarias con prospectos sin asignar retrasados", "$msg_gral", $_email_headers);
}else
{
  @mail("orangel@pcsmexico.com", "No hubo prospectos sin asignar", "$msg_gte", $_email_headers);
  echo "No hubo prospectos sin asignar\n";
}
?>