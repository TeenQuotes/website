<?php 
include 'header.php'; 
include "lang/$language/favorite.php"; 
$id_quote=mysql_real_escape_string($_GET['id_quote']);
// FORMULAIRE
if (empty($id_quote)) {header("Location: error.php?erreur=403"); }

$query=mysql_query("INSERT INTO teen_quotes_favorite (id_quote, id_user) VALUES ('$id_quote','$id')");
?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/heart_big.png" class="icone"><?php echo $add_favorite; ?></h1>
<?php if($query) {
				 echo"$succes $add_succes";
				 echo "<meta http-equiv=\"refresh\" content=\"3;url=quote-$id_quote\" />";
				 }
				 else
				{
				echo '<h2>'.$error.'</h2>'.$lien_retour.'';
				}
echo '</div>';

include "footer.php"; ?>