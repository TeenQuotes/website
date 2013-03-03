<?php 
include 'header.php'; 
include '../lang/'.$language.'/quote.php'; 

$id_quote = nl2br(htmlspecialchars(mysql_real_escape_string($_POST['id_quote'])));
$texte = htmlspecialchars(mysql_real_escape_string($_POST['texte']));
$date = date("d/m/Y"); 
$comments_ucfirst = ucfirst($comments);
echo '
<div class="post">
<h2><img src="http://'.$domain.'/images/icones/about.png" class="icone" />'.$comments_ucfirst.'</h2>
';

if (!empty($id_quote) AND !empty($texte)) 
{
	if (strlen($texte) <= 450) 
	{
		$query = mysql_query("INSERT INTO teen_quotes_comments (id_quote, texte, auteur_id, date) VALUES ('$id_quote', '$texte', '$id', '$date')");
		
		$select_quote = mysql_fetch_array(mysql_query("SELECT q.auteur_id auteur_id, q.texte_english texte_english, q.date date, a.username auteur, a.email email, a.notification_comment_quote notification_comment_quote FROM teen_quotes_quotes q, teen_quotes_account a WHERE q.id = '$id_quote' AND q.auteur_id = a.id"));
		$txt_quote = $select_quote['texte_english'];
		$auteur_id = $select_quote['auteur_id'];
		$auteur = $select_quote['auteur']; 
		$date_posted = $select_quote['date'];
		$texte_comment = stripslashes(stripslashes($texte));
		$email_auteur = $select_quote['email'];
		$notification_comment_quote = $select_quote['notification_comment_quote'];
		
		if ($notification_comment_quote == '1')
		{
			$avatar = $_SESSION['avatar'];
				
			$comment_added_mail = '
			'.$top_mail.' 
			Hello '.$auteur.',<br/><br/>
			A comment has been added on your quote :
			<br/>
			<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">
				'.$txt_quote.'<br/><br/>
				<a href="http://teen-quotes.com/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">'.$by.' <a href="http://teen-quotes.com/user-'.$auteur_id.'" target="_blank" title="'.$view_his_profile.'">'.$auteur.'</a> '.$on.' '.$date_posted.'</span>
			</div>
			Here is the comment :
			<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">
			'.$texte_comment.'<br/><br/>
			<a href="http://teen-quotes.com/user-'.$id.'" title="'.$view_his_profile.'"><img src="http://'.$domain.'/images/avatar/'.$avatar.'" style="border:2px solid #394DAC;float:left;height:20px;margin-right:5px;margin-top:-10px;width:20px" /></a><span style="float:right">par <a href="http://teen-quotes.com/user-'.$id.'" title="'.$view_his_profile.'">'.$username.'</a> '.$on.' '.$date.'</span><br/>
			</div>
			'.$end_mail;
			
			$mail = mail($email_auteur, $comment_added_on_quote, $comment_added_mail, $headers);
		}
		
		if ($query) 
		{
			echo $comment_add_succes;
			echo '<meta http-equiv="refresh" content="3;url=quote-'.$id_quote.'" />';
		}
		else
		{
			echo '<h2>'.$error.'</h2>'.$lien_retour;
		}
	}
	else
	{
		echo '<span class="erreur">'.$comment_too_long.'</span>';
	}
}
else
{
	echo '<span class="erreur">'.$not_complete.'</span>';
}
	
echo '</div>';
include "footer.php";
?>