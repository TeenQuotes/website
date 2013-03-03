<?php
// PERMET DE GERER LE TITRE DES PAGES DYNAMIQUES ET LES DESCRIPTION POUR LE SHARE SUR FB
if (isset($_GET['id_user'])) 
{
	$id_user = mysql_real_escape_string($_GET['id_user']);
	$php_self = 'user-'.$id_user;
	$result = mysql_fetch_array(mysql_query("SELECT username FROM teen_quotes_account WHERE id = '$id_user'"));
	$username_title = $result['username'];
	echo '<title>'.$name_website.' | '.$username_title.'</title>';
	echo "\r\n";
	if ($domain == $domain_fr)
	{
		echo '<meta name="description" content="Profil de '.$username_title.' sur '.$name_website.'. Voir ses citations dans les favoris, ses citations ajoutées."/>';
	}
	else
	{
		echo '<meta name="description" content="'.$username_title.'\'s profile on '.$name_website.'. View his favorite quotes and his quotes." />';
	}
	echo "\r\n";
	// Add a canonical URL
	echo '<link rel="canonical" href="http://'.$domain.'/user-'.$id_user.'" />';
}
elseif (isset($_GET['p']) AND (int) $_GET['p'] >= 2 AND !(preg_match('#members#', $_SERVER["SCRIPT_URI"])) AND !(preg_match('#random#', $_SERVER["SCRIPT_URI"])))
{
	$page_index = htmlspecialchars($_GET['p']);
	echo '<title>'.$name_website.' | '.$last_quotes.' - page '.$page_index.'</title>';
	echo "\r\n";

	if ($domain == $domain_fr)
	{
		echo '<meta name="description" content="'.$name_website.' : ta dose quotidienne de phrases. Citations de la vie quotidienne. Quotes Ados." />';
	}
	else
	{
		echo '<meta name="description" content="'.$name_website.' : because our lives are filled full of beautiful sentences, and because some quotes are simply true. Your every day life moments."/>';
	}
	echo "\r\n";

	// Add a canonical URL
	echo '<link rel="canonical" href="http://'.$domain.'/?p='.$page_index.'" />';
}
elseif (preg_match('#random#', $_SERVER["SCRIPT_URI"]))
{
	$page_random = (int) htmlspecialchars($_GET['p']);

	if ($page_random >= 2)
	{
		echo '<title>'.$name_website.' | '.$random_quote.' - page '.$page_random.'</title>';
	}
	else
	{
		echo '<title>'.$name_website.' | '.$random_quote.'</title>';
	}
	echo "\r\n";

	if ($domain == $domain_fr)
	{
		echo '<meta name="description" content="Citations aléatoires postées sur '.$name_website.'. Citations de la vie quotidienne. Quotes ados."/>';
	}
	else
	{
		echo '<meta name="description" content="Random quotes released on '.$name_website.'. Because some quotes are simply true. Your everyday life moments."/>';
	}
	echo "\r\n";
	// Add a canonical URL
	echo '<link rel="canonical" href="http://'.$domain.'/?p='.$page_random.'" />';
}
elseif (isset($_GET['id_quote'])) 
{
	$id_quote = mysql_real_escape_string($_GET['id_quote']);
	$php_self = 'quote-'.$id_quote;
	$result = mysql_fetch_array(mysql_query("SELECT texte_english FROM teen_quotes_quotes WHERE id = '$id_quote' AND approved = '1'"));
	$texte = $result['texte_english'];

	echo '<title>'.$name_website.' | Quote #'.$id_quote.'</title>';
	echo "\r\n";
	echo '<meta name="description" content="'.$texte.'"/>';
	echo "\r\n";

	// Add a canonical URL
	echo '<link rel="canonical" href="http://'.$domain.'/quote-'.$id_quote.'" />';
}
elseif (isset($_GET['letter']) OR preg_match('#members#', $_SERVER["SCRIPT_URI"])) 
{
	$lettre = mysql_real_escape_string($_GET['letter']);
	if (empty($lettre)) { $lettre = "A"; }
	$php_self = 'members-'.$lettre;

	if ($domain == $domain_fr)
	{
		echo '<title>'.$name_website.' | Membre - '.$lettre.'</title>';
		echo "\r\n";
		echo '<meta name="description" content="Membres commençant par la lettre '.$lettre.' sur '.$name_website.'. '.$name_website.' : ta dose quotidienne de phrases. Citations de la vie quotidienne. Quotes Ados." />';
	}
	else
	{
		echo '<title>'.$name_website.' | Member - '.$lettre.'</title>';
		echo "\r\n";
		echo '<meta name="description" content="Members beginning with '.$lettre.' on '.$name_website.'. '.$name_website.' : because some quotes are simply true. Your everyday life moments." />';
	}
	echo "\r\n";
	// Add a canonical URL
	echo '<link rel="canonical" href="http://'.$domain.'/members" />';
}
elseif ($php_self == 'contact')
{
	echo '<title>'.$name_website.' | Contact</title>';
	echo "\r\n";

	if ($domain == $domain_fr)
	{
		echo '<meta name="description" content="'.$name_website.' : contactez-nous par email pour toute question."/>';	
	}
	else
	{
		echo '<meta name="description" content="'.$name_website.' : contact us by email if you have any question."/>';
	}
	echo "\r\n";		
}
elseif ($php_self == 'signup')
{
	echo '<title>'.$name_website.' | '.$sign_up.'</title>';
	echo "\r\n";

	if ($domain == $domain_fr)
	{
		echo '<meta name="description" content="'.$name_website.' : créez votre compte et accédez à tous les avantages qui vont avec : profils, citations favorites, ajout de commentaires..." />';
	}
	else
	{
		echo '<meta name="description" content="'.$name_website.' : create an account and be able to access all the advantages that come with it: profiles, favorite quotes, comments..." />';
	}
	echo "\r\n";
}
elseif ($php_self == 'apps')
{
	echo '<title>'.$name_website.' | '.$application.'</title>';
	echo "\r\n";
	if ($domain == $domain_fr)
	{
		echo '<meta name="description" content="'.$name_website.' : téléchargez notre application pour iOS et Android. Visitez notre version mobile depuis votre portable."/>';
	}
	else
	{
		echo '<meta name="description" content="'.$name_website.' : download our application for iOS and Android. Visit our mobile website right from your smartphone."/>';
	}
	echo "\r\n";
	// Add a canonical URL
	echo '<link rel="canonical" href="http://'.$domain.'/apps" />';
}
elseif ($php_self == 'advertise')
{
	echo '<title>'.$name_website.' | '.$advertise.'</title>';
	echo "\r\n";
	if ($domain == $domain_fr)
	{
		echo '<meta name="description" content="'.$name_website.' : avec plus de 1 700 000 followers sur Twitter, près de 50 000 fans sur Facebook et un site, Teen Quotes est une bonne opportunité pour proposer de la publicité."/>';
	}
	else
	{
		echo '<meta name="description" content="'.$name_website.' : with more than 1,700,000 followers on Twitter, nearly 50,000 fans on Facebook and a website, Teen Quotes is a really good opportunity for advertising."/>';
	}
	echo "\r\n";
}
elseif ($php_self == 'statistics')
{
	echo '<title>'.$name_website.' | '.$statistics.'</title>';
	echo "\r\n";
	if ($domain == $domain_fr)
	{
		echo '<meta name="description" content="'.$name_website.' : statistiques. Quelques statistiques sur l\'utilisation du site : membres, citations, recherches..."/>';
	}
	else
	{
		echo '<meta name="description" content="'.$name_website.' : statistics. Some statistics about the use of the website : members, quotes, searchs..."/>';
	}
	echo "\r\n";
}
else 
{
	if ($domain == $domain_fr)
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
	echo "\r\n";
}
// Fin des différents cas de <title></title>
if ($domain == $domain_fr)
{
	echo '<meta name="keywords" content="Kotado, Quotes Ados, Citations Ados, Citations vie quotidienne, Citations adolescents, Teen Quotes, Pretty Web, Antoine Augusti, Twitter"/>';
}
else
{
	echo '<meta name="keywords" content="Teen Quotes, teenage quotes, teenager quotes, quotes for teenagers, teen qoutes, quotes, teen, citations, sentences, Augusti, Twitter, Facebook"/>';
}
echo "\r\n";