<?php 
include 'header.php';
include '../lang/'.$language.'/user.php';
$i = '0';
$j = '0';

$id = mysql_real_escape_string($_GET['id_user']);
$exist_user = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE id='$id'"));
if ($exist_user=='0') {header("Location: error.php?erreur=404"); }

if($id != $_SESSION['id'] AND !empty($id) AND !empty($_SESSION['id']))
	{
	$id_visitor = $_SESSION['id'];
	$insert_visitor = mysql_query("INSERT INTO teen_quotes_visitors (id_user,id_visitor) VALUES ('$id','$id_visitor')");
	}

// FORMULAIRE
if (empty($id))
	{
	echo '
	<div class="post">
	<h1>'.$error.'</h1>
	</div>';
include 'footer.php'; 
	}
else
	{
	$result = mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_account where id='$id'"));
	
	// SI LE PROFIL DOIT ETRE CACHE
	if ($result['hide_profile'] == '1' AND $id != $_SESSION['id'])
		{
		echo '
		<div class="bandeau_erreur">
		<span class="erreur">'.$hide_profile.'</span>
		'.$lien_retour.'
		</div>
		';
		}
	elseif ($result['hide_profile'] == '0' OR ($result['hide_profile'] == '1' AND $id = $_SESSION['id']))
		{
		// AFFICHAGE DES INFOS DU PROFIL
		$nb_quotes_approved=mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes where auteur_id='$id' AND approved='1'"));
		$nb_quotes_submited=mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes where auteur_id='$id'"));
		$nb_favorite_quotes=mysql_num_rows(mysql_query("SELECT DISTINCT id_quote FROM teen_quotes_favorite where id_user='$id'"));
		$nb_comments=mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_comments where auteur_id='$id'"));
		$nb_quotes_added_to_favorite = mysql_num_rows(mysql_query("SELECT F.id FROM teen_quotes_favorite F, teen_quotes_quotes Q WHERE F.id_quote=Q.id AND Q.auteur_id='$id'"));
		if ($nb_quotes_approved >= '1') 
			{
			$quotes = mysql_query("SELECT id, texte, date FROM teen_quotes_quotes where auteur_id='$id' AND approved='1'");
			}
		if(empty($result['birth_date'])) {$result['birth_date']="$not_specified";}
		if(empty($result['title'])) {$result['title']="$not_specified";}
		if(empty($result['about_me'])) {$result['about_me']="$no_description";}
		if(empty($result['country'])) {$result['country']="$not_specified";}
		if(empty($result['city'])) {$result['city']="$not_specified";}
		if(empty($result['number_comments'])) {$result['number_comments']="$no_posted_comments";}
		if(empty($result['avatar'])) {$result['avatar']="icon50.png";}
		if($result['avatar']=="http://www.teen-quotes.com/images/icon50.png") {$result['avatar']="icon50.png";}
		
		if ($result['birth_date'] != "$not_specified")
			{
			$age = age($result['birth_date']);
			$age = '('.$age.' '.$years_old.')';
			}
		else
			{
			$age = '';
			}
		
		echo '<div class="post">';
		
		if ($result['hide_profile'] == '1' AND $id = $_SESSION['id'])
			{
			echo '
			<div class="bandeau_erreur hide_this">
			<img src="http://www.teen-quotes.com/images/icones/infos.png" class="mini_plus_icone">'.$profile_hidden_self.'
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
			<div style="position:relative;margin-left:55px;">
			<span class="bleu">'.$title.':</span> '.$result['title'].'<br>
			<span class="bleu">'.$birth_date.' :</span> '. $result['birth_date'].' '.$age.'<br>
			<span class="bleu">'.$country.' :</span> '. $result['country'].'<br>
			<span class="bleu">'.$city.' :</span> '. $result['city'].'<br>
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
			
		echo '</div>';
		
		if ($show_pub == '1' AND $nb_quotes_approved == '0' AND $nb_favorite_quotes == '0')
			{
			echo '
			<div class="pub_middle">
			<script type="text/javascript"><!--
			google_ad_client = "ca-pub-8130906994953193";
			/* Pub haut user - mobile */
			google_ad_slot = "3398396117";
			google_ad_width = 320;
			google_ad_height = 50;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
			</div>';
			}
		
		// CITATIONS FAVORITES
		echo '
		<div class="post" id="fav_quotes">
		<h2><img src="http://www.teen-quotes.com/images/icones/heart_big.png" class="icone">'.$favorite_quotes.'</h2>
		';
		
		if($nb_favorite_quotes >= '1')
			{
			$nb_messages_par_page = 5;

			$display_page_top = display_page_top($nb_favorite_quotes, $nb_messages_par_page, 'page_fav', $previous_page, $next_page, '#fav_quotes');
			$premierMessageAafficher = $display_page_top[0];
			$nombreDePages = $display_page_top[1];
			$page = $display_page_top[2];
	
			$reponse = mysql_query("SELECT DISTINCT id_quote FROM teen_quotes_favorite WHERE id_user='$id' ORDER BY id DESC LIMIT $premierMessageAafficher ,  $nb_messages_par_page");
			while ($resultat = mysql_fetch_array($reponse))
				{
				$id_quote_fav=$resultat['id_quote'];
				$donnees=mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_quotes WHERE id='$id_quote_fav'"));
				$id_visitor = $_SESSION['id'];
				$nombre_commentaires = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_comments WHERE id_quote='".$id_quote_fav."'"));
				$is_favorite = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_favorite WHERE id_quote='$id_quote_fav' AND id_user='$id_visitor'"));
				$logged = $_SESSION['logged'];
				$id_quote = $donnees['id'];
				$txt_quote = $donnees['texte_english'];
				$auteur_id = $donnees['auteur_id'];
				$auteur = $donnees['auteur']; 
				$date_quote = $donnees['date'];
				?>
		
				<div class="grey_post">
				<?php echo $donnees['texte_english']; ?><br>
				<div style="font-size:65%">
				<a href="quote-<?php echo $id_quote_fav; ?>">#<?php echo $id_quote_fav; ?> - <?php if($nombre_commentaires >'1'){echo "$nombre_commentaires $comments";}elseif($nombre_commentaires=='1'){echo "$nombre_commentaires $comment";}else{echo"$no_comments";} ?></a><?php afficher_favori_m($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['id']); date_et_auteur_m($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
				</div>
				</div>
				<?php 
				$i++;
				}
				
			display_page_bottom($page, $nombreDePages, 'page_fav', '#fav_quotes', $previous_page, $next_page);
				
			echo '</div>';
			if ($show_pub == '1' AND ($nb_quotes_approved >= '1' OR $nb_favorite_quotes >= '1'))
				{
				echo '
				<div class="pub_middle">
				<script type="text/javascript"><!--
				google_ad_client = "ca-pub-8130906994953193";
				/* Pub milieu user - mobile */
				google_ad_slot = "6564367088";
				google_ad_width = 320;
				google_ad_height = 50;
				//-->
				</script>
				<script type="text/javascript"
				src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
				</script>
				</div>';
				}
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
		<h2><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone">'.$user_quotes.'</h2>
		';
			
		if($nb_quotes_approved >= '1')
			{
			$nb_messages_par_page = 5;

			$display_page_top = display_page_top($nb_quotes_approved, $nb_messages_par_page, 'page_user', $previous_page, $next_page, '#user_quotes');
			$premierMessageAafficher = $display_page_top[0];
			$nombreDePages = $display_page_top[1];
			$page = $display_page_top[2];
			
			$query_reponse = "SELECT * FROM teen_quotes_quotes WHERE auteur_id='$id' AND approved='1' ORDER BY id DESC LIMIT $premierMessageAafficher ,  $nb_messages_par_page";
			$reponse = mysql_query($query_reponse) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query_reponse. "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
			while ($result = mysql_fetch_array($reponse))
				{
				$logged = $_SESSION['logged'];
				$id_quote = $result['id'];
				$txt_quote = $result['texte_english'];
				$auteur_id = $result['auteur_id'];
				$auteur = $result['auteur']; 
				$date_quote = $result['date'];
				
				$id_user_co = $compte['id'];
				$nombre_commentaires = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_comments WHERE id_quote='".$id_quote."'"));
				$is_favorite = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_favorite WHERE id_quote='$id_quote' AND id_user='$id_user_co'"))
				?>
				<div class="grey_post">
				<?php echo $result['texte_english']; ?><br>
				<div style="font-size:65%">
				<a href="quote-<?php echo $id_quote; ?>">#<?php echo $id_quote; ?> - <?php if($nombre_commentaires >'1'){echo "$nombre_commentaires $comments";}elseif($nombre_commentaires=='1'){echo "$nombre_commentaires $comment";}else{echo"$no_comments";} ?></a><?php afficher_favori_m($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['id']); date_et_auteur_m($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
				</div>
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