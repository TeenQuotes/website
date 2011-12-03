<?php 

require "kernel/config.php";
require "kernel/fonctions.php";
$db = mysql_connect("$host", "$user", "$pass")  or die('Erreur de connexion '.mysql_error());
mysql_select_db('teenq208598',$db)  or die('Erreur de selection '.mysql_error()); 

$query_monday=mysql_fetch_array(mysql_query("SELECT send_mail_monday FROM config WHERE id='1'"));
$send_monday=$query_monday['send_mail_monday'];

$jour=date("D");

if ($send_monday=="0" AND $jour=="Mon") 
	{ // ENVOI DE MAIL LE LUNDI
	$message = "$top_mail";
	$message.= MailRandomQuote(15);
	$message.= "$end_mail";


	$query=mysql_query("SELECT email,code FROM newsletter");

	while ($donnees=mysql_fetch_array($query)) 
		{
		$email=$donnees['email'];
		$code=$donnees['code'];
		$unsuscribe= '<br /><span style="font-size:80%">This email was adressed to you ('.$email.') because you are subscribed to our newsletter. If you want to unsuscribe, please follow <a href="http://www.teen-quotes.com/newsletter.php?action=unsuscribe&email='.$email.'&code='.$code.'" target="_blank"> this link</a></span>';
		$mail = mail ("$email", "Newsletter", $message.$unsuscribe, $headers);
		}
		
	$update=mysql_query("UPDATE config SET send_mail_monday='1' WHERE id='1'");
	}


if ($send_monday=="1" AND $jour=="Tue") 
	{ // RESET COMPTEUR MARDI
	$update=mysql_query("UPDATE config SET send_mail_monday='0' WHERE id='1'");
	}