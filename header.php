<?php session_start();
error_reporting(E_ALL ^ E_NOTICE);
header("Cache-Control: public");

// INCLUSION DES FICHIERS
require "kernel/config.php";
$db = mysql_connect("$host", "$user", "$pass")  or die('Erreur de connexion '.mysql_error());
mysql_select_db('teenq208598',$db)  or die('Erreur de selection '.mysql_error()); 
require "kernel/fonctions.php";
require "lang/$language/general.php"; 




if (!$_SESSION['logged'])
{
	$_SESSION['logged'] = false;
}else{$post = mysql_real_escape_string($_SESSION['username']);}

if ($_COOKIE['Pseudo'] AND $_COOKIE['Pass']){
$pseudo = mysql_escape_string($_COOKIE['Pseudo']);
$pass = mysql_escape_string($_COOKIE['Pass']);
		$retour_nb_pseudo = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE `username` ='$pseudo'"));
		
		if ($retour_nb_pseudo == '1')
		{
			$compte = mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_account WHERE `username` = '$pseudo'"));				
			{	
				$sha = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE `pass` = '$pass' AND `username` = '$pseudo'"));
				if ($sha == '1')
				{
					
					$_SESSION['logged'] = true;
					$_SESSION['account'] = $compte['id'];										
					$_SESSION['pseudo'] = $pseudo;
					$_SESSION['security_level'] = $compte['security_level'];									
					$_SESSION['username'] = $compte['username'];
					
					$username = $_SESSION['username'];
					$id = $_SESSION['account'];
					$email=$compte['email'];
					$is_newsletter=mysql_num_rows(mysql_query("SELECT id FROM newsletter where email='$email'")); 
					
					if (empty($compte['birth_date']) && empty($compte['title']) && empty($compte['country']) && empty($compte['about_me']) && $compte['avatar']=="icon50.png" && empty($compte['city']))
						{
						$profile_not_fullfilled = TRUE;
						}
					
				}
			}
		}


;}
else
{
$_SESSION['logged'] = false;
}

if ($_SESSION['security_level'] >= '2') 
	{
	$citations_awaiting_approval = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved='0'"));
	$alerte_admin_query = mysql_fetch_array(mysql_query("SELECT alerte_admin FROM config WHERE id='1'"));
	$alerte_admin = $alerte_admin_query['alerte_admin'];
	if ($citations_awaiting_approval >= '10' AND $alerte_admin=='0')
		{
		$email_subject = "Quotes awaiting approval";
		$message = 'Hey,<br><br />There are more than 10 quotes awaiting approval ! It\'s time to check the admin panel, you can access it by clicking <a href="http://www.teen-quotes.com/admin" target="_blank">here</a>';
		$mail = mail("antoine.augusti@gmail.com", $email_subject, $top_mail.$message.$end_mail, $headers); 
		$mail_2 = mail("southernstarzz@facebook.com", $email_subject, $top_mail.$message.$end_mail, $headers);
		$update_alerte = mysql_query("UPDATE config SET alerte_admin='1' WHERE id='1'");	
		}
		
	if ($citations_awaiting_approval < '10' AND $alerte_admin=='1')
		{
		$update_alerte = mysql_query("UPDATE config SET alerte_admin='0' WHERE id='1'");
		}
	}
 ?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<?php 
// PERMET DE GERER LE TITRE DES PAGES DYNAMIQUES ET LES DESCRIPTION POUR LE SHARE SUR FB
if (isset($_GET['id_user'])) {
$id_user=mysql_real_escape_string($_GET['id_user']);
$php_self="user-$id_user";
$result = mysql_fetch_array(mysql_query("SELECT username FROM teen_quotes_account where id='$id_user'"));
$username_title = ucfirst($result['username']);
echo "<title>Teen Quotes | $username_title</title>";
echo"\r\n";
echo '<meta name="description" content="'.$username_title.'\'s profile on Teen Quotes" />';
}

