<?php 
header("Access-Control-Allow-Origin: *");
require "../kernel/config.php";
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db('teenq208598',$db)  or die('Erreur de selection '.mysql_error()); 
require "../kernel/fonctions.php";

$approve = mysql_real_escape_string($_POST['approve']);
$id_quote=mysql_real_escape_string($_POST['id_quote']);
$auteur_id = mysql_real_escape_string($_POST['id_user']);

if (preg_match('/teen-quotes.com/', $_SERVER['SERVER_NAME'])) 
	{
	if ($approve=="yes") 
		{
		$approve_quote= mysql_query("UPDATE teen_quotes_quotes set approved='2' WHERE id='$id_quote'");
		
		if ($approve_quote) 
			{
			echo ''.$succes.' The author has been notified successfully !';
			}
		}
	else
		{
		$query_texte_quote=mysql_fetch_array(mysql_query("SELECT texte_english,date FROM teen_quotes_quotes WHERE id='$id_quote'"));
		$texte_quote=$query_texte_quote['texte_english'];
		$date_quote = $query_texte_quote['date'];

		$delete_quote= mysql_query("UPDATE teen_quotes_quotes set approved='-1' WHERE id='$id_quote'");

		$query_email_auteur=mysql_fetch_array(mysql_query("SELECT email,username FROM teen_quotes_account WHERE id='$auteur_id'"));
		$email_auteur=$query_email_auteur['email'];
		$name_auteur=ucfirst($query_email_auteur['username']);
						
		if ($delete_quote AND !empty($email_auteur)) 
			{
			$message = ''.$top_mail.' Hello <font color="#5C9FC0"><b>'.$name_auteur.'</b></font> !<br><br />Your quote has been <font color="#5C9FC0"><b>rejected</b></font> recently by a member of our team...<br><div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br><br /><a href="http://www.teen-quotes.com" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://www.teen-quotes.com/user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> on '.$date_quote.'</span></div>Sincerely,<br><b>The Teen Quotes Team</b><br /><br /><br /><div style="border-top:1px dashed #CCCCCC"></div><br /><br />VERSION FRANCAISE :<br /><br />Bonjour <font color="#5C9FC0"><b>'.$name_auteur.'</b></font> !<br><br />Votre citation a été <font color="#5C9FC0"><b>rejetée</b></font> récemment par un membre de notre équipe...<br><div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br><br /><a href="http://www.teen-quotes.com" target="_blank">#'.$id_quote.'</a><span style="float:right">par <a href="http://www.teen-quotes.com/user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> le '.$date_quote.'</span></div>Cordialement,<br><b>The Teen Quotes Team</b> '.$end_mail.'';
			$mail = mail($email_auteur, "Quote rejected", $message, $headers); 
			echo ''.$succes.' The author has been notified successfully !';
			}
													
		}
	}		
?>