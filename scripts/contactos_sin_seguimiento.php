<?php
global $db;
$sql = "SELECT contacto.contacto_id, contacto.uid, contacto.gid, concat_ws( ' ', contacto.nombre,' ',
        contacto.apellido_paterno,' ' ,contacto.apellido_materno ) , contacto.fecha_importado FROM `crm_contactos`
        AS contacto inner join `crm_campanas_llamadas` AS llamada on contacto.contacto_id=llamada.contacto_id
        LEFT JOIN `crm_campanas_llamadas_eventos` AS evento ON llamada.id = evento.llamada_id where
        evento.llamada_id is null";
$result = $db->sql_query($sql) or die("Error al obtener los contactos");
$fp = fopen('contactosSinSeguimiento.csv', 'w');
fputcsv($fp, split(',', "Contacto Id, Uid, Gid, Nombre, Fecha importado"));
while(list($contacto_id,$uid, $gid, $nombre, $fechaImportado) = $db->sql_fetchrow($result))
{
    fputcsv($fp, split(',', "$contacto_id, $uid, $gid, $nombre, $fechaImportado"));
    echo "\n".$contacto_id;
}
fclose($fp);

?>
