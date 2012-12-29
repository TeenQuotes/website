<?php
include "header.php";
include 'lang/'.$language.'/members.php'; 
include 'lang/'.$language.'/user.php';

$abcd = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$j = 0;

// GERE LES PAGES DE LETTRE
echo '<div class="post padding_letters">';
	for ($i=0;$i<=25;$i++) 
	{
		$lettre_url = $abcd[$i];
		echo '<a href="../members-'.$lettre_url.'" class="letters_members">'.$lettre_url.'</a>';
	}
echo '</div>';

// $lettre in header.php
$retour = mysql_query("SELECT COUNT(*) AS nb_membre FROM teen_quotes_account WHERE username LIKE '$lettre%' AND hide_profile = '0'");
$donnees = mysql_fetch_array($retour);
$totalDesMembres = $donnees['nb_membre'];

if ($totalDesMembres > 0) 
{
	$nb_messages_par_page = 10;

	$display_page_top = display_page_top($totalDesMembres, $nb_messages_par_page, 'p', $previous_page, $next_page, NULL, TRUE);
	$premierMessageAafficher = $display_page_top[0];
	$nombreDePages = $display_page_top[1];
	$page = $display_page_top[2];
	
	echo '<div class="post">';
	
	$reponse = mysql_query("SELECT * FROM teen_quotes_account WHERE username LIKE '$lettre%' AND hide_profile = '0' ORDER BY username ASC LIMIT $premierMessageAafficher, $nb_messages_par_page");
	while ($result = mysql_fetch_array($reponse))
	{
		$id_user = $result['id'];
		$avatar = $result['avatar'];
		$username_member = $result['username'];
		$about_me = $result['about_me'];
		$country = $result['country'];
		$city = $result['city'];
		
		$nb_quotes_approved = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE auteur_id = '$id_user' AND approved = '1'"));
		$nb_quotes_submited = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE auteur_id = '$id_user'"));
		$nb_favorite_quotes = mysql_num_rows(mysql_query("SELECT DISTINCT id_quote FROM teen_quotes_favorite WHERE id_user = '$id_user'"));
		$nb_comments = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_comments WHERE auteur_id = '$id_user'"));
		$nb_quotes_added_to_favorite = mysql_num_rows(mysql_query("SELECT F.id FROM teen_quotes_favorite F, teen_quotes_quotes Q WHERE F.id_quote = Q.id AND Q.auteur_id = '$id_user'"));
		
		echo '<div class="grey_post">';
		echo '<img src="http://'.$domaine.'/images/avatar/'.$avatar.'" class="user_avatar_members" /><a href="user-'.$id_user.'"><h2>'.$username_member.'';
		
		if (!empty($city)) 
		{
			echo '<span class="right">'.$city.'';
		}

		if (!empty($country))
		{
			if (!empty($city))
			{
				echo ' - ';
			}
			echo ''.$country.'</span>';
		}
		echo '</h2></a>';

		if (!empty($about_me)) 
		{
			echo ''.$about_me.'';
			echo '<div class="grey_line"></div>';
		}

		echo '
		<span class="bleu">'.$fav_quote.' :</span> '.$nb_favorite_quotes.'<br/>
		<span class="bleu">'.$number_comments.' :</span> '.$nb_comments.'<br/>
		<span class="bleu">'.$number_quotes.' :</span> '.$nb_quotes_approved.' '.$validees.' '.$nb_quotes_submited.' '.$soumises.'<br/>';

		if ($nb_quotes_approved > 0)
		{
			echo '<span class="bleu">'.$added_on_favorites.' :</span> '.$nb_quotes_added_to_favorite.'<br/>';
		}

		echo '</div>';
		
		$j++;
	}  // END WHILE

	echo '</div>';
	
	display_page_bottom($page, $nombreDePages, 'p', NULL, $previous_page, $next_page, TRUE);
}
else
{
	echo '
	<div class="post">
		<span class="erreur">'.$no_members.' ('.$lettre.')</span>
	</div>';
}

include "footer.php";