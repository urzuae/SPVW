<?php

require_once("$_includesdir/mail.php");
global $db;

$filename = $argv[2]; //php.exe script nombre_de_script

$fh = fopen($filename, "r");
if (!$fh) die("Error, no se puede leer el archivo (tal vez sea demasiado grande)".$filename);
include("$_includesdir/select.php");
global $_entidades_federativas;

//los gids que se encuentran
$gid_founds = array();

$array_entidades = array("AGS"=>"AGUASCALIENTES",
                         "BC"=>"BAJA CALIFORNIA",
                         "BCS"=>"BAJA CALIFORNIA SUR",
                         "CMP"=>"CAMPECHE",
                         "CHS"=>"CHIAPAS",
                         "CHI"=>"CHIHUAHUA",
                         "COA"=>"COAHUILA",
                         "COL"=>"COLIMA",
                         "DF"=>"DISTRITO FEDERAL",
                         "DGO"=>"DURANGO",
                         "GTO"=>"GUANAJUATO",
                         "GRO"=>"GUERRERO",
                         "HGO"=>"HIDALGO",
                         "JAL"=>"JALISCO",
                         "MEX"=>"ESTADO DE MEXICO",
                         "MCH"=>"MICHOACAN",
                         "MOR"=>"MORELOS",
                         "NAY"=>"NAYARIT",
                         "NL"=>"NUEVO LEON",
                         "OAX"=>"OAXACA",
                         "PUE"=>"PUEBLA",
                         "QRO"=>"QUERETARO",
                         "QR"=>"QUINTANA ROO",
                         "SNL"=>"SAN LUIS POTOSI",
                         "SIN"=>"SINALOA",
                         "SON"=>"SONORA",
                         "TAB"=>"TABASCO",
                         "TMS"=>"TAMAULIPAS",
                         "TLX"=>"TLAXCALA",
                         "VER"=>"VERACRUZ",
                         "YUC"=>"YUCATAN",
                         "ZAC"=>"ZACATECAS");

//iniciar la lista para checar telefonos repetidos    
$telefonos = array();
$motivo_de_rechazo_1 = $motivo_de_rechazo_2 = $motivo_de_rechazo_3 = $motivo_de_rechazo_4 = 0;

$linea = 0;
$procesados = 0;
$insertados = 0;
$rechazados_concesionaria = array();

