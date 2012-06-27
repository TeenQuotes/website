<?php
session_start();

$domaine = $_SERVER['HTTP_HOST'];
switch ($domaine)
	{
	case "fr.teen-quotes.com" :
	$domaine = "teen-quotes.com";
	break;
	case "m.teen-quotes.com" :
	$domaine = "teen-quotes.com";
	break;
	case "en.kotado.fr" :
	$domaine = "kotado.fr";
	break;
	case "m.kotado.fr" :
	$domaine = "kotado.fr";
	break;
	}
$domaine_en = "teen-quotes.com";
$domaine_fr = "kotado.fr";

// INCLUSION DES FICHIERS
require "kernel/config.php";
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user,$db)  or die('Erreur de selection '.mysql_error()); 
require "kernel/fonctions.php";
require 'lang/'.$language.'/general.php';

if ($_SESSION['logged'] == TRUE AND (empty($_SESSION['id']) OR empty($_SESSION['username']) OR empty($_SESSION['email']) OR empty($_SESSION['avatar'])))
	{
	deconnexion();
	}

if (isset($_COOKIE['Pseudo']) AND isset($_COOKIE['Pass']) AND $_SESSION['logged'] == FALSE)
	{
	$pseudo = mysql_real_escape_string($_COOKIE['Pseudo']);
	$pass = mysql_real_escape_string($_COOKIE['Pass']);
	$query_base = mysql_query("SELECT * FROM teen_quotes_account WHERE `username` ='$pseudo'");
	
	$retour_nb_pseudo = mysql_num_rows($query_base);
	if ($retour_nb_pseudo == '1')
		{				
		$sha = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE `pass` = '$pass' AND `username` = '$pseudo'"));
		if ($sha == '1')
			{
			$compte = mysql_fetch_array($query_base);
			
			$_SESSION['logged'] = TRUE;
			$_SESSION['id'] = $compte['id'];										
			$_SESSION['security_level'] = $compte['security_level'];									
			$_SESSION['username'] = $compte['username'];
			$_SESSION['email'] = $compte['email'];
			$_SESSION['avatar'] = $compte['avatar'];
			
			$username = $_SESSION['username'];
			$id = $_SESSION['id'];
			$email = $compte['email'];
			$last_visit = $compte['last_visit'];
			$session_last_visit = $_SESSION['last_visit_user'];
				
			last_visit($session_last_visit,$last_visit,$id);
				
			if (empty($compte['birth_date']) AND empty($compte['title']) AND empty($compte['country']) AND empty($compte['about_me']) AND $compte['avatar']=="icon50.png" AND empty($compte['city']))
				{
				$_SESSION['profile_not_fullfilled'] = TRUE;
				}
			}
		}
	}

if ($_SESSION['logged'] == TRUE)
	{
	$username = $_SESSION['username'];
	$id = $_SESSION['id'];
	$email = $_SESSION['email'];
	$session_last_visit = $_SESSION['last_visit_user'];
	if (username_est_valide(strtolower($_SESSION['username'])) == FALSE AND $php_self != 'changeusername')
		{
		echo '<meta http-equiv="refresh" content="0;url=changeusername">';
		}
	if (isset($_COOKIE['Pseudo']) AND username_est_valide(strtolower($_SESSION['username'])) == TRUE AND username_est_valide($_SESSION['username']) == FALSE)
		{
		$_SESSION['username'] = strtolower($_SESSION['username']);
		}
	}
?>
<!DOCTYPE html>
<?php
if ($domaine == 'kotado.fr')
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
// PERMET DE GERER LE TITRE DES PAGES DYNAMIQUES ET LES DESCRIPTION POUR LE SHARE SUR FB
if (isset($_GET['id_user'])) 
	{
		$id_user = mysql_real_escape_string($_GET['id_user']);
		$php_self = 'user-'.$id_user.'';
		$result = mysql_fetch_array(mysql_query("SELECT username FROM teen_quotes_account WHERE id = '$id_user'"));
		$username_title = $result['username'];
		echo '<title>'.$name_website.' | '.$username_title.'</title>';
		echo "\r\n";
		if ($domaine == 'kotado.fr')
		{
			echo '<meta name="description" content="Profil de '.$username_title.' sur '.$name_website.'. Voir ses citations dans les favoris, ses citations ajoutées."/>';
		}
		else
		{
			echo '<meta name="description" content="'.$username_title.'\'s profile on '.$name_website.'. View his favorite quotes and his quotes." />';
		}
	}
