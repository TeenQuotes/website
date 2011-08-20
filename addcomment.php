<?php 
include 'header.php'; 
include "lang/$language/quote.php"; 

$id_quote = nl2br(htmlspecialchars(mysql_escape_string($_POST['id_quote'])));
$texte = htmlspecialchars(mysql_escape_string($_POST['texte']));
$username=ucfirst($username);
$date = date("d/m/Y"); ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/about.png" class="icone" /><?php echo ucfirst($comments); ?></h1>
<?php
if (!empty($id_quote) && !empty($texte)) 
	{
	if (strlen($texte) <= '450') 
		{
		$query = mysql_query("INSERT INTO teen_quotes_comments (id_quote, texte, auteur, auteur_id, date) VALUES ('$id_quote', '$texte', '$username', '$id', '$date')");
		if ($query) {
					echo ''.$comment_add_succes.'';
					echo '<meta http-equiv="refresh" content="3;url=quote-'.$id_quote.'" />';
					}
					else
					{
					echo '<h2>'.$error.'</h2>'.$lien_retour.'';
					}
		}
		else
		{
		echo '<span class="erreur">'.$comment_too_long.'</span>';
		}
	}
	else
	{
	echo '<span class="erreur">'.$not_complete.'</span>';
	}
echo '</div>';
include "footer.php"; ?>