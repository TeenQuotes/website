<?php 
include 'header.php';
$action=$_GET['action'];
if ($_SESSION['security_level'] <'2') 
	{
	echo '<meta http-equiv="refresh" content="0; url=error.php?erreur=403">';
	} 
elseif (empty($action) AND $_SESSION['security_level'] >='2') 
	{
	$nb_quote_awaiting_post = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved='2'"));
	$jours_posted = floor($nb_quote_awaiting_post / $nb_quote_released_per_day);
	if ($nb_quote_awaiting_post % $nb_quote_released_per_day != '0')
		{
		$jours_posted = $jours_posted + 1;
		}
	if ($jours_posted > '1')
		{
		$days_quote_posted = "days";
		}
	else
		{
		$days_quote_posted = "day";
		}
	?>
	<div class="post">
		<h2><img src="http://www.teen-quotes.com/images/icones/add.png" class="icone" />Add a quote</h2>
		<form action="?action=add_quote" method="post">
			Enter the Quote<br>
			<textarea name="texte_quote" style="width:100%;height:50px"></textarea>
			<br /><br />
			<div class="clear"></div>
			<center><p><input type="submit" value="Okey" class="submit" /></p></center>
		</form>
	</div>
	
	<div class="post">
		<h2><img src="http://teen-quotes.com/images/icones/profil.png" class="icone" />Edit an existing quote</h2>
		<form action="?action=edit_existing_quote" method="post">
			Enter the ID of the quote<br>
			<input name="id_quote" type="text" />
			<br />
			<center><p><input type="submit" value="Okey" class="submit" /></p></center>
		</form>
	</div>

	<div class="post">
		<h2><img src="http://www.teen-quotes.com/images/icones/test.png" class="icone" />Approve Quotes</h2>
	<?php 
		echo '
		<div class="grey_post">
		Number of citations waiting to be posted : '.$nb_quote_awaiting_post.' ('.$jours_posted.' '.$days_quote_posted .')
		</div>
		';
	$query = mysql_query("SELECT * FROM teen_quotes_quotes WHERE approved='0' ORDER BY id ASC");
	while ($result = mysql_fetch_array($query)) 
		{
		$txt_quote = $result['texte_english'];
		$auteur_id = $result['auteur_id'];
		$auteur = $result['auteur']; 
		$date = $result['date'];
		$id_quote = $result ['id'];
		
		echo '
		<div class="grey_post" data-id="'.$id_quote.'">
			'.$txt_quote.'<br><br />
			
			<span class="admin_quote" data-id="'.$id_quote.'">
			<a href="admin.php?action=rate&approve=yes&id='.$id_quote.'&auteur='.$auteur_id.'"><img src="http://www.teen-quotes.com/images/icones/succes.png" class="mini_icone" /></a>
			<a href="admin.php?action=edit&id='.$id_quote.'"><img src="http://www.teen-quotes.com/images/icones/profil.png" class="mini_icone" /></a>
			<a href="admin.php?action=rate&approve=no&id='.$id_quote.'&auteur='.$auteur_id.'"><img src="http://www.teen-quotes.com/images/icones/delete.png" class="mini_icone" /></a>
			</span>
			
			<span class="right">'.$by.' <a href="user-'.$auteur_id.'" title="View his profile">'.$auteur.'</a> '.$on.' '.$date.'</span><br><br />
		</div>';
		}
	echo '</div>';
	}
elseif ($action=="add_quote") 
	{
	echo '
	<div class="post">
	<h2><img src="http://www.teen-quotes.com/images/icones/add.png" class="icone" />Add a quote</h2>';
	
	$texte_quote= htmlspecialchars(mysql_escape_string($_POST['texte_quote']));
	$date = date("d/m/Y");
	$texte_quote=stripslashes($texte_quote);


	if (strlen($texte_quote) >= '30') 
		{
		$query = mysql_query("INSERT INTO teen_quotes_quotes (texte_english,auteur,date,auteur_id,approved) VALUES ('$texte_quote', '$username', '$date', '$id','2')");
		if ($query) 
			{
			echo ''.$succes.' <a href="../admin">Add anoter one</a>';
			}
		else 
			{
			echo '<h2>'.$error.'</h2> '.$lien_retour.'';
			}
		}
	else 
		{
		echo '<h2>'.$error.' : too short</h2> '.$lien_retour.'';
		}
	}
