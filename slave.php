<?php
include 'kernel/config.php';
include 'kernel/fonctions.php';


if (date('s') % 2 == 0)
{
	sql_connect(TRUE);
	$server = 'Tried on slave';
}
else
{
	sql_connect();
	$server = 'Tried on master';
}
$query = mysql_query("SELECT username FROM teen_quotes_account WHERE id = 27");
$data = mysql_fetch_array($query);
$username = $data['username'];

echo $server.' - '.$username.'<br/>';

echo '<meta http-equiv="refresh" content="1;url=http://teen-quotes.com/slave">';

mysql_close();