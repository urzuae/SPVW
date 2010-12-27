<?php
	class Genera_Excel
	{
		private $data;
    	private $archivo;

        function __construct($data,$tipo)
		{
			$num=rand(1,100000);
			$this->archivo="archivos/".$tipo.".xls";
            $this->data=$data;
			$this->Genera_Archivo();
		}
    	function Genera_Archivo()
		{
			$f1=fopen($this->archivo,"w+");
			fwrite($f1,$this->data);
			fclose($f1);
		}
		function Obten_href()
		{
			return $this->archivo;
		}
	}
?>
