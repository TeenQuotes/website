<?php 
include "header.php";

$i = 0;
$logged = $_SESSION['logged'];
$donnees = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS nb_messages FROM teen_quotes_quotes WHERE approved = '1'"));
$nbPostsPerPage = 10;

$display_page_top = display_page_top($donnees['nb_messages'], $nbPostsPerPage, 'p', $previous_page, $next_page);
$premierMessageAafficher = $display_page_top[0];
$nombreDePages           = $display_page_top[1];
$page                    = $display_page_top[2];

if ($logged)
{
	$reponse = mysql_query("SELECT q.texte_english texte_english, q.id id, q.auteur_id auteur_id, q.date date, a.username auteur, COUNT(c.id) nb_comments, COUNT(f.id) is_favorite
							FROM teen_quotes_quotes q
							LEFT JOIN teen_quotes_comments c
							ON q.id = c.id_quote AND q.approved = '1'
							LEFT JOIN teen_quotes_account a
							ON q.auteur_id = a.id AND q.approved = '1'
							LEFT JOIN teen_quotes_favorite f
							ON q.id = f.id_quote AND f.id_user = $id
							WHERE q.approved = '1'
							GROUP BY q.id
							ORDER BY `q`.`id` DESC LIMIT $premierMessageAafficher, $nbPostsPerPage");
}
else
{
	$reponse = mysql_query("SELECT q.texte_english texte_english, q.id id, q.auteur_id auteur_id, q.date date, a.username auteur, COUNT( c.id ) nb_comments
							FROM teen_quotes_quotes q
							LEFT JOIN teen_quotes_comments c ON q.id = c.id_quote
							AND q.approved =  '1'
							LEFT JOIN teen_quotes_account a ON q.auteur_id = a.id
							AND q.approved =  '1'
							WHERE q.approved =  '1'
							GROUP BY q.id
							ORDER BY  `q`.`id` DESC LIMIT $premierMessageAafficher, $nbPostsPerPage");
}
while ($result = mysql_fetch_array($reponse))
{
	displayQuote($result, $page, $i, 'index');	
	$i++;
} 
	
display_page_bottom($page, $nombreDePages, 'p', null, $previous_page, $next_page);

include "footer.php"; 
?>