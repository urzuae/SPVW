<?php
/** Script que sirve para eliminar registros duplicados **/

global $db,$fecha;
$buffer='';
$fecha_i=$argv[2];
$fecha_c=$argv[3];
if( ($fecha_i=='') && ($fecha_c==''))
    $filtro= " fecha_importado between '".date('Y-m-d')." 00:01:01' AND '".date('Y-m-d')." 23:59:59' ";
if( ($fecha_i!='') && ($fecha_c!=''))
    $filtro= " fecha_importado between '".$fecha_i." 00:01:01' AND '".$fecha_c." 23:59:59' ";

if( ($fecha_i!='') && ($fecha_c==''))
    $filtro= " fecha_importado between '".$fecha_i." 00:01:01' AND '".$fecha_i." 23:59:59' ";

if( ($fecha_i=='') && ($fecha_c!=''))
    $filtro= " fecha_importado between '".$fecha_c." 00:01:01' AND '".$fecha_i." 23:59:59' ";


$tabla=" prospectos_duplicados ";
// Elimino la tabla si es que existe para empezar el proceso
$db->sql_query("DROP TABLE IF EXISTS ".$tabla.";") or die("Error al elimina la tabla  ");

// creo la tabla con los prospectos con un mismo nombre de un dia
$sql="CREATE TABLE ".$tabla." AS
     SELECT concat(nombre,' ',apellido_paterno,' ',apellido_materno) AS prospecto,
     COUNT(concat(nombre,' ',apellido_paterno,' ',apellido_materno)) AS total
     FROM crm_contactos WHERE ".$filtro."
     GROUP BY concat(nombre,' ',apellido_paterno,' ',apellido_materno)
     HAVING total > 1
     ORDER BY nombre,apellido_paterno,apellido_materno,contacto_id;";
echo"\nsql:  ".$sql."\n";
if($db->sql_query($sql) or die("Error al crear la tabla: ".$sql))
{
    echo"\n Se ha creado la tabla de prospectos duplicados\n\nSe realizara el proceso de depuración\n";
    
    // Consulta la tabla generada para sacar nombre por nombre

    $sql_d="SELECT prospecto,total FROM ".$tabla." ORDER BY prospecto;";
    $res_d=$db->sql_query($sql_d) or die("Error en la consulta de prospectos duplicados");
    if($db->sql_numrows ($res_d) > 0)
    {
        $total_final_eliminados=0;
        while(list($nombre,$total) = $db->sql_fetchrow($res_d))
        {
            $registros_duplicados=0;
            $array_prospectos_eliminados=array();
            $array_prospectos_no_eliminados=array();

            // ESTE METODO ME REGRESA LOS ID DEL PROSPECTO
            $array_contacts_id=Regresa_Id_Prospecto($db,$nombre,$filtro);
            $registros_duplicados=count($array_contacts_id);

            // SI EL ARRAY NO ES VACIO, ME INDICA QUE SE RECUPERARON LOS IDS DE PROSPECTO
            if(count($array_contacts_id) > 0)
            {
                // RECORRO EL ARRAY
                foreach($array_contacts_id as $contacto_id)
                {
                    // REVISO QUE EL ID ASOCIADO A UN PROSPECTO, TENGA SEGUIMIENTO
                    // REGRESA 0 SI EL PROSPECTO NO TIENE SEGUIMIENTO, 1 EN CASO CONTRARIO
                    if(Revisa_Seguimiento($db,$contacto_id) == 0)
                    {
                        //SI EL PROSPECTO NO TIENE SEGUIMIENTO LO GUARDO PARA ELIMINARLO
                        //ESTO SE HIZO POR QUE SE PUEDE DAR EL CASO QUE EL PROSPECTO NO TENGA SEGUIMIENTO EN NINGUNO DE SUS REGITROS DUPLICADOS
                        $array_prospectos_eliminados[]=$contacto_id;
                    }
                    else
                    {
                        // GUARDO LOS ID DE LOS PROSPECTOS NO ELIMINADO
                        $array_prospectos_no_eliminados[]=$contacto_id;
                    }
                }
                // EN TOTAL_ELIMINADOS GUARDO EL NUMERO DE PROSPECTOS A ELIMINAR
                $total_eliminados=count($array_prospectos_eliminados);

                // SI EL TOTAL DE PROSPECTOS REPITIDOS ES IGUAL AL NUMERO DE PROSPECTOS A ELIMINAR
                // ME INDICA QUE NINGUN PROSPECTO DUPLICADO TIENE SEGUIMIENTO
                if($registros_duplicados == count($array_prospectos_eliminados))
                {
                    // POR LO CUAL SOLO BORRO EL TOTAL DE ELIMINAR MENOS 1, PARA GARANTIZAR QUE NO ELIMINE UN REGISTO
                    $total_eliminados=count($array_prospectos_eliminados)- 1;
                }
                $buffer.="\n".$nombre."    registros duplicados:  ".$registros_duplicados."   eliminados:  ".count($array_prospectos_eliminados)."  prosp no eliminados:   ".count($array_prospectos_no_eliminados)."     prosp a eliminar:   ".$total_eliminados."\n";

                // RECORRO EL ARRAY  Y VOY ELIMINANDO
                for($i=0;$i<$total_eliminados;$i++)
                {
                    $contacto_id=$array_prospectos_eliminados[$i];

                    //USO EL METODO PARA IR ELIMINADO A LOS PROSPECTOS
                    $buffer.=Elimina_Prospecto_Duplicado($db,$contacto_id,$nombre);
                    $total_final_eliminados++;
                }
            }
        }
       $buffer.="\n\nTotal de registros eliminados:   ".$total_final_eliminados;
    }
}
else
{
    echo"\n No hay prospectos duplicados en la tabla de crm contactos\n";
}
echo"\n".$buffer."\n\n";
/**********************  FUNCIONES AUXILIARES  **************************/


