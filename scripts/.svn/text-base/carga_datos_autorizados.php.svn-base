<?php
global $db, $submit, $del;
$filename = $argv[2];
if(!file_exists($filename))
{
    die("El archivo de carga [$filename] no existe\n");
}
$insertados1 = 0;
$prioridades = array();
$prioridades=Genera_Prioridades($db);
$submit = true;
if ($submit)
{
	require_once("$_includesdir/mail.php");
	include("$_includesdir/select.php");
	global $db;
	global $_entidades_federativas;
	$gid_founds = array();
	$telefonos = array();
	$fh = fopen($filename, "r");
	if (!$fh)
    {
	  die("Error, no se puede leer el archivo (tal vez sea demasiado grande)".$filename);
	  return;
	}
    $unidades  = Genera_Unidades($db);
    $groups    = Genera_Groups($db);
    $entidades = Genera_Entidades($db);
    $fuente_id = Regresa_Fuente($db);
    $satelites = Genera_Satelites($db);

	$motivo_de_rechazo_1 = $motivo_de_rechazo_2 = $motivo_de_rechazo_3 = $motivo_de_rechazo_4 = $motivo_de_rechazo_5 = $motivo_de_rechazo_6 = $motivo_de_rechazo_7 = 0;
	$linea = $total_de_registros_esperados = $procesados = $insertados = $origen_momento = $origen_portal = $origen_news = 0;
    $rechazados_concesionaria = array();
	$concesionarias = array();
	while(1)
    {
		$linea++;
        if($linea == 1)
        {
			$data = fgets($fh, 1000);//quitar la linea
			continue;
        }
        else
        {
			$data = fgetcsv($fh, 1000, ",");
			if (!$data)
                break;
			$procesados++;
			$data2 = array();
			foreach ($data as $undato)
            {
				$data2[] = addslashes($undato);
			}
			list($nombre,$apellidos,$email,$lada,$telefono,$modelo,$estado,$zona,$concesionaria,$fecha_registro,$fecha_autorizado,$codigo)= $data2;
            $entrada_bd=true;
            $modelo_id=Revisa_modelo($db,strtoupper(trim($modelo)));
            $modelo=$unidades[$modelo_id];

            echo"\n".$modelo."\n";
            $fecha_importado = str_replace("/","-",$fecha_registro);
            $fecha_autorizado= str_replace("/","-",$fecha_autorizado);
            //verifico la fecha de impotacion
            $fecha_importado = checa_formato($fecha_importado);
            if($fecha_importado=='0000-00-00 00:00:00')
            {
                $rechazados[] = $linea;//linea en la que lo botamos
            	$rechazados_motivo[$linea] = "LA FECHA DE IMPORTACION NO ESTA EN EL FORMATO DD/MM/AAAA HH:MM:SS";
                $motivo_de_rechazo_5++;
                $entrada_bd=false;
                continue;
                break;
            }
            else
            {
                // verifico que la fecha de importado no sea mayor que la actual
                $no_dias=Verifica_dias($db,$fecha_importado);
                if($no_dias < 0)
                {
                	$rechazados[] = $linea;//linea en la que lo botamos
        			$rechazados_motivo[$linea] = "LA FECHA DE IMPORTADO NO PUEDE SER MAYOR QUE LA FECHA ACTUAL";
            		$motivo_de_rechazo_5++;
                    $entrada_bd=false;
                	continue;
                }

            }
            // verifico la fecha de autorizacion

            $fecha_autorizado = checa_formato($fecha_autorizado);
            if($fecha_autorizado=='0000-00-00 00:00:00')
            {
                $rechazados[] = $linea;//linea en la que lo botamos
            	$rechazados_motivo[$linea] = "LA FECHA DE AUTORIZACION NO ESTA EN EL FORMATO DD/MM/AAAA HH:MM:SS";
                $motivo_de_rechazo_6++;
                $entrada_bd=false;
                continue;
                break;
            }
            else
            {
                // verifico que la fecha de autorizacion no sea mayor que la actual
                $no_dias=Verifica_dias($db,$fecha_autorizado);
                if($no_dias < 0)
                {
                    $rechazados[] = $linea;//linea en la que lo botamos
                    $rechazados_motivo[$linea] = "LA FECHA DE AUTORIZACION NO PUEDE SER MAYOR QUE LA FECHA ACTUAL";
                    $motivo_de_rechazo_6++;
                    $entrada_bd=false;
                    continue;
                }
            }
            
            if (!$telefono && !$email)
            {
				$rechazados[] = $linea;//linea en la que lo botamos
				$rechazados_motivo[$linea] = "TELEFONO VACIO";
				$motivo_de_rechazo_1++;
                $entrada_bd=false;
				continue;
			}
			if ($telefono)
			{
				if (in_array($telefono, $telefonos) )
                {
					$sql = "SELECT contacto_id FROM crm_contactos WHERE tel_casa='$telefono'";
					$r = $db->sql_query($sql) or die($sql);
					list($c_id) = $db->sql_fetchrow($r);
					if ($c_id)
                    {
						$sql = "SELECT modelo FROM crm_prospectos_unidades WHERE contacto_id='$contacto_id'";//solo checo tel_casa por que es elï¿?nico que agrego en este script
						$r = $db->sql_query($sql) or die($sql);
						list($modelo_) = $db->sql_fetchrow($r);
						if ($modelo_ == $modelo) //si es diferente el modelo asignarlo
						{
							$rechazados[] = $linea;//linea en la que lo botamos
							$rechazados_motivo[$linea] = "CONTACTO REPETIDO (TELEFONO EN ESTA CARGA: $telefono)";
							$motivo_de_rechazo_2++;
                            $entrada_bd=false;
							continue;
						}
					}
				}
				$telefonos[] = $telefono;//guar
				$sql = "SELECT contacto_id FROM crm_contactos WHERE tel_casa='$telefono'";//solo checo tel_casa por que es elï¿?nico que agrego en este script
				$r = $db->sql_query($sql) or die($sql);
				if ($db->sql_numrows($r) > 0) //encontrï¿? algo, rechazar
				{
					list($c_id) = $db->sql_fetchrow($r);
					if ($c_id)
                    {
						$sql = "SELECT modelo FROM crm_prospectos_unidades WHERE contacto_id='$contacto_id'";//solo checo tel_casa por que es elï¿?nico que agrego en este script
						$r = $db->sql_query($sql) or die($sql);
						list($modelo_) = $db->sql_fetchrow($r);
						if ($modelo_ == $modelo) //si es diferente el modelo asignarlo
						{
							$rechazados[] = $linea;//linea en la que lo botamos
							$rechazados_motivo[$linea] = "CONTACTO REPETIDO (TELEFONO EN LA BD: $telefono)";
							$motivo_de_rechazo_2++;
                            $entrada_bd=false;
							continue;
						}
					}
				}
				//rechazar si el telefono esta repetido, para eso checamos en la db ahora
				$sql = "SELECT contacto_id FROM crm_contactos_finalizados WHERE tel_casa='$telefono'";//solo checo tel_casa por que es elï¿?nico que agrego en este script
				$r = $db->sql_query($sql) or die($sql);
				if ($db->sql_numrows($r) > 0) //encontrï¿? algo, rechazar
				{
					$rechazados[] = $linea;//linea en la que lo botamos
					$rechazados_motivo[$linea] = "CONTACTO REPETIDO (TELEFONO EN LA BD FINALIZADA: $telefono)";
					$motivo_de_rechazo_2++;
                    $entrada_bd=false;
					continue;
					}
				}
				if ($modelo == "" || !in_array($modelo,$unidades)) //checar que el vehï¿?culo estï¿? en la lista
				{
					$rechazados[] = $linea;//linea en la que lo botamos
					$rechazados_motivo[$linea] = "MODELO INVALIDO: $modelo";
					$motivo_de_rechazo_3++;
                    $entrada_bd=false;
					continue;
				}
				//mas adelante hay mas tratamiendo de errores
				$tel_casa = "";
				if ($lada)
                    $tel_casa .= "($lada)";
				$tel_casa .= " $telefono";
                $nombre = strtoupper($nombre);
				$apellidos = strtoupper($apellidos);
				$espacio = strpos($apellidos, " ");
				if ($espacio){
				  $apellido_paterno = substr($apellidos, 0, $espacio);
				  $apellido_materno = substr($apellidos, $espacio + 1);
				}
				else
                {
				  $apellido_paterno = $apellidos;
                  $apellido_materno ='';
                }
                $estado=trim($estado);
                if(strtoupper($estado) == 'ESTADO DE MEXICO')
                    $estado='MEXICO';                   
                $entidad_federativa_id = (array_search(strtoupper($estado), $entidades)) +0 ;                
                if($entidad_federativa_id == 0)
                {
                	$rechazados[] = $linea;//linea en la que lo botamos
        			$rechazados_motivo[$linea] = "LA ENTIDAD FEDERATIVA NO EXISTE";
            		$motivo_de_rechazo_7++;
                    $entrada_bd=false;
                	continue;

                }

                //rechazar si el nombre estï¿? repetido, para eso checamos en la db ahora,
				//es en este momento por que hasta ahorita procesamos el nombre
				$sql = "SELECT contacto_id FROM crm_contactos WHERE nombre='$nombre' AND apellido_paterno='$apellido_paterno' AND apellido_materno='$apellido_materno'";//solo checo tel_casa por que es elï¿?nico que agrego en este script
				$r = $db->sql_query($sql) or die($sql);
				if ($db->sql_numrows($r) > 0) //encontrï¿? algo, rechazar
				{
					$rechazados[] = $linea;//linea en la que lo botamos
					$rechazados_motivo[$linea] = "CONTACTO REPETIDO (NOMBRE EN LA BD: $nombre $apellido_paterno $apellido_materno)";
					$motivo_de_rechazo_2++;
                    $entrada_bd=false;
					continue;
				}
                // checamos el codigo de campana en crm_contactos
                if(strlen($codigo)>10)
                {
    				$rechazados[] = $linea;//linea en la que lo botamos
        			$rechazados_motivo[$linea] = "LA LONGITUD DEL CODIGO DE CAMPAÑA NO PUEDE SER MAYOR DE 10 CARACTERES";
            		$motivo_de_rechazo_1++;
                    $entrada_bd=false;
                    continue;
                }
                //tambien checar en la tabla de finalizados
                //rechazar si el nombre esta repetido, para eso checamos en la db ahora,
                //es en este momento por que hasta ahorita procesamos el nombre
                $sql = "SELECT contacto_id FROM crm_contactos_finalizados WHERE nombre='$nombre' AND apellido_paterno='$apellido_paterno' AND apellido_materno='$apellido_materno'";//solo checo tel_casa por que es elï¿?nico que agrego en este script
                $r = $db->sql_query($sql) or die($sql);
                if ($db->sql_numrows($r) > 0) //encontrï¿? algo, rechazar
                {
                    $rechazados[] = $linea;//linea en la que lo botamos
                    $rechazados_motivo[$linea] = "CONTACTO REPETIDO (NOMBRE EN LA BD FINALIZADA: $nombre $apellido_paterno $apellido_materno)";
            		$motivo_de_rechazo_2++;
                    $entrada_bd=false;
                    continue;
                }
				//buscar la concesionaria
                
				$gid=array_search($concesionaria, $groups);
				if ($gid === FALSE)
                { //buscar en las satelites
			      foreach ($satelites AS $id => $aliases) //obtener el gid y el array de nombres
			      {
			      	if (array_search($concesionaria, $aliases) !== FALSE)//checar que esté en los aliases el nombre
			      	{
			      	  $gid = $id;
			      	  break;
			      	}
			      }
			    }
			    if ($gid === FALSE)
                {
			      $rechazados[] = $linea;//linea en la que lo botamos
			      $rechazados_motivo[$linea] = "CONCESIONARIA: ".($concesionaria)."    (NO PUEDE SER VACIA)";
			      $rechazados_concesionaria[] = implode("|",$data);
			      if (!in_array($concesionaria, $concesionarias))
			      $concesionarias[] = "$concesionaria";
			      $motivo_de_rechazo_4++;
                  $entrada_bd=false;
			      continue;
			    }
			    else
                {
			      if (!in_array($gid, $gid_founds))
                        $gid_founds[] = $gid;
			    }
			    $asignados_por_gid[$gid]++;
                $hoy=date("Y-m-d H:i:s");
                if($entrada_bd)
                {
                	$sql = "INSERT INTO crm_contactos (
                                          apellido_paterno,
                                          apellido_materno,
                                          nombre,
                                          tel_casa,
                                          email,
                                          fecha_importado,
                                          fecha_alta,
                                          gid,
                                          entidad_id,
                                          fecha_autorizado,codigo_campana,origen_id
                                        ) VALUES (
                                          '$apellido_paterno',
                                          '$apellido_materno',
                                          '$nombre',
                                          '$tel_casa',
                                          '$email',
                                          '$fecha_importado',
                                          '$hoy',
                                          '$gid',
                                          '$entidad_federativa_id',
                                          '$fecha_autorizado','$codigo','$fuente_id'
                                        )";
                        $r2 = $db->sql_query($sql) or die("Error<br>$sql<br>".print_r($db->sql_error()));
                        $contacto_id = $db->sql_nextid($r2);
                        //meterlo en la campania correspondiente
                        $sql = "SELECT c.campana_id FROM crm_campanas AS c, crm_campanas_groups AS g WHERE c.campana_id=g.campana_id AND g.gid='$gid' ORDER BY campana_id ASC LIMIT 1";//la primer campaï¿?a de la concesionaria
                        $r2 = $db->sql_query($sql) or die("Error<br>$sql<br>".print_r($db->sql_error()));
                        list($campana_id) = $db->sql_fetchrow($r2);
                        $sql = "INSERT INTO crm_campanas_llamadas (contacto_id, campana_id)VALUES('$contacto_id', '$campana_id')";
                        $db->sql_query($sql);
                        $sql = "INSERT INTO crm_contactos_asignacion_log (contacto_id, uid, from_uid, to_uid, from_gid, to_gid)VALUES('$contacto_id','0','0','0','0','$gid')";
                        $db->sql_query($sql) or die("Error");
                        //insertar modelo
                        $sql = "insert into crm_prospectos_unidades (contacto_id, modelo,modelo_id)VALUES('$contacto_id', '$modelo','$modelo_id')";
                        $r2 = $db->sql_query($sql) or die("Error<br>$sql<br>".print_r($db->sql_error()));
                        $insertados1++;
                        $insertados++;
                }
		}
	}
	fclose($fh);
	$msg = "\n\nRegistro de asignación de prospectos asignados y no asignados desde el Portal Web de VW\n\n".
            $total_de_registros_esperados." registros esperados\n\n".$procesados." registros procesados\n\n".
            $insertados1." \"asignados\" agregados\n\n";

	if ($asignados_por_gid)
    {
	  $msg .= "\n\n\nCantidad asignada por concesionaria\n
                Concesionaria";
	  list($ano,$dia,$mes) = explode("-",$fecha_registro);
	  $fecha_registro = sprintf("%s-%s-%s",$ano,$mes,$dia);
	  foreach ($asignados_por_gid AS $gid=>$cuantos)
      {
        $msg .= "\n".$gid."             ".$cuantos;
        $sql = sprintf("INSERT INTO carga_prospectos_log (cantidad, gid, fecha_contacto)
		        VALUES ('%s','%s','%s')",$cuantos,$gid,$fecha_registro);
		//$resultado = $db->sql_query($sql) or die("Error<br>$sql<br>".print_r($db->sql_error()));
	  }
	}
	
    $msg .= "\n\n\nCantidad asignada por campaña";
	if ($rechazados)
    {
	  $msg .= "\n\nLista de lineas rechazadas\n";


      $msg .= "\nRechazados por tener teléfono inválido          ".$motivo_de_rechazo_1."
               \nRechazados por ser un contacto repetido          ".$motivo_de_rechazo_2."
               \nRechazados por presentar un modelo de unidad inválido          ".$motivo_de_rechazo_3."
               \nRechazados por presentar una concesionaria inválida          ".$motivo_de_rechazo_4."
               \nRechazados por presentar una fecha de importado inválida          ".$motivo_de_rechazo_5."
               \nRechazados por presentar una fecha de autorización inválida          ".$motivo_de_rechazo_6."
               \nRechazados por presentar una Entidad Federativa inválida          ".$motivo_de_rechazo_7."
               \n\n\n\nLineas Rechazadas
               \nLinea          Motivo";

      foreach ($rechazados AS $linea)
      {
      	$motivo = $rechazados_motivo[$linea];
      	$msg .= "\n".$linea."          ".$motivo;
      }
	}
	if ($rechazados_concesionaria)
    {
	  $msg .= "\n\n\n\nConcesionarias no encontradas en la BD, requieren atención
               \nLinea";
	  foreach ($concesionarias AS $linea){
    	$linea = stripslashes($linea);
        $msg .= "\n".$linea;
	  }
	  $msg .= "\n\n\n\nA continuación se agrega el segmento de carga que se debe de volver a cargar";
	  $msg .= "\n\nTotal de registros: ".count($rechazados_concesionaria)."";

	  foreach ($rechazados_concesionaria AS $linea){
		$linea = stripslashes($linea);
		$msg .= "\n".$linea;
	  }
  }


  if(count($alertas_motivo) > 1)
  {
    foreach ($alertas_motivo as $alerta)
    {
        $alerta["msg"] = stripslashes($alerta["msg"]);
        $msg .= "\n".$alerta["msg"]."        En la linea ".$alerta["linea"];
    }
  }
  echo"\n\n****************************************";
  echo $msg;
  $_tabla_carga = $msg;
}
die("\n\nTotal de registros: $total_de_registros_esperados
Registros procesados: $procesados
Registros insertados: $insertados1\n\n");
  echo"\n\n****************************************";


function Genera_Prioridades($db){
    $sql = "SELECT prioridad_id, valor FROM crm_prioridades_contactos";
    $r = $db->sql_query($sql) or die($sql);
    while(list($prioridad_id, $prioridad_val) = $db->sql_fetchrow($r))
    {
        $prioridades[$prioridad_val] = $prioridad_id;
    }
    return $prioridades;
}

function horario_preferido($horario){
    $return = array();
    $return['manana'] = eregi('M',$horario) ? true : false;
    $return['tarde'] = eregi('T',$horario) ? true : false;
    $return['noche'] = eregi('N',$horario) ? true : false;
    return serialize($return);
}

function genera_telefono($lada,$telefono){
    $nuevo_telefono = '';
    if($lada != '') $nuevo_telefono .= "({$lada}) ";
    $nuevo_telefono .= $telefono;
    return $nuevo_telefono;
}

function Genera_Unidades($db)
{
    $sql = "SELECT unidad_id, nombre FROM crm_unidades";
    $r = $db->sql_query($sql) or die($sql);
    $unidades = array();
    while (list($id, $n) = $db->sql_fetchrow($r))
        $unidades[$id] = $n;
    return $unidades;
}
function Genera_Groups($db)
{
    $sql = "SELECT gid, name FROM groups";
    $r = $db->sql_query($sql) or die($sql);
    $groups = array();
    while (list($id, $n) = $db->sql_fetchrow($r))
        $groups[$id] = $id;
    return $groups;
}

function Genera_Satelites($db)
{
    $sql = "SELECT gid, name FROM groups_satelites";
    $r = $db->sql_query($sql) or die($sql);
    $satelites = array();
    while (list($id, $n) = $db->sql_fetchrow($r))
    {
        if (!is_array($satelites[$id]))
            $satelites[$id] = array();
        if (!in_array($n, $satelites[$id])) //checar que no esté ya
            $satelites[$id][] = $n;//metemos en el array dentro de este id el nombre
    }
    return $satelites;
}
function Genera_Entidades($db)
{
    $sql = "SELECT id_entidad, nombre FROM crm_entidades ORDER BY id_entidad";
    $r = $db->sql_query($sql) or die($sql);
    $entidades = array();
    while (list($id, $n) = $db->sql_fetchrow($r))
        $entidades[$id] = $n;
    return $entidades;
}
function Regresa_Fuente($db)
{
    $fuente=0;
    $sql="select fuente_id FROM crm_fuentes WHERE nombre='VWBank';";
    $res=$db->sql_query($sql) or die("Error:  ".$sql);
    if($db->sql_numrows($res) > 0)
    {
        $fuente=$db->sql_fetchfield(0,0,$res);
    }
    return $fuente;
}

function Verifica_dias($db,$fecha)
{
    $sql="SELECT TIMESTAMPDIFF(DAY , '".$fecha."', '".date("Y-m-d H:i:s")."' ) AS retrasos;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res) > 0)
    {
        $no_dias=($db->sql_fetchfield(0,0,$res)) + 0;
    }
    return  $no_dias;
}

function checa_formato($fecha)
{
    $regreso='0000-00-00 00:00:00';
    if(strlen($fecha) >= 10)
    {
        if(strlen($fecha)==10)
        {
            $tm_fecha=$fecha;
            $tm_hora ="09:00:00";
        }
        else
        {
            $array_fecha=explode(" ", $fecha);
            $tm_fecha=$array_fecha[0];
            $tm_hora =$array_fecha[1];
        }
        // reviso en que formato viene la fecha
        if(strlen($tm_fecha) == 10)
        {
            $posicion=strpos ($tm_fecha, "-");
            switch($posicion)
            {
                case 2:
                    $tmp=substr($tm_fecha,6,4).'-'.substr($tm_fecha,3,2).'-'.substr($tm_fecha,0,2);
                    break;
                case 4:
                    $tmp=$tm_fecha;
                    break;
                default:
                    $tmp='0000-00-00';
                    break;
            }
        }
        else
        {
            $tmp='0000-00-00';
            $tm_hora='00:00:00';
        }
        $regreso=$tmp." ".$tm_hora;
    }
    return $regreso;
}
function Revisa_modelo($db,$modelo)
{
    $id=0;
    $sql="SELECT unidad_id FROM crm_unidades where upper(nombre)='".$modelo."';";
    $res=$db->sql_query($sql) or die("Error en el query:  ".$sql);
    if($db->sql_numrows($res)>0)
        $id=$db->sql_fetchfield(0,0,$res);
    return $id;
}
?>