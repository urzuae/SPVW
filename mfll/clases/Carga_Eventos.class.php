<?php
class Carga_Eventos
{
    private $fh;
    private $conn;
    private $data;
    private $buffer_errores;
    private $buffer_insertados;
    private $procesados;
    private $insertados;
    private $buffer;

    function __construct($fh,$conn)
    {
        $this->buffer='';
        $this->procesados=0;
        $this->insertados=0;
        $this->buffer_insertados='';
        $this->buffer_errores='';
        $this->conn=$conn;
        $this->fh=$fh;
        $this->data=array();
        $this->Procesa_Archivo();
        $this->Procesa_Informe();
        $this->Envia_Email_Aviso();
    }
    /**
     * Funcion que se encarga de procesar e insertar los registros
     */
    function Procesa_Archivo()
    {
        $linea=0;
        while($this->data = fgetcsv($this->fh, 1000, "|"))
        {
            $linea++;
            if (!($iii++)) continue;
                $this->procesados++;
            if(count($this->data) == 7 )
            {
                $data2 = array();
                foreach($this->data as $undato)
                {
                    $data2[] = addslashes($undato);
                }
                list($evento_nombre, $evento_direccion, $evento_fecha_inicial,$evento_fecha_final,
                 $evento_horario, $evento_costo, $evento_responsable)= $data2;

                if (!$evento_nombre)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  EL NOMBRE DEL EVENTO NO PUEDE SER VACIO\n";
                    continue;
                }
                if (!$evento_responsable)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  EL NOMBRE DEL RESPONSABLE DEL EVENTO NO PUEDE SER VACIO\n";
                    continue;
                }
                if (!$evento_fecha_inicial)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  LA FECHA INICIAL DEL EVENTO NO PUEDE SER VACIO\n";
                    continue;
                }
                if (!$evento_fecha_final)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  LA FECHA DE TERMINO DEL EVENTO NO PUEDE SER VACIO\n";
                    continue;
                }
                // quito acentos en las cadenas
                $evento_nombre=$this->elimina_acentos(trim($evento_nombre));
                $evento_direccion=$this->elimina_acentos(trim($evento_direccion));
                $evento_responsable=$this->elimina_acentos(trim($evento_responsable));

                // las convierto a mayusculas
                $evento_nombre = strtoupper($evento_nombre);
                $evento_direccion = strtoupper($evento_direccion);
                $evento_responsable = strtoupper($evento_responsable);

                //busco que el evento no exista
                $sql= "SELECT evento_nombre, evento_responsable FROM mfll_eventos WHERE evento_nombre='$evento_nombre' AND evento_responsable='$evento_responsable'";
                $res=mysql_query($sql,$this->conn) or die ("Error al verificar datos del evento");
                if(mysql_num_rows($res) > 0)
                {
                    $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  EL NOMBRE EVENTO:&nbsp;".$evento_nombre. " YA EXISTE EN LA BASE DE DATOS\n";
                    continue;
                }

                //siempre es insert por que no tenemos un id en el layout
                $sql = "INSERT INTO mfll_eventos (evento_nombre, evento_direccion, evento_fecha_inicial,
                        evento_fecha_final, evento_horario, evento_costo, evento_responsable)
                    VALUES ('$evento_nombre','$evento_direccion','$evento_fecha_inicial',
                    '$evento_fecha_final','$evento_horario','$evento_costo','$evento_responsable');";
                if(mysql_query($sql,$this->conn) or die("Error insertar el evento: ".$sql))
                {
                    $evento_id = mysql_insert_id();
                    $this->insertados++;
                    $this->buffer_insertados.="Evento: ".$evento_nombre." insertado en la base de datos";
                }
            }
            else
            {
                $this->buffer_errores.= "Linea:  ".$linea."    ERROR:  LOS PARAMETROS DE ENTRADA SON MAS DE LOS REQUERIDOS\n";
                continue;
            }
        }
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
        $this->buffer="Eventos Procesados:   ".$this->procesados."\n";
        if(strlen($this->buffer_errores) > 0)
        {
            $this->buffer.="Errores:   ".$this->buffer_errores."\n";
        }
        if(strlen($this->buffer_insertados) > 0)
        {
            $this->buffer.="Resumen:   ".$this->buffer_insertados."\n";
        }
        $this->buffer.="Eventos Insertados:   ".$this->insertados;
    }

    /**
     * Función que envia el informe al email de soporte
     */
    function Envia_Email_Aviso()
    {
        $_email_headers  = 'MIME-Version: 1.0' . "\r\n";
        $_email_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $_email_from = "noreply@pcsmexico.com";
        $_email_headers .= "from:$_email_from\r\n";
        mail("lahernandez@pcsmexico.com", "Carga de datos de MFLL (Eventos) ".date("Y-m-d"), $this->buffer, $_email_headers);
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
