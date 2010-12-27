DELIMITER ;;
drop procedure if exists reporte_prospectos;
create procedure reporte_prospectos(IN id int,IN fecha_inicio varchar(19),IN fecha_conluye varchar(19))
BEGIN
    declare gid int;
    declare fecha_i varchar(19);
    declare fecha_c varchar(19);
    set gid=id;
    set fecha_i=fecha_inicio;
    set fecha_c=fecha_conluye;


CREATE TEMPORARY TABLE `vw_gid_llamadas` (
	`id` int(11) not null auto_increment,
	`status_id` int(11) not null,
	`id_campana` int(11) unsigned not null,
	`contacto_id` int(11) unsigned not null,
	`llamada_id` int(11) unsigned not null,
	`nombre_campana` varchar(30) collate latin1_spanish_ci not null,
	`nombre_origen` varchar(30) collate latin1_spanish_ci not null,
	`nombre_prospecto` varchar(50) collate latin1_spanish_ci not null,
	`nombre_vendedor` varchar(50) collate latin1_spanish_ci not null,
	`tiempo_espera` int(6) unsigned,
	`primer_contacto` date not null,
	`ultimo_contacto` date not null,
	`compromiso` datetime not null,
	`tiempo_retraso` int(6) unsigned,
	`total_campana` int(6) unsigned,
	`fecha_importado` varchar(20),
	`tel_casa`  varchar(30) collate latin1_spanish_ci not null,
	`tel_oficina`  varchar(30) collate latin1_spanish_ci not null,    
	`email`  varchar(60) collate latin1_spanish_ci not null,
	`domicilio`  varchar(128) collate latin1_spanish_ci not null,
	`colonia`  varchar(128) collate latin1_spanish_ci not null,
	`cp`  varchar(10) collate latin1_spanish_ci not null,
	`poblacion`  varchar(128) collate latin1_spanish_ci not null,
	`fecha_autorizado` datetime not null,
	`fecha_firmado` datetime not null,
    `tel_movil`  varchar(60) collate latin1_spanish_ci not null,
    `codigo_campana`  varchar(10) collate latin1_spanish_ci not null,
	`medio_entero` int(2) unsigned,
	 KEY `id` (`id`)
);
CREATE TEMPORARY TABLE `vw_gid_llamadas_temp` (
	`id` int(11) not null auto_increment,
	`status_id` int(11) not null,
	`id_campana` int(11) unsigned not null,
	`contacto_id` int(11) unsigned not null,
	`llamada_id` int(11) unsigned not null,
	`nombre_campana` varchar(30) collate latin1_spanish_ci not null,
	`nombre_origen` varchar(30) collate latin1_spanish_ci not null,
	`nombre_prospecto` varchar(50) collate latin1_spanish_ci not null,
	`nombre_vendedor` varchar(50) collate latin1_spanish_ci not null,
	`tiempo_espera` int(6) unsigned,
	`primer_contacto` date not null,
	`ultimo_contacto` date not null,
	`compromiso` datetime not null,
	`tiempo_retraso` int(6) unsigned,
	`fecha_importado` varchar(20),
	`tel_casa`  varchar(30) collate latin1_spanish_ci not null,
	`tel_oficina`  varchar(30) collate latin1_spanish_ci not null,    
	`email`  varchar(60) collate latin1_spanish_ci not null,
	`domicilio`  varchar(128) collate latin1_spanish_ci not null,
	`colonia`  varchar(128) collate latin1_spanish_ci not null,
	`cp`  varchar(10) collate latin1_spanish_ci not null,
	`poblacion`  varchar(128) collate latin1_spanish_ci not null,
	`fecha_autorizado` datetime not null,
	`fecha_firmado` datetime not null,
    `tel_movil`  varchar(60) collate latin1_spanish_ci not null,
    `codigo_campana`  varchar(10) collate latin1_spanish_ci not null,
	`medio_entero` int(2) unsigned,
	KEY `id` (`id`)
);
CREATE TEMPORARY TABLE `vw_gid_primer_contacto` (
	`id` int(11) not null auto_increment,
	`contacto_id` int(11) unsigned not null,
	`campana_id` int(11) unsigned not null,
	`fecha_contacto` date not null,
	KEY `id` (`id`)
);
CREATE TEMPORARY TABLE `vw_gid_ultimo_contacto` (
	`id` int(11) not null auto_increment,
	`contacto_id` int(11) unsigned not null,
	`campana_id` int(11) unsigned not null,
	`fecha_contacto` date not null,
	KEY `id` (`id`)
);


