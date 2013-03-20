<?php
ini_set('session.cookie_domain', '.teen-quotes.com');
session_start();
header('Content-type: text/html; charset=utf-8');
if (preg_match('#[a-zA-Z]#', $_GET['p']))
{
	header("Location: /");
	exit;
}


// INCLUSION DES FICHIERS
// Does the SQL replication works or not
require '../files/replication.php';
require "../kernel/config.php";
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user, $db)  or die('Erreur de selection '.mysql_error()); 
require "../kernel/fonctions.php";
require '../lang/'.$language.'/general.php';

include '../kernel/connexion_cookie.php';
?>
<!DOCTYPE html>
<?php
if ($domain == $domain_fr)
{
	echo '<html lang="fr">';
}
else
{
	echo '<html lang="en">';
}
?>
<head>
<?php 
		include '../kernel/page_title.php';
?>
		<meta name="author" content="Antoine Augusti"/> 
		<meta name="revisit-after" content="1 day"/>
		<meta name="robots" content="all"/>
		<meta charset="utf-8" />
		<meta name="apple-itunes-app" content="app-id=577239995"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no;"/>
		
		<link rel="stylesheet" href="style.css" />

		<link rel="shortcut icon" type="image/x-icon" href="http://<?php echo $domain; ?>/images/favicon.png"/>
		<meta property="og:image" content="http://<?php echo $domain; ?>/images/icon50.png" />
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
<div id="topbar">
	<a href="../"><img src="http://<?php echo $domain; ?>/images/logo_<?php echo $name_logo; ?>.png" height="30px" alt="Logo <?php echo $name_website; ?>" /></a>
	<!--<span id="flags">
		<a href="http://teen-quotes.com" title="View the english version"><span class="icone_flags english"></span></a>
		<a href="http://kotado.fr" title="Voir la version française"><span class="icone_flags french"></span></a>
	</span>-->
</div><!-- END TOPBAR -->


<div id="content">

<div id="wrapper"><!-- START WRAPPER -->
	<div id="menu_content">
		<ul class="menu">
		<?php if ($_SESSION['logged'] != TRUE) { ?>
			<li><a href="/"><?php echo $home; ?></a></li>
			<li><a href="signup?topbar" title="<?php echo $sign_up; ?>" onClick="_gaq.push(['_trackEvent', 'signup', 'clic', 'Mobile - topbar']);"><?php echo $sign_up; ?></a></li>
			<li><a href="signin" title="<?php echo $sign_in; ?>"><?php echo $sign_in; ?></a></li>
			<li><a href="random" title="<?php echo $random_quote_m; ?>"><?php echo $random_quote_m; ?></a></li>
			<li><a href="searchform" title="<?php echo $search; ?>"><?php echo $search; ?></a></li>
			<li><a href="newsletter" title="<?php echo $newsletter; ?>">Newsletter</a></li>
			<li><a href="signup?addquote" title="<?php echo $add_a_quote; ?>" onClick="_gaq.push(['_trackEvent', 'signup', 'clic', 'Mobile - add a quote']);"><?php echo $add_a_quote; ?></a></li>
				<?php } else { ?>
			<li><a href="/" title="<?php echo $home; ?>"><?php echo $home; ?></a></li>
			<li><a href="user-<?php echo $id; ?>" title="<?php echo $my_profile; ?>"><?php echo $my_profile; ?></a></li>
			<li><a href="random" title="<?php echo $random_quote_m; ?>"><?php echo $random_quote_m; ?></a></li>
			<li><a href="searchform" title="<?php echo $search; ?>"><?php echo $search; ?></a></li>
			<?php if ($is_newsletter == "0") { ?><li><a href="newsletter">Newsletter</a></li><?php } ?>
			<li><a href="addquote" title="<?php echo $add_a_quote; ?>"><?php echo $add_a_quote; ?></a></li>
			<li><a href="?deconnexion" title="<?php echo $log_out; ?>"><?php echo $logout; ?></a></li>
			<?php if ($_SESSION['security_level'] >='2') { ?><li><a href="admin" title="Admin">Admin</a></li><?php } ?>
			<?php }	?>
		</ul>
	<div class="clear"></div>
	</div>

<div class="clear" style="height:10px"></div>

<?php
if ($download_app == TRUE OR $_SESSION['security_level'] > 0)
{
	/*
	if (((mb_eregi('ipod', $user_agent) OR mb_eregi('iphone', $user_agent)) AND $link_app_iphone != '#' AND $_SESSION['hide_download_app'] != TRUE) OR ((mb_eregi('ipod', $user_agent) OR mb_eregi('iphone', $user_agent)) AND $_SESSION['security_level'] > '0' AND $_SESSION['hide_download_app'] != TRUE))
	{
		echo $download_iphone_app;
	}
	*/
	if ((mb_eregi('android', $user_agent) AND $link_app_android != '#' AND $_SESSION['hide_download_app'] != TRUE) OR (mb_eregi('android', $user_agent) AND $_SESSION['security_level'] > '0' AND $_SESSION['hide_download_app'] != TRUE))
	{
		echo $download_android_app;
	}
}
?>