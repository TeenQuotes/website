<?php
include_once "header.php";
include "lang/$language/connexion.php";

$pseudo = mysql_escape_string($_POST['pseudo']);
$password = mysql_escape_string($_POST['pass']);

$method=htmlspecialchars($_GET['method']);

if ($method=='get')
	{
	$pseudo = htmlspecialchars($_GET['pseudo']);
	$password = htmlspecialchars($_GET['password']);
	}

if (isset($_POST['connexion']) OR $method=='get')
{
	if (!empty($pseudo) and !empty($password))
	{
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
					
										
					$_SESSION['logged'] = true;
					// variables session
					$_SESSION['account'] = $compte['id'];										
					$_SESSION['pseudo'] = $pseudo;		
					$_SESSION['passwd']=$passwd;
					$_SESSION['username'] = $compte['username'];
					
						
					// redirection
						?>
						<script language="JavaScript">
							<!--
							window.location.href="../?co"
							//-->
						</script>
						<?php
				}
				else
				{
					echo '<span class="error"><img src=\"http://www.teen-quotes.com/images/icones/alert.png\" class="alerte" />'.$wrong_pass.'</span>';
				}
			}
		}
		else
		{
			echo '<span class="error"><img src=\"http://www.teen-quotes.com/images/icones/alert.png\" class="alerte" />'.$no_username.'</span>';
		}
	}
	else
	{
		echo '<span class="error"><img src=\"http://www.teen-quotes.com/images/icones/alert.png\" class="alerte" />'.$not_filled.'</span>';
	}
}
?>