elseif ($action=="rate") 
	{
	echo '
	<div class="post">
	<h2><img src="http://www.teen-quotes.com/images/icones/test.png" class="icone" />Approve Quotes</h2>';

	$id_quote = mysql_real_escape_string($_GET['id']);
	$approve = mysql_real_escape_string($_GET['approve']);
	$auteur_id = mysql_real_escape_string($_GET['auteur']);

	if ($approve == "yes") 
		{
		$approve_quote= mysql_query("UPDATE teen_quotes_quotes set approved='2' WHERE id='$id_quote'");
		
		if ($approve_quote) 
			{
			echo ''.$succes.' The author has been notified successfully !';
			echo '<meta http-equiv="refresh" content="1;url=admin" />';
			}
		}
	else
		{
		$query_texte_quote=mysql_fetch_array(mysql_query("SELECT texte_english,date FROM teen_quotes_quotes WHERE id='$id_quote'"));
		$texte_quote=$query_texte_quote['texte_english'];
		$date_quote = $query_texte_quote['date'];

		$delete_quote= mysql_query("UPDATE teen_quotes_quotes set approved='-1' WHERE id='$id_quote'");

		$query_email_auteur=mysql_fetch_array(mysql_query("SELECT email,username FROM teen_quotes_account WHERE id='$auteur_id'"));
		$email_auteur=$query_email_auteur['email'];
		$name_auteur=ucfirst($query_email_auteur['username']);

		if ($delete_quote AND !empty($email_auteur)) 
			{
			$message = ''.$top_mail.' Hello <font color="#5C9FC0"><b>'.$name_auteur.'</b></font> !<br><br />Your quote has been <font color="#5C9FC0"><b>rejected</b></font> recently by a member of our team...<br><div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br><br /><a href="http://www.teen-quotes.com" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://www.teen-quotes.com/user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> on '.$date_quote.'</span></div>Sincerely,<br><b>The Teen Quotes Team</b><br /><br /><br /><div style="border-top:1px dashed #CCCCCC"></div><br /><br />VERSION FRANCAISE :<br /><br />Bonjour <font color="#5C9FC0"><b>'.$name_auteur.'</b></font> !<br><br />Votre citation a été <font color="#5C9FC0"><b>rejetée</b></font> récemment par un membre de notre équipe...<br><div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br><br /><a href="http://www.teen-quotes.com" target="_blank">#'.$id_quote.'</a><span style="float:right">par <a href="http://www.teen-quotes.com/user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> le '.$date_quote.'</span></div>Cordialement,<br><b>The Teen Quotes Team</b> '.$end_mail.'';
			$mail = mail($email_auteur, "Quote rejected", $message, $headers); 
			echo ''.$succes.' The author has been notified successfully !';
			echo '<meta http-equiv="refresh" content="1;url=admin" />';
			}
		}
	}
