<?php
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
switch ($domaine)
	{
	case "teen-quotes.com" :
	$name_website = "Teen Quotes";
	break;
	case "kotado.fr" :
	$name_website = "Kotado";
	break;
	}
$domaine_en = "teen-quotes.com";
$domaine_fr = "kotado.fr";

// NOMS DES PAGES
$url_page=$_SERVER["SCRIPT_URI"];
$m_url=substr($url_page, 0, 25);
$fr_url=substr($url_page, 0, 9);

$page=$_SERVER['PHP_SELF'];
$taille= strlen($name_page);
$taille2=$taille-4;
$php_self = substr("$page",1,$taille2);

if ($domaine == "teen-quotes.com")
	{
	if($php_self=="index" OR $php_self=="error") 
		{
		$php_self=NULL;
		}
		
	if($fr_url=="http://fr")
		{
		$language="french";
		$second_language = "english";
		$php_self=$page_include; // FIX NOM DE LA PAGE  VOIR /fr/.htaccess
		}
	else
		{
		$language="english";
		$second_language = "french";
		}

	if (isset($_GET['p']))
		{
		$page_index = htmlspecialchars($_GET['p']);
		$php_self = '?p='.$page_index.'';
		
		if($fr_url=="http://fr")
			{
			$php_self=$page_include.'?p='.$page_index.'';
			}
		}
	}
elseif ($domaine == $domaine_fr)
	{
	if($php_self=="index" OR $php_self=="error") 
		{
		$php_self=NULL;
		}
		
	if($fr_url=="http://en")
		{
		$language="english";
		$second_language = "french";
		$php_self=$page_include; // FIX NOM DE LA PAGE  VOIR /fr/.htaccess
		}
	else
		{
		$language="french";
		$second_language = "english";
		}

	if (isset($_GET['p']))
		{
		$page_index = htmlspecialchars($_GET['p']);
		$php_self = '?p='.$page_index.'';
		
		if($fr_url=="http://en")
			{
			$php_self=$page_include.'?p='.$page_index.'';
			}
		}
	}

/* ANCIENNE TRADUCTION


if (isset($_GET['english']))
{
setcookie("french", 1 , time() -4200);
setcookie("english", 1 , time() + (((3600*24)*30)*12));
echo "<meta http-equiv=\"refresh\" content=\"1;url=../\" />";
}

if (isset($_GET['french']))
{
setcookie("english", 1 , time() -4200);
setcookie("french", 1 , time() + (((3600*24)*30)*12));
echo "<meta http-equiv=\"refresh\" content=\"1;url=../\" />";
}

if (isset($_COOKIE['english']) OR !isset($_COOKIE['french'])) {
$language="english";
}
else{
$language="french";
}
*/

/* MESSAGES DE CONNEXION - BUG IE


if (isset($_GET['succes']))
{
	echo "<span class=\"error\"><img src=\"http://<?php echo $domaine; ?>/images/icones/succes.png\" class=\"alerte\" />$co_succes</span>";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=../\" />";
}

if (isset($_GET['deco_succes']))
{
	echo "<span class=\"error\"><img src=\"http://<?php echo $domaine; ?>/images/icones/succes.png\" class=\"alerte\" />$deco_succes</span>";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=../\" />";
} */


if (isset($_GET['co'])) 
	{
	$domaine = "teen-quotes.com";
	$pseudo=$_SESSION['pseudo'];		
	$passwd=$_SESSION['passwd'];

	setcookie("Pseudo", $pseudo, time() + (((3600*24)*30)*12), null, '.'.$domaine.'', false, true);
	setcookie("Pass", $passwd, time() + (((3600*24)*30)*12), null, '.'.$domaine.'', false, true);

	$_SESSION['passwd']=NULL;
	// redirection
	?>
	<script language="JavaScript">
	<!--
	window.location.href="../"
	//-->
	</script>
	<?php
	}

if (isset($_GET['deconnexion']))
	{
	deconnexion();
	}

function deconnexion()
	{
	$domaine = "teen-quotes.com";
	$_SESSION = array(); //Destruction des variables.
	session_destroy(); //Destruction de la session.
	setcookie("Pseudo", Yo, time()-4200, null, '.'.$domaine.'', false, true);
	setcookie("Pass", Yo, time()-4200, null, '.'.$domaine.'', false, true);
	setcookie("Pass", Yo, time()-4200);
	setcookie("Pseudo", Yo, time()-4200);
	$pseudo="";
	$id="";
	$email="";
	$username=""; ?>
	<script language="JavaScript">
	<!--
	window.location.href="../"
	//-->
	</script>	
	
<?php 
	}

function caracteresAleatoires($nombreDeCaracteres)
	{
	$string = ""; 
	$chaine = "abcdefghijklmnpqrstuvwxyz123456789"; 
	srand((double)microtime()*1000000);
	
	for($i=0;$i<$nombreDeCaracteres; $i++)
		{
		$string .= $chaine[rand()%strlen($chaine)]; 
		}
	return $string;
    }

function microtime_float() 
	{ 
	list($usec, $sec) = explode(" ", microtime()); 
	return ((float)$usec + (float)$sec); 
	}
	
$time_start = microtime_float(); 

function number_space($number) 
	{
	$number_space = number_format($number, 0, ',', ' '); // Arrondi et espaces sur les milliers
	return $number_space;
	}

