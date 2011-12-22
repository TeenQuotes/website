<?php 
include 'header.php';
include "../lang/$language/forgot.php";
$action=$_GET['action'];

if (empty($action) AND !$_SESSION['logged']) 
	{ ?>
	<div class="post">
		<h1><img src="http://www.teen-quotes.com/images/icones/faq.png" class="icone" /><?php echo $pass_forget; ?></h1>
		<?php echo $texte_forget; ?>
		<form action="?action=send" method="post">
			<?php echo $email_adress; ?><br>
			<input type="text" name="email" class="signup"/><br>
			<span class="min_info"><?php echo $email_use_signup; ?></span>
			<br /><br />
			<center><p><input type="submit" value="Okey" class="submit" /></p></center>
		</form>
	</div>
<?php 
	}
elseif ($action=="send") 
	{ ?>
	<div class="post">
	<h1><img src="http://www.teen-quotes.com/images/icones/faq.png" class="icone" /><?php echo $pass_forget; ?></h1>
<?php 

	$email = mysql_escape_string($_POST['email']);

	if(preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email))
		{
		$test = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_account WHERE email='$email'"));
		if($test == '1')
			{
			$resultat=mysql_query("SELECT * FROM teen_quotes_account WHERE email='$email'");
			$donnees=mysql_fetch_array($resultat);
			$username=$donnees['username'];
			$newpass = caracteresAleatoires(6);
			$passwd = sha1(strtoupper($username).':'.strtoupper($newpass));
			$update_pass = mysql_query ("UPDATE teen_quotes_account SET pass='$passwd' WHERE username='$username'") or die(mysql_error());
			
			$message="$top_mail $change_succes1 <font color=\"#5C9FC0\"><b>$username</b></font> $change_succes2 <font color=\"#5C9FC0\"><b>$newpass</b></font> $change_succes3 <a href=\"http://www.teen-quotes.com/connexion.php?method=get&pseudo=$username&password=$newpass\" target=\"_blank\">$this_link</a>. $end_mail";
			$mail = mail($email, "$email_subject", $message, $headers); 
			if($update_pass AND $mail) 
				{
				echo ''.$its_ok.'';
				}
				else
				{
				echo '<h2>'.$error.'</h2>';
				}
			}
			else
			{
			echo '<span class="erreur">'.$no_account.'</span>'.$lien_retour.'';
			}
		}
		else
		{
		echo '<span class="erreur">'.$email_not_valid.'</span>'.$lien_retour.'';
		}
																	
	} ?>
</div>																
<?php 
include "footer.php"; ?>