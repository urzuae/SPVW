<?php
include_once 'Component.php';
global $treeString;
class Compuesto extends Componente
{
    private $treeString = "";
    private $hijo = array();
    public function Compuesto ($id, $name,$bloqueado,$fecha_i,$fecha_c)
    {
        parent::Componente($id,$name,$bloqueado,$fecha_i,$fecha_c);
    }
    public function agregar(Componente $componente)
    {
        //hijo.add(componente);
        $this->hijo[] = $componente;
    }
    public function remover(Componente $componente)
    {
        //$hijo.remove(componente);
    }
    public function getTreeString()
    {
        return $this->treeString;
    }
    public function mostrar($profundidad,&$tree)
    {
        $class = "";
        $div = "";
        $classTree = "";
        $hfef = '<a href="?/enewsletters/index.cfm">Airdrie eNewsletters </a>';
        if(sizeof($this->hijo) > 1)
        {
            $class = "class='expandable'";
            $div = '<div class="hitarea collapsable-hitarea"></div>';
        }
        if($profundidad == 1)
        $classTree = 'class="tree"';

        $url='';
        if(sizeof($this->hijo) < 1)
            $url="&nbsp;&nbsp;&nbsp;<a href='?_module=Catalogos&_op=concesionarias&fuente_id=".parent::getId()."'><font color='#ff8000'>Concesionarias</font></a>";

        $tree .= "<ul $classTree><li $class>".$div."<a name='".parent::getNombre().
                    "' id='".parent::getId()."' fecha_i='".parent::getFecha_I()."' fecha_c='".parent::getFecha_C()."'  href='".
                    "index.php?_module=Catalogos&_op=muestra_fuente&fuente_id=".parent::getId()."'>".
        parent::getNombre()."</a>         ".parent::getBloqueado()."&nbsp;&nbsp;".$url;
        for ($i = 0; $i < sizeof($this->hijo); $i++)
        $this->hijo[$i]->mostrar($profundidad + 1,$tree);
        $tree .="</li></ul>";
    }
}
?>
