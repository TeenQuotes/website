<?php 
header("Access-Control-Allow-Origin: *");
require "../kernel/config.php";
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user,$db)  or die('Erreur de selection '.mysql_error()); 
require "../kernel/fonctions.php";
include '../lang/'.$language.'/favorite.php'; 

$id_quote=mysql_real_escape_string($_POST['id_quote']);
$id = mysql_real_escape_string($_POST['id_user']);

if (preg_match('/'.$domaine_fr.'/', $_SERVER['SERVER_NAME']) OR preg_match('/'.$domaine_en.'/', $_SERVER['SERVER_NAME'])){
	$verif = array(
		'quote' => mysql_fetch_assoc(mysql_query("SELECT COUNT(*) AS nb FROM teen_quotes_quotes WHERE id = '$id_quote'")),
		'user' => mysql_fetch_assoc(mysql_query("SELECT COUNT(*) AS nb FROM teen_quotes_account WHERE id = '$id'")),
		'username' => mysql_fetch_assoc(mysql_query("SELECT username FROM teen_quotes_account WHERE id = '$id'"))
		);
	
	if (empty($id_quote) OR empty($id) OR !is_numeric($id_quote) OR !is_numeric($id)){
		echo 'Error id !';
	} elseif ($verif['quote']['nb'] != 1 || $verif['user']['nb'] != 1 || $_SESSION['logged'] == FALSE || $_COOKIE['Pseudo'] != $verif['username']['username']){
		echo 'Error during the verification of the owner of the account';
	} else {
		$query = mysql_query("INSERT INTO teen_quotes_favorite (id_quote, id_user) VALUES ('$id_quote','$id')");
		$update_fav = mysql_query("UPDATE teen_quotes_quotes SET nb_fav=nb_fav+1 WHERE id = '$id_quote'");
		 
		if($query && $update_fav){
			echo ''.$add_succes.'';
		} else {
			echo '<h2>'.$error.'</h2>';
		}
	}
} else {
	echo $_SERVER['SERVER_NAME'];
}
	
?>