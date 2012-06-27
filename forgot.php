<?php 
include 'header.php';
include 'lang/'.$language.'/forgot.php';
$action = $_GET['action'];

if (empty($action) AND $_SESSION['logged'] != TRUE) 
	{ 
	echo '
	<div class="post">
		<h1><img src="http://'.$domaine.'/images/icones/faq.png" class="icone" />'.$pass_forget.'</h1>
		'.$texte_forget.'
		<div class="grey_post">
		<form action="?action=send" method="post">
			<div class="colonne-gauche">'.$email_adress.'</div><div class="colonne-milieu"><input type="text" name="email" class="signup"/></div><div class="colonne-droite"><span class="min_info">'.$email_use_signup.'</span></div>
			<br /><br />
			<center><p><input type="submit" value="Okey" class="submit" /></p></center>
		</form>
		</div>
	</div>
	';
	}
elseif ($action == "send") 
	{
	echo '
	<div class="post">
	<h1><img src="http://'.$domaine.'/images/icones/faq.png" class="icone" />'.$pass_forget.'</h1>
	';
	
	$email = mysql_real_escape_string($_POST['email']);

	if(preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email))
		{
		$test = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_account WHERE email='$email'"));
		if ($test == '1')
			{
			$resultat=mysql_query("SELECT * FROM teen_quotes_account WHERE email='$email'");
			$donnees=mysql_fetch_array($resultat);
			$username=$donnees['username'];
			$newpass = caracteresAleatoires(6);
			$passwd = sha1(strtoupper($username).':'.strtoupper($newpass));
			$update_pass = mysql_query ("UPDATE teen_quotes_account SET pass='$passwd' WHERE username='$username'") or die(mysql_error());
			
			$message = "$top_mail $change_succes1 <font color=\"#5C9FC0\"><b>$username</b></font> $change_succes2 <font color=\"#5C9FC0\"><b>$newpass</b></font> $change_succes3 <a href=\"http://$domaine/connexion.php?method=get&pseudo=$username&password=$passwd\" target=\"_blank\">$this_link</a>. $end_mail";
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
	}
	
echo '</div>';
include "footer.php";
?>