function create_stats ($language) {
if ($language == "english")
	{
	require 'lang/'.$language.'/stats.php';
	}
else
	{
	require '../lang/'.$language.'/stats.php';
	}
$total_quotes = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes"));
$quotes_approved = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved='1'"));
$quotes_rejected = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved='-1'"));
$quotes_pending = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved='0'"));
$quotes_queued = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved='2'"));

$total_members = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account"));
$nb_empty_avatar = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE avatar='icon50.png'"));
$nb_members_empty_profile = $total_members - $nb_empty_avatar;

$nb_favorite = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_favorite"));
$nb_members_has_favorite_quotes = mysql_num_rows(mysql_query("SELECT DISTINCT A.id FROM teen_quotes_account A, teen_quotes_favorite F WHERE A.id IN (F.id_user)"));
$nb_members_no_favorite_quotes = $total_members - $nb_members_has_favorite_quotes;
$nb_newsletter = mysql_num_rows(mysql_query("SELECT id FROM newsletter"));
$nb_members_newsletter = mysql_num_rows(mysql_query("SELECT a.id FROM teen_quotes_account a, newsletter n WHERE a.email = n.email"));
$nb_no_members_newsletter = $nb_newsletter - $nb_members_newsletter;

$query_top_user_favorite = mysql_query("SELECT COUNT( F.id ) AS nb_fav , A.id, A.username AS username FROM teen_quotes_favorite F, teen_quotes_quotes Q, teen_quotes_account A WHERE F.id_quote = Q.id AND Q.auteur_id = A.id GROUP BY A.id ORDER BY COUNT( F.id ) DESC LIMIT 0,20");
$query_search = mysql_query ("SELECT * FROM teen_quotes_search ORDER BY value DESC LIMIT 0,20");
$nb_search_query = mysql_fetch_array(mysql_query("SELECT SUM(value) AS nb_search FROM teen_quotes_search"));
$nb_search = $nb_search_query['nb_search'];
$graph_stats_js = "
<script type=\"text/javascript\" src=\"http://www.google.com/jsapi\"></script>
<script type=\"text/javascript\">
  google.load('visualization', '1', {packages: ['corechart']});
</script>
<script type=\"text/javascript\">
function graph_quotes() {
var data = new google.visualization.DataTable();
data.addColumn('string', 'Quote');
data.addColumn('number', 'Status');
data.addRows(4);
data.setValue(0, 0, '".$approved." : ".$quotes_approved."');
data.setValue(0, 1, ".$quotes_approved.");
data.setValue(1, 0, '".$rejected." : ".$quotes_rejected."');
data.setValue(1, 1, ".$quotes_rejected.");
data.setValue(2, 0, '".$pending." : ".$quotes_pending."');
data.setValue(2, 1, ".$quotes_pending.");
data.setValue(3, 0, '".$waiting_to_be_posted." : ".$quotes_queued."');
data.setValue(3, 1, ".$quotes_queued.");

// Create and draw the visualization.
new google.visualization.PieChart(document.getElementById('graph_quotes')).
	draw(data, {title:'".$total_nb_quotes." : ".$total_quotes."'});
}
function graph_empty_profile() {
var data = new google.visualization.DataTable();
data.addColumn('string', 'Profile');
data.addColumn('number', 'Status');
data.addRows(2);
data.setValue(0, 0, '".$profile_not_fullfilled." : ".$nb_empty_avatar."');
data.setValue(0, 1, ".$nb_empty_avatar.");
data.setValue(1, 0, '".$profile_fullfilled." : ".$nb_members_empty_profile."');
data.setValue(1, 1, ".$nb_members_empty_profile.");

// Create and draw the visualization.
new google.visualization.PieChart(document.getElementById('graph_empty_profile')).
	draw(data, {title:'".$total_nb_members." : ".$total_members."'});
}
function graph_newsletter() {
var data = new google.visualization.DataTable();
data.addColumn('string', 'Profile');
data.addColumn('number', 'Status');
data.addRows(2);
data.setValue(0, 0, '".$visitors." : ".$nb_no_members_newsletter."');
data.setValue(0, 1, ".$nb_no_members_newsletter.");
data.setValue(1, 0, '".$members." : ".$nb_members_newsletter."');
data.setValue(1, 1, ".$nb_members_newsletter.");

// Create and draw the visualization.
new google.visualization.PieChart(document.getElementById('graph_newsletter')).
	draw(data, {title:'".$people_subscribed_newsletter." : ".$nb_newsletter."'});
}
function members_favorite_quote() {
var data = new google.visualization.DataTable();
data.addColumn('string', 'Profile');
data.addColumn('number', 'Status');
data.addRows(2);
data.setValue(0, 0, '".$members_with_fav_quotes." : ".$nb_members_has_favorite_quotes."');
data.setValue(0, 1, ".$nb_members_has_favorite_quotes.");
data.setValue(1, 0, '".$members_without_fav_quotes." : ".$nb_members_no_favorite_quotes."');
data.setValue(1, 1, ".$nb_members_no_favorite_quotes.");

// Create and draw the visualization.
new google.visualization.PieChart(document.getElementById('members_favorite_quote')).
	draw(data, {title:'".$members_and_fav_quotes."'});
}
function top_user_favorite_quote() {
var data = new google.visualization.DataTable();
data.addColumn('string', 'Profile');
data.addColumn('number', 'Status');
data.addRows(21);";
$i = '0';
$sum_fav_top_user = '0';
while ($donnees = mysql_fetch_array($query_top_user_favorite))
	{
	$nb_fav = $donnees['nb_fav'];
	$username = $donnees['username'];
	
	$sum_fav_top_user = $sum_fav_top_user + $nb_fav;
	$graph_stats_js .="
	data.setValue(".$i.", 0, '".$username." : ".$nb_fav."');
	data.setValue(".$i.", 1, ".$nb_fav.");
	";
	$i++;
	}
$reste_nb_favorite = $nb_favorite -  $sum_fav_top_user;
$graph_stats_js .="
data.setValue(".$i.", 0, '".$others." : ".$reste_nb_favorite."');
data.setValue(".$i.", 1, ".$reste_nb_favorite.");
// Create and draw the visualization.
new google.visualization.PieChart(document.getElementById('top_user_favorite_quote')).
	draw(data, {title:'".$top_members_ordered_by_nb_quotes_in_fav." (".$nb_favorite." ".$quotes_in_fav.")'});
}
function graph_search() {
var data = new google.visualization.DataTable();
data.addColumn('string', 'Profile');
data.addColumn('number', 'Status');
data.addRows(21);";
$j = '0';
$sum_nb_search = '0';
while ($donnees = mysql_fetch_array($query_search))
	{
	$value = $donnees['value'];
	$text = ucfirst($donnees['text']);
	
	$sum_nb_search = $sum_nb_search + $value;
	
	$graph_stats_js .="
	data.setValue(".$j.", 0, '".$text." : ".$value."');
	data.setValue(".$j.", 1, ".$value.");
	";
	$j++;
	}
$reste_nb_search = $nb_search - $sum_nb_search;
$graph_stats_js .="
data.setValue(".$i.", 0, '".$others." : ".$reste_nb_search."');
data.setValue(".$i.", 1, ".$reste_nb_search.");
// Create and draw the visualization.
new google.visualization.PieChart(document.getElementById('graph_search')).
	draw(data, {title:'".$total_nb_search." : ".$nb_search."'});
}
google.setOnLoadCallback(graph_quotes);
google.setOnLoadCallback(graph_empty_profile); 
google.setOnLoadCallback(graph_newsletter); 
google.setOnLoadCallback(members_favorite_quote); 
google.setOnLoadCallback(top_user_favorite_quote);
google.setOnLoadCallback(graph_search);
</script>
";
echo $graph_stats_js;
}


$minute = date("i");
if ($minute % '10' == '0' OR $_SESSION['security_level'] > '0') 
	{
	$citations_awaiting_approval = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved='0'"));
	$alerte_admin_query = mysql_fetch_array(mysql_query("SELECT alerte_admin FROM config WHERE id='1'"));
	$alerte_admin = $alerte_admin_query['alerte_admin'];
	if ($citations_awaiting_approval >= '10' AND $alerte_admin=='0')
		{
		$email_subject = "Quotes awaiting approval";
		$domaine = "teen-quotes.com";
		
		$message = 'Hey,<br><br />There are more than 10 quotes awaiting approval ! It\'s time to check the admin panel, you can access it by clicking <a href="http://'.$domaine.'/admin" target="_blank">here</a>';
		$mail = mail("antoine.augusti@gmail.com", $email_subject, $top_mail.$message.$end_mail, $headers); 
 		$mail_2 = mail("southernstarzz@facebook.com", $email_subject, $top_mail.$message.$end_mail, $headers);
 		$update_alerte = mysql_query("UPDATE config SET alerte_admin='1' WHERE id='1'");
		}
		
	if ($citations_awaiting_approval < '10' AND $alerte_admin=='1')
		{
		$update_alerte = mysql_query("UPDATE config SET alerte_admin='0' WHERE id='1'");
		}
	}

function last_visit($session_last_visit,$last_visit,$id_account)
	{
	if ($session_last_visit != '1')
		{
		$today = date("d/m/Y");
		if ($last_visit != $today)
			{
			$update_last_visit = mysql_query("UPDATE teen_quotes_account SET last_visit='$today' WHERE id='$id_account'");
			$_SESSION['last_visit_user'] = '1';
			}
		}
	}
	
