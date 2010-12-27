<?php
class Carga_Prospectos_SPVW_BD
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

    function __construct($fh,$conn)
    {
        $this->buffer='';
        $this->procesados=0;
        $this->vehiculos_insertados=0;
        $this->insertados=0;
        $this->buffer_insertados='';
        $this->buffer_errores='';
        $this->conn=$conn;
        $this->fh=$fh;
        $this->data=array();
        $this->Procesa_Archivo_Prospectos();
        $this->Procesa_Informe();
        $this->Envia_Email_Aviso();
    }

     /**
     * Funcion que se encarga de procesar e insertar los registros
     */
    function Procesa_Archivo_Prospectos()
    {
        $linea=0;
        while($this->data = fgetcsv($this->fh, 1000, "|"))
        {
            $contacto_id=0;
            $fase_id  = 0;
            $evento_id= 0;
            $unidad_id= 0;
            $tipo_pago_id=0;
            $inv_id=0;
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

                if (!$gid)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." EL ID DE LA CONCESIONARIA NO PUEDE SER VACIO<br>";
                    continue;
                }
                if (!$uid)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." EL ID DEL VENDEDOR NO PUEDE SER VACIO<br>";
                    continue;
                }
                if (!$fase_nombre)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." EL NOMBRE DE LA FASE NO PUEDE SER VACIO<br>";
                    continue;
                }
                if (!$nombre)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." EL NOMBRE DEL PROSPECTO NO PUEDE SER VACIO<br>";
                    continue;
                }
                if (!$ap_pat)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." EL APELLIDO PATERNO DEL PROSPECTO NO PUEDE SER VACIO<br>";
                    continue;
                }
                if (!$tel_casa && !$tel_ofi && !$tel_mov && !$tel_otro)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." ES NECESARIO AL MENOS UN TELEFONO<br>";
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
                $tipo_pago_nombre=$this->elimina_acentos(trim($tipo_pago_nombre));

                // Busco los id en los catalogos
                $fase_id  = $this->Revisa_tablas('fase_id','fase_nombre','mfll_fases',$fase_nombre);
                $evento_id= $this->Revisa_tablas('evento_id','evento_nombre','mfll_eventos',$evento_nombre);
                $unidad_id= $this->Revisa_tablas('unidad_id','nombre','crm_unidades',$unidad);
                $tipo_pago_id=$this->Revisa_tablas('tipo_pago_id','tipo_pago_nombre','mfll_tipo_pagos',$tipo_pago_nombre);
                $medio_id=$this->Revisa_tablas('medio_id','medio','mfll_medios',$medio_entero);

                if($fase_id == 0){
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." EL NOMBRE DE LA FASE ES INCORRECTO<br>";
                    continue;
                }
                if($evento_id == 0){
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." EL NOMBRE DEL EVENTO ES INCORRECTO<br>";
                    continue;
                }
                if($unidad_id == 0){
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." EL NOMBRE DEL MODELO ES INCORRECTO<br>";
                    continue;
                }
                if(($fase_id == 3) && ($tipo_pago_id == 0)){
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR: PROSPECTO  ".$nombre.' '.$ap_pat.' '.$ap_mat." EL NOMBRE DEL TIPO DE PAGO ES INCORRECTO, YA QUE LA FASE ES FIRMA Y LLEVATELO <br>";
                    continue;
                }

                // Convierto a mayusculas el nombre y a minusculas el email
                $nombre=strtoupper(strtolower($nombre));
                $ap_pat=strtoupper(strtolower($ap_pat));
                $ap_mat=strtoupper(strtolower($ap_mat));
                $email =strtolower($email);

                // Reviso que el contacto ya este en la tabla de prospectos
                if( $this->Revisa_Prospecto_BD($nombre, $ap_pat, $ap_mat,$unidad_id) == 0)
                {

                    // Insertamos el prospecto en la base de datos
                    $sql = "INSERT INTO mfll_contactos_spvw (gid, uid, nombre, apellido_paterno,apellido_materno,
                        tel_casa,tel_oficina, tel_movil, tel_otro,email,evento_id,fase_id,unidad_id,tipo_pago_id,medio_id) VALUES ('$gid','$uid','$nombre',
                        '$ap_pat','$ap_mat','$tel_casa','$tel_ofi','$tel_mov','$tel_otro','$email','$evento_id','$fase_id','$unidad_id','$tipo_pago_id','$medio_id');";
                    if(mysql_query($sql) or die("Error al insertar el prospecto.  ".$sql))
                    {
                        $this->buffer_insertados.="Prospecto: ".$nombre." ".$ap_pat." ".$ap_mat." se registro en la base de datos<br>";
                        $this->insertados++;
                    }
                }
                else
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    El CONTACTO:".$nombre. " ".$ap_pat." ".$ap_mat." YA EXISTE EN LA BASE DE DATOS<br>";
                }
            }
            else
            {
                $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  LOS PARAMETROS DE ENTRADA SON MAS DE LOS REQUERIDOS<br>";
                continue;
            }
        }
    }


    function Revisa_Prospecto_Vehiculo($contacto_id,$unidad_id)
    {
        $reg=0;
        $sql_mod="SELECT unidad_id FROM mfll_contactos_unidades WHERE contacto_id=".$contacto_id." AND unidad_id=".$unidad_id.";";
        $res_mod=mysql_query($sql_mod) or die ("Error al verificar datos del contacto y vehiculo".$sql_mod);
        if(mysql_num_rows($res_mod) > 0)
        {
            $reg=1;
        }
        return $reg;
    }
    /*
    *  Funcion encargada de buscar en la base de datos el nombre del prospecto
    */
    function Revisa_Prospecto_BD($nombre, $ap_pat, $ap_mat,$unidad_id)
    {
        $reg=0;
        $sql= "SELECT contacto_id,nombre, apellido_paterno, apellido_materno FROM mfll_contactos_spvw WHERE upper(nombre)='$nombre' AND UPPER(apellido_paterno)='$ap_pat' AND UPPER(apellido_materno)='$ap_mat' LIMIT 1;";
        $res=mysql_query($sql) or die ("Error al verificar datos del contacto ".$sql);
        if(mysql_num_rows($res) > 0)
        {
            $tmp_contacto=mysql_result($res,0,0);
            $sql_uni="SELECT unidad_id FROM mfll_contactos_spvw WHERE contacto_id=".$tmp_contacto." and unidad_id=".$unidad_id.";";
            $res_uni=mysql_query($sql_uni);
            if(mysql_num_rows($res_uni)>0)
            {
                $reg=1;
            }
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
    function Procesa_Informe()
    {
        $this->buffer="Prospectos Procesados:   ".$this->procesados."<br>";
        if(strlen($this->buffer_errores) > 0)
        {
            $this->buffer.="Errores:   <br>".$this->buffer_errores."<br>";
        }
        if(strlen($this->buffer_insertados) > 0)
        {
            $this->buffer.="Resumen:   ".$this->buffer_insertados."<br>";
        }
        $this->buffer.="Prospectos Insertados:   ".$this->insertados."<br>";
        $this->buffer.="Vehículos  Insertados:   ".$this->vehiculos_insertados."<br>";
    }

    /**
     * Función que envia el informe al email de soporte
     */
    function Envia_Email_Aviso()
    {
        $_email_headers  = 'MIME-Version: 1.0' . "\r<br>";
        $_email_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r<br>";
        $_email_from = "noreply@pcsmexico.com";
        $_email_headers .= "from:$_email_from\r<br>";
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
