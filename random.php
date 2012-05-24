<?php 
include "header.php";

$i = 0;
$logged = $_SESSION['logged'];
$donnees = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS nb_messages FROM teen_quotes_quotes WHERE approved = '1'"));
$nb_messages_par_page = 10;

$display_page_top = display_page_top($donnees['nb_messages'], $nb_messages_par_page, 'p', $previous_page, $next_page, NULL, TRUE);
$premierMessageAafficher = $display_page_top[0];
$nombreDePages = $display_page_top[1];
$page = $display_page_top[2];

if ($logged)
{
	$reponse = mysql_query("SELECT q.texte_english texte_english, q.id id, q.auteur_id auteur_id, q.date date, a.username auteur,
							(SELECT COUNT(*)
							FROM teen_quotes_comments c
							WHERE q.id = c.id_quote AND q.approved = '1') AS nb_comments,
							(SELECT COUNT(*)
							FROM teen_quotes_favorite f
							WHERE q.id = f.id_quote AND f.id_user = '$id' AND q.approved = '1' ) AS is_favorite
							FROM teen_quotes_quotes q, teen_quotes_account a 
							WHERE q.auteur_id = a.id AND q.approved = '1' 
							ORDER BY RAND() LIMIT $premierMessageAafficher, $nb_messages_par_page");
}
else
{
	$reponse = mysql_query("SELECT q.texte_english texte_english, q.id id, q.auteur_id auteur_id, q.date date, a.username auteur,
							(SELECT COUNT(*)
							FROM teen_quotes_comments c
							WHERE q.id = c.id_quote AND q.approved = '1') AS nb_comments
							FROM teen_quotes_quotes q, teen_quotes_account a 
							WHERE q.auteur_id = a.id AND q.approved = '1' 
							ORDER BY RAND() LIMIT $premierMessageAafficher, $nb_messages_par_page");
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
	
?>
	<div class="post">
	<?php echo $txt_quote; ?><br>
	<div class="footer_quote">
		<a href="quote-<?php echo $result['id']; ?>">#<?php echo $result['id']; ?> - <?php afficher_nb_comments ($nombre_commentaires, $comments, $comment, $no_comments); ?></a><?php afficher_favori($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['id']); date_et_auteur ($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
	</div>
	<?php share_fb_twitter ($id_quote,$txt_quote,$share); ?> 
	</div>
<?php 
	$i++;
	}
	
	display_page_bottom($page, $nombreDePages, 'p', NULL, $previous_page, $next_page);


include "footer.php"; 
?>