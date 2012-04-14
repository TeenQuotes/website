<?php 
include 'header.php'; 
include '../lang/'.$language.'/quote.php'; 
$id_quote=mysql_real_escape_string($_GET['id_quote']);
$exist_quote = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE id='$id_quote' AND approved='1'"));

if ($exist_quote=='0') 
	{
	echo '<meta http-equiv="refresh" content="0; url=error.php?erreur=404">';
	}

$nombre_commentaires= mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_comments WHERE id_quote='$id_quote'"));
$commentaires = mysql_query("SELECT * FROM teen_quotes_comments WHERE id_quote='$id_quote' ORDER BY id ASC");
$is_favorite = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_favorite WHERE id_quote='$id_quote' AND id_user='$id'"));
$logged= htmlspecialchars($_SESSION['logged']);
// SI PAS D'ID DONNE
if (empty($id_quote)) 
	{
	echo '
	<div class="post">
	<h1>'.$error.'</h1>
	</div>';
	include 'footer.php'; 
	}
else 
	{ 
	$result = mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_quotes where id='$id_quote' AND approved='1'"));
	$txt_quote = $result['texte_english'];
	$auteur_id = $result['auteur_id'];
	$auteur = $result['auteur']; 
	$date_quote = $result['date'];   ?>

	<div class="post">
	<?php echo $txt_quote; ?><br><br />
	<a href="quote-<?php echo $result['id']; ?>">#<?php echo $result['id']; ?></a><?php afficher_favori_m($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['id']);date_et_auteur_m($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
	</div>
	
	<?php
	if ($show_pub == '1')
		{
		echo '
		<div class="pub_middle">
		<script type="text/javascript"><!--
		google_ad_client = "ca-pub-8130906994953193";
		/* Pub page quote - mobile */
		google_ad_slot = "9237663358";
		google_ad_width = 320;
		google_ad_height = 50;
		//-->
		</script>
		<script type="text/javascript"
		src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>
		</div>';
		}
	$comments_ucfirst = ucfirst($comments);
	echo '
	<div class="post slidedown">
	<h2><img src="http://'.$domaine.'/images/icones/about.png" class="icone" />'.$comments_ucfirst.''; if ($nombre_commentaires >'1'){echo '<span class="right">'.$nombre_commentaires.' '.$comment.'s</span>';}else{echo'<span class="right">'.$nombre_commentaires.' '.$comment.'</span>';}echo '</h2>';
	if ($_SESSION['logged']) 
		{
		echo '
		<form action="addcomment" method="post">
		<input type="hidden" name="id_quote" value="'.$id_quote.'" />
		<textarea  name="texte" style="width:100%;height:50px" onFocus="javascript:this.value=\'\'">'.$warning_comments.'</textarea> 
		<center><p><input type="submit" value="Okey" class="submit" /></p></center>
		</form>
		';
		}
	else
		{
		echo '
		<span class="erreur">'.$must_be_log.'</span><br>
		<br />
		';
		}
		
		
	if ($nombre_commentaires >= '1')
		{ // affichage si seulement il y a des commentaires
		$nb_messages_par_page = '10';

		$display_page_top = display_page_top($nombre_commentaires, $nb_messages_par_page, 'p', $previous_page, $next_page);
		$premierMessageAafficher = $display_page_top[0];
		$nombreDePages = $display_page_top[1];
		$page = $display_page_top[2];
		
		$commentaires = mysql_query("SELECT * FROM teen_quotes_comments WHERE id_quote='$id_quote' ORDER BY id ASC LIMIT $premierMessageAafficher ,  $nb_messages_par_page");
		while ($donnees = mysql_fetch_array ($commentaires))
			{
			$id_comment = $donnees['id'];
			$id_auteur = $donnees['auteur_id'];
			$query_avatar = mysql_fetch_array(mysql_query("SELECT avatar FROM teen_quotes_account where id='$id_auteur'"));
			$avatar = $query_avatar['avatar'];
			$texte_stripslashes = stripslashes($donnees['texte']);
			
			echo '
			<div class="grey_post">
			'.$texte_stripslashes.'<br><br />
			<a href="user-'.$donnees['auteur_id'].'" title="'.$view_his_profile.'"><img src="http://'.$domaine.'/images/avatar/'.$avatar.'" class="mini_user_avatar" /></a>'; 
			if ($_SESSION['security_level'] >= '2' OR $id_auteur == $id)
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
			
			echo '<span class="right">'.$by.' <a href="user-'.$donnees['auteur_id'].'" title="'.$view_his_profile.'">'.$donnees['auteur'].'</a> '.$on.' '.$donnees['date'].'</span><br>
			</div>';
			}
			
		display_page_bottom($page, $nombreDePages, 'p', NULL, $previous_page, $next_page);
			
		echo '<div class="clear"></div>';
		echo '</div>';
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
	}

include 'footer.php'; 
?>