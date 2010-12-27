<?php

require_once("$_includesdir/mail.php");
global $db;

$filename = $argv[2]; //php.exe script nombre_de_script


$fh = fopen($filename, "r");
if (!$fh) die("Error, no se puede leer el archivo (tal vez sea demasiado grande)".$filename);
include("$_includesdir/select.php");
global $_entidades_federativas;
//obtener la lista de veh�culos
$sql = "SELECT unidad_id, nombre FROM crm_unidades";
$r = $db->sql_query($sql) or die($sql);
$unidades = array();
while (list($id, $n) = $db->sql_fetchrow($r))
    $unidades[$id] = $n;
//obtener la lista de concesionarias
$sql = "SELECT gid, name FROM groups";
$r = $db->sql_query($sql) or die($sql);
$groups = array();
while (list($id, $n) = $db->sql_fetchrow($r))
    $groups[$id] = $n;
//obtener la lista de satelites y el gid que les corresponde
$sql = "SELECT gid, name FROM groups_satelites";
$r = $db->sql_query($sql) or die($sql);
$satelites = array();
while (list($id, $n) = $db->sql_fetchrow($r))
{
	if (!is_array($satelites[$id]))
		$satelites[$id] = array();
	if (!in_array($n, $satelites[$id])) //checar que no est� ya
    	$satelites[$id][] = $n;//metemos en el array dentro de este id el nombre
}

