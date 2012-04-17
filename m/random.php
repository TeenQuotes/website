<?php 
include "header.php";
// CALCUL DU NOMBRE DE PAGES
$i = '0';

$retour = mysql_query("SELECT COUNT(*) AS nb_messages FROM teen_quotes_quotes WHERE approved = '1'");
$donnees = mysql_fetch_array($retour);
$nb_messages_par_page = '10';

$display_page_top = display_page_top($donnees['nb_messages'], $nb_messages_par_page, 'p', $previous_page, $next_page);
$premierMessageAafficher = $display_page_top[0];
$nombreDePages = $display_page_top[1];
$page = $display_page_top[2];

$reponse = mysql_query("SELECT * FROM teen_quotes_quotes WHERE approved='1' ORDER BY RAND() LIMIT ".$premierMessageAafficher.",  ".$nb_messages_par_page."");
while ($result = mysql_fetch_array($reponse))
	{
	$logged = $_SESSION['logged'];
	$id_quote = $result['id'];
	$txt_quote = $result['texte_english'];
	$auteur_id = $result['auteur_id'];
	$auteur = $result['auteur']; 
	$date_quote = $result['date'];

	$nombre_commentaires= mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_comments WHERE id_quote = '".$id_quote."'")); 
	if ($logged)
		{
		$is_favorite = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_favorite WHERE id_quote='".$id_quote."' AND id_user='".$id."'"));
		}
?>
	<div class="post<?php if($i=='0'){echo ' no_rounded_borders_right_top';} ?>">
	<?php is_quote_new($date_quote,$last_visit,$page,$i); ?><?php echo $txt_quote; ?><br>
	<div style="font-size:65%">
	<a href="quote-<?php echo $id_quote; ?>">#<?php echo $id_quote; ?> - <?php if($nombre_commentaires >'1'){echo ''.$nombre_commentaires.' '.$comments.'';}elseif($nombre_commentaires=='1'){echo ''.$nombre_commentaires.' '.$comment.'';}else{echo ''.$no_comments.'';} ?></a><?php afficher_favori_m($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['id']); date_et_auteur_m($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
	</div>
	</div>
<?php
	if ($i=='4' && $show_pub == '1')
		{
		echo 
		'<div class="pub_middle">
		<script type="text/javascript"><!--
		google_ad_client = "ca-pub-8130906994953193";
		/* Pub milieu quotes - mobile */
		google_ad_slot = "9272061144";
		google_ad_width = 320;
		google_ad_height = 50;
		//-->
		</script>
		<script type="text/javascript"
		src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>
		</div>';
		}
	$i++;
	} 
	
display_page_bottom($page, $nombreDePages, 'p', NULL, $previous_page, $next_page);

include "footer.php"; 
?>