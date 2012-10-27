<?php
require "kernel/config.php";
require "kernel/fonctions.php";
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user,$db)  or die('Erreur de selection '.mysql_error()); 

$query = mysql_query("SELECT COUNT(*) tot, SUBSTRING_INDEX(email,'@',-1) AS domaine
					FROM teen_quotes_account
					GROUP BY domaine
					ORDER BY tot DESC
					LIMIT 0, 1");

while ($data = mysql_fetch_array($query))
{
	$domaine = 'http://'.$data['domaine'];

	$curl = curl_init();
	curl_setopt_array( $curl, array(
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_URL => $domaine));
	curl_exec($curl);
	$response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	if (preg_match('#404#', $response_code) OR $response_code == 0)
	{
		echo $domaine.' <a href="'.$domaine.'" title="'.$domaine.'">'.$domaine.'</a> appears to be down.<br/>';
	}
}

?>