<?php 
include 'header.php'; 
include 'lang/'.$language.'/quote.php';

$id_quote = mysql_real_escape_string($_GET['id_quote']);
$logged = $_SESSION['logged'];
$exist_quote = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE id = '$id_quote' AND approved = '1'"));

if ($exist_quote == 0 OR empty($id_quote)) 
	{
	echo '<meta http-equiv="refresh" content="0; url=error.php?erreur=404">';
	}

if ($logged)
{
	$result = mysql_fetch_array(mysql_query("SELECT q.texte_english texte_english, q.id id, q.auteur_id auteur_id, q.date date, a.username auteur,
											(SELECT COUNT(*)
											FROM teen_quotes_comments c
											WHERE q.id = c.id_quote) AS nb_comments,
											(SELECT COUNT(*)
											FROM teen_quotes_favorite f
											WHERE q.id = f.id_quote AND f.id_user = '$id') AS is_favorite
											FROM teen_quotes_quotes q, teen_quotes_account a 
											WHERE q.auteur_id = a.id AND q.id = '$id_quote'"));
}
else
{
	$result = mysql_fetch_array(mysql_query("SELECT q.texte_english texte_english, q.id id, q.auteur_id auteur_id, q.date date, a.username auteur,
											(SELECT COUNT(*)
											FROM teen_quotes_comments c
											WHERE q.id = c.id_quote) AS nb_comments
											FROM teen_quotes_quotes q, teen_quotes_account a 
											WHERE q.auteur_id = a.id AND q.id = '$id_quote'"));
}

	$txt_quote = $result['texte_english'];
	$auteur_id = $result['auteur_id'];
	$auteur = $result['auteur']; 
	$date_quote = $result['date'];
	$nombre_commentaires = $result['nb_comments'];
	if ($logged)
	{
		$is_favorite = $result['is_favorite'];
	}
?>

	<div class="post">
		<?php echo $txt_quote; ?><br/>
		<div class="footer_quote">
		<a href="quote-<?php echo $result['id']; ?>">#<?php echo $result['id']; ?></a><?php afficher_favori($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['id']);date_et_auteur ($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
		</div>
		<?php share_fb_twitter ($id_quote,$txt_quote,$share); ?> 
	</div>
	
	<?php
	if ($show_pub == '1')
	{
		echo '
		<div class="pub">
		<script type="text/javascript"><!--
		google_ad_client = "ca-pub-8130906994953193";
		/* Page quote */
		google_ad_slot = "8219438641";
		google_ad_width = 468;
		google_ad_height = 60;
		//-->
		</script>
		<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
		<script type="text/javascript"><!--
		google_ad_client = "ca-pub-8130906994953193";
		/* Page quote 2 */
		google_ad_slot = "4669557053";
		google_ad_width = 234;
		google_ad_height = 60;
		//-->
		</script>
		<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
		</div>';
	}
	$comments_ucfirst = ucfirst($comments);

	echo '
	<div class="post slidedown">
	<h2><img src="http://'.$domaine.'/images/icones/about.png" class="icone" alt="icone" />'.$comments_ucfirst.''; if ($nombre_commentaires >'1'){echo '<span class="right">'.$nombre_commentaires.' '.$comment.'s</span>';}else{echo'<span class="right">'.$nombre_commentaires.' '.$comment.'</span>';}echo '</h2>';
	if ($_SESSION['logged']) 
	{
		echo '
		<form action="addcomment" method="post">
			<input type="hidden" name="id_quote" value="'.$id_quote.'" />
			<textarea name="texte" id="textarea_add_comment" placeholder="'.$warning_comments.'"/></textarea> 
			<center><p><input type="submit" value="'.$add_my_comment.'" class="submit" /></p></center>
		</form>
		';
	}
	else
	{
		echo '
		<span class="erreur">'.$must_be_log.'</span><br/>
		<br/>
		';
	}
		
		
	if ($nombre_commentaires >= 1)
	{ // affichage si seulement il y a des commentaires
		$nb_messages_par_page = 10;

		$display_page_top = display_page_top($nombre_commentaires, $nb_messages_par_page, 'p', $previous_page, $next_page);
		$premierMessageAafficher = $display_page_top[0];
		$nombreDePages = $display_page_top[1];
		$page = $display_page_top[2];
		
		$commentaires = mysql_query("SELECT c.id id, c.auteur_id auteur_id, c.texte texte, c.date date, a.username auteur, a.avatar avatar FROM teen_quotes_comments c, teen_quotes_account a WHERE c.auteur_id = a.id AND c.id_quote = '$id_quote' ORDER BY c.id ASC LIMIT $premierMessageAafficher ,  $nb_messages_par_page");
		while ($donnees = mysql_fetch_array ($commentaires))
		{
			$id_comment = $donnees['id'];
			$id_auteur = $donnees['auteur_id'];
			$avatar = $donnees['avatar'];
			$texte_stripslashes = stripslashes($donnees['texte']);
			
			echo '
			<div class="grey_post">
			'.$texte_stripslashes.'<br/><br/>
			<a href="user-'.$donnees['auteur_id'].'" title="'.$view_his_profile.'"><img src="http://'.$domaine.'/images/avatar/'.$avatar.'" class="mini_user_avatar" alt="Avatar" /></a>'; 
			if ($_SESSION['security_level'] >= 2 OR $id_auteur == $id)
			{
				echo '<span class="favorite">';
				if ($id_auteur == $id)
				{
					echo '<a href="editcomment-'.$id_comment.'"><img src="http://'.$domaine.'/images/icones/profil.png" class="mini_icone" /></a>';
				}
				if ($_SESSION['security_level'] >= '2')
				{
					echo '<a href="admin.php?action=delete_comment&id='.$id_comment.'"><img src="http://'.$domaine.'/images/icones/delete.png" class="mini_icone" /></a>';
				}
				echo '</span>';
			}
			
			echo '<span class="right">'.$by.' <a href="user-'.$donnees['auteur_id'].'" title="'.$view_his_profile.'">'.$donnees['auteur'].'</a> '.$on.' '.$donnees['date'].'</span><br/>
			</div>';
		}
			
		display_page_bottom($page, $nombreDePages, 'p', NULL, $previous_page, $next_page);
			
		echo '<div class="clear"></div>';
	}
	else 
	{ // NO COMMENTS
		echo '
		<div class="bandeau_erreur">
			'.$no_comments.'
		</div>
		';
	}

echo '</div>';

include 'footer.php'; 
?>