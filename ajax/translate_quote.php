<?php 
header("Access-Control-Allow-Origin: *");
require "../kernel/config.php";
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user,$db)  or die('Erreur de selection '.mysql_error()); 
require "../kernel/fonctions.php";

$search = array('&#39;','á');
$replace = array("\'", 'à');


$id_quote = mysql_real_escape_string($_POST['id_quote']);
$texte_quote_translate = html_entity_decode(mysql_real_escape_string($_POST['texte_quote_translate']));
$texte_quote_translate = str_replace($search, $replace, $texte_quote_translate);
$language_source = mysql_real_escape_string($_POST['language_source']);
$language_translate = mysql_real_escape_string($_POST['language_translate']);
$second_language = $language_translate;


if (preg_match('/'.$domaine_fr.'/', $_SERVER['SERVER_NAME']) OR preg_match('/'.$domaine_en.'/', $_SERVER['SERVER_NAME'])) 
	{
	$previous_translate_query = mysql_fetch_array(mysql_query("SELECT texte_".$language_translate." as txt FROM teen_quotes_quotes WHERE id = '".$id_quote."'"));
	$previous_translate = $previous_translate_query['txt'];
	
	if (!empty($texte_quote_translate) AND !empty($id_quote) AND $previous_translate == '')
		{
		$update = mysql_query("UPDATE teen_quotes_quotes SET texte_".$language_translate." = '".$texte_quote_translate."' WHERE id = '".$id_quote."'");
		
		if ($update)
			{
			$donnees = mysql_fetch_array(mysql_query("SELECT id, texte_".$language." AS txt FROM teen_quotes_quotes WHERE texte_".$second_language."= '' AND approved = '1' ORDER BY RAND() LIMIT 0, 1"));
			$texte_quote = $donnees['txt'];
			$id_quote = $donnees['id'];
			
			echo '
			'.$succes.' Quote translated successfully.
			<form name="contact" action="">  
				<div class="grey_post">
				<b>#'.$id_quote.'</b> '.$texte_quote.'
				</div>
				<input type="hidden" name="id_quote" id="id_quote" value="'.$id_quote.'">
				<input type="hidden" name="language_source" id="language_source" value="'.$language.'">
				<input type="hidden" name="language_translate" id="language_translate" value="'.$second_language.'">
				
				<div class="grey_post">
				<textarea name="texte_quote_translate" id="texte_quote_translate" style="height:50px;width:600px;"></textarea>
				</div>
				<center><input type="submit" class="submit" id="submit_translate"></center>
			</form>';
			}
		else
			{
			echo 'Error';
			echo "1<br>
			$id_quote <br>
			$texte_quote_translate <br>
			$language_source <br>
			$language_translate <br>";
			}
		}
	else
		{
		echo 'Error';
		echo "
			2<br>
			$id_quote <br>
			$previous_translate <br>
			$texte_quote_translate <br>
			$language_source <br>
			$language_translate <br>";
		}
	}		
?>