#LLENADO DE TABLA LLAMADAS
INSERT INTO vw_gid_llamadas (
SELECT '',b.status_id,b.campana_id,a.contacto_id,b.id,c.nombre,f.nombre,concat_ws(' ',a.nombre,a.apellido_paterno,a.apellido_materno) as nombre,d.name,if(b.fecha_cita='0000-00-00 00:00:00',0,hour(timestamp(NOW()) - timestamp(b.fecha_cita))) as  horas_retraso,'','',b.fecha_cita,'','',a.fecha_importado,a.tel_casa,a.tel_oficina,a.email,a.domicilio,a.colonia,a.cp,a.poblacion,a.fecha_autorizado,a.fecha_firmado,concat(a.tel_movil,' ',a.tel_movil_2) as tel_movil,a.codigo_campana,a.medio_entero
FROM crm_contactos a
INNER JOIN crm_campanas_llamadas b
ON a.contacto_id = b.contacto_id 
INNER JOIN crm_campanas c
ON b.campana_id = c.campana_id
INNER JOIN users d
ON a.uid = d.uid
INNER JOIN crm_campanas_groups e
ON c.campana_id = e.campana_id
AND e.gid = gid
INNER JOIN crm_fuentes f
ON a.origen_id = f.fuente_id  
WHERE a.fecha_importado BETWEEN fecha_i AND fecha_c
ORDER BY e.campana_id);

#LLENADO DE TABLA LLAMADAS
INSERT INTO vw_gid_llamadas_temp (
SELECT '',b.status_id,b.campana_id,a.contacto_id,b.id,c.nombre,f.nombre,concat_ws(' ',a.nombre,a.apellido_paterno,a.apellido_materno) as nombre,d.name,if(b.fecha_cita='0000-00-00 00:00:00',0,hour(timestamp(NOW()) - timestamp(b.fecha_cita))) as horas_retraso,'','',b.fecha_cita,'',a.fecha_importado,a.tel_casa,a.tel_oficina,a.email,a.domicilio,a.colonia,a.cp,a.poblacion,a.fecha_autorizado,a.fecha_firmado,concat(a.tel_movil,' ',a.tel_movil_2) as tel_movil,a.codigo_campana,a.medio_entero
FROM crm_contactos a
INNER JOIN crm_campanas_llamadas b
ON a.contacto_id = b.contacto_id
INNER JOIN crm_campanas c
ON b.campana_id = c.campana_id
INNER JOIN users d
ON a.uid = d.uid
INNER JOIN crm_campanas_groups e
ON c.campana_id = e.campana_id
AND e.gid = gid
INNER JOIN crm_fuentes f
ON a.origen_id = f.fuente_id 
WHERE a.fecha_importado BETWEEN fecha_i AND fecha_c
ORDER BY e.campana_id
);

#LLENADO DE TABLA PRIMER CONMTACTO
INSERT INTO vw_gid_primer_contacto (
SELECT '',a.contacto_id,b.campana_id,b.timestamp
FROM crm_contactos a
inner join crm_campanas_llamadas_log b
ON a.contacto_id = b.contacto_id
INNER JOIN crm_campanas_groups c
ON b.campana_id = c.campana_id
AND c.gid = gid  
AND a.fecha_importado BETWEEN fecha_i AND fecha_c
ORDER BY b.timestamp ASC
);
#LLENADO DE TABLA ULTIMO CONTACTO
INSERT INTO vw_gid_ultimo_contacto (
SELECT '',a.contacto_id,b.campana_id,b.timestamp
FROM crm_contactos a
inner join crm_campanas_llamadas_log b
ON a.contacto_id = b.contacto_id
INNER JOIN crm_campanas_groups c
ON b.campana_id = c.campana_id
AND c.gid = gid 
AND  a.fecha_importado BETWEEN fecha_i AND fecha_c
ORDER BY b.timestamp DESC
);
UPDATE vw_gid_llamadas a SET a.primer_contacto = (SELECT b.fecha_contacto FROM vw_gid_primer_contacto b WHERE contacto_id = a.contacto_id ORDER BY b.fecha_contacto ASC LIMIT 1) WHERE contacto_id = a.contacto_id;
UPDATE vw_gid_llamadas a SET a.ultimo_contacto = (SELECT b.fecha_contacto FROM vw_gid_ultimo_contacto b WHERE contacto_id = a.contacto_id ORDER BY b.fecha_contacto DESC LIMIT 1) WHERE contacto_id = a.contacto_id;
UPDATE vw_gid_llamadas a SET a.total_campana = (SELECT count(*) FROM vw_gid_llamadas_temp b WHERE nombre_campana = a.nombre_campana ) WHERE nombre_campana = a.nombre_campana;

SELECT id_campana,status_id,nombre_campana,nombre_origen,nombre_prospecto,nombre_vendedor,TIMESTAMPDIFF(hour,ultimo_contacto,compromiso) as espera,primer_contacto,ultimo_contacto,compromiso,IF(compromiso='0000-00-00 00:00:00','',IF(TIMESTAMPDIFF(hour,compromiso,NOW())<0,'',TIMESTAMPDIFF(hour,compromiso,NOW()))) AS retraso,contacto_id,llamada_id,total_campana,fecha_importado,tel_casa,tel_oficina,email,domicilio,colonia,cp,poblacion,fecha_autorizado,fecha_firmado,tel_movil,codigo_campana,medio_entero FROM vw_gid_llamadas ORDER BY nombre_campana;
DROP TABLE vw_gid_llamadas;
DROP TABLE vw_gid_llamadas_temp;
DROP TABLE vw_gid_primer_contacto;
DROP TABLE vw_gid_ultimo_contacto;
END ;;
DELIMITER ;