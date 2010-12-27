#!/bin/bash

cd /var/www/vw/;
#/usr/local/bin/wget g3n.dyndns.org/vw_files/rptCFG.txt;
/usr/bin/php /var/www/vw/script.php carga_portal rptCFG.txt;
/usr/bin/php /var/www/vw/pruebas/script.php carga_portal_no_asignados ../rptCFG.txt;
 mysql -u root crm_prospectos_pruebas --password=MuchosHuevos < pruebas.sql 

#mover la anterior
mv /var/www/vw/rptCFG.txt /var/www/vw/bk/rptCFG-`date "+%Y%m%d%H%M%S"`.txt;
mv /var/www/vw/carga_portal-*.log /var/www/vw/bk/;
