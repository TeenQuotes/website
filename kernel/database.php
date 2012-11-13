<?php
function sql_connect ($slave=false)
{
	// Grant access to variables located in /kernel/config.php
	global $host, $user, $pass, $replication, $freq, $host_slave, $user_slave, $pass_slave;
	$db_name = $user;

	if ($slave == TRUE AND $replication == TRUE AND date(s) % $freq == 0)
	{
		$host = $host_slave;
		$user = $user_slave;
		$pass = $pass_slave;

		echo 'slave ';
	}

	$db = mysql_connect($host, $user, $pass)  or die('Connexion error'.mysql_error());
	mysql_select_db($db_name,$db)  or die('Selection error '.mysql_error());
}
?>