function is_quote_new($date_quote,$last_visit,$page,$compteur_quote)
	{
	include "config.php";
	
	$yesterday_timestamp = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
	$yesterday= date("d/m/Y", $yesterday_timestamp);
	
	$jour = substr($date_quote, 0, 2);
	$mois = substr($date_quote, 3, 2);
	$annee = substr($date_quote, 6, 4);
	$timestamp_date_quote = mktime(0, 0, 0, $mois, $jour, $annee); 
	
	$jour_last_visit = substr($last_visit, 0, 2);
	$mois_last_visit = substr($last_visit, 3, 2);
	$annee_last_visit = substr($last_visit, 6, 4);
	$timestamp_last_visit = mktime(0, 0, 0, $mois_last_visit, $jour_last_visit, $annee_last_visit); 
	
	
	if ($date_quote == $yesterday OR ($timestamp_last_visit != '943916400' AND $timestamp_date_quote >$timestamp_last_visit) OR ($page == '1' AND $compteur_quote < $nb_quote_released_per_day))
		{
		echo '<span class="icone_new_quote hide_this"></span>';
		}
	}


$heure = date("H");
// RESET COMPTEUR QUOTE POSTED TODAY
if ($heure >= "22" AND $heure <= "23")
	{
	$compteur_quote_posted_today_query = mysql_fetch_array(mysql_query("SELECT compteur_quote_posted_today FROM config WHERE id='1'"));
	$compteur_quote_posted_today = $compteur_quote_posted_today_query['compteur_quote_posted_today'];
	
	if ($compteur_quote_posted_today == '1')
		{
		$update = mysql_query ("UPDATE config SET compteur_quote_posted_today='0' WHERE id='1'");
		}
	}
// POSTAGE DES QUOTES ENTRE 0H ET 2H DU MATIN
elseif ($heure >= "00" AND $heure <= "02")
	{
	$compteur_quote_posted_today_query = mysql_fetch_array(mysql_query("SELECT compteur_quote_posted_today FROM config WHERE id='1'"));
	$compteur_quote_posted_today = $compteur_quote_posted_today_query['compteur_quote_posted_today'];
	
	if ($compteur_quote_posted_today == '0')
		{
		flush_quotes();
		}
	}

// POST DES $nb_quote_released_per_day QUOTES DU JOUR
function flush_quotes ()
	{
	include "config.php";
	
	$query = mysql_query("SELECT id FROM teen_quotes_quotes WHERE approved='2' ORDER BY id ASC LIMIT 0,$nb_quote_released_per_day");
	$affected_rows = mysql_affected_rows();
	
	while ($result = mysql_fetch_array($query))
		{
		$id_quote = $result['id'];
		
		$query_texte_quote = mysql_fetch_array(mysql_query("SELECT texte_english,date,auteur_id FROM teen_quotes_quotes WHERE id='$id_quote'"));
		$texte_quote = $query_texte_quote['texte_english'];
		$date_quote = $query_texte_quote['date'];
		$auteur_id = $query_texte_quote['auteur_id'];

		$approve_quote= mysql_query("UPDATE teen_quotes_quotes set approved='1' WHERE id='$id_quote'");

		$query_email_auteur = mysql_fetch_array(mysql_query("SELECT email,username FROM teen_quotes_account WHERE id='$auteur_id'"));
		$email_auteur = $query_email_auteur['email'];
		$name_auteur = ucfirst($query_email_auteur['username']);

		if ($approve_quote AND !empty($email_auteur)) 
			{
			$message = "$top_mail Hello <font color=\"#5C9FC0\"><b>$name_auteur</b></font> !<br><br />Your quote has been <font color=\"#5C9FC0\"><b>approved</b></font> recently by a member of our team ! <div style=\"background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px\">$texte_quote<br><br /><a href=\"http://www.teen-quotes.com/quote-$id_quote\" target=\"_blank\">#$id_quote</a><span style=\"float:right\">by <a href=\"http://www.teen-quotes.com/user-$auteur_id\" target=\"_blank\">$name_auteur</a> on $date_quote</span></div>Congratulations !<br><br />Your Quote is now visible on our website. You can share it or comment it if you want !<br><br /><br />If you want to see your quote, <a href=\"http://www.teen-quotes.com/quote-$id_quote\" target=\"_blank\">click here</a>.<br><br /><br />Sincerely,<br><b>The Teen Quotes Team</b><br /><br /><br /><div style=\"border-top:1px dashed #CCCCCC\"></div><br /><br />VERSION FRANCAISE :<br /><br />Bonjour <font color=\"#5C9FC0\"><b>$name_auteur</b></font> !<br><br />Votre citation a été récemment <font color=\"#5C9FC0\"><b>approuvée</b></font> par un membre de notre équipe ! <div style=\"background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px\">$texte_quote<br><br /><a href=\"http://www.teen-quotes.com/quote-$id_quote\" target=\"_blank\">#$id_quote</a><span style=\"float:right\">par <a href=\"http://www.teen-quotes.com/user-$auteur_id\" target=\"_blank\">$name_auteur</a> le $date_quote</span></div>Congratulations !<br><br />Votre citation est maintenant visible sur Teen Quotes. Vous pouvez dès à présent la partager ou la commenter si vous le souhaitez !<br><br /><br />Si vous voulez voir votre citation, <a href=\"http://www.teen-quotes.com/quote-$id_quote\" target=\"_blank\">cliquez ici</a>.<br><br /><br />Cordialement,<br><b>The Teen Quotes Team</b> $end_mail";
			$mail = mail($email_auteur, "Quote approved", $message, $headers); 
			}
		$ids_quotes_posted_today .= ''.$id_quote.'';
		$ids_quotes_posted_today .= ",";
		}
	$update = mysql_query ("UPDATE config SET compteur_quote_posted_today='1' WHERE id='1'");
	
	$ids_quotes_posted_today = substr($ids_quotes_posted_today,0,strlen($ids_quotes_posted_today)-1);
	if ($affected_rows >= '1')
		{
		MailPostedToday($ids_quotes_posted_today);
		}
	}

