<?php
session_start();
header("Access-Control-Allow-Origin: *");
require "../kernel/config.php";

if ($domaine == "kotado.fr")
{
	$language = "french";
}
else
{
	$language = "english";
}

$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user,$db)  or die('Erreur de selection '.mysql_error()); 
require "../kernel/fonctions.php";

$approve = mysql_real_escape_string($_POST['approve']);
$edit = mysql_real_escape_string($_POST['edit']);
$id_quote = mysql_real_escape_string($_POST['id_quote']);
$texte_quote = mysql_real_escape_string($_POST['texte_quote']);

// Update quote if the quote has been edited before approval
if (!empty($texte_quote))
{
	$update_quote = mysql_query("UPDATE teen_quotes_quotes SET texte_english = '$texte_quote' WHERE id = '$id_quote'");
}

// Fetch infos about the author of the quote and the quote itself
$query_texte_quote = mysql_fetch_array(mysql_query("SELECT a.id auteur_id, q.texte_english texte_english, q.date date, a.username username, a.email email FROM teen_quotes_quotes q, teen_quotes_account a WHERE q.auteur_id = a.id AND q.id = '".$id_quote."'"));
$author_id = $query_texte_quote['auteur_id'];
$texte_quote = $query_texte_quote['texte_english'];
$date_quote = $query_texte_quote['date'];
$email_author = $query_texte_quote['email'];
$name_author = $query_texte_quote['username'];

