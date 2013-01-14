<?php 
include 'header.php';
include 'lang/'.$language.'/statistics.php';
$query = mysql_query("UPDATE teen_quotes_settings SET value=value+1 WHERE param = 'clics_stats'");

echo '
<div class="post">
	<h1><img src="http://teen-quotes.com/images/icones/test.png" class="icone" />'.$statistics.'</h1>
	<div class="bandeau_erreur hide_this">
		'.$more_stats_email.'
	</div>
	'.$last_update.' <abbr class="timeago" title="'.$timestamp_last_update_stats.'"></abbr>
	<h2>'.$quotes.'</h2>
	<div id="graph_quotes" class="graph_stats"></div>
	<div id="quotes_time" class="graph_stats"></div>
	
	<h2>'.$members.'</h2>
	<div id="graph_empty_profile" class="graph_stats"></div>
	<div id="members_time" class="graph_stats"></div>

	<h2>'.$location_signup.'</h2>
	<div id="graph_location_signup" class="graph_stats"></div>
	
	<h2>'.$search.'</h2>
	<div id="graph_search" class="graph_stats"></div>
	
	<h2>'.$other_stats.'</h2>
	<div id="graph_newsletter" class="graph_stats"></div>
	<div id="members_favorite_quote" class="graph_stats"></div>
	<div id="top_user_favorite_quote" class="graph_stats"></div>
</div>';


include "footer.php";
?>