function select_country($country,$other_countries,$common_choices)
	{
	$country = ucfirst($country);
	$str='
	<select name="country" style="width:197px;">
		<optgroup label="'.$common_choices.'">
			<option value="United States" selected="selected">United States</option> 
			<option value="Canada">Canada</option> 
			<option value="United Kingdom" >United Kingdom</option>
			<option value="Ireland" >Ireland</option>
			<option value="Australia" >Australia</option>
			<option value="New Zealand" >New Zealand</option>
		</optgroup>
		<optgroup label="'.$other_countries.'">
			<option value="Afghanistan">Afghanistan</option> 
			<option value="Albania">Albania</option> 
			<option value="Algeria">Algeria</option> 
			<option value="American Samoa">American Samoa</option> 
			<option value="Andorra">Andorra</option> 
			<option value="Angola">Angola</option> 
			<option value="Anguilla">Anguilla</option> 
			<option value="Antarctica">Antarctica</option> 
			<option value="Antigua and Barbuda">Antigua and Barbuda</option> 
			<option value="Argentina">Argentina</option> 
			<option value="Armenia">Armenia</option> 
			<option value="Aruba">Aruba</option> 
			<option value="Australia">Australia</option> 
			<option value="Austria">Austria</option> 
			<option value="Azerbaijan">Azerbaijan</option> 
			<option value="Bahamas">Bahamas</option> 
			<option value="Bahrain">Bahrain</option> 
			<option value="Bangladesh">Bangladesh</option> 
			<option value="Barbados">Barbados</option> 
			<option value="Belarus">Belarus</option> 
			<option value="Belgium">Belgium</option> 
			<option value="Belize">Belize</option> 
			<option value="Benin">Benin</option> 
			<option value="Bermuda">Bermuda</option> 
			<option value="Bhutan">Bhutan</option> 
			<option value="Bolivia">Bolivia</option> 
			<option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option> 
			<option value="Botswana">Botswana</option> 
			<option value="Bouvet Island">Bouvet Island</option> 
			<option value="Brazil">Brazil</option> 
			<option value="British Indian Ocean Territory">British Indian Ocean Territory</option> 
			<option value="Brunei Darussalam">Brunei Darussalam</option> 
			<option value="Bulgaria">Bulgaria</option> 
			<option value="Burkina Faso">Burkina Faso</option> 
			<option value="Burundi">Burundi</option> 
			<option value="Cambodia">Cambodia</option> 
			<option value="Cameroon">Cameroon</option> 
			<option value="Canada">Canada</option> 
			<option value="Cape Verde">Cape Verde</option> 
			<option value="Cayman Islands">Cayman Islands</option> 
			<option value="Central African Republic">Central African Republic</option> 
			<option value="Chad">Chad</option> 
			<option value="Chile">Chile</option> 
			<option value="China">China</option> 
			<option value="Christmas Island">Christmas Island</option> 
			<option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option> 
			<option value="Colombia">Colombia</option> 
			<option value="Comoros">Comoros</option> 
			<option value="Congo">Congo</option> 
			<option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option> 
			<option value="Cook Islands">Cook Islands</option> 
			<option value="Costa Rica">Costa Rica</option> 
			<option value="Cote D\'ivoire">Cote D\'ivoire</option> 
			<option value="Croatia">Croatia</option> 
			<option value="Cuba">Cuba</option> 
			<option value="Cyprus">Cyprus</option> 
			<option value="Czech Republic">Czech Republic</option> 
			<option value="Denmark">Denmark</option> 
			<option value="Djibouti">Djibouti</option> 
			<option value="Dominica">Dominica</option> 
			<option value="Dominican Republic">Dominican Republic</option> 
			<option value="Ecuador">Ecuador</option> 
			<option value="Egypt">Egypt</option> 
			<option value="El Salvador">El Salvador</option> 
			<option value="Equatorial Guinea">Equatorial Guinea</option> 
			<option value="Eritrea">Eritrea</option> 
			<option value="Estonia">Estonia</option> 
			<option value="Ethiopia">Ethiopia</option> 
			<option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option> 
			<option value="Faroe Islands">Faroe Islands</option> 
			<option value="Fiji">Fiji</option> 
			<option value="Finland">Finland</option> 
			<option value="France">France</option> 
			<option value="French Guiana">French Guiana</option> 
			<option value="French Polynesia">French Polynesia</option> 
			<option value="French Southern Territories">French Southern Territories</option> 
			<option value="Gabon">Gabon</option> 
			<option value="Gambia">Gambia</option> 
			<option value="Georgia">Georgia</option> 
			<option value="Germany">Germany</option> 
			<option value="Ghana">Ghana</option> 
			<option value="Gibraltar">Gibraltar</option> 
			<option value="Greece">Greece</option> 
			<option value="Greenland">Greenland</option> 
			<option value="Grenada">Grenada</option> 
			<option value="Guadeloupe">Guadeloupe</option> 
			<option value="Guam">Guam</option> 
			<option value="Guatemala">Guatemala</option> 
			<option value="Guinea">Guinea</option> 
			<option value="Guinea-bissau">Guinea-bissau</option> 
			<option value="Guyana">Guyana</option> 
			<option value="Haiti">Haiti</option> 
			<option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option> 
			<option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option> 
			<option value="Honduras">Honduras</option> 
			<option value="Hong Kong">Hong Kong</option> 
			<option value="Hungary">Hungary</option> 
			<option value="Iceland">Iceland</option> 
			<option value="India">India</option> 
			<option value="Indonesia">Indonesia</option> 
			<option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option> 
			<option value="Iraq">Iraq</option> 
			<option value="Ireland">Ireland</option> 
			<option value="Israel">Israel</option> 
			<option value="Italy">Italy</option> 
			<option value="Jamaica">Jamaica</option> 
			<option value="Japan">Japan</option> 
			<option value="Jordan">Jordan</option> 
			<option value="Kazakhstan">Kazakhstan</option> 
			<option value="Kenya">Kenya</option> 
			<option value="Kiribati">Kiribati</option> 
			<option value="Korea, Democratic People\'s Republic of">Korea, Democratic People\'s Republic of</option> 
			<option value="Korea, Republic of">Korea, Republic of</option> 
			<option value="Kuwait">Kuwait</option> 
			<option value="Kyrgyzstan">Kyrgyzstan</option> 
			<option value="Lao People\'s Democratic Republic">Lao People\'s Democratic Republic</option> 
			<option value="Latvia">Latvia</option> 
			<option value="Lebanon">Lebanon</option> 
			<option value="Lesotho">Lesotho</option> 
			<option value="Liberia">Liberia</option> 
			<option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option> 
			<option value="Liechtenstein">Liechtenstein</option> 
			<option value="Lithuania">Lithuania</option> 
			<option value="Luxembourg">Luxembourg</option> 
			<option value="Macao">Macao</option> 
			<option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option> 
			<option value="Madagascar">Madagascar</option> 
			<option value="Malawi">Malawi</option> 
			<option value="Malaysia">Malaysia</option> 
			<option value="Maldives">Maldives</option> 
			<option value="Mali">Mali</option> 
			<option value="Malta">Malta</option> 
			<option value="Marshall Islands">Marshall Islands</option> 
			<option value="Martinique">Martinique</option> 
			<option value="Mauritania">Mauritania</option> 
			<option value="Mauritius">Mauritius</option> 
			<option value="Mayotte">Mayotte</option> 
			<option value="Mexico">Mexico</option> 
			<option value="Micronesia, Federated States of">Micronesia, Federated States of</option> 
			<option value="Moldova, Republic of">Moldova, Republic of</option> 
			<option value="Monaco">Monaco</option> 
			<option value="Mongolia">Mongolia</option> 
			<option value="Montserrat">Montserrat</option> 
			<option value="Morocco">Morocco</option> 
			<option value="Mozambique">Mozambique</option> 
			<option value="Myanmar">Myanmar</option> 
			<option value="Namibia">Namibia</option> 
			<option value="Nauru">Nauru</option> 
			<option value="Nepal">Nepal</option> 
			<option value="Netherlands">Netherlands</option> 
			<option value="Netherlands Antilles">Netherlands Antilles</option> 
			<option value="New Caledonia">New Caledonia</option> 
			<option value="New Zealand">New Zealand</option> 
			<option value="Nicaragua">Nicaragua</option> 
			<option value="Niger">Niger</option> 
			<option value="Nigeria">Nigeria</option> 
			<option value="Niue">Niue</option> 
			<option value="Norfolk Island">Norfolk Island</option> 
			<option value="Northern Mariana Islands">Northern Mariana Islands</option> 
			<option value="Norway">Norway</option> 
			<option value="Oman">Oman</option> 
			<option value="Pakistan">Pakistan</option> 
			<option value="Palau">Palau</option> 
			<option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option> 
			<option value="Panama">Panama</option> 
			<option value="Papua New Guinea">Papua New Guinea</option> 
			<option value="Paraguay">Paraguay</option> 
			<option value="Peru">Peru</option> 
			<option value="Philippines">Philippines</option> 
			<option value="Pitcairn">Pitcairn</option> 
			<option value="Poland">Poland</option> 
			<option value="Portugal">Portugal</option> 
			<option value="Puerto Rico">Puerto Rico</option> 
			<option value="Qatar">Qatar</option> 
			<option value="Reunion">Reunion</option> 
			<option value="Romania">Romania</option> 
			<option value="Russian Federation">Russian Federation</option> 
			<option value="Rwanda">Rwanda</option> 
			<option value="Saint Helena">Saint Helena</option> 
			<option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option> 
			<option value="Saint Lucia">Saint Lucia</option> 
			<option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option> 
			<option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option> 
			<option value="Samoa">Samoa</option> 
			<option value="San Marino">San Marino</option> 
			<option value="Sao Tome and Principe">Sao Tome and Principe</option> 
			<option value="Saudi Arabia">Saudi Arabia</option> 
			<option value="Senegal">Senegal</option> 
			<option value="Serbia and Montenegro">Serbia and Montenegro</option> 
			<option value="Seychelles">Seychelles</option> 
			<option value="Sierra Leone">Sierra Leone</option> 
			<option value="Singapore">Singapore</option> 
			<option value="Slovakia">Slovakia</option> 
			<option value="Slovenia">Slovenia</option> 
			<option value="Solomon Islands">Solomon Islands</option> 
			<option value="Somalia">Somalia</option> 
			<option value="South Africa">South Africa</option> 
			<option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option> 
			<option value="Spain">Spain</option> 
			<option value="Sri Lanka">Sri Lanka</option> 
			<option value="Sudan">Sudan</option> 
			<option value="Suriname">Suriname</option> 
			<option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option> 
			<option value="Swaziland">Swaziland</option> 
			<option value="Sweden">Sweden</option> 
			<option value="Switzerland">Switzerland</option> 
			<option value="Syrian Arab Republic">Syrian Arab Republic</option> 
			<option value="Taiwan, Province of China">Taiwan, Province of China</option> 
			<option value="Tajikistan">Tajikistan</option> 
			<option value="Tanzania, United Republic of">Tanzania, United Republic of</option> 
			<option value="Thailand">Thailand</option> 
			<option value="Timor-leste">Timor-leste</option> 
			<option value="Togo">Togo</option> 
			<option value="Tokelau">Tokelau</option> 
			<option value="Tonga">Tonga</option> 
			<option value="Trinidad and Tobago">Trinidad and Tobago</option> 
			<option value="Tunisia">Tunisia</option> 
			<option value="Turkey">Turkey</option> 
			<option value="Turkmenistan">Turkmenistan</option> 
			<option value="Turks and Caicos Islands">Turks and Caicos Islands</option> 
			<option value="Tuvalu">Tuvalu</option> 
			<option value="Uganda">Uganda</option> 
			<option value="Ukraine">Ukraine</option> 
			<option value="United Arab Emirates">United Arab Emirates</option> 
			<option value="United Kingdom">United Kingdom</option> 
			<option value="United States">United States</option> 
			<option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option> 
			<option value="Uruguay">Uruguay</option> 
			<option value="Uzbekistan">Uzbekistan</option> 
			<option value="Vanuatu">Vanuatu</option> 
			<option value="Venezuela">Venezuela</option> 
			<option value="Viet Nam">Viet Nam</option> 
			<option value="Virgin Islands, British">Virgin Islands, British</option> 
			<option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option> 
			<option value="Wallis and Futuna">Wallis and Futuna</option> 
			<option value="Western Sahara">Western Sahara</option> 
			<option value="Yemen">Yemen</option> 
			<option value="Zambia">Zambia</option> 
			<option value="Zimbabwe">Zimbabwe</option>
		</optgroup>
	</select>';
		if(strstr($str, 'value="'.$country.'"')) 
		{
		$str=str_replace('selected="selected"', '', $str);
		$str=str_replace('value="'.$country.'"', 'value="'.$country.'" selected="selected"', $str);
		}
	echo $str;
	}


