<?php 
include "header.php";
$action = mysql_real_escape_string($_GET['action']);
include 'lang/'.$language.'/addquote.php';

if (empty($action))
{
	echo '
	<div class="post">
		<h1><img src="http://'.$domaine.'/images/icones/add.png" class="icone" />'.$add_quote.'</h1>
		
		<div class="grey_post">
			<form action="?action=add_quote" method="post">
				'.$enter_quote.'<br/>
				<textarea name="texte_quote" id="texte_quote" placeholder="'.$placeholder_addquote.'"></textarea>
				<span id="compteur_textarea" class="red">0 '.$characters.'</span><br/>
				<br/>
				<div class="clear"></div>
				<center><p><input type="submit" value="'.$add_my_quote.'" class="submit" /></p></center>
				<br/>
				'.$add_consignes.'
			</form>
		</div>
	';
}
elseif ($action == "add_quote") 
{
	echo '
	<div class="post">
	<h1><img src="http://'.$domaine.'/images/icones/add.png" class="icone" />'.$add_quote.'</h1>
	';

	$texte_quote = ucfirst(htmlspecialchars(mysql_real_escape_string($_POST['texte_quote'])));
	$texte_quote = str_replace(array("\r", "\n"," dont "," im "," Im ",'´', " cant "), array('','',' don\'t ',' I\'m ',' I\'m ','\'','can\'t'), $texte_quote);
	$date = date("d/m/Y");

	if (strlen($texte_quote) >= 50) 
	{
		$submitted_today = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE auteur_id = '$id' AND date = '$date'"));
		if ($submitted_today < $nb_quote_released_per_day)
		{
			if (is_quote_exist($texte_quote) == FALSE) 
			{
				if (!empty($username) AND !empty($id))
				{
					$query = mysql_query("INSERT INTO teen_quotes_quotes (texte_english, date, auteur_id, approved) VALUES ('$texte_quote', '$date', '$id','0')");

					if ($query) 
					{
						echo $succes.' '.$add_ok;
					}
					else 
					{
						echo '<h2>'.$error.'</h2> '.$lien_retour;
					}
				}
				else
				{
					echo '<h2>'.$error.'</h2> Please contact us at support@teen-quotes.com with your username. '.$lien_retour;
				}
			}
			else
			{
				echo '<span class="erreur">'.$quote_already_exist.'</span> '.$lien_retour;
			}
		}
		else
		{
			echo '<span class="erreur">'.$submitted_too_much.'</span> '.$lien_retour;
		}
	}
	else 
	{
		echo '<span class="erreur">'.$too_short.'</span> '.$lien_retour;
	}								
} 
echo '</div>';
include "footer.php";
?>