while(1){

    $linea++;    
    //leer normalmente
    $data = fgetcsv($fh);
    if (!$data) break;
    $procesados++;
    $data2 = array();
    foreach ($data as $undato){
      $data2[] = addslashes($undato);
    }
    list(
         $gid, $nombre_gid,
         $nombre,$nombre_2,
         $ap_paterno, $ap_materno, 
         $direccion,
         $delegacion,
         $colonia,
		 $cp,
         $entidad_id,
         $telefono,
         $telefono_fax,
         $correo,
		 $telefono_otro,
		 $telefono_movil)
         = $data2;
	 
	if($gid =="#")
      $gid = "";
	if($nombre=="#")
		$nombre=""; 
	if($nombre_2=="#")
		$nombre_2 == "";
	if($ap_paterno=="#")
		$ap_paterno="";
	if($ap_materno=="#")
		$ap_materno = "";
	if($direccion=="#")
		$direccion = "";
	if($delegacion == "#")
         $delegacion="";
	if($colonia=="#")
		$colonia ="";
	if($cp=="#")
		$cp="";
	if($entidad_id=="#")
		$entidad_id="";
	if($telefono=="#")
		$telefono="";
	if($telefono_fax=="#")
		$telefono_fax="";
	if($correo=="#")
		$correo=="";
	if($telefono_otro=="#")
		$telefono_otro="";
	if($telefono_movil=="#")
		$telefono_movil="";
    //rechazar si no tienen teléfono no email (puede tener nada mas uno)
    if (!$telefono && !$email)
    {
      $rechazados[] = $linea;//linea en la que lo botamos
      $rechazados_motivo[$linea] = "No tiene un medio de contacto";
      $motivo_de_rechazo_1++;
      continue;
    }
    if ($telefono) //si el teléfono no está vacio, correr filtros
    {
        //rechazar si está repetido el teléfono
        if (in_array($telefono, $telefonos) )
        {
          //buscamos el teléfono y vemos que nombre tiene dado de alta
          //si es el mismo nombre que tenemos en la db no agregarlo
          $sql = "SELECT contacto_id, nombre FROM crm_contactos WHERE tel_casa='$telefono'";//solo checo tel_casa por que es el único que agrego en este script
          $r = $db->sql_query($sql) or die($sql);          
          list($c_id,$nombre_aux) = $db->sql_fetchrow($r);
          if ($c_id)
          {
	          if ($nombre_aux == $nombre) //si es diferente el nombre asignarlo
	          {
	          	  $rechazados[] = $linea;//linea en la que lo botamos
		          $rechazados_motivo[$linea] = "CONTACTO REPETIDO (Tiene el mismo nombre: $nombre)";
		          $motivo_de_rechazo_2++;
		          continue;
	          }
          }
          
        }
        $telefonos[] = $telefono;//guardar para comparar
    
        //rechazar si el teléfono está repetido, para eso checamos en la db ahora
        $sql = "SELECT contacto_id, nombre FROM crm_contactos WHERE tel_casa='$telefono'";//solo checo tel_casa por que es elúnico que agrego en este script
        $r = $db->sql_query($sql) or die($sql);
        if ($db->sql_numrows($r) > 0) //encontró algo, rechazar
        {
          //buscamos el teléfono y vemos que modelo tiene dado de alta
          //si es el mismo modelo que tenemos en la db no agregarlo
          list($c_id,$nombre_aux) = $db->sql_fetchrow($r);
          if ($c_id){
	          if ($nombre_aux == $nombre) //si es diferente el modelo asignarlo
	          {
		          $rechazados[] = $linea;//linea en la que lo botamos
		          $rechazados_motivo[$linea] = "CONTACTO REPETIDO (Tiene el mismo nombre: $nombre)";
		          $motivo_de_rechazo_2++;
		          continue;
	          }
          }
        }
    }
    
	$entidad = $array_entidades[$entidad_id];
	//echo $array_entidades[$entidad_id];
	$entidad_federativa_id = array_search($entidad, $_entidades_federativas);
    if ($entidad_federativa_id) $entidad_federativa_id++;//por que el array es 0 aguascalientes
	
	//mï¿½s adelante hay mï¿½s tratamiendo de errores
	
	$nombre = strtoupper($nombre);
	
	//rechazar si el nombre está repetido, para eso checamos en la db ahora,
    //es en este momento por que hasta ahorita procesamos el nombre
    $sql = "SELECT contacto_id FROM crm_contactos WHERE nombre='$nombre' AND apellido_paterno='$apellido_paterno' AND apellido_materno='$apellido_materno'";//solo checo tel_casa por que es elúnico que agrego en este script
    $r = $db->sql_query($sql) or die($sql);
    if ($db->sql_numrows($r) > 0) //encontrï¿½ algo, rechazar
    {
      $rechazados[] = $linea;//linea en la que lo botamos
      $rechazados_motivo[$linea] = "CONTACTO REPETIDO (NOMBRE EN LA BD: $nombre $apellido_paterno $apellido_materno)";
      $motivo_de_rechazo_2++;
      continue;
    }
	//rechazar si la razon social está repetido, para eso checamos en la db ahora,
    //es en este momento por que hasta ahorita procesamos la razon social
    $sql = sprintf("SELECT contacto_id FROM crm_contactos WHERE razon_social='%s %s %s'",$nombre,$ap_paterno, $ap_materno);//solo checo tel_casa por que es elúnico que agrego en este script
    $r = $db->sql_query($sql) or die($sql);
    if ($db->sql_numrows($r) > 0) //encontrï¿½ algo, rechazar
    {
      $rechazados[] = $linea;//linea en la que lo botamos
      $rechazados_motivo[$linea] = "CONTACTO REPETIDO (Razon social en la DB: $nombre $apellido_paterno $apellido_materno)";
      $motivo_de_rechazo_2++;
      continue;
    }
	
    //siempre es insert por que no tenemos un id en el layout
    //el origen siempre es -1 = portal
    $sql = sprintf("INSERT INTO crm_contactos (
                                          gid, nombre, apellido_paterno, apellido_materno, domicilio,
										  poblacion, colonia, cp, entidad_id, tel_casa,
										  tel_otro, tel_movil, tel_oficina, email, fecha_importado,
										  razon_social, origen_id
										  
                                        ) VALUES (
										   '%s','%s','%s','%s','%s',
										   '%s','%s','%s','%s','%s',
										   '%s','%s','%s','%s',NOW(),
										   '%s','-19'
										   )",$gid,$nombre." ".strtoupper($nombre_2),$ap_paterno, $ap_materno,$direccion,
										     $delegacion, $colonia, $cp, $entidad_id,$telefono,
											 $telefono_otro, $telefono_movil, $telefono_fax,$correo,
											 $nombre." ".strtoupper($nombre_2)." ".$ap_paterno." ".$ap_materno
                                        );
    $insertados++;

	$r2 = $db->sql_query($sql) or die("Error<br>$sql<br>".print_r($db->sql_error()));


	$contacto_id = $db->sql_nextid($r2);
	//meterlo en la campaï¿½a correspondiente
    $sql = "SELECT c.campana_id FROM crm_campanas AS c, crm_campanas_groups AS g WHERE c.campana_id=g.campana_id AND g.gid='$gid' ORDER BY campana_id ASC LIMIT 1";//la primer campaï¿½a de la concesionaria
    $r2 = $db->sql_query($sql) or die("Error<br>$sql<br>".print_r($db->sql_error()));
    list($campana_id) = $db->sql_fetchrow($r2);
	$sql = "INSERT INTO crm_campanas_llamadas (contacto_id, campana_id)VALUES('$contacto_id', '$campana_id')";
	$db->sql_query($sql); 
    //guardar el log de asignacion 
    $sql = "INSERT INTO crm_contactos_asignacion_log (contacto_id, uid, from_uid, to_uid, from_gid, to_gid)VALUES('$contacto_id','0','0','0','0','$gid')";
    $db->sql_query($sql) or die("Error");
}
fclose($fh);//ya se leyo todo del archivo
$msg = "Registro de asignación de prospectos empresas\nHora y fecha: ".date("H:i d-m-Y");

