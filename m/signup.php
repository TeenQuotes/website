<?php 
include 'header.php';
include '../lang/'.$language.'/signup.php';
include '../lang/'.$language.'/edit_profile.php';
include '../lang/'.$language.'/newsletter.php';
$action = $_GET['action'];
// FORMULAIRE
if (empty($action)) 
	{
	echo '
	<div class="post">
	<h2><img src="http://www.teen-quotes.com/images/icones/signin.png" class="icone"/>'.$sign_up.'</h2>
	'.$account_create.'<br>
	<br />
	'.$require_age.'<br>
	<br />
		<div class="grey_post">
			<form method="post" action="signup.php?action=send"> 
				'.$username_enter.'<br>
				<input type="text" name="username" class="signup"/><br>
				<span class="min_info">Minimum 5 '.$characters.'</span>
				<br /><br />
				'.$password.'<br>
				<input type="password" name="pass1" class="signup"/><br>
				<span class="min_info">Minimum 6 '.$characters.'</span>
				<br /><br />
				'.$confirm_password.'<br>
				<input type="password" name="pass2" class="signup"/><br>
				<span class="min_info">'.$reenter_pass.'</span>
				<br /><br />
				Email<br>
				<input type="text" name="email" class="signup"/><br>
				<span class="min_info">'.$valid_email.'</span>
				<br /><br />
				<input type="checkbox" name="newsletter" value="1" checked="checked" /> '.$i_want_newsletter.'<br>
				<input type="checkbox" name="email_quote_today" value="1" /> '.$i_want_email_quote_today.'<br>
				<br />
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
	<h2><img src="http://'.$domaine.'/images/icones/signin.png" class="icone"/>'.$sign_up.'</h2>
	';
 
	$username = ucfirst(trim(htmlspecialchars(mysql_escape_string($_POST['username']))));
	$username=str_replace(' ','',$username);
	$pass1 = htmlspecialchars(mysql_escape_string($_POST['pass1']));
	$pass2 = htmlspecialchars(mysql_escape_string($_POST['pass2']));
	$email = htmlspecialchars(mysql_escape_string($_POST['email']));
	$ip=$_SERVER["REMOTE_ADDR"];
	$confmail="0";
	$timestamp_expire = time() + 3600;
	$code = caracteresAleatoires(5);
	
	if (strlen(trim($username)) >= '5') 
		{
		if (preg_match('#[\w]{5,15}#', $username)) 
			{
			$test = mysql_num_rows(mysql_query("select * from teen_quotes_account WHERE username='$username'"));
			if($test == '0') 
				{
				if( $pass1 == $pass2 )
					{
					if(strlen($pass1) >= '6')
					{
					$pass = sha1(strtoupper($username).':'.strtoupper($pass1));
					if(strlen($email) >= '6')
						{          
						if(preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email))
							{
							$test = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_account WHERE email='$email'"));
							if($test == '0')
								{
								$add = mysql_query("INSERT INTO teen_quotes_account (username,pass,email,ip,security_level) values('$username','$pass', '$email', '$ip','0')");
								$query_newsletter =mysql_query("INSERT INTO newsletter (email,code) VALUES ('$email','$code')");
								$message = ''.$email_message.'';
								$mail = mail($email, $email_subject, $message, $headers);
								if($add)
									{
									echo ''.$signup_succes.'';
									echo '<meta http-equiv="refresh" content="10;url=connexion.php?method=get&pseudo='.$username.'&password='.$pass.'" />';
									}
								else
									{
									echo ''.$error.'';
									}
								}
							else
								{
								echo '<span class="erreur">'.$email_taken.'</span>'.$lien_retour.'';
								}		
							}
						else
							{
							echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour.'';
							}
						}
					else
						{
						echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour.'';
						}
					}
					else
						{
						echo '<span class="erreur">'.$password_short.'</span>'.$lien_retour.'';
						}
					}
				else
					{
					echo '<span class="erreur">'.$password_not_same.'</span>'.$lien_retour.'';
					}
				}
			else
				{
				echo '<span class="erreur">'.$username_taken.'</span>'.$lien_retour.'';
				}
			}
		else
			{
			echo '<span class="erreur">'.$username_not_valid.'</span>'.$lien_retour.'';
			}
		}
	else
		{
		echo '<span class="erreur">'.$username_short.'</span>'.$lien_retour.'';
		}

	echo '</div>';
	}
include "footer.php";