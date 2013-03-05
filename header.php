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
require 'files/replication.php';
require 'kernel/config.php';
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user, $db)  or die('Erreur de selection '.mysql_error()); 
require 'kernel/fonctions.php';
require 'lang/'.$language.'/general.php';
include 'lang/'.$language.'/connexion.php';

include 'kernel/connexion_cookie.php';
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
		include 'kernel/page_title.php';
?>
		<meta name="author" content="Antoine Augusti"/> 
		<meta name="revisit-after" content="1 day"/>
		<meta name="robots" content="all"/>
		<meta charset="utf-8" />
		
		<link href='//fonts.googleapis.com/css?family=Ubuntu:300|Gloria+Hallelujah|Open+Sans:300' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="//<?php echo $domain; ?>/style.css" />

		<link rel="shortcut icon" type="image/x-icon" href="http://<?php echo $domain; ?>/images/favicon.png"/>
		<meta property="og:image" content="http://<?php echo $domain; ?>/images/icon50.png" /> 
		
		<script src="http://code.jquery.com/jquery-1.8.0.min.js"></script>
		
		<?php
		if ($php_self == "statistics")
		{
			$timestamp_last_update_stats = display_stats($language);
		}
		?>
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
		<div id="logo">
			<a href="/" title="<?php echo $name_website; ?>" class="fade_on_hover"><img src="//<?php echo $domain; ?>/images/logo_<?php echo $name_logo; ?>.png" alt="<?php echo $name_website; ?>"/></a>
			<span id="caption"><?php echo $website_caption; ?></span>
		</div>

		<div id="social-networks">
			<a href="http://twitter.com/<?php echo $twitter_url; ?>" class="twitter-follow-button" data-show-count="<?php echo $twitter_show_count; ?>" data-lang="<?php echo $twitter_lang; ?>">Follow @<?php echo $twitter_url; ?></a>
			<div class="clear"></div>
			<iframe src="http://www.facebook.com/plugins/like.php?locale=<?php echo $facebook_locale; ?>&amp;app_id=211130238926911&amp;href=<?php echo $facebook_like_url; ?>&amp;send=false&amp;layout=button_count&amp;width=20&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=segoe+ui&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:21px;" allowTransparency="true"></iframe>
		</div>
	</div>
</div><!-- END HEADER -->
<div id="menu">
	<div class="content">	
	<?php 
	if ($_SESSION['logged'] != TRUE) 
	{ 
	?>
		<a href="/" class="menu" title="<?php echo $home; ?>"><span class="icone_menu home"></span><?php echo $home; ?></a>
		<a href="/signup?topbar" onClick="_gaq.push(['_trackEvent', 'signup', 'clic', 'Website - topbar']);" class="menu"><span class="icone_menu signin"></span><span <?php hint('bottom', $sign_up_hint); ?>><?php echo $sign_up; ?></span></a>
		<a href="/members" class="menu" title="<?php echo $members; ?>"><span class="icone_menu members"></span><?php echo $members; ?></a>
		<a href="/random" class="menu" title="<?php echo $random_quote; ?>"><span class="icone_menu random"></span><?php echo $random_quote; ?></a>
		<a href="/newsletter" class="menu" title="<?php echo $newsletter; ?>"><span class="icone_menu newsletter"></span>Newsletter</a>
		<a href="/signup?addquote" onClick="_gaq.push(['_trackEvent', 'signup', 'clic', 'Website - addquote']);" class="menu"><span class="icone_menu add"></span><span <?php hint('bottom', $add_a_quote_hint); ?>><?php echo $add_a_quote; ?></span></a>
		<?php
		// APPLICATIONS
		if ($download_app == TRUE OR $_SESSION['security_level'] >= '2')
		{
		?>
			<a href="/apps" onClick="_gaq.push(['_trackEvent', 'appiOS', 'clic', 'Website - menu topbar']);" class="menu" title="<?php echo $apps; ?>"><span class="icone_menu apps"></span><span <?php hint('bottom', $apps_hint); ?>><?php echo $apps; ?></span></a>
		<?php
		}
	}
	else
	{
	?>
		<a href="/" class="menu" title="<?php echo $home; ?>"><span class="icone_menu home"></span><?php echo $home; ?></a>
		<a href="/user-<?php echo $id; ?>" class="menu"><span class="icone_menu profile"></span><span <?php hint('bottom', $my_profile_hint); ?>><?php echo $my_profile; ?></span></a>
		<a href="/members" class="menu" title="<?php echo $members; ?>"><span class="icone_menu members"></span><?php echo $members; ?></a>
		<a href="/random" class="menu" title="<?php echo $random_quote; ?>"><span class="icone_menu random"></span><?php echo $random_quote; ?></a>
		<a href="/addquote" class="menu"><span class="icone_menu add"></span><span <?php hint('bottom', $add_a_quote_hint); ?>><?php echo $add_a_quote; ?></span></a>
		<?php
		// APPLICATIONS
		if ($download_app == TRUE OR $_SESSION['security_level'] >= '2')
		{
		?>
			<a href="/apps" onClick="_gaq.push(['_trackEvent', 'appiOS', 'clic', 'Website - menu topbar']);" class="menu"><span class="icone_menu apps"></span><span <?php hint('bottom', $apps_hint); ?>><?php echo $apps; ?></span></a>
		<?php
		}

		// ADMIN PANEL
		if($_SESSION['security_level'] >= '2') 
		{ 
		?>
			
			<a href="/admin" class="menu" title="Admin"><span class="icone_menu admin"></span>Admin <?php if ($citations_awaiting_approval > 0){echo '- '.$citations_awaiting_approval;} ?></a>
		<?php
		}
	}
	?>
		<span class="right">
			<a href="//teen-quotes.com" title="View the english version"><span class="icone_flags english"></span></a>
			<a href="//kotado.fr" title="Voir la version française"><span class="icone_flags french"></span></a>
		</span> 
	</div>
</div><!-- END MENU -->

<?php
if($_SESSION['profile_not_fullfilled'] == TRUE AND $_SERVER['PHP_SELF'] == '/index.php')
{
	echo $profite_not_yet_fulffiled;
}
?>

<div id="content">

	<div id="wrapper" <?php if ($_SERVER['PHP_SELF'] =='/index.php' OR $_SERVER['PHP_SELF'] =='/random.php')
		{
		echo 'class="wrapper_index"';
		} 
	?>><!-- START WRAPPER -->