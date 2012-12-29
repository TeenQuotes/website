<?php
$pseudo = mysql_real_escape_string($_POST['pseudo']);
$password = mysql_real_escape_string($_POST['pass']);

$method = htmlspecialchars($_GET['method']);

if ($method == 'get')
{
	$pseudo = mysql_real_escape_string($_GET['pseudo']);
	$password = mysql_real_escape_string($_GET['password']);
}

if (isset($_POST['connexion']) OR $method == 'get')
{
	if (!empty($pseudo) AND !empty($password))
	{
		$query_base = mysql_query("SELECT * FROM teen_quotes_account WHERE `username` ='$pseudo'");
		$retour_nb_pseudo = mysql_num_rows($query_base );

		// Check if the username exists
		if ($retour_nb_pseudo == 1)
		{				
			if ($method == 'get')
			{
				$passwd = $password;
			}
			else
			{
				$passwd = sha1(strtoupper($pseudo).':'.strtoupper($password));
			}
				
			$sha = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE `pass` = '$passwd' AND `username` = '$pseudo'"));
			if ($sha == 1)
			{
				$compte = mysql_fetch_array($query_base);

				if (empty($compte['birth_date']) AND empty($compte['title']) AND empty($compte['country']) AND empty($compte['about_me']) AND $compte['avatar'] == "icon50.png" AND empty($compte['city']))
				{
					$_SESSION['profile_not_fullfilled'] = TRUE;
				}
				
				// Store session values
				$_SESSION['logged'] = TRUE;
				$_SESSION['id'] = $compte['id'];										
				$_SESSION['security_level'] = $compte['security_level'];
				$_SESSION['email'] = $compte['email'];
				$_SESSION['avatar'] = $compte['avatar'];
				// Session passwd will be stored in a cookie after
				$_SESSION['passwd'] = $passwd;
				$_SESSION['username'] = $compte['username'];

				// Force the user to rename if he hasn't a valid username
				if (usernameIsValid(strtolower($_SESSION['username'])) == FALSE)
				{
					echo '<meta http-equiv="refresh" content="0; url=changeusername">';
				}
				else
				{
				
				// Redirect
				// We redirect to ../?co because we will store the $_SESSION['passwd'] in $_COOKIE['Pass']
				?>
				<script language="JavaScript">
				<!--
				window.location.href="../?co"
				//-->
				</script>
				<?php
				}
			}
			else
			{
				echo '<span class="error"><img src="http://'.$domaine.'/images/icones/alert.png" class="alerte" />'.$wrong_pass.'</span>';
			}
			
		}
		else
		{
			echo '<span class="error"><img src="http://'.$domaine.'/images/icones/alert.png" class="alerte" />'.$no_username.'</span>';
		}
	}
	else
	{
		echo '<span class="error"><img src="http://'.$domaine.'/images/icones/alert.png" class="alerte" />'.$not_filled.'</span>';
	}
}
?>