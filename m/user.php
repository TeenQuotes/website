<?php 
include 'header.php';
include "../lang/$language/user.php";
$id=mysql_real_escape_string($_GET['id_user']);
$exist_user = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE id='$id'"));
if ($exist_user=='0') {header("Location: error.php?erreur=404"); }

if($id != $_SESSION['account'] AND !empty($id) AND !empty($_SESSION['account']))
	{
	$id_visitor = $_SESSION['account'];
	$insert_visitor = mysql_query("INSERT INTO teen_quotes_visitors (id_user,id_visitor) VALUES ('$id','$id_visitor')");
	}
	
// FORMULAIRE
if (empty($id)) {
?>
<div class="post">
<h1><?php echo $error; ?></h1>
</div>
<?php include 'footer.php'; 
 }else { // SI LE PROFIL DOIT ETRE CACHE
		$result = mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_account where id='$id'"));
		if ($result['hide_profile']=='1')
		{ ?>
		<div class="bandeau_erreur">
		<span class="erreur"><?php echo $hide_profile; ?></span>
		<?php echo $lien_retour; ?>
		</div>
		<?php 
		}
		else {
		// AFFICHAGE DU PROFIL
		$nb_quotes_approved=mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes where auteur_id='$id' AND approved='1'"));
		$nb_quotes_submited=mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes where auteur_id='$id'"));
		$nb_favorite_quotes=mysql_num_rows(mysql_query("SELECT DISTINCT id_quote FROM teen_quotes_favorite where id_user='$id'"));
		$nb_comments=mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_comments where auteur_id='$id'"));
		if ($nb_quotes >= 1) {
							$quotes = mysql_query("SELECT id, texte, date FROM teen_quotes_quotes where auteur_id='$id' AND approved='1'");
							}
		if(empty($result['birth_date'])) {$result['birth_date']="$not_specified";}
		if(empty($result['title'])) {$result['title']="$not_specified";}
		if(empty($result['about_me'])) {$result['about_me']="$no_description";}
		if(empty($result['country'])) {$result['country']="$not_specified";}
		if(empty($result['city'])) {$result['city']="$not_specified";}
		if(empty($result['number_comments'])) {$result['number_comments']="$no_posted_comments";}
		if(empty($result['avatar'])) {$result['avatar']="images/icon50.png";}
		if($result['avatar']=="http://www.teen-quotes.com/images/icon50.png") {$result['avatar']="images/icon50.png";}
		
		?>
			<div class="post">
			<img src="http://www.teen-quotes.com/images/avatar/<?php echo $result['avatar']; ?>" class="user_avatar" />
			<h2><?php echo $result['username']; ?><span class="right"><?php echo $user_informations; ?></span></h2>
			<div style="position:relative;margin-left:55px;">
			<span class="bleu"><?php echo $title; ?> :</span> <?php echo $result['title']; ?><br>
			<span class="bleu"><?php echo $birth_date; ?> :</span> <?php echo $result['birth_date']; ?><br>
			<span class="bleu"><?php echo $country; ?> :</span> <?php echo $result['country']; ?><br>
			<span class="bleu"><?php echo $city; ?> :</span> <?php echo $result['city']; ?><br>
			<span class="bleu"><?php echo $fav_quote; ?> :</span> <?php echo $nb_favorite_quotes; ?><br>
			<span class="bleu"><?php echo $number_comments; ?> :</span> <?php echo $nb_comments; ?><br>
			<span class="bleu"><?php echo $number_quotes; ?> :</span> <?php echo ''.$nb_quotes_approved.' '.$validees.' '.$nb_quotes_submited.' '.$soumises.''; ?><br>
			</div>
			<div class="clear"></div>
			<h3><?php echo $about_user; ?> <?php echo $result['username']; ?></h3>
			<?php echo $result['about_me']; ?>
			</div>
			
			<div class="post" id="page_fav" style="margin-bottom:4px">
			<h2><img src="http://www.teen-quotes.com/images/icones/heart_big.png" class="icone"><?php echo $favorite_quotes; ?></h2>
			</div>
						<?php if($nb_favorite_quotes >=1){
	
		// CALCUL DU NOMBRE DE PAGES QUOTES FAVORITES

		$totalDesMessages = $nb_favorite_quotes;

		$nombreDeMessagesParPage = 5; 
		$nombreDePages_fav  = ceil($totalDesMessages / $nombreDeMessagesParPage);
		if (isset($_GET['page_fav']))
		{
				$page_fav = mysql_real_escape_string($_GET['page_fav']);
		}
		else 
		{
				$page_fav = 1; 
		}

		if ($page_fav > $nombreDePages_fav) {$page_fav=$nombreDePages_fav;}

		$page_fav2 = $page_fav + 1;
		$page_fav3 = $page_fav - 1;
		if ($page_fav > 1){echo "<span class=\"page\"><a href=\"?page_fav=$page_fav3#fav_quotes\">$previous_page</a> ||  ";}
		if ($page_fav == 1){ echo"<span class=\"page\">";}  if($page_fav < $nombreDePages_fav) {echo " <a href=\"?page_fav=$page_fav2#fav_quotes\">$next_page</a>";}echo"</span><br>";


		$premierMessageAafficher = ($page_fav - 1) * $nombreDeMessagesParPage;

		$reponse = mysql_query("SELECT DISTINCT id_quote FROM teen_quotes_favorite WHERE id_user='$id' ORDER BY id DESC LIMIT $premierMessageAafficher ,  $nombreDeMessagesParPage");
		// BOUCLES DES QUOTES MIS EN FAVORITE PAR L'USER
		
		while ($resultat = mysql_fetch_array($reponse))
		{
		$id_quote_fav=$resultat['id_quote'];
		$donnees=mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_quotes WHERE id='$id_quote_fav'")); ?>
		
		<div class="post">
		<?php echo $donnees['texte_english']; ?><br><br />
		<a href="quote-<?php echo $donnees['id']; ?>">#<?php echo $donnees['id']; ?></a><a href="unfavorite.php?id_quote=<?php echo $donnees['id']; ?>" title="<?php echo $unfavorite; ?>"><img src="http://www.teen-quotes.com/images/icones/broken_heart.gif" style="margin-left:20px" /></a><span class="right"><?php echo $by; ?> <a href="user-<?php echo $donnees['auteur_id']; ?>" title="<?php echo $view_his_profile; ?>"><?php echo $donnees['auteur']; ?></a> <?php echo $on; ?> <?php echo $donnees['date']; ?></span><br>
		
		</div>
		<?php }
			}
			else { ?>
			<div class="bandeau_erreur">
			<?php echo $no_fav_quotes; ?>
			</div>
			<?php
			 } ?>
			<div class="clear"></div>
			<br />
			<div class="post" id="user_quotes" style="margin-bottom:4px">
			<h2><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone"><?php echo $user_quotes; ?></h2>
			</div>
			
			<?php if($nb_quotes_approved >=1){
	
		// CALCUL DU NOMBRE DE PAGES QUOTES AJOUTEES PAR L'USER

		$retour = mysql_query("SELECT COUNT(*) AS nb_messages FROM teen_quotes_quotes where auteur_id='$id' AND approved='1'");

		$donnees = mysql_fetch_array($retour);
		$totalDesMessages = $donnees['nb_messages'];

		$nombreDeMessagesParPage = 5; 
		$nombreDePages  = ceil($totalDesMessages / $nombreDeMessagesParPage);
		if (isset($_GET['page']))
		{
				$page = mysql_real_escape_string($_GET['page']);
		}
		else 
		{
				$page = 1; 
		}

		if ($page > $nombreDePages) {$page=$nombreDePages;}

		$page2 = $page + 1;
		$page3 = $page - 1;
		if ($page > 1){echo "<span class=\"page\"><a href=\"?page_user=$page3#user_quotes\">$previous_page</a> ||  ";}
		if ($page == 1){ echo"<span class=\"page\">";}   if($page < $nombreDePages) {echo " <a href=\"?page_user=$page2#user_quotes\">$next_page</a>";}echo"</span><br>";

		$premierMessageAafficher = ($page - 1) * $nombreDeMessagesParPage;
		$reponse = mysql_query("SELECT * FROM teen_quotes_quotes WHERE auteur_id='$id' AND approved='1' ORDER BY id DESC LIMIT $premierMessageAafficher ,  $nombreDeMessagesParPage");
		while ($result = mysql_fetch_array($reponse))
		{
		?>
		<div class="post">
		<?php echo $result['texte_english']; ?><br><br />
		<a href="quote-<?php echo $result['id']; ?>">#<?php echo $result['id']; ?></a><span class="right"><?php echo $by; ?> <a href="user-<?php echo $result['auteur_id']; ?>" title="<?php echo $view_his_profile; ?>"><?php echo $result['auteur']; ?></a> <?php echo $on; ?> <?php echo $result['date']; ?></span><br>
		</div>
		<?php }
			}
			else {?>
			<div class="bandeau_erreur">
			<?php echo $no_quotes; ?>
			</div>
			<?php
			 }
		}
		}
		include "footer.php";?>