elseif (isset($_GET['id_quote'])) 
	{
		$id_quote=mysql_real_escape_string($_GET['id_quote']);
		$php_self = 'quote-'.$id_quote.'';
		$result = mysql_fetch_array(mysql_query("SELECT texte_english FROM teen_quotes_quotes WHERE id = '$id_quote' AND approved = '1'"));
		$texte = $result['texte_english'];
		echo '<title>'.$name_website.' | Quote #'.$id_quote.'</title>';
		echo "\r\n";
		echo '<meta name="description" content="'.$texte.'"/>';
	}
elseif (isset($_GET['letter']) OR $php_self == "members") 
	{
		$lettre = mysql_real_escape_string($_GET['letter']);
		if (empty($lettre)) { $lettre = "A"; }
		$php_self = 'members-'.$lettre.'';
		if ($domaine == 'kotado.fr')
		{
			echo '<title>'.$name_website.' | Membre - '.$lettre.'</title>';
			echo "\r\n";
			echo '<meta name="description" content="Membres commençant par la lettre '.$lettre.' sur '.$name_website.'. '.$name_website.' : ta dose quotidienne de phrases. Citations de la vie quotidienne. Quotes Ados." />';
		}
		else
		{
			echo '<title>'.$name_website.' | Member - '.$lettre.'</title>';
			echo "\r\n";
			echo '<meta name="description" content="Members beginning with '.$lettre.' on '.$name_website.'. '.$name_website.' : because some quotes are simply true." />';
		}

	}
elseif ($php_self == 'contact')
	{
		echo '<title>'.$name_website.' | Contact</title>';
		echo "\r\n";
		if ($domaine == 'kotado.fr')
		{
			echo '<meta name="description" content="'.$name_website.' : contactez-nous par email pour toute question."/>';	
		}
		else
		{
			echo '<meta name="description" content="'.$name_website.' : contact us by email if you have any question."/>';
		}
		
	}
elseif ($php_self == 'apps')
	{
		include 'lang/'.$language.'/apps.php';
		echo '<title>'.$name_website.' | '.$applications.'</title>';
		echo "\r\n";
		if ($domaine == 'kotado.fr')
		{
			echo '<meta name="description" content="'.$name_website.' : téléchargez notre application pour iOS et Android."/>';
		}
		else
		{
			echo '<meta name="description" content="'.$name_website.' : download our application for iOS and Android."/>';
		}
	}
else 
	{
		if ($domaine == 'kotado.fr')
		{
			echo '<title>'.$name_website.' | Ta dose quotidienne de phrases</title>';
			echo "\r\n";
			echo '<meta name="description" content="'.$name_website.' : ta dose quotidienne de phrases. Citations de la vie quotidienne. Quotes Ados." />';
		}
		else
		{
			echo '<title>'.$name_website.' | Because some quotes are simply true</title>';
			echo "\r\n";
			echo '<meta name="description" content="'.$name_website.' : because our lives are filled full of beautiful sentences, and because some quotes are simply true. Your every day life moments."/>';
		}
	}

		if ($domaine == 'kotado.fr')
		{
			echo '<meta name="keywords" content="Kotado, Quotes Ados, Citations Ados, Citations vie quotidienne, Citations adolescents, Teen Quotes, Pretty Web, Antoine Augusti, Twitter"/>';
		}
		else
		{
			echo '<meta name="keywords" content="Teen Quotes, teenage quotes, teenager quotes, quotes for teenagers, teen qoutes, quotes, teen, citations, sentences, Augusti, Twitter, Facebook"/>';
		}
		?>
		<meta name="author" content="Antoine Augusti"/> 
		<meta name="revisit-after" content="2 days"/>
		<meta name="robots" content="all"/>
		<meta charset="utf-8" />
		
		<link rel="stylesheet" href="http://<?php echo $domaine; ?>/style.css" /> 
		<link rel="stylesheet" href="http://<?php echo $domaine; ?>/uniform/uniform.css" />

		<?php
		if ($domaine == 'kotado.fr')
		{
			echo '<link rel="shortcut icon" type="image/x-icon" href="http://'.$domaine.'/images/favicon.png"/>';
		}
		else
		{
			echo '<link rel="shortcut icon" type="image/x-icon" href="http://'.$domaine.'/images/favicon.gif"/>';
		} 
		?>
		<meta property="og:image" content="http://<?php echo $domaine; ?>/images/icon50.png" /> 
		
		<script src="http://code.jquery.com/jquery-latest.min.js"></script>
		
		<?php
		if ($php_self == "statistics")
			{
			create_stats($language);
			}
		?>
		<?php 
		if ($domaine == 'kotado.fr')
		{
		?>
		<script>
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-12045924-22']);
		  _gaq.push(['_setDomainName', 'kotado.fr']);
		  _gaq.push(['_setAllowHash', 'false']);
		  _gaq.push(['_setSiteSpeedSampleRate', 100]);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		</script>
		<?php
		}
		else
		{
		?>
		<script>
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-12045924-10']);
		_gaq.push(['_setDomainName', 'teen-quotes.com']);
		_gaq.push(['_setAllowHash', 'false']);
		_gaq.push(['_setSiteSpeedSampleRate', 100]);
		_gaq.push(['_trackPageview']);
		
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		</script>
		<?php
		}
		?>
