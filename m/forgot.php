<?php 
include 'header.php';
include "../lang/$language/forgot.php";
$action=$_GET['action'];

if (empty($action) && !$_SESSION['logged']) { ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/faq.png" class="icone" /><?php echo $pass_forget; ?></h1>
<?php echo $texte_forget; ?>
<form action="forgot.php?action=send" method="post">
<div class="colonne-gauche"><?php echo $email_adress; ?></div><div class="colonne-milieu"><input type="text" name="email" class="signup"/></div><div class="colonne-droite"><span class="min_info"><?php echo $email_use_signup; ?></span></div>
<br /><br />
		<center><p><input type="submit" value="Okey" class="submit" /></p></center>
</form>
</div>
<?php }
elseif ($action=="send") { ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/faq.png" class="icone" /><?php echo $pass_forget; ?></h1>
<?php 

$email = mysql_escape_string($_POST['email']);

if(preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)){
																	 $test = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_account WHERE email='$email'"));
																	 if($test == '1'){
																					$resultat=mysql_query("SELECT * FROM teen_quotes_account WHERE email='$email'");
																					$donnees=mysql_fetch_array($resultat);
																					$username=$donnees['username'];
																					$newpass = caracteresAleatoires(6);
																					$passwd = sha1(strtoupper($username).':'.strtoupper($newpass));
																					$update_pass = mysql_query ("UPDATE teen_quotes_account SET pass='$passwd' WHERE username='$username'") or die(mysql_error());
																					
																					$message="$top_mail $change_succes1 <font color=\"#5C9FC0\"><b>$username</b></font> $change_succes2 <font color=\"#5C9FC0\"><b>$newpass</b></font> $change_succes3 $end_mail";
																					$mail = mail($email, "$email_subject", $message, $headers); 
																					if($update_pass && $mail) {
																									echo"$its_ok";
																									}
																									else
																									{
																									echo "<h2>$error</h2>";
																									}
																					}
																					else
																					{
																					echo "<span class=\"erreur\">$no_account</span>$lien_retour";
																					}
																	}
																	else
																	{
																	echo"<span class=\"erreur\">$email_not_valid</span>$lien_retour";
																	}
																	
						} ?>
</div>																
<?php 
include "footer.php"; ?>