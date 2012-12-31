<?php
function sql_connect ($slave=false, $force=false)
{
	// Grant access to variables located in /kernel/config.php
	global $host, $user, $pass, $replication, $freq, $host_slave, $user_slave, $pass_slave, $domaine, $domain_en;
	$db_name = $user;

	if (($slave == TRUE AND $replication == TRUE AND date(s) % $freq == 0 AND $domaine == $domain_en) OR $force == TRUE)
	{
		$host = $host_slave;
		$user = $user_slave;
		$pass = $pass_slave;
	}

	$db = mysql_connect($host, $user, $pass)  or die('Connexion error'.mysql_error());
	mysql_select_db($db_name, $db)  or die('Selection error '.mysql_error());
}