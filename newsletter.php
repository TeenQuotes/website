<?php 
include 'header.php';
include 'lang/'.$language.'/newsletter.php';
include 'lang/'.$language.'/signup.php';
include 'lang/'.$language.'/edit_profile.php';
$action = htmlspecialchars($_GET['action']);

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

	$email_quote_today_num_rows = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_settings WHERE param = 'email_quote_today' AND value = '$email'"));
	$is_newsletter = mysql_num_rows(mysql_query("SELECT id FROM newsletter where email = '$email'"));

	if (!empty($email) AND preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
	{
		// WEEKLY NEWSLETTER
		if ($newsletter == '1' AND $is_newsletter == 0)
		{
			$code = caracteresAleatoires(5);
			$query = mysql_query("INSERT INTO newsletter (email,code) VALUES ('$email','$code')");
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
			$query = mysql_query("INSERT INTO teen_quotes_settings (param,value) VALUES ('email_quote_today','$email')");
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
	$email = htmlspecialchars($_GET['email']);
	$code = htmlspecialchars($_GET['code']);

	if (!empty($email) AND preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
	{
		$num_rows = mysql_num_rows(mysql_query("SELECT id FROM newsletter WHERE email='$email' AND code='$code'")); 
		if ($num_rows == 1) 
		{
			$query = mysql_query("DELETE FROM newsletter WHERE email='$email'");
			if ($query) 
			{
				echo $succes_unsuscribe;
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
	$email = htmlspecialchars($_GET['email']);

	if (!empty($email) AND preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
	{
		$num_rows = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_settings WHERE param = 'email_quote_today' AND value = '$email'")); 

		if ($num_rows == 1) 
		{
			$query = mysql_query("DELETE FROM teen_quotes_settings WHERE param = 'email_quote_today' AND value = '$email'");
			if ($query) 
			{
				echo $succes_unsuscribe_everyday;
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