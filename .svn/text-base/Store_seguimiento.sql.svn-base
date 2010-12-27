DELIMITER ;;
    DROP PROCEDURE IF EXISTS Store2;
    CREATE PROCEDURE Store2(IN gid int)
    BEGIN
        DECLARE tot_c int default 0;
        DECLARE hrs_c int default 0;
        DECLARE pro_c int default 0;
        DECLARE max_c int default 0;
        DECLARE tot_a int default 0;
        DECLARE hrs_a int default 0;
        DECLARE pro_a int default 0;
        DECLARE max_a int default 0;

        CREATE TEMPORARY TABLE datos (gid int(10),total_com int(10),hrs_com int(10),prom_com decimal(12,2), max_com int(10), total_aten int(10),hrs_aten int(10), prom_aten decimal (12,2),max_aten int(10));
        CREATE TEMPORARY TABLE temporal AS SELECT DISTINCT b.contacto_id,a.gid,a.uid,b.id,b.user_id,b.inicio,b.fin,b.fecha_cita,b.timestamp,0 as status,0000000000 as evento_id,0000000000 as cierre_id FROM crm_contactos a LEFT JOIN crm_campanas_llamadas b on a.contacto_id=b.contacto_id WHERE a.gid=gid;
        UPDATE temporal as c SET c.status=1 WHERE c.id in (select b.llamada_id from crm_campanas_llamadas_eventos b where b.uid=c.uid and c.gid=gid);
        UPDATE temporal as c SET c.evento_id=(select distinct b.evento_id FROM crm_campanas_llamadas_eventos b where b.uid=c.uid and c.id=b.llamada_id order by evento_id desc limit 1) WHERE c.status=1;
        UPDATE temporal as c SET c.cierre_id=(select distinct b.cierre_id FROM crm_campanas_llamadas_eventos_cierres b where b.uid=c.uid and b.evento_id=c.evento_id order by b.cierre_id desc limit 1) WHERE c.status=1;
        UPDATE temporal SET status=2 WHERE uid=0;
        SELECT COUNT(b.fecha_cita),SUM(TIMESTAMPDIFF(HOUR,b.fecha_cita,now())),MAX(TIMESTAMPDIFF(HOUR,b.fecha_cita,now())),AVG(TIMESTAMPDIFF(HOUR,b.fecha_cita,now())) INTO tot_c, hrs_c, max_c, pro_c FROM temporal    as b WHERE b.gid=gid AND b.status=1 AND b.cierre_id=0 AND b.fecha_cita<=now() GROUP BY b.gid;
        SELECT COUNT(a.timestamp) ,SUM(TIMESTAMPDIFF(HOUR,a.timestamp,now())), MAX(TIMESTAMPDIFF(HOUR,a.timestamp,now())), AVG(TIMESTAMPDIFF(HOUR,a.timestamp,now()))  INTO tot_a, hrs_a, max_a, pro_a FROM unicos_log  as a WHERE a.gid=gid AND a.contacto_id IN (SELECT b.contacto_id FROM temporal b WHERE b.status=0) GROUP BY a.gid;
        INSERT INTO datos (gid,total_com,hrs_com,prom_com,max_com,total_aten,hrs_aten,prom_aten,max_aten) VALUES (gid,tot_c,hrs_c,max_c,pro_c,tot_a, hrs_a, max_a, pro_a);
        SELECT * FROM datos;
        DROP TABLE temporal;
        DROP TABLE datos;
    END ;;
DELIMITER ;