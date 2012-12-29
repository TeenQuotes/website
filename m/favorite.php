<?php 
include 'header.php'; 
include '../lang/'.$language.'/favorite.php'; 
$id_quote = mysql_real_escape_string($_GET['id_quote']);

if (empty($id_quote)) 
{
	header("Location: error.php?erreur=403"); 
}

$query = mysql_query("INSERT INTO teen_quotes_favorite (id_quote, id_user) VALUES ('$id_quote','$id')");
$update_fav = mysql_query('UPDATE teen_quotes_quotes SET nb_fav = nb_fav + 1 WHERE id = '.$id_quote.'');

echo '
<div class="post">';
	if($query) 
	{
		echo ''.$add_succes.'';
		echo '<meta http-equiv="refresh" content="2;url=quote-'.$id_quote.'" />';
	}
	else
	{
		echo '<h2>'.$error.'</h2>'.$lien_retour.'';
	}
echo '
</div>';

include "footer.php";
?>