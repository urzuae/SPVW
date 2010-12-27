<?
  if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}
global $db, $submit, $campana_id, $file, $del;

$dir = "$_module/files/$campana_id";
if (!$campana_id) header("location: index.php?_module=$_module");
else
{
    $sql = "SELECT nombre FROM crm_campanas WHERE campana_id='$campana_id'";
    $result = $db->sql_query($sql) or die("Error al cargar campaña".print_r($db->sql_error()));
    list($campana) = $db->sql_fetchrow($result);
    if (!file_exists($dir))
    {
      mkdir("$dir");
      chmod("$dir", 0777);
    }
    if ($_FILES['file']['name'] && $submit)
    {
                $tmp_name = $_FILES['file']['tmp_name'];
                $new_name = $_FILES['file']['name'];
                move_uploaded_file($tmp_name, "$dir/$new_name");
                chmod("$dir/$new_name", 0666);
                $msg  = "<h1>Archivo \"$new_name\" subido con éxito</h1>";
    }
    else if ($submit && $_FILES['file']['name'] == '')
    {
      header("location:index.php?_module=$_module&_op=campana&campana_id=$campana_id");
    }
    if(isset($del) && $del != "")
    {
        unlink("$dir/$del");
        $msg  = "<h1>Archivo \"$del\" borrado con éxito</h1>";
    }

    $list .= "<table style=\"width:100%;\"><thead><tr><td  style=\"width:60%;\">Nombre</td><td  style=\"width:10%;\">Tamaño (Bytes)</td><td   style=\"width:25%;\">Última modificación</td><td  style=\"width:5%;\">Borrar</td></tr></thead>";
    $dir_handle = @opendir("$dir") or die("No se puede leer el directorio $dir");
    while ($file = readdir($dir_handle))
    {
        if ((strpos($file, ".") === 0)) //si enkontramos archivo que empieze por . no mostrar
            continue;
        $i++;
        $fp = fopen("$dir/$file", "r") ;
        $fstat = fstat($fp);
        fclose($fp);
        if (!($c++ % 2))
            $class = "row1";
        else 
            $class = "row2";
        $list .= "<tr class=\"$class\"><td><a href=\"$_module/files/$campana_id/$file\" target=\"documento_de_apoyo\">$file</a></td>"
              ."<td align=right>".$fstat['size']."</td>\n"
              ."<td>".date('d-m-Y H:i:s', $fstat['mtime'])."</td>\n"
              ."<td align=right><a href=\"#\" onclick=\"del('$file')\"><img src=\"../img/del.gif\" onmouseover=\"return escape('Borrar')\"  border=0></a></td>\n";
        $list .= "</tr>\n";
    }
    closedir($dir_handle);
    $list .= "</table>\n";
    if (!$i) $list = "";//borra 
}

?>