<?php
/* 
 * Crea una fuente
 * 
 */

global $db,$submit, $nombreFuente, $padre_id,$nombre;


if($submit)
{
    $sql="SELECT min(fuente_id) as minimo FROM crm_fuentes;";
    $res=$db->sql_query($sql);
    if($db->sql_numrows($res)> 0)
    {
        $minimo=$db->sql_fetchfield(0,0,$res);
        $minimo=$minimo - 1;
        echo"<br>".$minimo;
        $timeStamp = date("Y-m-d H:i:s");
        $sqlNuevaFuente = "insert into crm_fuentes values('','$nombreFuente','$timeStamp')";
        //$db->sql_query($sqlNuevaFuente) or die("Erro al insertar la nueva fuente->".$sqlNuevaFuente);
        $lastIdInsert = $db->sql_nextid();
        $sqlNuevaRama = "insert into crm_fuentes_arbol values('$padre_id','$lastIdInsert')";
        //$db->sql_query($sqlNuevaRama) or die("Erro al insertar una rama en la relacion->".$sqlNuevaRama);
    }
    //header("Location: index.php?_module=Catalogos&_op=fuentes");
}
?>