elseif ($action=="delete_comment") 
	{ 
	echo '
	<div class="post">
	<h2><img src="http://www.teen-quotes.com/images/icones/delete.png" class="icone" />Delete a comment</h2>';
	
	$id_comment = htmlspecialchars($_GET['id']);

	$donnees = mysql_fetch_array(mysql_query("SELECT auteur, auteur_id, texte,id_quote,date FROM teen_quotes_comments WHERE id='$id_comment'"));
	$auteur_id = $donnees['auteur_id'];
	$id_quote = $donnees['id_quote'];
	$name_auteur = ucfirst($donnees['auteur']);
	$texte_comment = stripslashes($donnees['texte']);
	$date_comment = $donnees['date'];

	$donnees2 = mysql_fetch_array(mysql_query("SELECT email,avatar,username FROM teen_quotes_account WHERE id='$auteur_id'"));
	$email_auteur = $donnees2['email'];
	$avatar = $donnees2['avatar'];
	$username_comment = $donnees2['username'];
	
	$select_quote = mysql_fetch_array(mysql_query("SELECT auteur_id, auteur, texte_english, date FROM teen_quotes_quotes WHERE id='$id_quote'"));
	$txt_quote = $select_quote['texte_english'];
	$auteur_id = $select_quote['auteur_id'];
	$auteur = $select_quote['auteur']; 
	$date_posted = $select_quote['date'];

	$message = '
	'.$top_mail.' Hello <font color="#5C9FC0"><b>'.$name_auteur.'</b></font> !<br><br />
	Your comment has been <b>deleted</b> recently by a member of our team on this quote :<br>
	<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">
		'.$txt_quote.'<br><br />
		<a href="http://www.teen-quotes.com/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">'.$by.' <a href="http://www.teen-quotes.com/user-'.$auteur_id.'" target="_blank" title="'.$view_his_profile.'">'.$auteur.'</a> '.$on.' '.$date_posted.'</span>
	</div>
	Here is your comment :
	<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">
	'.$texte_comment.'<br><br />
	<a href="http://www.teen-quotes.com/user-'.$id.'" title="'.$view_his_profile.'"><img src="http://www.teen-quotes.com/images/avatar/'.$avatar.'" style="border:2px solid #5C9FC0;float:left;height:20px;margin-right:5px;margin-top:-10px;width:20px" /></a><span style="float:right">par <a href="http://teen-quotes.com/user-'.$id.'" title="'.$view_his_profile.'">'.$username_comment.'</a> '.$on.' '.$date_comment.'</span><br>
	</div>
	Sincerely,<br>
	<b>The Teen Quotes Team</b><br /><br />
	<div style="border-top:1px dashed #CCC"></div><br />
	VERSION FRANCAISE :<br /><br />
	Bonjour <font color="#5C9FC0"><b>'.$name_auteur.'</b></font> !<br><br />
	Votre commentaire a été <b>supprimé</b> récemment par un membre de notre équipe sur cette citation :<br>
	<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">
		'.$txt_quote.'<br><br />
		<a href="http://www.teen-quotes.com/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">'.$by.' <a href="http://www.teen-quotes.com/user-'.$auteur_id.'" target="_blank" title="'.$view_his_profile.'">'.$auteur.'</a> '.$on.' '.$date_posted.'</span>
	</div>
	Voici votre commentaire :
	<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">
	'.$texte_comment.'<br><br />
	<a href="http://www.teen-quotes.com/user-'.$id.'" title="'.$view_his_profile.'"><img src="http://www.teen-quotes.com/images/avatar/'.$avatar.'" style="border:2px solid #5C9FC0;float:left;height:20px;margin-right:5px;margin-top:-10px;width:20px" /></a><span style="float:right">par <a href="http://teen-quotes.com/user-'.$id.'" title="'.$view_his_profile.'">'.$username_comment.'</a> '.$on.' '.$date_comment.'</span><br>
	</div>
	Cordialement,<br>
	<b>The Teen Quotes Team</b> 
	'.$end_mail.'
	';
	$mail = mail($email_auteur, "Comment deleted", $message, $headers); 

	$delete=mysql_query("DELETE FROM teen_quotes_comments where id='$id_comment'");

	if ($delete AND $mail) 
		{
		echo ''.$succes.' The author has been notified successfully !';
		echo '<meta http-equiv="refresh" content="1;url=admin" />';
		}			
	}