function MailRandomQuote($nombre) 
	{
	$query = mysql_query('SELECT id, texte_english,date,auteur,auteur_id FROM teen_quotes_quotes WHERE approved = 1 ORDER BY RAND() LIMIT '.$nombre.'');
		
	while($donnees=mysql_fetch_array($query)) 
		{
		$txt_quote=$donnees['texte_english'];
		$id_quote=$donnees['id'];
		$auteur=$donnees['auteur'];
		$auteur_id=$donnees['auteur_id'];
		$date=$donnees['date'];
		
		$email_txt.= '<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">';
		$email_txt.= ''.$txt_quote.'<br><div style="font-size:90%;margin-top:5px"><a href="http://www.teen-quotes.com/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://www.teen-quotes.com/user-'.$auteur_id.'" target="_blank">'.$auteur.'</a> on '.$date.'</span></div>';
		$email_txt.= '</div>';
		}
		
	return $email_txt;

	}

function MailPostedToday($id_quote) 
	{
	include "config.php";
	
	if (!empty($id_quote))
		{
		$id_quote = str_replace(',', '\',\'', $id_quote);
		$query = mysql_query("SELECT id, texte_english,date,auteur,auteur_id FROM teen_quotes_quotes WHERE approved = '1' AND id IN ('$id_quote')");
			
		while($donnees = mysql_fetch_array($query)) 
			{
			$txt_quote=$donnees['texte_english'];
			$id_quote=$donnees['id'];
			$auteur=$donnees['auteur'];
			$auteur_id=$donnees['auteur_id'];
			$date=$donnees['date'];
			
			$email_txt.= '<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">';
			$email_txt.= ''.$txt_quote.'<br><div style="font-size:90%;margin-top:5px"><a href="http://'.$domaine.'/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://'.$domaine.'/user-'.$auteur_id.'" target="_blank">'.$auteur.'</a> on '.$date.'</span></div>';
			$email_txt.= '</div>';
			}
			
		$today = date("d/m/Y"); 
		$message = ''.$top_mail.$query_txt.'Here are the quotes posted today ('.$today.') :<br><br />'.$email_txt.$end_mail.'';
		
		$search_email = mysql_query("SELECT value FROM teen_quotes_settings WHERE param = 'email_quote_today'");
		
		while ($donnees = mysql_fetch_array($search_email))
			{
			$email = $donnees['value'];
			$mail = mail($email, 'Quotes posted today - '.$today.'', $message, $headers);
			}
		}
	}

function geoloca_ip($ip){
	$email = "u3jr1cyppzo6ax0@jetable.org";
	$pass = "azerty";
	global $erreur;
	global $result;
	$tab_result = array();
	$urlb = "http://www.geolocalise-ip.com/api.php";
	//$url = $urlb."?email=".$email."&pass=".$pass."&ip=".$ip;
	$url = $urlb."?email=".urlencode($email)."&pass=".urlencode($pass)."&ip=".$ip;
	$result = file_get_contents($url);
	if($result!=false AND $result!=""){
		$tab_result_temp = split("&",$result);
		if(is_array($tab_result_temp)){
			if(sizeof($tab_result_temp)>0){
				foreach($tab_result_temp as $val){
					$tb_v_temp = split("=",$val);
					$tab_result[$tb_v_temp[0]] = $tb_v_temp[1];
				}
			}else{
				$erreur=1;
			}
		}else{
			$erreur=1;
		}
		foreach($tab_result as $key => $val){
			if($key !="")
				global ${$key};
				${$key}=$val;
		}
	}else{
		$erreur=1;
	}
}


