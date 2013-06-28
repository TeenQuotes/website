<?php
ini_set('session.cookie_domain', '.teen-quotes.com');
session_start();
header('Content-type: text/html; charset=utf-8');

// Go to the subdomain if we are in $domain/statistics
if (preg_match($domain.'/statistics/', $_SERVER['SCRIPT_URI']) AND !preg_match("#statistics#", $_SERVER['HTTP_HOST']))
{
	header('Status: 301 Moved Permanently', false, 301);  
	header("Location: http://statistics.teen-quotes.com");
	exit();
}

// Try to detect language from browser setting
if (!isset($_GET['fr']) AND !isset($_GET['en']))
{
	if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == "fr")
		header("Location: http://statistics.teen-quotes.com/fr");
	else
		header("Location: http://statistics.teen-quotes.com/en");
}


// Include all the files
// Does the SQL replication works or not
require '../files/replication.php';
require '../kernel/config.php';

// Switch language if the user wants it
if (isset($_GET['fr']))
{
	$language = 'french';
	$second_language = 'english';
}
else
{
	$language = 'english';
	$second_language = 'french';
}

$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user, $db)  or die('Erreur de selection '.mysql_error());

require '../kernel/fonctions.php';
require '../lang/'.$language.'/general.php';
include '../lang/'.$language.'/statistics.php';
include '../kernel/connexion_cookie.php';
include 'js/lang.php';
?>
<!DOCTYPE html>
<?php
if ($language == "french")
	echo '<html lang="fr">';
else
	echo '<html lang="en">';
?>
<head>
		<title><?php echo $name_website; ?> - Statistics</title>
		<meta name="description" content="<?php echo $description_statistics; ?>"/>
		<meta name="author" content="Antoine Augusti"/> 
		<meta name="revisit-after" content="1 day"/>
		<meta name="robots" content="all"/>
		<meta charset="utf-8" />
		
		<link href="//fonts.googleapis.com/css?family=Lato:300,400|Roboto:300,400" rel="stylesheet" type="text/css">
		<link href="//statistics.<?php echo $domain; ?>/style.css" rel="stylesheet" type="text/css"/>

		<link rel="shortcut icon" type="image/x-icon" href="http://<?php echo $domain; ?>/images/favicon.png"/>
		<meta property="og:image" content="http://<?php echo $domain; ?>/images/icon50.png" /> 
		
		<script src="//code.jquery.com/jquery-latest.min.js"></script>
		<script src="https://www.google.com/jsapi"></script>
		<script><?php echo $lang_js_charts; ?></script>
		<script src="/js/charts.js"></script>
		<?php $timestamp_last_update_stats = display_stats($language); ?>
		<script>
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', <?php echo "'".$google_analytics_account."'"; ?>]);
		  _gaq.push(['_setDomainName', <?php echo "'".$domain."'"; ?>]);
		  _gaq.push(['_setAllowHash', 'false']);
		  _gaq.push(['_setSiteSpeedSampleRate', 100]);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		</script>
