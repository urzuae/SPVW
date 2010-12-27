<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $campana_id, $submit, $guardar, $reset,
       $compania, $cargo,
       $email,
       $colonia, $cp, 
       $poblacion, $entidad_id,
       $edad_desde, $edad_hasta,
       $nota;
if (!$campana_id) die("Porfavor seleccione una campaña antes.");
$sql = "SELECT nombre FROM crm_campanas WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die(print_r($db->sql_error()));
list($campana) = $db->sql_fetchrow($result);

$email = 1; //siempre buscar a los que tengan correo

if ($reset)
{
  $sql = "DELETE FROM crm_campanas_emails_contactos WHERE campana_id='$campana_id'";
  $result = $db->sql_query($sql) or die(print_r($db->sql_error()));
  $status_busqueda = " - Se reinicio la campaña a 0 contactos<br>\n";
}

//crear
if ($submit || $guardar) //buscando
{
    //el WHERE son los criterios de buskeda y depende de lo ke mandaron
    if ($compania) $where .= " AND `compania` LIKE '%$compania%'";
    if ($cargo) $where .= " AND `cargo` LIKE '%$cargo%'";
    if ($email) {$where .= " AND `email` != ''"; }
    if ($colonia) $where .= " AND `colonia` LIKE '%$colonia%'";
    if ($cp) $where .= " AND `cp` LIKE '%$cp%'";
    if ($poblacion) $where .= " AND `poblacion` LIKE '%$poblacion%'";
    if ($entidad_id) $where .= " AND `entidad_id` = '$entidad_id'";
    if ($edad_desde) $where .= " AND fecha_de_nacimiento != 0 AND ((UNIX_TIMESTAMP(now())-UNIX_TIMESTAMP(fecha_de_nacimiento))/60/60/24/365) > ($edad_desde)";
    if ($edad_hasta) $where .= " AND fecha_de_nacimiento != 0 AND ((UNIX_TIMESTAMP(now())-UNIX_TIMESTAMP(fecha_de_nacimiento))/60/60/24/365) < ($edad_hasta + 1)";
    if ($cp) $where .= " AND `nota` LIKE '%$nota%'";
    $orderby = "nombre";
    $sql = "SELECT contacto_id, nombre, apellido_paterno, apellido_materno FROM crm_contactos WHERE 1 $where ORDER BY $orderby";
    $result = $db->sql_query($sql) or die("Error al consultar contactos");
    $numrows = $db->sql_numrows($result);

    while (list($contacto_id, $name, $apellido_paterno, $apellido_materno) =
            $db->sql_fetchrow($result))
    {
        if ($guardar) //GUARDAR!
        {
            $sql = "SELECT 1 FROM crm_campanas_emails_contactos WHERE contacto_id='$contacto_id' AND campana_id='$campana_id'";
            if ($db->sql_numrows($db->sql_query($sql)) == 0) //si no está ya, agregar
            {
                $sql = "INSERT INTO crm_campanas_emails_contactos (campana_id, contacto_id) VALUES('$campana_id','$contacto_id')";
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
//inicializar los selects
require_once("$_includesdir/select.php");
$select_entidades = select_entidades_federativas($entidad_id);
$select_dia = select_dia($dia);
$select_mes = select_mes($mes);
$select_ano = select_ano($ano);

$_site_title .= " - $campana";

$sql = "SELECT contacto_id FROM crm_campanas_emails_contactos WHERE campana_id='$campana_id'";
$result = $db->sql_query($sql) or die("Error al contar");
$n = $db->sql_numrows($result);
if ($n)
  $cuantos_contactos = "$n contactos dentro de la campaña.";
?>
