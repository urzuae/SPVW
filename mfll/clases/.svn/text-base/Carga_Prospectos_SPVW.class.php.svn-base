<?php
class Carga_Prospectos_SPVW
{
    private $fh;
    private $conn;
    private $data;
    private $buffer_errores;
    private $buffer_insertados;
    private $procesados;
    private $insertados;
    private $vehiculos_insertados;
    private $buffer;
    private $array_fases;
    private $array_medios;
    private $array_ciclo_venta;
    private $array_prioridad;

    function __construct($fh,$conn)
    {
        $this->array_ciclo_venta=array(1 => '01',2 => '04',3 => '05');
        $this->array_prioridad=array(1 => '1',2 => '3',3 => '5');
        $this->array_fases = array('Registra' => 1,'Maneja' => 2,'Firma y Llevatelo' => 3);
        $this->array_medios= array('Triptico' => 1,'Espectacular' => 2,'Pasaba por aqui' => 6,'Periodico' => 5,'Radio' => 4,'Television' => 3);
        $this->buffer='';
        $this->procesados=0;
        $this->insertados=0;
        $this->vehiculos_insertados=0;
        $this->buffer_insertados='';
        $this->buffer_errores='';
        $this->conn=$conn;
        $this->fh=$fh;
        $this->data=array();
        $this->Procesa_Archivo_Prospectos_SPVW();
        $this->Procesa_Informe_SPVW();
        $this->Envia_Email_Aviso_SPVW();
    }