//los gids que se encuentran
$gid_founds = array();
//iniciar la lista para checar telefonos repetidos    
$telefonos = array();
$motivo_de_rechazo_1 = $motivo_de_rechazo_2 = $motivo_de_rechazo_3 = $motivo_de_rechazo_4 = 0;
//   $_edo_civil = array( 0=>"Otro", 1=>"Soltero", 2=>"Casado", 3=>"Divorciado", 4=>"Union Libre", 5=>"Viudo" );
$linea = 0;
$total_de_registros_esperados = 0;
$procesados = 0;
$insertados = 0;
$origen_momento = $origen_portal = $origen_news = $origen_GTIA6 = 0 ;
$rechazados_concesionaria = array();
$concesionarias = array();
while(1)
{

    $linea++;
    //las primeras 4 lineas son de control, la 1, 3 y 4 est�n vacias
    if ($linea == 1 || $linea == 3 || $linea == 4) 
    {
        $data = fgets($fh, 1000);//quitar la linea
        continue;
    }
    elseif ($linea == 2) //la tercer linea indica el n�mero de registros
    {
        $data = fgets($fh, 1000);
        if (!$data) break;
        
        list($texto, $total_de_registros_esperados) = explode(": ", $data);
        //quitarle el �ltimo caracter (salto de linea)
        $total_de_registros_esperados = intval($total_de_registros_esperados);
        
        continue;
    }
    elseif ($linea > 4)
    {
    
    //leer normalmente
    $data = fgetcsv($fh, 1000, "|");
    if (!$data) break;
    $procesados++;
    $data2 = array();
    foreach ($data as $undato)
    {
      $data2[] = addslashes($undato);
    }
    list(
         $nombre, $apellidos,
         $email,
         $lada, $telefono, 
         $modelo,
         $medio_contacto,
         $estado,
         $ciudad,
         $concesionaria,
         $origen,
         $fecha_de_registro)
         = $data2;
    //concesionaria nunca la checamos con sql, as� que le podemos quitar las slashes
    $concesionaria = stripslashes($concesionaria);
    //rechazar si no tienen tel�fono no email (puede tener nada mas uno)
    if (!$telefono && !$email)
    {
      $rechazados[] = $linea;//linea en la que lo botamos
      $rechazados_motivo[$linea] = "TELEFONO VACIO";
      $motivo_de_rechazo_1++;
      continue;
    }
    if ($telefono) //si el tel�fono no est� vacio, correr filtros
    {
        //rechazar si est� repetido el tel�fono
        if (in_array($telefono, $telefonos) )
        {
          //buscamos el tel�fono y vemos que modelo tiene dado de alta
          //si es el mismo modelo que tenemos en la db no agregarlo
          $sql = "SELECT contacto_id FROM crm_contactos WHERE tel_casa='$telefono'";//solo checo tel_casa por que es el�nico que agrego en este script
          $r = $db->sql_query($sql) or die($sql);          
          list($c_id) = $db->sql_fetchrow($r);
          if ($c_id)
          {
	          $sql = "SELECT modelo FROM crm_prospectos_unidades WHERE contacto_id='$contacto_id'";//solo checo tel_casa por que es el�nico que agrego en este script
	          $r = $db->sql_query($sql) or die($sql);          
	          list($modelo_) = $db->sql_fetchrow($r);
	          if ($modelo_ == $modelo) //si es diferente el modelo asignarlo
	          {
	          	  $rechazados[] = $linea;//linea en la que lo botamos
		          $rechazados_motivo[$linea] = "CONTACTO REPETIDO (TELEFONO EN ESTA CARGA: $telefono)";
		          $motivo_de_rechazo_2++;
		          continue;
	          }
          }
          
        }
        $telefonos[] = $telefono;//guardar para comparar
    
        //rechazar si el tel�fono est� repetido, para eso checamos en la db ahora
        $sql = "SELECT contacto_id FROM crm_contactos WHERE tel_casa='$telefono'";//solo checo tel_casa por que es el�nico que agrego en este script
        $r = $db->sql_query($sql) or die($sql);
        if ($db->sql_numrows($r) > 0) //encontr� algo, rechazar
        {
          //buscamos el tel�fono y vemos que modelo tiene dado de alta
          //si es el mismo modelo que tenemos en la db no agregarlo
          list($c_id) = $db->sql_fetchrow($r);
          if ($c_id)
          {
	          $sql = "SELECT modelo FROM crm_prospectos_unidades WHERE contacto_id='$contacto_id'";//solo checo tel_casa por que es el�nico que agrego en este script
	          $r = $db->sql_query($sql) or die($sql);          
	          list($modelo_) = $db->sql_fetchrow($r);
	          if ($modelo_ == $modelo) //si es diferente el modelo asignarlo
	          {
		
		          $rechazados[] = $linea;//linea en la que lo botamos
		          $rechazados_motivo[$linea] = "CONTACTO REPETIDO (TELEFONO EN LA BD: $telefono)";
		          $motivo_de_rechazo_2++;
		          continue;
	          }
          }
        }
        //rechazar si el tel�fono est� repetido, para eso checamos en la db ahora
        $sql = "SELECT contacto_id FROM crm_contactos_finalizados WHERE tel_casa='$telefono'";//solo checo tel_casa por que es el�nico que agrego en este script
        $r = $db->sql_query($sql) or die($sql);
        if ($db->sql_numrows($r) > 0) //encontr� algo, rechazar
        {
          $rechazados[] = $linea;//linea en la que lo botamos
          $rechazados_motivo[$linea] = "CONTACTO REPETIDO (TELEFONO EN LA BD FINALIZADA: $telefono)";
          $motivo_de_rechazo_2++;
          continue;
        }
    }
    if ($modelo == "" || !in_array($modelo, $unidades)) //checar que el veh�culo est� en la lista
    {
      $rechazados[] = $linea;//linea en la que lo botamos
      $rechazados_motivo[$linea] = "MODELO INVALIDO: $modelo";
      $motivo_de_rechazo_3++;
      continue;
    }
    
	//m�s adelante hay m�s tratamiendo de errores
	
    $tel_casa = "";
    if ($lada) $tel_casa .= "($lada)";
    $tel_casa .= " $telefono";
    $domicilio = $direccion;
    $persona_moral = 0;
    $poblacion = $ciudad;
	$nombre = strtoupper($nombre);
    $apellidos = strtoupper($apellidos); 
    $espacio = strpos($apellidos, " ");
    if ($espacio)
    {
      $apellido_paterno = substr($apellidos, 0, $espacio);
      $apellido_materno = substr($apellidos, $espacio + 1); 
    }
    else
    {
      $apellido_paterno = $apellidos;
      $apellido_materno ='';
    }
    $entidad_federativa_id = array_search($estado, $_entidades_federativas);
    if ($entidad_federativa_id) $entidad_federativa_id++;//por que el array es 0 aguascalientes


    //rechazar si el nombre est� repetido, para eso checamos en la db ahora,
    //es en este momento por que hasta ahorita procesamos el nombre
    $sql = "SELECT contacto_id FROM crm_contactos WHERE nombre='$nombre' AND apellido_paterno='$apellido_paterno' AND apellido_materno='$apellido_materno'";//solo checo tel_casa por que es el�nico que agrego en este script
    $r = $db->sql_query($sql) or die($sql);
    if ($db->sql_numrows($r) > 0) //encontr� algo, rechazar
    {
      $rechazados[] = $linea;//linea en la que lo botamos
      $rechazados_motivo[$linea] = "CONTACTO REPETIDO (NOMBRE EN LA BD: $nombre $apellido_paterno $apellido_materno)";
      $motivo_de_rechazo_2++;
      continue;
    }
    //tambien checar en la tabla de finalizados
    //rechazar si el nombre est� repetido, para eso checamos en la db ahora,
    //es en este momento por que hasta ahorita procesamos el nombre
    $sql = "SELECT contacto_id FROM crm_contactos_finalizados WHERE nombre='$nombre' AND apellido_paterno='$apellido_paterno' AND apellido_materno='$apellido_materno'";//solo checo tel_casa por que es el�nico que agrego en este script
    $r = $db->sql_query($sql) or die($sql);
    if ($db->sql_numrows($r) > 0) //encontr� algo, rechazar
    {
      $rechazados[] = $linea;//linea en la que lo botamos
      $rechazados_motivo[$linea] = "CONTACTO REPETIDO (NOMBRE EN LA BD FINALIZADA: $nombre $apellido_paterno $apellido_materno)";
      $motivo_de_rechazo_2++;
      continue;
    }    
    
    //buscar la concesionaria
    $gid = array_search($concesionaria, $groups);
    if ($gid === FALSE)
    { //buscar en las satelites

    	foreach ($satelites AS $id => $aliases) //obtener el gid y el array de nombres
    	{

    		if (array_search($concesionaria, $aliases) !== FALSE)//checar que est� en los aliases el nombre
    		{
    			$gid = $id;

    			break;
    		}
    	}		

    }
    if ($gid === FALSE)
    {
      $rechazados[] = $linea;//linea en la que lo botamos
      $rechazados_motivo[$linea] = "CONCESIONARIA: ".($concesionaria);
      $rechazados_concesionaria[] = implode("|",$data);
      if (!in_array($concesionaria, $concesionarias))
      	$concesionarias[] = "$concesionaria";
      $motivo_de_rechazo_4++;
      
      continue;
    }
    else
    {
        if (!in_array($gid, $gid_founds))$gid_founds[] = $gid;
    }
    $asignados_por_gid[$gid]++;
	
    if ($origen == "Momento")
    {
    	$origen_id = "-3";
    	$origen_momento++;
    }
    elseif ($origen == "newslvw")
    {
    	$origen_id = "-11";
    	$origen_news++;
    }
    elseif ($origen == "GTIA6")
    {
    	$origen_id = "-109";
    	$origen_GTIA6++;
    }
    else 
    {
    	$origen_id = "-1";
    	$origen_portal++;
    }
    //siempre es insert por que no tenemos un id en el layout
    //el origen siempre es -1 = portal
    $sql = "INSERT INTO crm_contactos (
                                          apellido_paterno,
                                          apellido_materno,
                                          nombre,
                                          tel_casa,
                                          email,
                                          persona_moral,
                                          domicilio,
                                          poblacion,
                                          fecha_importado,
                                          gid,
                                          entidad_id,
                                          origen_id
                                        ) VALUES (

                                          '$apellido_paterno',
                                          '$apellido_materno',
                                          '$nombre',
                                          '$tel_casa',
                                          '$email',
                                          '$persona_moral',
                                          '$domicilio',
                                          '$ciudad',
                                          NOW(),
                                          '$gid',
                                          '$entidad_federativa_id',
                                          '$origen_id'
                                        )";
    $insertados++;

	$r2 = $db->sql_query($sql) or die("Error<br>$sql<br>".print_r($db->sql_error()));


	$contacto_id = $db->sql_nextid($r2);
	  //meterlo en la campa�a correspondiente
    $sql = "SELECT c.campana_id FROM crm_campanas AS c, crm_campanas_groups AS g WHERE c.campana_id=g.campana_id AND g.gid='$gid' ORDER BY campana_id ASC LIMIT 1";//la primer campa�a de la concesionaria
    $r2 = $db->sql_query($sql) or die("Error<br>$sql<br>".print_r($db->sql_error()));
    list($campana_id) = $db->sql_fetchrow($r2);
	$sql = "INSERT INTO crm_campanas_llamadas (contacto_id, campana_id)VALUES('$contacto_id', '$campana_id')";
	$db->sql_query($sql); 
    //guardar el log de asignacion 
    $sql = "INSERT INTO crm_contactos_asignacion_log (contacto_id, uid, from_uid, to_uid, from_gid, to_gid)VALUES('$contacto_id','0','0','0','0','$gid')";
    $db->sql_query($sql) or die("Error");

    //insertar modelo
	$sql = "insert into crm_prospectos_unidades (contacto_id, modelo)VALUES('$contacto_id', '$modelo')";
	  $r2 = $db->sql_query($sql) or die("Error<br>$sql<br>".print_r($db->sql_error()));
  }
}
fclose($fh);//ya se leyo todo del archivo
$msg = "Registro de asignaci�n de prospectos desde el Portal Web de VW\nHora y fecha: ".date("H:i d-m-Y");
$msg .= "\n$total_de_registros_esperados registros esperados\n$procesados registros procesados.\n$insertados agregados.\n";

