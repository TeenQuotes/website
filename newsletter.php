<?php 
include 'header.php';

$email = mysql_real_escape_string($_GET['email']);
$code = mysql_real_escape_string($_GET['code']);
$action = htmlspecialchars($_GET['action']);

include 'lang/'.$language.'/newsletter.php';
include 'lang/'.$language.'/signup.php';
include 'lang/'.$language.'/edit_profile.php';

echo '
<div class="post">
<h1><img src="http://'.$domaine.'/images/icones/mail.png" class="icone" />'.$newsletter.'</h1>
';
if (empty($action)) 
{ 
	echo '
	<div class="div_pre_form">
		'.$texte_newsletter.'
	</div>

	<div class="grey_post">
		<form action="?action=send" method="post">
			<div class="colonne-gauche">Email </div><div class="colonne-milieu"><input type="text" name="email" class="signup"/></div><div class="colonne-droite"><span class="min_info">'.$valid_email.'</span></div>
			<div class="clear"></div>
			<br/><br/>
			<input type="checkbox" id="input_newsletter" name="newsletter" value="1"/><label for="input_newsletter">'.$i_want_newsletter.'</label><br/>
			<input type="checkbox" id="input_email_quote_today" name="email_quote_today" value="1"/><label for="input_email_quote_today">'.$i_want_email_quote_today.'</label><br/> 
			<br/><br/>
			<center><p><input type="submit" value="'.$inscription_newsletter.'" class="submit" /></p></center>
		</form>
	</div>';
}
	
elseif ($action == "send") // SUBSCRIBE
{ 
	$email = htmlspecialchars($_POST['email']);
	$newsletter = htmlspecialchars($_POST['newsletter']);
	$email_quote_today = htmlspecialchars($_POST['email_quote_today']);

	$email_quote_today_num_rows = mysql_num_rows(mysql_query("SELECT id FROM newsletters WHERE email = '".$email."' AND type = 'daily'"));
	$is_newsletter = mysql_num_rows(mysql_query("SELECT id FROM newsletters WHERE email = '".$email."' AND type = 'weekly'"));

	if (!empty($email) AND preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
	{
		// WEEKLY NEWSLETTER
		if ($newsletter == '1' AND $is_newsletter == 0)
		{
			$code = caracteresAleatoires(5);
			$query = mysql_query("INSERT INTO newsletters (email, code_unsubscribe, type) VALUES ('".$email."','".$code."', 'weekly')");
			if ($query) 
			{
				echo $succes_newsletter;
				$notifications_succes = TRUE;
			}
			else 
			{
				echo $error.' '.$lien_retour;
			}	
		}
		elseif ($is_newsletter == 1) 
		{
			echo $already_subscribe.$lien_retour;
		}
			
		// DAILY NEWSLETTER
		if ($email_quote_today == '1' AND $email_quote_today_num_rows == 0)
		{
			$code = caracteresAleatoires(5);
			$query = mysql_query("INSERT INTO newsletters (email, code_unsubscribe, type) VALUES ('".$email."', '".$code."', 'daily')");
			if ($query) 
			{
				if ($notifications_succes != TRUE)
				{
					echo $succes_newsletter;
					$notifications_succes = TRUE;
				}
			}
			else 
			{
				echo $error.' '.$lien_retour;
			}
		}
		elseif ($email_quote_today_num_rows == 1)
		{
			echo $already_subscribe.$lien_retour;
		}
	}
	else 
	{
			echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour;
	}
}	
elseif ($action == "unsubscribe")  // DESINSCRIPTION
{
	if (!empty($email) AND !empty($code) AND preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
	{
		$num_rows = mysql_num_rows(mysql_query("SELECT id FROM newsletters WHERE email = '".$email."' AND code_unsubscribe = '".$code."' AND type = 'weekly'")); 
		if ($num_rows == 1) 
		{
			$query = mysql_query("DELETE FROM newsletters WHERE email = '".$email."' AND code_unsubscribe = '".$code."' AND type = 'weekly'");
			if ($query) 
			{
				echo $succes_unsubscribe;
			}
			else 
			{
				echo $error;
			}
		}
		else
		{
			echo $not_subscribe;
		}
	}
	else 
	{
		echo $error;
	}
}	
elseif ($action == "unsubscribe_everyday")  // DESINSCRIPTION
{
	if (!empty($email) AND !empty($code) AND preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
	{
		$num_rows = mysql_num_rows(mysql_query("SELECT id FROM newsletters WHERE email = '".$email."' AND code_unsubscribe = '".$code."' AND type = 'daily'")); 

		if ($num_rows == 1) 
		{
			$query = mysql_query("DELETE FROM newsletters WHERE email = '".$email."' AND code_unsubscribe = '".$code."' AND type = 'daily'");
			if ($query) 
			{
				echo $succes_unsubscribe_everyday;
			}
			else 
			{
				echo $error;
			}
		}
		else
		{
			echo $not_subscribe;
		}
	}
	else 
	{
		echo $error;
	}
}	
echo '</div>';
include "footer.php";
?>