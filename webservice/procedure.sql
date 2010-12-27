DELIMITER ;;
    DROP PROCEDURE IF EXISTS Concesionarias;
    CREATE PROCEDURE Concesionarias()
    BEGIN
        SELECT gid,name FROM groups WHERE gid>3 ORDER BY gid;
    END ;;
DELIMITER ;

DELIMITER ;;
    DROP PROCEDURE IF EXISTS Modelos;
    CREATE PROCEDURE Modelos()
    BEGIN
        SELECT unidad_id,nombre FROM crm_unidades ORDER BY nombre;
    END ;;
DELIMITER ;


DELIMITER ;;
    DROP PROCEDURE IF EXISTS Vendedores;
    CREATE PROCEDURE Vendedores(IN gid varchar(255))
    BEGIN
select gid;
        CREATE TABLE tmp_vendedores AS SELECT uid,COUNT(uid) as no_prospectos FROM crm_contactos WHERE uid>0 AND gid=gid GROUP BY uid ORDER BY uid;
        SELECT a.gid,a.uid,a.name,a.user,'Vendedor' AS tipo_acceso,a.email,if(b.no_prospectos>0,b.no_prospectos,0) as prospectos FROM users AS a LEFT JOIN tmp_vendedores AS b ON a.uid=b.uid WHERE a.gid IN (gid) AND a.super=8 AND a.active=1 ORDER BY a.name;
        DROP TABLE tmp_vendedores;
    END ;;
DELIMITER ;