$msg .= "\n";
if ($asignados_por_gid) 
{
    $msg .= "Cantidad asignada por concesionaria\n";
    foreach ($asignados_por_gid AS $gid=>$cuantos)
    {
      $msg .= "Concesionaria $gid: $cuantos\n";
    }
}

$msg .= "\nCantidad asignada por campa�a\n";
$msg .= "Momento: $origen_momento\n";
$msg .= "NewslVW: $origen_news\n";
$msg .= "Portal: $origen_portal\n";
$msg .= "GTIA6: $origen_GTIA6\n";
$msg .= "\n";

if ($rechazados)
{
    $msg .= "Lista de lineas rechazadas\n";
    
    $msg .= "\n";
    
    $msg .= "Rechazados por tener tel�fono inv�lido: $motivo_de_rechazo_1\n";
    $msg .= "Rechazados por ser un contacto repetido: $motivo_de_rechazo_2\n";
    $msg .= "Rechazados por presentar un modelo de unidad inv�lido: $motivo_de_rechazo_3\n";
    $msg .= "Rechazados por presentar una concesionaria inv�lida: $motivo_de_rechazo_4\n";
    
    $msg .= "\n";
    
    foreach ($rechazados AS $linea)
    {
      $motivo = $rechazados_motivo[$linea];
      $msg .= "Linea $linea: $motivo\n";
    }
    $msg .= "\n";

}    

