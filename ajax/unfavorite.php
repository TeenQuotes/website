<?php
session_start();
header("Access-Control-Allow-Origin: *");
require "../kernel/config.php";
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user,$db)  or die('Erreur de selection '.mysql_error()); 
require "../kernel/fonctions.php";
include '../lang/'.$language.'/favorite.php'; 

$id_quote = mysql_real_escape_string($_POST['id_quote']);
$id = mysql_real_escape_string($_POST['id_user']);

if (preg_match('/'.$domaine_fr.'/', $_SERVER['SERVER_NAME']) OR preg_match('/'.$domaine_en.'/', $_SERVER['SERVER_NAME'])) 
{

	$verif_quote = mysql_fetch_assoc(mysql_query('SELECT COUNT(*) AS nb FROM teen_quotes_favorite WHERE id_quote = '.$id_quote.' AND id_user = '.$id.''));

	if (empty($id_quote))
	{
		echo 'Erreur id !';
	}
	elseif ($verif_quote['nb'] == 0 OR $_SESSION['logged'] == FALSE OR $_SESSION['id'] != $id)
	{
		echo 'Error during the verification of the owner of the account.';
	} 
	else
	{
		$query = mysql_query('DELETE FROM teen_quotes_favorite WHERE id_quote = '.$id_quote.' AND id_user = '.$id.'');
		$update_fav = mysql_query('UPDATE teen_quotes_quotes SET nb_fav = nb_fav-1 WHERE id = '.$id_quote.'');
		 
		if($query) 
			{
			echo ''.$delete_succes.'';
			}
		else
			{
			echo '<h2>'.$error.'</h2>';
			}
	}
}
?>