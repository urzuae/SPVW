DELIMITER ;;
drop procedure if exists reasignaciones;
create procedure reasignaciones(IN origen int,IN fechai varchar(10),IN fechac varchar(10))
BEGIN
    declare origen_id int;
    declare fecha_inicio varchar(10);
    declare fecha_termino varchar(10);
    declare fecha_consulta varchar(10);
    set origen_id=origen;

    IF(fechai = '') && (fechac = '') THEN
        set fecha_consulta='';
    END IF;

    IF(fechai != '') && (fechac = '') THEN
        set fecha_consulta='fechas';
        set fecha_inicio=fechac;
        set fecha_termino=fechac;
    END IF;

    IF(fechai = '') && (fechac != '') THEN
        set fecha_consulta='fechas';
        set fecha_inicio=fechai;
        set fecha_termino=fechai;
    END IF;

    IF(fechai != '') && (fechac != '') THEN
        set fecha_consulta='fechas';
        set fecha_inicio=fechai;
        set fecha_termino=fechac;
    END IF;


    if(origen_id != 0) && (fecha_consulta != '') THEN
        CREATE TEMPORARY TABLE reasignaciones  AS SELECT a.gid,b.contacto_id, count(b.contacto_id) as total  FROM crm_contactos a, crm_contactos_asignacion_log b WHERE a.contacto_id = b.contacto_id  AND a.uid > 0 AND a.origen_id=origen_id AND a.timestamp BETWEEN fecha_inicio AND fecha_termino GROUP BY (b.contacto_id) HAVING count( b.contacto_id ) >2 ORDER BY b.contacto_id;
    elseif(origen_id != 0) && (fecha_consulta = '') THEN
        CREATE TEMPORARY TABLE reasignaciones  AS SELECT a.gid,b.contacto_id, count(b.contacto_id) as total  FROM crm_contactos a, crm_contactos_asignacion_log b WHERE a.contacto_id = b.contacto_id  AND a.uid > 0 AND a.origen_id=origen_id GROUP BY (b.contacto_id) HAVING count( b.contacto_id ) >2 ORDER BY b.contacto_id;
    elseif(origen_id = 0) && (fecha_consulta != '') THEN
        CREATE TEMPORARY TABLE reasignaciones  AS SELECT a.gid,b.contacto_id, count(b.contacto_id) as total  FROM crm_contactos a, crm_contactos_asignacion_log b WHERE a.contacto_id = b.contacto_id AND a.uid > 0 AND a.timestamp BETWEEN fecha_inicio AND fecha_termino GROUP BY (b.contacto_id) HAVING count( b.contacto_id ) > 2 ORDER BY b.contacto_id;
    else
        CREATE TEMPORARY TABLE reasignaciones  AS SELECT a.gid,b.contacto_id, count(b.contacto_id) as total  FROM crm_contactos a, crm_contactos_asignacion_log b WHERE a.contacto_id = b.contacto_id AND a.uid > 0 GROUP BY (b.contacto_id) HAVING count( b.contacto_id ) >2 ORDER BY b.contacto_id;
    end IF;


    select a.gid,count(a.gid),sum(total -2) from reasignaciones a group by a.gid order by a.gid;
    DROP TABLE reasignaciones;
END ;;
DELIMITER ;