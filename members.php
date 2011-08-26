<?php 

include "header.php";
include "lang/$language/members.php"; 
include "lang/$language/user.php";

$abcd = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$j = '0';

// variable $lettre dans header.php

// GERE LES PAGES DE LETTRE
echo '<div class="post">';
for ($i='0';$i<='25';$i++) {
$lettre_url=$abcd[$i];
echo '<a href="../members-'.$lettre_url.'" class="letters_members">'.$lettre_url.'</a>';
}
echo '</div>';

$retour = mysql_query("SELECT COUNT(*) AS nb_membre FROM teen_quotes_account WHERE username like '$lettre%' AND hide_profile = '0'");
$donnees = mysql_fetch_array($retour);
$totalDesMembres = $donnees['nb_membre'];

if ($totalDesMembres > 0) 
	{
	$nombreDeMembresParPage = 10; 
	$nombreDePages  = ceil($totalDesMembres / $nombreDeMembresParPage);
	if (isset($_GET['p']))
	{
			$page = mysql_real_escape_string($_GET['p']);
	}
	else 
	{
			$page = 1; 
	}

	if ($page > $nombreDePages) {$page=$nombreDePages;}

	$page2 = $page + 1;
	$page3 = $page - 1;
	if ($page > 1){echo "<span class=\"page\"><a href=\"?p=$page3\">$previous_page</a> ||  ";}
	if ($page == 1){ echo"<span class=\"page\">";}   if($page < $nombreDePages) {echo "<a href=\"?p=$page2\">$next_page</a>";} echo "</span><br>";


	$premierMessageAafficher = ($page - 1) * $nombreDeMembresParPage;

	$reponse = mysql_query("SELECT * FROM teen_quotes_account WHERE username like '$lettre%' AND hide_profile = '0' ORDER BY username ASC LIMIT $premierMessageAafficher ,  $nombreDeMembresParPage");
	while ($result = mysql_fetch_array($reponse))
	{
	$id_user = $result['id'];
	$avatar = $result['avatar'];
	$username_member = $result['username'];
	$about_me = $result['about_me'];
	$country = $result['country'];
	$city = $result['city'];
	
	$nb_quotes_approved=mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes where auteur_id='$id_user' AND approved='1'"));
	$nb_quotes_submited=mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes where auteur_id='$id_user'"));
	$nb_favorite_quotes=mysql_num_rows(mysql_query("SELECT DISTINCT id_quote FROM teen_quotes_favorite where id_user='$id_user'"));
	$nb_comments=mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_comments where auteur_id='$id_user'"));
	
	if ($j=='0' && $page < $nombreDePages) 
		{ 
		echo '<div class="post no_rounded_borders_right_top">';
		}
		else
		{
		echo '<div class="post">';
		}
	echo '<img src="http://www.teen-quotes.com/images/avatar/'.$avatar.'" class="user_avatar_members" /><a href="user-'.$id_user.'"><h2>'.$username_member.'';
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
		echo ''.$country.'';
		}
	echo '</h2></a>';
	if (!empty($about_me)) 
		{
		echo ''.$about_me.'';
		echo '<div class="grey_line"></div>';
		}
	echo '
	<span class="bleu">'.$fav_quote.' :</span> '.$nb_favorite_quotes.'<br>
	<span class="bleu">'.$number_comments.' :</span> '.$nb_comments.'<br>
	<span class="bleu">'.$number_quotes.' :</span> '.$nb_quotes_approved.' '.$validees.' '.$nb_quotes_submited.' '.$soumises.'<br>';
	echo '</div>';
	
	$j++;
	}  // END WHILE
	if ($page > 1){echo "<span class=\"page_bottom\" ><a href=\"?p=$page3\">$previous_page</a> ||  ";}
	if ($page == 1){ echo"<span class=\"page_bottom\">";}   if($page < $nombreDePages) {echo "<a href=\"?p=$page2\">$next_page</a>";} echo "</span><br>";
 
	}
	else
	{
	echo '<div class="post">';
	echo '<span class="erreur">'.$no_members.' ('.$lettre.')</span>';
	echo '</div>';
	}
 

 
include "footer.php";
 