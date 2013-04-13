<?php
session_start();
header("Access-Control-Allow-Origin: *");
require "../kernel/config.php";
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user, $db)  or die('Erreur de selection '.mysql_error()); 
require "../kernel/fonctions.php";

if (isDomainValidForAjax()) 
{
	// Code by default
	$code = 0;
	
	// Fetch data
	$usage = trim(mysql_real_escape_string($_POST['usage']));
	$frequence = trim(mysql_real_escape_string($_POST['frequence']));
	$id_user = mysql_real_escape_string($_POST['id_user']);
	$hash = mysql_real_escape_string($_POST['hash']);

	$query_exist_user = mysql_query("SELECT id FROM teen_quotes_account WHERE id = '".$id_user."' AND pass = '".$hash."'");

	if (mysql_num_rows($query_exist_user) === 1 AND !empty($usage) AND !(empty($frequence)) AND !empty($id_user) AND strlen($frequence) >= 100 AND strlen($usage) >= 100)
	{
		$exist_user_query = mysql_query("SELECT id FROM stories WHERE id_user = ".$id_user."");
		
		// If the user has not already wrote a story, insert it
		if (mysql_num_rows($exist_user_query) === 0)
		{
			$query = mysql_query("INSERT INTO stories (id_user, txt_represent, txt_frequence) VALUES ('".$id_user."', '".$usage."', '".$frequence."')");

			if ($query)
				$code = 1;

		}
		else
			$code = 2;
	}

	// Return code for JS
	echo $code;
}