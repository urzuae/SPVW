<? 

if (!defined('_IN_ADMIN_MAIN_INDEX')) {
    die ("No puedes acceder directamente a este archivo...");
}

global $db, $gid, $submit,$listPlazas;
$warning = "";
$leyend = "";
$style = "style='width:250px;'";
if($submit)
{    
    $sqlGetConcesionaria = "select gid, plaza_id from crm_plazas_concesionarias where gid='$gid'";
    $result = $db->sql_query($sqlGetConcesionaria) or die("Error al comprobar si existe la concesionaria->".$sqlGetConcesionaria);
    if($db->sql_numrows($result))
    {
        $sqlUpdatePlazasConcesionarias = "update crm_plazas_concesionarias set plaza_id='$listPlazas' where gid='$gid'";
        $db->sql_query($sqlUpdatePlazasConcesionarias) or die("Error al actualizar la relacion plaza-concesionaria->".$sqlUpdatePlazasConcesionarias);
    }
    else
    {
        $sqlInserPlazaConcesionaria= "insert into crm_plazas_concesionarias values('$gid','$listPlazas')";
        $db->sql_query($sqlInserPlazaConcesionaria) or die("Error al insertar registro en la relacion  plaza-concesionaria->".$sqlInserPlazaConcesionaria);
    }
    $updateGroupsUbications="UPDATE groups_ubications SET region_id=".$_GET['listRegiones'].",zona_id=".$_GET['listZonas'].", entidad_id=".$_GET['listEntidades'].", plaza_id=".$_GET['listPlazas']." WHERE gid=".$gid.";";
    $db->sql_query($updateGroupsUbications) or die("Error al actualizar la tabla de ubicaciones de concesionarias".$updateGroupsUbications);

    $update="UPDATE reporte_contactos_asignados SET region_id=".$_GET['listRegiones'].",zona_id=".$_GET['listZonas'].", entidad_id=".$_GET['listEntidades'].", plaza_id=".$_GET['listPlazas']." WHERE gid=".$gid.";";
    $db->sql_query($update) or die("Error al actualizar la tabla de ubicaciones de concesionarias".$update);
    $leyend = "Datos actualizados exitosamente";
}

/*
 * Obtener datos de la concesionaria
 */
$sqlConcesionarias = "select gid, name from groups where gid='$gid'";
$resultConcesionarias = $db->sql_query($sqlConcesionarias) or
die("Error al obtener la lista de concesionarias->".$sqlConcesionarias);
list($id, $nameConcesionaria) = $db->sql_fetchrow($resultConcesionarias);
$nombreConcesionaria = $nameConcesionaria;

/*
 * Lista de regiones, asociar la regiona a la concesionaria seleccionada
 */
$sqlRegionesIds = "select distinct(zonas.region_id), zonas.zona_id, ze.entidad_id,plazas.plaza_id
                   from crm_zonas as zonas, crm_zonas_entidad as ze, crm_plazas as plazas,
                   crm_plazas_concesionarias as pc where pc.plaza_id=plazas.plaza_id and
                   plazas.entidad_id=ze.entidad_id and ze.zona_id=zonas.zona_id and pc.gid='$gid'";
$resultRegionId = $db->sql_query($sqlRegionesIds) or die("Error al obtener el id de regiones->".$sqlRegionesIds);
list($regionId, $zonaId,$entidadId,$plazaId) = $db->sql_fetchrow($resultRegionId);
$listRegiones = "";
$optionsRegiones = "";
$sqlRegiones = "select region_id, nombre from crm_regiones";
$resultRegiones = $db->sql_query($sqlRegiones) or die("Error al obtener las regiones");
while(list($id , $nombre) = $db->sql_fetchrow($resultRegiones))
{
    if($id==$regionId)
    $selected = "selected='selected'";
    $optionsRegiones .="<option value='$id' $selected>$nombre</option>";
    $selected = "";
}
$listRegiones = "<select name='listRegiones' $style id='listRegiones'>".$optionsRegiones."</select>";

if($regionId != "")
{
    $sqlZonas = "select  distinct(zona_id), nombre from crm_zonas where region_id='$regionId'";
    $sqlEntidades = "select distinct(entidades.id_entidad), entidades.nombre from crm_entidades as entidades,
    crm_zonas_entidad as ze where entidades.id_entidad=ze.entidad_id and ze.zona_id='$zonaId'";
    $sqlPlazas = "select distinct(plaza_id), nombre from crm_plazas where entidad_id='$entidadId'";
}
else
{
    $sqlZonas = "select distinct(zona_id), nombre from crm_zonas";
    $sqlEntidades = "select id_entidad,nombre from crm_entidades";
    $sqlPlazas = "select distinct(plaza_id), nombre from crm_plazas";
    $warning = "No se han encontrado datos geograficos asociados con el grupo.
    Por favor eliga de las opciones mostrados arriba para actualizar el grupo";
}

/*
 * Obtener las zonas asociadas a la region
 */
$listZonas = "";
$optionsZonas = "";
$resultZonas = $db->sql_query($sqlZonas) or die("Error al obtener las zonas");
while(list($zona_id,$nombre) = $db->sql_fetchrow($resultZonas))
{
    if($zona_id == $zonaId)
    $selected = "selected='selected'";
    $optionsZonas .= "<option value='$zona_id' $selected>$nombre</option>";
    $selected ="";
}
$listZonas = "<select name='listZonas' $style id='listZonas'>".$optionsZonas."</select>";

/*
 * Obtener las entidades asociadas a la zona
 */
$listEntidades = "";
$optionsEntidades = "";
$resultEntidades = $db->sql_query($sqlEntidades);
while(list($id_entidad,$nombre) = $db->sql_fetchrow($resultEntidades))
{
    if($id_entidad == $entidadId)
    $selected = "selected='selected'";
    $optionsEntidades .= "<option value='$id_entidad' $selected>$nombre</option>";
    $selected = "";
}
$listEntidades .= "<select name='listEntidades' $style id='listEntidades'>".$optionsEntidades."</select>";

/*
 * Obtener las plazas asociadas a la entidad
 */
$listPlazas = "";
$optionsPlazas = "";
$resultPlazas = $db->sql_query($sqlPlazas);
while(list($plaza_id, $nombre) = $db->sql_fetchrow($resultPlazas))
{
    if($plaza_id == $plazaId)
    $selected = "selected='selected'";
    $optionsPlazas .= "<option value='$plaza_id' $selected>$nombre</option>";
    $selected = "";
}
$listPlazas = "<select name='listPlazas' $style id='listPlazas'>".$optionsPlazas."</select>";

?>