<?php
global $db;


$tabla=" groups_ubications ";
$sql_drop="DROP TABLE ".$tabla.";";
$res_drop=$db->sql_query($sql_drop);

$sql_create="CREATE TABLE ".$tabla." AS SELECT * FROM groups ORDER BY gid;";
$res_create=$db->sql_query($sql_create);

$sql_a1="ALTER TABLE ".$tabla." ADD COLUMN region_id integer(4) not null default 0;";
$res_a1=$db->sql_query($sql_a1);

$sql_a2="ALTER TABLE ".$tabla." ADD COLUMN zona_id integer(4) not null default 0;";
$res_a2=$db->sql_query($sql_a2);

$sql_a3="ALTER TABLE ".$tabla." ADD COLUMN entidad_id integer(4) not null default 0;";
$res_a3=$db->sql_query($sql_a3);

$sql_a4="ALTER TABLE ".$tabla." ADD COLUMN plaza_id integer(4) not null default 0;";
$res_a4=$db->sql_query($sql_a4);

$sql_a5="ALTER TABLE ".$tabla." ADD COLUMN grupo_empresarial_id integer(4) not null default 0;";
$res_a5=$db->sql_query($sql_a5);

$sql_a6="ALTER TABLE ".$tabla." ADD COLUMN nivel_id integer(4) not null default 1;";
$res_a6=$db->sql_query($sql_a6);

$sql_a7="ALTER TABLE ".$tabla." ADD COLUMN nombre_nivel varchar(20) not null default 'Básico';";
$res_a7=$db->sql_query($sql_a7);


$sql="SELECT * FROM ".$tabla." ORDER BY gid;";
$res_sql=$db->sql_query($sql);
if( $db->sql_numrows($res_sql) > 0 )
{
    while($fila = $db->sql_fetchrow($res_sql))
	{
		$gid=$fila['gid'];
        echo"\ngid:  ".$fila['gid'];
		$zona_id=regresa_zona($db,$gid);
		$plaza_id=regresa_plaza($db,$gid);
		$empresa_id=regresa_empresa($db,$gid);
		$nivel_id=regresa_nivel($db,$gid);
		switch($nivel_id)
		{
			case 1:
				$tit="Basico";
				break;
			case 2:
				$tit="Medio";
				break;
			case 3:
				$tit="Avanzado";
				break;
			default:
				$tit="Básico";
				break;

		}
		$entidad_id=regresa_entidad($db,$plaza_id);
		$region_id=regresa_region($db,$zona_id);
		$upda="UPDATE ".$tabla." SET region_id=".$region_id." , zona_id=".$zona_id." , entidad_id=".$entidad_id." ,plaza_id=".$plaza_id." , grupo_empresarial_id=".$empresa_id." , nivel_id=".$nivel_id." , nombre_nivel='".$tit."' WHERE gid=".$gid.";";
        if($db->sql_query($upda))
            echo"\ngid:  ".$gid."   ".$upda;


	}
}

function regresa_region($db,$id)
{
	$reg=0;
	$sql_con="SELECT region_id FROM crm_zonas where zona_id=".$id.";";
	$res_con=$db->sql_query($sql_con);
    if ( $db->sql_numfields($res_con) > 0)
	{
        $reg=$db->sql_fetchfield(0,0,$res_con);
	}
	return $reg;

}

function regresa_entidad($db,$id)
{
	$reg=0;
	$sql_con="SELECT entidad_id FROM crm_plazas  where plaza_id=".$id.";";
    $res_con=$db->sql_query($sql_con);
    if ( $db->sql_numfields($res_con) > 0)
    {
        $reg=$db->sql_fetchfield(0,0,$res_con);
	}
	return $reg;

}
function regresa_nivel($db,$id)
{
	$reg=0;
	$sql_con="SELECT nivel_id FROM crm_niveles_concesionarias  where gid=".$id.";";
	$res_con=$db->sql_query($sql_con);
    if ( $db->sql_numfields($res_con) > 0)
	{
        $reg=$db->sql_fetchfield(0,0,$res_con);
	}
	return $reg;
}


function regresa_empresa($db,$id)
{
	$reg=0;
	$sql_con="SELECT grupo_empresarial_id FROM crm_grupos_concesionarias  where gid=".$id.";";
	$res_con=$db->sql_query($sql_con);
    if ( $db->sql_numfields($res_con) > 0)
	{
        $reg=$db->sql_fetchfield(0,0,$res_con);
	}
	return $reg;
}

function regresa_plaza($db,$id)
{
	$reg=0;
	$sql_con="SELECT plaza_id FROM crm_plazas_concesionarias  where gid=".$id.";";
	$res_con=$db->sql_query($sql_con);
    if ( $db->sql_numfields($res_con) > 0)
	{
        $reg=$db->sql_fetchfield(0,0,$res_con);
	}
	return $reg;
}


function regresa_zona($db,$id)
{
	$reg=0;
	$sql_con="SELECT zona_id FROM groups_zonas where gid=".$id.";";
	$res_con=$db->sql_query($sql_con);
    if ( $db->sql_numfields($res_con) > 0)
	{
        $reg=$db->sql_fetchfield(0,0,$res_con);
	}
	return $reg;
}
?>