<?php

global $db;

$_email_headers = 'MIME-Version: 1.0' . "\r\n";
$_email_headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

$_email_from = "noreply@pcsmexico.com";
$_email_gerente_gral = "gerardo.garcia@vw.com.mx";
$_email_headers .= "from:$_email_from\r\n";

$sql = "SELECT gid FROM `groups`";
$result = $db->sql_query($sql) or die("Error al consultar los contactos");
while(list($gid) = $db->sql_fetchrow($result))
{
    $sql_tmp = sprintf("SELECT COUNT(contacto_id) FROM crm_contactos_asignados_tmp 
  	                     WHERE gid = '%s' GROUP BY gid", $gid);
    $result_tmp = $db->sql_query($sql_tmp) or die("Error al consultar los contactos");
    list($contactos_asignados) = $db->sql_fetchrow($result_tmp);
    if($contactos_asignados != 0)
    {
        $lista_correos = "";
        $html = "<html>"
  	 	           ."<body>"
  	 	             ."<h1>SE HAN ASIGNADO CONTACTOS A LA CONCESIONARIA $gid</h1><br>"
  	 	                ."Se añadieron $contactos_asignados contactos recientemente en su concesionaria sin asignar a vendedor."
  	 	           ."</body>"
  	 	         ."</html>";
        $sql_mail = "SELECT email FROM users WHERE gid = $gid AND (super = '4' OR super = '6')";
        $result_mail = $db->sql_query($sql_mail) or die($sql_mail . " - Error al consultar los correos");
        if($db->sql_numrows($result_mail) >= 1)
        {
            while(list($email_gtes) = $db->sql_fetchrow($result_mail))
            {
                $lista_correos .= $email_gtes . ",";
            }
            $lista_correos = trim($lista_correos, ",");
            echo $lista_correos . "\n";
            $html_gral .= $html;
            //mail("jgodoy@pcsmexico.com", "Contactos asignados ".date("Y-m-d"), $html, $_email_headers);
        //mail("otoral@pcsmexico.com", "Contactos asignados de VW ".date("Y-m-d"), $html, $_email_headers);
        //$_email_gerente_gral
        	mail($lista_correos, "Contactos asignados ".date("Y-m-d"), $html, $_email_headers);
        }
        $sql_del = sprintf("DELETE FROM crm_contactos_asignados_tmp WHERE gid = '%s'",$gid);
  	 	$result_del = $db->sql_query($sql_del) or die($sql_del." - Error al eliminar los contactos");
    }

}
mail("orangel@pcsmexico.com", "Contactos asignados " . date("Y-m-d"), $html_gral, $_email_headers);

?>