elseif ($action=="edit") 
	{
	echo '
	<div class="post">
	<h2><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone" />Edit a quote</h2>';
	
	$id_quote=$_GET['id'];

	$query_texte_quote=mysql_fetch_array(mysql_query("SELECT texte_english FROM teen_quotes_quotes WHERE id='$id_quote'"));
	$texte_quote=$query_texte_quote['texte_english'];
	echo 'Edit quote #'.$id_quote.'<br>
	<br />
	<form action="?action=edit_quote" method="post">
		<input type="hidden" name="id_quote" value="'.$id_quote.'" />
		<textarea name="texte_quote" style="width:100%;height:50px">'.$texte_quote.'</textarea>
		<br /><br />
		<div class="clear"></div>
		<center><p><input type="submit" value="Edit AND approve this quote" class="submit" /></p></center>
	</form>';
		
	}
elseif ($action=="edit_quote") 
	{
	echo '
	<div class="post">
	<h2><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone" />Edit a quote</h2>';
	$id_quote=$_POST['id_quote'];
	$texte_quote=$_POST['texte_quote'];

	$update_quote = mysql_query("UPDATE teen_quotes_quotes SET texte_english='$texte_quote' WHERE id='$id_quote'");
	$id_auteur_query = mysql_fetch_array(mysql_query("SELECT auteur_id FROM teen_quotes_quotes WHERE id='$id_quote'"));
	$id_auteur = $id_auteur_query['auteur_id'];

	if ($update_quote)
		{
		echo '<meta http-equiv="refresh" content="1;url=admin.php?action=rate&id='.$id_quote.'&approve=yes&auteur='.$id_auteur.'&edit=yes" />';
		}
		
	}
elseif ($action=="edit_existing_quote")
	{
	echo '
	<div class="post">
	<h2><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone" />Edit an existing quote</h2>';
	$id_quote=$_POST['id_quote'];
	$exist = mysql_num_rows(mysql_query("SELECT texte_english FROM teen_quotes_quotes WHERE id='$id_quote' AND approved='1'"));
	if ($exist == '1')
		{
		$result = mysql_fetch_array(mysql_query("SELECT texte_english, auteur, auteur_id, date FROM teen_quotes_quotes WHERE id = '$id_quote' AND approved = '1'"));
		
		$txt_quote = $result['texte_english'];
		$auteur_id = $result['auteur_id'];
		$auteur = $result['auteur']; 
		$date = $result['date'];
		
		echo 'The original one :';
		echo '<div class="grey_post">';
		echo ''.$txt_quote.'<br><br /><a href="http://www.teen-quotes.com/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://www.teen-quotes.com/user-'.$auteur_id.'" target="_blank">'.$auteur.'</a> on '.$date.'</span>';
		echo '</div>';
		
		echo '
		Enter the new quote :<br>
		<br />
		<form action="?action=edit_existing_quote_valide" method="post">
		<input type="hidden" name="id_quote" value="'.$id_quote.'" />
		<textarea name="texte_quote" style="width:100%;height:50px">'.$txt_quote.'</textarea>
			<br /><br />
			<div class="clear"></div>
			<center><p><input type="submit" value="Okey" class="submit" /></p></center>
		</form>';
		}
	else
		{
		echo '<h2>'.$error.'</h2> That quote doesn\'t exist ! '.$lien_retour.'';
		}
	}
elseif ($action=="edit_existing_quote_valide")
	{
	echo '
	<div class="post">
	<h2><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone" />Edit an existing quote</h2>';
	$id_quote=$_POST['id_quote'];
	$texte_quote= htmlspecialchars(mysql_escape_string($_POST['texte_quote']));
	$texte_quote=stripslashes($texte_quote);
	
	$query = mysql_query("UPDATE teen_quotes_quotes SET texte_english='$texte_quote' WHERE id='$id_quote'");
	
	if ($query)
		{
		echo ''.$succes.' Your quote has been edited !';
		echo '<meta http-equiv="refresh" content="2;url=admin" />';
		}
	else 
		{
		echo '<h2>'.$error.'</h2> '.$lien_retour.'';
		}
	}

echo '</div>';
include "footer.php"; ?>