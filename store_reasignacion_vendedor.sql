DELIMITER ;;
drop procedure if exists reasignaciones_vendedores;
create procedure reasignaciones_vendedores(IN gid int,IN origen int,IN fechai varchar(10),IN fechac varchar(10))
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
        CREATE TEMPORARY TABLE reasignaciones_vendedor AS SELECT a.uid,count(b.contacto_id) as total  FROM crm_contactos a, crm_contactos_asignacion_log b WHERE a.gid=gid AND a.contacto_id = b.contacto_id  AND a.origen_id=origen_id AND a.timestamp BETWEEN fecha_inicio AND fecha_termino GROUP BY (b.contacto_id) HAVING count( b.contacto_id ) >2 ORDER BY b.contacto_id;
    elseif(origen_id != 0) && (fecha_consulta = '') THEN
        CREATE TEMPORARY TABLE reasignaciones_vendedor AS SELECT a.uid, count(b.contacto_id) as total  FROM crm_contactos a, crm_contactos_asignacion_log b WHERE a.gid=gid AND a.contacto_id = b.contacto_id  AND a.origen_id=origen_id GROUP BY (b.contacto_id) HAVING count( b.contacto_id ) >2 ORDER BY b.contacto_id;
    elseif(origen_id = 0) && (fecha_consulta != '') THEN
        CREATE TEMPORARY TABLE reasignaciones_vendedor AS SELECT a.uid, count(b.contacto_id) as total  FROM crm_contactos a, crm_contactos_asignacion_log b WHERE a.gid=gid AND a.contacto_id = b.contacto_id AND a.timestamp BETWEEN fecha_inicio AND fecha_termino GROUP BY (b.contacto_id) HAVING count( b.contacto_id ) > 2 ORDER BY b.contacto_id;
    else
        CREATE TEMPORARY TABLE reasignaciones_vendedor AS SELECT a.uid, count(b.contacto_id) as total  FROM crm_contactos a, crm_contactos_asignacion_log b WHERE a.gid=gid AND a.contacto_id = b.contacto_id  GROUP BY (b.contacto_id) HAVING count( b.contacto_id ) >2 ORDER BY b.contacto_id;
    end IF;


    select a.uid,count(a.uid),sum(a.total -2),max(a.total -2) from reasignaciones_vendedor a where a.uid > 0 group by a.uid order by a.uid;
    DROP TABLE reasignaciones_vendedor;
END ;;
DELIMITER ;