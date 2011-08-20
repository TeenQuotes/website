<?php
include "../lang/$language/connexion.php";
if (isset($_POST['connexion']))
{
	if (!empty($_POST['pseudo']) and !empty($_POST['pass']))
	{
		$pseudo = mysql_escape_string($_POST[pseudo]);
		$password = mysql_escape_string($_POST[pass]);
		$checkbox = "1";

		
		$retour_nb_pseudo = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE `username` ='$pseudo'"));// test du pseudo
		
		if ($retour_nb_pseudo == '1')
		{
			$compte = mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_account WHERE `username` = '$pseudo'"));				
			{	
				$passwd = sha1(strtoupper($pseudo).':'.strtoupper($password));
				$sha = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE `pass` = '$passwd'"));
				if ($sha == '1') // Sha1 puis vérification du pass
				{
					if ($checkbox == '1'){ setcookie("Pseudo", $pseudo, time() + (((3600*24)*30)*12));   setcookie("Pass", $passwd, time() + (((3600*24)*30)*12)); }
					
					$_SESSION['logged'] = true;
					// variables session
					$_SESSION['account'] = $compte['id'];										
					$_SESSION['pseudo'] = $pseudo;							
					$_SESSION['username'] = $compte['username'];
					
						
					// redirection
						?>
								<script language="JavaScript">
							<!--
							window.location.href="index.php?succes"
							//-->
						</script>
						<?php
				}
				else
				{
					echo '<span class="error"><img src="http://www.teen-quotes.com/images/icones/alert.png" class="alerte" />'.$wrong_pass.'</span>';
				}
			}
		}
		else
		{
			echo '<span class="error"><img src="http://www.teen-quotes.com/images/icones/alert.png" class="alerte" />'.$no_username.'</span>';
		}
	}
	else
	{
		echo '<span class="error"><img src="http://www.teen-quotes.com/images/icones/alert.png" class="alerte" />'.$not_filled.'</span>';
	}
}
?>

