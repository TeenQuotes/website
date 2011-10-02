<?php 
include 'header.php';

$total_quotes = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes"));
$quotes_approved = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved='1'"));
$quotes_rejected = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved='-1'"));
$pourcentage_quotes = round(($quotes_approved/$total_quotes) * 100);

$total_members = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account"));
$nb_empty_avatar = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE avatar='icon50.png'"));
$nb_members_empty_profile = $total_members - $nb_empty_avatar;
$pourcentage_members_empty_profile = round(($nb_members_empty_profile/$total_members) * 100);

$nb_favorite = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_favorite"));
$nb_newsletter = mysql_num_rows(mysql_query("SELECT id FROM newsletter"));
$pourcentage_newsletter = round(($nb_newsletter/$total_members) * 100);
$nb_comments = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_comments"));

echo '
<div class="post">
	<h1><img src="http://teen-quotes.com/images/icones/test.png" class="icone" />Statistiques</h1>
	<h2>Quotes</h2>
	<li>Number total of quotes : '.$total_quotes.'</li>
	<li>'.$quotes_approved.' quotes approved, '.$quotes_rejected.' quotes rejected ('.$pourcentage_quotes.' % of approved quotes)</li>
	
	<h2>Members</h2>
	<li>Number total of members : '.$total_members.'</li>
	<li>'.$pourcentage_members_empty_profile.' % ('.$nb_members_empty_profile .' members) of members have fullfilled their profile</li>
	
	<h2>Other stats</h2>
	<li>Total number of favorite quotes : '.$nb_favorite.'</li>
	<li>People subscribed to the newsletter : '.$nb_newsletter.' ('.$pourcentage_newsletter.' % of members)</li>
	<li>Total number of comments : '.$nb_comments.'</li>
</div>';



include "footer.php";
?>