function cut_tweet($chaine)
	{
		$lg_max='117';
	   if (strlen($chaine) > $lg_max) 
	   {
		  $chaine1 = substr($chaine, 0, $lg_max);
		  $last_space = strrpos($chaine1, " "); 
		  
		  // On ajoute ... à la suite de cet espace    
		  $chaine1 = substr($chaine1, 0, $last_space);
		  $chaine1 .='...';
		  
		  return $chaine1;
	   }
	elseif (strlen($chaine) <= '105')
	   {
	   $chaine .= ' @ohteenquotes';
	   return $chaine;
	   }
	else
	   {
	   return $chaine;
	   }
	}

function afficher_favori ($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$id_user) 
	{
	if ($logged == true AND $is_favorite == '0') 
		{
		echo '<span class="favorite" data-id="'.$id_quote.'"><a href="" onclick="favorite('.$id_quote.','.$id_user.'); return false;" title="'.$add_favorite.'"><img src="http://teen-quotes.com/images/icones/heart.png" /></a></span>';
		}
	elseif($logged == true AND $is_favorite == '1')
		{
		echo '<span class="favorite" data-id="'.$id_quote.'"><a href=""  onclick="unfavorite('.$id_quote.','.$id_user.'); return false;" title="'.$unfavorite.'"><img src="http://teen-quotes.com/images/icones/broken_heart.gif" /></a></span>';
		}
	}

function afficher_favori_m ($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite) 
	{
	if ($logged == true AND $is_favorite=='0') 
		{
		echo '<span class="favorite"><a href="favorite-'.$id_quote.'" title="'.$add_favorite.'"><img src="http://teen-quotes.com/images/icones/heart.png" /></a></span>';
		}
	elseif($logged==true AND $is_favorite=='1')
		{
		echo '<span class="favorite"><a href="unfavorite-'.$id_quote.'" title="'.$unfavorite.'"><img src="http://teen-quotes.com/images/icones/broken_heart.gif" /></a></span>';
		}
	}

function share_fb_twitter ($id_quote,$txt_quote,$share) 
	{
	$domaine = "teen-quotes.com";
	$txt_tweet=cut_tweet($txt_quote);
	$url_encode = urlencode('http://'.$domaine.'/quote-'.$id_quote.'');
	echo '<div class="share_fb_twitter"><span class="fade_jquery"><iframe src="//www.facebook.com/plugins/like.php?href='.$url_encode.'&amp;send=false&amp;layout=button_count&amp;width=110&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:110px; height:21px;" allowTransparency="true"></iframe></span><span class="right fade_jquery"><a href="http://twitter.com/share?url=http://'.$domaine.'/quote-'.$id_quote.'&text='.$txt_tweet.'" class="twitter-share-button" data-count="none">Tweet</a></span></div>';
	}

function date_et_auteur ($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile) 
	{
	echo '<span class="right">'.$by.'<a href="user-'.$auteur_id.'" title="'.$view_his_profile.'"> '.$auteur.' </a>'.$on.' '.$date_quote.'</span><br>';
	}

function date_et_auteur_m ($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile) 
	{
	echo '<span class="right">'.$by.'<a href="user-'.$auteur_id.'" title="'.$view_his_profile.'"> '.$auteur.' </a>'.$on.' '.$date_quote.'</span><br>';
	}

function is_quote_exist ($txt_quote) 
	{
	$txt_quote_cut = cut_tweet($txt_quote);
	$quote_exist = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE texte_english like '%$txt_quote_cut%' AND approved='1'"));
	if ($quote_exist > '0')
		{
		return true;
		}
	else
		{
		return false;
		}
	}

function nl2br_to_textarea ($texte) 
	{
	$line_break = PHP_EOL;
	$patterns = array("/(<br>|<br \/>|<br\/>)\s*/i","/(\r\n|\r|\n)/");
	$replacements = array(PHP_EOL,$line_break);
	$string = preg_replace($patterns, $replacements, $texte);
	return $string;
	}

function RelativeTime_fr($time) {
        $timeDiff = time() - $time;

        if($timeDiff <= 0)
                return "moins d'une seconde";

        $timeDiff = abs($timeDiff);

        $times = array(
                31104000 => 'an{s}',       // 12 * 30 * 24 * 60 * 60 secondes
                2592000  => 'mois',        // 30 * 24 * 60 * 60 secondes
                604800   => 'semaine{s}',  // 7 * 24 * 60 * 60 secondes
                86400    => 'jour{s}',     // 24 * 60 * 60 secondes
                3600     => 'heure{s}',    // 60 * 60 secondes
                60       => 'minute{s}');

        $strTime = NULL;

        // Until that the rest can't being converted
        while($timeDiff >= 60) {
                foreach($times AS $seconds => $unit) {
                        $delta = floor($timeDiff / $seconds);

                        if($delta >= 1) {
                                $unit = str_replace('{s}', ($delta == 1 ? NULL : 's'), $unit);
                                $strTime .= "$delta $unit ";
                                $timeDiff -= $delta * $seconds;
                        }
                }
        }

        // If there is still seconds
        ($timeDiff > 0)
                && $strTime .= "et $timeDiff secondes";

        return trim($strTime);
}

function RelativeTime_en($time) {
        $timeDiff = time() - $time;

        if($timeDiff <= 0)
                return "moins d'une seconde";

        $timeDiff = abs($timeDiff);

        $times = array(
                31104000 => 'year{s}',       // 12 * 30 * 24 * 60 * 60 secondes
                2592000  => 'month{s}',        // 30 * 24 * 60 * 60 secondes
                604800   => 'week{s}',  // 7 * 24 * 60 * 60 secondes
                86400    => 'day{s}',     // 24 * 60 * 60 secondes
                3600     => 'hour{s}',    // 60 * 60 secondes
                60       => 'minute{s}');

        $strTime = NULL;

        // Until that the rest can't being converted
        while($timeDiff >= 60) {
                foreach($times AS $seconds => $unit) {
                        $delta = floor($timeDiff / $seconds);

                        if($delta >= 1) {
                                $unit = str_replace('{s}', ($delta == 1 ? NULL : 's'), $unit);
                                $strTime .= "$delta $unit ";
                                $timeDiff -= $delta * $seconds;
                        }
                }
        }

        // If there is still seconds
        ($timeDiff > 0)
                && $strTime .= "et $timeDiff secondes";

        return trim($strTime);
}

// MOBILE
if (isset($_GET['mobile'])) {
setcookie("mobile", 1 , time() + (((3600*24)*30)*12));
}
			
