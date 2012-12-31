<?php
ini_set('session.cookie_domain', '.teen-quotes.com');
session_start();
header('Content-type: text/html; charset=utf-8');
if (preg_match('#[a-zA-Z]#', $_GET['p']))
{
	header("Location: /");
	exit;
}

// Go to the subdomain if we are in $domaine/stories
if (preg_match($domaine.'/stories/', $_SERVER['SCRIPT_URI']) AND !preg_match("#stories#", $_SERVER['HTTP_HOST']))
{
	header("Location: http://stories.teen-quotes.com");
}

// Include all the files
// Does the SQL replication works or not
require '../files/replication.php';
require '../kernel/config.php';
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user, $db)  or die('Erreur de selection '.mysql_error()); 
require '../kernel/fonctions.php';
require '../lang/'.$language.'/general.php';
include '../lang/'.$language.'/stories.php';
include '../lang/'.$language.'/connexion.php';
include '../kernel/connexion_cookie.php';
?>
<!DOCTYPE html>
<?php
if ($domaine == $domain_fr)
{
	echo '<html lang="fr">';
}
else
{
	echo '<html lang="en">';
}
?>
<head>
		<title><?php echo $name_website; ?> - Stories</title>
		<meta name="description" content="<?php echo $description_stories; ?>"/>
		<meta name="author" content="Antoine Augusti"/> 
		<meta name="revisit-after" content="1 day"/>
		<meta name="robots" content="all"/>
		<meta charset="utf-8" />
		
		<link href='//fonts.googleapis.com/css?family=Ubuntu:300|Gloria+Hallelujah|Open+Sans:300' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="//<?php echo $domaine; ?>/style.css" />
		<link rel="stylesheet" href="//stories.<?php echo $domaine; ?>/style.css" />
		<link rel="stylesheet" href="//<?php echo $domaine; ?>/uniform/uniform.css" />

		<link rel="shortcut icon" type="image/x-icon" href="http://<?php echo $domaine; ?>/images/favicon.png"/>
		<meta property="og:image" content="http://<?php echo $domaine; ?>/images/icon50.png" /> 
		
		<script src="//code.jquery.com/jquery-latest.min.js"></script>
		<script>
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', <?php echo "'".$google_analytics_account."'"; ?>]);
		  _gaq.push(['_setDomainName', <?php echo "'".$domaine."'"; ?>]);
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
	<div id="header" class="shadow_header">
		<div class="content">
			<div id="logo">
				<a href="/" title="<?php echo $name_website; ?>" class="fade_on_hover"><img src="//<?php echo $domaine; ?>/images/logo_<?php echo $name_logo; ?>.png" alt="<?php echo $name_website; ?>"/></a>
				<span id="caption"><?php echo $website_caption; ?></span>
			</div>
		</div>
	</div><!-- END HEADER -->

	<div id="banner">
		<div class="container">
			<div class="text">
				<h1><?php echo $banner_title; ?></h1>
				<?php echo $banner_text; ?>
			</div>
		</div>
	</div><!-- END BANNER -->

	<div id="story_content">