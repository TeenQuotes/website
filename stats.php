<?php 
include 'header.php';
$query = mysql_query("UPDATE teen_quotes_settings SET value=value+1 WHERE param = 'clics_stats'");

echo '
<div class="post">
	<h1><img src="http://teen-quotes.com/images/icones/test.png" class="icone" />Statistics</h1>
	<div class="bandeau_erreur hide_this">
		<img src="http://www.teen-quotes.com/images/icones/infos.png" class="mini_plus_icone" /> If you want more stats, leave us a message at <a href="mailto:contact@pretty-web.com">contact@pretty-web.com</a><br>
	</div>
	<h2>Quotes</h2>
	<div id="graph_quotes" class="graph_stats"></div>
	
	<h2>Members</h2>
	<div id="graph_empty_profile" class="graph_stats"></div>
	
	<h2>Search</h2>
	<div id="graph_search" class="graph_stats"></div>
	
	<h2>Other stats</h2>
	<div id="graph_newsletter" class="graph_stats"></div>
	<div id="members_favorite_quote" class="graph_stats"></div>
	<div id="top_user_favorite_quote" class="graph_stats"></div>
</div>';


include "footer.php";
?>