     /**
     * Funcion que se encarga de procesar e insertar los registros
     */
    function Procesa_Archivo_Prospectos_SPVW()
    {
        $linea=0;
        $etapa_ciclo_vta='';
        $prioridad=0;
        while($this->data = fgetcsv($this->fh, 1000, "|"))
        {
            $contacto_id=0;
            $fase_id  = 0;
            $fuente_id= 0;
            $unidad_id= 0;
            $etapa_ciclo_vta='';
            $prioridad=0;
            $linea++;
            if (!($iii++)) continue;
            $this->procesados++;
            if(count($this->data) == 15 )
            {
                $data2 = array();
                foreach($this->data as $undato)
                {
                    $data2[] = addslashes($undato);
                }
                list($gid, $uid,$fase_nombre, $nombre, $ap_pat, $ap_mat,$tel_casa,$tel_ofi, $tel_mov, $tel_otro,
                $email,$medio_entero,$evento_nombre,$unidad, $tipo_pago_nombre)= $data2;
                // Reviso que los datos obligatorios no sean vacios
                if (!$gid)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  EL ID DE LA CONCESIONARIA NO PUEDE SER VACIO\n";
                    continue;
                }
                if (!$uid)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  EL ID DEL VENDEDOR NO PUEDE SER VACIO\n";
                    continue;
                }
                if (!$fase_nombre)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  EL NOMBRE DE LA FASE NO PUEDE SER VACIO\n";
                    continue;
                }
                if (!$nombre)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  EL NOMBRE DEL PROSPECTO NO PUEDE SER VACIO\n";
                    continue;
                }
                if (!$ap_pat)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  EL APELLIDO PATERNO DEL PROSPECTO NO PUEDE SER VACIO\n";
                    continue;
                }
                if (!$tel_casa && !$tel_ofi && !$tel_mov && !$tel_otro)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  ES NECESARIO AL MENOS UN TELEFONO\n";
                    continue;
                }
                // Quito posibles acentos en los parametros de entrada
                $nombre=$this->elimina_acentos(trim($nombre));
                $ap_pat=$this->elimina_acentos(trim($ap_pat));
                $ap_mat=$this->elimina_acentos(trim($ap_mat));

                $fase_nombre=$this->elimina_acentos(trim($fase_nombre));
                $evento_nombre=$this->elimina_acentos(trim($evento_nombre));
                $unidad=$this->elimina_acentos(trim($unidad));
                $medio_entero=$this->elimina_acentos(trim($medio_entero));

                // Regreso la fase y el tipo del medio en que se entero
                $fase_id =( $this->array_fases[$fase_nombre] + 0);
                $medio_id=( $this->array_medios[$medio_entero] + 0);

                // si se encuntra la fase
                if($fase_id > 0)
                {
                    $etapa_ciclo_vta=$this->array_ciclo_venta[$fase_id];
                    $prioridad =$this->array_prioridad[$fase_id];
                }
                // recupero la fuente_id y el modelo del vehiculo
                $fuente_id= $this->Revisa_tablas('fuente_id','nombre','crm_fuentes',$evento_nombre);
                $unidad_id= $this->Revisa_tablas('unidad_id','nombre','crm_unidades',$unidad);
                if($fuente_id == 0){
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." EL NOMBRE DE LA FUENTE ES INCORRECTO\n";
                    continue;
                }
                if($unidad_id == 0){
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." EL NOMBRE DEL MODELO ES INCORRECTO\n";
                    continue;
                }

                //Convierto todo a mayusculas
                $nombre=strtoupper(strtolower($nombre));
                $ap_pat=strtoupper(strtolower($ap_pat));
                $ap_mat=strtoupper(strtolower($ap_mat));
                $contacto_id=( $this->Revisa_Prospecto_BD($nombre, $ap_pat, $ap_mat) + 0);
                if( $contacto_id > 0)
                {
                    if($this->Revisa_Prospecto_Vehiculo($contacto_id,$unidad_id) == 0)
                    {
                        $this->Inserta_Prospecto_Unidad($contacto_id,$unidad,$unidad_id);
                    }
                    else
                    {
                        $this->buffer_errores.= "Linea:  ".$linea."    El CONTACTO:".$nombre. " ".$ap_pat." ".$ap_mat." YA EXISTE EN LA BASE DE DATOS\n";
                     //   continue;
                    }
                }
                else
                {
                    // Insertamos el prospecto en la base de datos
                    $sql = "INSERT INTO crm_contactos (gid, uid, nombre, apellido_paterno,apellido_materno,
                        tel_casa,tel_oficina, tel_movil, tel_otro,email,origen_id,no_contactar,fecha_importado,	prioridad) VALUES ('$gid','$uid','$nombre',
                        '$ap_pat','$ap_mat','$tel_casa','$tel_ofi','$tel_mov','$tel_otro','$email','$fuente_id','1',NOW(),'$prioridad');";
                    if(mysql_query($sql) or die("Error al insertar el prospecto.  ".$sql))
                    {
                        $contacto_id = mysql_insert_id();
                        $campana_id=$gid.$etapa_ciclo_vta;
                        $sql = "INSERT INTO crm_campanas_llamadas (contacto_id, campana_id)VALUES('$contacto_id', '$campana_id')";
                        mysql_query($sql) or die("Error al insertar el prospecto en campanas   ".$sql);

                        //guardar el log de asignacion
                        $sql = "INSERT INTO crm_contactos_asignacion_log (contacto_id, uid, from_uid, to_uid, from_gid, to_gid)VALUES('$contacto_id','0','0','$uid','0','$gid')";
                        mysql_query($sql) or die("Error al insertar el prospecto en logs   ".$sql);
                        //insertar modelo
                        $this->Inserta_Prospecto_Unidad($contacto_id,$unidad,$unidad_id);

                        // revisar si la fuente ya se asigno a estas fuentes
                        $sql="SELECT gid,fuente_id FROM crm_groups_fuentes WHERE gid=".$gid." AND fuente_id=".$fuente_id.";";
                        $res=mysql_query($sql) or die("Error en la consulta de groups fuentes  ".$sql);
                        if(mysql_num_rows($res) == 0)
                        {
                            mysql_query("INSERT INTO crm_groups_fuentes (gid,fuente_id) VALUES ('$gid','$fuente_id');") or die("Error en el inser:  ");
                        }
                        $this->buffer_insertados.="Prospecto: ".$nombre." ".$ap_pat." ".$ap_mat." se registro en la base de datos";
                        $this->insertados++;
                    }
                }
            }
            else
            {
                $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  LOS PARAMETROS DE ENTRADA SON MAS DE LOS REQUERIDOS\n";
                continue;
            }
        }
    }

    /*
    *  Funcion encargada de buscar en la base de datos el nombre del prospecto
    */
    function Revisa_Prospecto_BD($nombre, $ap_pat, $ap_mat)
    {
        $reg=0;
        $sql= "SELECT contacto_id,nombre, apellido_paterno, apellido_materno FROM crm_contactos WHERE upper(nombre)='$nombre' AND UPPER(apellido_paterno)='$ap_pat' AND UPPER(apellido_materno)='$ap_mat' LIMIT 1;";
        $res=mysql_query($sql) or die ("Error al verificar datos del contacto ".$sql);
        if(mysql_num_rows($res) > 0)
        {
            $reg=mysql_result($res,0,0);
        }
        return $reg;
    }

    function Inserta_Prospecto_Unidad($contacto_id,$unidad,$unidad_id)
    {
        $sql = "insert into crm_prospectos_unidades (contacto_id,modelo,modelo_id) VALUES ('$contacto_id', '$unidad','$unidad_id')";
        if(mysql_query($sql) or die("Error al insertar el prospecto en unidades   ".$sql))
            $this->vehiculos_insertados++;
    }
    function Revisa_Prospecto_Vehiculo($contacto_id,$unidad_id)
    {
        $reg=0;
        $sql_mod="SELECT modelo_id FROM crm_prospectos_unidades WHERE contacto_id=".$contacto_id."  AND  modelo_id=".$unidad_id.";";
        $res_mod=mysql_query($sql_mod) or die ("Error al verificar datos del contacto y vehiculo".$sql_mod);
        if(mysql_num_rows($res_mod) > 0)
        {
            $reg=1;
        }
        return $reg;
    }
    /*
    *  Funcion encargada de buscar en distintas tablas los valores de los catalogos
    */
    function Revisa_tablas($campo_regresa,$campo_busqueda,$tabla,$valor)
    {
        $reg=0;
        $sql="SELECT ".$campo_regresa." FROM ".$tabla." WHERE ".$campo_busqueda."='".$valor."';";
        $res=mysql_query($sql,$this->conn);
        if(mysql_num_rows($res) > 0)
        {
            $reg=mysql_result($res,0,0);
        }
        return $reg;
    }

    /**
    * Función que se encarga de quitar los acentos a la cadena pasada
    * @param <string> $cadena
    * @return <string>
    */ 
    function elimina_acentos($cadena)
    {
        $tofind = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
        $replac = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
        return(strtr($cadena,$tofind,$replac));
    }

    /**
    *  Funcion que se encarga de crear el informe final del procesamiento
    */
    function Procesa_Informe_SPVW()
    {
        $this->buffer="Prospectos Procesados:   ".$this->procesados."\n";
        if(strlen($this->buffer_errores) > 0)
        {
            $this->buffer.="Errores:   ".$this->buffer_errores."\n";
        }
        if(strlen($this->buffer_insertados) > 0)
        {
            $this->buffer.="Resumen:   ".$this->buffer_insertados."\n";
        }
        $this->buffer.="Prospectos Insertados:   ".$this->insertados."\n";
        $this->buffer.="Vehículos  Insertados:   ".$this->vehiculos_insertados."\n";
    }

    /**
     * Función que envia el informe al email de soporte
     */
    function Envia_Email_Aviso_SPVW()
    {
        $_email_headers  = 'MIME-Version: 1.0' . "\r\n";
        $_email_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $_email_from = "noreply@pcsmexico.com";
        $_email_headers .= "from:$_email_from\r\n";
        mail("lahernandez@pcsmexico.com", "Carga de datos de MFLL (Prospectos para Reportes MFLL) ".date("Y-m-d"), $this->buffer, $_email_headers);
    }

    /*
     * Función que da salida al informe del procesamiento
     */
    function Obten_Resultados()
    {
        return $this->buffer;
    }

}
?>
