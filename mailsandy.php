<?php

require "kernel/config.php";
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user,$db)  or die('Erreur de selection '.mysql_error()); 
require "kernel/fonctions.php";

$query = mysql_query("SELECT email, username, city FROM teen_quotes_account WHERE id = '27'");

while ($donnees = mysql_fetch_array($query))
{
	$username = $donnees['username'];
	$email = $donnees['email'];
	$domaine = 'teen-quotes.com';
	$id_quote = 1279;
	$auteur_id = 828;
	$auteur = 'cookiemonster';
	$date = '22/11/2011';
	$txt_quote = 'In all things, it is better to hope than to despair.';

	$message = $top_mail.'
	Hi <font color="#5C9FC0"><b>'.$username.'</b></font>,<br/>
	<br/>
	Today we want to contact you because our team has seen terrible images about the hurricane Sandy, in the United States of America and especially in New York City. We\'re living in France so we can\'t understand what it is to see such a hurricane but we want to share our love with you.<br/>
	<br/>
	We really hope that you\'re doing well, that your relatives are safe and that you do not have too much property damage.<br/>
	<br/>
	We\'ve found a great quote for you:<br/>
	<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">
	'.$txt_quote.'<br>
		<div style="font-size:90%;margin-top:5px">
			<a href="http://'.$domaine.'/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://'.$domaine.'/user-'.$auteur_id.'" target="_blank">'.$auteur.'</a> on '.$date.'</span>
		</div>
	</div>
	<br/>
	Stay strong. Despite all the damage, you stay alive. Tomorrow will be better than yesterday.<br/>
	<br/>
	We send you all our love. See you soon on Teen Quotes.<br/>
	<br/>
	Best regards,<br/>
	<b>The Teen Quotes Team</b>'.$end_mail;

	mail($email, 'Hurricane Sandy in NYC', $message, $headers);
	echo 'Sent:'.$email.'<br/>';
}
