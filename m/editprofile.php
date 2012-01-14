<?php
include 'header.php';
$action=$_GET['action'];
$id_user =$_SESSION['account'];
$result = mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_account where id='$id_user'"));
		
		// SELECTED POUR LE TITRE USER
		switch ($result['title']) {
		case "Mr" : $selected_mr="selected";
		break;
		case "Mrs" : $selected_mrs="selected";
		break;
		case "Miss" : $selected_miss="selected";
		break;
		}
include "../lang/$language/edit_profile.php";


		if(empty($result['birth_date'])) {$result['birth_date']="";}
		if(empty($result['title'])) {$result['title']="";}
		if(empty($result['about_me'])) {$result['about_me']="";}else{$result['about_me'] = str_replace("<br />", "\n", $result['about_me']);}
		if(empty($result['country'])) {$result['country']="";}
		if(empty($result['city'])) {$result['city']="";}
		


// FORMULAIRE
if (empty($action)) {
?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone" /><?php echo $edit_profile; ?></h1>
<span class="right"><img src="http://<?php echo $domaine; ?>/images/avatar/<?php echo $result['avatar']; ?>" height="50px" /></span>
<br />
<form action="editprofile.php?action=send" method="post">
<div class="colonne-gauche"><?php echo $choose_title; ?>
<br /><br />
<div class="colonne-gauche"><?php echo $choose_birth; ?></div><div class="colonne-milieu"><input type="text" class="signup" name="birth_date" value="<?php echo $result['birth_date']; ?>" /></div>
<br /><br />
<div class="colonne-gauche"><?php echo $choose_country; ?></div><div class="colonne-milieu"><input type="text" class="signup" name="country" value="<?php echo $result['country']; ?>" /></div>
<br /><br />
<div class="colonne-gauche"><?php echo $choose_city; ?></div><div class="colonne-milieu"><input type="text" class="signup" name="city" value="<?php echo $result['city']; ?>" /></div>
<br /><br />
<div class="colonne-gauche"><?php echo $about_you; ?></div><div class="colonne-milieu"><textarea name="about_me" value="<?php echo $result['about_me']; ?>" style="height:60px;width:230px;"><?php echo $result['about_me']; ?></textarea></div> 
<br /><br />
<div class="clear"></div>
<div class="colonne-gauche"><?php echo $hide_profile; ?>
<br /><br />

		<center><p><input type="submit" value="Okey" class="submit" /></p></center>

</form>
</div>

<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/avatar.png" class="icone" /><?php echo $change_avatar; ?></h1>
<?php echo $change_avatar_rules; ?>
<br />
<form method="post" action="editprofile.php?action=avatar" enctype="multipart/form-data">
<div class="colonne-gauche"><?php echo $select_photo; ?></div><div class="colonne-milieu"><input type="file" name="photo" class="signup" /></div>
<br /><br />
<a href="?action=reset_avatar"><?php echo $reset_avatar; ?></a><br>
<center><p><input type="submit" value="Okey" class="submit" /></p></center>
</form>
</div>


<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/password.png" class="icone" /><?php echo $change_password; ?></h1>

<form action="editprofile.php?action=change" method="post">
<div class="colonne-gauche"><?php echo $new_password; ?></div><div class="colonne-milieu"><input type="password" class="signup" name="pass1" /></div><div class="colonne-droite"><span class="min_info">Minimum 5 <?php echo $characters; ?></span></div>
<br /><br />
<div class="colonne-gauche"><?php echo $new_password_repeat; ?></div><div class="colonne-milieu"><input type="password" class="signup" name="pass2" /></div>
<br /><br />
<center><p><input type="submit" value="Okey" class="submit" /></p></center>
</form>
</div>

<?php }
elseif ($action=="send") {
// LE FORMULAIRE A ETE ENVOYE ON LE TRAITE ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone" /><?php echo $edit_profile; ?></h1>
<?php 
				$title = htmlspecialchars(mysql_escape_string($_POST['title']));
				$birth_date = htmlspecialchars(mysql_escape_string($_POST['birth_date']));
				$country = ucfirst(htmlspecialchars(mysql_escape_string($_POST['country'])));
				$city = ucfirst(htmlspecialchars(mysql_escape_string($_POST['city'])));
				$about_me = nl2br(htmlspecialchars($_POST['about_me']));
				$hide_profile = htmlspecialchars(mysql_escape_string($_POST['hide_profile']));
				
				$about_me= nl2br($about_me);
				
if(!empty($title) && !empty($birth_date) && !empty($country) && !empty($city) && !empty($about_me) && !empty($hide_profile)) {
	if ($hide_profile=="No") {$hide_profile='0';}
	if (strlen($about_me) <= '1000') {
										$query = mysql_query("UPDATE teen_quotes_account set title='$title', birth_date='$birth_date', country='$country', city='$city', about_me='$about_me', hide_profile='$hide_profile' WHERE id='$id'");
										if ($query) {
										echo "$edit_succes";
										}
										else 
										{
										echo "<h2>$error</h2> $lien_retour";
										}
										
									}
									else
								{
								echo "$description_long";
								}
							}
							else 
						{
						echo "$not_completed";
						}
					
?>
</div>
<?php }
elseif ($action=="avatar") { ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/avatar.png" class="icone" /><?php echo $change_avatar; ?></h1>
<?php
$photo= $_FILES['photo']['name'];
$point=".";
// Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur
if (isset($_FILES['photo']) AND $_FILES['photo']['error'] == 0)
{
        // Testons si le fichier n'est pas trop gros
        if ($_FILES['photo']['size'] <= 120000)
        {
                // Testons si l'extension est autorisée
                $infosfichier = pathinfo($_FILES['photo']['name']);
                $extension_upload = $infosfichier['extension'];
                $extensions_autorisees = array('jpg', 'gif', 'png','JPG');
                if (in_array($extension_upload, $extensions_autorisees))
                {		unlink("./images/avatar/$id.$extension_upload"); // delete de l'image si celle ci existe
                        // On peut valider le fichier et le stocker définitivement
                        move_uploaded_file($_FILES['photo']['tmp_name'], './images/avatar/' . $id.$point.$extension_upload);
						// on écrit la requête sql 
						$nom_fichier=$id.$point.$extension_upload;
						$sql = "UPDATE teen_quotes_account SET avatar='images/avatar/$nom_fichier' WHERE id='$id'"; 
						mysql_query($sql) or die('Erreur SQL !'.$sql.'<br>'.mysql_error()); 
                        echo "$change_avatar_succes";
						echo "<meta http-equiv=\"refresh\" content=\"3;url=user-$id\" />";
                }
				else
				{
				echo "<span class=\"erreur\">$bad_extension</span>$lien_retour";
				}
        }
		else
		{
		echo "<span class=\"erreur\">$photo_extra_size</span>$lien_retour";
		}
}
	 ?>
</div>
<?php }
elseif ($action=="reset_avatar") {
// RESET DE L'AVATAR ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/avatar.png" class="icone" /><?php echo $change_avatar; ?></h1>
<?php
						$sql = "UPDATE teen_quotes_account SET avatar='images/icon50.png' WHERE id='$id'"; 
						mysql_query($sql) or die('Erreur SQL !'.$sql.'<br>'.mysql_error()); 
                        echo "$change_avatar_succes";
						echo "<meta http-equiv=\"refresh\" content=\"3;url=user-$id\" />";
	 ?>
</div>
<?php }
elseif ($action=="change") { ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone" /><?php echo $change_password; ?></h1>
<?php 		//CHANGEMENT DE MOT DE PASSE
			$pass1 = htmlspecialchars(mysql_escape_string($_POST['pass1']));
			$pass2 = htmlspecialchars(mysql_escape_string($_POST['pass2']));
			
			if ($pass1==$pass2) {
				if(strlen($pass1) >= '5'){
										$pass = sha1(strtoupper($username).':'.strtoupper($pass1));
										$query = mysql_query ("UPDATE teen_quotes_account SET pass='$pass' WHERE id='$id'") or die ('Erreur : '.mysql_error());
										$message = "$email_message";
										$mail = mail($email, "$email_subject", $message, $headers); 
										if($query && $mail){
														echo "$change_pass_succes";
														echo "<meta http-equiv=\"refresh\" content=\"3;url=index.php?deconnexion\" />";
														}
														else 
														{
														echo "<h2>$error</h2>$lien_retour";
														} 
										/*if ($query) {
														echo "$change_pass_succes";
														echo "<meta http-equiv=\"refresh\" content=\"3;url=index.php?deconnexion\" />";
													}
													else 
													{
														echo "<h2>$error</h2>$lien_retour";
													} */
										
										}
										else 
										{
										echo "<span class=\"erreur\">$password_short</span>$lien_retour";
										}
								}		
								else 
								{
								echo "<span class=\"erreur\">$password_not_same</span>$lien_retour";
								} ?>
</div>
								
								
<?php	}
include "footer.php"; ?>