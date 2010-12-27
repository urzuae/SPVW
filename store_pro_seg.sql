DELIMITER ;;
    DROP PROCEDURE IF EXISTS StoreSeg;
    CREATE PROCEDURE StoreSeg(IN gid int)
    BEGIN
        DECLARE hrs_atencion   int default 0;
        DECLARE hrs_compromiso int default 0;

        /*CREATE TABLE temporal as SELECT DISTINCT b.contacto_id,a.gid,a.uid,b.id,b.user_id,b.inicio,b.fin,b.fecha_cita,b.timestamp,0 as status,0000000000 as evento_id,0000000000 as cierre_id FROM crm_contactos a LEFT JOIN crm_campanas_llamadas b on a.contacto_id=b.contacto_id where a.gid=gid;
        UPDATE temporal AS c set c.status=1 WHERE c.id in (select b.llamada_id from crm_campanas_llamadas_eventos b where b.uid=c.uid and c.gid=gid);
        UPDATE temporal AS c set c.evento_id=(select distinct b.evento_id FROM crm_campanas_llamadas_eventos b where b.uid=c.uid and c.id=b.llamada_id order by evento_id desc limit 1) WHERE c.status=1;
        UPDATE temporal AS c set c.cierre_id=(select distinct b.cierre_id FROM crm_campanas_llamadas_eventos_cierres b where b.uid=c.uid and c.evento_id=b.evento_id order by b.cierre_id desc limit 1) WHERE c.status=1;
        UPDATE temporal AS c set c.status=2 WHERE c.uid=0;
        SELECT COUNT(b.fecha_cita) as total_compromiso,SUM(TIMESTAMPDIFF(HOUR,b.fecha_cita,now())) as hrs_compromiso,max(TIMESTAMPDIFF(HOUR,b.fecha_cita,now())) as max__compromiso,avg(TIMESTAMPDIFF(HOUR,b.fecha_cita,now())) as pro_compromiso from temporal b where b.gid=gid and b.status=1 and b.cierre_id=0 and b.fecha_cita <= NOW() group by b.gid;
        SELECT COUNT(b.timestamp)  as total_atencion  ,SUM(TIMESTAMPDIFF(HOUR,b.timestamp,now()))  as hrs_atencion,max(TIMESTAMPDIFF(HOUR,b.timestamp,now())) as max_atencion,avg(TIMESTAMPDIFF(HOUR,b.timestamp,now())) as pro_atencion FROM temporal a,unicos_log b where a.status=0 and a.gid=gid and a.contacto_id=b.contacto_id  group by a.gid;
        DROP TABLE temporal;*/
        CREATE TABLE temporal as SELECT DISTINCT b.contacto_id,a.gid,a.uid,b.id,b.user_id,b.inicio,b.fin,b.fecha_cita,b.timestamp,1 as status FROM crm_contactos a LEFT JOIN crm_campanas_llamadas b on a.contacto_id=b.contacto_id where a.gid=gid;
        UPDATE temporal AS c set c.status=0 WHERE c.id NOT IN (select b.llamada_id from crm_campanas_llamadas_eventos b where b.uid=c.uid and c.gid=gid);
        UPDATE temporal AS c set c.status=2 WHERE c.uid=0;
        SELECT COUNT(b.timestamp)  as total_atencion  ,SUM(TIMESTAMPDIFF(HOUR,b.timestamp,now()))  as hrs_atencion,max(TIMESTAMPDIFF(HOUR,b.timestamp,now())) as max_atencion,avg(TIMESTAMPDIFF(HOUR,b.timestamp,now())) as pro_atencion FROM temporal a,unicos_log b where a.status=0 and a.gid=gid and a.contacto_id=b.contacto_id  group by a.gid;
        DROP TABLE temporal;
    END ;;
DELIMITER ;
