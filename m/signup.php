<?php 
include 'header.php';
$action = $_GET['action'];

if ($action == 'send')
{
	$username = strtolower(trim(htmlspecialchars(mysql_real_escape_string($_POST['username']))));
	$username = str_replace(' ','', $username);
	$pass1 = htmlspecialchars(mysql_real_escape_string($_POST['pass1']));
	$pass2 = htmlspecialchars(mysql_real_escape_string($_POST['pass2']));
	$pass_salt = sha1(strtoupper($username).':'.strtoupper($pass1));
	$email = htmlspecialchars(mysql_real_escape_string($_POST['email']));
	$email_quote_today = htmlspecialchars(mysql_real_escape_string($_POST['email_quote_today']));
	$newsletter_checkbox = htmlspecialchars(mysql_real_escape_string($_POST['newsletter']));
	$ip = $_SERVER["REMOTE_ADDR"];
}
	
include '../lang/'.$language.'/signup.php';
include '../lang/'.$language.'/edit_profile.php';
include '../lang/'.$language.'/newsletter.php';
// FORMULAIRE
if (empty($action)) 
{
	echo '
	<div class="post slidedown">
		<h2><img src="http://'.$domain.'/images/icones/signin.png" class="icone" alt="Icone"/>'.$sign_up.'</h2>
	';
	if (isset($_GET['addquote'])) 
	{
		echo $must_be_registered_for_quote;
		$query = mysql_query("UPDATE teen_quotes_settings SET value = value+1 WHERE param = 'signup_addquote'");
	} 
	elseif (isset($_GET['addcomment'])) 
	{
		echo $must_be_registered_to_comment;
		$query = mysql_query("UPDATE teen_quotes_settings SET value = value+1 WHERE param = 'signup_addcomment'");
	}
	elseif (isset($_GET['topbar'])) 
	{
		$query = mysql_query("UPDATE teen_quotes_settings SET value = value+1 WHERE param = 'signup_topbar'");
	}
	elseif (isset($_GET['menuright'])) 
	{
		$query = mysql_query("UPDATE teen_quotes_settings SET value = value+1 WHERE param = 'signup_menuright'");
	}
	else
	{
		$query = mysql_query("UPDATE teen_quotes_settings SET value = value+1 WHERE param = 'signup_empty'");
	}
	
	echo '
	'.$account_create.'<br/>
	<br/>
		<div class="grey_post">
			<form method="post" action="signup.php?action=send"> 
				'.$username_enter.'<br/>
				<input type="text" name="username" class="signup"/><br/>
				<span class="min_info">Minimum 5 '.$characters.'. '.$username_shape.'</span>
				<br/><br/>
				'.$password.'<br/>
				<input type="password" name="pass1" class="signup"/><br/>
				<span class="min_info">Minimum 6 '.$characters.'.</span>
				<br/><br/>
				'.$confirm_password.'<br/>
				<input type="password" name="pass2" class="signup"/><br/>
				<span class="min_info">'.$reenter_pass.'.</span>
				<br/><br/>
				Email<br/>
				<input type="email" name="email" class="signup"/><br/>
				<span class="min_info">'.$valid_email.'.</span>
				<br/><br/>
				<input type="checkbox" name="newsletter" value="1" checked="checked" /> '.$i_want_newsletter.'<br/>
				<input type="checkbox" name="email_quote_today" value="1" /> '.$i_want_email_quote_today.'<br/>
				<br/>
				<center><p><input type="submit" value="'.$create_account.'" class="submit" /></p></center>
			</form>
		</div>
	</div>
	';
}
elseif ($action == "send") 
{
	echo '
	<div class="post">
	<h2><img src="http://'.$domain.'/images/icones/signin.png" class="icone"/>'.$sign_up.'</h2>
	';
	
	$code = caracteresAleatoires(5);
	
	if (strlen(trim($username)) >= 5) 
	{
		if (preg_match("#^[a-z0-9_]+$#", $username)) 
		{
			$test = mysql_num_rows(mysql_query("SELECT * from teen_quotes_account WHERE username = '$username'"));
			if ($test == 0) 
			{
				if ($pass1 == $pass2)
				{
					if (strlen($pass1) >= 6)
					{
						// Pass salt
						if (strlen($email) >= 6)
						{          
							if (preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email))
							{
								$test = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_account WHERE email = '$email'"));
								if ($test == 0)
								{
									$add = mysql_query("INSERT INTO teen_quotes_account (username, pass, email, ip, security_level, location_signup) VALUES ('$username', '$pass_salt', '$email', '$ip', '0', 'mobile')");
									if ($newsletter_checkbox == '1')
									{
										$query_newsletter = mysql_query("INSERT INTO newsletters (email, code_unsubscribe, type) VALUES ('".$email."', '".$code."', 'weekly')");
									}
									if ($email_quote_today == '1')
									{
										$code = caracteresAleatoires(5);
										$query = mysql_query("INSERT INTO newsletters (email, code_unsubscribe, type) VALUES ('".$email."', '".$code."', 'daily')");
									}
									$message = ''.$email_message;
									$mail = mail($email, $email_subject, $message, $headers);
									if ($add)
									{
										echo $signup_succes;
										echo '<meta http-equiv="refresh" content="10;url=connexion.php?method=get&pseudo='.$username.'&password='.$pass_salt.'" />';
									}
									else
									{
										echo $error;
									}
								}
								else
								{
									echo '<span class="erreur">'.$email_taken.'</span>'.$lien_retour;
								}		
							}
							else
							{
								echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour;
							}
						}
						else
						{
							echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour;
						}
					}
					else
					{
						echo '<span class="erreur">'.$password_short.'</span>'.$lien_retour;
					}
				}
				else
				{
					echo '<span class="erreur">'.$password_not_same.'</span>'.$lien_retour;
				}
			}
			else
			{
				echo '<span class="erreur">'.$username_taken.'</span>'.$lien_retour;
			}
		}
		else
		{
			echo '<span class="erreur">'.$username_not_valid.'</span>'.$lien_retour;
		}
	}
	else
	{
		echo '<span class="erreur">'.$username_short.'</span>'.$lien_retour;
	}

	echo '</div>';
}
include "footer.php";