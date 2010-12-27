<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit, $fecha_ini, $fecha_fin, $_csv_delimiter;

if ($submit)
{
  $file = "$_module/files/contactos_modificados.csv";;
  if ($fecha_ini)
  {
    $where .= " AND timestamp>='".date_reverse($fecha_ini)."' ";
  }
  if ($fecha_fin)
  {
    $where .= " AND timestamp<='".date_reverse($fecha_fin)."' ";
  }
  
  $sql = "SELECT * FROM crm_contactos WHERE 1 $where";
  $result = $db->sql_query($sql) or die("Error al leer contactos");
  if ($db->sql_numrows($result) > 0)
  {
    include("$_includesdir/select.php");
    global $_edo_civil;
    $fh = fopen("$file", "w");
    while($row = $db->sql_fetchrow($result))
    {
      list(
            $contacto_id, 
            $nombre,
            $apellido_paterno,
            $apellido_materno,
            $sexo,
            $compania,
            $cargo,
            $tel_casa,
            $tel_oficina,
            $tel_movil,
            $tel_otro,
            $email,
            $domicilio,
            $colonia,
            $cp,
            $poblacion,
            $entidad_id,
            $rfc,
            $persona_moral,
            $fecha_de_nacimiento,
            $ocupacion,
            $edo_civil,
            $nombre_conyugue,
            $no_contactar,
            $timestamp
            ) = $row;
      $sexo = $sexo?"F":"M";
      $edo_civil = $_edo_civil[$edo_civil];
      $array_csv = array(
            $contacto_id,
            $nombre,
            $apellido_paterno,
            $apellido_materno,
            $sexo,
            $compania,
            $cargo,
            $tel_casa,
            $tel_oficina,
            $tel_movil,
            $tel_otro,
            $email,
            $domicilio,
            $colonia,
            $cp,
            $poblacion,
            $entidad_id,
            $rfc,
            $persona_moral,
            $fecha_de_nacimiento,
            $ocupacion,
            $edo_civil,
            $nombre_conyugue,
            $no_contactar,
            $timestamp
            );
      fputcsv($fh, $array_csv, $_csv_delimiter);
    }
    fclose($fh);
    header("location: $file");
  }
}



// global $_admin_menu2, $_admin_menu;
// $_admin_menu2 .= "<br>
// <a href=\"index.php?_module=$_module&_op=contactos_modificados\" >Contactos modificados</a><br>
// ";
 ?>