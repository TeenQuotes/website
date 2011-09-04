<?php 
include 'header.php';
include "lang/$language/user.php";
$i = '0';
$j = '0';

$id=mysql_real_escape_string($_GET['id_user']);
$exist_user = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE id='$id'"));
if ($exist_user=='0') {header("Location: error.php?erreur=404"); }
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
		if ($show_pub =='1')
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
		else {
		// AFFICHAGE DU PROFIL
		$nb_quotes_approved=mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes where auteur_id='$id' AND approved='1'"));
		$nb_quotes_submited=mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes where auteur_id='$id'"));
		$nb_favorite_quotes=mysql_num_rows(mysql_query("SELECT DISTINCT id_quote FROM teen_quotes_favorite where id_user='$id'"));
		$nb_comments=mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_comments where auteur_id='$id'"));
		if ($nb_quotes_approved >= 1) {
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
			<h2><?php echo $result['username']; ?><span class="right"><?php echo $user_informations; ?> <?php if($id==$_SESSION['account']) {?>- <a class="submit" href="editprofile"><?php echo $edit; ?></a><?php } ?></span></h2>
			<div style="position:relative;margin-left:150px;">
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
			
			<?php
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
			?>
		<div class="post" id="fav_quotes">
		<h2><img src="http://www.teen-quotes.com/images/icones/heart_big.png" class="icone"><?php echo $favorite_quotes; ?></h2>
		
		<?php
		if($nb_favorite_quotes >=1){
	
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
		$donnees=mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_quotes WHERE id='$id_quote_fav'"));
		$id_visitor = $_SESSION['account'];
		$is_favorite = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_favorite WHERE id_quote='$id_quote_fav' AND id_user='$id_visitor'"));
		$logged = $_SESSION['logged'];
		$id_quote = $donnees['id'];
		$txt_quote = $donnees['texte_english'];
		$auteur_id = $donnees['auteur_id'];
		$auteur = $donnees['auteur']; 
		$date_quote = $donnees['date'];		?>
		
		<div class="grey_post">
		<?php echo $donnees['texte_english']; ?><br><br />
		<a href="quote-<?php echo $donnees['id']; ?>">#<?php echo $donnees['id']; ?></a><?php afficher_favori($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['account']); date_et_auteur ($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
		<?php share_fb_twitter ($id_quote,$txt_quote,$share); ?> 
		</div>
		<?php 
		$i++;
		}
		if ($page_fav > 1){echo "<span class=\"page\"><a href=\"?page_fav=$page_fav3#fav_quotes\">$previous_page</a> ||  ";}
		if ($page_fav == 1){ echo"<span class=\"page\">";}  if($page_fav < $nombreDePages_fav) {echo " <a href=\"?page_fav=$page_fav2#fav_quotes\">$next_page</a>";}echo"</span><br>";
		echo '</div>';
			}
			else { ?>
			</div>
			<div class="bandeau_erreur">
			<?php echo $no_fav_quotes; ?>
			</div>
			<?php
			 } ?>
			<div class="clear"></div>
			<br />
			<div class="post" id="user_quotes">
			<h2><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone"><?php echo $user_quotes; ?></h2>
			
			<?php if($nb_quotes_approved >=1){
	
		// CALCUL DU NOMBRE DE PAGES QUOTES AJOUTEES PAR L'USER

		$retour = mysql_query("SELECT COUNT(*) AS nb_messages FROM teen_quotes_quotes where auteur_id='$id' AND approved='1'");

		$donnees = mysql_fetch_array($retour);
		$totalDesMessages = $donnees['nb_messages'];

		$nombreDeMessagesParPage = 5; 
		$nombreDePages  = ceil($totalDesMessages / $nombreDeMessagesParPage);
		if (isset($_GET['page_user']))
		{
				$page_user = mysql_real_escape_string($_GET['page_user']);
		}
		else 
		{
				$page_user = 1; 
		}

		if ($page_user > $nombreDePages) {$page_user=$nombreDePages;}

		$page_user2 = $page_user + 1;
		$page_user3 = $page_user - 1;
		if ($page_user > 1){echo "<span class=\"page\"><a href=\"?page_user=$page_user3#user_quotes\">$previous_page</a> ||  ";}
		if ($page_user == 1){ echo"<span class=\"page\">";}   if($page_user < $nombreDePages) {echo " <a href=\"?page_user=$page_user2#user_quotes\">$next_page</a>";}echo"</span><br>";

		$premierMessageAafficher = ($page_user - 1) * $nombreDeMessagesParPage;
		// BOUCLES DES QUOTES AJOUTEES PAR L'USER
		$query_reponse="SELECT * FROM teen_quotes_quotes WHERE auteur_id='$id' AND approved='1' ORDER BY id DESC LIMIT $premierMessageAafficher ,  $nombreDeMessagesParPage";
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
		
		$is_favorite = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_favorite WHERE id_quote='$id_quote' AND id_user='$id_user_co'"))
		?>
		<div class="grey_post">
		<?php echo $result['texte_english']; ?><br><br />
		<a href="quote-<?php echo $result['id']; ?>">#<?php echo $result['id']; ?></a><?php afficher_favori($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['account']); date_et_auteur ($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
		<?php share_fb_twitter ($id_quote,$txt_quote,$share); ?> 
		</div>
		<?php
		$j++;
		}
		if ($page_user > 1){echo "<span class=\"page\"><a href=\"?page_user=$page_user3#user_quotes\">$previous_page</a> ||  ";}
		if ($page_user == 1){ echo"<span class=\"page\">";}   if($page_user < $nombreDePages) {echo " <a href=\"?page_user=$page_user2#user_quotes\">$next_page</a>";}echo"</span><br>";
		echo '</div>';
			}
			else { ?>
			</div>
			<div class="bandeau_erreur">
			<?php echo $no_quotes; ?>
			</div>
			<?php
			 }
		}
		}
		include "footer.php";?>