</head>
<body>
	<div id="header">
		<div class="content">
			<div id="logo" class="animated rotateInDownRight">
				<a href="/" title="<?php echo $name_website; ?>" class="fade_on_hover"><img src="//<?php echo $domain; ?>/images/logo_<?php echo $name_logo; ?>.png" alt="<?php echo $name_website; ?>"/></a>
				<span id="caption"><?php echo $website_caption; ?></span>
			</div>
			<div id="flags_translate">
				<a href="/<?php echo substr($second_language, 0, 2); ?>" <?php hint('bottom', $change_language); ?> onClick="_gaq.push(['_trackEvent', 'translateStats', 'clic', '<?php echo $second_language; ?>']);"><span class="<?php echo substr($second_language, 0, 2); ?>"></span></a>
			</div>
		</div>
	</div><!-- END HEADER -->

	<div id="content">
		<?php echo $last_update ?> <abbr class="timeago" title="<?php echo $timestamp_last_update_stats; ?>"></abbr>.

		<div class="chart" class="animated rotateInDownLeft">
			<h1><?php echo $domain_visitors; ?></h1>
			<div id="geoMap" class="chartObject"></div>
			<div id="pieGeo" class="chartObject"></div>
			<div class="explanation">
				<h3><?php echo $data_provenance; ?></h3>
				<?php echo $visits_provenance; ?>
			</div>
			<div id="drawVisitsTechnology" class="chartObject"></div>
			<div id="drawMobileOS" class="chartObject"></div>
			<div id="drawVisitsDuration" class="chartObject"></div>
			<div id="sexUsers" class="chartObject"></div>
			<div class="explanation">
				<h3><?php echo $data_provenance; ?></h3>
				<?php echo $sex_data_provenance; ?>
			</div>
		</div><!-- END LES VISITEURS DE TEEN QUOTES -->

		<div class="chart">
			<h1><?php echo $the_quotes; ?></h1>		
			<div id="graph_quotes" class="chartObject"></div>
			<div id="quotes_time" class="chartObject"></div>
			<div id="quotes_time_percentage" class="chartObject"></div>
			<div id="days_between_signup_post_quote" class="chartObject"></div>
			<div id="days_between_signup_post_quote_table" class="chartObject table"></div>
			<div id="nb_quotes_submitted_user" class="chartObject"></div>
			<div id="nb_quotes_submitted_user_table" class="chartObject table"></div>
		</div><!-- END LES QUOTES -->
		
		<div class="chart">
			<h1><?php echo $domain_accounts; ?></h1>
			<div id="members_time" class="chartObject"></div>
			<div id="members_time_exp" class="chartObject"></div>
			<div id="users_ages" class="chartObject"></div>
			<div id="users_ages_table" class="chartObject table"></div>
			<div id="graph_empty_profile" class="chartObject"></div>
		</div><!-- END LES COMPTES -->

		<div class="chart">
			<h1><?php echo $the_comments; ?></h1>
			<div id="graph_comments_time" class="chartObject"></div>
			<div id="graph_comments_time_exp" class="chartObject"></div>
			<div id="comments_length" class="chartObject"></div>
			<div id="comments_length_table" class="chartObject table"></div>
		</div><!-- END LES COMMENTAIRES -->

		<div class="chart">
			<h1><?php echo $type_registration; ?></h1>
			<div id="graph_location_signup" class="chartObject"></div>
			<div id="sign_up_methods" class="chartObject"></div>
		</div><!-- END LES METHODES D'INSCRIPTION -->

	</div><!-- END CONTENT -->

	<div id="footer">
		<div class="content">
			<div class="left">
				<?php echo $footer_description; ?>
			</div>

			<div class="right">
				<?php
					echo $name_website.' &copy; '.date("Y");
				?>
				<br/>
				<br/>
				<a href="//m.<?php echo $domain; ?>/<?php echo $php_self; ?>"><?php echo $mobile_website; ?></a><br/>
				<a href="//stories.<?php echo $domain; ?>" title="<?php echo $stories; ?>" onClick="_gaq.push(['_trackEvent', 'stories', 'clic', 'Footer']);"><?php echo $stories; ?></a> &bull; <a href="//<?php echo $domain; ?>/advertise" title="<?php echo $advertise; ?>"><?php echo $advertise; ?></a><br/>
				<a href="//statistics.<?php echo $domain; ?>" title="<?php echo $statistics; ?>"><?php echo $statistics; ?></a> &bull; <a href="//<?php echo $domain; ?>/shortcuts" title="<?php echo $keyboard_shortcuts; ?>"><?php echo $keyboard_shortcuts; ?></a><br/>
				<a href="//<?php echo $domain; ?>/contact" title="Contact">Contact</a> &bull; <a href="//<?php echo $domain; ?>/legalterms" title="<?php echo $legal_terms; ?>"><?php echo $legal_terms; ?></a><br/>
				<br/>
				<span id="caption_footer">Designed in Paris. <span id="eiffel-tower"></span></span><br/>
			</div>

			<div class="clear"></div>
		</div>
	</div><!-- END FOOTER -->
<script>
$("#flags_translate a").click(function()
{
	var href = $(this).attr('href');
	$("body").fadeOut(2000);

	setTimeout(function()
	{
		window.location = href
	}, 2200);
	
	return false;
});
</script>
<script src="/js/timeago-<?php echo $language; ?>.js"></script>
</body>
</html>