elseif (isset($_GET['id_quote'])) {
$id_quote=mysql_real_escape_string($_GET['id_quote']);
$php_self="quote-$id_quote";
$result = mysql_fetch_array(mysql_query("SELECT texte_english FROM teen_quotes_quotes where id='$id_quote' AND approved='1'"));
$texte = $result['texte_english'];
echo '<title>Teen Quotes | Quote #'.$id_quote.'</title>';
echo '<meta name="description" content="'.$texte.'"/>';
}
elseif (isset($_GET['letter'])) {
$lettre = htmlspecialchars($_GET['letter']);
if (empty($lettre)) { $lettre = "a"; }
$php_self = 'members-'.$lettre.'';
echo '<title>Teen Quotes | Members - '.$lettre.'</title>';
echo"<meta name=\"description\" content=\"Teen Quotes : because our lives are filled full of beautiful sentences, and because some quotes are simply true\"/>";
}
else {
echo"<title>Teen Quotes | Because some quotes are simply true</title>";
echo"\r\n";
echo"<meta name=\"description\" content=\"Teen Quotes : because our lives are filled full of beautiful sentences, and because some quotes are simply true\"/>";
}?>	
		<meta name="keywords" content="'Teen Quotes', 'quotes', 'teen', 'citations', 'sentences', 'Augusti', 'Twitter', 'Facebook'"/> 
		<meta name="author" content="Antoine Augusti"/> 
		<meta name="revisit-after" content="2 days"/> 
		<meta name="date-creation-ddmmyyyy" content="2609010"/> 
		<meta name="Robots" content="all"/> 
		<meta name="Rating" content="General"/> 
		<meta name="location" content="France, FRANCE"/> 
		<meta name="expires" content="never"/> 
		<meta name="Distribution" content="Global"/> 
		<meta name="Audience" content="General"/> 
		<meta http-equiv="Content-Language" content="en,fr" /> 
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<link type="text/css" rel="stylesheet" media="screen" href="http://www.teen-quotes.com/style.css" /> 
		<!--[if IE]><style>.submit:hover{color:#000!important}</style><![endif]--> 
		<link rel="shortcut icon" type="image/x-icon" href="http://www.teen-quotes.com/images/favicon.gif" /> 
		<link rel="image_src" href="http://www.teen-quotes.com/images/icon50.png" /> 
		<meta property="og:image" content="http://www.teen-quotes.com/images/icon50.png" /> 
		

		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
		<script type="text/javascript" src="http://teen-quotes.com/ajax.js"></script>
		
		<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-12045924-10']);
		_gaq.push(['_setDomainName', 'teen-quotes.com']);
		_gaq.push(['_setAllowHash', 'false']);
		<?php
		if ($_SESSION['logged'] == false)
			{
			echo "_gaq.push(['._setCustomVar', 1, 'user-type', 'visitor', 2]);";
			}
		else
			{
			echo "_gaq.push(['._setCustomVar', 1, 'user-type', 'member', 2]);";
			}
		?>
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
	<a href="../" class="menu"><img src="http://www.teen-quotes.com/images/logo.png" alt="logo" /></a>
		<div class="follow">
		<a href="http://twitter.com/ohteenquotes" class="twitter-follow-button" data-show-count="false" data-lang="fr">Follow @ohteenquotes</a>
		<div class="clear"></div>
		<iframe src="http://www.facebook.com/plugins/like.php?locale=en_US&amp;app_id=211130238926911&amp;href=http%3A%2F%2Fwww.facebook.com%2Fohteenquotes&amp;send=false&amp;layout=button_count&amp;width=20&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=segoe+ui&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:21px;" allowTransparency="true"></iframe>
		</div>
</div><!-- END TOPBAR -->

<div id="menu">	
	<?php if (!$_SESSION['logged']) { ?>
	<a href="/" class="menu"><img src="http://www.teen-quotes.com/images/icones/menu/home.png" class="icone_menu" /><?php echo $home; ?></a>
	<a href="signup" class="menu"><img src="http://www.teen-quotes.com/images/icones/signin.png" class="icone_menu" /><?php echo $sign_up; ?></a>
	<a href="members" class="menu"><img src="http://www.teen-quotes.com/images/icones/menu/members.png" class="icone_menu" /><?php echo $members; ?></a>
	<a href="random" class="menu"><img src="http://www.teen-quotes.com/images/icones/menu/random.png" class="icone_menu" /><?php echo $random_quote; ?></a>
	<a href="newsletter" class="menu"><img src="http://www.teen-quotes.com/images/icones/menu/newsletter.png" class="icone_menu" />Newsletter</a>
	<a href="signup?addquote" class="menu"><img src="http://www.teen-quotes.com/images/icones/menu/add.png" class="icone_menu" /><?php echo $add_a_quote; ?></a>
	<span class="right">
		<a href="http://teen-quotes.com/<?php echo $php_self; ?>" title="View the website in English"><img src="http://www.teen-quotes.com/images/icones/english.png" class="icone_login_left" /></a>
		<a href="http://fr.teen-quotes.com/<?php echo $php_self; ?>" title="Voir le site en Français"><img src="http://www.teen-quotes.com/images/icones/french.png" class="icone_login_right" /></a>
	</span> 
		<?php } else { ?>
	<a href="/" class="menu"><img src="http://www.teen-quotes.com/images/icones/menu/home.png" class="icone_menu" /><?php echo $home; ?></a>
	<a href="user-<?php echo $id; ?>" class="menu"><img src="http://www.teen-quotes.com/images/icones/menu/profile.png" class="icone_menu" /><?php echo $my_profile; ?></a>
	<a href="members" class="menu"><img src="http://www.teen-quotes.com/images/icones/menu/members.png" class="icone_menu" /><?php echo $members; ?></a>
	<a href="random" class="menu"><img src="http://www.teen-quotes.com/images/icones/menu/random.png" class="icone_menu" /><?php echo $random_quote; ?></a>
	<?php if($is_newsletter=="0") { ?><a href="newsletter" class="menu"><img src="http://www.teen-quotes.com/images/icones/menu/newsletter.png" class="icone_menu" />Newsletter</a><?php } ?>
	<a href="addquote" class="menu"><img src="http://www.teen-quotes.com/images/icones/menu/add.png" class="icone_menu" /><?php echo $add_a_quote; ?></a>
	<?php if($_SESSION['security_level'] >='2') { ?><a href="admin" class="menu"><img src="http://www.teen-quotes.com/images/icones/menu/admin.png" class="icone_menu" />Admin <?php if ($citations_awaiting_approval > '0'){echo '- '.$citations_awaiting_approval.'';} ?></a><?php } ?>	
	<span class="right">
		<a href="http://teen-quotes.com/<?php echo $php_self; ?>" title="View the website in English"><img src="http://www.teen-quotes.com/images/icones/english.png" class="icone_login_left" /></a>
		<a href="http://fr.teen-quotes.com/<?php echo $php_self; ?>" title="Voir le site en Français"><img src="http://www.teen-quotes.com/images/icones/french.png" class="icone_login_right" /></a>
	</span>
		<?php }	?>
</div><!-- END MENU -->

<?php 
if($profile_not_fullfilled == TRUE && $_SERVER['PHP_SELF']=='/index.php')
	{
	echo ''.$profite_not_yet_fulffiled.'';
	}
?>

<div id="content">

	<div id="wrapper" <?php if ($_SERVER['PHP_SELF']!='/index.php' && $_SERVER['PHP_SELF']!='/random.php') {echo "style=\"margin-top:33px\"";} ?>><!-- START WRAPPER -->