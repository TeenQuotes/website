<?php 
include 'header.php';
include 'lang/'.$language.'/newsletter.php';
include 'lang/'.$language.'/signup.php';
$action = htmlspecialchars($_GET['action']);

echo '
<div class="post">
<h1><img src="http://'.$domaine.'/images/icones/mail.png" class="icone" />Newsletter</h1>
';
if (empty($action)) 
{ 
	echo $texte_newsletter; 
	echo '<div class="grey_post">';
		echo '<form action="?action=send" method="post">';
		
		if ($_SESSION['logged'] != TRUE) 
		{ 
			echo '<div class="colonne-gauche">Email </div><div class="colonne-milieu"><input type="text" name="email" class="signup"/></div><div class="colonne-droite"><span class="min_info">'.$valid_email.'</span></div>
			<br /><br />';
		}
		else
		{
			echo '<input type="hidden" name="email" value="'.$email.'" class="signup"/>';
		} 

		echo '<center><p><input type="submit" value="'.$inscription_newsletter.'" class="submit" /></p></center>
		</form>';
	echo '</div>';
	
	// NEWSLETTER EVERYDAY
	echo $texte_newsletter_everyday;
	echo '<div class="grey_post">';
		echo '<form action="?action=send_everyday" method="post">';
		if ($_SESSION['logged'] != TRUE) 
		{ 
			echo '<div class="colonne-gauche">Email </div><div class="colonne-milieu"><input type="text" name="email" class="signup"/></div><div class="colonne-droite"><span class="min_info">'.$valid_email.'</span></div>
			<br /><br />';
		}
		else
		{
			echo '<input type="hidden" name="email" value="'.$email.'" class="signup"/>';
		} 

		echo '<center><p><input type="submit" value="'.$inscription_newsletter.'" class="submit" /></p></center>
		</form>';
	echo '</div>';
}
	
elseif ($action == "send") // INSCRIPTION
{ 
	$email = htmlspecialchars($_POST['email']);
	if (!empty($email) AND preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
	{
		$num_rows = mysql_num_rows(mysql_query("SELECT id FROM newsletter where email='$email'")); 
		if ($num_rows == 0) 
			{
			$code = caracteresAleatoires(5);
			$query = mysql_query("INSERT INTO newsletter (email,code) VALUES ('$email','$code')");
			if ($query) 
			{
				echo ''.$succes_newsletter.'';
			}
			else 
			{
				echo ''.$error.' '.$lien_retour.'';
			}
		}
		else
		{
			echo ''.$already_subscribe.' '.$lien_retour.'';
		}
	}
	else 
	{
		echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour.'';
	}
}	
elseif ($action == "send_everyday") // INSCRIPTION EVERYDAY
{ 
	$email = htmlspecialchars($_POST['email']);
	if (!empty($email) AND preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
	{
		$num_rows = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_settings WHERE param = 'email_quote_today' AND value = '$email'")); 
		if ($num_rows == 0) 
		{
			$query = mysql_query("INSERT INTO teen_quotes_settings (param,value) VALUES ('email_quote_today','$email')");

			if ($query) 
			{
				echo ''.$succes_newsletter_everyday.'';
			}
			else 
			{
				echo ''.$error.' '.$lien_retour.'';
			}
		}
		else
		{
			echo ''.$already_subscribe.' '.$lien_retour.'';
		}
	}
	else 
	{
		echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour.'';
	}
}	
elseif ($action == "unsuscribe")  // DESINSCRIPTION
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
				echo ''.$succes_unsuscribe.'';
			}
			else 
			{
				echo ''.$error.'';
			}
		}
		else
		{
			echo ''.$not_subscribe.'';
		}
	}
	else 
	{
		echo ''.$error.'';
	}
}	
elseif ($action == "unsuscribe_everyday")  // DESINSCRIPTION
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
				echo ''.$succes_unsuscribe_everyday.'';
			}
			else 
			{
				echo ''.$error.'';
			}
		}
		else
		{
			echo ''.$not_subscribe.'';
		}
	}
	else 
	{
		echo ''.$error.'';
	}
}	
echo '</div>';
include "footer.php";
?>