function Elimina_Prospecto_Duplicado($db,$contacto_id,$nombre)
{
    mysql_query("DELETE FROM crm_campanas_llamadas WHERE contacto_id=".$contacto_id.";");
    mysql_query("DELETE FROM crm_contactos WHERE contacto_id=".$contacto_id.";");

    /**
        mysql_query("INSERT INTO crm_prospectos_cancelaciones (contacto_id, uid, motivo, motivo_id)VALUES('$contacto_id', '0', 'Prospectos sin atender por el vendedor', '0')");
        mysql_query("INSERT INTO crm_contactos_finalizados Select * from crm_contactos where contacto_id = '$contacto_id'");
        mysql_query("INSERT INTO crm_campanas_llamadas_finalizadas Select * from crm_campanas_llamadas where contacto_id = '$contacto_id'");
        mysql_query("delete from crm_contactos where contacto_id = '$contacto_id'");
        mysql_query("delete from crm_campanas_llamadas where contacto_id = '$contacto_id'");
        echo "$contacto_id OK<br>";
        ++$cont;
        ob_flush();
        flush();
     */
    return "\n".$contacto_id."   ".$nombre."\nDELETE FROM crm_campanas_llamadas WHERE contacto_id=".$contacto_id.";\nDELETE FROM crm_contactos WHERE contacto_id=".$contacto_id.";\n\n\n";
}

/**
 *
 * @param <type> $db
 * @param <type> $contacto_id
 * @return <type>
 */

function Revisa_Seguimiento($db,$contacto_id)
{
    $id_llamada=0;
    $eliminar=0;
    $sql="SELECT id FROM crm_campanas_llamadas WHERE contacto_id=".$contacto_id." ORDER BY timestamp DESC LIMIT 1;";
    $res=$db->sql_query($sql) or die("Error en la consulta a campanas llamadas:  ".$sql);
    if($db->sql_numrows($res)>0)
    {
        list($id_llamada) = $db->sql_fetchrow($res);        
        $sql_2="SELECT evento_id FROM crm_campanas_llamadas_eventos WHERE llamada_id=".$id_llamada.";";
        $res_2=$db->sql_query($sql_2) or die("Error en la consulta a campanas llamadas eventos:  ".$sql_2);
        if($db->sql_numrows($res_2)>0)
        {
            $eliminar=1;
        }
    }
    return $eliminar;
}


/**
 * Funcion que regresa el id del contacto
 * @param <object> $db       conexion a la base de datos
 * @param <string> $nombre   nombre del prospecto
 * @return <int>   $id_contacto    id del contacto
 */
function Regresa_Id_Prospecto($db,$nombre,$filtro)
{
    $array_contacts_id=array();
    $sql_bus="SELECT contacto_id FROM crm_contactos WHERE concat(nombre,' ',apellido_paterno,' ',apellido_materno) ='".$nombre."' AND ".$filtro." ;";
    $res_bus=$db->sql_query($sql_bus) or die("Error en la busqueda del prospecto por nombre:  ".$sql_bus);
    if($db->sql_numrows($res_bus) >0 )
    {
        while(list($id) = $db->sql_fetchrow($res_bus) )
        {
            $array_contacts_id[]=$id;
        }
    }
    return $array_contacts_id;
}
?>
