<?
  if (!defined('_IN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $uid;
if (!$uid) $no_registrado = " Para operarlo ingrese su usuario y password (este es sensible a mayúsculas y minúsculas).";
else $no_registrado = " Para operar el sistema, favor de seleccionar una opción del menú de la izquierda.";
?>