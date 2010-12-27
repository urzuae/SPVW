<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $campana_id, $submit, $guardar,
       $numero_desde, $numero_hasta,
       $sexo, 
       $compania, $cargo,
       $giro, 
       $cuotas_desde, $cuotas_hasta,
       $neto_desde, $neto_hasta,
       $email, $prospecto,
       $persona_moral,
       $colonia, $cp, 
       $poblacion, $entidad_id,
       $edad_desde, $edad_hasta,
       $nota;
if (!$campana_id) die("Porfavor seleccione una campaña antes.");
$sql = "SELECT nombre FROM crm_campanas WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die(print_r($db->sql_error()));
list($campana) = $db->sql_fetchrow($result);

$neto_hasta = remove_money_format2($neto_hasta);
$neto_desde = remove_money_format2($neto_desde);
if ($numero_desde > $numero_hasta && $numero_hasta != "") $numero_hasta = $numero_desde;
if ($edad_desde > $edad_hasta && $edad_hasta != "") $edad_hasta = $edad_desde;
if ($neto_desde > $neto_hasta && ($neto_hasta != "")) $neto_hasta = $neto_desde;


$sql  = "SELECT gid FROM crm_campanas_groups WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error");
if ($db->sql_numrows($result) == 1)
  list($c_gid) = $db->sql_fetchrow($result);

//crear
if ($submit || $guardar) //buscando
{

//     //los grupos a los cuales permitirles
//     $sql = "SELECT gid FROM crm_campanas_groups WHERE campana_id='$campana_id'";
//     $result = $db->sql_query($sql) or die(print_r($db->sql_error()));
//     $where_groups = "gid='0'";
//     if ($db->sql_numrows($result) > 0)
//     {
//       while (list($c_gid) = $db->sql_fetchrow($result))
//       {
//         $where_groups .= " OR gid='$c_gid' ";
//       }
//     }
//     $where .= " AND ($where_groups)";
    //el WHERE son los criterios de buskeda y depende de lo ke mandaron
    if ($numero_desde) $where .= " AND `contrato_id` >= '$numero_desde'";
    if ($numero_hasta) $where .= " AND `contrato_id` <= '$numero_hasta'";
    if ($neto_desde) $where .= " AND `ingreso_mensual_neto` >= '$neto_desde'";
    if ($neto_hasta) $where .= " AND `ingreso_mensual_neto` <= '$neto_hasta'";
    if ($cuotas_desde) $where .= " AND `cuotas_impagadas` >= '$cuotas_desde'";
    if ($cuotas_hasta) $where .= " AND `cuotas_impagadas` <= '$cuotas_hasta'";
    if ($compania) $where .= " AND `compania` LIKE '%$compania%'";
    if ($cargo) $where .= " AND `cargo` LIKE '%$cargo%'";
    if ($email) {$where .= " AND `email` != ''"; $chbx_email = "CHECKED";}
    if ($prospecto) {$where .= " AND `prospecto` = '1'"; $chbx_prospecto = "CHECKED";}
    if (strlen($persona_moral)) $where .= " AND `persona_moral` = '$persona_moral'";
    if (strlen($sexo)) $where .= " AND `sexo` = '$sexo'";
    if ($colonia) $where .= " AND `colonia` LIKE '%$colonia%'";
    if ($cp) $where .= " AND `cp` LIKE '%$cp%'";
    if ($poblacion) $where .= " AND `poblacion` LIKE '%$poblacion%'";
    if ($entidad_id) $where .= " AND `entidad_id` = '$entidad_id'";
    if ($edad_desde) $where .= " AND fecha_de_nacimiento != 0 AND ((DATEDIFF(NOW(), fecha_de_nacimiento))/365) > ($edad_desde)";
    if ($edad_hasta) $where .= " AND fecha_de_nacimiento != 0 AND ((DATEDIFF(NOW(), fecha_de_nacimiento))/365) < ($edad_hasta + 1)";
    if ($cp) $where .= " AND `nota` LIKE '%$nota%'";
    $orderby = "nombre";
    $sql = "SELECT contacto_id, contrato_id, nombre, apellido_paterno, apellido_materno FROM crm_contactos WHERE 1 $where ORDER BY $orderby";
    $result = $db->sql_query($sql) or die("Error al consultar contactos<br>".$sql.print_r($db->sql_error()));
    $numrows = $db->sql_numrows($result);

    while (list($contacto_id, $contrato_id, $name, $apellido_paterno, $apellido_materno) =
            $db->sql_fetchrow($result))
    {
        if ($guardar) //GUARDAR!
        {
            if ($c_gid) //setear este contacto para que pueda ser accedido por el grupo que hace esta campaña
            {
              $sql = "UPDATE crm_contactos SET gid='$c_gid' WHERE contacto_id='$contacto_id' LIMIT 1";
              $db->sql_query($sql) or die("Error al asignar grupo al contacto");
            }
            $sql = "SELECT 1 FROM crm_campanas_llamadas WHERE contacto_id='$contacto_id' AND campana_id='$campana_id'";
            if ($db->sql_numrows($db->sql_query($sql)) == 0) //si no está ya, agregar
            {
                $sql = "INSERT INTO crm_campanas_llamadas (campana_id, contacto_id) VALUES('$campana_id','$contacto_id')";
                $db->sql_query($sql) or die("Error al insertar contacto");
            }
        }
    }
    if (!$guardar){
     $status_busqueda = " - $numrows contactos encontrados<br>\n";
     $tabla_contactos .= "Se encontraron $numrows contactos que cumplen ese criterio<br>\n";
    } 
    else{
     $status_busqueda = " - Se agregaron $numrows contactos a la campaña<br>\n";
     $tabla_contactos .= "Se agregaron $numrows contactos a la campaña. De click en salir o busque con otro criterio para agregar mas contactos.<br>\n";
    }
}
else $guardar_disabled = "DISABLED";

