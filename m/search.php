<?php 
include 'header.php'; 
include "../lang/$language/search.php"; 
$value_search = htmlspecialchars(mysql_escape_string($_POST['search']));
// FORMULAIRE
if (empty($value_search)) {
?>
<div class="post">
<h1><?php echo $error; ?></h1>
<?php echo $not_completed; ?>
</div>
<?php 
}
elseif (isset($value_search)) {
$reponse_init = mysql_query("SELECT * FROM teen_quotes_quotes WHERE approved='1' AND auteur like '%$value_search%' OR texte_english like '%$value_search%' OR texte_french like '%$value_search%'");
$num_rows_result = mysql_num_rows($reponse_init);
// VERIFICATION SI IL YA DES RESULTATS
if ($num_rows_result >='1') {

$reponse = mysql_query("SELECT * FROM teen_quotes_quotes WHERE approved='1' AND auteur like '%$value_search%' OR texte_english like '%$value_search%' OR texte_french like '%$value_search%' ORDER BY id DESC LIMIT 0,15");
 ?>
		<div class="post">
		<h1 style="font-size:11px"><img src="http://www.teen-quotes.com/images/icones/search_result.png" class="icone" /><?php echo $search_results; ?><span class="right" style="font-size:70%;padding-top:5px"><?php echo $num_rows_result; ?> <?php echo $results; ?><?php if($num_rows_result >'1'){echo"s";} ?><?php if ($num_rows_result > '25'){echo " ($max_result)";} ?></span></h1>
		</div>
<?php
while ($result = mysql_fetch_array($reponse))
{
?>			
		<div class="post">
		<?php echo $result['texte_english']; ?><br>
		<span class="min_info"><a href="quote-<?php echo $result['id']; ?>">#<?php echo $result['id']; ?></a><span class="right"><?php echo $by; ?> <a href="user-<?php echo $result['auteur_id']; ?>" title="<?php echo $view_his_profile; ?>"><?php echo $result['auteur']; ?></a> <?php echo $on; ?> <?php echo $result['date']; ?></span></span>
		</div>
<?php 
}
	}
	// AFFICHAGE SI 0 RESULTAT
	else
	{ ?>
	<div class="post">
	<h1><img src="http://www.teen-quotes.com/images/icones/search_result.png" class="icone" /><?php echo $no_result; ?></h1>
	<?php echo $no_result_fun; ?>
	</div>
	
<?php }
	}
include "footer.php"; ?>