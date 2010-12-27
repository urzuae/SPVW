<?
if (!defined('_IN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $uid, $orderby, $submit,$submit_seleccionar;

include_once("class_autorizado.php");
$sql  = "SELECT gid, super FROM users WHERE uid='$uid'";
$result = $db->sql_query($sql) or die("Error");
list($gid, $super) = $db->sql_fetchrow($result);
if ($super > 6)
{
    $_html = "<h1>Usted no es un Gerente</h1>";
}
else
{
    $select_modelo=Regresa_modelos($db);
    $select_vendedores=Regresa_Vendedores($db,$gid);
    $users=Regresa_Catalogo_Vendedores($db,$gid);
    $origenes=Regresa_Catalogo_Origenes($db);
    $prioridades = array();
    $sql = "SELECT prioridad_id, prioridad, color FROM crm_prioridades_contactos";
    $r = $db->sql_query($sql) or die($sql);
    while(list($prioridad_id, $prioridad, $prioridad_color) = $db->sql_fetchrow($r))
    {
        $prioridades[$prioridad_id] = $prioridad;
        $prioridades_color[$prioridad_id] = $prioridad_color;
    }

    global $asignar_a, $submit, $submit_seleccionar, $buscar_asignado;

    if($submit_seleccionar)
    {
        // Sacamos la primera etapa del ciclo de venta por concesionaria
        $sql = "SELECT c.campana_id FROM crm_campanas_groups AS g, crm_campanas AS c WHERE c.campana_id=g.campana_id AND g.gid='$gid' ORDER BY c.campana_id  LIMIT 1"; //la primera que sea parte de un ciclo
        $result = $db->sql_query($sql) or die("Error al leer".print_r($db->sql_error()));
        list($campana_id) = $db->sql_fetchrow($result);

        // COnsulto a todos los contactos de la concesionaria
        $sql = "SELECT c.contacto_id,concat(c.nombre,' ',c.apellido_paterno,' ',c.apellido_materno) as prospecto FROM crm_contactos AS c  WHERE (gid='$gid' )";
        $result = $db->sql_query($sql) or die("Error al leer".print_r($db->sql_error()));
        if ($db->sql_numrows($result) > 0)
        {
            $message='';
            while (list($contacto_id,$prospecto) = $db->sql_fetchrow($result)) //revisar si lo mandaron en el post ( => on)
            {
                $tmp = "chbx_$contacto_id";
                if (array_key_exists("$tmp", $_POST))
                {
                    $sql = "SELECT id FROM crm_campanas_llamadas WHERE contacto_id='$contacto_id' LIMIT 1";
                    $result2 = $db->sql_query($sql) or die("Error en la consulta:  ".$sql);
                    if (list($llamada_id) = $db->sql_fetchrow($result2) )
                    {
                        $sql = "UPDATE crm_campanas_llamadas SET campana_id='$campana_id' WHERE id='$llamada_id'";
                        $db->sql_query($sql) or die("Error al leer".print_r($db->sql_error()));
                        $message.="<br>Prospecto: <b>".$prospecto."</b> se le regreso a la primera etapa.";
                    }
                }
            }
        }
    }
    if($submit)
    {
        global $submit, $nombre, $apellido_paterno, $apellido_materno, $telefono, $contacto_id, $no_asignados, $order, $vehiculo,$uid_vendedor;
        $nombre_bk = $nombre;
        $apellido_paterno_bk = $apellido_paterno;
        $apellido_materno_bk = $apellido_materno;
        $vehiculo_bk = $vehiculo;

        if (!$order) $order = "contacto_id";

        if ($contacto_id)      $where .= "AND c.contacto_id LIKE '%$contacto_id%' ";
        if ($nombre)           $where .= "AND c.nombre LIKE '%$nombre%' ";
        if ($apellido_paterno) $where .= "AND c.apellido_paterno LIKE '%$apellido_paterno%'";
        if ($apellido_materno) $where .= "AND c.apellido_materno LIKE '%$apellido_materno%'";
        if ($uid_vendedor > 0) $where .= "AND c.uid = ".$uid_vendedor." ";
        if ($buscar_asignado)  $where .= " AND uid='$buscar_asignado'";


        $sql = "SELECT c.contacto_id, c.origen_id, concat(c.nombre,' ',c.apellido_paterno,' ',c.apellido_materno) as prospecto, c.tel_casa, c.tel_oficina, c.tel_movil, c.tel_otro, c.uid, c.prioridad, DATE_FORMAT(c.fecha_importado,'%d-%m-%Y %h:%m:%s'), c.fecha_autorizado,c.fecha_firmado,l.campana_id"
        ." FROM crm_contactos AS c, crm_campanas_llamadas as l WHERE (gid='$gid' ) AND c.contacto_id = l.contacto_id $where ORDER BY c.prioridad DESC";
        $result = $db->sql_query($sql) or die("Error al leer".$sql);
        $regs=$db->sql_numrows($result);
        if ( $regs > 0)
        {
            $lista_contactos .= "
        <center><div id='loading'><img src='img/loading.gif'></div></center>
        <table  id='tabla_contactos'  class='tablesorter'>
        <thead><tr>
            <th width='8%'>Campa&ntilde;a</th>
            <th width='7%'>Prioridad</th>
            <th width='22%'>Nombre</th>
            <th width='10%'>Fecha de registro</th>
            <th width='6%'>Vehículo</th>
            <th width='9%'>Asignado a</th>
            <th width='6%'>C. Vta</th>
            <th width='6%'>Seleccionar</th>
            </tr></thead><tbody>";
            while (list($c, $origen_id, $prospecto, $t1, $t2, $t3, $t4, $c_uid, $prioridad, $fecha_importado, $fecha_autorizado,$fecha_firmado,$id_campana) = htmlize($db->sql_fetchrow($result)))
            {
                if ($t4) $t = $t4;
                if ($t3) $t = $t3;
                if ($t2) $t = $t2;
                if ($t1) $t = $t1;
                $telefono_ = $t;
                $r3 = $db->sql_query("SELECT modelo FROM crm_prospectos_unidades WHERE contacto_id='$c' LIMIT 1");
                list($vehiculo) = $db->sql_fetchrow($r3);
                if ($vehiculo_bk)
                if (strpos(strtoupper($vehiculo), strtoupper($vehiculo_bk)) === FALSE)
                continue;

                $r4=$db->sql_query("SELECT b.nombre FROM crm_campanas_groups as a left join crm_campanas as b
                            ON a.campana_id=b.campana_id WHERE a.campana_id='".$id_campana."';");
                list($nm_campana) = $db->sql_fetchrow($r4);

                $objeto= new Fecha_autorizado ($db,$fecha_autorizado,$fecha_firmado);
                $color_semaforo=$objeto->Obten_Semaforo();

                $lista_contactos .= "
                <tr style='height:35px;'>
                    <td>".$origenes[$origen_id]."</td>
                    <td style='background-image:none;background-color:".$prioridades_color[$prioridad]."'
                    style='background-image:none'>$prioridades[$prioridad]</td>
                    <td  style='cursor:pointer' onclick=\"location.href='index.php?_module=Directorio&_op=contacto&contacto_id=".$c."&last_module=".$_module."&last_op=".$_op."';\" NOWRAP>
                    ".$prospecto."&nbsp;&nbsp;<span style='background-color:{$autorizados[$fecha_autorizado]}'>&nbsp;&nbsp;&nbsp;</span></td>
                    <td nowrap='nowrap'>".$fecha_importado."</td>
                    <td nowrap='nowrap'>".$vehiculo."</td>
                    <td nowrap='nowrap'>".$users[$c_uid]."</td>
                     <td nowrap='nowrap'>".$nm_campana."</td>
                    <td><input type='checkbox' name='chbx_$c' style='height:12;width:16;' ></td></tr>";
            }
            $lista_contactos .= "</tbody><thead><tr><td colspan='8'>Total de prospectos: ".$regs."</td></tr></thead></table><br><br>
                <table style='width:100%'>
                    <tr class='row".(++$row_class%2+1)."' style='text-align:center;'>
                        <td colspan='3'>
                            <input name='all'  type='button' onclick='allon();'  value='Todos'>&nbsp;
                            <input name='none' type='button' onclick='alloff();' value='Ninguno'>&nbsp;
                            <input type='submit' name='submit_seleccionar' value='Regresar a primera etapa'>
                        </td>
                    </tr>
                </table>";

        }//sql numrows
        else
        {
            $lista_contactos .= "<br><center>No se encontraron contactos con esos datos, porfavor intente de nuevo.</center>";
        }
    }
}

/**** FUNCIONES AUXILIARES***/
    function Regresa_modelos($db)
    {
        $buffer='';
        $sql_uni="SELECT unidad_id,nombre FROM crm_unidades ORDER BY nombre;";
        $res_uni=$db->sql_query($sql_uni);
        if($db->sql_numrows($res_uni)>0)
        {
            $buffer.='<select name="vehiculo">
                    <option value="">Todos</option>';
            while($fila = $db->sql_fetchrow($res_uni))
            {
                $buffer.="<option value='".$fila['nombre']."'>".$fila['nombre']."</option>";
            }
            $buffer.='</select>';
        }
        return $buffer;
    }
    function Regresa_vendedores($db,$gid)
    {
        $buffer='';
        $sql_uni="SELECT DISTINCT uid,name FROM users WHERE (super > 5 AND super< 9 AND gid='".$gid."') ORDER BY name;";
        $res_uni=$db->sql_query($sql_uni) or die("Error:  ".$sql_uni);
        if($db->sql_numrows($res_uni)>0)
        {
            $buffer.='<select name="uid_vendedor">
                    <option value="">Todos</option>';
            while(list($id,$name)= $db->sql_fetchrow($res_uni))
            {
                $buffer.="<option value='".$id."'>".$id." - ".$name."</option>";
            }
            $buffer.='</select>';
        }
        return $buffer;
    }
    function Regresa_Catalogo_Vendedores($db,$gid)
    {
        $users = array();
        $sql = "SELECT uid, user FROM users WHERE gid = '$gid'";
        $r = $db->sql_query($sql) or die($sql);
        while(list($uid, $user) = $db->sql_fetchrow($r))
        {
            $users[$uid] = $user;
        }
        return $users;
    }

    function Regresa_Catalogo_Origenes($db)
    {
        $origenes = array();
        $sql = "SELECT fuente_id, nombre FROM crm_fuentes order by fuente_id DESC";
        $r = $db->sql_query($sql) or die($sql);
        while(list($c_id, $c_nombre) = $db->sql_fetchrow($r))
        {
            $origenes[$c_id] = $c_nombre;
        }
        return $origenes;
    }



    //obtenemos todas las campañas posibles para evitar consultas posteriores

  /*if ($asignar_a && $submit_seleccionar) //si se van a reasignar
  {
    //buscar a que campaï¿½a lo meteremos
    $sql = "SELECT c.campana_id FROM crm_campanas_groups AS g, crm_campanas AS c WHERE c.campana_id=g.campana_id AND g.gid='$gid' ORDER BY c.campana_id  LIMIT 1"; //la primera que sea parte de un ciclo
    $result = $db->sql_query($sql) or die("Error al leer".print_r($db->sql_error()));
    list($campana_id) = $db->sql_fetchrow($result);

    $sql = "SELECT c.contacto_id" //buscar todos los que pudieran ser posibles
        ." FROM crm_contactos AS c  WHERE (gid='$gid' )";//OR gid='0'
    $result = $db->sql_query($sql) or die("Error al leer".print_r($db->sql_error()));

    if ($db->sql_numrows($result) > 0)
      while (list($contacto_id) = $db->sql_fetchrow($result)) //revisar si lo mandaron en el post ( => on)
      {
        $tmp = "chbx_$contacto_id";
        if (array_key_exists("$tmp", $_POST))
        {
          // buscar en que campaña se quedo
          $sql = "SELECT c.campana_id FROM crm_campanas_llamadas AS c WHERE c.contacto_id='$contacto_id' ORDER BY c.campana_id  LIMIT 1"; //la primera que sea parte de un ciclo
          $result = $db->sql_query($sql) or die("Error al leer".print_r($db->sql_error()));
          if($db->sql_numrows($result) > 0)
            list($campana_id) = $db->sql_fetchrow($result);

          //buscar quien lo tiene
          $sql = "SELECT uid FROM crm_contactos WHERE contacto_id='$contacto_id'";//OR gid='0'
          $db->sql_query($sql) or die("Error al asignar".print_r($db->sql_error()));
          list($from_uid) = $db->sql_fetchrow($result2);
          if ($from_uid == $asignar_a) //reasignarselo a sï¿½ mismo no se puede
          {
            $no_asignados++;
            continue;
          }
          //cambiar al asignado
          $sql = "UPDATE crm_contactos SET uid='$asignar_a' WHERE contacto_id='$contacto_id' AND (gid='$gid' ) ";//OR gid='0'
          $db->sql_query($sql) or die("Error al asignar".print_r($db->sql_error()));
          //ahora mandarlo a la primer campaï¿½a
          //checar primero si no estï¿½ en alguna ya
          $sql = "SELECT id FROM crm_campanas_llamadas WHERE contacto_id='$contacto_id' LIMIT 1";
          $result2 = $db->sql_query($sql) or die("Error al leer".print_r($db->sql_error()));
          if (list($llamada_id) = $db->sql_fetchrow($result2))
            $sql = "UPDATE crm_campanas_llamadas SET campana_id='$campana_id' WHERE id='$llamada_id'";
          else
            $sql = "INSERT INTO crm_campanas_llamadas (campana_id, contacto_id,status_id,fecha_cita)VALUES('$campana_id','$contacto_id','-2','0000-00-00 00:00:00')";

          $db->sql_query($sql) or die("Error al leer".print_r($db->sql_error()));

              //meter la asignaciï¿½n al log
              $sql = "INSERT INTO crm_contactos_asignacion_log (contacto_id,uid,from_uid,to_uid)VALUES('$contacto_id','$uid','$from_uid','$asignar_a')";
          $db->sql_query($sql) or die("Error al leer".print_r($db->sql_error()));
        }
      }
      if ($no_asignados) $extra_js .= "alert('$no_asignados prospectos no fueron reasignados debido a que ya estaban asignados al mismo vendedor.');";
  }*/

    ?>