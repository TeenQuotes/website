<?php 
include 'header.php'; 
include "lang/$language/search.php"; 
$value_search = htmlspecialchars(mysql_escape_string($_GET['q']));
// FORMULAIRE
if (empty($value_search)) 
	{
	echo '<div class="post">
	<h1>'.$error.'</h1>
	'.$not_completed.'
	</div>';
	}
elseif (isset($value_search)) 
	{
	$reponse_init = mysql_query("SELECT * FROM teen_quotes_quotes WHERE approved='1' AND (auteur like '%$value_search%' OR texte_english like '%$value_search%' OR texte_french like '%$value_search%')");
	$num_rows_result = mysql_num_rows($reponse_init);

	if ($num_rows_result >='1') 
		{
		$reponse = mysql_query("SELECT * FROM teen_quotes_quotes WHERE approved='1' AND (auteur like '%$value_search%' OR texte_english like '%$value_search%' OR texte_french like '%$value_search%') ORDER BY id DESC LIMIT 0,15");
?>
		<div class="post">
		<h1><img src="http://www.teen-quotes.com/images/icones/search_result.png" class="icone" /><?php echo $search_results; ?><span class="right" style="font-size:70%;padding-top:5px"><?php echo $num_rows_result; ?> <?php echo $results; ?><?php if($num_rows_result >'1'){echo"s";} ?><?php if ($num_rows_result > '25'){echo " ($max_result)";} ?></span></h1>
		</div>
<?php
		while ($result = mysql_fetch_array($reponse))
			{
			$logged = $_SESSION['logged'];
			$id_quote = $result['id'];
			$txt_quote = $result['texte_english'];
			$auteur_id = $result['auteur_id'];
			$auteur = $result['auteur']; 
			$date_quote = $result['date'];

			$nombre_commentaires= mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_comments WHERE id_quote='$id_quote'")); 
			$is_favorite = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_favorite WHERE id_quote='$id_quote' AND id_user='$id'"));?>
					<div class="post">
					<?php echo $txt_quote; ?><br><br />
					<a href="quote-<?php echo $result['id']; ?>">#<?php echo $result['id']; ?> - <?php if($nombre_commentaires >'1'){echo "$nombre_commentaires $comments";}elseif($nombre_commentaires=='1'){echo "$nombre_commentaires $comment";}else{echo"$no_comments";} ?></a><?php afficher_favori($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['account']); date_et_auteur ($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
					<?php share_fb_twitter ($id_quote,$txt_quote,$share); ?> 
					</div>
			<?php 
			}
		}
		// AFFICHAGE SI 0 RESULTAT
		else
		{ 
		echo '
		<div class="post">
		<h1><img src="http://www.teen-quotes.com/images/icones/search_result.png" class="icone" />'.$no_result.'</h1>
		'.$no_result_fun.'
		</div>';
		}
	}
	
include "footer.php"; ?>