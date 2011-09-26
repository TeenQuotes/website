<?php 
include 'header.php';
include "lang/$language/signup.php";
$action=$_GET['action'];
// FORMULAIRE
if (empty($action)) {
?>
<div class="post slidedown">
<h1><img src="http://www.teen-quotes.com/images/icones/signin.png" class="icone"/><?php echo $sign_up; ?></h1>
<?php if (isset($_GET['addquote'])) { echo ''.$must_be_registered_for_quote.'';} ?>
<?php echo $account_create; ?><br>
<br />
<?php echo $require_age; ?><br>
<br />
<form method="post" action="?action=send"> 

		<div class="colonne-gauche"><?php echo $username_enter; ?> </div><div class="colonne-milieu"><input type="text" name="username" class="signup"/></div><div class="colonne-droite"><span class="min_info">Minimum 5 <?php echo $characters; ?></span></div>
		<br /><br />
		<div class="colonne-gauche"><?php echo $password; ?> </div><div class="colonne-milieu"><input type="password" name="pass1" class="signup"/></div><div class="colonne-droite"><span class="min_info">Minimum 6 <?php echo $characters; ?></span></div>
		<br /><br />
		<div class="colonne-gauche"><?php echo $confirm_password; ?> </div><div class="colonne-milieu"><input type="password" name="pass2" class="signup"/></div><div class="colonne-droite"><span class="min_info"><?php echo $reenter_pass; ?></span></div>
		<br /><br />
		<div class="colonne-gauche">Email </div><div class="colonne-milieu"><input type="text" name="email" class="signup"/></div><div class="colonne-droite"><span class="min_info"><?php echo $valid_email; ?></span></div>
		<br /><br />
		<center><p><input type="submit" value="<?php echo $create_account; ?>" class="submit" /></p></center>

</form>
</div>
<?php include "footer.php"; 
}
//ENVOI DU FORMULAIRE
elseif ($action=="send") { ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/signin.png" class="icone"/><?php echo $sign_up; ?></h1>
<?php 
				$username = ucfirst(trim(htmlspecialchars(mysql_escape_string($_POST['username']))));
				$username=str_replace(' ','',$username);
				$pass1 = htmlspecialchars(mysql_escape_string($_POST['pass1']));
				$pass2 = htmlspecialchars(mysql_escape_string($_POST['pass2']));
				$email = htmlspecialchars(mysql_escape_string($_POST['email']));
				$ip=$_SERVER["REMOTE_ADDR"];
				$confmail="0";
                $timestamp_expire = time() + 3600;
				$code = caracteresAleatoires(5);
				
							
						if (strlen(trim($username)) >= '5') {
							if (preg_match('#[\w]{5,15}#', $username)) {
								$test = mysql_num_rows(mysql_query("select * from teen_quotes_account WHERE username='$username'"));
								if($test == '0') {
									if( $pass1 == $pass2 ){
										if(strlen($pass1) >= '6'){
											$pass = sha1(strtoupper($username).':'.strtoupper($pass1));
											if(strlen($email) >= '6'){          
												if(preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)){
													$test = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_account WHERE email='$email'"));
													if($test == '0'){
													if($test == '0'){
																	$add = mysql_query("INSERT INTO teen_quotes_account (username,pass,email,ip,security_level) values('$username','$pass', '$email', '$ip','0')");
																	$query_newsletter =mysql_query("INSERT INTO newsletter (email,code) VALUES ('$email','$code')");
																	$message = "$email_message";
																	$mail = mail($email, $email_subject, $message, $headers);
															if($add){
															echo "$signup_succes";
															echo '<meta http-equiv="refresh" content="10;url=connexion.php?method=get&pseudo='.$username.'&password='.$pass2.'" />';
															}
															else
															{
															echo "$error";
															echo " $email - $email_subject - $message - $headers";
															}
														}else{
															echo "$error";	
														}		
													}else{
														echo '<span class="erreur">'.$email_taken.'</span>'.$lien_retour.'';
													}
												}else{
													echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour.'';
												}
											}else{
												echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour.'';
											}
										}else{
											echo '<span class="erreur">'.$password_short.'</span>'.$lien_retour.'';
										}
									}else{
										echo '<span class="erreur">'.$password_not_same.'</span>'.$lien_retour.'';
									}
								}else{
									echo '<span class="erreur">'.$username_taken.'</span>'.$lien_retour.'';
								}
							}
							else{
								echo '<span class="erreur">'.$username_not_valid.'</span>'.$lien_retour.'';
							}
						}else{
							echo '<span class="erreur">'.$username_short.'</span>'.$lien_retour.'';
						}
				
echo '</div>';

include "footer.php";
}

?>