if (isDomainValidForAjax() AND $_SESSION['security_level'] >= 2) 
{
	if ($approve == "yes") 
	{
		$nb_quote_awaiting_post = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved = '2'"));
		$jours_posted = floor($nb_quote_awaiting_post / $nb_quote_released_per_day) + 1;
		$date = date("d/m/Y", strtotime('+'.$jours_posted.' days'));
			
		if ($jours_posted > 1)
		{
			$days_quote_posted = $days_quote_posted.'s';
		}

		$date_log = ''.$date.'-'.$jours_posted;

		$approve_quote = mysql_query("UPDATE teen_quotes_quotes SET approved = '2' WHERE id = '".$id_quote."'");
		$approve_quote_log = mysql_query("INSERT INTO approve_quotes (id_quote, id_user, quote_release) VALUES ('".$id_quote."', '".$author_id."', '".$date_log."')");

		$waiting_moderation = mysql_query("SELECT id FROM teen_quotes_quotes WHERE auteur_id = '".$author_id."' AND approved = '0'");
		$waiting_send = mysql_query("SELECT id FROM approve_quotes WHERE id_user = '".$author_id."' AND send = '0'");
		
		$query_texte_quote = mysql_fetch_array(mysql_query("SELECT q.texte_english texte_english, q.date date, a.username username, a.email email FROM teen_quotes_quotes q, teen_quotes_account a WHERE q.auteur_id = a.id AND q.id = '".$id_quote."'"));
		$texte_quote = $query_texte_quote['texte_english'];
		$date_quote = $query_texte_quote['date'];
		$email_author = $query_texte_quote['email'];
		$name_author = $query_texte_quote['username'];

		include '../lang/'.$language.'/admin.php';

		// Delete the edit message if the quote was not edited
		if ($edit != 'yes')
		{
			$edit_message = '';
		}
		
		// We have only one quote to approve	
		if ($approve_quote AND mysql_num_rows($waiting_moderation) == 0 AND mysql_num_rows($waiting_send) == 1) 
		{	
			if(preg_match('/'.$domain_fr.'/', $_SERVER['SERVER_NAME']))
			{
				$message = ''.$top_mail.' Bonjour <font color="#394DAC"><b>'.$name_author.'</b></font> !<br/><br/>Votre citation a été <font color="#394DAC"><b>approuvée</b></font> récemment par un membre de notre équipe. Elle sera publiée le <b>'.$date.'</b> ('.$jours_posted.' '.$days_quote_posted.'), vous recevrez un email quand elle sera publiée sur le site.<br/><br/>Voici votre citation :<br/><div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br/><br/><a href="http://kotado.fr" target="_blank">#'.$id_quote.'</a><span style="float:right">par <a href="http://kotado.fr/user-'.$author_id.'" target="_blank">'.$name_author.'</a> le '.$date_quote.'</span></div>'.$edit_message.'<br/><br/>Cordialement,<br/><b>L\'équipe de Kotado</b>'.$end_mail;
			}
			else
			{
				$message = ''.$top_mail.' Hello <font color="#394DAC"><b>'.$name_author.'</b></font> !<br/><br/>Your quote has been <font color="#394DAC"><b>approved</b></font> recently by a member of our team. It will be released on <b>'.$date.'</b> ('.$jours_posted.' '.$days_quote_posted .'), you will receive an email when it will be posted on the website.<br/><br/>Here is your quote:<br/><div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br/><br/><a href="http://teen-quotes.com" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://teen-quotes.com/user-'.$author_id.'" target="_blank">'.$name_author.'</a> on '.$date_quote.'</span></div>'.$edit_message.'<br/><br/>Sincerely,<br/><b>The Teen Quotes Team</b>'.$end_mail;
			}
			
			$mail = mail($email_author, $quote_added_queue, $message, $headers);
			$update_send = mysql_query("UPDATE approve_quotes SET send = '1' WHERE id_quote = '".$id_quote."' AND id_user = '".$author_id."' LIMIT 1");
		}
		// Many quotes in the queue
		elseif (mysql_num_rows($waiting_moderation) == 0 AND mysql_num_rows($waiting_send) >= 2)
		{
			include_once('../kernel/send_moderation.php');
		}

		echo $succes;
	}
	else
	{
		$delete_quote = mysql_query("UPDATE teen_quotes_quotes SET approved = '-1' WHERE id = '".$id_quote."'");
		$delete_quote_log = mysql_query("INSERT INTO approve_quotes (id_quote, id_user, approved) VALUES ('".$id_quote."', '".$author_id."', '0')");

		$waiting_moderation = mysql_query("SELECT id FROM teen_quotes_quotes WHERE auteur_id = '".$author_id."' AND approved = '0'");
		$waiting_send = mysql_query("SELECT id FROM approve_quotes WHERE id_user = '".$author_id."' AND send = '0'");

		$query_texte_quote = mysql_fetch_array(mysql_query("SELECT q.texte_english texte_english, q.date date, a.username username, a.email email FROM teen_quotes_quotes q, teen_quotes_account a WHERE q.auteur_id = a.id AND q.id = '".$id_quote."'"));
		$texte_quote = $query_texte_quote['texte_english'];
		$date_quote = $query_texte_quote['date'];
		$email_author = $query_texte_quote['email'];
		$name_author = $query_texte_quote['username'];

		include '../lang/'.$language.'/admin.php';
		
		if ($delete_quote AND !empty($email_author)) 
		{	
			// Only one quote has been unapproved
			if (mysql_num_rows($waiting_moderation) == 0 AND mysql_num_rows($waiting_send) == 1)
			{
				if(preg_match('/'.$domain_fr.'/', $_SERVER['SERVER_NAME']))
				{
					$message = ''.$top_mail.'Bonjour <font color="#394DAC"><b>'.$name_author.'</b></font> !<br/><br/>Votre citation a été <font color="#394DAC"><b>rejetée</b></font> récemment par un membre de notre équipe...<br/><div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br/><br/><a href="http://kotado.fr" target="_blank">#'.$id_quote.'</a><span style="float:right">par <a href="http://kotado.fr/user-'.$author_id.'" target="_blank">'.$name_author.'</a> le '.$date_quote.'</span></div>'.$quotes_unapproved_singular.$quotes_unapproved_reasons.'Cordialement,<br/><b>The Kotado Team</b>'.$end_mail;
				}
				else
				{
					$message = ''.$top_mail.' Hello <font color="#394DAC"><b>'.$name_author.'</b></font>!<br/><br/>Your quote has been <font color="#394DAC"><b>rejected</b></font> recently by a member of our team...<br/><div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br/><br/><a href="http://teen-quotes.com" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://teen-quotes.com/user-'.$author_id.'" target="_blank">'.$name_author.'</a> on '.$date_quote.'</span></div>'.$quotes_unapproved_singular.$quotes_unapproved_reasons.'Sincerely,<br/><b>The Teen Quotes Team</b>'.$end_mail;
				}

				$mail = mail($email_author, $quote_rejected, $message, $headers);
				$update_send = mysql_query("UPDATE approve_quotes SET send = '1' WHERE id_quote = '".$id_quote."' AND id_user = '".$author_id."' LIMIT 1");
			}
			// Many quotes have been unapproved
			elseif (mysql_num_rows($waiting_moderation) == 0 AND mysql_num_rows($waiting_send) >= 2)
			{
				include_once('../kernel/send_moderation.php');
			}
		}

		echo 'Unapproved';
	}
}