</head>
<body>
<div id="topbar">
	<a href="../" class="menu"><img src="http://<?php echo $domaine; ?>/images/logo.png" alt="Logo <?php echo $name_website; ?>" /></a>
		<div class="follow">
			<?php
			if ($domaine == 'kotado.fr')
			{
			?>
			<a href="https://twitter.com/kotado_" class="twitter-follow-button" data-show-count="false" data-lang="fr">Follow @kotado_</a>
			<div class="clear"></div>
			<iframe src="http://www.facebook.com/plugins/like.php?locale=fr_FR&amp;app_id=211130238926911&amp;href=http%3A%2F%2Fwww.facebook.com%2Fpages%2Fkotado%2F207728899322070&amp;send=false&amp;layout=button_count&amp;width=40&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=segoe+ui&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
			<?php
			}
			else
			{
			?>
			<a href="http://twitter.com/ohteenquotes" class="twitter-follow-button" data-show-count="false" data-lang="en">Follow @ohteenquotes</a>
			<div class="clear"></div>
			<iframe src="http://www.facebook.com/plugins/like.php?locale=en_US&amp;app_id=211130238926911&amp;href=http%3A%2F%2Fwww.facebook.com%2Fohteenquotes&amp;send=false&amp;layout=button_count&amp;width=20&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=segoe+ui&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:21px;" allowTransparency="true"></iframe>
			<?php
			}
			?>		
		</div>
</div><!-- END TOPBAR -->

<div id="menu">	
	<?php if ($_SESSION['logged'] != TRUE) { ?>
	<a href="/" class="menu"><span class="icone_menu home"></span><?php echo $home; ?></a>
	<a href="signup?topbar" class="menu"><span class="icone_menu signin"></span><?php echo $sign_up; ?></a>
	<a href="members" class="menu"><span class="icone_menu members"></span><?php echo $members; ?></a>
	<a href="random" class="menu"><span class="icone_menu random"></span><?php echo $random_quote; ?></a>
	<a href="newsletter" class="menu"><span class="icone_menu newsletter"></span>Newsletter</a>
	<a href="signup?addquote" class="menu"><span class="icone_menu add"></span><?php echo $add_a_quote; ?></a>
	<span class="right">
		<a href="http://teen-quotes.com" title="View the english version"><span class="icone_flags english"></span></a>
		<a href="http://kotado.fr" title="Voir la version française"><span class="icone_flags french"></span></a>
	</span> 
		<?php } else { ?>
	<a href="/" class="menu"><span class="icone_menu home"></span><?php echo $home; ?></a>
	<a href="user-<?php echo $id; ?>" class="menu"><span class="icone_menu profile"></span><?php echo $my_profile; ?></a>
	<a href="members" class="menu"><span class="icone_menu members"></span><?php echo $members; ?></a>
	<a href="random" class="menu"><span class="icone_menu random"></span><?php echo $random_quote; ?></a>
	<?php if($is_newsletter=="0") { ?><a href="newsletter" class="menu"><span class="icone_menu newsletter"></span>Newsletter</a><?php } ?>
	<a href="addquote" class="menu"><span class="icone_menu add"></span><?php echo $add_a_quote; ?></a>
	<?php if($_SESSION['security_level'] >='2') { ?><a href="apps" class="menu"><img src="http://<?php echo $domaine; ?>/images/icones/mobile.png" class="icone_menu_apps" /><?php echo $apps; ?></a><a href="admin" class="menu"><span class="icone_menu admin"></span>Admin <?php if ($citations_awaiting_approval > '0'){echo '- '.$citations_awaiting_approval.'';} ?></a><?php } ?>	
	<span class="right">
		<a href="http://teen-quotes.com" title="View the english version"><span class="icone_flags english"></span></a>
		<a href="http://kotado.fr" title="Voir la version française"><span class="icone_flags french"></span></a>
	</span> 
		<?php }	?>
</div><!-- END MENU -->

<?php 
if($_SESSION['profile_not_fullfilled'] == TRUE AND $_SERVER['PHP_SELF'] =='/index.php')
	{
	echo ''.$profite_not_yet_fulffiled.'';
	}
?>

<div id="content">

	<div id="wrapper" <?php if ($_SERVER['PHP_SELF']!='/index.php' AND $_SERVER['PHP_SELF']!='/random.php')
		{
		echo 'style="margin-top:33px"';
		} 
	?>><!-- START WRAPPER -->