<?php 

require "kernel/config.php";
require "kernel/fonctions.php";
$db = mysql_connect("$host", "$user", "$pass")  or die('Erreur de connexion '.mysql_error());
mysql_select_db('teenq208598',$db)  or die('Erreur de selection '.mysql_error()); 

$result = mysql_fetch_array(mysql_query("SELECT id,texte_english FROM teen_quotes_quotes WHERE approved='1' ORDER BY RAND() LIMIT 1"));

$id_quote = $result['id'];
$texte_quote= urlencode($result['texte_english']);

$url='http://clockface.fr/tq.php?txt='.$texte_quote.'&id='.$id_quote.'';


$content = file_get_contents('http://clockface.fr/tq.php?txt='.$texte_quote.'&id='.$id_quote.'');

echo $url;
echo "<hr />";
echo $content;