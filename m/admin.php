<?php 
include 'header.php';
include '../lang/'.$language.'/admin.php';
$action = $_GET['action'];
if ($_SESSION['security_level'] < '2') 
{
	echo '<meta http-equiv="refresh" content="0; url=error.php?erreur=403">';
} 
elseif (empty($action) AND $_SESSION['security_level'] >= '2') 
{
	$nb_quote_awaiting_post = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved = '2'"));
	$jours_posted = floor($nb_quote_awaiting_post / $nb_quote_released_per_day); 
	if ($nb_quote_awaiting_post % $nb_quote_released_per_day != 0)
	{
		$jours_posted = $jours_posted + 1;
	}
	if ($jours_posted > 1)
	{
		$days_quote_posted = $days_quote_posted.'s';
	}
		
	echo '
	<div class="post">
		<h2><img src="http://'.$domaine.'/images/icones/add.png" class="icone" />Add a quote</h2>
		<form action="?action=add_quote" method="post">
			Enter the quote :<br/>
			<textarea name="texte_quote" style="width:100%;height:50px"></textarea>
			<br/>
			<div class="clear"></div>
			<input type="checkbox" id="release_admin" name="release_admin" value="1" checked><label for="release_admin">Release with the '.$name_website.'\'s admin account</label><br/>
			<input type="checkbox" id="release_unknown" name="release_unknown" value="1"><label for="release_unknown">Release with the unknown account</label><br/>
			<div class="clear"></div>
			<center><p><input type="submit" value="Okay" class="submit" /></p></center>
		</form>
	</div>
	
	<div class="post">
		<h2><img src="http://'.$domaine.'/images/icones/test.png" class="icone" />Approve Quotes</h2>
		<div class="grey_post">
		Number of citations waiting to be posted : '.$nb_quote_awaiting_post.' ('.$jours_posted.' '.$days_quote_posted .')
		</div>
	';
	$query = mysql_query("SELECT q.texte_english texte_english, q.id id, q.auteur_id auteur_id, q.date date, a.username auteur FROM teen_quotes_quotes q, teen_quotes_account a WHERE q.auteur_id = a.id AND approved = '0' ORDER BY id ASC");
	while ($result = mysql_fetch_array($query)) 
	{
		$txt_quote = $result['texte_english'];
		$auteur_id = $result['auteur_id'];
		$auteur = $result['auteur']; 
		$date = $result['date'];
		$id_quote = $result ['id'];
		
		echo '
		<div class="grey_post" data-id="'.$id_quote.'">
			'.$txt_quote.'<br/><br/>
			
			<span class="admin_quote" data-id="'.$id_quote.'">
				<a href="admin.php?action=rate&approve=yes&id='.$id_quote.'&auteur='.$auteur_id.'"><img src="http://teen-quotes.com/images/icones/succes.png" class="mini_icone" /></a>
				<a href="admin.php?action=edit&id='.$id_quote.'"><img src="http://teen-quotes.com/images/icones/profil.png" class="mini_icone" /></a>
				<a href="admin.php?action=rate&approve=no&id='.$id_quote.'&auteur='.$auteur_id.'"><img src="http://teen-quotes.com/images/icones/delete.png" class="mini_icone" /></a>
			</span>
			
			<span class="right">'.$by.' <a href="user-'.$auteur_id.'" title="'.$view_his_profile .'">'.$auteur.'</a> '.$on.' '.$date.'</span><br/><br/>
		</div>';
	}
	echo '
	</div>

	<div class="post">
		<h2><img src="http://'.$domaine.'/images/icones/about.png" class="icone" />Last comments</h2>
		<div class="grey_post">
			<ul>';
			$query = mysql_query("SELECT c.id id, c.id_quote id_quote, c.auteur_id auteur_id, c.texte texte, c.date date, a.username auteur FROM teen_quotes_comments c, teen_quotes_account a WHERE c.auteur_id = a.id ORDER BY c.id DESC LIMIT 0, 5");
			while ($comments = mysql_fetch_array($query))
			{
				$comment_id = $comments['id'];
				$comment_id_quote = $comments['id_quote'];
				$comment_auteur_id = $comments['auteur_id'];
				$comment_texte = cut_comment(stripcslashes($comments['texte']));
				$comment_date = $comments['date'];
				$comment_username_auteur = $comments['auteur'];
				echo '<li><a href="quote-'.$comment_id_quote.'" title="Quote '.$comment_id_quote.'">#'.$comment_id.'</a> by <a href="user-'.$comment_auteur_id.'" title="'.$comment_username_auteur.'">'.$comment_username_auteur.'</a> on '.$comment_date.' : "'.$comment_texte.'". &nbsp;&nbsp;<a href="editcomment-'.$comment_id.'"><img src="http://teen-quotes.com/images/icones/profil.png" class="mini_icone" /></a><a href="admin.php?action=delete_comment&id='.$comment_id.'"><img src="http://teen-quotes.com/images/icones/delete.png" class="mini_icone" /></a></li>'; 
			}
	echo '
			</ul>
		</div>
	</div>
	
	<div class="post">
		<h2><img src="http://teen-quotes.com/images/icones/profil.png" class="icone" />Edit an existing quote</h2>
		<form action="?action=edit_existing_quote" method="post">
			Enter the ID of the quote<br/>
			<input name="id_quote" type="text" />
			<br/>
			<center><p><input type="submit" value="Okay" class="submit" /></p></center>
		</form>
	</div>';
	
	echo '
	<div class="post">
		<h2><img src="http://'.$domaine.'/images/icones/delete.png" class="icone" />Delete existing quotes</h2>
		If you want to delete only one quote, just enter the ID.<br/>
		If you want to delete quotes, you have to enter data like this : 100,200,300<br/>
		<form action="?action=delete_existing_quote" method="post">
			Enter the ID(s)<br/>
			<input name="id_quote" type="text" />
			<br/>
			<center><p><input type="submit" value="Okay" class="submit" /></p></center>
		</form>
	</div>';
}
elseif ($action == "add_quote") 
{
	echo '
	<div class="post">
	<h2><img src="http://'.$domaine.'/images/icones/add.png" class="icone" />Add a quote</h2>';
	
	$texte_quote = htmlspecialchars(mysql_real_escape_string($_POST['texte_quote']));
	$release_admin = htmlspecialchars(mysql_real_escape_string($_POST['release_admin']));
	$release_unknown = htmlspecialchars(mysql_real_escape_string($_POST['release_unknown']));
	$date = date("d/m/Y");

	if (strlen($texte_quote) >= 30 AND ($release_admin != '1' OR $release_unknown != '1')) 
	{

		$id_auteur_quote = $id; // Cas général, l'administrateur est l'auteur de la nouvelle quote

		if ($release_admin == '1')
		{
			if ($domaine == $domain_en)
			{
				$id_auteur_quote = 70;
			}
			elseif ($domaine == $domain_fr)
			{
				$id_auteur_quote = 3;
			}
		}
		elseif ($release_unknown == '1')
		{
			if ($domaine == $domain_en)
			{
				$id_auteur_quote = 1211;
			}
			elseif ($domaine == $domain_fr)
			{
				$id_auteur_quote = 35;
			}
		}

		$nb_quote_awaiting_post = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved = '2'"));
		$jours_posted = floor($nb_quote_awaiting_post / $nb_quote_released_per_day) + 1;
		$date = date("d/m/Y", strtotime('+'.$jours_posted.' days'));
			
		if ($jours_posted > 1)
		{
			$days_quote_posted = $days_quote_posted.'s';
		}

		$date_log = ''.$date.'-'.$jours_posted;

		$query = mysql_query("INSERT INTO teen_quotes_quotes (texte_english, date, auteur_id, approved) VALUES ('".$texte_quote."', '".$date."', '".$id_auteur_quote."','2')");
		$id_quote = mysql_insert_id();

		$approve_quote_log = mysql_query("INSERT INTO approve_quotes (id_quote, id_user, quote_release) VALUES ('".$id_quote."', '".$id_auteur_quote."', '".$date_log."')");
		
		if ($query) 
		{
			echo $succes.' <a href="../admin">Add anoter one</a>';
			echo '<meta http-equiv="refresh" content="0;url=admin" />';
		}
		else 
		{
			echo '<h2>'.$error.'</h2> '.$lien_retour;
		}
	}
	else 
	{
		echo '<h2>'.$error.' : too short</h2> '.$lien_retour;
	}	
}
elseif ($action == "rate") 
{
	echo '
	<div class="post" id="approvequotes">
	<h2><img src="http://'.$domaine.'/images/icones/test.png" class="icone" />Approve Quotes</h2>';

	$id_quote = mysql_real_escape_string($_GET['id']);
	$approve = mysql_real_escape_string($_GET['approve']);
	$auteur_id = mysql_real_escape_string($_GET['auteur']);
	$edit = mysql_real_escape_string($_GET['edit']);

	if ($approve == "yes") 
	{
		$query_texte_quote = mysql_fetch_array(mysql_query("SELECT q.texte_english texte_english, q.date date, a.email email, a.username username FROM teen_quotes_quotes q, teen_quotes_account a WHERE q.auteur_id = a.id AND q.id = '".$id_quote."'"));
		$texte_quote = $query_texte_quote['texte_english'];
		$date_quote = $query_texte_quote['date'];
		$email_auteur = $query_texte_quote['email'];
		$name_auteur = $query_texte_quote['username'];
		
		$nb_quote_awaiting_post = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved = '2'"));
		$jours_posted = floor($nb_quote_awaiting_post / $nb_quote_released_per_day) + 1;
		$date = date("d/m/Y", strtotime('+'.$jours_posted.' days'));
			
		if ($jours_posted > 1)
		{
			$days_quote_posted = $days_quote_posted.'s';
		}

		$date_log = ''.$date.'-'.$jours_posted;

		$approve_quote = mysql_query("UPDATE teen_quotes_quotes SET approved = '2' WHERE id = '".$id_quote."'");

		if ($edit == 'yes')
		{
			$approve_quote_log = mysql_query("INSERT INTO approve_quotes (id_quote, id_user, edit, quote_release) VALUES ('".$id_quote."', '".$auteur_id."', '1', '".$date_log."')");
		}
		else
		{
			$approve_quote_log = mysql_query("INSERT INTO approve_quotes (id_quote, id_user, quote_release) VALUES ('".$id_quote."', '".$auteur_id."', '".$date_log."')");
		}

		$waiting_moderation = mysql_query("SELECT id FROM teen_quotes_quotes WHERE auteur_id = '".$auteur_id."' AND approved = '0'");
		$waiting_send = mysql_query("SELECT id FROM approve_quotes WHERE id_user = '".$auteur_id."' AND send = '0'");
		
		if ($approve_quote AND mysql_num_rows($waiting_moderation) == 0 AND mysql_num_rows($waiting_send) == 1)
		{
			$edit_message = '';
			
			if ($language == "french")
			{
				if ($edit == 'yes')
				{
					$edit_message = '<br/><br/><b>Votre citation a été modifiée par notre équipe avant son approbation. Veuillez respecter la syntaxe, l\'orthographe et le sens de votre citation.</b>';
				}
				$message = ''.$top_mail.' Bonjour <font color="#394DAC"><b>'.$name_auteur.'</b></font> !<br/><br/>Votre citation a été <font color="#394DAC"><b>approuvée</b></font> récemment par un membre de notre équipe. Elle sera publiée le <b>'.$date.'</b> ('.$jours_posted.' '.$days_quote_posted.'), vous recevrez un email quand elle sera publiée sur le site.'.$edit_message.'<br/><br/>Voici votre citation :<br/><div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br/><br/><a href="http://kotado.fr" target="_blank">#'.$id_quote.'</a><span style="float:right">par <a href="http://kotado.fr/user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> le '.$date_quote.'</span></div>Cordialement,<br/><b>L\'équipe de Kotado</b>'.$end_mail;
			}
			else
			{
				if ($edit == 'yes')
				{
					$edit_message = '<br/><br/><b>Your quote has been modified by our team before approval. Please follow the syntax, the spelling and the meaning of your quote.</b>';
				}

				$message = ''.$top_mail.' Hello <font color="#394DAC"><b>'.$name_auteur.'</b></font> !<br/><br/>Your quote has been <font color="#394DAC"><b>approved</b></font> recently by a member of our team. It will be released on <b>'.$date.'</b> ('.$jours_posted.' '.$days_quote_posted .'), you will receive an email when it will be posted on the website.'.$edit_message.'<br/><br/>Here is your quote :<br/><div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br/><br/><a href="http://teen-quotes.com" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://teen-quotes.com/user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> on '.$date_quote.'</span></div>Sincerely,<br/><b>The Teen Quotes Team</b>'.$end_mail;
			}

			$mail = mail($email_auteur, $quote_added_queue, $message, $headers);
			$update_send = mysql_query("UPDATE approve_quotes SET send = '1' WHERE id_quote = '".$id_quote."' AND id_user = '".$auteur_id."' LIMIT 1");
			echo $succes.' The quote has been added to the queue. The author will be notified';
			echo '<meta http-equiv="refresh" content="0;url=admin" />';
		}
		elseif (mysql_num_rows($waiting_moderation) == 0 AND mysql_num_rows($waiting_send) >= 2)
		{
			include_once('../kernel/send_moderation.php');
			echo '<meta http-equiv="refresh" content="0;url=admin" />';
		}
		else
		{
			echo $succes;
			echo '<meta http-equiv="refresh" content="0;url=admin" />';
		}
	}
	else
	{
		$query_texte_quote = mysql_fetch_array(mysql_query("SELECT q.texte_english texte_english, q.date date, a.email email, a.username username FROM teen_quotes_quotes q, teen_quotes_account a WHERE q.auteur_id = a.id AND q.id = '".$id_quote."'"));
		$texte_quote = $query_texte_quote['texte_english'];
		$date_quote = $query_texte_quote['date'];
		$email_auteur = $query_texte_quote['email'];
		$name_auteur = $query_texte_quote['username'];

		$delete_quote = mysql_query("UPDATE teen_quotes_quotes SET approved = '-1' WHERE id='".$id_quote."'");
		$delete_quote_log = mysql_query("INSERT INTO approve_quotes (id_quote, id_user, approved) VALUES ('".$id_quote."', '".$auteur_id."', '0')");

		$waiting_moderation = mysql_query("SELECT id FROM teen_quotes_quotes WHERE auteur_id = '".$auteur_id."' AND approved = '0'");
		$waiting_send = mysql_query("SELECT id FROM approve_quotes WHERE id_user = '".$auteur_id."' AND send = '0'");	

		if ($delete_quote AND !empty($email_auteur)) 
		{
			if (mysql_num_rows($waiting_moderation) == 0 AND mysql_num_rows($waiting_send) == 1)
			{
				if ($language == "french")
				{
					$message = ''.$top_mail.'Bonjour <font color="#394DAC"><b>'.$name_auteur.'</b></font> !<br/><br/>Votre citation a été <font color="#394DAC"><b>rejetée</b></font> récemment par un membre de notre équipe...<br/><div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br/><br/><a href="http://kotado.fr" target="_blank">#'.$id_quote.'</a><span style="float:right">par <a href="http://kotado.fr/user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> le '.$date_quote.'</span></div>Cordialement,<br/><b>The Kotado Team</b>'.$end_mail;
				}
				else
				{
					$message = ''.$top_mail.' Hello <font color="#394DAC"><b>'.$name_auteur.'</b></font> !<br/><br/>Your quote has been <font color="#394DAC"><b>rejected</b></font> recently by a member of our team...<br/><div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br/><br/><a href="http://teen-quotes.com" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://teen-quotes.com/user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> on '.$date_quote.'</span></div>Sincerely,<br/><b>The Teen Quotes Team</b>'.$end_mail;
				}

				$mail = mail($email_auteur, $quote_rejected, $message, $headers); 
				echo $succes.' The author has been notified successfully !';
				echo '<meta http-equiv="refresh" content="0;url=admin" />';
			}
			elseif (mysql_num_rows($waiting_moderation) == 0 AND mysql_num_rows($waiting_send) >= 2)
			{
				include_once('../kernel/send_moderation.php');
				echo '<meta http-equiv="refresh" content="0;url=admin" />';
			}
			else
			{
				echo $succes;
				echo '<meta http-equiv="refresh" content="0;url=admin" />';
			}
		}									
	}	
}
elseif ($action == "delete_comment") 
{ 
	echo '
	<div class="post">
	<h2><img src="http://'.$domaine.'/images/icones/delete.png" class="icone" />Delete a comment</h2>';
	
	$id_comment = htmlspecialchars($_GET['id']);

	$donnees = mysql_fetch_array(mysql_query("SELECT c.auteur_id auteur_id, c.texte texte, c.id_quote id_quote, c.date date, a.username auteur, a.email email, a.avatar avatar FROM teen_quotes_comments c, teen_quotes_account a WHERE c.auteur_id = a.id AND c.id = '".$id_comment."'"));
	$auteur_id = $donnees['auteur_id'];
	$id_quote = $donnees['id_quote'];
	$name_auteur = $donnees['auteur'];
	$texte_comment = stripslashes($donnees['texte']);
	$date_comment = $donnees['date'];
	$email_auteur = $donnees['email'];
	$avatar = $donnees['avatar'];
	$username_comment = $name_auteur;
	
	$select_quote = mysql_fetch_array(mysql_query("SELECT q.auteur_id auteur_id, q.texte_english texte_english, q.date date, a.username auteur FROM teen_quotes_quotes q, teen_quotes_account a WHERE q.auteur_id = a.id AND q.id = '".$id_quote."'"));
	$txt_quote = $select_quote['texte_english'];
	$auteur_id = $select_quote['auteur_id'];
	$auteur = $select_quote['auteur']; 
	$date_posted = $select_quote['date'];
	
	if ($language == "french")
	{
		$message = ''.$top_mail.' 
			Bonjour <font color="#394DAC"><b>'.$name_auteur.'</b></font> !<br/><br/>
		Votre commentaire a été <b>supprimé</b> récemment par un membre de notre équipe sur cette citation :<br/>
		<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">
			'.$txt_quote.'<br/><br/>
			<a href="http://teen-quotes.com/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">'.$by.' <a href="http://teen-quotes.com/user-'.$auteur_id.'" target="_blank" title="'.$view_his_profile.'">'.$auteur.'</a> '.$on.' '.$date_posted.'</span>
		</div>
		Voici votre commentaire :
		<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">
		'.$texte_comment.'<br/><br/>
		<a href="http://teen-quotes.com/user-'.$id.'" title="'.$view_his_profile.'"><img src="http://'.$domaine.'/images/avatar/'.$avatar.'" style="border:2px solid #394DAC;float:left;height:20px;margin-right:5px;margin-top:-10px;width:20px" /></a><span style="float:right">par <a href="http://teen-quotes.com/user-'.$id.'" title="'.$view_his_profile.'">'.$username_comment.'</a> '.$on.' '.$date_comment.'</span><br/>
		</div>
		Cordialement,<br/>
		<b>The Teen Quotes Team</b>'.$end_mail;
	}
	else
	{
		$message = '
		'.$top_mail.' Hello <font color="#394DAC"><b>'.$name_auteur.'</b></font> !<br/><br/>
		Your comment has been <b>deleted</b> recently by a member of our team on this quote :<br/>
		<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">
			'.$txt_quote.'<br/><br/>
			<a href="http://teen-quotes.com/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">'.$by.' <a href="http://teen-quotes.com/user-'.$auteur_id.'" target="_blank" title="'.$view_his_profile.'">'.$auteur.'</a> '.$on.' '.$date_posted.'</span>
		</div>
		Here is your comment :
		<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">
		'.$texte_comment.'<br/><br/>
		<a href="http://teen-quotes.com/user-'.$id.'" title="'.$view_his_profile.'"><img src="http://'.$domaine.'/images/avatar/'.$avatar.'" style="border:2px solid #394DAC;float:left;height:20px;margin-right:5px;margin-top:-10px;width:20px" /></a><span style="float:right">par <a href="http://teen-quotes.com/user-'.$id.'" title="'.$view_his_profile.'">'.$username_comment.'</a> '.$on.' '.$date_comment.'</span><br/>
		</div>
		Sincerely,<br/>
		<b>The Teen Quotes Team</b>
		'.$end_mail;
	}
	$mail = mail($email_auteur, $comment_deleted, $message, $headers); 

	$delete = mysql_query("DELETE FROM teen_quotes_comments where id = '".$id_comment."'");

	if ($delete AND $mail) 
	{
		echo $succes.' The author has been notified successfully !';
		echo '<meta http-equiv="refresh" content="1;url=admin" />';
	}			
}
elseif ($action == "edit") 
{
	echo '
	<div class="post">
	<h2><img src="http://'.$domaine.'/images/icones/profil.png" class="icone" />Edit a quote</h2>';
	
	$id_quote = $_GET['id'];
	
	$query_texte_quote = mysql_fetch_array(mysql_query("SELECT texte_english FROM teen_quotes_quotes WHERE id = '".$id_quote."'"));
	$texte_quote = $query_texte_quote['texte_english'];
	echo 'Edit quote #'.$id_quote.'<br/>
	<br/>
	<form action="?action=edit_quote" method="post">
		<input type="hidden" name="id_quote" value="'.$id_quote.'" />
		<textarea name="texte_quote" style="width:100%;height:50px">'.$texte_quote.'</textarea>
		<br/><br/>
		<div class="clear"></div>
		<center><p><input type="submit" value="Edit AND approve this quote" class="submit" /></p></center>
	</form>';
}
elseif ($action == "edit_quote") 
{
	echo '
	<div class="post">
	<h2><img src="http://'.$domaine.'/images/icones/profil.png" class="icone" />Edit a quote</h2>';
	$id_quote = $_POST['id_quote'];
	$texte_quote = mysql_real_escape_string($_POST['texte_quote']);

	$update_quote = mysql_query("UPDATE teen_quotes_quotes SET texte_english = '$texte_quote' WHERE id = '$id_quote'");
	$id_auteur_query = mysql_fetch_array(mysql_query("SELECT auteur_id FROM teen_quotes_quotes WHERE id = '$id_quote'"));
	$id_auteur = $id_auteur_query['auteur_id'];

	if ($update_quote)
	{
		echo '<meta http-equiv="refresh" content="0;url=admin.php?action=rate&id='.$id_quote.'&approve=yes&auteur='.$id_auteur.'&edit=yes" />';
	}
}
elseif ($action == "edit_existing_quote")
{
	echo '
	<div class="post">
	<h2><img src="http://'.$domaine.'/images/icones/profil.png" class="icone" />Edit an existing quote</h2>';
	$id_quote = $_POST['id_quote'];
	$exist = mysql_num_rows(mysql_query("SELECT texte_english FROM teen_quotes_quotes WHERE id = '".$id_quote."' AND approved = '1'"));
	if ($exist == '1')
	{
		$result = mysql_fetch_array(mysql_query("SELECT q.texte_english texte_english, q.auteur_id auteur_id, q.date date, a.username auteur FROM teen_quotes_quotes q, teen_quotes_account a WHERE q.auteur_id = a.id AND q.id = '".$id_quote."' AND q.approved = '1'"));
		
		$txt_quote = $result['texte_english'];
		$auteur_id = $result['auteur_id'];
		$auteur = $result['auteur']; 
		$date = $result['date'];
		
		echo 'The original one :';
		echo '<div class="grey_post">';
		echo $txt_quote.'<br/><br/><a href="http://teen-quotes.com/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://teen-quotes.com/user-'.$auteur_id.'" target="_blank">'.$auteur.'</a> on '.$date.'</span>';
		echo '</div>';
		
		echo '
		Enter the new quote :<br/>
		<br/>
		<form action="?action=edit_existing_quote_valide" method="post">
		<input type="hidden" name="id_quote" value="'.$id_quote.'" />
		<textarea name="texte_quote" style="width:100%;height:50px">'.$txt_quote.'</textarea>
			<br/><br/>
			<div class="clear"></div>
			<center><p><input type="submit" value="Okay" class="submit" /></p></center>
		</form>';
	}
	else
	{
		echo '<h2>'.$error.'</h2> That quote doesn\'t exist ! '.$lien_retour;
	}
}
elseif ($action == "edit_existing_quote_valide")
{
	echo '
	<div class="post">
	<h2><img src="http://'.$domaine.'/images/icones/profil.png" class="icone" />Edit an existing quote</h2>';
	
	$id_quote = $_POST['id_quote'];
	$texte_quote = htmlspecialchars(mysql_real_escape_string($_POST['texte_quote']));
	
	$query = mysql_query("UPDATE teen_quotes_quotes SET texte_english = '".$texte_quote."' WHERE id = '".$id_quote."'");
	
	if ($query)
	{
		echo $succes.' Your quote has been edited !';
		echo '<meta http-equiv="refresh" content="2;url=admin" />';
	}
	else 
	{
		echo '<h2>'.$error.'</h2> '.$lien_retour;
	}
}
elseif ($action == "delete_existing_quote")
{
	$id_quote = mysql_real_escape_string($_POST['id_quote']);
	$date = date("d/m/Y");
	$ip = $_SERVER["REMOTE_ADDR"];
	$username = $_SESSION['username'];
	
	echo '
	<div class="post">
	<h2><img src="http://'.$domaine.'/images/icones/delete.png" class="icone" />Delete an existing quote</h2>';
	
	if (is_numeric($id_quote) AND !empty($username) AND !empty($ip) AND !empty($id_quote))
	{
		// Il n'y a qu'une seule quote à supprimer
		$delete_quote = mysql_query("UPDATE teen_quotes_quotes SET approved = '-1' WHERE id = '".$id_quote."'");
		$delete_fav_quote = mysql_query("DELETE FROM teen_quotes_favorite WHERE id_quote = '".$id_quote."'");
		
		if ($delete_quote AND $delete_fav_quote)
		{
			$log_result = mysql_query("INSERT INTO delete_quotes (date, username, ip, id_quote) VALUES ('".$date."','".$username."','".$ip."', '".$id_quote."')");
			
			if ($log_result)
			{
				echo $succes.' Your quote has been successfully unapproved.';
				echo '<meta http-equiv="refresh" content="1;url=admin" />';
			}
			else
			{
				echo '<h2>'.$error.'</h2> 1'.$lien_retour;
			}
		}
		else
		{
			echo '<h2>'.$error.'</h2> 2'.$lien_retour;
		}
	}
	elseif (preg_match('/,/',$id_quote) AND !preg_match('/%/',$id_quote) AND !empty($username) AND !empty($ip) AND !empty($id_quote)) // REGEX à vérifier
	{
		// Il y a plusieurs quotes à supprimer
		$delete_quote = mysql_query("UPDATE teen_quotes_quotes SET approved = '-1' WHERE id IN ('".$id_quote."')");
		$delete_fav_quote = mysql_query("DELETE FROM teen_quotes_favorite WHERE id_quote IN ('".$id_quote."')");
		
		if ($delete_quote AND $delete_fav_quote)
		{
			$log_result = mysql_query("INSERT INTO delete_quotes (date, username, ip, id_quote) VALUES ('".$date."','".$username."','".$ip."', '".$id_quote."')");
			
			if ($log_result)
			{
				echo $succes.' Your quotes has been deleted successfully.';
				echo '<meta http-equiv="refresh" content="1;url=admin" />';
			}
			else
			{
				echo '<h2>'.$error.'</h2> '.$lien_retour;
			}
		}
		else
		{
			echo '<h2>'.$error.'</h2> '.$lien_retour;
		}
	}
}
echo '</div>';
include "footer.php";
?>