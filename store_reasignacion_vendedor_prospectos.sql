DELIMITER ;;
drop procedure if exists reasignaciones_vendedores_prospectos;
create procedure reasignaciones_vendedores_prospectos(IN gid int,IN uid int,IN origen int,IN fechai varchar(10),IN fechac varchar(10))
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
        CREATE TEMPORARY TABLE reasignaciones_vendedor_prospectos AS SELECT b.contacto_id, count(b.contacto_id) -2 as total,MIN(b.timestamp) AS minimo,MAX(b.timestamp) AS maximo  FROM crm_contactos a, crm_contactos_asignacion_log b WHERE a.gid=gid AND a.uid=uid AND a.contacto_id = b.contacto_id  AND a.origen_id=origen_id AND a.timestamp BETWEEN fecha_inicio AND fecha_termino GROUP BY (b.contacto_id) ORDER BY b.contacto_id;
    elseif(origen_id != 0) && (fecha_consulta = '') THEN
        CREATE TEMPORARY TABLE reasignaciones_vendedor_prospectos AS SELECT b.contacto_id, count(b.contacto_id) -2 as total,MIN(b.timestamp) AS minimo,MAX(b.timestamp) AS maximo  FROM crm_contactos a, crm_contactos_asignacion_log b WHERE a.gid=gid AND a.uid=uid AND a.contacto_id = b.contacto_id  AND a.origen_id=origen_id GROUP BY (b.contacto_id) ORDER BY b.contacto_id;
    elseif(origen_id = 0) && (fecha_consulta != '') THEN
        CREATE TEMPORARY TABLE reasignaciones_vendedor_prospectos AS SELECT b.contacto_id, count(b.contacto_id) -2 as total,MIN(b.timestamp) AS minimo,MAX(b.timestamp) AS maximo  FROM crm_contactos a, crm_contactos_asignacion_log b WHERE a.gid=gid AND a.uid=uid AND a.contacto_id = b.contacto_id  AND a.timestamp BETWEEN fecha_inicio AND fecha_termino GROUP BY (b.contacto_id) ORDER BY b.contacto_id;
    else
        CREATE TEMPORARY TABLE reasignaciones_vendedor_prospectos AS SELECT b.contacto_id, count(b.contacto_id) -2 as total,MIN(b.timestamp) AS minimo,MAX(b.timestamp) AS maximo  FROM crm_contactos a, crm_contactos_asignacion_log b WHERE a.gid=gid AND a.uid=uid AND a.contacto_id = b.contacto_id  GROUP BY (b.contacto_id) ORDER BY b.contacto_id;
    end IF;


    select a.contacto_id,a.total,a.maximo,a.minimo from reasignaciones_vendedor_prospectos a where a.contacto_id > 0 group by a.contacto_id order by a.contacto_id;
    DROP TABLE reasignaciones_vendedor_prospectos;
END ;;
DELIMITER ;