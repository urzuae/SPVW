#!/usr/local/bin/bash

cd /var/www/vw/;
mysqldump -u root --password=MuchosHuevos crm_prospectos > crm_prospectos.sql;
mysql -u root --password=MuchosHuevos crm_prospectos_pruebas < crm_prospectos.sql;
echo "update users set password=''" | mysql -u root  --password=MuchosHuevos  crm_prospectos_pruebas
#echo "Ambiente de prueba copiado a vw_pruebas" |mail -s "Ambiente de prueba copiado" omael

mysql -u root --password=MuchosHuevos crm_prospectos_pruebas < pruebas.sql;
#mover la anterior
bzip2 /var/www/vw/crm_prospectos.sql
mv /var/www/vw/crm_prospectos.sql.bz2 /var/www/vw/bk/crm_prospectos-`date "+%Y%m%d%H%M%S"`.sql.bz2;

rm -f /var/www/vw/*.csv
