<?php
function sql_connect ($slave=false, $force=false)
{
	// Grant access to variables located in /kernel/config.php
	global $host, $user, $pass, $replication, $freq, $host_slave, $user_slave, $pass_slave, $domain, $domain_en;
	$db_name = $user;

	if (($slave AND $replication AND date(s) % $freq == 0 AND $domain == $domain_en) OR $force)
	{
		$host = $host_slave;
		$user = $user_slave;
		$pass = $pass_slave;
	}

	$db = mysql_connect($host, $user, $pass)  or die('Connexion error'.mysql_error());
	mysql_select_db($db_name, $db)  or die('Selection error '.mysql_error());
}