<?php
	class Genera_Excel
	{
		private $cabecera;
		private $data;
		private $cabecera_ce;
		private $data_ce;
		private $archivo;
		private $array_cabeceras;
		private $buffer_html;
		private $buffer;
		private $buffer_ce;

		function __construct($data,$tipo)
		{
			$num=rand(1,100000);
			$archivo="archivos/".$tipo.".xls";
			$this->Genera_Archivo($data,$archivo);
		}
    	function Genera_Archivo($data,$archivo)
		{
			$this->archivo=$archivo;
			$this->data=$data;
            $this->data=str_replace('<th>&nbsp;</th>','',$this->data);
            $this->data=str_replace('Vendedores','',$this->data);
            $this->data=str_replace('Prospectos','',$this->data);
			$boton='<table width="90%">
					<tr><td align="right">
                     <a href="'.$this->archivo.'" target="_blank">Exportar a Excel</a>
					</td></tr></table>';
			$this->buffer_html=$this->data;
            $this->buffer=$boton;
			$f1=fopen($this->archivo,"w+");
			fwrite($f1,$this->buffer_html);
			fclose($f1);
		}
		function Obten_href()
		{
			return $this->buffer;
		}
	}
?>