$msg .= "\n";
if ($rechazados)
{
    $msg .= "Lista de lineas rechazadas\n";
    
    $msg .= "\n";
    
    $msg .= "Rechazados por tener teléfono inválido: $motivo_de_rechazo_1\n";
    $msg .= "Rechazados por ser un contacto repetido: $motivo_de_rechazo_2\n";
    $msg .= "Rechazados por presentar un modelo de unidad inválido: $motivo_de_rechazo_3\n";
    $msg .= "Rechazados por presentar una concesionaria inválida: $motivo_de_rechazo_4\n";
    
    $msg .= "\n";
    
    foreach ($rechazados AS $linea)
    {
      $motivo = $rechazados_motivo[$linea];
      $msg .= "Linea $linea: $motivo\n";
    }
    $msg .= "\n";

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
//mail("orangel@pcsmexico.com", "Carga de datos de VW ".date("Y-m-d"), $msg, $_email_headers);
//mail("gerardo.garcia@vw.com.mx", "Carga de datos de VW ".date("Y-m-d"), $msg, $_email_headers);
//mail("loucho.advanced@gmail.com", "Carga de datos de VW ".date("Y-m-d"), $msg, $_email_headers);
//mail("otoral@pcsmexico.com", "Carga de datos de VW ".date("Y-m-d"), $msg, $_email_headers);

?>