$sql = "SELECT id FROM crm_campanas_llamadas WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error");
$contactos_totales = $db->sql_numrows($result);
$sql = "SELECT id FROM crm_campanas_llamadas WHERE campana_id='$campana_id' AND (status_id!='0' AND status_id!='-1')";
$result = $db->sql_query($sql) or die("Error");
$contactos_finalizados = $db->sql_numrows($result);

//inicializar los selects
require_once("$_includesdir/select.php");
$select_entidades = select_entidades_federativas($entidad_id);
$select_dia = select_dia($dia);
$select_mes = select_mes($mes);
$select_ano = select_ano($ano);

$select_sexo = "<select name=\"sexo\">";
$select_sexo .= "<option value=\"\" ".(!strlen($sexo)?"SELECTED":"").">Ambos</option>";
$select_sexo .= "<option value=\"0\" ".($sexo=="0"?"SELECTED":"").">Masculino</option>";
$select_sexo .= "<option value=\"1\" ".($sexo=="1"?"SELECTED":"").">Femenino</option>";
$select_sexo .= "</select>";


$select_persona_moral = "<select name=\"persona_moral\">";
$select_persona_moral .= "<option value=\"\" ".(!strlen($persona_moral)?"SELECTED":"").">Ambos</option>";
$select_persona_moral .= "<option value=\"0\" ".($persona_moral=="0"?"SELECTED":"").">Persona Física</option>";
$select_persona_moral .= "<option value=\"1\" ".($persona_moral=="1"?"SELECTED":"").">Persona Moral</option>";
$select_persona_moral .= "</select>";

$_site_title .= " - $campana";
if ($neto_desde != 0)
  $neto_desde = money_format2("%d", $neto_desde);
else $neto_desde ="";
if ($neto_hasta != 0)
  $neto_hasta = money_format2("%d", $neto_hasta);
else $neto_hasta = "";

?>