function mobile_device_detect($iphone=true,$android=true,$opera=true,$blackberry=true,$palm=true,$windows=true,$mobileredirect=true,$desktopredirect=false){

  $mobile_browser   = false; // set mobile browser as false till we can prove otherwise
  $user_agent       = $_SERVER['HTTP_USER_AGENT']; // get the user agent value - this should be cleaned to ensure no nefarious input gets executed
  $accept           = $_SERVER['HTTP_ACCEPT']; // get the content accept value - this should be cleaned to ensure no nefarious input gets executed
  $domaine = "teen-quotes.com";
  $iphone='http://m.'.$domaine.'';
  $android=$iphone;
  $opera=$iphone;
  $blackberry=$iphone;
  $palm=$iphone;
  $windows=$iphone;

  switch(true){ // using a switch against the following statements which could return true is more efficient than the previous method of using if statements

    case (mb_eregi('ipod',$user_agent)||mb_eregi('iphone',$user_agent)); // we find the words iphone or ipod in the user agent
      $mobile_browser = $iphone; // mobile browser is either true or false depending on the setting of iphone when calling the function
      $status = 'Apple';
      if(substr($iphone,0,4)=='http'){ // does the value of iphone resemble a url
        $mobileredirect = $iphone; // set the mobile redirect url to the url value stored in the iphone value
      } // ends the if for iphone being a url
    break; // break out and skip the rest if we've had a match on the iphone or ipod

    case (mb_eregi('android',$user_agent));  // we find android in the user agent
      $mobile_browser = $android; // mobile browser is either true or false depending on the setting of android when calling the function
      $status = 'Android';
      if(substr($android,0,4)=='http'){ // does the value of android resemble a url
        $mobileredirect = $android; // set the mobile redirect url to the url value stored in the android value
      } // ends the if for android being a url
    break; // break out and skip the rest if we've had a match on android

    case (mb_eregi('opera mini',$user_agent)); // we find opera mini in the user agent
      $mobile_browser = $opera; // mobile browser is either true or false depending on the setting of opera when calling the function
      $status = 'Opera';
      if(substr($opera,0,4)=='http'){ // does the value of opera resemble a rul
        $mobileredirect = $opera; // set the mobile redirect url to the url value stored in the opera value
      } // ends the if for opera being a url 
    break; // break out and skip the rest if we've had a match on opera

    case (mb_eregi('blackberry',$user_agent)); // we find blackberry in the user agent
      $mobile_browser = $blackberry; // mobile browser is either true or false depending on the setting of blackberry when calling the function
      $status = 'Blackberry';
      if(substr($blackberry,0,4)=='http'){ // does the value of blackberry resemble a rul
        $mobileredirect = $blackberry; // set the mobile redirect url to the url value stored in the blackberry value
      } // ends the if for blackberry being a url 
    break; // break out and skip the rest if we've had a match on blackberry

    case (preg_match('/(pre\/|palm os|palm|hiptop|avantgo|fennec|plucker|xiino|blazer|elaine)/i',$user_agent)); // we find palm os in the user agent - the i at the end makes it case insensitive
      $mobile_browser = $palm; // mobile browser is either true or false depending on the setting of palm when calling the function
      $status = 'Palm';
      if(substr($palm,0,4)=='http'){ // does the value of palm resemble a rul
        $mobileredirect = $palm; // set the mobile redirect url to the url value stored in the palm value
      } // ends the if for palm being a url 
    break; // break out and skip the rest if we've had a match on palm os

    case (preg_match('/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i',$user_agent)); // we find windows mobile in the user agent - the i at the end makes it case insensitive
      $mobile_browser = $windows; // mobile browser is either true or false depending on the setting of windows when calling the function
      $status = 'Windows Smartphone';
      if(substr($windows,0,4)=='http'){ // does the value of windows resemble a rul
        $mobileredirect = $windows; // set the mobile redirect url to the url value stored in the windows value
      } // ends the if for windows being a url 
    break; // break out and skip the rest if we've had a match on windows

    case (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i',$user_agent)); // check if any of the values listed create a match on the user agent - these are some of the most common terms used in agents to identify them as being mobile devices - the i at the end makes it case insensitive
      $mobile_browser = true; // set mobile browser to true
      $status = 'Mobile matched on piped preg_match';
    break; // break out and skip the rest if we've preg_match on the user agent returned true 

    case ((strpos($accept,'text/vnd.wap.wml')>0)||(strpos($accept,'application/vnd.wap.xhtml+xml')>0)); // is the device showing signs of support for text/vnd.wap.wml or application/vnd.wap.xhtml+xml
      $mobile_browser = true; // set mobile browser to true
      $status = 'Mobile matched on content accept header';
    break; // break out and skip the rest if we've had a match on the content accept headers

    case (isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE'])); // is the device giving us a HTTP_X_WAP_PROFILE or HTTP_PROFILE header - only mobile devices would do this
      $mobile_browser = true; // set mobile browser to true
      $status = 'Mobile matched on profile headers being set';
    break; // break out and skip the final step if we've had a return true on the mobile specfic headers

    case (in_array(strtolower(substr($user_agent,0,4)),array('1207'=>'1207','3gso'=>'3gso','4thp'=>'4thp','501i'=>'501i','502i'=>'502i','503i'=>'503i','504i'=>'504i','505i'=>'505i','506i'=>'506i','6310'=>'6310','6590'=>'6590','770s'=>'770s','802s'=>'802s','a wa'=>'a wa','acer'=>'acer','acs-'=>'acs-','airn'=>'airn','alav'=>'alav','asus'=>'asus','attw'=>'attw','au-m'=>'au-m','aur '=>'aur ','aus '=>'aus ','abac'=>'abac','acoo'=>'acoo','aiko'=>'aiko','alco'=>'alco','alca'=>'alca','amoi'=>'amoi','anex'=>'anex','anny'=>'anny','anyw'=>'anyw','aptu'=>'aptu','arch'=>'arch','argo'=>'argo','bell'=>'bell','bird'=>'bird','bw-n'=>'bw-n','bw-u'=>'bw-u','beck'=>'beck','benq'=>'benq','bilb'=>'bilb','blac'=>'blac','c55/'=>'c55/','cdm-'=>'cdm-','chtm'=>'chtm','capi'=>'capi','cond'=>'cond','craw'=>'craw','dall'=>'dall','dbte'=>'dbte','dc-s'=>'dc-s','dica'=>'dica','ds-d'=>'ds-d','ds12'=>'ds12','dait'=>'dait','devi'=>'devi','dmob'=>'dmob','doco'=>'doco','dopo'=>'dopo','el49'=>'el49','erk0'=>'erk0','esl8'=>'esl8','ez40'=>'ez40','ez60'=>'ez60','ez70'=>'ez70','ezos'=>'ezos','ezze'=>'ezze','elai'=>'elai','emul'=>'emul','eric'=>'eric','ezwa'=>'ezwa','fake'=>'fake','fly-'=>'fly-','fly_'=>'fly_','g-mo'=>'g-mo','g1 u'=>'g1 u','g560'=>'g560','gf-5'=>'gf-5','grun'=>'grun','gene'=>'gene','go.w'=>'go.w','good'=>'good','grad'=>'grad','hcit'=>'hcit','hd-m'=>'hd-m','hd-p'=>'hd-p','hd-t'=>'hd-t','hei-'=>'hei-','hp i'=>'hp i','hpip'=>'hpip','hs-c'=>'hs-c','htc '=>'htc ','htc-'=>'htc-','htca'=>'htca','htcg'=>'htcg','htcp'=>'htcp','htcs'=>'htcs','htct'=>'htct','htc_'=>'htc_','haie'=>'haie','hita'=>'hita','huaw'=>'huaw','hutc'=>'hutc','i-20'=>'i-20','i-go'=>'i-go','i-ma'=>'i-ma','i230'=>'i230','iac'=>'iac','iac-'=>'iac-','iac/'=>'iac/','ig01'=>'ig01','im1k'=>'im1k','inno'=>'inno','iris'=>'iris','jata'=>'jata','java'=>'java','kddi'=>'kddi','kgt'=>'kgt','kgt/'=>'kgt/','kpt '=>'kpt ','kwc-'=>'kwc-','klon'=>'klon','lexi'=>'lexi','lg g'=>'lg g','lg-a'=>'lg-a','lg-b'=>'lg-b','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-f'=>'lg-f','lg-g'=>'lg-g','lg-k'=>'lg-k','lg-l'=>'lg-l','lg-m'=>'lg-m','lg-o'=>'lg-o','lg-p'=>'lg-p','lg-s'=>'lg-s','lg-t'=>'lg-t','lg-u'=>'lg-u','lg-w'=>'lg-w','lg/k'=>'lg/k','lg/l'=>'lg/l','lg/u'=>'lg/u','lg50'=>'lg50','lg54'=>'lg54','lge-'=>'lge-','lge/'=>'lge/','lynx'=>'lynx','leno'=>'leno','m1-w'=>'m1-w','m3ga'=>'m3ga','m50/'=>'m50/','maui'=>'maui','mc01'=>'mc01','mc21'=>'mc21','mcca'=>'mcca','medi'=>'medi','meri'=>'meri','mio8'=>'mio8','mioa'=>'mioa','mo01'=>'mo01','mo02'=>'mo02','mode'=>'mode','modo'=>'modo','mot '=>'mot ','mot-'=>'mot-','mt50'=>'mt50','mtp1'=>'mtp1','mtv '=>'mtv ','mate'=>'mate','maxo'=>'maxo','merc'=>'merc','mits'=>'mits','mobi'=>'mobi','motv'=>'motv','mozz'=>'mozz','n100'=>'n100','n101'=>'n101','n102'=>'n102','n202'=>'n202','n203'=>'n203','n300'=>'n300','n302'=>'n302','n500'=>'n500','n502'=>'n502','n505'=>'n505','n700'=>'n700','n701'=>'n701','n710'=>'n710','nec-'=>'nec-','nem-'=>'nem-','newg'=>'newg','neon'=>'neon','netf'=>'netf','noki'=>'noki','nzph'=>'nzph','o2 x'=>'o2 x','o2-x'=>'o2-x','opwv'=>'opwv','owg1'=>'owg1','opti'=>'opti','oran'=>'oran','p800'=>'p800','pand'=>'pand','pg-1'=>'pg-1','pg-2'=>'pg-2','pg-3'=>'pg-3','pg-6'=>'pg-6','pg-8'=>'pg-8','pg-c'=>'pg-c','pg13'=>'pg13','phil'=>'phil','pn-2'=>'pn-2','pt-g'=>'pt-g','palm'=>'palm','pana'=>'pana','pire'=>'pire','pock'=>'pock','pose'=>'pose','psio'=>'psio','qa-a'=>'qa-a','qc-2'=>'qc-2','qc-3'=>'qc-3','qc-5'=>'qc-5','qc-7'=>'qc-7','qc07'=>'qc07','qc12'=>'qc12','qc21'=>'qc21','qc32'=>'qc32','qc60'=>'qc60','qci-'=>'qci-','qwap'=>'qwap','qtek'=>'qtek','r380'=>'r380','r600'=>'r600','raks'=>'raks','rim9'=>'rim9','rove'=>'rove','s55/'=>'s55/','sage'=>'sage','sams'=>'sams','sc01'=>'sc01','sch-'=>'sch-','scp-'=>'scp-','sdk/'=>'sdk/','se47'=>'se47','sec-'=>'sec-','sec0'=>'sec0','sec1'=>'sec1','semc'=>'semc','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','sk-0'=>'sk-0','sl45'=>'sl45','slid'=>'slid','smb3'=>'smb3','smt5'=>'smt5','sp01'=>'sp01','sph-'=>'sph-','spv '=>'spv ','spv-'=>'spv-','sy01'=>'sy01','samm'=>'samm','sany'=>'sany','sava'=>'sava','scoo'=>'scoo','send'=>'send','siem'=>'siem','smar'=>'smar','smit'=>'smit','soft'=>'soft','sony'=>'sony','t-mo'=>'t-mo','t218'=>'t218','t250'=>'t250','t600'=>'t600','t610'=>'t610','t618'=>'t618','tcl-'=>'tcl-','tdg-'=>'tdg-','telm'=>'telm','tim-'=>'tim-','ts70'=>'ts70','tsm-'=>'tsm-','tsm3'=>'tsm3','tsm5'=>'tsm5','tx-9'=>'tx-9','tagt'=>'tagt','talk'=>'talk','teli'=>'teli','topl'=>'topl','hiba'=>'hiba','up.b'=>'up.b','upg1'=>'upg1','utst'=>'utst','v400'=>'v400','v750'=>'v750','veri'=>'veri','vk-v'=>'vk-v','vk40'=>'vk40','vk50'=>'vk50','vk52'=>'vk52','vk53'=>'vk53','vm40'=>'vm40','vx98'=>'vx98','virg'=>'virg','vite'=>'vite','voda'=>'voda','vulc'=>'vulc','w3c '=>'w3c ','w3c-'=>'w3c-','wapj'=>'wapj','wapp'=>'wapp','wapu'=>'wapu','wapm'=>'wapm','wig '=>'wig ','wapi'=>'wapi','wapr'=>'wapr','wapv'=>'wapv','wapy'=>'wapy','wapa'=>'wapa','waps'=>'waps','wapt'=>'wapt','winc'=>'winc','winw'=>'winw','wonu'=>'wonu','x700'=>'x700','xda2'=>'xda2','xdag'=>'xdag','yas-'=>'yas-','your'=>'your','zte-'=>'zte-','zeto'=>'zeto','acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','aste'=>'aste','audi'=>'audi','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','brvw'=>'brvw','bumb'=>'bumb','ccwa'=>'ccwa','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eml2'=>'eml2','eric'=>'eric','fetc'=>'fetc','hipt'=>'hipt','http'=>'http','ibro'=>'ibro','idea'=>'idea','ikom'=>'ikom','inno'=>'inno','ipaq'=>'ipaq','jbro'=>'jbro','jemu'=>'jemu','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','kyoc'=>'kyoc','kyok'=>'kyok','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','libw'=>'libw','m-cr'=>'m-cr','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','mywa'=>'mywa','nec-'=>'nec-','newt'=>'newt','nok6'=>'nok6','noki'=>'noki','o2im'=>'o2im','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play','pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','rozo'=>'rozo','sage'=>'sage','sama'=>'sama','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','vx52'=>'vx52','vx53'=>'vx53','vx60'=>'vx60','vx61'=>'vx61','vx70'=>'vx70','vx80'=>'vx80','vx81'=>'vx81','vx83'=>'vx83','vx85'=>'vx85','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','whit'=>'whit','winw'=>'winw','wmlb'=>'wmlb','xda-'=>'xda-',))); // check against a list of trimmed user agents to see if we find a match
      $mobile_browser = true; // set mobile browser to true
      $status = 'Mobile matched on in_array';
    break; // break even though it's the last statement in the switch so there's nothing to break away from but it seems better to include it than exclude it

    default;
      $mobile_browser = false; // set mobile browser to false
      $status = 'Desktop / full capability browser';
    break; // break even though it's the last statement in the switch so there's nothing to break away from but it seems better to include it than exclude it

  } // ends the switch 

  // tell adaptation services (transcoders and proxies) to not alter the content based on user agent as it's already being managed by this script
//  header('Cache-Control: no-transform'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies
//  header('Vary: User-Agent, Accept'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies

  // if redirect (either the value of the mobile or desktop redirect depending on the value of $mobile_browser) is true redirect else we return the status of $mobile_browser
  if($redirect = ($mobile_browser==true) ? $mobileredirect : $desktopredirect){
    header('Location: '.$redirect); // redirect to the right url for this device
    exit;
  }else{ 
    return $mobile_browser; // will return either true or false 
  }

} // ends function mobile_device_detect



if (empty($_COOKIE['mobile']) AND $m_url != 'http://m.teen-quotes.com/' AND !isset($_GET['mobile']))
	{
	mobile_device_detect();
	}


?>