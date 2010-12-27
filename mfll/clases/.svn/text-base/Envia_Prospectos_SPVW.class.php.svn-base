<?php
class Envia_Prospectos_SPVW
{
    private $conn;
    private $data;
    private $data_unidades;
    private $data_tpagos;
    private $buffer_errores;
    private $buffer_insertados;
    private $procesados;
    private $insertados;
    private $vehiculos_insertados;
    private $no_cargados;
    private $buffer;
    private $buffer_email;
    private $array_fases;
    private $array_medios;
    private $array_ciclo_venta;
    private $array_prioridad;
    private $file;
    private $buffer_modelo_insertados;

    function __construct($array,$array_unidades,$array_tpagos)
    {
        // Me conecto a la base de datos de prospeccion
        $_dbhost = 'www.prospeccionvw.com';
        $_dbuname = 'mfll';
        $_dbpass  = 'mfll_pwd';
        $_dbname = 'crm_prospectos';


/*      #Claves para ejectuar el script de manera local
        $_dbhost = 'localhost';
        $_dbuname = 'root';
        $_dbpass  = 'mysql_pwd!';
        $_dbname = 'crm_prospectos';
*/
        
        $this->file="prospectos_mfll_a_prospeccion".date("Y-m-d H:i:s").".csv";
        $this->conn = mysql_connect($_dbhost,$_dbuname,$_dbpass) or die ("error");
        mysql_select_db($_dbname,$this->conn) or die("Error de Base de Datos");
        $this->data=$array;
        $this->data_unidades=$array_unidades;
        $this->data_tpagos=$array_tpagos;

        $this->array_ciclo_venta=array(1 => '01',2 => '04',3 => '05');
        $this->array_prioridad=array(1 => '1',2 => '3',3 => '5');
        $this->array_fases = array('Registra' => 1,'Maneja' => 2,'Firma y Llevatelo' => 3);
        $this->array_medios= array('Triptico' => 1,'Espectacular' => 2,'Pasaba por aqui' => 6,'Periodico' => 5,'Radio' => 4,'Television' => 3);

        // variable globales que viven en toda la clase
        $this->buffer='';
        $this->buffer_email='';
        $this->procesados=0;
        $this->insertados=0;
        $this->no_cargados=0;
        $this->vehiculos_insertados=0;
        $this->buffer_insertados='';
        $this->buffer_errores='';
        $this->buffer_modelo_insertados='';

        // procesamiento de la informacion
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
        $maximo_contacto_id=$this->Regresa_Id_SPVW();
        $maximo_contacto_id++;
        foreach($this->data as $clave => $data)
        {
            $contacto_id=0;
            $fase_id  = 0;
            $fuente_id= 0;
            $unidad_id= 0;
            $etapa_ciclo_vta='';
            $prioridad=0;
            $linea++;
            $this->procesados++;
            list($gid, $uid,$fase_id, $nombre, $ap_pat, $ap_mat,$tel_casa,$tel_ofi,$tel_mov, $tel_otro,
                $email,$medio_id,$evento_nombre)= $data;

            // Reviso que los datos obligatorios no sean vacios
            if (!$gid){
                $this->buffer_errores.= "Linea:  ".$linea."    ERROR PROSPECTO: ".$nombre." ".$ap_pat." ".$ap_mat." EL ID DE LA CONCESIONARIA NO PUEDE SER VACIO\n";
                $this->no_cargados++;
                continue;
            }
            if (!$uid){
                $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO: ".$nombre." ".$ap_pat." ".$ap_mat." EL ID DEL VENDEDOR NO PUEDE SER VACIO\n";
                $this->no_cargados++;
                continue;
            }
            if (!$fase_id){
                $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO: ".$nombre." ".$ap_pat." ".$ap_mat." EL NOMBRE DE LA FASE NO PUEDE SER VACIO\n";
                $this->no_cargados++;
                continue;
            }
            if (!$nombre){
                $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO: ".$nombre." ".$ap_pat." ".$ap_mat." EL NOMBRE DEL PROSPECTO NO PUEDE SER VACIO\n";
                $this->no_cargados++;
                continue;
            }
            if (!$ap_pat){
                $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO: ".$nombre." ".$ap_pat." ".$ap_mat." EL APELLIDO PATERNO DEL PROSPECTO NO PUEDE SER VACIO\n";
                $this->no_cargados++;
                continue;
            }
            if(!$evento_nombre){
                $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO: ".$nombre." ".$ap_pat." ".$ap_mat." EL EVENTO  NO PUEDE SER VACIO\n";
                $this->no_cargados++;
                continue;
            }
            if (!$tel_casa && !$tel_ofi && !$tel_mov && !$tel_otro){
                $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO: ".$nombre." ".$ap_pat." ".$ap_mat." ES NECESARIO AL MENOS UN TELEFONO\n";
                $this->no_cargados++;
                continue;
            }

            // Quito posibles acentos en los parametros de entrada
            $nombre=$this->elimina_acentos(trim($nombre));
            $ap_pat=$this->elimina_acentos(trim($ap_pat));
            $ap_mat=$this->elimina_acentos(trim($ap_mat));

            // si se encuntra la fase
            if($fase_id > 0){
                $etapa_ciclo_vta=$this->array_ciclo_venta[$fase_id];
                $prioridad =$this->array_prioridad[$fase_id];
            }

            // recupero la fuente_id y el modelo del vehiculo
            $fuente_id= $this->Revisa_tablas('fuente_id','nombre','crm_fuentes',$evento_nombre);
            if($fuente_id == 0){
                $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat.", EL NOMBRE DE LA FUENTE ES INCORRECTO\n";
                $this->no_cargados++;
                continue;
            }

            if(count($this->data_unidades) > 0)
            {
                $conta=0;
                foreach($this->data_unidades[$clave] as $tmp_valor)
                {
                    if($tmp_valor == 0)
                    {
                        $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." EL NOMBRE DEL MODELO ES INCORRECTO\n";
                         if($conta==0)
                            $this->no_cargados++;
                        $conta++;
                        continue;
                    }
                }
            }
              
            //Convierto todo a mayusculas
            $nombre=strtoupper(strtolower($nombre));
            $ap_pat=strtoupper(strtolower($ap_pat));
            $ap_mat=strtoupper(strtolower($ap_mat));
            $contacto_id=( $this->Revisa_Prospecto_BD($nombre, $ap_pat, $ap_mat,$fuente_id) + 0);
             if( $contacto_id > 0)
             {
                $this->buffer_errores.="Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." EL NOMBRE DEL PROSPECTOS YA SE ENCUENTRA EN LA BASE DE DATOS REGISTRADO CON LA MISMA FUENTE\n";
                $this->no_cargados++;
                if(count($this->data_unidades) > 0)
                {
                    foreach($this->data_unidades[$clave] as $unidad_id)
                    {
                        if($this->Revisa_Prospecto_Vehiculo($contacto_id,$unidad_id) == 0)
                        {
                            $this->Inserta_Prospecto_Unidad($contacto_id,$unidad_id);
                            $this->buffer_errores.="SOLO SE AGREGO EL MODELO:  ".$this->Regresa_Unidad($unidad_id)."\n\n";
                        }
                    }
                }
                else
                {
                    $this->buffer_errores.="NO TIENE MODELOS REGISTRADOS\n";
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
                    $this->buffer_insertados.="Prospecto: ".$nombre." ".$ap_pat." ".$ap_mat." se registro en la base de datos\n";
                    $contacto_id = mysql_insert_id();
                    $campana_id=$gid.$etapa_ciclo_vta;
                    $sql = "INSERT INTO crm_campanas_llamadas (contacto_id, campana_id)VALUES('$contacto_id', '$campana_id')";
                    mysql_query($sql) or die("Error al insertar el prospecto en campanas   ".$sql);

                    //guardar el log de asignacion
                    $sql = "INSERT INTO crm_contactos_asignacion_log (contacto_id, uid, from_uid, to_uid, from_gid, to_gid)VALUES('$contacto_id','0','0','$uid','0','$gid')";
                    mysql_query($sql) or die("Error al insertar el prospecto en logs   ".$sql);

                    //insertar modelo
                    if(count($this->data_unidades) > 0)
                    {
                        foreach($this->data_unidades[$clave] as $unidad_id)
                        {
                            $this->Inserta_Prospecto_Unidad($contacto_id,$unidad_id);
                            $this->buffer_insertados.="SE ASIGNO EL MODELO:  ".$this->Regresa_Unidad($unidad_id)."\n\n";
                        }
                    }
                    // revisar si la fuente ya se asigno a estas fuentes
                    $sql="SELECT gid,fuente_id FROM crm_groups_fuentes WHERE gid=".$gid." AND fuente_id=".$fuente_id.";";
                    $res=mysql_query($sql) or die("Error en la consulta de groups fuentes  ".$sql);
                    if(mysql_num_rows($res) == 0)
                    {
                        mysql_query("INSERT INTO crm_groups_fuentes (gid,fuente_id) VALUES ('$gid','$fuente_id');") or die("Error en el inser:  ");
                    }
                    $this->insertados++;
                }
            }
        }
    }



