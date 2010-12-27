<?php
/**** Este script sirve para calcular las horas de retraso en atencion (asignacion),
 *    y el numero de reasignaciones de un prospecto, cada que calcula deja el dato de
 *    la fecha, en que fue calculado.
 */
global $db;
$arg = $argv[0];
$fechai=$argv[2];
$fechac=$argv[3];

if( (empty($fechai)) && (empty($fechac)) )
    $fechai=date('Y-m-d');

if ( empty($fechac) )
    $fechac=$fechai;

// Nombre de la tabla temporal
$tablename_conn="reporte_contactos_asignados";

// recupero el catalogo de origenes
$array_origenes = origenes($db);
$array_concesionarias = concesionarias($db);
$array_vendedores = vendedores($db);

//calcula tiempo segumiento
include_once ("libForHRAC.php");
updateReportAssignedContactForDay($db, $fechai, $fechac);

/**********   FUNCIONES AUXILIARES  RECUPERACION DE CATALOGOS *******/

// CATALOGOS DE FUENTES
function origenes($db)
{
    $sql_tmp="SELECT padre_id,hijo_id from crm_fuentes_arbol WHERE padre_id > 0 ORDER BY hijo_id;";
    $res_tmp=$db->sql_query($sql_tmp) or die($sql_tmp);
    if($db->sql_numrows($res_tmp) > 0)
    {
        while($rs=$db->sql_fetchrow($res_tmp))
        {
            $array_tmp[$rs['hijo_id']]=$rs['padre_id'];
        }
    }
    return $array_tmp;
}

// CATALOGOS DE VENDEDORES
function vendedores($db)
{
    $sql_tmp="SELECT uid,name from users ORDER BY uid;";
    $res_tmp=$db->sql_query($sql_tmp) or die($sql_tmp);
    if($db->sql_numrows($res_tmp) > 0)
    {
        while($rs=$db->sql_fetchrow($res_tmp))
        $array_tmp[$rs['uid']]=$rs['name'];
    }
    return $array_tmp;
}

// CATALOGOS DE CONCESIONARIAS
function concesionarias($db)
{
    $sql_tmp="SELECT gid,name from groups_ubications WHERE gid>0 ORDER BY gid;";
    $res_tmp=$db->sql_query($sql_tmp) or die($sql_tmp);
    if($db->sql_numrows($res_tmp) > 0)
    {
        while($rs=$db->sql_fetchrow($res_tmp))
        {
            $array_tmp[$rs['gid']]=$rs['name'];
        }
    }
    return $array_tmp;
}
?>
