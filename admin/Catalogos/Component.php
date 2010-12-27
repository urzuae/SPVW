<?php

class Componente
{
	private $nombre;
    private $id;
    private $bloqueado;
    private $fecha_i;
    private $fecha_c;
	public function Componente ($id,$nombre,$bloqueado,$fecha_i,$fecha_c)
	{
		$this->nombre = $nombre;
        $this->id = $id;
        $this->fecha_i=$fecha_i;
        $this->fecha_c=$fecha_c;
        $this->bloqueado='';
        if($bloqueado == 0)
            $this->bloqueado="<font color='#800000'>Bloqueado</font>";

	}
    public function getNombre()
    {
        return $this->nombre;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getBloqueado()
    {
        return $this->bloqueado;
    }
    public function getFecha_I()
    {
        return $this->fecha_i;
    }
    public function getFecha_C()
    {
        return $this->fecha_c;
    }
    public function agregar(Componente $c)
    {
        
    }
    public function remover(Componente $c)
    {
        
    }
    public function mostrar(int $prioridad)
    {
        
    }	
}

?>