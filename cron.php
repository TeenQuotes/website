<?php 
require "kernel/config.php";
require "kernel/fonctions.php";
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user,$db)  or die('Erreur de selection '.mysql_error()); 

$query_monday = mysql_fetch_array(mysql_query("SELECT send_mail_monday FROM config WHERE id = '1'"));
$send_monday = $query_monday['send_mail_monday'];

$jour = date("D");

if ($send_monday == "0" AND $jour == "Mon") 
	{ // ENVOI DE MAIL LE LUNDI
	$message = ''.$top_mail.'';
	$message.= MailRandomQuote(15);
	$message.= ''.$end_mail.'';
	
	echo 'Envoi de la newsletter';
	
	$today = date("d/m/Y");
	$i = '0';
	$txt_file = 'Newsletter on '.$today.'\r\n\n';


	$query = mysql_query("SELECT email, code FROM newsletter");

	while ($donnees=mysql_fetch_array($query)) 
		{
		$email = $donnees['email'];
		$code = $donnees['code'];

		if ($domaine == 'kotado.fr')
		{
			$unsubscribe= '<br /><span style="font-size:80%">Cet email a été envoyé à votre adresse ('.$email.') car vous êtes inscrit à la newsletter. Si vous souhaitez vous désinscrire, cliquez sur <a href="http://kotado.fr/newsletter.php?action=unsubscribe&email='.$email.'&code='.$code.'" target="_blank">ce lien</a>.</span>.';
		}
		else
		{
			$unsubscribe = '<br /><span style="font-size:80%">This email was adressed to you ('.$email.') because you are subscribed to our newsletter. If you want to unsubscribe, please follow <a href="http://teen-quotes.com/newsletter.php?action=unsubscribe&email='.$email.'&code='.$code.'" target="_blank">this link</a>.</span>';
		}
		
		$mail = mail ($email, "Newsletter", $message.$unsubscribe, $headers);
		if ($mail)
			{
			$i++;
			$txt_file .= '#'.$i.' : '.$email.' - '.$code.''."\r";
			}
		}
	$monfichier = fopen('files/compteur_email_hebdomadaire.txt', 'r+'); // Ouverture du fichier
	fseek($monfichier, 0); // On remet le curseur au début du fichier
	fputs($monfichier, $txt_file); // On écrit le nouveau nombre de pages vues
	fclose($monfichier);
		
	$update = mysql_query("UPDATE config SET send_mail_monday='1' WHERE id = '1'");
	}
else
	{
	echo 'Newsletter déjà envoyée';
	}


if ($send_monday == "1" AND $jour == "Tue") 
	{ // RESET COMPTEUR MARDI
	$update=mysql_query("UPDATE config SET send_mail_monday='0' WHERE id = '1'");
	}