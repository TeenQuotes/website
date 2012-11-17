<?php 
require "kernel/config.php";
require "kernel/fonctions.php";
// Connect to SQL master unless we want to ping the slave
if ($_GET['code'] == 'pingslave' OR $_GET['code'] == 'checkslaveupdate') 
{
	sql_connect(TRUE, TRUE);
}
else
{
	sql_connect();
}


$hour = date("H");
$day = date("D");

// Look if the newsletter has been posted
if ($_GET['code'] == 'monday' OR $_GET['code'] == 'tuesday')
{
	$query_monday = mysql_fetch_array(mysql_query("SELECT send_mail_monday FROM config WHERE id = '1'"));
	$send_monday = $query_monday['send_mail_monday'];
}
// Check if quotes have been posted
if ($_GET['code'] == 'quote' OR $_GET['code'] == 'resetquote')
{
	$compteur_quote_posted_today_query = mysql_fetch_array(mysql_query("SELECT compteur_quote_posted_today FROM config WHERE id = '1'"));
	$compteur_quote_posted_today = $compteur_quote_posted_today_query['compteur_quote_posted_today'];
}
// Post quotes
if ($_GET['code'] == 'quote')
{
	if ($hour >= 00 AND $hour <= 02)
	{
		if ($compteur_quote_posted_today == '0')
		{
			$update = mysql_query("UPDATE config SET compteur_quote_posted_today = '1' WHERE id = '1'");
			flush_quotes();
			email_birthday();
		}
	}
}
// Reset Quotes
if ($_GET['code'] == 'resetquote')
{
	if ($hour >= 21 AND $hour <= 22)
	{
		if ($compteur_quote_posted_today == '1')
		{
			$update = mysql_query("UPDATE config SET compteur_quote_posted_today = '0' WHERE id = '1'");
		}
	}
}
// Send the newsletter
if ($_GET['code'] == 'monday')
{

	if ($send_monday == "0" AND $day == "Mon") 
	{ // ENVOI DE MAIL LE LUNDI
		$message = ''.$top_mail.'';
		$message.= MailRandomQuote(15);
		$message.= ''.$end_mail.'';
		
		echo 'Envoi de la newsletter';
		
		$today = date("d/m/Y");
		$i = '0';
		$txt_file = 'Newsletter on '.$today."\r\n\n";


		$query = mysql_query("SELECT email, code FROM newsletter");

		while ($donnees = mysql_fetch_array($query)) 
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
			
		$update = mysql_query("UPDATE config SET send_mail_monday = '1' WHERE id = '1'");
		mail('antoine.augusti@gmail.com', 'Sent newsletter', '', $headers);
	}
	else
	{
		echo 'Newsletter already sent.<br/>';
	}
}
// Reset newsletter
elseif ($_GET['code'] == 'tuesday')
{
	if ($send_monday == "1" AND $day == "Tue") 
	{ 	
		// RESET COMPTEUR MARDI
		$update = mysql_query("UPDATE config SET send_mail_monday = '0' WHERE id = '1'");
		mail('antoine.augusti@gmail.com', 'Reset done', '',$headers);
	}
	else
	{
		echo 'No reset.<br/>';
	}
}
// Try to connect to the slave. If it fails, alert with an email. (1 mn)
elseif ($_GET['code'] == 'pingslave')
{
	$ping = mysql_ping();

	if ($ping == FALSE)
	{
		$object = 'SQL slave down';
		$message = $top_mail.'The SQL slave appears to be down. Check its status NOW!'.$end_mail;

		mail('antoine.augusti@gmail.com', $object, $message, $headers);
		mail('maxime05.antoine@gmail.com', $object, $message, $headers);
		mail('michel@navissal.com', $object, $message, $headers);
	}
}
// Try to connect to the slave. If it fails, disable SQL replication (15 mns)
elseif ($_GET['code'] == 'checkslaveupdate')
{
	$do = FALSE;
	
	// Check the current content of the file
	$content_file = file_get_contents("files/replication.php");
	// Ping the slave
	$ping = mysql_ping();
	// Check if everything is ok
	if ($ping == TRUE)
	{
		$query = mysql_query("SHOW SLAVE STATUS");
		$data = mysql_fetch_array($query);
	}
	
	// The slave answers
	if ($ping == TRUE AND $data['Slave_IO_State'] == 'Waiting for master to send event' AND $data['Slave_IO_Running'] == 'Yes' AND $data['Slave_SQL_Running'] == 'Yes')
	{
		// If the replication was disabled, enable it
		if (strpos($content_file, "FALSE"))
		{
			
			$txt_to_write = "TRUE";
			$do = TRUE;
			$message = $top_mail.' The slave has been ENABLED.'.$end_mail;
		}
	}
	// The slave is down
	else
	{
		// If the replication was enabled, disable it
		if (strpos($content_file, "TRUE"))
		{
			$txt_to_write = "FALSE";
			$do = TRUE;
			$message = $top_mail.' The slave has been DISABLED.<br/><br/>Debug: ping state: '.$ping.'<br/><br/>'.$data.$end_mail;
		}
	}

	// Check if we need to write or not
	if ($do == TRUE)
	{
		// New content of the file
		$string = '
		<?php
		$replication = '.$txt_to_write.';
		?>';

		// Write in the file
		$file = fopen("files/replication.php", "w");
		fwrite($file, $string);
		fclose($file);

		// Send a notification to administrators
		mail('antoine.augusti@gmail.com', 'SQL slave updated', $message, $headers);
		mail('maxime05.antoine@gmail.com', 'SQL slave updated', $message, $headers);
	}
	
}

echo 'Hello World.';