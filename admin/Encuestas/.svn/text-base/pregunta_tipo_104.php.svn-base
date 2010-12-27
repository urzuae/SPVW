<? 
if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $pregunta_id, $valor, $submit, $encuesta_id;

if (!$pregunta_id) header("index.php?_module=$_module");

$name = "../modules/$_module/files/$pregunta_id.jpg";

if ($submit)
{
  $fn = ($_FILES['f']['tmp_name']);
  
  if (is_uploaded_file($fn))
  {
    move_uploaded_file($fn, $name);
    chmod($name, 0666);
  }
}

if (file_exists($name)) 
{
  $link = "<a href=\"$name\" target=\"Imagen\">Imagen actual</a>";
}


?> 
