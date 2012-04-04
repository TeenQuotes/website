<?php 
include 'header.php'; 
include '../lang/'.$language.'/editcomment.php';
include '../lang/'.$language.'/quote.php'; 
$id_comment = mysql_real_escape_string($_GET['id_comment']);
$action = htmlspecialchars($_GET['action']);
if ($action == 'send' AND is_numeric($_POST['id_comment']))
	{
	$id_comment = $_POST['id_comment'];
	}

echo '
<div class="post">
<h2><img src="http://'.$domaine.'/images/icones/profil.png" class="icone" />'.$edit_comment.'</h2>
';

if (is_numeric($id_comment) AND !empty($id_comment))
	{
	$query_comment = mysql_query("SELECT * FROM teen_quotes_comments WHERE id = '".$id_comment."'");
	$fetch_comment = mysql_fetch_array($query_comment);
	$id_auteur = $fetch_comment['auteur_id'];
	$id_quote = $fetch_comment['id_quote'];
	$texte_comment = stripcslashes($fetch_comment['texte']);
	$date = $fetch_comment['date'];
	
	if (mysql_num_rows($query_comment) == 1 AND $id_auteur == $id)
		{
		if (empty($action))
			{
			$result = mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_quotes where id='".$id_quote."' AND approved ='1'"));
			$txt_quote = $result['texte_english'];
			$auteur_id = $result['auteur_id'];
			$auteur = $result['auteur']; 
			$date_quote = $result['date'];
			
			$query_avatar = mysql_fetch_array(mysql_query("SELECT avatar FROM teen_quotes_account where id='$id_auteur'"));
			$avatar = $query_avatar['avatar'];
			
			echo '
			'.$here_is_the_quote.'
			<div class="grey_post">
				'.$txt_quote.'<br>
				<div class="footer_quote">
				<a href="quote-'.$id_quote.'">#'.$id_quote.'</a>'; afficher_favori($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['id']);date_et_auteur($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile);
			echo '</div>
			</div>';
			
			echo ''.$here_is_your_comment.'';
			echo '
			<div class="grey_post">
				'.$texte_comment.'<br><br />
				<a href="user-'.$id_auteur.'" title="'.$view_his_profile.'"><img src="http://'.$domaine.'/images/avatar/'.$avatar.'" class="mini_user_avatar" /></a>
				<span class="right">'.$by.' <a href="user-'.$id_auteur.'" title="'.$view_his_profile.'">'.$username.'</a> '.$on.' '.$date.'</span><br>
			</div>';
			
			echo ''.$write_new_comment_here.'';
			echo '
			<form action="?action=send" method="post">
				<input type="hidden" name="id_comment" value="'.$id_comment.'" />
				<textarea name="texte_comment" style="width:100%;height:50px">'.$texte_comment.'</textarea>
				<br /><br />
				<div class="clear"></div>
				<center><p><input type="submit" value="'.$edit_my_comment.'" class="submit" /></p></center>
			</form>';
			}
		elseif($action == 'send')
			{
			$new_texte = htmlspecialchars(mysql_escape_string($_POST['texte_comment']));
			if (!empty($new_texte))
				{
				$update = mysql_query("UPDATE teen_quotes_comments SET texte = '".$new_texte."' WHERE id = '".$id_comment."'");
				
				if ($update)
					{
					echo '<meta http-equiv="refresh" content="5;url=quote-'.$id_quote.'" />';
					echo ''.$succes.' '.$comment_updated_successfully.'';
					}
				else
					{
					echo '<div class="bandeau_erreur">'.$error.'</div> '.$lien_retour.'';
					}
				}
			else
				{
				echo '<div class="bandeau_erreur">'.$please_enter_a_comment.'</div> '.$lien_retour.'';
				}
			}
		}
	else
		{
		echo '<div class="bandeau_erreur">'.$not_author.'</div>'.$lien_retour.'';
		}
	}
else
	{
	echo ''.$error.' '.$lien_retour.'';
	}

echo '</div>';
include 'footer.php'; 
?>