<?php
/* 
 * Script para contabilizar los prospectos cargados por dia:
 */

global $db;
$fechai = $argv[2];
$fechac = $argv[3];
if($fechai=='')     $fechai=date('Y-m-d');
if($fechac=='')     $fechac=date('Y-m-d');

$fechai=$fechai.' 00:01:01';
$fechac=$fechac.' 23:59:59';

/** Sacamos a los prospectos que tenemos en el historial  ***/
require_once("$_includesdir/mail.php");
include("$_includesdir/select.php");

$array_prospectos=array();
$sql="SELECT timestamp,archivo,reg_esperados,reg_procesados,reg_insertados,reg_rechazados,reg_alertas
      FROM crm_prospectos_cargados_call_center
      WHERE timestamp  BETWEEN '".$fechai."' AND '".$fechac."'
      ORDER BY id;";
$res=$db->sql_query($sql) or die("Error en la consulta:  ".$sql);
$num=$db->sql_numrows($res);
if($num > 0)
{
    $buf="<br>Prospectos Cargados al sistema el dia ".substr($fechai,0,10)."<br><br>";
    $buf.="<br>Numero de cargas realizadas:  ".$num."<br><br>Listado de cargas realizadas<br>";
    $total_proc=0;
    $total_inse=0;
    $total_rech=0;
    $total_aler=0;

    $buf.="<table width='80%' align='center' style='border:1px solid #cdcdcd;'>
            <thead><tr bgcolor='#cdcdcd'>
                <th align='center'>Fecha de Carga</th>
                <th align='center'>Prospectos Procesados</th>
                <th align='center'>Prospectos Insertados</th>
                <th align='center'>Prospectos Rechazados</th>
           </tr></thead><tbody>";

    while(list($timestamp,$archivo,$reg_esperados,$reg_procesados,$reg_insertados,$reg_rechazados,$reg_alertas) = $db->sql_fetchrow($res))
    {
        $total_proc=$total_proc + $reg_procesados;
        $total_inse=$total_inse + $reg_insertados;
        $total_rech=$total_rech + $reg_rechazados;
        $total_aler=$total_aler + $reg_alertas;
        $buf.="<tr>
                <td align='left'>".$timestamp."</td>
                <td align='center'>".$reg_procesados."</td>
                <td align='center'>".$reg_insertados."</td>
                <td align='center'>".$reg_rechazados."</td>
                </tr>";
    }
    $buf.="</tbody>
            <thead>
            <tr bgcolor='#cdcdcd'>
                <th align='center'>Totales</th>
                <th align='center'>".$total_proc."</th>
                <th align='center'>".$total_inse."</th>
                <th align='center'>".$total_rech."</th>
            </tr></thead></table>";

}
$buf.="\n\n";
$_email_headers  = 'MIME-Version: 1.0' . "\r\n";
$_email_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$_email_from = "noreply@pcsmexico.com";
$_email_headers .= "from:$_email_from\r\n";

mail("orangel@pcsmexico.com", "Totales de Carga de prospectos del dia ".date("Y-m-d"), $buf, $_email_headers);
mail("lahernandez@pcsmexico.com", "Totales de Carga de prospectos del dia ".date("Y-m-d"), $buf, $_email_headers);
mail($_email_gerente_gral, "Totales de Carga de prospectos del dia ".date("Y-m-d"), $buf, $_email_headers);
die($buf);
?>