if ($rechazados_concesionaria)
{
	$msg .= "Concesionarias no encontradas en la BD, requieren atenci�n:\n\n";
	foreach ($concesionarias AS $linea)
    {
    	$linea = stripslashes($linea);
        $msg .= "$linea\n";
    }	
	$msg .= "\nA continuaci�n se agrega el segmento de carga que se debe de volver a cargar:\n\n";
    $msg .= "===============================================================\n**********************";
	$msg .= "\nTotal de registros: ".count($rechazados_concesionaria)."\n\n\n";
	foreach ($rechazados_concesionaria AS $linea)
	{
		$linea = stripslashes($linea);
		$msg .= "$linea\n";
	}
	$msg .= "===============================================================";
}
$filename = 'carga_portal-'.date('YmdHi').'.log';



if (!$handle = fopen($filename, 'w+')) {
     echo "Error al abrir ($filename)\n$msg";
     exit;
}

// Write $somecontent to our opened file.
if (fwrite($handle, $msg) === FALSE) {
    echo "Error al escribir ($filename)\n$msg";
    exit;
}

fclose($handle);

//mail("gcorzo@pcsmexico.com", "Carga de datos de VW", $msg, $_email_headers);
//mail("guadalupebej@gmail.com", "Carga de datos de VW", $msg, $_email_headers);
mail("orangel@pcsmexico.com", "Carga de datos de VW ".date("Y-m-d"), $msg, $_email_headers);
mail("gerardo.garcia@vw.com.mx", "Carga de datos de VW ".date("Y-m-d"), $msg, $_email_headers);
mail("loucho.advanced@gmail.com", "Carga de datos de VW ".date("Y-m-d"), $msg, $_email_headers);
mail("otoral@pcsmexico.com", "Carga de datos de VW ".date("Y-m-d"), $msg, $_email_headers);

?>