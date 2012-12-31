<?php
// If the user is logged and one of his sessions variables is empty, disconnect the user
if ($_SESSION['logged'] == TRUE AND (empty($_SESSION['id']) OR empty($_SESSION['username']) OR empty($_SESSION['email']) OR empty($_SESSION['avatar'])))
{
	deconnexion();
}

// If the cookies Pseudo / Pass exist and the user is not logged
if (isset($_COOKIE['Pseudo']) AND isset($_COOKIE['Pass']) AND $_SESSION['logged'] == FALSE)
{
	$pseudo = mysql_real_escape_string($_COOKIE['Pseudo']);
	$pass = mysql_real_escape_string($_COOKIE['Pass']);

	$query_base = mysql_query("SELECT * FROM teen_quotes_account WHERE `username` = '$pseudo'");
	$retour_nb_pseudo = mysql_num_rows($query_base);

	// Check if the username exists
	if ($retour_nb_pseudo == 1)
	{				
		// Check if the password is right
		$sha = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE `pass` = '$pass' AND `username` = '$pseudo'"));

		if ($sha == 1)
		{
			$compte = mysql_fetch_array($query_base);

			// Store session values
			$_SESSION['logged'] = TRUE;
			$_SESSION['id'] = $compte['id'];										
			$_SESSION['security_level'] = $compte['security_level'];									
			$_SESSION['username'] = $compte['username'];
			$_SESSION['email'] = $compte['email'];
			$_SESSION['avatar'] = $compte['avatar'];

			// Set variables
			$username = $_SESSION['username'];
			$id = $_SESSION['id'];
			$email = $compte['email'];
			$last_visit = $compte['last_visit'];
			$session_last_visit = $_SESSION['last_visit_user'];

			last_visit ($session_last_visit, $last_visit, $id);

			// The user hasn't fullfilled his profile.
			if (empty($compte['birth_date']) AND empty($compte['title']) AND empty($compte['country']) AND empty($compte['about_me']) AND $compte['avatar'] == "icon50.png" AND empty($compte['city']))
			{
				$_SESSION['profile_not_fullfilled'] = TRUE;
			}
		}
	}
}

// If the user is logged, set variables
if ($_SESSION['logged'] == TRUE)
{
	$username = $_SESSION['username'];
	$id = $_SESSION['id'];
	$email = $_SESSION['email'];
	$session_last_visit = $_SESSION['last_visit_user'];

	// Force the user to rename if he hasn't a valid username
	if (usernameIsValid(strtolower($_SESSION['username'])) == FALSE AND $php_self != 'changeusername')
	{
		echo '<meta http-equiv="refresh" content="0;url=changeusername">';
	}

	// The username is valid if it's only in lowercases. Store the username in lowercases
	if (isset($_COOKIE['Pseudo']) AND usernameIsValid(strtolower($_SESSION['username'])) == TRUE AND usernameIsValid($_SESSION['username']) == FALSE)
	{
		$_SESSION['username'] = strtolower($_SESSION['username']);
	}
}