    /*
    *  Funcion encargada de buscar en la base de datos el nombre del prospecto
    */
    function Revisa_Prospecto_BD($nombre, $ap_pat, $ap_mat,$fuente_id)
    {
        $reg=0;
        $sql= "SELECT contacto_id,nombre, apellido_paterno, apellido_materno FROM crm_contactos WHERE upper(nombre)='$nombre' AND UPPER(apellido_paterno)='$ap_pat' AND UPPER(apellido_materno)='$ap_mat' AND origen_id='$fuente_id' LIMIT 1;";
        $res=mysql_query($sql) or die ("Error al verificar datos del contacto ".$sql);
        if(mysql_num_rows($res) > 0)
        {
            $reg=mysql_result($res,0,0);
        }
        return $reg;
    }

    function Inserta_Prospecto_Unidad($contacto_id,$unidad_id)
    {
        $unidad=$this->Regresa_Unidad($unidad_id);
        $sql = "insert into crm_prospectos_unidades (contacto_id,modelo,modelo_id) VALUES ('$contacto_id', '$unidad','$unidad_id')";
        if(mysql_query($sql) or die("Error al insertar el prospecto en unidades   ".$sql))
        {
            $this->vehiculos_insertados++;
        }
    }

    /**
     * Funcion que regresa el nombre del vehiculo
     * @param <int> $unidad_id id de la unidad
     * @return <string>  $nom nombre del vehiculo
     */
    function Regresa_Unidad($unidad_id)
    {
        $nom='';
        $sql="SELECT nombre FROM crm_unidades WHERE unidad_id='".$unidad_id."';";
        $res=mysql_query($sql) or die ("Error:  ".$sql);
        if(mysql_num_rows($res) > 0)
        {
            $nom=mysql_result($res,0,0);
        }
        return $nom;
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
        // Prospectos Procesados
        $this->buffer="Prospectos Procesados:   ".$this->procesados."<br>";
        $this->buffer_email="Prospectos Procesados:   ".$this->procesados."\n";
        // Prospectos Insertados
        $this->buffer.="Prospectos Insertados:   ".$this->insertados."<br>";
        $this->buffer_email.="Prospectos Insertados:   ".$this->insertados."\n";
        if(strlen($this->buffer_insertados) > 0)
        {
            $this->buffer_email.=$this->buffer_insertados."\n";
        }
        // Prospectos Con Modelos Insertados
        $this->buffer.="Modelos  Insertados:   ".$this->vehiculos_insertados."<br>";
        $this->buffer_email.="Modelos  Insertados:   ".$this->vehiculos_insertados."\n";

        // // Prospectos No Insertados
        if(strlen($this->buffer_errores) > 0)
        {
            $this->buffer.="Prospectos NO cargados:   ".$this->no_cargados."<br>";
            $this->buffer_email.="Prospectos NO cargados:   ".$this->no_cargados."\n\n".$this->buffer_errores."\n";
        }  
        $f1=fopen("../".$this->file,"w+");
        fwrite($f1,$this->buffer_email);
        fclose($f1);
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
        mail("lahernandez@pcsmexico.com", "Carga de datos de MFLL (Prospectos para Reportes MFLL) ".date("Y-m-d"), $this->buffer_email, $_email_headers);
    }

    /*
     * Función que da salida al informe del procesamiento
     */
    function Obten_Resultados()
    {
        return $this->buffer."\n\n<a href='../".$this->file."' target='_blank'>Descargar Archivos de Resultados</a>\n";
    }

    /**
     * Función que regresa el maximo valor del id de contacto que va en SPVW
     */
    function Regresa_Id_SPVW()
    {
        $reg=0;
        $sql="SELECT MAX(contacto_id) as maximo FROM crm_contactos;";
        $res=mysql_query($sql);
        $reg=mysql_result($res,0,0);
        return $reg;
    }
}
?>