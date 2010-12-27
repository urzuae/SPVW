<?php

//require_once("$_includesdir/mail.php");
global $db;

$arg = $argv[2]; //php.exe script nombre_de_script

$sql = "SELECT gid FROM groups ORDER BY gid";
$r = $db->sql_query($sql) or die($sql);
while (list($gid) = $db->sql_fetchrow($r))
{
	$user = sprintf("%04d", $gid)."HOSTESS";
	$sql = "SELECT uid, user FROM users WHERE user = '$user'";
	$r2 = $db->sql_query($sql) or die($sql);	
	list($uid, $user2) = $db->sql_fetchrow($r2);
	if (!$uid) //no existe
	{
		$sql = "INSERT INTO users (gid, user, password, super)VALUES('$gid', '$user', PASSWORD('$user'), '0')";
		$db->sql_query($sql) or die($sql);
	}
	else
	 echo "Concesionaria $gid: Este usuario ya existe con el nombre: ".$user."\n";
}

/**/
?>