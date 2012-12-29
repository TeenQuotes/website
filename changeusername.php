<?php
include 'header.php';
$action = htmlspecialchars($_GET['action']);
if ($action == 'send')
{	
	$username_old = mysql_real_escape_string($_POST['username_old']);
	$new_username = mysql_real_escape_string($_POST['username']);
	$pass1 = htmlspecialchars(mysql_real_escape_string($_POST['pass1']));
	$pass2 = htmlspecialchars(mysql_real_escape_string($_POST['pass2']));
	$session_id = $_SESSION['id'];
}
include 'lang/'.$language.'/signup.php';
include 'lang/'.$language.'/changeusername.php';


if ($_SESSION['logged'])
{
	echo '
	<div class="post">
		<h2><img src="http://'.$domaine.'/images/icones/refresh.png" class="icone" />'.$change_username.'</h2>
		';
	if (empty($action))
	{
		if (usernameIsValid($_SESSION['username']) == FALSE)
		{
			echo ''.$text_change_username.'';
			echo '
			<div class="grey_post">
				<form method="post" action="?action=send">
					<input type="hidden" value="'.$_SESSION['username'].'" name="username_old" />
					<div class="colonne-gauche">'.$username_enter.' </div><div class="colonne-milieu"><input type="text" name="username" class="signup"/></div><div class="colonne-droite"><span class="min_info">Minimum 5 '.$characters.'. '.$username_shape.'</span></div>
					<br/><br/>
					<div class="colonne-gauche">'.$password.' </div><div class="colonne-milieu"><input type="password" name="pass1" class="signup"/></div><div class="colonne-droite"><span class="min_info">Minimum 6 '.$characters.'.</span></div>
					<br/><br/>
					<div class="colonne-gauche">'.$confirm_password.' </div><div class="colonne-milieu"><input type="password" name="pass2" class="signup"/></div><div class="colonne-droite"><span class="min_info">'.$reenter_pass.'.</span></div>
					<br/><br/>
					<center><p><input type="submit" value="Okay" class="submit" /></p></center>
				</form>
			</div>
			';
		}
		else
		{	
			echo '<span class="erreur">'.$username_is_valid.'</span>'.$lien_retour.'';
		}
	}
	elseif ($action == 'send')
	{
		if (!empty($username_old) AND !empty($new_username))
		{
			if ((usernameIsValid($username_old) == FALSE) AND (usernameIsValid($new_username) == TRUE))
			{
				if(($pass1 == $pass2) AND (strlen($pass1) >= '6'))
				{
					$passwd = sha1(strtoupper($new_username).':'.strtoupper($pass1));
					$sql = "UPDATE  `teen_quotes_account` SET  `username` =  '$new_username', `pass` = '$passwd' WHERE  `teen_quotes_account`.`id` = '$session_id' LIMIT 1;";
					$update_account = mysql_query($sql)  or die('Erreur SQL !'.$sql.'<br/>'.mysql_error()); ;
					$update_comment = mysql_query("UPDATE teen_quotes_comments SET auteur = '$new_username' WHERE auteur_id = '$session_id'");
					$update_quote = mysql_query("UPDATE teen_quotes_quotes SET auteur = '$new_username' WHERE auteur_id = '$session_id'");

					if ($update_account AND $update_comment AND $update_quote)
					{	
						echo ''.$change_username_succes.'';
						echo '<meta http-equiv="refresh" content="5;url=index.php?deconnexion" />';
					}
					else
					{	
						echo ''.$error.' '.$lien_retour.'';
					}
				}
				else
				{	
					echo '<span class="erreur">'.$password_not_same.' - '.$password_short.'</span>'.$lien_retour.'';
				}
			}
			else
			{
				echo '<span class="erreur">'.$username_not_valid.'</span>'.$lien_retour.'';
			}
		}
		else
		{	
			echo '<span class="erreur">'.$erreur_empty.'</span> '.$lien_retour.'';
		}
	}
	echo '</div>';
}
else
{	
	echo ''.$error.' : you must be logged. '.$lien_retour.'';
}
include "footer.php";
?>