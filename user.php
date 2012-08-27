<?php 
include 'header.php';
include 'lang/'.$language.'/user.php';
$i = 0;
$j = 0;

$id = mysql_real_escape_string($_GET['id_user']);
$exist_user = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE id='".$id."'"));
$logged = $_SESSION['logged'];

if ($exist_user == 0)
{
	echo '<meta http-equiv="refresh" content="0; url=error.php?erreur=404">';
}

if ($id != $_SESSION['id'] AND !empty($id) AND !empty($_SESSION['id']))
{
	$id_visitor = $_SESSION['id'];
	$insert_visitor = mysql_query("INSERT INTO teen_quotes_visitors (id_user,id_visitor) VALUES ('".$id."','".$id_visitor."')");
}

// FORMULAIRE
if (empty($id))
{
	echo '
	<div class="post">
	<h1>'.$error.'</h1>
	</div>';
}
else
{
	$result = mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_account WHERE id='".$id."'"));
	
	// SI LE PROFIL DOIT ETRE CACHE
	if ($result['hide_profile'] == '1' AND $id != $_SESSION['id'])
	{
		echo '
		<div class="bandeau_erreur">
		<span class="erreur">'.$hide_profile.'</span>
		'.$lien_retour.'
		</div>
		';
		
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
			</div>
			';
		}
	}
	elseif ($result['hide_profile'] == '0' OR ($result['hide_profile'] == '1' AND $id = $_SESSION['id']))
	{
		// AFFICHAGE DES INFOS DU PROFIL

		// OPTIMISATION POSSIBLE DES REQUETES SUIVANTES
		$nb_quotes_approved = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE auteur_id = '".$id."' AND approved = '1'"));
		$nb_quotes_submited = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE auteur_id = '".$id."'"));
		$nb_favorite_quotes = mysql_num_rows(mysql_query("SELECT DISTINCT id_quote FROM teen_quotes_favorite WHERE id_user='".$id."'"));
		$nb_comments = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_comments WHERE auteur_id = '".$id."'"));
		$nb_quotes_added_to_favorite = mysql_num_rows(mysql_query("SELECT F.id FROM teen_quotes_favorite F, teen_quotes_quotes Q WHERE F.id_quote = Q.id AND Q.auteur_id = '".$id."'"));
		
		if(empty($result['birth_date'])) {$result['birth_date'] = ''.$not_specified.'';}
		if(empty($result['title'])) {$result['title'] = ''.$not_specified.'';}
		if(empty($result['about_me'])) {$result['about_me'] = ''.$no_description.'';}
		if(empty($result['country'])) {$result['country'] = ''.$not_specified.'';}
		if(empty($result['city'])) {$result['city'] = ''.$not_specified.'';}
		if(empty($result['number_comments'])) {$result['number_comments'] = ''.$no_posted_comments.'';}
		if(empty($result['avatar'])) {$result['avatar'] = "icon50.png";}
		if($result['avatar'] =="http://".$domaine."/images/icon50.png") {$result['avatar'] = "icon50.png";}
		
		if ($result['birth_date'] != "$not_specified")
		{
			$age = age($result['birth_date']);
			$age = '('.$age.' '.$years_old.')';
		}
		else
		{
			$age = '';
		}
		
		echo '<div class="post">
		<div class="grey_post margin_profile_user">';
		
		if ($result['hide_profile'] == '1' AND $id = $_SESSION['id'])
		{
			echo '
			<div class="bandeau_erreur hide_this">
				<img src="http://'.$domaine.'/images/icones/infos.png" class="mini_plus_icone" alt="icone">'.$profile_hidden_self.'
			</div>';
		}
			
		echo '
			<img src="http://'.$domaine.'/images/avatar/'.$result['avatar'].'" class="user_avatar" />
			<h2>'.$result['username'].'<span class="right">'. $user_informations.'
			';
			if($id == $_SESSION['id']) 
			{
			    echo ' - <a class="submit" href="editprofile">'.$edit.'</a>';
			}
			echo '</span></h2>';
			echo '
			<div class="cadre_infos_profil">
				<span class="bleu">'.$title.' :</span> '.$result['title'].'<br>
				<span class="bleu">'.$birth_date.' :</span> '. $result['birth_date'].' '.$age.'<br>';

				if ($result['country'] != $not_specified)
				{
					echo '<span class="bleu">'.$country.' :</span> <a href="search?country='.$result['country'].'" class="link_grey" title="'.$search.' '.$result['country'].'">'.$result['country'].'</a><br>';
				}
				else
				{
					echo '<span class="bleu">'.$country.' :</span> '. $result['country'].'<br>';
				}

				if ($result['city'] != $not_specified)
				{
					echo '<span class="bleu">'.$city.' :</span> <a href="search?city='.$result['city'].'" class="link_grey" title="'.$search.' '.$result['city'].'">'. $result['city'].'</a><br>';
				}
				else
				{
					echo '<span class="bleu">'.$city.' :</span> '. $result['city'].'<br>';
				}

				echo '
				<span class="bleu">'.$fav_quote.' :</span> '. $nb_favorite_quotes.'<br>
				<span class="bleu">'.$number_comments.' :</span> '. $nb_comments.'<br>
				<span class="bleu">'.$number_quotes.' :</span> '.$nb_quotes_approved.' '.$validees.' '.$nb_quotes_submited.' '.$soumises.'<br>';
			if ($nb_quotes_approved > '0')
				{
				echo '
				<span class="bleu">'.$added_on_favorites.' :</span> '.$nb_quotes_added_to_favorite.'<br>
				';
				}
			echo '
			</div>
			<div class="clear"></div>
			<h3>'.$about_user.' '.$result['username'].'</h3>
			'.$result['about_me'].'
			';
			
		// DERNIERS VISITEURS DU PROFIL
		$query_visiteur = mysql_query("SELECT DISTINCT V.id_visitor id_visitor, A.username username_visitor, A.avatar avatar FROM teen_quotes_visitors V, teen_quotes_account A WHERE V.id_visitor = A.id AND V.id_user='".$id."' ORDER BY V.id DESC LIMIT 0,10"); 
		$num_rows_visitors = mysql_num_rows($query_visiteur);
		if ($num_rows_visitors > '0')
		{
			echo '
			<div class="slidedown">
			<h3>'.$last_visitor.'</h3>
				<div class="right">';
				while ($reponse_visiteur = mysql_fetch_array($query_visiteur))
				{
					$avatar = $reponse_visiteur['avatar'];
					$id_visitor = $reponse_visiteur['id_visitor'];
					$username_visitor = $reponse_visiteur['username_visitor'];
					
					echo '<a href="user-'.$id_visitor.'" title="'.$username_visitor.'"><img src="http://'.$domaine.'/images/avatar/'.$avatar.'" class="user_avatar_last_visitors" alt="'.$username_visitor.'"/></a>';
				}
				echo '
				</div>
			</div>
			<div class="clear"></div>
			</div>';
		}
		else
		{
			echo '</div>';
		}
			
		echo '</div>';
		
			if ($show_pub == '1')
			{
				echo
				'
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
				</div>
				';
			} 
		// CITATIONS FAVORITES
		echo '
		<div class="post" id="fav_quotes">
		<h2><img src="http://'.$domaine.'/images/icones/heart_big.png" class="icone" alt="icone">'.$favorite_quotes.'</h2>
		';
		
		if($nb_favorite_quotes >= 1)
		{
			$nb_messages_par_page = 5;

			$display_page_top = display_page_top($nb_favorite_quotes, $nb_messages_par_page, 'page_fav', $previous_page, $next_page, '#fav_quotes');
			$premierMessageAafficher = $display_page_top[0];
			$nombreDePages = $display_page_top[1];
			$page = $display_page_top[2];
	
			$reponse = mysql_query("SELECT DISTINCT id_quote FROM teen_quotes_favorite WHERE id_user='".$id."' ORDER BY id DESC LIMIT ".$premierMessageAafficher." ,  ".$nb_messages_par_page."");
			while ($resultat = mysql_fetch_array($reponse))
			{
				$id_quote_fav = $resultat['id_quote'];
				$id_visitor = $_SESSION['id'];

				if ($logged AND $id_visitor != $id)
				{
					$donnees = mysql_fetch_array(mysql_query("SELECT q.texte_english texte_english, q.id id, q.auteur_id auteur_id, q.date date, a.username auteur,
															(SELECT COUNT(*)
															FROM teen_quotes_comments c
															WHERE q.id = c.id_quote) AS nb_comments,
															(SELECT COUNT(*)
															FROM teen_quotes_favorite f
															WHERE q.id = f.id_quote AND f.id_user = '$id_visitor') AS is_favorite
															FROM teen_quotes_quotes q, teen_quotes_account a 
															WHERE q.auteur_id = a.id AND q.id = '$id_quote_fav'"));
				}
				else
				{
					$donnees = mysql_fetch_array(mysql_query("SELECT q.texte_english texte_english, q.id id, q.auteur_id auteur_id, q.date date, a.username auteur,
															(SELECT COUNT(*)
															FROM teen_quotes_comments c
															WHERE q.id = c.id_quote) AS nb_comments
															FROM teen_quotes_quotes q, teen_quotes_account a 
															WHERE q.auteur_id = a.id AND q.id = '$id_quote_fav'"));
				}
				 
				$id_quote = $donnees['id'];
				$txt_quote = $donnees['texte_english'];
				$auteur_id = $donnees['auteur_id'];
				$auteur = $donnees['auteur']; 
				$date_quote = $donnees['date'];
				$nombre_commentaires = $donnees['nb_comments'];
				
				if ($logged AND $id_visitor != $id)
				{
					$is_favorite = $donnees['is_favorite'];
				}
				elseif ($id_visitor == $id)
				{
					$is_favorite = '1';
				}
				?>
		
				<div class="grey_post">
				<?php echo $txt_quote; ?><br>
					<div class="footer_quote">
						<a href="quote-<?php echo $id_quote_fav; ?>">#<?php echo $id_quote_fav; ?> - <?php afficher_nb_comments ($nombre_commentaires, $comments, $comment, $no_comments); ?></a><?php afficher_favori($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['id']); date_et_auteur ($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
					</div>
				<?php share_fb_twitter ($id_quote,$txt_quote,$share); ?> 
				</div>
				<?php 
				$i++;
			}
				
				display_page_bottom($page, $nombreDePages, 'page_fav', '#fav_quotes', $previous_page, $next_page);
				
			echo '</div>';
		}
		// PAS DE QUOTES FAVORITES
		else 
		{
			echo '
			<div class="bandeau_erreur">
			'.$no_fav_quotes.'
			</div>
			</div>';
		}
		// QUOTES AJOUTEES PAR L'USER
		echo '
		<div class="clear"></div>
		<div class="post" id="user_quotes">
		<h2><img src="http://'.$domaine.'/images/icones/profil.png" class="icone">'.$user_quotes.'</h2>
		';
			
		if($nb_quotes_approved >= 1)
		{
			$nb_messages_par_page = 5;

			$display_page_top = display_page_top($nb_quotes_approved, $nb_messages_par_page, 'page_user', $previous_page, $next_page, '#user_quotes');
			$premierMessageAafficher = $display_page_top[0];
			$nombreDePages = $display_page_top[1];
			$page = $display_page_top[2];

			if ($logged)
			{
					$reponse = mysql_query("SELECT q.texte_english texte_english, q.id id, q.auteur_id auteur_id, q.date date, a.username auteur,
										(SELECT COUNT(*)
										FROM teen_quotes_comments c
										WHERE q.id = c.id_quote) AS nb_comments,
										(SELECT COUNT(*)
										FROM teen_quotes_favorite f
										WHERE q.id = f.id_quote AND f.id_user = '$id_visitor') AS is_favorite
										FROM teen_quotes_quotes q, teen_quotes_account a 
										WHERE q.auteur_id = a.id AND q.auteur_id = '$id' AND q.approved = '1'
										ORDER BY q.id DESC LIMIT $premierMessageAafficher, $nb_messages_par_page");
			}
			else
			{
					$reponse = mysql_query("SELECT q.texte_english texte_english, q.id id, q.auteur_id auteur_id, q.date date, a.username auteur,
										(SELECT COUNT(*)
										FROM teen_quotes_comments c
										WHERE q.id = c.id_quote) AS nb_comments
										FROM teen_quotes_quotes q, teen_quotes_account a 
										WHERE q.auteur_id = a.id AND q.auteur_id = '$id' AND q.approved = '1'
										ORDER BY q.id DESC LIMIT $premierMessageAafficher, $nb_messages_par_page");
			}

			while ($result = mysql_fetch_array($reponse))
			{
				$id_quote = $result['id'];
				$txt_quote = $result['texte_english'];
				$auteur_id = $result['auteur_id'];
				$auteur = $result['auteur']; 
				$date_quote = $result['date'];
				$nombre_commentaires = $result['nb_comments'];
				if ($logged)
				{
					$is_favorite = $result['is_favorite'];
				}
				
				$id_user_co = $_SESSION['id'];
				?>
				<div class="grey_post">
				<?php echo $txt_quote; ?><br>
					<div class="footer_quote">
						<a href="quote-<?php echo $id_quote; ?>">#<?php echo $id_quote; ?> - <?php afficher_nb_comments ($nombre_commentaires, $comments, $comment, $no_comments); ?></a><?php afficher_favori($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['id']); date_et_auteur ($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
					</div>
				<?php share_fb_twitter ($id_quote,$txt_quote,$share); ?> 
				</div>
				<?php
				$j++;
			}
				
			display_page_bottom($page, $nombreDePages, 'page_user', '#user_quotes', $previous_page, $next_page);
				
			echo '</div>';
		}
		// PAS DE QUOTES AJOUTEES PAR L'USER
		else
		{
			echo '
			<div class="bandeau_erreur">
			'.$no_quotes.'
			</div>
			</div>';
		}
	}
}

include "footer.php";
?>